<?php
/*
===================================================================
   Copyright © 2007 by Francisco Burzi
   http://phpnuke.org

   AntiSlaed CMS based on:

   RNuke (http://rusnuke.com)
   EdogsNuke (http://edogs.ru)
   XNuke (http://xnuke.info)
   phpBB (http://phpbb.com)

   Code optimization, adaptation and other modifications by Sergey Next / ArtGlobals, December 2008
   http://www.artglobals.com

   Code modifications by AntiSlaed Team, June 2008
   http://antislaedcms.ru

   Please contact us, if you have any questions about AntiSlaedCMS
   mailto: admin@antislaedcms.ru

   AntiSlaed CMS is free software. You can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License.
===================================================================
*/

if (!defined("MODULE_FILE")) {
	header("Location: ../../index.php");
	exit;
}
get_lang($conf['name']);
include("config/config_pages.php");

function menu($logo) {
	global $conf, $confp;
	$home = "<a href=\"index.php?name=".$conf['name']."\" title=\""._HOME."\">"._HOME."</a>";
	$best = ($confp['rate']) ? "<a href=\"index.php?name=".$conf['name']."&best=1\" title=\""._BEST."\">"._BEST."</a>" : "";
	$pop = ($confp['rate']) ? "<a href=\"index.php?name=".$conf['name']."&hits=1\" title=\""._POP."\">"._POP."</a>" : "";
	$liste = "<a href=\"index.php?name=".$conf['name']."&op=liste\" title=\""._LIST."\">"._LIST."</a>";
	$add = ((is_user() && $confp['add'] == 1) || (!is_user() && $confp['addquest'] == 1)) ? "<a href=\"index.php?name=".$conf['name']."&op=add\" title=\""._ADD."\">"._ADD."</a>" : "";
	$navi = "[ <a href=\"index.php?name=".$conf['name']."\" title=\""._HOME."\">"._HOME."</a>";
	$navi .= ($confp['rate']) ? " | <a href=\"index.php?name=".$conf['name']."&best=1\" title=\""._BEST."\">"._BEST."</a> | <a href=\"index.php?name=".$conf['name']."&hits=1\" title=\""._POP."\">"._POP."</a>" : "";
	$navi .= " | <a href=\"index.php?name=".$conf['name']."&op=liste\" title=\""._LIST."\">"._LIST."</a>";
	$navi .= ((is_user() && $confp['add'] == 1) || (!is_user() && $confp['addquest'] == 1)) ? " | <a href=\"index.php?name=".$conf['name']."&op=add\" title=\""._ADD."\">"._ADD."</a>" : "";
	$navi .= " ]";
	search($logo, $conf['name'], $navi, $home, $best, $pop, $liste, $add);
}

function pages() {
	global $prefix, $db, $admin_file, $user, $conf, $confp, $confu, $home, $pagetitle;
	$cwhere = catmids($conf['name'], "s.catid");
	$newnum = user_news($user[3], $confp['num']);
	$sbest = (isset($_GET['best'])) ? 1 : 0;
	$shits = (isset($_GET['hits'])) ? 1 : 0;
	$scat = (isset($_GET['cat'])) ? intval($_GET['cat']) : 0;
	if ($sbest && $confp['rate']) {
		$caton = 0;
		$field = "best=1&";
		$order = "WHERE s.time <= now() AND s.status!='0' ".$cwhere." ORDER BY s.score DESC";
		$ordernum = "time <= now() AND status!='0'";
		$page_logo = _BEST;
		$pagetitle = $conf['defis']." "._PAGES." ".$conf['defis']." $page_logo";
	} elseif ($shits && $confp['rate']) {
		$caton = 0;
		$field = "hits=1&";
		$order = "WHERE s.time <= now() AND s.status!='0' ".$cwhere." ORDER BY s.counter DESC";
		$ordernum = "time <= now() AND status!='0'";
		$page_logo = _POP;
		$pagetitle = $conf['defis']." "._PAGES." ".$conf['defis']." $page_logo";
	} elseif ($scat) {
		$caton = 1;
		$field = "cat=$scat&";
		list($cat_title, $cat_description) = $db->sql_fetchrow($db->sql_query("SELECT title, description FROM ".$prefix."_categories WHERE id='$scat'"));
		$order = "WHERE s.catid='$scat' AND s.time <= now() AND s.status!='0' ".$cwhere." ORDER BY s.time DESC";
		$ordernum = "catid='$scat' AND time <= now() AND status!='0'";
		$pagetitle = $conf['defis']." "._PAGES." ".$conf['defis']." $cat_title";
	} else {
		$caton = 1;
		$field = "";
		$order = "WHERE s.time <= now() AND s.status!='0' ".$cwhere." ORDER BY s.time DESC";
		$ordernum = "time <= now() AND status!='0'";
		$page_logo = _PAGES;
		$pagetitle = $conf['defis']." $page_logo";
	}
	head();
	if (!$home) {
		if ($scat) {
			menu($cat_title);
		} else {
			menu($page_logo);
		}
		if ($scat) templ("catlink", catlink($conf['name'], $scat, $confp['defis'], _PAGES));
		if ($caton == 1) categories($conf['name'], $confp['col'], $confp['sub'], $confp['catdesc'], $scat);
	}
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $newnum;
	$offset = intval($offset);
	$result = $db->sql_query("SELECT s.pid, s.catid, s.name, s.title, UNIX_TIMESTAMP(s.time) as formatted, s.hometext, s.comments, s.counter, s.acomm, s.score, s.ratings, s.banner, c.id, c.title, c.description, c.img, u.user_name FROM ".$prefix."_page AS s LEFT JOIN ".$prefix."_categories AS c ON (s.catid=c.id) LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id) ".$order." LIMIT $offset, $newnum");
	if ($db->sql_numrows($result) > 0) {
		while (list($pid, $catid, $uname, $stitle, $formatted, $hometext, $comments, $counter, $acomm, $score, $ratings, $banner, $cid, $ctitle, $cdescription, $cimg, $user_name) = $db->sql_fetchrow($result)) {
			$time = date(_DATESTRING, $formatted);
			$title = "<a href=\"index.php?name=".$conf['name']."&op=view&id=$pid\" title=\"$stitle\">".$stitle."</a> ".new_graphic($formatted);
			$read = "<a href=\"index.php?name=".$conf['name']."&op=view&id=$pid\" title=\"$stitle\">"._READMORE."</a>";
			$post = ($user_name) ? " "._POSTEDBY.": ".user_info($user_name, 1) : (($uname) ? " "._POSTEDBY.": ".$uname : " "._POSTEDBY.": ".$confu['anonym']);
			$ndate = ($confp['date']) ? " "._DATE.": ".$time : "";
			$reads = ($confp['read']) ? " "._READS.": ".$counter : "";
			if ($acomm) {
				if ($comments == 0) {
					$comm = " <a href=\"index.php?name=".$conf['name']."&op=view&id=$pid#$pid\" title=\"$stitle\">"._COMMENTS."</a>";
				} elseif ($comments == 1) {
					$comm = " <a href=\"index.php?name=".$conf['name']."&op=view&id=$pid#$pid\" title=\"$stitle\">"._COMMENT.": $comments</a>";
				} elseif ($comments > 1) {
					$comm= " <a href=\"index.php?name=".$conf['name']."&op=view&id=$pid#$pid\" title=\"$stitle\">"._COMMENTS.": $comments</a>";
				}
			} else {
				$comm = "";
			}
                  $hometext = bb_decode($hometext, $conf['name']);
                  if ($confp['key'] && $conf['keywords']){
                  $words = explode(', ', $conf['keywords']);
                    foreach($words as $word){
                    $hometext = preg_replace('#('.$word.')#i', '<strong>\\1</strong>', $hometext);
                      while(preg_match('#<([^>]*)<strong>([^<>]+)</strong>([^>]*)>#i', $hometext)){
                      $hometext = preg_replace('#<([^>]*)<strong>([^<>]+)</strong>([^>]*)>#i', '<\\1\\2\\3>', $hometext);
                      }
                      while(preg_match('#<strong><strong>([^<>]+)</strong></strong>#i', $hometext)){                     
                      $hometext = preg_replace('#<strong><strong>([^<>]+)</strong></strong>#i', '<strong>\\1</strong>', $hometext);
                      }
                    }
                  }
			$arating = " ".ajax_rating(0, $pid, $conf['name'], $ratings, $score, "");
			$print = " ".ad_print("index.php?name=".$conf['name']."&op=printe&id=".$pid);
			$admin = (is_moder($conf['name'])) ? " ".add_menu($pid, "<a href=\"".$admin_file.".php?op=page_add&id=".$pid."\" title=\""._FULLEDIT."\">"._FULLEDIT."</a>||<a href=\"".$admin_file.".php?op=page_delete&id=".$pid."\" OnClick=\"return DelCheck(this, '"._DELETE." &quot;$stitle&quot;?');\" title=\""._ONDELETE."\">"._ONDELETE."</a>") : "";
			$cdescription = ($cdescription) ? $cdescription : $ctitle;
			$cimg = ($cimg) ? "<a href=\"index.php?name=".$conf['name']."&cat=$cid\"><img src=\"images/categories/".$cimg."\" border=\"0\" alt=\"$cdescription\" title=\"$cdescription\" align=\"right\" hspace=\"10\" vspace=\"10\"></a>" : "";
			$link = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"75%\" align=\"left\"><b>".$read."</b>".$post.$ndate.$reads.$comm."</td><td>".$arating."</td><td align=\"right\">".$print.$admin."</td></tr></table>";
			basic($cid, $cimg, $ctitle, $pid, $title, $hometext, $link, $read, $post, $ndate, $reads, $comm, $arating, $print, $admin);
                  echo bb_decode($banner, $conf['name']);
		}
		num_article($conf['name'], $newnum, $field, "pid", "_page", "catid", $ordernum);
	}
	foot();
}

function liste() {
	global $prefix, $db, $pagetitle, $conf, $confu, $confp;
	$cwhere = catmids($conf['name'], "s.catid");
	$listnum = intval($confp['listnum']);
	$let = (!preg_match("/[^a-zA-Zа-яА-Я0-9]/", $_GET['let'])) ? $_GET['let'] : "";
	if ($let) {
		$field = "op=liste&let=".urlencode($let)."&";
		$pagetitle = $conf['defis']." "._PAGES." ".$conf['defis']." "._LIST." ".$conf['defis']." $let";
		$order = "WHERE UPPER(s.title) LIKE '".$let."%' AND s.time <= now() AND s.status!='0'";
	} else {
		$field = "op=liste&";
		$pagetitle = $conf['defis']." "._PAGES." ".$conf['defis']." "._LIST;
		$order = "WHERE s.time <= now() AND s.status!='0'";
	}
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $listnum;
	$offset = intval($offset);
	$result = $db->sql_query("SELECT s.pid, s.catid, s.name, s.title, s.time, c.id, c.title, u.user_name FROM ".$prefix."_page AS s LEFT JOIN ".$prefix."_categories AS c ON (s.catid=c.id) LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id) ".$order." ".$cwhere." ORDER BY time DESC LIMIT $offset, $listnum");
	head();
	menu(_LIST);
	if ($db->sql_numrows($result) > 0) {
		open();
		if ($confp['letter']) letter($conf['name']);
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr>"
		."<th>"._ID."</th><th>"._TITLE."</th><th>"._CATEGORY."</th><th>"._DATE."</th><th>"._POSTEDBY."</th></tr>";
		while (list($pid, $catid, $uname, $stitle, $time, $cid, $ctitle, $user_name) = $db->sql_fetchrow($result)) {
			$ctitle = (!$ctitle) ? _NO : "<a href=\"index.php?name=".$conf['name']."&cat=$cid\" title=\"".$ctitle."\">".cutstr($ctitle, 10)."</a>";
			$post = ($user_name) ? user_info($user_name, 1) : (($uname) ? $uname : $confu['anonym']);
			echo "<tr class=\"bgcolor1\">"
			."<td align=\"center\">".$pid."</td>"
			."<td><a href=\"index.php?name=".$conf['name']."&op=view&id=$pid\" title=\"".$stitle."\">".cutstr($stitle, 35)."</a></td>"
			."<td align=\"center\">".$ctitle."</td>"
			."<td align=\"center\">".format_time($time)."</td>"
			."<td align=\"center\">".$post."</td></tr>";
		}
		echo "</table>";
		close();
		$ordernum = ($let) ? "UPPER(title) LIKE '".$let."%' AND time <= now() AND status!='0'" : "time <= now() AND status!='0'";
		num_article($conf['name'], $listnum, $field, "pid", "_page", "catid", $ordernum);
	} else {
		warning(_NO_INFO, "", "", 2);
	}
	foot();
}

function printe() {
	global $prefix, $db, $pagetitle, $conf, $confp;
	$id = intval($_GET['id']);
	$cwhere = catmids($conf['name'], "catid");
	$result = $db->sql_query("SELECT title, time, hometext, bodytext FROM ".$prefix."_page WHERE pid='$id' AND time <= now() AND status!='0' ".$cwhere);
	if ($db->sql_numrows($result) == 1) {
		$db->sql_query("UPDATE ".$prefix."_page SET counter=counter+1 WHERE pid='$id'");
		list($stitle, $date, $hometext, $bodytext) = $db->sql_fetchrow($result);
		get_theme_inc();
		$conf['defis'] = urldecode($conf['defis']);
		$title = "$stitle ".$conf['defis']." "._PAGES." ".$conf['defis']." ".$conf['sitename'];
		$ptitle = format_time($date)." - ".$stitle;
		$text = ($bodytext) ? bb_decode($hometext, $conf['name'])."<br><br>".bb_decode($bodytext, $conf['name']) : bb_decode($hometext, $conf['name']);
		$url = _COMESFROM.": <a href=\"".$conf['homeurl']."\" title=\"".$conf['sitename']."\">".$conf['homeurl']."</a><br>"._THEURL.": <a href=\"".$conf['homeurl']."/index.php?name=".$conf['name']."&op=view&id=$id\" title=\"".$stitle."\">".$conf['homeurl']."/index.php?name=".$conf['name']."&op=view&id=$id</a>";
		prints($title, $ptitle, str_replace("[pagebreak]", "", $text), $url);
	} else {
		header("Location: index.php?name=".$conf['name']);
	}
}

function view() {
	global $db, $prefix, $admin_file, $conf, $confu, $confp, $pagetitle, $hometext, $bodytext;
	$id = intval($_GET['id']);
	$pag = intval($_GET['pag']);
	$word = ($_GET['word']) ? text_filter($_GET['word']) : "";
	$cwhere = catmids($conf['name'], "s.catid");
	$result = $db->sql_query("SELECT s.pid, s.catid, s.name, s.title, s.time, s.hometext, s.bodytext, s.comments, s.counter, s.acomm, s.score, s.ratings, c.id, c.title, c.description, c.img, u.user_name FROM ".$prefix."_page AS s LEFT JOIN ".$prefix."_categories AS c ON (s.catid=c.id) LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id) WHERE s.pid = '$id' AND s.time <= now() AND s.status!='0' ".$cwhere);
	if ($db->sql_numrows($result) == 1) {
		$db->sql_query("UPDATE ".$prefix."_page SET counter=counter+1 WHERE pid='$id'");
		list($pid, $catid, $uname, $title, $time, $hometext, $bodytext, $comments, $counter, $acomm, $score, $ratings, $cid, $ctitle, $cdescription, $cimg, $user_name) = $db->sql_fetchrow($result);
		$pagetitle = (intval($catid)) ? $conf['defis']." "._PAGES." ".$conf['defis']." $ctitle ".$conf['defis']." $title" : $conf['defis']." "._PAGES." ".$conf['defis']." $title";
		head();
		menu(_PAGES);
		if ($catid) templ("catlink", catlink($conf['name'], $catid, $confp['defis'], _PAGES));
            $bookmarks = ($confp['bookmarks']) ? "<hr width=\"23%\" align=\"left\"><script src=\"ajax/ok2.js\" type=\"text/javascript\"></script>" : ""; 
		$text = ($bodytext) ? $hometext."<br><br>".$bodytext.$bookmarks : $hometext.$bookmarks;
		$conpag = explode("[pagebreak]", $text);
		$pageno = count($conpag);
		$pag = ($pag == "" || $pag < 1) ? 1 : $pag;
		if ($pag > $pageno) $pag = $pageno;
		$arrayelement = (int)$pag;
		$arrayelement--;
		$post = ($user_name) ? _POSTEDBY.": ".user_info($user_name, 1) : (($uname) ? _POSTEDBY.": ".$uname : _POSTEDBY.": ".$confu['anonym']);
		$ndate = ($confp['date']) ? " "._DATE.": ".format_time($time) : "";
		$reads = ($confp['read']) ? " "._READS.": ".$counter : "";
		$arating = " ".ajax_rating(1, $pid, $conf['name'], $ratings, $score, "");
		$print = " ".ad_print("index.php?name=".$conf['name']."&op=printe&id=".$pid);
		$admin = (is_moder($conf['name'])) ? " ".add_menu($pid, "<a href=\"".$admin_file.".php?op=page_add&id=".$pid."\" title=\""._FULLEDIT."\">"._FULLEDIT."</a>||<a href=\"".$admin_file.".php?op=page_delete&id=".$pid."\" OnClick=\"return DelCheck(this, '"._DELETE." &quot;$title&quot;?');\" title=\""._ONDELETE."\">"._ONDELETE."</a>") : "";
		$cdescription = ($cdescription) ? $cdescription : $ctitle;
		$cimg = ($cimg) ? "<a href=\"index.php?name=".$conf['name']."&cat=$cid\"><img src=\"images/categories/".$cimg."\" border=\"0\" alt=\"$cdescription\" title=\"$cdescription\" align=\"right\" hspace=\"10\" vspace=\"10\"></a>" : "";
		$link = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"75%\" align=\"left\">".$post.$ndate.$reads."</td><td>".$arating."</td><td align=\"right\">".$print.$admin."</td></tr></table>";
		basic($cid, $cimg, $ctitle, $pid, search_color($title, $word), search_color(bb_decode($conpag[$arrayelement], $conf['name']), $word), $link, $read, $post, $ndate, $reads, $comm, $arating, $print, $admin);
		num_pages($conf['name'], 1, $pageno, 1, "op=view&id=".$id."&");
		if ($confp['link']) {
			$plimit = intval($confp['linknum']);
			$result2 = $db->sql_query("SELECT pid, title, time FROM ".$prefix."_page WHERE catid='$catid' AND pid!='$pid' AND time <= now() AND status!='0' ORDER BY time DESC LIMIT 0, ".$plimit);
			if ($db->sql_numrows($result2) > 0) {
				open();
				echo "<h2 style=\"margin: 0 0 5px 0;\">"._CATASSOC."</h2>"
				."<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
				while (list($l_pid, $l_title, $l_date) = $db->sql_fetchrow($result2)) {
					echo "<tr><td><a href=\"index.php?name=".$conf['name']."&op=view&id=$l_pid\" title=\"$l_title\"><img src=\"".img_find("all/pages")."\" border=\"0\"></a></td><td>".format_time($l_date)." - <a href=\"index.php?name=".$conf['name']."&op=view&id=$l_pid\" title=\"$l_title\">$l_title</a></td></tr>";
				}
				echo "</table>";
				close();
			}
		}
		if ($acomm) {
			echo "<a name=\"$pid\"></a>";
			show_com($pid);
		}
		foot();
	} else {
		header("Location: index.php?name=".$conf['name']);
	}
}

function add() {
	global $prefix, $db, $user, $conf, $confp, $confu, $pagetitle, $stop;
	$pagetitle = $conf['defis']." "._PAGES." ".$conf['defis']." "._ADD;
	if ((is_user() && $confp['add'] == 1) || (!is_user() && $confp['addquest'] == 1)) {
		head();
		menu(_ADD);
		if ($stop) warning($stop, "", "", 1);
		$subject = save_text($_POST['subject']);
		$catid = intval($_POST['catid']);
		$hometext = save_text($_POST['hometext']);
		$bodytext = save_text($_POST['bodytext']);
		$postname = text_filter(substr($_POST['postname'], 0, 25));
		if ($hometext) preview($subject, $hometext, $bodytext, "", $conf['name']);
		warning(_SUBMIT, "", "", 2);
		open();
		echo "<form name=\"post\" action=\"index.php?name=".$conf['name']."\" method=\"post\">";
		if (is_user()) {
			echo "<div class=\"left\">"._YOURNAME.":</div><div class=\"center\">".text_filter(substr($user[1], 0, 25))."</div>";
		} else {
			$postname = ($postname) ? $postname : $confu['anonym'];
			echo "<div class=\"left\">"._YOURNAME.":</div><div class=\"center\"><input type=\"text\" name=\"postname\" value=\"".$postname."\" size=\"65\" class=\"".$conf['style']."\"></div>";
		}
		echo "<div class=\"left\">"._TITLE.":</div><div class=\"center\"><input type=\"text\" name=\"subject\" value=\"".$subject."\" maxlength=\"80\" size=\"65\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._CATEGORY.":</div><div class=\"center\">".getcat($conf['name'], $catid, "catid", $conf['style'], "<option value=\"\">"._HOMECAT."</option>")."</div>"
		."<div class=\"left\">"._TEXT.":</div><div class=\"center\">".textarea("1", "hometext", $hometext, $conf['name'], "5")."</div>"
		."<div class=\"left\">"._ENDTEXT.":</div><div class=\"center\">".textarea("2", "bodytext", $bodytext, $conf['name'], "15")."</div>"
		.captcha_random()
		."<div class=\"button\"><select name=\"posttype\">"
		."<option value=\"preview\">"._PREVIEW."</option>"
		."<option value=\"save\">"._SEND."</option></select>"
		."<input type=\"hidden\" name=\"op\" value=\"send\">"
		." <input type=\"submit\" value=\""._OK."\" class=\"fbutton\"></div></form>";
		close();
		foot();
	} else {
		header("Location: index.php?name=".$conf['name']);
	}
}

function send() {
	global $prefix, $db, $user, $conf, $confp, $stop;
	if ((is_user() && $confp['add'] == 1) || (!is_user() && $confp['addquest'] == 1)) {
		$postname = text_filter(substr($_POST['postname'], 0, 25));
		$subject = save_text($_POST['subject']);
		$hometext = save_text($_POST['hometext']);
		$bodytext = save_text($_POST['bodytext']);
		$catid = intval($_POST['catid']);
		if (!$subject) $stop = _CERROR;
		if (!$hometext) $stop = _CERROR1;
		if (!$postname && !is_user()) $stop = _CERROR3;
		if (captcha_check()) $stop = _SECCODEINCOR;
		if (!$stop && $_POST['posttype'] == "save") {
			$postid = (is_user()) ? intval($user[0]) : "";
			$postname = (!is_user()) ? $postname : "";
			$ip = getip();
			$db->sql_query("INSERT INTO ".$prefix."_page (pid, catid, uid, name, title, time, hometext, bodytext, comments, counter, acomm, score, ratings, ip_sender, status) VALUES (NULL, '$catid', '$postid', '$postname', '$subject', now(), '$hometext', '$bodytext', '0', '0', '0', '0', '0', '$ip', '0')");
			update_points(35);
			head();
			menu(_ADD);
			warning(_SUBTEXT, "?name=".$conf['name'], 10, 2);
			foot();
		} else {
			add();
		}
	} else {
		header("Location: index.php?name=".$conf['name']);
	}
}

switch($op) {
	default:
	pages();
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
}
?>