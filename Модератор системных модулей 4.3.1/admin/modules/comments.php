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

if (!defined("ADMIN_FILE") || !is_admin_god(basename(__FILE__))) die("Illegal File Access");

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
	echo ashowcom("", "", 5);
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
	."<div class=\"left\">"._COMMENT.":</div><div class=\"center\">".textarea("1", "comment", $com_text, $modul, "10")."</div>"
	."<div class=\"button\"><input type=\"hidden\" name=\"id\" value=\"$id\"><input type=\"hidden\" name=\"op\" value=\"comm_edit_save\"><input type=\"submit\" value=\""._SAVECHANGES."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function comm_edit_save() {
	global $prefix, $db, $admin_file;
	$id = intval($_POST['id']);
	$com_text = nl2br(text_filter($_POST['comment'], 2));
	$db->sql_query("UPDATE ".$prefix."_comment SET comment='$com_text' WHERE id='$id'");
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
	."<div class=\"left\">"._COMNUM.":</div><div class=\"center\"><input type=\"text\" name=\"num\" value=\"".$confc['num']."\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._COMLETTER.":</div><div class=\"center\"><input type=\"text\" name=\"letter\" value=\"".$confc['letter']."\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._CEDITT.":</div><div class=\"center\"><input type=\"text\" name=\"edit\" value=\"".intval($confc['edit'] / 60)."\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._CSEND.":</div><div class=\"center\"><input type=\"text\" name=\"send\" value=\"".$confc['send']."\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._SORT.":</div><div class=\"center\"><select name=\"sort\" class=\"admin\">"
	."<option value=\"1\"";
	if ($confc['sort'] == "1") echo " selected";
	echo ">"._ASC."</option>"
	."<option value=\"0\"";
	if ($confc['sort'] == "0") echo " selected";
	echo ">"._DESC."</option>"
	."</select></div>"
	."<div class=\"left\">"._ALLOWANONPOST."</div><div class=\"center\"><select name=\"anonpost\" class=\"admin\">"
	."<option value=\"0\"";
	if ($confc['anonpost'] == "0") echo " selected";
	echo ">"._NO."</option>"
	."<option value=\"1\"";
	if ($confc['anonpost'] == "1") echo " selected";
	echo ">"._APOSTMOD."</option>"
	."<option value=\"2\"";
	if ($confc['anonpost'] == "2") echo " selected";
	echo ">"._APOSTNOMOD."</option>"
	."</select></div>"
	."<div class=\"left\">"._VPRIVAT."</div><div class=\"center\">".radio_form($confc['privat'], "privat")."</div>"
	."<div class=\"left\">"._VPROFIL."</div><div class=\"center\">".radio_form($confc['profil'], "profil")."</div>"
	."<div class=\"left\">"._VWEB."</div><div class=\"center\">".radio_form($confc['web'], "web")."</div>"
	."<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"comm_save\"><input type=\"submit\" value=\""._SAVECHANGES."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function comm_save() {
	global $admin_file;
	$xnum = (!intval($_POST['num'])) ? 10 : $_POST['num'];
	$xletter = (!intval($_POST['letter'])) ? 50 : $_POST['letter'];
	$xedit = (!intval($_POST['edit'])) ? 600 : $_POST['edit'] * 60;
	$xsend = (!intval($_POST['send'])) ? 30 : $_POST['send'];
	$content = "\$confc = array();\n"
	."\$confc['num'] = \"".$xnum."\";\n"
	."\$confc['letter'] = \"".$xletter."\";\n"
	."\$confc['edit'] = \"".$xedit."\";\n"
	."\$confc['send'] = \"".$xsend."\";\n"
	."\$confc['sort'] = \"".$_POST['sort']."\";\n"
	."\$confc['anonpost'] = \"".$_POST['anonpost']."\";\n"
	."\$confc['privat'] = \"".$_POST['privat']."\";\n"
	."\$confc['profil'] = \"".$_POST['profil']."\";\n"
	."\$confc['web'] = \"".$_POST['web']."\";\n";
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
	
	case "comm_act":
	$id = (isset($_POST['id'])) ? $_POST['id'] : "";
	if (is_array($id)) {
		$cwhere = implode(", ", $id);
		$db->sql_query("UPDATE ".$prefix."_comment SET status='1' WHERE id IN (".$cwhere.")");
	}
	referer($admin_file.".php?op=comm_show");
	break;
	
	case "comm_del":
	$id = (isset($_POST['id'])) ? ((isset($_POST['id'])) ? $_POST['id'] : "") : ((isset($_GET['id'])) ? array($_GET['id']) : "");
	if (is_array($id)) {
		foreach ($id as $val) {
			list($cid, $modul) = $db->sql_fetchrow($db->sql_query("SELECT cid, modul FROM ".$prefix."_comment WHERE id='$val'"));
			if (intval($val) && $cid && $modul) {
				$db->sql_query("DELETE FROM ".$prefix."_comment WHERE id='$val'");
				if ($modul == "faq") {
					$db->sql_query("UPDATE ".$prefix."_faq SET comments=comments-1 WHERE fid='$cid'");
				} elseif ($modul == "files") {
					$db->sql_query("UPDATE ".$prefix."_files SET totalcomments=totalcomments-1 WHERE lid='$cid'");
				} elseif ($modul == "links") {
					$db->sql_query("UPDATE ".$prefix."_links SET totalcomments=totalcomments-1 WHERE lid='$cid'");
				} elseif ($modul == "media") {
					$db->sql_query("UPDATE ".$prefix."_media SET totalcom=totalcom-1 WHERE id='$cid'");
				} elseif ($modul == "news") {
					$db->sql_query("UPDATE ".$prefix."_stories SET comments=comments-1 WHERE sid='$cid'");
				} elseif ($modul == "pages") {
					$db->sql_query("UPDATE ".$prefix."_page SET comments=comments-1 WHERE pid='$cid'");
				} elseif ($modul == "shop") {
					$db->sql_query("UPDATE ".$prefix."_products SET product_com=product_com-1 WHERE product_id='$cid'");
				} elseif ($modul == "voting") {
					$db->sql_query("UPDATE ".$prefix."_survey SET pool_comments=pool_comments-1 WHERE poll_id='$cid'");
				}
			}
		}
	}
	referer($admin_file.".php?op=comm_show");
	break;
	
	case "comm_conf":
	comm_conf();
	break;
	
	case "comm_save":
	comm_save();
	break;
}
?>