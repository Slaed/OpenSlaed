<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

function cat_navi() {
	global $admin_file;
	panel();
	open();
	echo "<center><h1>"._CATEGORIES."</h1>"
	."[ <a href=\"".$admin_file.".php?op=cat_show\">"._HOME."</a> | <a href=\"".$admin_file.".php?op=cat_add\">"._ADD."</a> ]</center>";
	close();
}

function cat_show() {
	global $prefix, $db, $admin_file, $conf;
	head();
	cat_navi();
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $conf['anum'];
	$result = $db->sql_query("SELECT id, modul, title, description, img, language, parentid FROM ".$prefix."_categories ORDER BY modul ASC LIMIT ".$offset.", ".$conf['anum']."");
	if ($db->sql_numrows($result) > 0) {
		open();
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr><th>"._ID."</th><th>"._TITLE."</th><th>"._MODUL."</th>";
		if ($conf['multilingual'] == 1) echo "<th>"._LANGUAGE."</th>";
		echo "<th>"._SUBCATEGORY."</th><th>"._IMG."</th><th>"._FUNCTIONS."</th></tr>";
		while (list($id, $modul, $title, $description, $imgcat, $language, $parentid) = $db->sql_fetchrow($result)) {
			$active = ($parentid) ? "<font color=\"#009900\">"._YES."</font>" : "<font color=\"#FF0000\">"._NO."</font>";
			$img = ($imgcat) ? "<font color=\"#009900\">"._YES."</font>" : "<font color=\"#FF0000\">"._NO."</font>";
			if ($parentid) $title = getparent($parentid, $title);
			echo "<tr class=\"bgcolor1\"><td align=\"center\">".$id."</td>"
			."<td>".$title."</td>"
			."<td align=\"center\">".$modul."</td>";
			if ($conf['multilingual'] == 1) {
				$language = (!$language) ? ""._ALL."" : $language;
				echo "<td align=\"center\">".ucfirst($language)."</td>";
			}
			echo "<td align=\"center\">".$active."</td>"
			."<td align=\"center\">".$img."</td>"
			."<td align=\"center\">".ad_edit("".$admin_file.".php?op=cat_edit&cid=".$id."")." ".ad_delete("".$admin_file.".php?op=cat_del&id=".$id."&refer=1", $title)."</td></tr>";
		}
		echo "</table>";
		close();
		list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_categories"));
		$numpages = ceil($numstories / $conf['anum']);
		num_page("", $numstories, $numpages, $conf['anum'], "op=cat_show&");
	} else {
		warning(""._NO_INFO."", "", "", 2);
	}
	foot();
}

function cat_add() {
	global $prefix, $db, $conf, $admin_file;
	head();
	cat_navi();
	open();
	echo "<h2>"._ADDCATEGORY."</h2>"
	."<form name=\"post\" action=\"".$admin_file.".php\" method=\"post\">"
	."<script language=\"JavaScript\" type=\"text/javascript\" src=\"ajax/show_image.js\"></script>"
	."<div class=\"left\">"._TITLE.":</div><div class=\"center\"><input type=\"text\" name=\"title\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._DESCRIPTION.":</div><div class=\"center\"><textarea name=\"description\" cols=\"65\" rows=\"5\" class=\"admin\"></textarea></div>";
	if ($conf['multilingual'] == 1) echo "<div class=\"left\">"._LANGUAGE.":</div><div class=\"center\"><select name=\"language\" class=\"admin\">".language()."</select></div>";
	echo "<div class=\"left\">"._MODUL.":</div><div class=\"center\">".cat_modul("modul", "admin", "")."</div>"
	."<div class=\"left\">"._IMG.":</div><div class=\"center\"><select name=\"imgcat\" onChange=\"ShowImage('post', 'imgcat', '1')\" class=\"admin\">"
	."<option value=\"images/categories/no.png\">"._NO."</option>";
	$dir = opendir("images/categories");
	while ($entry = readdir($dir)) {
		if (preg_match("/(\.gif|\.png|\.jpg|\.jpeg)$/is", $entry) && $entry != "." && $entry != ".." && $entry != "no.png") echo "<option value=\"images/categories/".$entry."\">".$entry."</option>";
	}
	closedir($dir);
	echo "</select></div>"
	."<div class=\"left\">"._PREVIEW.":</div><div class=\"center\"><img src=\"images/categories/no.png\" name=\"pictures\" alt=\""._IMG."\"></div>"
	."<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"cat_add_save\">"
	."<input type=\"submit\" value=\""._ADD."\" class=\"fbutton\"></div></form>";
	close();
	$result = $db->sql_query("SELECT id, title, parentid FROM ".$prefix."_categories ORDER BY parentid, title");
	if ($db->sql_numrows($result) > 0) {
		open();
		echo "<h2>"._ADDSUBCATEGORY."</h2>"
		."<form name=\"post2\" action=\"".$admin_file.".php\" method=\"post\">"
		."<div class=\"left\">"._TITLE.":</div><div class=\"center\"><input type=\"text\" name=\"title\" maxlength=\"100\" size=\"65\" class=\"admin\"></div>"
		."<div class=\"left\">"._DESCRIPTION.":</div><div class=\"center\"><textarea name=\"description\" cols=\"65\" rows=\"5\" class=\"admin\"></textarea></div>";
		if ($conf['multilingual'] == 1) echo "<div class=\"left\">"._LANGUAGE.":</div><div class=\"center\"><select name=\"language\" class=\"admin\">".language()."</select></div>";
		echo "<div class=\"left\">"._MODUL.":</div><div class=\"center\">".cat_modul("modul", "admin", "")."</div>"
		."<div class=\"left\">"._CATEGORY.":</div><div class=\"center\"><select name=\"cid\" class=\"admin\">";
		while (list($cid, $title, $parentid) = $db->sql_fetchrow($result)) {
			if ($parentid) $title = getparent($parentid, $title);
			echo "<option value=\"$cid\">$title</option>";
		}
		echo "</select></div>"
		."<div class=\"left\">"._IMG.":</div><div class=\"center\"><select name=\"imgsubcat\" onChange=\"ShowImage('post2', 'imgsubcat', '2')\" class=\"admin\">"
		."<option value=\"images/categories/no.png\">"._NO."</option>";
		$dir = opendir("images/categories");
		while ($entry = readdir($dir)) {
			if (preg_match("/(\.gif|\.png|\.jpg|\.jpeg)$/is", $entry) && $entry != "." && $entry != ".." && $entry != "no.png") echo "<option value=\"images/categories/".$entry."\">".$entry."</option>";
		}
		closedir($dir);
		echo "</select></div>"
		."<div class=\"left\">"._PREVIEW.":</div><div class=\"center\"><img src=\"images/categories/no.png\" name=\"pictures2\" alt=\""._IMG."\"></div>"
		."<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"cat_sub_add_save\"><input type=\"submit\" value=\""._ADD."\" class=\"fbutton\"></div></form>";
		close();
	}
	$result = $db->sql_query("SELECT id, title, parentid FROM ".$prefix."_categories ORDER BY parentid, title");
	if ($db->sql_numrows($result) > 0) {
		open();
		echo "<h2>"._EDITCATEGORY."</h2>"
		."<form action=\"".$admin_file.".php\" method=\"post\">"
		."<div class=\"left\">"._CATEGORY.":</div><div class=\"center\"><select name=\"cid\" class=\"admin\">";
		while (list($cid, $title, $parentid) = $db->sql_fetchrow($result)) {
			if ($parentid) $title = getparent($parentid, $title);
			echo "<option value=\"$cid\">$title</option>";
		}
		echo "</select></div><div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"cat_edit\"><input type=\"submit\" value=\""._EDIT."\" class=\"fbutton\"></div></form>";
		close();
	}
	foot();
}

function cat_edit() {
	global $prefix, $db, $conf, $admin_file;
	$cid = $_REQUEST['cid'];
	$result = $db->sql_query("SELECT modul, title, description, img, language, parentid FROM ".$prefix."_categories WHERE id='$cid'");
	list($modul, $title, $description, $imgcat, $language, $parentid) = $db->sql_fetchrow($result);
	head();
	cat_navi();
	open();
	echo "<h2>"._EDITCATEGORY."</h2>"
	."<form name=\"post\" action=\"".$admin_file.".php\" method=\"post\">"
	."<script language=\"JavaScript\" type=\"text/javascript\" src=\"ajax/show_image.js\"></script>"
	."<div class=\"left\">"._TITLE.":</div><div class=\"center\"><input type=\"text\" name=\"title\" value=\"$title\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._DESCRIPTION.":</div><div class=\"center\"><textarea name=\"description\" cols=\"65\" rows=\"5\" class=\"admin\">$description</textarea></div>"
	."<div class=\"left\">"._MODUL.":</div><div class=\"center\">".cat_modul("modul", "admin", $modul)."</div>";
	if ($conf['multilingual'] == 1) echo "<div class=\"left\">"._LANGUAGE.":</div><div class=\"center\"><select name=\"language\" class=\"admin\">".language($language)."</select></div>";
	if ($parentid != 0) {
		$result2 = $db->sql_query("SELECT id, title, parentid FROM ".$prefix."_categories WHERE parentid != '$parentid'");
		echo "<div class=\"left\">"._CATEGORY.":</div><div class=\"center\"><select name=\"parentid\" class=\"admin\">";
		while (list($cid2, $ctitle2, $parentid2) = $db->sql_fetchrow($result2)) {
			if ($parentid2 != 0) $ctitle2 = getparent($parentid2, $ctitle2);
			if ($cid2 == $parentid) echo "<option value=\"$cid2\" selected>$ctitle2</option>";
			echo "<option value=\"$cid2\">$ctitle2</option>";
		}
		echo "</select></div>";
	} else {
		echo "<input type=\"hidden\" name=\"parentid\" value=\"0\">";
	}
	echo "<div class=\"left\">"._IMG.":</div><div class=\"center\"><select name=\"imgcat\" onChange=\"ShowImage('post', 'imgcat', '1')\" class=\"admin\">"
	."<option value=\"images/categories/no.png\">"._NO."</option>";
	$dir = opendir("images/categories");
	while ($entry = readdir($dir)) {
		if (preg_match("/(\.gif|\.png|\.jpg|\.jpeg)$/is", $entry) && $entry != "." && $entry != ".." && $entry != "no.png") {
			$sel = ($imgcat == $entry) ? "selected" : "";
			echo "<option value=\"images/categories/".$entry."\" $sel>".$entry."</option>";
		}
	}
	closedir($dir);
	$imgcat = (!$imgcat) ? "no.png" : $imgcat;
	echo "</select></div>"
	."<div class=\"left\">"._PREVIEW.":</div><div class=\"center\"><img src=\"images/categories/".$imgcat."\" name=\"pictures\" alt=\""._IMG."\"></div>"
	."<div class=\"button\"><input type=\"hidden\" name=\"id\" value=\"$cid\"><input type=\"hidden\" name=\"op\" value=\"cat_save\"><input type=\"submit\" value=\""._SAVECHANGES."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

switch($op) {
	case "cat_show":
	cat_show();
	break;
	
	case "cat_add":
	cat_add();
	break;
	
	case "cat_add_save":
	$modul = $_POST['modul'];
	$title = $_POST['title'];
	$description = $_POST['description'];
	$imgcat = $_POST['imgcat'];
	$language = $_POST['language'];
	$imgcat = str_replace("images/categories/", "", $imgcat);
	$imgcat = (!$imgcat || $imgcat == "no.png") ? "" : $imgcat;
	$db->sql_query("INSERT INTO ".$prefix."_categories VALUES (NULL, '$modul', '$title', '$description', '$imgcat', '$language', '0')");
	Header("Location: ".$admin_file.".php?op=cat_show");
	break;
	
	case "cat_sub_add_save":
	$modul = $_POST['modul'];
	$title = $_POST['title'];
	$description = $_POST['description'];
	$imgsubcat = $_POST['imgsubcat'];
	$language = $_POST['language'];
	$cid = $_POST['cid'];
	$imgsubcat = str_replace("images/categories/", "", $imgsubcat);
	$imgsubcat = (!$imgsubcat || $imgsubcat == "no.png") ? "" : $imgsubcat;
	$db->sql_query("INSERT INTO ".$prefix."_categories VALUES (NULL, '$modul', '$title', '$description', '$imgsubcat', '$language', '$cid')");
	Header("Location: ".$admin_file.".php?op=cat_show");
	break;
	
	case "cat_edit":
	cat_edit();
	break;
	
	case "cat_save":
	$modul = $_POST['modul'];
	$title = $_POST['title'];
	$description = $_POST['description'];
	$imgcat = $_POST['imgcat'];
	$language = $_POST['language'];
	$parentid = $_POST['parentid'];
	$imgcat = str_replace("images/categories/", "", $imgcat);
	$imgcat = (!$imgcat || $imgcat == "no.png") ? "" : $imgcat;
	$db->sql_query("UPDATE ".$prefix."_categories SET modul='$modul', title='$title', description='$description', img='$imgcat', language='$language', parentid='$parentid' WHERE id='".$id."'");
	Header("Location: ".$admin_file.".php?op=cat_show");
	break;
	
	case "cat_del":
	$db->sql_query("DELETE FROM ".$prefix."_categories WHERE id='".$id."'");
	$db->sql_query("DELETE FROM ".$prefix."_categories WHERE parentid='".$id."'");
	referer("".$admin_file.".php?op=cat_show");
	break;
}
?>