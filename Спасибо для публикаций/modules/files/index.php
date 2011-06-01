<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("MODULE_FILE")) {
	Header("Location: ../../index.php");
	exit;
}
get_lang($conf['name']);
include("config/config_files.php");

function menu($logo) {
	global $conf, $conff;
	$home = "<a href=\"index.php?name=".$conf['name']."\" title=\""._HOME."\">"._HOME."</a>";
	$best = ($conff['rate']) ? "<a href=\"index.php?name=".$conf['name']."&best=1\" title=\""._BEST."\">"._BEST."</a>" : "";
	$pop = ($conff['rate']) ? "<a href=\"index.php?name=".$conf['name']."&hits=1\" title=\""._POP."\">"._POP."</a>" : "";
	$liste = "<a href=\"index.php?name=".$conf['name']."&op=liste\" title=\""._LIST."\">"._LIST."</a>";
	$add = ((is_user() && $conff['addfiles'] == 1) || (!is_user() && $conff['addquest'] == 1)) ? "<a href=\"index.php?name=".$conf['name']."&op=add\" title=\""._ADD."\">"._ADD."</a>" : "";
	$navi = "[ <a href=\"index.php?name=".$conf['name']."\" title=\""._HOME."\">"._HOME."</a>";
	$navi .= ($conff['rate']) ? " | <a href=\"index.php?name=".$conf['name']."&best=1\" title=\""._BEST."\">"._BEST."</a> | <a href=\"index.php?name=".$conf['name']."&hits=1\" title=\""._POP."\">"._POP."</a>" : "";
	$navi .= " | <a href=\"index.php?name=".$conf['name']."&op=liste\" title=\""._LIST."\">"._LIST."</a>";
	$navi .= ((is_user() && $conff['addfiles'] == 1) || (!is_user() && $conff['addquest'] == 1)) ? " | <a href=\"index.php?name=".$conf['name']."&op=add\" title=\""._ADD."\">"._ADD."</a>" : "";
	$navi .= " ]";
	search($logo, $conf['name'], $navi, $home, $best, $pop, $liste, $add);
}

function files() {
	global $prefix, $db, $admin_file, $pagetitle, $conf, $confu, $conff, $home, $currentlang;
	$lang = ($conf['multilingual']) ? "AND (c.language='$currentlang' OR c.language='')" : "";
	$filenum = intval($conff['num']);
	$fbest = (isset($_GET['best'])) ? 1 : 0;
	$fhits = (isset($_GET['hits'])) ? 1 : 0;
	$fcat = (isset($_GET['cat'])) ? intval($_GET['cat']) : 0;
	if ($fbest && $conff['rate']) {
		$caton = 0;
		$field = "best=1&";
		$order = "WHERE date <= now() AND status!='0' ".$lang." ORDER BY totalvotes DESC";
		$ordernum = "date <= now() AND status!='0'";
		$files_logo = ""._BEST."";
		$pagetitle = "".$conf['defis']." "._FILES." ".$conf['defis']." $files_logo";
	} elseif ($fhits && $conff['rate']) {
		$caton = 0;
		$field = "hits=1&";
		$order = "WHERE date <= now() AND status!='0' ".$lang." ORDER BY hits DESC";
		$ordernum = "date <= now() AND status!='0'";
		$files_logo = ""._POP."";
		$pagetitle = "".$conf['defis']." "._FILES." ".$conf['defis']." $files_logo";
	} elseif ($fcat) {
		$caton = 1;
		$field = "cat=$fcat&";
		list($cat_title, $cat_description) = $db->sql_fetchrow($db->sql_query("SELECT title, description FROM ".$prefix."_categories WHERE id='$fcat'"));
		$order = "WHERE cid='$fcat' AND date <= now() AND status!='0' ".$lang." ORDER BY date DESC";
		$ordernum = "cid='$fcat' AND date <= now() AND status!='0'";
		$pagetitle = "".$conf['defis']." "._FILES." ".$conf['defis']." $cat_title";
	} else {
		$caton = 1;
		$field = "";
		$order = "WHERE date <= now() AND status!='0' ".$lang." ORDER BY date DESC";
		$ordernum = "date <= now() AND status!='0'";
		$files_logo = ""._FILES."";
		$pagetitle = "".$conf['defis']." $files_logo";
	}
	head();
	if (!$home) {
		if ($fcat) {
			menu($cat_title);
		} else {
			menu($files_logo);
		}
		if ($caton == 1) categories($conf['name'], $conff['tabcol'], $conff['subkat'], $conff['catdesc'], $fcat);
	}
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $filenum;
	$offset = intval($offset);
	$result = $db->sql_query("SELECT f.lid, f.cid, f.name, f.title, f.description, UNIX_TIMESTAMP(f.date) as formatted, f.votes, f.totalvotes, f.totalcomments, f.hits, c.id, c.title, c.description, c.img, u.user_name FROM ".$prefix."_files AS f LEFT JOIN ".$prefix."_categories AS c ON (f.cid=c.id) LEFT JOIN ".$prefix."_users AS u ON (f.uid=u.user_id) ".$order." LIMIT $offset, $filenum");
	if ($db->sql_numrows($result) > 0) {
		while (list($id, $fcid, $uname, $f_title, $description, $formatted, $votes, $totalvotes, $comment, $hits, $cid, $ctitle, $cdescription, $cimg, $user_name) = $db->sql_fetchrow($result)) {
			$fp_data = date(""._DATESTRING."", $formatted);
			$title = "<a href=\"index.php?name=".$conf['name']."&op=view&id=$id\" title=\"$f_title\">$f_title</a> ".new_graphic($formatted)."";
			$read = "<a href=\"index.php?name=".$conf['name']."&op=view&id=$id\" title=\"$f_title\">"._READMORE."</a>";
			$post = ($user_name) ? " "._POSTEDBY.": ".user_info($user_name, 1)."" : (($uname) ? " "._POSTEDBY.": ".$uname."" : " "._POSTEDBY.": ".$confu['anonym']."");
			$ndate = ($conff['date']) ? " "._DATE.": ".$fp_data."" : "";
			$reads = ($conff['read']) ? " "._FILEHITS.": ".$hits."" : "";
			if ($conff['comm']) {
				if ($comment == 0) {
					$comm = " <a href=\"index.php?name=".$conf['name']."&op=view&id=$id#$id\" title=\"$f_title\">"._COMMENTS."</a>";
				} elseif ($comment == 1) {
					$comm = " <a href=\"index.php?name=".$conf['name']."&op=view&id=$id#$id\" title=\"$f_title\">"._COMMENT.": $comment</a>";
				} elseif ($comment > 1) {
					$comm = " <a href=\"index.php?name=".$conf['name']."&op=view&id=$id#$id\" title=\"$f_title\">"._COMMENTS.": $comment</a>";
				}
			}
			$arating = " ".ajax_rating(0, $id, $conf['name'], $votes, $totalvotes)."";
			$print = " ".ad_print("index.php?name=".$conf['name']."&op=printe&id=".$id."")."";
			$admin = (is_moder($conf['name'])) ? " ".ad_edit("".$admin_file.".php?op=files_add&id=".$id."")." ".ad_delete("".$admin_file.".php?op=files_delete&id=".$id."", $f_title)."" : "";
			$cdescription = ($cdescription) ? $cdescription : $ctitle;
			$cimg = ($cimg) ? "<a href=\"index.php?name=".$conf['name']."&cat=$cid\"><img src=\"images/categories/".$cimg."\" border=\"0\" alt=\"$cdescription\" title=\"$cdescription\" align=\"right\" hspace=\"10\" vspace=\"10\"></a>" : "";
			$link = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"75%\" align=\"left\"><b>".$read."</b>".$post."".$ndate."".$reads."".$comm."</td><td>".$arating."</td><td align=\"right\">".$print."".$admin."</td></tr></table>";
			basic($cid, $cimg, $ctitle, $id, $title, bb_decode($description, $conf['name']), $link, $read, $post, $ndate, $reads, $comm, $arating, $print, $admin, $size, $vers, $down, $broc, $email, $home);
		}
		num_article($conf['name'], $filenum, $field, "lid", "_files", "cid", $ordernum);
	}
	foot();
}

function liste() {
	global $db, $prefix, $pagetitle, $conf, $confu, $conff, $currentlang;
	$lang = ($conf['multilingual']) ? "AND (c.language='$currentlang' OR c.language='')" : "";
	$listnum = intval($conff['listnum']);
	$let = (isset($_GET['let'])) ? mb_substr($_GET['let'], 0, 1, "utf-8") : "";
	if ($let) {
		$field = "op=liste&let=".urlencode($let)."&";
		$pagetitle = "".$conf['defis']." "._FILES." ".$conf['defis']." "._LIST." ".$conf['defis']." $let";
		$order = "WHERE UPPER(f.title) LIKE '".$let."%' AND date <= now() AND status!='0'";
	} else {
		$field = "op=liste&";
		$pagetitle = "".$conf['defis']." "._FILES." ".$conf['defis']." "._LIST."";
		$order = "WHERE date <= now() AND status!='0'";
	}
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $listnum;
	$offset = intval($offset);
	$result = $db->sql_query("SELECT f.lid, f.cid, f.name, f.title, f.date, c.id, c.title, u.user_name FROM ".$prefix."_files AS f LEFT JOIN ".$prefix."_categories AS c ON (f.cid=c.id) LEFT JOIN ".$prefix."_users AS u ON (f.uid=u.user_id) ".$order." ".$lang." ORDER BY date DESC LIMIT $offset, $listnum");
	head();
	menu(""._LIST."");
	if ($db->sql_numrows($result) > 0) {
		open();
		if ($conff['letter']) letter($conf['name']);
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr>"
		."<th>"._ID."</th><th>"._TITLE."</th><th>"._CATEGORY."</th><th>"._DATE."</th><th>"._POSTEDBY."</th></tr>";
		while (list($id, $catid, $uname, $stitle, $time, $cid, $ctitle, $user_name) = $db->sql_fetchrow($result)) {
			$ctitle = (!$ctitle) ? ""._NO."" : "<a href=\"index.php?name=".$conf['name']."&cat=$cid\" title=\"".$ctitle."\">".cutstr($ctitle, 10)."</a>";
			$post = ($user_name) ? user_info($user_name, 1) : (($uname) ? $uname : $confu['anonym']);
			echo "<tr class=\"bgcolor1\">"
			."<td align=\"center\">".$id."</td>"
			."<td><a href=\"index.php?name=".$conf['name']."&op=view&id=$id\" title=\"".$stitle."\">".cutstr($stitle, 35)."</a></td>"
			."<td align=\"center\">".$ctitle."</td>"
			."<td align=\"center\">".format_time($time)."</td>"
			."<td align=\"center\">".$post."</td></tr>";
		}
		echo "</table>";
		close();
		$ordernum = ($let) ? "UPPER(title) LIKE '".$let."%' AND date <= now() AND status!='0'" : "date <= now() AND status!='0'";
		num_article($conf['name'], $listnum, $field, "lid", "_files", "cid", $ordernum);
	} else {
		warning(""._NO_INFO."", "", "", 2);
	}
	foot();
}

function printe() {
	global $prefix, $db, $ThemeSel, $pagetitle, $conf, $confu;
	$id = intval($_GET['id']);
	$result = $db->sql_query("SELECT f.cid, f.name, f.title, f.url, f.description, f.bodytext, f.date, f.filesize, f.version, f.email, f.homepage, f.votes, f.totalvotes, f.totalcomments, f.hits, c.id, c.title, c.description, c.img, u.user_name FROM ".$prefix."_files AS f LEFT JOIN ".$prefix."_categories AS c ON (f.cid=c.id) LEFT JOIN ".$prefix."_users AS u ON (f.uid=u.user_id) WHERE lid='$id' AND date <= now() AND status!='0'");
	if ($db->sql_numrows($result) == 1) {
		list($cid, $uname, $title, $url, $description, $bodytext, $date, $f_size, $f_version, $a_email, $a_homepage, $votes, $totalvotes, $totalcomments, $hits, $ccid, $ctitle, $cdescription, $cimg, $user_name) = $db->sql_fetchrow($result);
		if (file_exists("templates/$ThemeSel/index.php")) {
			include("templates/$ThemeSel/index.php");
		} else {
			include("function/template.php");
		}
		$conf['defis'] = urldecode($conf['defis']);
		$ftitle = (intval($ccid)) ? "$title ".$conf['defis']." $ctitle ".$conf['defis']." "._FILES." ".$conf['defis']." ".$conf['sitename']."" : "$title ".$conf['defis']." "._FILES." ".$conf['defis']." ".$conf['sitename']."";
		$ctitle = (!$ctitle) ? ""._NO."" : "<a href=\"index.php?name=".$conf['name']."&cat=$ccid\" title=\"".$ctitle."\">".cutstr($ctitle, 35)."</a>";
		$ptitle = "".format_time($date)." - ".$title."";
		$dtext = ($bodytext) ? "".bb_decode($description, $conf['name'])."<br /><br />".bb_decode($bodytext, $conf['name'])."" : bb_decode($description, $conf['name']);
		$post = ($user_name) ? user_info($user_name, 1) : (($uname) ? $uname : $confu['anonym']);
		$text .= "<table border=\"0\" width=\"100%\" cellpadding=\"3\" cellspacing=\"0\"><tr><td width=\"50%\">"._POSTEDBY.": ".$post."</td><td width=\"50%\">"._CATEGORY.": ".$ctitle."</td></tr>"
		."<tr><td>"._FILESIZE.": ".files_size($f_size)."</td><td>"._DATE.": ".format_time($date)."</td></tr>"
		."<tr><td>"._FILEVERS.": ".$f_version."</td><td>"._FILEHITS.": ".$hits."</td></tr></table>"
		."<hr><table border=\"0\" width=\"100%\" cellpadding=\"3\" cellspacing=\"0\"><tr><td>".$dtext."</td></tr></table>";
		$url = ""._COMESFROM.": <a href=\"".$conf['homeurl']."\" title=\"".$conf['sitename']."\">".$conf['homeurl']."</a><br />"._THEURL.": <a href=\"".$conf['homeurl']."/index.php?name=".$conf['name']."&op=view&id=$id\" title=\"".$title."\">".$conf['homeurl']."/index.php?name=".$conf['name']."&op=view&id=$id</a>";
		prints($ftitle, $ptitle, $text, $url);
	} else {
		Header("Location: index.php?name=".$conf['name']."");
	}
}

function view() {
	global $prefix, $db, $hometext, $pagetitle, $admin_file, $conf, $confu, $conff;
	$id = intval($_GET['id']);
	$word = ($_GET['word']) ? text_filter($_GET['word']) : "";
	$result = $db->sql_query("SELECT f.uid, f.cid, f.name, f.title, f.url, f.description, f.bodytext, f.date, f.filesize, f.version, f.email, f.homepage, f.votes, f.totalvotes, f.totalcomments, f.hits, c.id, c.title, c.description, c.img, u.user_name FROM ".$prefix."_files AS f LEFT JOIN ".$prefix."_categories AS c ON (f.cid=c.id) LEFT JOIN ".$prefix."_users AS u ON (f.uid=u.user_id) WHERE lid='$id' AND date <= now() AND status!='0'");
	if ($db->sql_numrows($result) == 1) {
		list($uid, $cid, $uname, $title, $url, $description, $bodytext, $date, $f_size, $f_version, $a_email, $a_homepage, $votes, $totalvotes, $totalcomments, $hits, $ccid, $ctitle, $cdescription, $cimg, $user_name) = $db->sql_fetchrow($result);
		$pagetitle = (intval($cid)) ? "".$conf['defis']." "._FILES." ".$conf['defis']." $ctitle ".$conf['defis']." $title" : "".$conf['defis']." "._FILES." ".$conf['defis']." $title";
		$hometext = $description;
		$ctitle = (!$ctitle) ? ""._NO."" : "<a href=\"index.php?name=".$conf['name']."&cat=$ccid\" title=\"".$ctitle."\">".cutstr($ctitle, 15)."</a>";
		head();
		menu(""._FILES."");
		$dtext = ($bodytext) ? "".$description."<br /><br />".$bodytext."" : $description;
		$post = ($user_name) ? ""._POSTEDBY.": ".user_info($user_name, 1)."" : (($uname) ? ""._POSTEDBY.": ".$uname."" : ""._POSTEDBY.": ".$confu['anonym']."");
		$ndate = ($conff['date']) ? " "._DATE.": ".format_time($date)."" : "";
		$reads = ($conff['read']) ? " "._FILEHITS.": ".$hits."" : "";
		$size = " "._FILESIZE.": ".files_size($f_size)."";
		$vers = " "._FILEVERS.": ".$f_version."";
		if (is_user() || $conff['down'] == "1") {
			$onclick = (!$conff['stream']) ? "OnClick=\"window.open('$url')\"" : "";
			$down = "<form action=\"index.php?name=".$conf['name']."\" method=\"post\" style=\"display: inline\">"
			."<input type=\"hidden\" name=\"id\" value=\"$id\">"
			."<input type=\"hidden\" name=\"op\" value=\"geturl\">"
			."<input type=\"submit\" $onclick value=\""._DOWNLFILE."\" class=\"fbutton\">"
			."</form>";
		}
		$arating = " ".ajax_rating(1, $id, $conf['name'], $votes, $totalvotes)."";
		$print = " ".ad_print("index.php?name=".$conf['name']."&op=printe&id=".$id."")."";
		$broc = ($conff['broc'] == 1) ? " <a href=\"index.php?name=".$conf['name']."&op=broken&id=$id\" title=\""._BROCFILE."\"><img src=\"".img_find("all/warning")."\" border=\"0\" align=\"center\"></a>" : "";
		$email = ($a_email) ? " "._AUEMAIL.": ".anti_spam($a_email)."" : "";
		$home = ($a_homepage) ? " "._FAUURL.": ".domain($a_homepage)."" : "";
		$admin = (is_moder($conf['name'])) ? " ".ad_edit("".$admin_file.".php?op=files_add&id=".$id."")." ".ad_delete("".$admin_file.".php?op=files_delete&id=".$id."", $title)."" : "";
		$cdescription = ($cdescription) ? $cdescription : $ctitle;
		$cimg = ($cimg) ? "<a href=\"index.php?name=".$conf['name']."&cat=$cid\"><img src=\"images/categories/".$cimg."\" border=\"0\" alt=\"$cdescription\" title=\"$cdescription\" align=\"right\" hspace=\"10\" vspace=\"10\"></a>" : "";
		$ctitle = " "._CATEGORY.": ".$ctitle."";
		$link = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"33%\">".$arating."</td><td width=\"33%\" align=\"center\">".$down."</td><td width=\"33%\" align=\"right\">".$print."".$broc."";
		if (is_moder($conf['name']) && $a_email) $link .= " <a href=\"mailto:".$a_email."?subject=".$conf['sitename']."\" title=\""._AUEMAIL."\"><img src=\"".img_find("all/contact")."\" border=\"0\" align=\"center\"></a>";
		if ($a_homepage) $link .= " <a href=\"".$a_homepage."\" target=\"_blank\" title=\""._FAUURL."\"><img src=\"".img_find("all/home")."\" border=\"0\" align=\"center\"></a>";
		$link .= "".$admin."</td></tr></table>";
		
		$dtext .= '<br />'.show_thanks($conf['name'],$id,$uid);
		basic($cid, $cimg, $ctitle, $id, search_color($title, $word), search_color(bb_decode($dtext, $conf['name']), $word), $link, $read, $post, $ndate, $reads, $comm, $arating, $print, $admin, $size, $vers, $down, $broc, $email, $home);
		if ($conff['comm']) {
			echo "<a name=\"$id\"></a>";
			show_com($id);
		}
		foot();
	} else {
		Header("Location: index.php?name=".$conf['name']."");
	}
}

function broken() {
	global $prefix, $db, $pagetitle, $conf, $conff;
	$pagetitle = "".$conf['defis']." "._FILES." ".$conf['defis']." "._BROCFILE."";
	$id = intval($_GET['id']);
	if ($conff['broc'] == 1 && $id) {
		head();
		menu(""._BROCFILE."");
		$db->sql_query("UPDATE ".$prefix."_files SET status='2' WHERE lid='$id'");
		warning(""._BROCNOTE."", "?name=".$conf['name']."&op=view&id=$id", 5, 2);
		foot();
	} else {
		Header("Location: index.php?name=".$conf['name']."");
	}
}

function add() {
	global $db, $prefix, $user, $pagetitle, $conf, $conff, $confu, $stop;
	$pagetitle = "".$conf['defis']." "._FILES." ".$conf['defis']." "._ADD."";
	if ((is_user() && $conff['addfiles'] == 1) || (!is_user() && $conff['addquest'] == 1)) {
		if (is_user()) {
			$userinfo = getusrinfo();
			$authormail = (isset($_POST['authormail'])) ? text_filter($_POST['authormail']) : $userinfo['user_email'];
			$authorurl = (isset($_POST['authorurl'])) ? url_filter($_POST['authorurl']) : $userinfo['user_website'];
		} else {
			$authormail = (isset($_POST['authormail'])) ? text_filter($_POST['authormail']) : "";
			$authorurl = (isset($_POST['authorurl'])) ? url_filter($_POST['authorurl']) : "http://";
		}
		$filelink = (isset($_POST['filelink'])) ? url_filter($_POST['filelink']) : "http://";
		head();
		menu(""._ADD."");
		$info = ""._ADDFNOTE."";
		if ($conff['upload'] == 1) $info .= "".sprintf(""._ADDFNOTE2."", str_replace(",", ", ", $conff['typefile']), files_size($conff['max_size']))."";
		$info .= " "._ADDFNOTE3."";
		if ($stop) warning($stop, "", "", 1);
		warning($info, "", "", 2);
		$title = save_text($_POST['title']);
		$cid = intval($_POST['cid']);
		$description = save_text($_POST['description']);
		$bodytext = save_text($_POST['bodytext']);
		$postname = text_filter(substr($_POST['postname'], 0, 25));
		$f_version = text_filter($_POST['f_version']);
		$file_size = intval($_POST['file_size']);
		if ($description) preview($title, $description, $bodytext, "", $conf['name']);
		open();
		echo "<form name=\"post\" enctype=\"multipart/form-data\" action=\"index.php?name=".$conf['name']."\" method=\"post\">";
		if (is_user()) {
			echo "<div class=\"left\">"._YOURNAME.":</div><div class=\"center\">".text_filter(substr($user[1], 0, 25))."</div>";
		} else {
			$postname = ($postname) ? $postname : $confu['anonym'];
			echo "<div class=\"left\">"._YOURNAME.":</div><div class=\"center\"><input type=\"text\" name=\"postname\" value=\"".$postname."\" size=\"65\" class=\"".$conf['style']."\"></div>";
		}
		echo "<div class=\"left\">"._AUEMAIL.":</div><div class=\"center\"><input type=\"text\" name=\"authormail\" value=\"".$authormail."\" maxlength=\"100\" size=\"65\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._FTITLE.":</div><div class=\"center\"><input type=\"text\" name=\"title\" value=\"".$title."\" maxlength=\"100\" size=\"65\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._CATEGORY.":</div><div class=\"center\"><select name=\"cid\" class=\"".$conf['style']."\">".getcat($conf['name'], $cid)."</select></div>"
		."<div class=\"left\">"._TEXT.":</div><div class=\"center\">".textarea("1", "description", $description, $conf['name'], "5")."</div>"
		."<div class=\"left\">"._ENDTEXT.":</div><div class=\"center\">".textarea("2", "bodytext", $bodytext, $conf['name'], "15")."</div>"
		."<div class=\"left\">"._FAUURL.":</div><div class=\"center\"><input type=\"text\" name=\"authorurl\" value=\"".$authorurl."\" maxlength=\"100\" size=\"65\" class=\"".$conf['style']."\"></div>";
		if ($conff['upload'] == 1) echo "<div class=\"left\">"._FILE_USER.":</div><div class=\"center\"><input name=\"userfile\" type=\"file\" size=\"65\" class=\"".$conf['style']."\"></div>";
		echo "<div class=\"left\">"._FILELINK.":</div><div class=\"center\"><input type=\"text\" name=\"filelink\" value=\"".$filelink."\" maxlength=\"100\" value=\"http://\" size=\"65\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._FILEVERSION.":</div><div class=\"center\"><input type=\"text\" name=\"f_version\" value=\"".$f_version."\" maxlength=\"10\" size=\"65\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._FILESIZE.":</div><div class=\"center\"><input type=\"text\" name=\"file_size\" value=\"".$file_size."\" maxlength=\"10\" size=\"65\" class=\"".$conf['style']."\"></div>"
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
	global $prefix, $db, $user, $conf, $conff, $stop;
	if ((is_user() && $conff['addfiles'] == 1) || (!is_user() && $conff['addquest'] == 1)) {
		$postname = text_filter(substr($_POST['postname'], 0, 25));
		$title = save_text($_POST['title']);
		$description = save_text($_POST['description']);
		$bodytext = save_text($_POST['bodytext']);
		$url = url_filter($_POST['filelink']);
		$authorurl = url_filter($_POST['authorurl']);
		$authormail = text_filter($_POST['authormail']);
		$f_version = text_filter($_POST['f_version']);
		$cid = intval($_POST['cid']);
		if (!$title) $stop = _CERROR;
		if (!$description) $stop = _CERROR1;
		if (!$postname && !is_user()) $stop = _CERROR3;
		checkemail($authormail);
		if (captcha_check()) $stop = _SECCODEINCOR;
		if ($db->sql_numrows($db->sql_query("SELECT title FROM ".$prefix."_files WHERE title='$title'")) > 0) $stop = _MEDIAEXIST;
		$filename = upload(1, $conff['temp'], $conff['typefile'], $conff['max_size'], "files", "", "");
		$url = ($filename) ? "".$conff['temp']."/".$filename."" : $url;
		$filesize = ($filename) ? filesize($url) : $filesize;
		if ($stop) {
			$stop = $stop;
		} elseif (!$url  && $_POST['posttype'] == "save") {
			$stop = _UPLOADEROR2;
		}
		if (!$stop && $_POST['posttype'] == "save") {
			$postid = (is_user()) ? intval($user[0]) : "";
			$postname = (!is_user()) ? $postname : "";
			$ip = getip();
			$db->sql_query("INSERT INTO ".$prefix."_files (lid, cid, uid, name, title, description, bodytext, url, date, filesize, version, email, homepage, ip_sender, status) VALUES (NULL, '$cid', '$postid', '$postname', '$title', '$description', '$bodytext', '$url', now(), '$filesize', '$f_version', '$authormail', '$authorurl', '$ip', '0')");
			update_points(9);
			head();
			menu(_ADD);
			warning(_UPLOADFINISH, "?name=".$conf['name'], 10, 2);
			foot();
		} else {
			add();
		}
	} else {
		Header("Location: index.php?name=".$conf['name']);
	}
}

function geturl() {
	global $prefix, $db, $pagetitle, $conf, $conff;
	$id = intval($_POST['id']);
	if (($id && is_user()) || ($id && $conff['down'] == "1")) {
		$db->sql_query("UPDATE ".$prefix."_files SET hits=hits+1 WHERE lid=$id");
		list($f_title, $url) = $db->sql_fetchrow($db->sql_query("SELECT title, url FROM ".$prefix."_files WHERE lid='$id'"));
		update_points(11);
		if ($conff['stream'] == 2) {
			$type = strtolower(end(explode(".", $url)));
			stream($url, "".gen_pass(10).".".$type."");
		} elseif ($conff['stream'] == 1) {
			stream($url, preg_replace("/(.*?)\//i", "", $url));
		} else {
			$pagetitle = $conf['defis']." "._FILES." ".$conf['defis']." ".$f_title;
			head();
			menu(_FILES);
			open();
			echo "<center>"._NOTEDOWNLOAD." <b>$f_title</b> "._NOTEDOWNLOAD2."<br /><br />"
			."<b><a href=\"$url\" target=\"_blank\">$url</a></b><br /><br /></center>";
			close();
			get_page($conf['name']);
			foot();
		}
	} else {
		Header("Location: index.php?name=".$conf['name']);
	}
}

switch($op) {
	default:
	files();
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
	
	case "geturl":
	geturl();
	break;
	
	case "broken":
	broken();
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