<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("FUNC_FILE")) die("Illegal File Access");

# User account navigation
function navi($id) {
	global $conf;
	if ($conf['name'] != "account") get_lang("account");
	if ($id != 1) {
		$massiv[] = "<a href=\"index.php?name=account\"><img src=\"images/account/home.png\" border=\"0\" alt=\""._RETURNACCOUNT."\" title=\""._RETURNACCOUNT."\"></a><br><a href=\"index.php?name=account\">"._HOME."</a>";
	}
	if ($conf['forum_link']) {
		$massiv[] = "<a href=\"forum/".$conf['forum_link']."\"><img src=\"images/account/info.png\" border=\"0\" alt=\""._ACCOUNT."\" title=\""._ACCOUNT."\"></a><br><a href=\"forum/".$conf['forum_link']."\">"._ACCOUNT."</a>";
	}
	if ($conf['forum_mess']) {
		$massiv[] = "<a href=\"forum/".$conf['forum_mess']."\"><img src=\"images/account/messages.png\" border=\"0\" alt=\""._PRIVATEMESSAGES."\" title=\""._PRIVATEMESSAGES."\"></a><br><a href=\"forum/".$conf['forum_mess']."\">"._MESSAGES."</a>";
	}
	if ($conf['forum']) {
		$massiv[] = "<a href=\"forum/index.php\"><img src=\"images/account/forum.png\" border=\"0\" alt=\""._FORUM."\" title=\""._FORUM."\"></a><br><a href=\"forum/index.php\">"._FORUM."</a>";
	}
	$massiv[] = "<a href=\"index.php?name=account&op=edithome\"><img src=\"images/account/preferences.png\" border=\"0\" alt=\""._CHANGE."\" title=\""._CHANGE."\"></a><br><a href=\"index.php?name=account&op=edithome\">"._CHANGE."</a>";
	$massiv[] = "<a href=\"index.php?name=account&op=logout\"><img src=\"images/account/exit.png\" border=\"0\" alt=\""._LOGOUT."\" title=\""._LOGOUT."\"></a><br><a href=\"index.php?name=account&op=logout\">"._LOGOUT."</a>";
	$content = "";
	foreach ($massiv as $val) {
		$content .= "<td width=\"10%\" align=\"center\">".$val."</td>";
		if ($cont == (5 - 1)) {
			$content .= "</tr><tr>";
			$cont = 0;
		} else {
			$cont++;
		}
	}
	open();
	echo "<table width=\"100%\" border=\"0\" align=\"center\"><tr>".$content."</tr></table>";
	close();
}

function cookieset($id, $name, $pass, $num, $blockon, $theme) {
	global $conf;
	$info = base64_encode("$id:$name:$pass:$num:$blockon:$theme");
	setcookie($conf['user_c'], "$info", time() + intval($conf['user_c_t']));
}

function is_group($name) {
	global $prefix, $db, $user;
	if (is_user()) {
		$uid = intval($user[0]);
		list($points, $group) = $db->sql_fetchrow($db->sql_query("SELECT user_points, user_group FROM ".$prefix."_users WHERE user_id='$uid'"));
		list($mgroup, $grpoints, $grextra) = $db->sql_fetchrow($db->sql_query("SELECT m.mod_group, g.points, g.extra FROM ".$prefix."_modules AS m LEFT JOIN ".$prefix."_groups AS g ON (m.mod_group=g.id) WHERE m.title='$name'"));
		if (intval($group) && $group != "" && $group == $mgroup && $grextra == "1") {
			return 1;
		} elseif ((intval($points) && $points >= $grpoints && $grextra != "1") || $mgroup == 0) {
			return 1;
		}
	}
	return 0;
}

function update_points($id) {
	global $prefix, $db, $user, $conf, $confu;
	$id = intval($id);
	if ($id && is_user() && $confu['point'] == 1) {
		$uid = intval($user[0]);
		$upoints = explode(",", $confu['points']);
		$a = $id - 1;
		$rpoints = $upoints[$a];
		$db->sql_query("UPDATE ".$prefix."_users SET user_points=user_points+".$rpoints." WHERE user_id='$uid'");
	}
}

function message_box() {
	global $prefix, $db, $admin_file, $conf, $currentlang, $user;
	if ($conf['message'] == 1) {
		$querylang = ($conf['multilingual'] == 1) ? "AND (mlanguage='$currentlang' OR mlanguage='')" : "";
		$result = $db->sql_query("SELECT mid, title, content, expire, view FROM ".$prefix."_message WHERE active='1' $querylang");
		if ($numrows = $db->sql_numrows($result) > 0) {
			while (list($mid, $title, $content, $expire, $view) = $db->sql_fetchrow($result)) {
				$mid = intval($mid);
				$content = bb_decode($content, "All");
				if ($title != "" && $content != "") {
					if ($expire == 0) {
						$remain = ""._UNLIMITED."";
					} else {
						$etime = intval(($expire - time()) / 3600);
						$etime_day = $etime / 24;
						$remain = ""._PURCHASED.": $etime "._HOURS." (".substr($etime_day, 0, 5)." "._DAYS.")";
					}
					$message_link = "- ".$remain." - <a href=\"".$admin_file.".php?op=msg_add&id=$mid\">"._EDIT."</a> ]</center>";
					if ($view == 5) {
						if (is_moder()) $content .= "<br><center>[ "._MVIEWSUBUSERS." ".$message_link."";
						messagebox($title, $content);
					} elseif ($view == 4 && is_moder()) {
						$content .= "<br><center>[ "._MVIEWADMIN." ".$message_link."";
						messagebox($title, $content);
					} elseif (($view == 3 && is_user()) || ($view == 3 && is_user() && is_moder())) {
						if (is_moder()) $content .= "<br><center>[ "._MVIEWUSERS." ".$message_link."";
						messagebox($title, $content);
					} elseif (($view == 2 && !is_user()) || ($view == 2 && !is_user() && is_moder())) {
						if (is_moder()) $content .= "<br><center>[ "._MVIEWANON." ".$message_link."";
						messagebox($title, $content);
					} elseif ($view == 1) {
						if (is_moder()) $content .= "<br><center>[ "._MVIEWALL." ".$message_link."";
						messagebox($title, $content);
					}
					if ($expire && $expire < time()) $db->sql_query("UPDATE ".$prefix."_message SET active='0', expire='0' WHERE mid='$mid'");
				}
			}
		}
	}
}

function getusrinfo($usr="") {
	global $prefix, $db, $user;
	$uid = intval($user[0]);
	if (is_user() && $uid) {
		$info = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_users WHERE user_id='$uid'"));
		return $info;
	}
}

function userblock() {
	global $user, $db, $prefix, $conf;
	$uid = intval($user[0]);
	$block = intval($user[4]);
	if (is_user() && $block) {
		list($userblock) = $db->sql_fetchrow($db->sql_query("SELECT user_block FROM ".$prefix."_users WHERE user_id='$uid'"));
		$userblock = bb_decode($userblock, "account");
		themesidebox(""._MENUFOR."", $userblock);
	}
}

# Show comments
function show_com($cid) {
	global $prefix, $db, $admin_file, $conf, $user, $confu;
	include("config/config_comments.php");
	list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(cid) FROM ".$prefix."_comment WHERE cid='$cid' AND modul='".$conf['name']."'"));
	if ($numstories > 0) {
		$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
		$offset = ($num - 1) * $confc['num'];
		$numpages = ceil($numstories / $confc['num']);
		if ($confc['sort']) {
			$sort = "ASC";
			$a = ($num) ? $offset+1 : 1;
		} else {
			$sort = "DESC";
			$a = $numstories;
			if ($numstories > $offset) $a -= $offset;
		}
		$result = $db->sql_query("SELECT id, cid, date, uid, name, host_name, comment FROM ".$prefix."_comment WHERE cid='$cid' AND modul='".$conf['name']."' ORDER BY date ".$sort." LIMIT ".$offset.", ".$confc['num']."");
		$c = 0;
		while (list($com_id, $com_cid, $com_date, $com_uid, $com_name, $com_host, $com_text) = $db->sql_fetchrow($result)) {
			$cmassiv[] = array($com_id, $com_cid, $com_date, $com_uid, $com_name, $com_host, $com_text);
			if ($c == 0) {
				$where = "'".$com_uid."'";
			} else {
				$where .= ",'".$com_uid."'";
			}
			$c++;
		}
		$result2 = $db->sql_query("SELECT user_id, user_name, user_email, user_website, user_avatar, user_regdate, user_icq, user_sig, user_viewemail, user_aim, user_yim, user_msnm, user_points, user_gender, user_votes, user_totalvotes FROM ".$prefix."_users WHERE user_id IN ($where)");
		while (list($user_id, $user_name, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msn, $user_points, $user_gender, $user_votes, $user_totalvotes) = $db->sql_fetchrow($result2)) {
			$umassiv[] = array($user_id, $user_name, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msn, $user_points, $user_gender, $user_votes, $user_totalvotes);
		}
		open();
		foreach ($cmassiv as $val) {
			$com_id = $val[0];
			$com_cid = $val[1];
			$com_date = $val[2];
			$com_uid = $val[3];
			$com_name = $val[4];
			$com_host = $val[5];
			$com_text = $val[6];
			unset($user_id, $user_name, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msn, $user_points, $user_gender, $user_votes, $user_totalvotes);
			if ($umassiv) {
				foreach ($umassiv as $val2) {
					if (strtolower($com_uid) == strtolower($val2[0])) {
						$user_id = $val2[0];
						$user_name = $val2[1];
						$user_email = $val2[2];
						$user_website = $val2[3];
						$user_avatar = $val2[4];
						$user_regdate = $val2[5];
						$user_icq = $val2[6];
						$user_sig = $val2[7];
						$user_viewemail = $val2[8];
						$user_aim = $val2[9];
						$user_yim = $val2[10];
						$user_msn = $val2[11];
						$user_points = $val2[12];
						$user_gender = $val2[13];
						$user_votes = $val2[14];
						$user_totalvotes = $val2[15];
					}
				}
			}
			$user_name = ($user_name) ? $user_name : (($com_name) ? $com_name : $confu['anonym']);
			$avatar = ($user_avatar && file_exists("".$confu['adirectory']."/".$user_avatar."")) ? "<img src=\"".$confu['adirectory']."/".$user_avatar."\" align=\"left\" alt=\"$user_name\" title=\"$user_name\">" : "<img src=\"".$confu['adirectory']."/00.gif\" align=\"left\" alt=\"$user_name\" title=\"$user_name\">";
			preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $user_regdate, $datetime);
			$user_regdate = ($user_regdate) ? "".$datetime[3].".".$datetime[2].".".$datetime[1]."" : "<i>"._NO_INFO."</i>";
			$text = ($user_sig) ? "".bb_decode($com_text, $conf['name'])."<hr>".bb_decode($user_sig, $conf['name'])."" : "".bb_decode($com_text, $conf['name'])."";
			$user_point = ($confu['point'] && $user_points) ? ""._POINTS.": ".$user_points."" : "";
			$rate = ajax_rating(0, $user_id, "account", $user_votes, $user_totalvotes);
			$name = "<a href=\"javascript: InsertCode('name', '".$user_name."', '', '', '1')\"><b>".$user_name."</b></a>";
			$uscomd = ""._DATE.": ".format_time($com_date)."";
			$uscomn = " <a href=\"#".$com_id."\" title=\""._COMMENT.": ".$a."\">"._COMMENT.": ".$a."</a>";
			$usrdate = ""._REG_DATE.": ".$user_regdate."";
			$usgender = ($user_gender != "") ? " ".gender($user_gender, 2)."" : "";
			$usinfo = ($user_name != "") ? " ".user_info($user_name, 2)."" : "";
			$ussite = ($user_website != "") ? " <a href=\"$user_website\" target=\"_blank\" title=\"$user_website\"><img src=\"".img_find("all/home")."\" border=\"0\" align=\"center\"></a>" : "";
			$usmail = ((is_moder() || $user_viewemail == 1) && $user_email) ? " <a href=\"mailto:".$user_email."?subject=".$conf['sitename']."\" title=\"$user_email\"><img src=\"".img_find("all/contact")."\" border=\"0\" align=\"center\"></a>" : "";
			$usicq = ($user_icq != "") ? " <a href=\"index.php?name=account&op=info&uname=$user_name\" title=\""._ICQ.": ".$user_icq."\"><img src=\"".img_find("all/icq")."\" border=\"0\" align=\"center\"></a>" : "";
			$usaim = ($user_aim != "") ? " <a href=\"index.php?name=account&op=info&uname=$user_name\" title=\""._AIM.": ".$user_aim."\"><img src=\"".img_find("all/aim")."\" border=\"0\" align=\"center\"></a>" : "";
			$usyim = ($user_yim != "") ? " <a href=\"index.php?name=account&op=info&uname=$user_name\" title=\""._YIM.": ".$user_yim."\"><img src=\"".img_find("all/yim")."\" border=\"0\" align=\"center\"></a>" : "";
			$usmsn = ($user_msn != "") ? " <a href=\"index.php?name=account&op=info&uname=$user_name\" title=\""._MSN.": ".$user_msn."\"><img src=\"".img_find("all/msn")."\" border=\"0\" align=\"center\"></a>" : "";
			if (is_moder($conf['name'])) {
				$usip = " <a href=\"".$conf['ip_link']."".$com_host."\" title=\""._IP.": $com_host\" target=\"_blank\"><img src=\"".img_find("all/question")."\" border=\"0\" align=\"center\"></a>";
				$usban = " ".ad_bann("".$admin_file.".php?op=security_block&new_ip=".$com_host."", $com_host)."";
				$usedit = " ".ad_edit("".$admin_file.".php?op=comm_edit&id=".$com_id."")."";
				$usdel = " ".ad_delete("".$admin_file.".php?op=comm_del&id=$com_id&cid=$com_cid&modul=".$conf['name']."", cutstr(text_filter(bb_decode($com_text, $conf['name'])), 10))."";
			}
			$info = "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"left\">".$user_point."</td><td align=\"right\">".$uscomd."".$uscomn."</td></tr></table>";
			$link = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td align=\"left\">".$usrdate."</td><td align=\"right\">".$usgender."".$usinfo."".$ussite."".$usmail."".$usicq."".$usaim."".$usyim."".$usmsn."".$usip."".$usban."".$usedit."".$usdel."</td></tr></table>";
			comment($com_id, $name, $info, $avatar, $text, $rate, $link, $user_point, $uscomd, $uscomn, $usrdate, $usgender, $usinfo, $ussite, $usmail, $usicq, $usaim, $usyim, $usmsn, $usip, $usban, $usedit, $usdel);
			if ($confc['sort']) {
				$a++;
			} else {
				$a--;
			}
		}
		close();
		$pag = isset($_GET['pag']) ? "&pag=".intval($_GET['pag'])."" : "";
		num_page($conf['name'], $numstories, $numpages, $confc['num'], "op=view&id=".$cid."".$pag."&");
	} else {
		warning(""._NOCOMMENTS."", "", "", 2);
	}
	if (!is_user() && $confc['anonpost'] == 0) {
		warning(""._NOANONCOMMENTS."", "", "", 1);
	} else {
		open();
		echo "<form name=\"post\" action=\"index.php?name=".$conf['name']."\" method=\"post\" OnSubmit=\"ButtonDisable(this)\">";
		if (is_user()) {
			echo "<div class=\"left\">"._YOURNAME.":</div><div class=\"center\">".text_filter(substr($user[1], 0, 25))."</div>";
		} else {
			echo "<div class=\"left\">"._YOURNAME.":</div><div class=\"center\"><input type=\"text\" name=\"postname\" value=\"".$confu['anonym']."\" size=\"65\" maxlength=\"25\" class=\"".$conf['name']."\"></div>";
		}
		echo "<div class=\"left\">"._COMMENT.":</div><div class=\"center\">".textarea("1", "comment", "", $conf['name'], "5")."</div>"
		."".captcha_random().""
		."<div class=\"button\"><input type=\"hidden\" name=\"cid\" value=\"$cid\"><input type=\"hidden\" name=\"op\" value=\"save_com\"><input type=\"submit\" value=\""._COMMENTREPLY."\" class=\"fbutton\"></div></form>";
		close();
	}
}

# Save comments
function save_com() {
	global $prefix, $db, $user, $conf;
	include("config/config_comments.php");
	$postname = $_POST['postname'];
	$comment = $_POST['comment'];
	$e = explode(" ", $comment);
	for ($a = 0; $a < sizeof($e); $a++) $o = strlen($e[$a]);
	$stop = "";
	if ($comment == "") $stop = ""._CERROR1."";
	if ($o > $confc['letter']) $stop = ""._CERROR2."";
	if ((!is_user() && $postname == "") || (!is_user() && $confc['anonpost'] == 0)) $stop = ""._CERROR3."";
	if (captcha_check(1)) $stop = ""._SECCODEINCOR."";
	if (!$stop) {
		$postid = (is_user()) ? intval($user[0]) : "";
		$postname = (!is_user()) ? text_filter(substr($postname, 0, 25)) : "";
		$cid = intval($_POST['cid']);
		$ip = getip();
		$comment = nl2br(text_filter($comment, 2));
		$db->sql_query("INSERT INTO ".$prefix."_comment VALUES (NULL, '$cid', '".$conf['name']."', now(), '$postid', '$postname', '$ip', '$comment')");
		if ($conf['name'] == "files") {
			$db->sql_query("UPDATE ".$prefix."_files SET totalcomments=totalcomments+1 WHERE lid='$cid'");
			update_points(10);
			Header("Location: ".view_article($conf['name'], $cid)."");
		} elseif ($conf['name'] == "news") {
			$db->sql_query("UPDATE ".$prefix."_stories SET comments=comments+1 WHERE sid='$cid'");
			update_points(32);
			Header("Location: ".view_article($conf['name'], $cid)."");
		} elseif ($conf['name'] == "voting") {
			$db->sql_query("UPDATE ".$prefix."_survey SET pool_comments=pool_comments+1 WHERE poll_id='$cid'");
			update_points(43);
			Header("Location: ".view_article($conf['name'], $cid)."");
		}
	} else {
		head();
		if ($conf['name'] == "files") {
			title(""._FILES."");
		} elseif ($conf['name'] == "news") {
			title(""._NEWS."");
		} elseif ($conf['name'] == "voting") {
			title(""._VOTING."");
		}
		warning("".$stop."<br><br>"._GOBACK."", "", "", 1);
		foot();
	}
}

switch(isset($_GET['hit'])) {
	case "1":
	$img = (intval($_GET['img'])) ? "_".$_GET['img']."" : "";
	$count_hit = "config/counter/hits.txt";
	$hits = file($count_hit);
	$hit = explode("|", trim($hits[0]));
	if (date("Ymd") > $hit[1]) {
		unlink($count_hit);
		$wco = "1|".date("Ymd")."";
	} else {
		$wco = "".intval($hit[0]+1)."|".intval($hit[1])."";
	}
	$fpc = fopen($count_hit, "wb");
	fwrite($fpc, $wco);
	fclose($fpc);
	if (rename($count_hit, $count_hit) == false) {
		# unlink ($count_hit);
		# rename ($count_hit, $count_hit);
	}
	
	$image = ImageCreateFromGif("images/banners/hits".$img.".gif");
	$color = ImageColorAllocate($image, 255, 255, 255);
	ImageString($image, 1, 40, 4, $hit[0], $color);
	Header("Content-type: image/gif");
	ImageGif($image, "", 100);
	ImageDestroy($image);
	exit;
	break;
}

switch(isset($_GET['host'])) {
	case "1":
	$img = (intval($_GET['img'])) ? "_".$_GET['img']."" : "";
	$count_host = "config/counter/hosts.txt";
	$check = ($_SESSION['host'] == date("d")) ? false : true;
	if ($check) {
		$hosts = file($count_host);
		$con = explode("|", trim($hosts[0]));
		if (date("Ymd") > $con[1]) {
			unlink($count_host);
			$wc = "1|".date("Ymd")."";
		} else {
			$wc = "".intval($con[0]+1)."|".intval($con[1])."";
		}
		$fph = fopen($count_host, "wb");
		fwrite($fph, $wc);
		fclose($fph);
		unset($_SESSION['host']);
		$_SESSION['host'] = date("d");
	} else {
		$hosts = file($count_host);
		$con = explode("|", trim($hosts[0]));
	}
	$image = ImageCreateFromGif("images/banners/hosts".$img.".gif");
	$color = ImageColorAllocate($image, 255, 255, 255);
	ImageString($image, 1, 40, 4, $con[0], $color);
	Header("Content-type: image/gif");
	ImageGif($image, "", 100);
	ImageDestroy($image);
	exit;
	break;
}
?>