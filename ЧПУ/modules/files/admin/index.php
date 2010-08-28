<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_modul("files")) die("Illegal File Access");

include("config/config_files.php");

function files_navi() {
	global $admin_file;
	panel();
	open();
	echo "<h1>"._FILES."</h1>"
	."<h5>[ <a href=\"".$admin_file.".php?op=files\">"._HOME."</a>"
	." | <a href=\"".$admin_file.".php?op=files_add\">"._ADD."</a>"
	." | <a href=\"".$admin_file.".php?op=files&status=1\">"._NEWFILES."</a>"
	." | <a href=\"".$admin_file.".php?op=files&status=2\">"._BROCFILES."</a>"
	." | <a href=\"".$admin_file.".php?op=files_conf\">"._PREFERENCES."</a> ]</h5>";
	close();
}

function files() {
	global $prefix, $db, $admin_file, $conf, $confu;
	head();
	files_navi();
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $conf['anum'];
	$offset = intval($offset);
	if ($_GET['status'] == 1) {
		$status = "0";
		$field = "op=files&status=1&";
		$refer = "&refer=1";
	} elseif ($_GET['status'] == 2) {
		$status = "2";
		$field = "op=files&status=2&";
		$refer = "";
	} else {
		$status = "1";
		$field = "op=files&";
		$refer = "";
	}
	$result = $db->sql_query("SELECT f.lid, f.name, f.title, f.date, f.ip_sender, c.id, c.title, u.user_name FROM ".$prefix."_files AS f LEFT JOIN ".$prefix."_categories AS c ON (f.cid=c.id) LEFT JOIN ".$prefix."_users AS u ON (f.uid=u.user_id) WHERE status='".$status."' ORDER BY f.date DESC LIMIT ".$offset.", ".$conf['anum']."");
	if ($db->sql_numrows($result) > 0) {
		open();
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr><th>"._ID."</th><th>"._TITLE."</th><th>"._IP."</th><th>"._POSTEDBY."</th><th>"._FUNCTIONS."</th></tr>";
		while (list($id, $uname, $title, $date, $ip_sender, $cid, $ctitle, $user_name) = $db->sql_fetchrow($result)) {
			$post = ($user_name) ? user_info($user_name, 1) : (($uname) ? $uname : $confu['anonym']);
			$ip_sender = ($ip_sender) ? $ip_sender : ""._NO."";
			$ctitle = ($cid) ? $ctitle : ""._NO."";
			$broc = ($status == 2) ? ad_broc("".$admin_file.".php?op=files_ignore&id=".$id."") : "";
			$ad_view = ($status) ? ad_view(view_article("files", $id)) : "";
			echo "<tr class=\"bgcolor1\"><td align=\"center\">".$id."</td>"
			."<td class=\"help\" OnMouseOver=\"Tip('"._CATEGORY.": $ctitle<br />"._DATE.": $date')\">".$title."</td>"
			."<td>".user_geo_ip($ip_sender, 4)."</td>"
			."<td align=\"center\">".$post."</td>"
			."<td align=\"center\">".$ad_view." ".ad_edit("".$admin_file.".php?op=files_add&id=".$id."")." ".ad_delete("".$admin_file.".php?op=files_delete&id=".$id."".$refer."", $title)." ".$broc."</td></tr>";
		}
		echo "</table>";
		close();
		num_article("files", $conf['anum'], $field, "lid", "_files", "cid", "status='".$status."'");
	} else {
		warning(""._NO_INFO."", "", "", 2);
	}
	foot();
}

function files_add() {
	global $prefix, $db, $admin_file, $conff, $confu, $stop;
	if (isset($_REQUEST['id'])) {
		$fid = intval($_REQUEST['id']);
		$result = $db->sql_query("SELECT f.cid, f.name, f.title, f.description, f.bodytext, f.url, f.date, f.filesize, f.version, f.email, f.homepage, u.user_name FROM ".$prefix."_files AS f LEFT JOIN ".$prefix."_users AS u ON (f.uid=u.user_id) WHERE lid='$fid'");
		list($cid, $uname, $title, $description, $bodytext, $url, $date, $filesize, $version, $email, $homepage, $user_name) = $db->sql_fetchrow($result);
		$postname = ($user_name) ? $user_name : (($uname) ? $uname : $confu['anonym']);
	} else {
		$fid = $_POST['fid'];
		$cid = $_POST['cid'];
		$title = save_text($_POST['title']);
		$description = save_text($_POST['description']);
		$bodytext = save_text($_POST['bodytext']);
		$url = $_POST['url'];
		$date = $_POST['date'];
		$filesize = $_POST['filesize'];
		$version = $_POST['version'];
		$postname = $_POST['postname'];
		$email = $_POST['email'];
		$homepage = (isset($_POST['homepage'])) ? $_POST['homepage'] : "http://";
	}
	head();
	files_navi();
	if ($stop) warning($stop, "", "", 1);
	if ($description) preview($title, $description, $bodytext, "", "files");
	$link_url = ($url) ? "<a href=\"".$url."\" target=\"_blank\" title=\""._TESTLINK."\">"._FILELINK."</a>": ""._FILELINK."";
	if (file_exists($url)) {
		$handle = opendir($conff['path']);
		$directory = "";
		while ($file = readdir($handle)) {
			if (!preg_match("/\./", $file)) $directory .= "<option value=\"".$conff['path']."/".$file."\">".$conff['path']."/".$file."</option>";
		}
		closedir($handle);
	}
	open();
	echo "<form name=\"post\" enctype=\"multipart/form-data\" action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._TITLE.":</div><div class=\"center\"><input type=\"text\" name=\"title\" value=\"".$title."\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._CATEGORY.":</div><div class=\"center\"><select name=\"cid\" class=\"admin\">".getcat("files", $cid)."</select></div>"
	."<div class=\"left\">"._TEXT.":</div><div class=\"center\">".textarea("1", "description", $description, "files", "5")."</div>"
	."<div class=\"left\">"._ENDTEXT.":</div><div class=\"center\">".textarea("2", "bodytext", $bodytext, "files", "15")."</div>"
	."<div class=\"left\">"._POSTEDBY.":</div><div class=\"center\">".get_user_search("postname", $postname, "25", "65", "400")."</div>"
	."<div class=\"left\">"._AUEMAIL.":</div><div class=\"center\"><input type=\"text\" name=\"email\" value=\"".$email."\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._FAUURL.":</div><div class=\"center\"><input type=\"text\" name=\"homepage\" value=\"".$homepage."\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._FILE_USER.":</div><div class=\"center\"><input type=\"file\" name=\"userfile\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._FILE_SITE.":</div><div class=\"center\"><input type=\"text\" name=\"sitefile\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">".$link_url.":</div><div class=\"center\"><input type=\"text\" name=\"url\" value=\"".$url."\" size=\"65\" class=\"admin\"></div>";
	if (file_exists($url)) echo "<div class=\"left\">"._FILE_DIR.":</div><div class=\"center\"><select name=\"path\" class=\"admin\"><option value=\"\">"._NO."</option><option value=\"".$conff['path']."\">".$conff['path']."</option>".$directory."</select></div>";
	echo "<div class=\"left\">"._FILEVERSION.":</div><div class=\"center\"><input type=\"text\" name=\"version\" value=\"".$version."\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._SIZENOTE.":</div><div class=\"center\"><input type=\"text\" name=\"filesize\" value=\"".$filesize."\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._CHNGSTORY.":</div><div class=\"center\" style=\"white-space: nowrap;\">".datetime(1, $date)."</div>"
	."<div class=\"button\"><select name=\"posttype\">"
	."<option value=\"preview\">"._PREVIEW."</option>"
	."<option value=\"save\">"._SEND."</option></select>"
	."<input type=\"hidden\" name=\"fid\" value=\"$fid\">"
	."<input type=\"hidden\" name=\"op\" value=\"files_save\">"
	." <input type=\"submit\" value=\""._OK."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function files_save() {
	global $prefix, $db, $admin_file, $stop, $conff;
	$fid = intval($_POST['fid']);
	$cid = intval($_POST['cid']);
	$title = save_text($_POST['title']);
	$description = save_text($_POST['description']);
	$bodytext = save_text($_POST['bodytext']);
	$url = $_POST['url'];
	$path = text_filter($_POST['path']);
	$date = save_datetime();
	$filesize = intval($_POST['filesize']);
	$version = text_filter($_POST['version']);
	$postname = $_POST['postname'];
	$email = text_filter($_POST['email']);
	$homepage = url_filter($_POST['homepage']);
	if (!$title) $stop = ""._CERROR."";
	if (!$description) $stop = ""._CERROR1."";
	if (!$postname) $stop = ""._CERROR3."";
	if (!$fid && $db->sql_numrows($db->sql_query("SELECT title FROM ".$prefix."_files WHERE title='$title'")) > 0) $stop = ""._MEDIAEXIST."";
	$filename = upload(1, $conff['path'], $conff['typefile'], $conff['max_size'], "files", "", "");
	$url = ($filename) ? $conff['path']."/".$filename : $url;
	$filesize =  ($filename) ? filesize($url) : $filesize;
	if ($stop) {
		$stop = $stop;
	} elseif (!$url  && $_POST['posttype'] == "save") {
		$stop = ""._UPLOADEROR2."";
	}
	if (!$stop && $_POST['posttype'] == "save") {
		$postid = (is_user_id($postname)) ? is_user_id($postname) : "";
		$postname = (!is_user_id($postname)) ? text_filter(substr($postname, 0, 25)) : "";
		if ($fid) {
			if ($path) {
				$filel = array_reverse(explode("/", $url));
				if (file_exists($url)) {
					$newfile = "".$path."/".$filel[0]."";
					rename($url, $newfile);
					$url = "".$path."/".$filel[0]."";
				}
			}
			$db->sql_query("UPDATE ".$prefix."_files SET cid='$cid', uid='$postid', name='$postname', title='$title', description='$description', bodytext='$bodytext', url='$url', date='$date', filesize='$filesize', version='$version', email='$email', homepage='$homepage', status='1' WHERE lid='$fid'");
		} else {
			$ip = getip();
			$db->sql_query("INSERT INTO ".$prefix."_files (lid, cid, uid, name, title, description, bodytext, url, date, filesize, version, email, homepage, ip_sender, status) VALUES (NULL, '$cid', '$postid', '$postname', '$title', '$description', '$bodytext', '$url', '$date', '$filesize', '$version', '$email', '$homepage', '$ip', '1')");
		}
		Header("Location: ".$admin_file.".php?op=files");
	} else {
		files_add();
	}
}

function files_conf() {
	global $prefix, $db, $admin_file;
	head();
	files_navi();
	include("config/config_files.php");
	$permtest = end_chmod("config/config_files.php", 666);
	if ($permtest) warning($permtest, "", "", 1);
	open();
	echo "<h2>"._GENSITEINFO."</h2>"
	."<form action='".$admin_file.".php' method='post'>"
	."<div class=\"left\">"._F_0.":</div><div class=\"center\"><input type='text' name='temp' value='".$conff['temp']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._F_1.":</div><div class=\"center\"><input type='text' name='path' value='".$conff['path']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._FSIZE.""._FIN.":</div><div class=\"center\"><input type='text' name='max_size' value='".$conff['max_size']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._FTYPE.":</div><div class=\"center\"><input type='text' name='typefile' value='".$conff['typefile']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._C_13.":</div><div class=\"center\"><input type='text' name='listnum' value='".$conff['listnum']."' maxlength='25' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._C_10.":</div><div class=\"center\"><input type='text' name='tabcol' value='".$conff['tabcol']."' maxlength='25' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._F_5.":</div><div class=\"center\"><input type='text' name='num' value='".$conff['num']."' maxlength='25' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._STREAM.":</div><div class=\"center\"><select name='stream' class=\"admin\">"
	."<option value='0'";
	if ($conff['stream'] == "0") echo " selected";
	echo ">"._STREAM_NO."</option>"
	."<option value='1'";
	if ($conff['stream'] == "1") echo " selected";
	echo ">"._STREAM_1."</option>"
	."<option value='2'";
	if ($conff['stream'] == "2") echo " selected";
	echo ">"._STREAM_2."</option>"
	."</select></div>"
	."<div class=\"left\">"._F_6."</div><div class=\"center\">".radio_form($conff['comm'], "comm")."</div>"
	."<div class=\"left\">"._F_8."</div><div class=\"center\">".radio_form($conff['addfiles'], "addfiles")."</div>"
	."<div class=\"left\">"._F_9."</div><div class=\"center\">".radio_form($conff['addquest'], "addquest")."</div>"
	."<div class=\"left\">"._C_15."</div><div class=\"center\">".radio_form($conff['subkat'], "subkat")."</div>"
	."<div class=\"left\">"._F_11."</div><div class=\"center\">".radio_form($conff['broc'], "broc")."</div>"
	."<div class=\"left\">"._F_12."</div><div class=\"center\">".radio_form($conff['down'], "down")."</div>"
	."<div class=\"left\">"._UPFILE."</div><div class=\"center\">".radio_form($conff['upload'], "upload")."</div>"
	."<div class=\"left\">"._C_17."</div><div class=\"center\">".radio_form($conff['date'], "date")."</div>"
	."<div class=\"left\">"._C_18."</div><div class=\"center\">".radio_form($conff['read'], "read")."</div>"
	."<div class=\"left\">"._C_19."</div><div class=\"center\">".radio_form($conff['rate'], "rate")."</div>"
	."<div class=\"left\">"._C_20."</div><div class=\"center\">".radio_form($conff['letter'], "letter")."</div>"
	."<div class=\"left\">"._C_32."</div><div class=\"center\">".radio_form($conff['catdesc'], "catdesc")."</div>"
	."<div class=\"button\"><input type='hidden' name='op' value='files_save_conf'><input type='submit' value='"._SAVECHANGES."' class=\"fbutton\"></div></form>";
	close();
	foot();
}

function files_save_conf() {
	global $admin_file;
	$protect = array("\n" => "", "\t" => "", "\r" => "", " " => "");
	$xtypefile = (!$_POST['typefile']) ? "zip,gzip,rar" : strtolower(strtr($_POST['typefile'], $protect));
	$content = "\$conff = array();\n"
	."\$conff['temp'] = \"".$_POST['temp']."\";\n"
	."\$conff['path'] = \"".$_POST['path']."\";\n"
	."\$conff['max_size'] = \"".$_POST['max_size']."\";\n"
	."\$conff['typefile'] = \"$xtypefile\";\n"
	."\$conff['listnum'] = \"".$_POST['listnum']."\";\n"
	."\$conff['tabcol'] = \"".$_POST['tabcol']."\";\n"
	."\$conff['num'] = \"".$_POST['num']."\";\n"
	."\$conff['comm'] = \"".$_POST['comm']."\";\n"
	."\$conff['addfiles'] = \"".$_POST['addfiles']."\";\n"
	."\$conff['addquest'] = \"".$_POST['addquest']."\";\n"
	."\$conff['subkat'] = \"".$_POST['subkat']."\";\n"
	."\$conff['broc'] = \"".$_POST['broc']."\";\n"
	."\$conff['down'] = \"".$_POST['down']."\";\n"
	."\$conff['upload'] = \"".$_POST['upload']."\";\n"
	."\$conff['date'] = \"".$_POST['date']."\";\n"
	."\$conff['read'] = \"".$_POST['read']."\";\n"
	."\$conff['rate'] = \"".$_POST['rate']."\";\n"
	."\$conff['letter'] = \"".$_POST['letter']."\";\n"
	."\$conff['catdesc'] = \"".$_POST['catdesc']."\";\n"
	."\$conff['stream'] = \"".$_POST['stream']."\";\n";
	save_conf("config/config_files.php", $content);
	Header("Location: ".$admin_file.".php?op=files_conf");
}

switch ($op) {
	case "files":
	files();
	break;
	
	case "files_add":
	files_add();
	break;
	
	case "files_save":
	files_save();
	break;
	
	case "files_delete":
	list($url) = $db->sql_fetchrow($db->sql_query("SELECT url FROM ".$prefix."_files WHERE lid=".$id.""));
	if (file_exists($url)) unlink($url);
	$db->sql_query("DELETE FROM ".$prefix."_comment WHERE cid='".$id."' AND modul='files'");
	$db->sql_query("DELETE FROM ".$prefix."_files WHERE lid='".$id."'");
	referer("".$admin_file.".php?op=files");
	break;
	
	case "files_ignore":
	$db->sql_query("UPDATE ".$prefix."_files SET status='1' WHERE lid='".$id."'");
	Header("Location: ".$admin_file.".php?op=files&status=2");
	break;
	
	case "files_conf":
	files_conf();
	break;
	
	case "files_save_conf":
	files_save_conf();
	break;
}
?>