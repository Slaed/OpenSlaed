<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("MODULE_FILE")) {
	Header("Location: ../../index.php");
	exit;
}
get_lang($conf['name']);
include("config/config_news.php");

function menu($logo) {
	global $conf, $confn;
	$home = "<a href=\"index.php?name=".$conf['name']."\" title=\""._HOME."\">"._HOME."</a>";
	$best = ($confn['newrate']) ? "<a href=\"index.php?name=".$conf['name']."&best=1\" title=\""._BEST."\">"._BEST."</a>" : "";
	$pop = ($confn['newrate']) ? "<a href=\"index.php?name=".$conf['name']."&hits=1\" title=\""._POP."\">"._POP."</a>" : "";
	$liste = "<a href=\"index.php?name=".$conf['name']."&op=liste\" title=\""._LIST."\">"._LIST."</a>";
	$add = ((is_user() && $confn['add'] == 1) || (!is_user() && $confn['addquest'] == 1)) ? "<a href=\"index.php?name=".$conf['name']."&op=add\" title=\""._ADD."\">"._ADD."</a>" : "";
	$navi = "[ <a href=\"index.php?name=".$conf['name']."\" title=\""._HOME."\">"._HOME."</a>";
	$navi .= ($confn['newrate']) ? " | <a href=\"index.php?name=".$conf['name']."&best=1\" title=\""._BEST."\">"._BEST."</a> | <a href=\"index.php?name=".$conf['name']."&hits=1\" title=\""._POP."\">"._POP."</a>" : "";
	$navi .= " | <a href=\"index.php?name=".$conf['name']."&op=liste\" title=\""._LIST."\">"._LIST."</a>";
	$navi .= ((is_user() && $confn['add'] == 1) || (!is_user() && $confn['addquest'] == 1)) ? " | <a href=\"index.php?name=".$conf['name']."&op=add\" title=\""._ADD."\">"._ADD."</a>" : "";
	$navi .= " ]";
	search($logo, $conf['name'], $navi, $home, $best, $pop, $liste, $add);
}

function news() {
	global $prefix, $db, $admin_file, $user, $conf, $confu, $confn, $home, $pagetitle, $currentlang;
	$lang = ($conf['multilingual']) ? "AND (c.language='$currentlang' OR c.language='')" : "";
	$newnum = (isset($user[3]) && $user[3] <= intval($confn['newnum']) && $confu['news'] == 1) ? intval($user[3]) : intval($confn['newnum']);
	$sbest = (isset($_GET['best'])) ? 1 : 0;
	$shits = (isset($_GET['hits'])) ? 1 : 0;
	$scat=0; if (isset($_GET['cat'])) {if (is_numeric($_GET['cat'])) $scat=intval($_GET['cat']); else list($scat) = $db->sql_fetchrow($db->sql_query("SELECT `id` FROM ".$prefix."_categories WHERE `url`='".preg_replace("/[^0-9a-z-]+/", "", $_GET['cat'])."'"));}
	if ($sbest && $confn['newrate']) {
		$caton = 0;
		$field = "best=1&";
		$order = "WHERE time <= now() AND status!='0' ".$lang." ORDER BY score DESC";
		$ordernum = "time <= now() AND status!='0'";
		$news_logo = ""._BEST."";
		$pagetitle = "".$conf['defis']." "._NEWS." ".$conf['defis']." $news_logo";
	} elseif ($shits && $confn['newrate']) {
		$caton = 0;
		$field = "hits=1&";
		$order = "WHERE time <= now() AND status!='0' ".$lang." ORDER BY counter DESC";
		$ordernum = "time <= now() AND status!='0'";
		$news_logo = ""._POP."";
		$pagetitle = "".$conf['defis']." "._NEWS." ".$conf['defis']." $news_logo";
	} elseif ($scat) {
		$caton = 1;
		$field = "cat=".url_fun(array('url'=>$_GET['cat']))."&";
		list($cat_title, $cat_description) = $db->sql_fetchrow($db->sql_query("SELECT title, description FROM ".$prefix."_categories WHERE id='$scat'"));
		$order = "WHERE catid='$scat' AND time <= now() AND status!='0' ".$lang." ORDER BY time DESC";
		$ordernum = "catid='$scat' AND time <= now() AND status!='0'";
		$pagetitle = "".$conf['defis']." "._NEWS." ".$conf['defis']." $cat_title";
	} else {
		$caton = 1;
		$field = "";
		$order = (!$home) ? "WHERE time <= now() AND status!='0' ".$lang." ORDER BY time DESC" : "WHERE ihome='0' AND time <= now() AND status!='0' ".$lang." ORDER BY time DESC";
		$ordernum = (!$home) ? "time <= now() AND status!='0'" : "ihome='0' AND time <= now() AND status!='0'";
		$news_logo = ""._NEWS."";
		$pagetitle = "".$conf['defis']." $news_logo";
	}
	head();
	if (!$home) {
		if ($scat) {
			menu($cat_title);
		} else {
			menu($news_logo);
		}
		if ($caton == 1) categories($conf['name'], $confn['newcol'], $confn['newsub'], $confn['newcatdesc'], $scat);
	}
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $newnum;
	$offset = intval($offset);
	$result = $db->sql_query("SELECT s.sid, s.catid, s.name, s.title, UNIX_TIMESTAMP(s.time) as formatted, s.hometext, s.comments, s.counter, s.acomm, s.score, s.ratings, s.url, c.id, c.title, c.description, c.img, c.url, u.user_name FROM ".$prefix."_stories AS s LEFT JOIN ".$prefix."_categories AS c ON (s.catid=c.id) LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id) ".$order." LIMIT $offset, $newnum");
	if ($db->sql_numrows($result) > 0) {
		while (list($sid, $catid, $uname, $stitle, $formatted, $hometext, $comments, $counter, $acomm, $score, $ratings, $surl, $cid, $ctitle, $cdescription, $cimg, $curl, $user_name) = $db->sql_fetchrow($result)) {
			$time = date(""._DATESTRING."", $formatted);
			$title = "<a href=\"index.php?name=".$conf['name']."&op=view&id=$surl\" title=\"$stitle\">".$stitle."</a> ".new_graphic($formatted)."";
			$read = "<a href=\"index.php?name=".$conf['name']."&op=view&id=$surl\" title=\"$stitle\">"._READMORE."</a>";
			$post = ($user_name) ? " "._POSTEDBY.": ".user_info($user_name, 1)."" : (($uname) ? " "._POSTEDBY.": ".$uname."" : " "._POSTEDBY.": ".$confu['anonym']."");
			$ndate = ($confn['newdate']) ? " "._DATE.": ".$time."" : "";
			$reads = ($confn['newread']) ? " "._READS.": ".$counter."" : "";
			if (!$acomm) {
				if ($comments == 0) {
					$comm = " <a href=\"index.php?name=".$conf['name']."&op=view&id=$surl#$sid\" title=\"$stitle\">"._COMMENTS."</a>";
				} elseif ($comments == 1) {
					$comm = " <a href=\"index.php?name=".$conf['name']."&op=view&id=$surl#$sid\" title=\"$stitle\">"._COMMENT.": $comments</a>";
				} elseif ($comments > 1) {
					$comm = " <a href=\"index.php?name=".$conf['name']."&op=view&id=$surl#$sid\" title=\"$stitle\">"._COMMENTS.": $comments</a>";
				}
			} else {
				$comm = "";
			}
			$arating = " ".ajax_rating(0, $sid, $conf['name'], $ratings, $score)."";
			$print = " ".ad_print("index.php?name=".$conf['name']."&op=printe&id=".$surl."")."";
			$admin = (is_moder($conf['name'])) ? " ".ad_edit("".$admin_file.".php?op=news_add&id=".$sid."")." ".ad_delete("".$admin_file.".php?op=news_delete&id=".$sid."", $stitle)."" : "";
			$cdescription = ($cdescription) ? $cdescription : $ctitle;
			$cimg = ($cimg) ? "<a href=\"index.php?name=".$conf['name']."&cat=$curl\"><img src=\"images/categories/".$cimg."\" border=\"0\" alt=\"$cdescription\" title=\"$cdescription\" align=\"right\" hspace=\"10\" vspace=\"10\"></a>" : "";
			$link = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"75%\" align=\"left\"><b>".$read."</b>".$post."".$ndate."".$reads."".$comm."</td><td>".$arating."</td><td align=\"right\">".$print."".$admin."</td></tr></table>";
			basic($cid, $cimg, $ctitle, $sid, $title, bb_decode($hometext, $conf['name']), $link, $read, $post, $ndate, $reads, $comm, $arating, $print, $admin);
		}
		num_article($conf['name'], $newnum, $field, "sid", "_stories", "catid", $ordernum);
	}
	foot();
}

function liste() {
	global $prefix, $db, $pagetitle, $conf, $confu, $confn, $currentlang;
	$lang = ($conf['multilingual']) ? "AND (c.language='$currentlang' OR c.language='')" : "";
	$newlistnum = intval($confn['newlistnum']);
	$let = (isset($_GET['let'])) ? mb_substr($_GET['let'], 0, 1, "utf-8") : "";
	if ($let) {
		$field = "op=liste&let=".urlencode($let)."&";
		$pagetitle = "".$conf['defis']." "._NEWS." ".$conf['defis']." "._LIST." ".$conf['defis']." $let";
		$order = "WHERE UPPER(s.title) LIKE '".$let."%' AND time <= now() AND status!='0'";
	} else {
		$field = "op=liste&";
		$pagetitle = "".$conf['defis']." "._NEWS." ".$conf['defis']." "._LIST."";
		$order = "WHERE time <= now() AND status!='0'";
	}
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $newlistnum;
	$offset = intval($offset);
	$result = $db->sql_query("SELECT s.sid, s.catid, s.name, s.title, s.time, s.url, c.id, c.title, c.url, u.user_name FROM ".$prefix."_stories AS s LEFT JOIN ".$prefix."_categories AS c ON (s.catid=c.id) LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id) ".$order." ".$lang." ORDER BY time DESC LIMIT $offset, $newlistnum");
	head();
	menu(""._LIST."");
	if ($db->sql_numrows($result) > 0) {
		open();
		if ($confn['newletter']) letter($conf['name']);
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr>"
		."<th>"._ID."</th><th>"._TITLE."</th><th>"._CATEGORY."</th><th>"._DATE."</th><th>"._POSTEDBY."</th></tr>";
		while (list($sid, $catid, $uname, $stitle, $time, $surl, $cid, $ctitle, $curl, $user_name) = $db->sql_fetchrow($result)) {
			$ctitle = (!$ctitle) ? ""._NO."" : "<a href=\"index.php?name=".$conf['name']."&cat=$curl\" title=\"".$ctitle."\">".cutstr($ctitle, 10)."</a>";
			$post = ($user_name) ? user_info($user_name, 1) : (($uname) ? $uname : $confu['anonym']);
			echo "<tr class=\"bgcolor1\">"
			."<td align=\"center\">".$sid."</td>"
			."<td><a href=\"index.php?name=".$conf['name']."&op=view&id=$surl\" title=\"".$stitle."\">".cutstr($stitle, 35)."</a></td>"
			."<td align=\"center\">".$ctitle."</td>"
			."<td align=\"center\">".format_time($time)."</td>"
			."<td align=\"center\">".$post."</td></tr>";
		}
		echo "</table>";
		close();
		$ordernum = ($let) ? "UPPER(title) LIKE '".$let."%' AND time <= now() AND status!='0'" : "time <= now() AND status!='0'";
		num_article($conf['name'], $newlistnum, $field, "sid", "_stories", "catid", $ordernum);
	} else {
		warning(""._NO_INFO."", "", "", 2);
	}
	foot();
}

function printe() {
	global $prefix, $db, $ThemeSel, $pagetitle, $conf, $confn;
	$id = url_fun(array('url'=>$_GET['id']));
	$result = $db->sql_query("SELECT title, time, hometext, bodytext FROM ".$prefix."_stories WHERE ".url_fun(array('url'=>$id,'id'=>'sid','row'=>'url'),2)." AND time <= now() AND status!='0'");
	if ($db->sql_numrows($result) == 1) {
		$db->sql_query("UPDATE ".$prefix."_stories SET counter=counter+1 WHERE ".url_fun(array('url'=>$id,'id'=>'sid','row'=>'url'),2));
		list($stitle, $date, $hometext, $bodytext) = $db->sql_fetchrow($result);
		if (file_exists("templates/$ThemeSel/index.php")) {
			include("templates/$ThemeSel/index.php");
		} else {
			include("function/template.php");
		}
		$conf['defis'] = urldecode($conf['defis']);
		$title = "$stitle ".$conf['defis']." "._NEWS." ".$conf['defis']." ".$conf['sitename']."";
		$ptitle = "".format_time($date)." - ".$stitle."";
		$text = ($bodytext) ? "".bb_decode($hometext, $conf['name'])."<br /><br />".bb_decode($bodytext, $conf['name'])."" : bb_decode($hometext, $conf['name']);
		$url = ""._COMESFROM.": <a href=\"".$conf['homeurl']."\" title=\"".$conf['sitename']."\">".$conf['homeurl']."</a><br />"._THEURL.": <a href=\"".$conf['homeurl']."/index.php?name=".$conf['name']."&op=view&id=$id\" title=\"".$stitle."\">".$conf['homeurl']."/index.php?name=".$conf['name']."&op=view&id=$id</a>";
		prints($title, $ptitle, str_replace("[pagebreak]", "", $text), $url);
	} else {
		Header("Location: index.php?name=".$conf['name']."");
	}
}

function view() {
	global $prefix, $db, $admin_file, $conf, $confu, $confn, $pagetitle, $hometext, $bodytext;
	$id = url_fun(array('url'=>$_GET['id']));
	$pag = intval($_GET['pag']);
	$word = ($_GET['word']) ? text_filter($_GET['word']) : "";
	$result = $db->sql_query("SELECT s.sid, s.catid, s.name, s.title, s.time, s.hometext, s.bodytext, s.field, s.comments, s.counter, s.acomm, s.score, s.ratings, s.associated, s.url, c.id, c.title, c.description, c.img, c.url, u.user_name FROM ".$prefix."_stories AS s LEFT JOIN ".$prefix."_categories AS c ON (s.catid=c.id) LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id) WHERE ".url_fun(array('url'=>$id,'id'=>'sid','row'=>'s.url'),2)." AND time <= now() AND status!='0'");
	if ($db->sql_numrows($result) == 1) {
		$db->sql_query("UPDATE ".$prefix."_stories SET counter=counter+1 WHERE ".url_fun(array('url'=>$id,'id'=>'sid','row'=>'url'),2));
		list($sid, $catid, $uname, $title, $time, $hometext, $bodytext, $field, $comments, $counter, $acomm, $score, $ratings, $associated, $surl, $cid, $ctitle, $cdescription, $cimg, $curl, $user_name) = $db->sql_fetchrow($result);
		$pagetitle = (intval($catid)) ? "".$conf['defis']." "._NEWS." ".$conf['defis']." $ctitle ".$conf['defis']." $title" : "".$conf['defis']." "._NEWS." ".$conf['defis']." $title";
		head();
		menu(""._NEWS."");
		$fields = fields_out($field, $conf['name']);
		$fields = ($fields) ? "<br /><br />".$fields."" : "";
		$text = (!$bodytext) ? "".$hometext."".$fields."" : "".$hometext."<br /><br />".$bodytext."".$fields."";
		$conpag = explode("[pagebreak]", $text);
		$pageno = count($conpag);
		$pag = ($pag == "" || $pag < 1) ? 1 : $pag;
		if ($pag > $pageno) $pag = $pageno;
		$arrayelement = (int)$pag;
		$arrayelement--;
		$post = ($user_name) ? ""._POSTEDBY.": ".user_info($user_name, 1)."" : (($uname) ? ""._POSTEDBY.": ".$uname."" : ""._POSTEDBY.": ".$confu['anonym']."");
		$ndate = ($confn['newdate']) ? " "._DATE.": ".format_time($time)."" : "";
		$reads = ($confn['newread']) ? " "._READS.": ".$counter."" : "";
		$arating = " ".ajax_rating(1, $sid, $conf['name'], $ratings, $score)."";
		$print = " ".ad_print("index.php?name=".$conf['name']."&op=printe&id=".$surl."")."";
		$admin = (is_moder($conf['name'])) ? " ".ad_edit("".$admin_file.".php?op=news_add&id=".$sid."")." ".ad_delete("".$admin_file.".php?op=news_delete&id=".$sid."", $title)."" : "";
		$cdescription = ($cdescription) ? $cdescription : $ctitle;
		$cimg = ($cimg) ? "<a href=\"index.php?name=".$conf['name']."&cat=$curl\"><img src=\"images/categories/".$cimg."\" border=\"0\" alt=\"$cdescription\" title=\"$cdescription\" align=\"right\" hspace=\"10\" vspace=\"10\"></a>" : "";
		$link = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"75%\" align=\"left\">".$post."".$ndate."".$reads."</td><td>".$arating."</td><td align=\"right\">".$print."".$admin."</td></tr></table>";
		basic($cid, $cimg, $ctitle, $sid, search_color($title, $word), search_color(bb_decode($conpag[$arrayelement], $conf['name']), $word), $link, $read, $post, $ndate, $reads, $comm, $arating, $print, $admin);
		num_pages($conf['name'], 1, $pageno, 1, "op=view&id=".$id."&");
		if ($confn['newassoc']) {
			if ($associated[strlen($associated)-1] == "-") $associated = substr($associated, 0, -1);
			$asso = str_replace("-", ",", $associated);
			$limit = intval($confn['newasocnum']);
			$result = $db->sql_query("SELECT sid, title, time, url FROM ".$prefix."_stories WHERE catid IN (".$asso.") AND sid!='$sid' AND time <= now() AND status!='0' ORDER BY time DESC LIMIT 0, ".$limit."");
			if ($db->sql_numrows($result) > 0) {
				open();
				echo "<h2 style=\"margin: 0 0 5px 0;\">"._ASSTORY."</h2>"
				."<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
				while(list($s_sid, $title, $time, $url) = $db->sql_fetchrow($result)) {
					echo "<tr><td><a href=\"index.php?name=".$conf['name']."&op=view&id=$url\" title=\"$title\"><img src=\"".img_find("all/news")."\" border=\"0\"></a></td><td>".format_time($time)." - <a href=\"index.php?name=".$conf['name']."&op=view&id=$url\" title=\"$title\">$title</a></td></tr>";
				}
				echo "</table>";
				close();
			}
		}
		if (!$acomm) {
			echo "<a name=\"$sid\"></a>";
			show_com($sid);
		}
		foot();
	} else {
		Header("Location: index.php?name=".$conf['name']."");
	}
}

function add() {
	global $prefix, $db, $user, $conf, $confn, $confu, $pagetitle, $stop;
	$pagetitle = "".$conf['defis']." "._NEWS." ".$conf['defis']." "._ADD."";
	if ((is_user() && $confn['add'] == 1) || (!is_user() && $confn['addquest'] == 1)) {
		head();
		menu(""._ADD."");
		if ($stop) warning($stop, "", "", 1);
		$subject = save_text($_POST['subject']);
		$catid = intval($_POST['catid']);
		$hometext = save_text($_POST['hometext']);
		$bodytext = save_text($_POST['bodytext']);
		$field = fields_save($_POST['field']);
		$postname = text_filter(substr($_POST['postname'], 0, 25));
		if ($hometext) preview($subject, $hometext, $bodytext, $field, $conf['name']);
		warning(""._SUBMIT."", "", "", 2);
		open();
		echo "<form name=\"post\" action=\"index.php?name=".$conf['name']."\" method=\"post\">";
		if (is_user()) {
			echo "<div class=\"left\">"._YOURNAME.":</div><div class=\"center\">".text_filter(substr($user[1], 0, 25))."</div>";
		} else {
			$postname = ($postname) ? $postname : $confu['anonym'];
			echo "<div class=\"left\">"._YOURNAME.":</div><div class=\"center\"><input type=\"text\" name=\"postname\" value=\"".$postname."\" size=\"65\" class=\"".$conf['style']."\"></div>";
		}
		echo "<div class=\"left\">"._TITLE.":</div><div class=\"center\"><input type=\"text\" name=\"subject\" value=\"".$subject."\" maxlength=\"80\" size=\"65\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._CATEGORY.":</div><div class=\"center\"><select name=\"catid\" class=\"".$conf['style']."\">".getcat($conf['name'], $catid)."</select></div>"
		."<div class=\"left\">"._TEXT.":</div><div class=\"center\">".textarea("1", "hometext", $hometext, $conf['name'], "5")."</div>"
		."<div class=\"left\">"._ENDTEXT.":</div><div class=\"center\">".textarea("2", "bodytext", $bodytext, $conf['name'], "15")."</div>"
		."".fields_in($field, $conf['name']).""
		."".captcha_random().""
		."<div class=\"button\"><select name=\"posttype\">"
		."<option value=\"preview\">"._PREVIEW."</option>"
		."<option value=\"save\">"._SEND."</option></select>"
		."<input type=\"hidden\" name=\"op\" value=\"send\">"
		." <input type=\"submit\" value=\""._OK."\" class=\"fbutton\"></div></form>";
		close();
		foot();
	} else {
		Header("Location: index.php?name=".$conf['name']."");
	}
}

function send() {
	global $prefix, $db, $user, $conf, $confn, $stop;
	if ((is_user() && $confn['add'] == 1) || (!is_user() && $confn['addquest'] == 1)) {
		$postname = text_filter(substr($_POST['postname'], 0, 25));
		$subject = save_text($_POST['subject']);
		$hometext = save_text($_POST['hometext']);
		$bodytext = save_text($_POST['bodytext']);
		$field = fields_save($_POST['field']);
		$catid = intval($_POST['catid']);
		if (!$subject) $stop = ""._CERROR."";
		if (!$hometext) $stop = ""._CERROR1."";
		if (!$postname && !is_user()) $stop = ""._CERROR3."";
		if (captcha_check()) $stop = ""._SECCODEINCOR."";
		if (!$stop && $_POST['posttype'] == "save") {
			$postid = (is_user()) ? intval($user[0]) : "";
			$postname = (!is_user()) ? $postname : "";
			$ip = getip();
			$db->sql_query("INSERT INTO ".$prefix."_stories (sid, catid, uid, name, title, time, hometext, bodytext, field, comments, counter, ihome, acomm, score, ratings, associated, ip_sender, status, url) VALUES (NULL, '$catid', '$postid', '$postname', '$subject', now(), '$hometext', '$bodytext', '$field', '0', '0', '0', '0', '0', '0', '0', '$ip', '0', '".url_uniq(array('url'=>$subject, 'table'=>'_stories'),70)."')");
			update_points(31);
			head();
			menu(""._ADD."");
			warning(""._SUBTEXT."", "?name=".$conf['name']."", 10, 2);
			foot();
		} else {
			add();
		}
	} else {
		Header("Location: index.php?name=".$conf['name']."");
	}
}

switch($op) {
	default:
	news();
	break;
	
	case "liste":
	liste();
	break;
	
	case "printe":
	printe();
	break;
	
	case "view":
	view();
	break;
	
	case "add":
	add();
	break;
	
	case "send":
	send();
	break;
	
	case "save_com":
	save_com();
	break;
}
?>