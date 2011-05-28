<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

function comm_navi() {
	global $admin_file, $chng_user, $search;
	panel();
	open();
	echo "<h1>"._EDITCOMMENTS."</h1>"
	."<h5>[ <a href=\"".$admin_file.".php?op=comm_show\">"._HOME."</a> | <a href=\"".$admin_file.".php?op=comm_conf\">"._PREFERENCES."</a> ]</h5>";
	close();
}

function comm_show() {
	global $prefix, $db, $admin_file, $conf, $confu;
	head();
	comm_navi();
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $conf['anum'] ;
	$result = $db->sql_query("SELECT a.id, a.cid, a.modul, a.name, a.comment, b.user_name FROM ".$prefix."_comment AS a LEFT JOIN ".$prefix."_users AS b ON (a.uid=b.user_id) ORDER BY date DESC LIMIT ".$offset.", ".$conf['anum']."");
	if ($db->sql_numrows($result) > 0) {
		open();
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr>"
		."<th>"._ID."</th><th>"._MODUL."</th><th>"._NICKNAME."</th><th width=\"60%\">"._COMMENT."</th><th>"._FUNCTIONS."</th></tr>";
		while (list($id, $cid, $com_modul, $com_name, $com_text, $user_name) = $db->sql_fetchrow($result)) {
			$com_name = ($user_name) ? "".user_info($user_name, 1)."" : (($com_name) ? $com_name : $confu['anonym']);
			echo "<tr class=\"bgcolor1\">"
			."<td align=\"center\">$id</td>"
			."<td align=\"center\">$com_modul</td>"
			."<td align=\"center\">$com_name</td>"
			."<td>".bb_decode($com_text, $com_modul)."</td>"
			."<td align=\"center\">".ad_edit("".$admin_file.".php?op=comm_edit&id=".$id."")." <a href=\"".view_article($com_modul, $cid, $id)."\" title=\""._READMORE."\"><img src=\"".img_find("all/about")."\" border=\"0\" align=\"center\" alt=\""._READMORE."\"></a> ".ad_delete("".$admin_file.".php?op=comm_del&id=$id&cid=$cid&modul=".$com_modul."&refer=1", cutstr(text_filter(bb_decode($com_text, $com_modul)), 50))."</td></tr>";
		}
		echo "</table>";
		close();
		list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_comment"));
		$numpages = ceil($numstories / $conf['anum']);
		num_page("", $numstories, $numpages, $conf['anum'], "op=comm_show&");
	} else {
		warning(""._NO_INFO."", "", "", 2);
	}
	foot();
}

function comm_edit() {
	global $db, $prefix, $admin_file;
	$id = intval($_GET['id']);
	head();
	comm_navi();
	$result = $db->sql_query("SELECT id, modul, comment FROM ".$prefix."_comment WHERE id='$id'");
	list($id, $modul, $com_text) = $db->sql_fetchrow($result);
	open();
	echo "<h2>"._EDIT."</h2>"
	."<form name=\"post\" action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._COMMENT.":</div><div class=\"center\">".textarea("1", "comment", $com_text, $modul, "5")."</div>"
	."<div class=\"button\">"._GOBACK." <input type=\"hidden\" name=\"id\" value=\"$id\"><input type=\"hidden\" name=\"op\" value=\"comm_edit_save\"><input type=\"submit\" value=\""._SAVE."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function comm_edit_save() {
	global $prefix, $db, $admin_file;
	$id = intval($_POST['id']);
	$com_text = nl2br(text_filter($_POST['comment'], 2));
	$db->sql_query("UPDATE ".$prefix."_comment SET id='$id', comment='$com_text' WHERE id='$id'");
	Header("Location: ".$admin_file.".php?op=comm_show");
}

function comm_conf() {
	global $admin_file;
	head();
	comm_navi();
	include("config/config_comments.php");
	$permtest = end_chmod("config/config_comments.php", 666);
	if ($permtest) warning($permtest, "", "", 1);
	open();
	echo "<h2>"._GENSITEINFO."</h2>"
	."<form action='".$admin_file.".php' method='post'>"
	."<div class=\"left\">"._COMNUM.":</div><div class=\"center\"><input type='text' name='num' value='".$confc['num']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._COMLETTER.":</div><div class=\"center\"><input type='text' name='letter' value='".$confc['letter']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._SORT.":</div><div class=\"center\"><select name='sort' class=\"admin\">"
	."<option value='1'";
	if ($confc['sort'] == "1") echo " selected";
	echo ">"._ASC."</option>"
	."<option value='0'";
	if ($confc['sort'] == "0") echo " selected";
	echo ">"._DESC."</option>"
	."</select></div>"
	."<div class=\"left\">"._ALLOWANONPOST."</div><div class=\"center\">".radio_form($confc['anonpost'], "anonpost")."</div>"
	."<div class=\"button\"><input type='hidden' name='op' value='comm_save'><input type='submit' value='"._SAVECHANGES."' class=\"fbutton\"></div></form>";
	close();
	foot();
}

function comm_save() {
	global $admin_file;
	$xnum = (!intval($_POST['num'])) ? 10 : $_POST['num'];
	$xletter = (!intval($_POST['letter'])) ? 50 : $_POST['letter'];
	$content = "\$confc = array();\n"
	."\$confc['num'] = \"".$xnum."\";\n"
	."\$confc['letter'] = \"".$xletter."\";\n"
	."\$confc['sort'] = \"".$_POST['sort']."\";\n"
	."\$confc['anonpost'] = \"".$_POST['anonpost']."\";\n";
	save_conf("config/config_comments.php", $content);
	Header("Location: ".$admin_file.".php?op=comm_conf");
}

switch($op) {
	case "comm_show":
	comm_show();
	break;
	
	case "comm_edit":
	comm_edit();
	break;
	
	case "comm_edit_save":
	comm_edit_save();
	break;
	
	case "comm_del":
	$id = intval($_GET['id']);
	$cid = intval($_GET['cid']);
	$modul = $_GET['modul'];
	if ($id && $cid && $modul) {
		$db->sql_query("DELETE FROM ".$prefix."_comment WHERE id='$id'");
		if ($modul == "files") {
			$db->sql_query("UPDATE ".$prefix."_files SET totalcomments=totalcomments-1 WHERE lid='$cid'");
		} elseif ($modul == "news") {
			$db->sql_query("UPDATE ".$prefix."_stories SET comments=comments-1 WHERE sid='$cid'");
		} elseif ($modul == "voting") {
			$db->sql_query("UPDATE ".$prefix."_survey SET pool_comments=pool_comments-1 WHERE poll_id='$cid'");
		}
	}
	referer("".$admin_file.".php?op=comm_show");
	break;
	
	case "comm_conf":
	comm_conf();
	break;
	
	case "comm_save":
	comm_save();
	break;
}
?>