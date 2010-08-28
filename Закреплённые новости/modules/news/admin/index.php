<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_modul("news")) die("Illegal File Access");

function news_navi() {
	global $admin_file;
	panel();
	open();
	echo "<h1>"._NEWS."</h1>"
	."<h5>[ <a href=\"".$admin_file.".php?op=news\">"._HOME."</a>"
	." | <a href=\"".$admin_file.".php?op=news_add\">"._ADD."</a>"
	." | <a href=\"".$admin_file.".php?op=news&status=1\">"._NEWPAGES."</a>"
	." | <a href=\"".$admin_file.".php?op=news_conf\">"._PREFERENCES."</a> ]</h5>";
	close();
}

function news() {
	global $prefix, $db, $admin_file, $conf, $confu;
	head();
	news_navi();
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $conf['anum'];
	$offset = intval($offset);
	if ($_GET['status'] == 1) {
		$status = "0";
		$field = "op=news&status=1&";
		$refer = "&refer=1";
	} else {
		$status = "1";
		$field = "op=news&";
		$refer = "";
	}
	
	$result = $db->sql_query("SELECT s.sid, s.name, s.title, s.time, s.ip_sender, c.id, c.title, u.user_name, status FROM ".$prefix."_stories AS s LEFT JOIN ".$prefix."_categories AS c ON (s.catid=c.id) LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id) WHERE status".(($status==0)?"='0'":"!='0'")." ORDER BY s.status DESC, s.time DESC LIMIT ".$offset.", ".$conf['anum']."");
	if ($db->sql_numrows($result) > 0) {
		open();
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr><th>"._ID."</th><th>"._TITLE."</th><th>"._IP."</th><th>"._POSTEDBY."</th><th>"._FUNCTIONS."</th></tr>";
		while (list($sid, $uname, $title, $time, $ip_sender, $cid, $ctitle, $user_name, $sstatus) = $db->sql_fetchrow($result)) {
			$ctitle = ($cid) ? $ctitle : ""._NO."";
			$post = ($user_name) ? user_info($user_name, 1) : (($uname) ? $uname : $confu['anonym']);
			$ad_view = ($status) ? ad_view(view_article("news", $sid)) : "";
			$pin = ($sstatus==5)?'<a href="'.$admin_file.'.php?op=news_pin&pin=0&id='.$sid.'"><img src="images/all/pin-del.png" border="0" alt="Unpin news" title="Unpin news" align="center"></a>':'<a href="'.$admin_file.'.php?op=news_pin&pin=1&id='.$sid.'"><img src="images/all/pin-add.png" border="0" alt="Pin news" title="Pin news" align="center"></a>';
			echo "<tr class=\"bgcolor1\"><td align=\"center\">".$sid."</td>"
			."<td class=\"help\" OnMouseOver=\"Tip('"._CATEGORY.": $ctitle<br />"._DATE.": $time')\">".$title."</td>"
			."<td>".user_geo_ip($ip_sender, 4)."</td>"
			."<td align=\"center\">".$post."</td>"
			."<td align=\"center\">".$ad_view." ".ad_edit("".$admin_file.".php?op=news_add&id=".$sid."")." ".ad_delete("".$admin_file.".php?op=news_delete&id=".$sid."".$refer."", $title)." $pin</td></tr>";
		}
		echo "</table>";
		close();
		num_article("news", $conf['anum'], $field, "sid", "_stories", "cid", "status='".$status."'");
	} else {
		warning(""._NO_INFO."", "", "", 2);
	}
	foot();
}

function news_add() {
	global $prefix, $db, $admin_file, $confu, $stop;
	if (isset($_REQUEST['id'])) {
		$sid = intval($_REQUEST['id']);
		$result = $db->sql_query("SELECT s.catid, s.name, s.title, s.time, s.hometext, s.bodytext, s.field, s.ihome, s.acomm, s.associated, u.user_name FROM ".$prefix."_stories AS s LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id) WHERE sid='$sid'");
		list($cat, $uname, $subject, $time, $hometext, $bodytext, $field, $ihome, $acomm, $associated, $user_name) = $db->sql_fetchrow($result);
		$associated = explode("-", $associated);
		$postname = ($user_name) ? $user_name : (($uname) ? $uname : $confu['anonym']);
	} else {
		$sid = $_POST['sid'];
		$postname = $_POST['postname'];
		$subject = save_text($_POST['subject']);
		$associated = $_POST['associated'];
		$cat = $_POST['cat'];
		$hometext = save_text($_POST['hometext']);
		$bodytext = save_text($_POST['bodytext']);
		$field = fields_save($_POST['field']);
		$ihome = $_POST['ihome'];
		$acomm = $_POST['acomm'];
	}
	head();
	news_navi();
	if ($stop) warning($stop, "", "", 1);
	if ($hometext) preview($subject, $hometext, $bodytext, $field, "news");
	warning(""._PAGENOTE."", "", "", 2);
	open();
	echo "<form name=\"post\" action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._POSTEDBY.":</div><div class=\"center\">".get_user_search("postname", $postname, "25", "65", "400")."</div>"
	."<div class=\"left\">"._TITLE.":</div><div class=\"center\"><input type=\"text\" name=\"subject\" value=\"".$subject."\" maxlength=\"80\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._CATEGORY.":</div><div class=\"center\"><select name=\"cat\" class=\"admin\">".getcat("news", $cat)."</select></div>"
	."<div class=\"left\">"._ASSOTOPIC.":</div><div class=\"center\"><table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" class=\"admin\"><tr>";
	$result2 = $db->sql_query("SELECT id, title FROM ".$prefix."_categories WHERE modul='news' ORDER BY parentid, title");
	while (list($cid, $ctitle) = $db->sql_fetchrow($result2)) {
		if ($a == 2) {
			echo "</tr><tr>";
			$a = 0;
		}
		$check = "";
		if ($associated) foreach ($associated as $val) if ($val == $cid) $check = "checked";
		echo "<td><input type=\"checkbox\" name=\"associated[]\" value=\"$cid\" $check>$ctitle</td>";
		$a++;
	}
	echo "</tr></table></div>"
	."<div class=\"left\">"._TEXT.":</div><div class=\"center\">".textarea("1", "hometext", $hometext, "news", "5")."</div>"
	."<div class=\"left\">"._ENDTEXT.":</div><div class=\"center\">".textarea("2", "bodytext", $bodytext, "news", "15")."</div>"
	."".fields_in($field, "news").""
	."<div class=\"left\">"._CHNGSTORY.":</div><div class=\"center\" style=\"white-space: nowrap;\">".datetime(1, $time)."</div>"
	."<div class=\"left\">"._PUBHOME."</div><div class=\"center\">".radio_form($ihome, "ihome", 1)."</div>"
	."<div class=\"left\">"._C_16."</div><div class=\"center\">".radio_form($acomm, "acomm", 1)."</div>"
	."<div class=\"button\"><select name=\"posttype\">"
	."<option value=\"preview\">"._PREVIEW."</option>"
	."<option value=\"save\">"._SEND."</option></select>"
	."<input type=\"hidden\" name=\"sid\" value=\"$sid\">"
	."<input type=\"hidden\" name=\"op\" value=\"news_save\">"
	." <input type=\"submit\" value=\""._OK."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function news_save() {
	global $prefix, $db, $admin_file, $stop;
	$sid = intval($_POST['sid']);
	$postname = $_POST['postname'];
	$subject = save_text($_POST['subject']);
	$associated = (isset($_POST['associated'])) ? implode("-", $_POST['associated']) : "";
	$cat = $_POST['cat'];
	$hometext = save_text($_POST['hometext']);
	$bodytext = save_text($_POST['bodytext']);
	$field = fields_save($_POST['field']);
	$ihome = $_POST['ihome'];
	$acomm = $_POST['acomm'];
	$time = save_datetime();
	if (!$subject) $stop = ""._CERROR."";
	if (!$hometext) $stop = ""._CERROR1."";
	if (!$postname) $stop = ""._CERROR3."";
	if (!$stop && $_POST['posttype'] == "save") {
		$postid = (is_user_id($postname)) ? is_user_id($postname) : "";
		$postname = (!is_user_id($postname)) ? text_filter(substr($postname, 0, 25)) : "";
		if ($sid) {
			$db->sql_query("UPDATE ".$prefix."_stories SET catid='$cat', uid='$postid', name='$postname', title='$subject', time='$time', hometext='$hometext', bodytext='$bodytext', field='$field', ihome='$ihome', acomm='$acomm', associated='$associated', status='1' WHERE sid='$sid'");
		} else {
			$ip = getip();
			$db->sql_query("INSERT INTO ".$prefix."_stories (sid, catid, uid, name, title, time, hometext, bodytext, field, comments, counter, ihome, acomm, score, ratings, associated, ip_sender, status) VALUES (NULL, '$cat', '$postid', '$postname', '$subject', '$time', '$hometext', '$bodytext', '$field', '0', '0', '$ihome', '$acomm', '0', '0', '$associated', '$ip', '1')");
		}
		Header("Location: ".$admin_file.".php?op=news");
	} else {
		news_add();
	}
}

function news_conf() {
	global $prefix, $db, $admin_file;
	head();
	news_navi();
	include("config/config_news.php");
	$permtest = end_chmod("config/config_news.php", 666);
	if ($permtest) warning($permtest, "", "", 1);
	open();
	echo "<h2>"._GENSITEINFO."</h2>"
	."<form name=\"post\" action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._C_10.":</div><div class=\"center\"><input type='text' name='newcol' value='".$confn['newcol']."' maxlength='25' size='45' class=\"admin\"></div>"
	."<div class=\"left\">"._C_11.":</div><div class=\"center\"><input type='text' name='newasocnum' value='".$confn['newasocnum']."' maxlength='25' size='45' class=\"admin\"></div>"
	."<div class=\"left\">"._C_12.":</div><div class=\"center\"><input type='text' name='newnum' value='".$confn['newnum']."' maxlength='25' size='45' class=\"admin\"></div>"
	."<div class=\"left\">"._C_13.":</div><div class=\"center\"><input type='text' name='newlistnum' value='".$confn['newlistnum']."' maxlength='25' size='45' class=\"admin\"></div>"
	."<div class=\"left\">"._NEWSADD."</div><div class=\"center\">".radio_form($confn['add'], "add")."</div>"
	."<div class=\"left\">"._NEWSADDG."</div><div class=\"center\">".radio_form($confn['addquest'], "addquest")."</div>"
	."<div class=\"left\">"._C_15."</div><div class=\"center\">".radio_form($confn['newsub'], "newsub")."</div>"
	."<div class=\"left\">"._C_17."</div><div class=\"center\">".radio_form($confn['newdate'], "newdate")."</div>"
	."<div class=\"left\">"._C_18."</div><div class=\"center\">".radio_form($confn['newread'], "newread")."</div>"
	."<div class=\"left\">"._C_19."</div><div class=\"center\">".radio_form($confn['newrate'], "newrate")."</div>"
	."<div class=\"left\">"._C_20."</div><div class=\"center\">".radio_form($confn['newletter'], "newletter")."</div>"
	."<div class=\"left\">"._C_23."</div><div class=\"center\">".radio_form($confn['newassoc'], "newassoc")."</div>"
	."<div class=\"left\">"._C_32."</div><div class=\"center\">".radio_form($confn['newcatdesc'], "newcatdesc")."</div>"
	."<div class=\"button\"><input type='hidden' name='op' value='news_conf_save'><input type='submit' value='"._SAVECHANGES."' class=\"fbutton\"></div></form>";
	close();
	foot();
}

function news_conf_save() {
	global $admin_file;
	$content = "\$confn = array();\n"
	."\$confn['newcol'] = \"".$_POST['newcol']."\";\n"
	."\$confn['newasocnum'] = \"".$_POST['newasocnum']."\";\n"
	."\$confn['newnum'] = \"".$_POST['newnum']."\";\n"
	."\$confn['newlistnum'] = \"".$_POST['newlistnum']."\";\n"
	."\$confn['add'] = \"".$_POST['add']."\";\n"
	."\$confn['addquest'] = \"".$_POST['addquest']."\";\n"
	."\$confn['newsub'] = \"".$_POST['newsub']."\";\n"
	."\$confn['newdate'] = \"".$_POST['newdate']."\";\n"
	."\$confn['newread'] = \"".$_POST['newread']."\";\n"
	."\$confn['newrate'] = \"".$_POST['newrate']."\";\n"
	."\$confn['newletter'] = \"".$_POST['newletter']."\";\n"
	."\$confn['newassoc'] = \"".$_POST['newassoc']."\";\n"
	."\$confn['newcatdesc'] = \"".$_POST['newcatdesc']."\";\n";
	save_conf("config/config_news.php", $content);
	Header("Location: ".$admin_file.".php?op=news_conf");
}

switch($op) {
	case "news":
	news();
	break;
	
	case "news_add":
	news_add();
	break;
	
	case "news_save":
	news_save();
	break;
	
	case "news_delete":
	$db->sql_query("DELETE FROM ".$prefix."_stories WHERE sid='".$id."'");
	$db->sql_query("DELETE FROM ".$prefix."_comment WHERE cid='".$id."' AND modul='news'");
	referer("".$admin_file.".php?op=news");
	break;
	
	case "news_conf":
	news_conf();
	break;
	
	case "news_conf_save":
	news_conf_save();
	break;
	
	case "news_pin":
	$db->sql_query("UPDATE `".$prefix."_stories` SET `status`='".((intval($_GET['pin'])==1)?5:1)."' WHERE `sid`=".intval($_GET['id']));
	Header("Location: ".$admin_file.".php?op=news");
	break;
}
?>