<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("MODULE_FILE")) {
	Header("Location: ../../index.php");
	exit;
}
get_lang($conf['name']);

function search_result() {
	global $prefix, $db, $pagetitle, $admin_file, $conf, $confu;
	$word = ($_POST['word']) ? text_filter($_POST['word']) : text_filter(iconv('cp1251','utf-8',$_GET['word']));
	$mod = ($_POST['mod']) ? text_filter($_POST['mod']) : text_filter($_GET['mod']);
	$mod = ($mod) ? $mod : 0;
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$display = ($conf['display']) ? "" : "style=\"display:none;\"";
	$navi = "<img src=\"".img_find("misc/navi")."\" border=\"0\">";
	$search = explode(",", $conf['search']);
	$pagetitle = ($word) ? "".$conf['defis']." "._SEARCH." ".$conf['defis']." $word" : "".$conf['defis']." "._SEARCH."";
	$lang = array(_FILES, _NEWS);
	$m = 0;
	$sel = "";
	foreach ($search as $val) {
		if (is_active($val) && $val != "") {
			$sel = ($val == $mod && $mod != "") ? "selected" : "";
			$modcont .= "<option value=\"$val\" $sel>".$lang[$m]."</option>";
		}
		$m++;
	}
	$stop = ($word && strlen($word) < $conf['searchlet']) ? ""._SEARCHLETMIN.": ".$conf['searchlet']."" : "";
	head();
	title(""._SEARCH."");
	open();
	echo "<table align=\"center\"><form action=\"index.php?name=".$conf['name']."\" method=\"post\"><tr>"
	."<td align=\"center\"><img src=\"".img_find("all/search")."\" alt=\""._SEARCH."\" title=\""._SEARCH."\"></td>"
	."<td align=\"center\"><select name=\"mod\" class=\"".$conf['style']."\"><option value=\"\">"._SEARCHALL."</option>".$modcont."</select></td>"
	."<td align=\"center\"><input type=\"text\" name=\"word\" value=\"$word\" size=\"25\" maxlength=\"100\" class=\"".$conf['style']."\"></td>"
	."<td align=\"center\"><input type=\"submit\" title=\""._SEARCH."\" value=\""._SEARCH."\" class=\"fbutton\"></td>"
	."</tr></form></table>";
	close();
	if (!$stop && $word) {
		foreach ($search as $val) {
			if ((!$mod || $mod == $val) && is_active($val) && $val != "") {
				if ($val == "files") {
					$result = $db->sql_query("SELECT f.lid, f.name, f.title, f.description, f. bodytext, f.date, f.homepage, c.id, c.title, u.user_name FROM ".$prefix."_files AS f LEFT JOIN ".$prefix."_categories AS c ON (f.cid=c.id) LEFT JOIN ".$prefix."_users AS u ON (f.uid=u.user_id) WHERE date <= now() AND status!='0' AND (f.title LIKE '%".$word."%' OR f.description LIKE '%".$word."%') ORDER BY date DESC");
					while (list($id, $uname, $title, $hometext, $bodytext, $date, $link, $cid, $ctitle, $user_name) = $db->sql_fetchrow($result)) {
						$atitle = "<a href=\"index.php?name=$val&op=view&id=$id&word=".urlencode($word)."\" title=\"$title\">".search_color($title, $word)."</a>";
						$description = ($bodytext) ? "".$hometext."<br /><br />".$bodytext."" : $hometext;
						$aimg = "<span id=\"cont\"><img src=\"".img_find("all/plus")."\" border=\"0\" align=\"center\" alt=\""._READMORE."\" title=\""._READMORE."\" id=\"menu".$a."\" OnClick=\"SwitchMenu('sub".$a."')\" style=\"cursor:pointer;\"></span>";
						$ahref = "<a href=\"index.php?name=$val&op=view&id=$id&word=".urlencode($word)."\" target=\"_blank\" title=\""._WINDOWNEW."\"><img src=\"".img_find("all/content")."\" border=\"0\" align=\"center\" alt=\""._WINDOWNEW."\"></a>";
						$text = "<div id=\"sub".$a."\" $display>".search_color(bb_decode($description, $val), $word)."</div>";
						$tdate = ""._DATE.": ".format_time($date)."";
						$tmodul = ""._MODUL.": <a href=\"index.php?name=$val\" title=\"".$lang[2]."\">".$lang[2]."</a>";
						$ctitle = (!$ctitle) ? ""._CATEGORY.": "._NO."" : ""._CATEGORY.": <a href=\"index.php?name=$val&cat=$cid\" title=\"".$ctitle."\">".cutstr($ctitle, 15)."</a>";
						$author = ($user_name) ? ""._POSTEDBY.": ".user_info($user_name, 1)."" : (($uname) ? ""._POSTEDBY.": ".$uname."" : ""._POSTEDBY.": ".$confu['anonym']."");
						$link = ($link) ? ""._SITEURL.": <a href=\"index.php?name=$val&op=view&id=$id\" target=\"_blank\" title=\"".$title."\">".search_color(str_replace(array("http://", "www."), "", $link), $word)."</a>" : "";
						$edit = (is_moder($val)) ? "".ad_edit("".$admin_file.".php?op=files_add&id=".$id."")."" : "";
						$conts[] = array($id, $atitle, $aimg, $ahref, $edit, $text, $tdate, $tmodul, $ctitle, $author, $link);
						$a++;
					}
				} elseif ($val == "news") {
					$result = $db->sql_query("SELECT s.sid, s.name, s.title, s.time, s.hometext, s.bodytext, c.id, c.title, u.user_name FROM ".$prefix."_stories AS s LEFT JOIN ".$prefix."_categories AS c ON (s.catid=c.id) LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id) WHERE time <= now() AND status!='0' AND (s.title LIKE '%".$word."%' OR s.hometext LIKE '%".$word."%' OR s.bodytext LIKE '%".$word."%') ORDER BY time DESC");
					while (list($id, $uname, $title, $date, $hometext, $bodytext, $cid, $ctitle, $user_name) = $db->sql_fetchrow($result)) {
						$atitle = "<a href=\"index.php?name=$val&op=view&id=$id&word=".urlencode($word)."\" title=\"$title\">".search_color($title, $word)."</a>";
						$description = ($bodytext) ? "".$hometext."<br /><br />".$bodytext."" : $hometext;
						$aimg = "<span id=\"cont\"><img src=\"".img_find("all/plus")."\" border=\"0\" align=\"center\" alt=\""._READMORE."\" title=\""._READMORE."\" id=\"menu".$a."\" OnClick=\"SwitchMenu('sub".$a."')\" style=\"cursor:pointer;\"></span>";
						$ahref = "<a href=\"index.php?name=$val&op=view&id=$id&word=".urlencode($word)."\" target=\"_blank\" title=\""._WINDOWNEW."\"><img src=\"".img_find("all/content")."\" border=\"0\" align=\"center\" alt=\""._WINDOWNEW."\"></a>";
						$text = "<div id=\"sub".$a."\" $display>".search_color(bb_decode($description, $val), $word)."</div>";
						$tdate = ""._DATE.": ".format_time($date)."";
						$tmodul = ""._MODUL.": <a href=\"index.php?name=$val\" title=\"".$lang[6]."\">".$lang[6]."</a>";
						$ctitle = (!$ctitle) ? ""._CATEGORY.": "._NO."" : ""._CATEGORY.": <a href=\"index.php?name=$val&cat=$cid\" title=\"".$ctitle."\">".cutstr($ctitle, 15)."</a>";
						$author = ($user_name) ? ""._POSTEDBY.": ".user_info($user_name, 1)."" : (($uname) ? ""._POSTEDBY.": ".$uname."" : ""._POSTEDBY.": ".$confu['anonym']."");
						$edit = (is_moder($val)) ? "".ad_edit("".$admin_file.".php?op=news_add&id=".$id."")."" : "";
						$conts[] = array($id, $atitle, $aimg, $ahref, $edit, $text, $tdate, $tmodul, $ctitle, $author, "");
						$a++;
					}
				}
			}
		}
		$offset = ($num - 1) * $conf['searchnum'];
		$tnum = ($offset) ? $conf['searchnum'] + $offset : $conf['searchnum'];
		for ($i = $offset; $i < $tnum; $i++) {
			if ($conts[$i] != "") basic($conts[$i][0], $conts[$i][1], $conts[$i][2], $conts[$i][3], $conts[$i][4], $conts[$i][5], $conts[$i][6], $conts[$i][7], $conts[$i][8], $conts[$i][9], $conts[$i][10]);
		}
		if (!$a) warning(""._NOMATCHES."", "", "", 1);
		$numpages = ceil($a / $conf['searchnum']);
		num_page($conf['name'], $a, $numpages, $conf['searchnum'], "mod=$mod&word=".urlencode($word)."&");
	} else {
		if ($stop) {
			$winfo = $stop;
			$typ = "1";
		} else {
			$winfo = ""._SEARCHINFO."";
			$typ = "2";
		}
		warning($winfo, "", "", $typ);
	}
	if ($a >= 10) get_page($conf['name']);
	foot();
}

switch($op) {
	default:
	search_result();
	break;
}
?>