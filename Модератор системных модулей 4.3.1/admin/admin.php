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

if (!defined("ADMIN_FILE")) die("Illegal File Access");
include("function/function.php");
get_lang('admin');

function add_admin() {
	global $prefix, $db, $admin_file, $conf, $stop;
	if ($db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_admins")) == 0) {
		$aname = $_POST['aname'];
		$aurl = url_filter($_POST['aurl']);
		$aemail = $_POST['aemail'];
		$apwd = md5($_POST['apwd']);
		$apwd2 = md5($_POST['apwd2']);
		$auser_new = intval($_POST['auser_new']);
		$aeditor = intval($conf['redaktor']);
		$alang = analyze($_COOKIE["lang"]);
		$aip = getip();
		if (!$aname || !analyze_name($aname)) $stop = _ERRORINVNICK;
		if (!$_POST['apwd'] && !$_POST['apwd2']) $stop = _NOPASS;
		if ($apwd != $apwd2) $stop = _ERROR_PASS;
		if (strlen($aname) > 25) $stop = _NICKLONG;
		if (!$stop) {
			$db->sql_query("INSERT INTO ".$prefix."_admins VALUES (NULL, '".$aname."', 'Admin', '".$aurl."', '".$aemail."', '".$apwd."', '1', '".$aeditor."', '1', '', '".$alang."', '".$aip."', now(), now())");
			if ($auser_new == 1) {
				$auser_avatar = "00.gif";
				$user_exist = $db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_users WHERE user_name='$aname'"));
				if ($user_exist) $db->sql_query("DELETE FROM ".$prefix."_users WHERE user_name='$aname'");
				$db->sql_query("INSERT INTO ".$prefix."_users (user_id, user_name, user_email, user_website, user_avatar, user_regdate, user_password, user_lang, user_last_ip) VALUES (NULL, '$aname', '$aemail', '$aurl', '$auser_avatar', now(), '$apwd', '$alang', '$aip')");
			}
			header("Location: ".$admin_file.".php");
		} else {
			login();
		}
	} else {
		header("Location: ".$admin_file.".php");
	}
}

function check_admin() {
	global $prefix, $db, $admin_file, $conf, $stop;
	$code = $_SESSION['captcha'];
	unset($_SESSION['captcha']);
	if (extension_loaded("gd") && $code != intval($_POST['check']) && ($conf['gfx_chk'] == 1 || $conf['gfx_chk'] == 5 || $conf['gfx_chk'] == 6 || $conf['gfx_chk'] == 7)) $stop = _SECCODEINCOR;
	$name = htmlspecialchars(trim(substr($_POST['name'], 0, 25)));
	$pwd = htmlspecialchars(trim(substr($_POST['pwd'], 0, 25)));
	if (!$name || !$pwd) $stop = _LOGININCOR;
	$result = $db->sql_query("SELECT id, name, pwd, editor FROM ".$prefix."_admins WHERE name='$name' AND pwd='".md5($pwd)."'");
	if ($db->sql_numrows($result) != 1) $stop = _LOGININCOR;
	list($aid, $aname, $apwd, $aeditor) = $db->sql_fetchrow($result);
	if (!$aid || $aname != $name || $apwd != md5($pwd)) $stop = _LOGININCOR;
	if (!$stop) {
		unset($_SESSION[$conf['admin_c']]);
		$info = base64_encode("$aid:$aname:$apwd:$aeditor");
		$_SESSION[$conf['admin_c']] = $info;
		$ip = getip();
		$visit = save_datetime(1, "");
		$db->sql_query("DELETE FROM ".$prefix."_session WHERE uname='$ip'");
		$db->sql_query("UPDATE ".$prefix."_admins SET ip='$ip', lastvisit='$visit' WHERE id='$aid'");
		login_report(1, 1, $name, "");
		header("Location: ".$admin_file.".php");
	} else {
		login_report(1, 0, $name, $pwd);
		login();
	}
}

function login() {
	global $prefix, $db, $admin_file, $conf, $stop;
	head();
	if ($db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_admins")) == 0) {
		echo "<h3 class=\"btitle\">"._ADMINLOGIN_NEW."</h3>";
		if ($stop) warning($stop, "", "", 1);
		$dom = (getenv("SERVER_NAME")) ? getenv("SERVER_NAME") : getenv("HTTP_HOST");
		echo "<form action=\"".$admin_file.".php\" method=\"post\">"
		."<div class=\"enter\">"
		."<div><span>"._NICKNAME.":</span><input type=\"text\" name=\"aname\" value=\"".$_POST['aname']."\" size=\"25\" maxlength=\"25\" class=\"ftext\"></div>"
		."<div><span>"._HOMEPAGE.":</span><input type=\"text\" name=\"aurl\" value=\"http://".$dom."\" size=\"25\" maxlength=\"255\" class=\"ftext\"></div>"
		."<div><span>"._EMAIL.":</span><input type=\"text\" name=\"aemail\" value=\"".$_POST['aemail']."\" size=\"25\" maxlength=\"255\" class=\"ftext\"></div>"
		."<div><span>"._PASSWORD.":</span><input type=\"password\" name=\"apwd\" size=\"25\" maxlength=\"25\" class=\"ftext\"></div>"
		."<div><span>"._RETYPEPASSWORD.":</span><input type=\"password\" name=\"apwd2\" size=\"25\" maxlength=\"25\" class=\"ftext\"></div>"
		."<div><span>"._CREATEUSERDATA."</span><input type=\"radio\" name=\"auser_new\" value=\"1\" checked>"._YES." &nbsp;<input type=\"radio\" name=\"auser_new\" value=\"0\">"._NO."</div>"
		."</div><div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"add_admin\"><input type=\"submit\" value=\""._SEND."\" class=\"fbutton\"></div></form>";
	} else {
		echo "<h3 class=\"btitle\">"._ADMINLOGIN."</h3>";
		if ($stop) warning($stop, "", "", 1);
		echo "<form action=\"".$admin_file.".php\" method=\"post\">"
		."<div class=\"enter\">"
		."<div><span>"._NICKNAME.":</span><input type=\"text\" name=\"name\" size=\"10\" maxlength=\"25\" class=\"ftext\"></div>"
		."<div><span>"._PASSWORD.":</span><input type=\"password\" name=\"pwd\" size=\"10\" maxlength=\"25\" class=\"ftext\"></div>";
		if (extension_loaded("gd") && ($conf['gfx_chk'] == 1 || $conf['gfx_chk'] == 5 || $conf['gfx_chk'] == 6 || $conf['gfx_chk'] == 7)) {
			echo "<div><span>"._SECURITYCODE.":</span><img src=\"captcha.php\" onclick=\"if(!this.adress)this.adress = this.src; this.src=adress+'?rand='+Math.random();\" border=\"1\" title=\"Нажмите, чтобы обновить картинку\" style=\"cursor:pointer;\" alt=\""._SECURITYCODE."\"></div>"
			."<div><span>"._TYPESECCODE.":</span><input type=\"text\" name=\"check\" size=\"10\" maxlength=\"6\" style=\"width: 75px;\" class=\"ftext\"></div>";
		}
		echo "</div><div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"check_admin\"><input type=\"submit\" value=\""._LOGIN."\" class=\"fbutton\"></div></form>";
	}
	foot();
}

function logout() {
	global $prefix, $db, $admin, $admin_file, $conf;
	$aname = text_filter(substr($admin[1], 0, 25), 1);
	$db->sql_query("DELETE FROM ".$prefix."_session WHERE uname='$aname' AND guest='3'");
	unset($_SESSION[$conf['admin_c']], $admin);
	header("Location: ".$admin_file.".php");
}

function adminmenu($url, $title, $image) {
	global $counter, $conf, $content_am, $class;
	$ltitle = ($class) ? $title." - "._DEACT : $title;
	$image = "images/admin/$image";
	if ($conf['panel'] == 1) {
		echo "<td align=\"center\" valign=\"top\" width=\"15%\" $class><a href=\"$url\" title=\"$ltitle\"><img src=\"$image\" border=\"0\" alt=\"$ltitle\" title=\"$ltitle\"><br><b>$title</b></a></td>";
		if ($counter == 5) {
			echo "</tr><tr>";
			$counter = 0;
		} else {
			$counter++;
		}
	} else {
		$content_am .= "<table><tr><td><img src=\"".img_find("misc/navi")."\" border=\"0\"></td><td><a href=\"$url\" title=\"$ltitle\">$title</a></td></tr></table>";
	}
}

function panelblock() {
	global $prefix, $db, $conf, $admin_file, $content_am, $currentlang;
	if ($conf['panel'] == 0) {
		if (is_admin_god()) {
			$dir = opendir("admin/links");
			while ($file = readdir($dir)) {
				if (substr($file, 0, 6) == "links.") include("admin/links/".$file);
			}
			closedir($dir);
			adminmenu($admin_file.".php?op=logout", _ADMINLOGOUT, "exit.png");
			$title_a = _ADMIN;
			themesidebox($title_a, $content_am, 1);
			$content_am = "";
		}
		$result = $db->sql_query("SELECT title, active FROM ".$prefix."_modules ORDER BY title ASC");
		while (list($title, $active) = $db->sql_fetchrow($result)) {
			if (is_admin_god() || is_admin_modul($title)) {
				if (file_exists("modules/$title/admin/index.php") && file_exists("modules/$title/admin/links.php")) {
					include("modules/$title/admin/links.php");
					if (file_exists("modules/$title/admin/language/lang-".$currentlang.".php")) include("modules/$title/admin/language/lang-".$currentlang.".php");
				}
			}
		}
		adminmenu($admin_file.".php?op=logout", _ADMINLOGOUT, "exit.png");
		$title_m = _MODULES;
		themesidebox($title_m, $content_am, 2);
	}
}

function panel() {
	global $prefix, $db, $conf, $counter, $admin_file, $currentlang, $class;
	if (file_exists("setup.php")) warning(_DELSETUP, "", "", 1);
	if (phpversion() < "4.3.0") warning(_PHPSETUP, "", "", 1);
	if ($conf['admininfo']) warning($conf['admininfo'], "", "", 2);
	if ($conf['panel'] == 1) {
		#if (is_admin_god()) {
			ob_start();
			$dir = opendir("admin/links");
			while ($file = readdir($dir)) {
				if (substr($file, 0, 6) == "links." /**/&& is_admin_god(str_replace('links.', '', $file))/**/) $files[] = $file;
			}
			closedir($dir);
			sort($files);
			foreach ($files as $entry) include("admin/links/".$entry);
			adminmenu($admin_file.".php?op=logout", _ADMINLOGOUT, "exit.png");
			$cont = ob_get_contents();
			ob_end_clean();
			panel_admin(_ADMINMENU, $cont);
			$counter = "";
		#}
		ob_start();
		$result = $db->sql_query("SELECT title, active FROM ".$prefix."_modules ORDER BY title ASC");
		while (list($title, $active) = $db->sql_fetchrow($result)) {
			if (is_admin_god() || is_admin_modul($title)) {
				if (file_exists("modules/$title/admin/index.php") && file_exists("modules/$title/admin/links.php")) {
					$class = (!$active) ? "class=\"hidden\"" : "";
					include("modules/$title/admin/links.php");
					if (file_exists("modules/$title/admin/language/lang-".$currentlang.".php")) include("modules/$title/admin/language/lang-".$currentlang.".php");
				}
			}
		}
		$class = "";
		adminmenu($admin_file.".php?op=logout", _ADMINLOGOUT, "exit.png");
		$cont = ob_get_contents();
		ob_end_clean();
		panel_modul(_MODULESADMIN, $cont);
	}
}

function admin() {
	global $admin_file, $conf, $panel;
	if (is_admin_god()) {
		header("Location: ".$admin_file.".php?op=".$conf['amod']."&panel=1");
	} else {
		if (is_active($conf['amod']) && is_admin_modul($conf['amod'])) {
			header("Location: ".$admin_file.".php?op=".$conf['amod']."&panel=1");
		} else {
			$panel = 1;
			head();
			panel();
			foot();
		}
	}
}

if (is_admin()) {
	$op = (isset($_POST['op'])) ? analyze($_POST['op']) : analyze($_GET['op']);
	$op = ($op) ? $op : "admin";
	$id = (isset($_POST['id'])) ? intval($_POST['id']) : intval($_GET['id']);
	$act = (isset($_POST['act'])) ? intval($_POST['act']) : intval($_GET['act']);
	$pagetitle = $conf['defis']." "._ADMINMENU;
	switch($op) {
		case "panel":
		panel();
		break;
		
		case "admin":
		admin();
		break;
		
		case "logout":
		logout();
		break;
		
		default:
		#if (is_admin_god()) {
			$dir = opendir("admin/modules");
			while ($file = readdir($dir)) {
				if (preg_match("/(\.php)$/is", $file) && $file != "." && $file != ".." /**/&& is_admin_god($file)/**/) include("admin/modules/".$file);
			}
			closedir($dir);
		#}
		$result = $db->sql_query("SELECT title FROM ".$prefix."_modules ORDER BY title ASC");
		while (list($mtitle) = $db->sql_fetchrow($result)) {
			if (is_admin_god() || is_admin_modul($mtitle)) {
				if (file_exists("modules/".$mtitle."/admin/index.php") && file_exists("modules/".$mtitle."/admin/links.php")) include("modules/".$mtitle."/admin/index.php");
			}
		}
		break;
	}
} else {
	$home = 1;
	$op = analyze($_POST['op']);
	switch($op) {
		default:
		login();
		break;
		
		case "add_admin":
		add_admin();
		break;
		
		case "check_admin";
		check_admin();
		break;
	}
}
?>