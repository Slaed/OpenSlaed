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

if (!defined("FUNC_FILE")) die("Illegal File Access");

include("config/config_moders.php");

# Global config file include
include("config/config_global.php");

# Users config file include
include("config/config_users.php");

# Comments config file include
include("config/config_comments.php");

# Old function include
if ($conf['old'] == 1) include("function/old_filters.php");

# Murder variables
unset($name, $file, $admin, $user, $admintrue, $godtrue, $usertrue, $aid, $uname, $guest, $userinfo, $stop);

# SQL config file include
include("config/config.php");

# Language on
get_lang();

# SQL class file include
include("function/db.php");

# Security config file include
include("config/config_security.php");

# Notice reporting
# error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

# Error reporting
if ($confs['error']) {
	error_reporting(E_ALL ^ E_NOTICE);
} else {
	error_reporting(0);
}

# GZip
if (strstr($_SERVER['HTTP_USER_AGENT'], 'compatible') || strstr($_SERVER['HTTP_USER_AGENT'], 'Gecko')) {
	if (extension_loaded('zlib')) ob_start('ob_gzhandler');
} else {
	if (strstr($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip')) {
		if (extension_loaded('zlib')) {
			$do_gzip_compress = true;
			ob_start();
			ob_implicit_flush(0);
			header('Content-Encoding: gzip');
		}
	}
}

# Security magic quotes gpc
if (!get_magic_quotes_gpc()) {
	function add_slashes($value) {
		if (is_array($value)) {
			$value = array_map("add_slashes", $value);
		} elseif (!empty($value) && is_string($value)) {
			$value = addslashes($value);
		}
		return $value;
	}
	$_GET = add_slashes($_GET);
	$_POST = add_slashes($_POST);
	$_COOKIE = add_slashes($_COOKIE);
	$_REQUEST = add_slashes($_REQUEST);
}

# Output buffering on
ob_start();

# Session start
session_start();

# Format admin variable
$admin = (isset($_SESSION[$conf['admin_c']])) ? explode(":", addslashes(base64_decode($_SESSION[$conf['admin_c']]))) : false;

# Format user variable
$user = (isset($_COOKIE[$conf['user_c']])) ? explode(":", addslashes(base64_decode($_COOKIE[$conf['user_c']]))) : false;

# Analyzer of variables
function variable() {
	if ($_GET) {
		$cont = array();
		foreach ($_GET as $var_name => $var_value) $cont[] = $var_name."=".$var_value;
		$content = "GET: ".implode(", ", $cont)."\n";
	}
	if ($_POST) {
		$cont = array();
		foreach ($_POST as $var_name => $var_value) {
			$var_value = is_array($var_value) ? fields_save($var_value) : $var_value;
			$var_value = str_replace(array("[", "]"), array("&#091;", "&#093;"), htmlspecialchars($var_value));
			$cont[] = $var_name."=".$var_value;
		}
		$content .= "POST: ".implode(", ", $cont)."\n";
	}
	if ($_COOKIE) {
		$cont = array();
		foreach ($_COOKIE as $var_name => $var_value) $cont[] = $var_name."=".$var_value;
		$content .= "COOKIE: ".implode(", ", $cont)."\n";
	}
	if ($_FILES) {
		$cont = array();
		foreach ($_FILES as $var_name => $var_value) $cont[] = $var_name."=".$var_value;
		$content .= "FILES: ".implode(", ", $cont)."\n";
	}
	if ($_SESSION) {
		$cont = array();
		foreach ($_SESSION as $var_name => $var_value) $cont[] = $var_name."=".$var_value;
		$content .= "SESSION: ".implode(", ", $cont)."\n";
	}
	/*
	if ($_SERVER) {
		$cont = array();
		foreach ($_SERVER as $var_name => $var_value) $cont[] = $var_name."=".$var_value;
		$content .= "SERVER: ".implode(", ", $cont)."\n";
	}
	*/
	return $content;
}

# Log report
function log_report() {
	global $user, $confu, $confs;
	$ip = getip();
	$agent = getagent();
	$url = text_filter(getenv("REQUEST_URI"));
	$luser = ($user) ? substr($user[1], 0, 25) : substr($confu['anonym'], 0, 25);
	$path = "config/logs/log.txt";
	if ($fhandle = @fopen($path, "ab")) {
		if (filesize($path) > $confs['log_size']) {
			zip_compress($path, "config/logs/log_".date("Y-m-d_H-i").".txt");
			@unlink($path);
		}
		fwrite($fhandle, variable()._IP.": ".$ip."\n"._USER.": ".$luser."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._DATE.": ".date("d.m.Y - H:i:s")."\n---\n");
		fclose($fhandle);
	}
}

if ($confs['log']) log_report();

# Security cookies blocker or ip blocker and member blocker
$bcookie = (isset($_COOKIE[$confs['blocker_cookie']])) ? $_COOKIE[$confs['blocker_cookie']] : "";
if ($bcookie == "block") {
	get_exit(_BANN_INFO, 0);
} else {
	$bip = explode("||", $confs['blocker_ip']);
	if ($bip) {
		foreach ($bip as $val) {
			if ($val != "") {
				$binfo = explode("|", $val);
				if (time() <= $binfo[3]) {
					$ipt = getip();
					$ipb = $binfo[0];
					$uagt = md5(getagent());
					if ($binfo[1] <= 3) {
						$ipt = substr($ipt, 0, strrpos($ipt, '.'));
						$ipb = substr($ipb, 0, strrpos($ipb, '.'));
					}
					if ($binfo[1] <= 2) {
						$ipt = substr($ipt, 0, strrpos($ipt, '.'));
						$ipb = substr($ipb, 0, strrpos($ipb, '.'));
					}
					if ($binfo[1] == 1) {
						$ipt = substr($ipt, 0, strrpos($ipt, '.'));
						$ipb = substr($ipb, 0, strrpos($ipb, '.'));
					}
					if ((!$binfo[2] && $ipt == $ipb) || ($binfo[2] && $ipt == $ipb && $uagt == $binfo[2])) {
						setcookie($confs['blocker_cookie'], "block", $binfo[3]);
						$btext = _BANN_INFO."<br>"._BANN_TERM.": ".rest_time($binfo[3], 1)."<br>"._BANN_REAS.": ".$binfo[4];
						get_exit($btext, 0);
					}
				}
			}
		}
	}
	$bus = explode("||", $confs['blocker_user']);
	if ($bus) {
		foreach ($bus as $val) {
			if ($val != "") {
				$tus = substr($user[1], 0, 25);
				$uinfo = explode("|", $val);
				if (time() <= $uinfo[1]) {
					if ($tus == $uinfo[0]) {
						setcookie($confs['blocker_cookie'], "block", $uinfo[1]);
						$utext = _BANN_INFO."<br>"._BANN_TERM.": ".rest_time($uinfo[1], 1)."<br>"._BANN_REAS.": ".$uinfo[2];
						get_exit($utext, 0);
					}
				}
			}
		}
	}
}

# Error reporting log
if ($confs['error_log']) {
	if (isset($_GET['error'])) {
		$error = intval($_GET['error']);
		unset($error_log, $http);
		static $http = array (
			100 => "HTTP/1.1 100 Continue",
			101 => "HTTP/1.1 101 Switching Protocols",
			200 => "HTTP/1.1 200 OK",
			201 => "HTTP/1.1 201 Created",
			202 => "HTTP/1.1 202 Accepted",
			203 => "HTTP/1.1 203 Non-Authoritative Information",
			204 => "HTTP/1.1 204 No Content",
			205 => "HTTP/1.1 205 Reset Content",
			206 => "HTTP/1.1 206 Partial Content",
			300 => "HTTP/1.1 300 Multiple Choices",
			301 => "HTTP/1.1 301 Moved Permanently",
			302 => "HTTP/1.1 302 Found",
			303 => "HTTP/1.1 303 See Other",
			304 => "HTTP/1.1 304 Not Modified",
			305 => "HTTP/1.1 305 Use Proxy",
			307 => "HTTP/1.1 307 Temporary Redirect",
			400 => "HTTP/1.1 400 Bad Request",
			401 => "HTTP/1.1 401 Unauthorized",
			402 => "HTTP/1.1 402 Payment Required",
			403 => "HTTP/1.1 403 Forbidden",
			404 => "HTTP/1.1 404 Not Found",
			405 => "HTTP/1.1 405 Method Not Allowed",
			406 => "HTTP/1.1 406 Not Acceptable",
			407 => "HTTP/1.1 407 Proxy Authentication Required",
			408 => "HTTP/1.1 408 Request Time-out",
			409 => "HTTP/1.1 409 Conflict",
			410 => "HTTP/1.1 410 Gone",
			411 => "HTTP/1.1 411 Length Required",
			412 => "HTTP/1.1 412 Precondition Failed",
			413 => "HTTP/1.1 413 Request Entity Too Large",
			414 => "HTTP/1.1 414 Request-URI Too Large",
			415 => "HTTP/1.1 415 Unsupported Media Type",
			416 => "HTTP/1.1 416 Requested range not satisfiable",
			417 => "HTTP/1.1 417 Expectation Failed",
			500 => "HTTP/1.1 500 Internal Server Error",
			501 => "HTTP/1.1 501 Not Implemented",
			502 => "HTTP/1.1 502 Bad Gateway",
			503 => "HTTP/1.1 503 Service Unavailable",
			504 => "HTTP/1.1 504 Gateway Time-out"
		);
		$error_log = $http[$error];
		if ($error_log) {
			$ip = getip();
			$agent = getagent();
			$url = text_filter(getenv("REQUEST_URI"));
			$path = "config/logs/error_site.txt";
			if ($fhandle = @fopen($path, "ab")) {
				if (filesize($path) > $confs['log_size']) {
					zip_compress($path, "config/logs/error_site_".date("Y-m-d_H-i").".txt");
					@unlink($path);
				}
				fwrite($fhandle, _ERROR.": ".$error_log."\n"._IP.": ".$ip."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._DATE.": ".date("d.m.Y - H:i:s")."\n---\n");
				fclose($fhandle);
			}
		}
		unset($error_log, $http);
	}
	function error_reporting_log($error_num, $error_var, $error_file, $error_line) {
		global $confs;
		$error_write = false;
		switch ($error_num) {
			case 1:
			$error_desc = "ERROR";
			$error_write = true;
			break;
			case 2:
			$error_desc = "WARNING";
			$error_write = true;
			break;
			case 4:
			$error_desc = "PARSE";
			$error_write = true;
			break;
			case 8:
			$error_desc = "NOTICE";
			$error_write = false;
			break;
		}
		if ($error_write) {
			global $conf;
			$ip = getip();
			$agent = getagent();
			$url = text_filter(getenv("REQUEST_URI"));
			$path = "config/logs/error.txt";
			if ($fhandle = @fopen($path, "ab")) {
				if (filesize($path) > $confs['log_size']) {
					zip_compress($path, "config/logs/error_".date("Y-m-d_H-i").".txt");
					@unlink($path);
				}
				fwrite($fhandle, _ERROR.": ".$error_desc.": ".$error_var." Line: ".$error_line." in file ".$error_file."\n"._IP.": ".$ip."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._DATE.": ".date("d.m.Y - H:i:s")."\n---\n");
				fclose($fhandle);
			}
		}
	}
	set_error_handler('error_reporting_log');
}

# Security GET, POST, COOKIE, FILES
if (!is_admin_god()) {
	function input_parse($value) {
		if (is_array($value)) {
			$value = array_map("input_parse", $value);
		} elseif (!empty($value) && is_string($value)) {
			$in = array("#javascript:#si", "#vbscript:#si", "#script:#si", "#about:#si", "#applet:#si", "#activex:#si", "#chrome:#si");
			$out = array("Java Script", "VB Script", "Script", "About", "Applet", "ActiveX", "Chrome");
			$value = preg_replace($in, $out, $value);
		}
		return $value;
	}
	$_POST = input_parse($_POST);

	foreach ($_GET as $var_name=>$var_value) {
		if ($confs['url_get']) if (preg_match("#^(http\:\/\/|ftp\:\/\/|\/\/|https:\/\/|php:\/\/|\/\/)#i", $var_value)) warn_report("URL in GET - ".$var_name." = ". $var_value);
		if (preg_match("#<.*?(script|body|object|iframe|applet|meta|form|style|img).*?>#i", urldecode($var_value)) || preg_match("#\([^>]*\"?[^)]*\)#", $var_value)) warn_report("HTML in GET - ".$var_name." = ". $var_value);
		if (preg_match("#\"|\'|\.\.\/|\*#", $var_value)) hack_report("Hack in GET - ".$var_name." = ". $var_value);
		$security_string = "#ALTER|DROP|INSERT|OUTFILE|SELECT|TRUNCATE|UNION|".$prefix."_admins|".$prefix."_users|admins_show|admins_add|admins_save|admins_del#i";
		$security_decode = base64_decode($var_value);
		if (preg_match($security_string, $security_decode)) hack_report("XSS base64 in GET - ".$var_name." = ". $var_value);
		if (preg_match($security_string, $var_value)) hack_report("XSS in GET - ".$var_name." = ". $var_value);
		$security_slash = preg_replace("#\/\*.*?\*\/#", "", $var_value);
		if (preg_match($security_string, $security_slash)) hack_report("XSS in GET - ".$var_name." = ". $var_value);
	}

	foreach ($_POST as $var_name=>$var_value) {
		$var_value = is_array($var_value) ? fields_save($var_value) : $var_value;
		if ($confs['url_post']) if (preg_match("#^(http\:\/\/|ftp\:\/\/|\/\/|https:\/\/|php:\/\/|\/\/)#i", $var_value)) warn_report("URL in POST - ".$var_name." = ". $var_value);
		$editor = intval(substr($admin[3], 0, 1));
		if (((defined("ADMIN_FILE") && $editor != 1) || (!defined("ADMIN_FILE") && $conf['redaktor'] != 1)) && preg_match("#<.*?(script|body|object|iframe|applet|meta|form).*?>#i", urldecode($var_value))) warn_report("HTML in POST - ".$var_name." = ". $var_value);
		$security_string = "#".$prefix."_admins|".$prefix."_users#i";
		$security_decode = base64_decode($var_value);
		if (preg_match($security_string, $security_decode)) hack_report("XSS base64 in POST - ".$var_name." = ". $var_value);
		if (preg_match($security_string, $var_value)) hack_report("XSS in POST - ".$var_name." = ". $var_value);
		$security_slash = preg_replace("#\/\*.*?\*\/#", "", $var_value);
		if (preg_match($security_string, $security_slash)) hack_report("XSS in POST - ".$var_name." = ". $var_value);
	}
}

foreach ($_COOKIE as $var_name=>$var_value) {
	if (preg_match("#<.*?(script|body|object|iframe|applet|meta|form|style|img).*?>#i", $var_value)) hack_report("HTML in COOKIE - ".$var_name." = ". $var_value);
	if (preg_match("#^(http\:\/\/|ftp\:\/\/|\/\/|https:\/\/|php:\/\/|\/\/)#i", $var_value)) hack_report("URL in COOKIE - ".$var_name." = ". $var_value);
	$security_string = "#ALTER|DROP|INSERT|OUTFILE|SELECT|TRUNCATE|UNION|".$prefix."_admins|".$prefix."_users|admins_show|admins_add|admins_save|admins_del#i";
	$security_decode = base64_decode($var_value);
	if (preg_match($security_string, $security_decode)) hack_report("XSS base64 in COOKIE - ".$var_name." = ". $var_value);
	if (preg_match($security_string, $var_value)) hack_report("XSS in COOKIE - ".$var_name." = ". $var_value);
	$security_slash = preg_replace("#\/\*.*?\*\/#", "", $var_value);
	if (preg_match($security_string, $security_slash)) hack_report("XSS in COOKIE - ".$var_name." = ". $var_value);
}

foreach ($_FILES as $var_name=>$var_value) {
	$var_value = end(explode(".", $_FILES['userfile']['name']));
	if (preg_match("#php.*|js|htm|html|phtml|cgi|pl|perl|asp#i", $var_value)) hack_report("Hack in FILES - ".$var_name." = ". $var_value);
	$var_value = end(explode(".", $_FILES['Filedata']['name']));
	if (preg_match("#php.*|js|htm|html|phtml|cgi|pl|perl|asp#i", $var_value)) hack_report("Hack in FILES - ".$var_name." = ". $var_value);
}

# Reset all variables
reset($_GET);
reset($_POST);
reset($_COOKIE);
reset($_FILES);

# Check super admin
function is_admin_god($in='0') {
	global $prefix, $db, $admin, $admods;
	static $godtrue;
	if (isset($admin)) {
	$name = htmlspecialchars(substr($admin[1], 0, 25));if (is_admin() && $in!='0' && is_array($admods['access'][$name]) && in_array($in,$admods['access'][$name])) return 1;
		if (!isset($godtrue)) {
			$id = intval(substr($admin[0], 0, 11));
			$name = htmlspecialchars(substr($admin[1], 0, 25));
			$pwd = htmlspecialchars(substr($admin[2], 0, 40));
			$ip = getip();
			if ($id && $name && $pwd && $ip) {
				list($aname, $apwd, $aip) = $db->sql_fetchrow($db->sql_query("SELECT name, pwd, ip FROM ".$prefix."_admins WHERE id='$id' AND super='1'"));
				if ($aname == $name && $aname != "" && $apwd == $pwd && $apwd != "" && $aip == $ip && $aip != "") {
					$godtrue = 1;
					return $godtrue;
				}
			}
			$godtrue = 0;
			return $godtrue;
		} else {
			return $godtrue;
		}
	} else {
		return 0;
	}
}

# Get IP
function getip() {
	if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
		$ip = getenv("REMOTE_ADDR");
	} elseif (!empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
		$ip = $_SERVER['REMOTE_ADDR'];
	} else {
		$ip = "0.0.0.0";
	}
	return $ip;
}

# Get user agent
function getagent() {
	if (getenv("HTTP_USER_AGENT") && strcasecmp(getenv("HTTP_USER_AGENT"), "unknown")) {
		$agent = text_filter(getenv("HTTP_USER_AGENT"));
	} elseif (!empty($_SERVER['HTTP_USER_AGENT']) && strcasecmp($_SERVER['HTTP_USER_AGENT'], "unknown")) {
		$agent = text_filter($_SERVER['HTTP_USER_AGENT']);
	} else {
		$agent = "unknown";
	}
	return $agent;
}

# Analyze a variable
function analyze($var) {
	$var = (preg_match("#[^a-zA-Z0-9_]#", $var)) ? "" : $var;
	return $var;
}

# Format language
function get_lang($module="") {
	global $currentlang, $conf;
	$rlang = (isset($_POST['newlang'])) ? ((isset($_POST['newlang'])) ? analyze($_POST['newlang']) : "") : ((isset($_GET['newlang'])) ? analyze($_GET['newlang']) : "");
	$clang = (isset($_COOKIE['lang'])) ? analyze($_COOKIE['lang']) : "";
	if ($rlang && $conf['multilingual'] == "1") {
		if (file_exists("language/lang-".$rlang.".php")) {
			setcookie("lang", $rlang, time() + intval($conf['user_c_t']));
			include_once("language/lang-".$rlang.".php");
			$currentlang = $rlang;
		} else {
			setcookie("lang", $conf['language'], time() + intval($conf['user_c_t']));
			include_once("language/lang-".$conf['language'].".php");
			$currentlang = $conf['language'];
		}
	} elseif ($clang && $conf['multilingual'] == "1") {
		if (file_exists("language/lang-".$clang.".php")) {
			include_once("language/lang-".$clang.".php");
			$currentlang = $clang;
		} else {
			include_once("language/lang-".$conf['language'].".php");
			$currentlang = $conf['language'];
		}
	} else {
		setcookie("lang", $conf['language'], time() + intval($conf['user_c_t']));
		include_once("language/lang-".$conf['language'].".php");
		$currentlang = $conf['language'];
	}
	if ($module != "") {
		if (file_exists("modules/$module/language/lang-".$currentlang.".php")) {
			if ($module == "admin") {
				include_once("admin/language/lang-".$currentlang.".php");
			} else {
				include_once("modules/$module/language/lang-".$currentlang.".php");
			}
		} else {
			if ($module == "admin") {
				include_once("admin/language/lang-".$currentlang.".php");
			} else {
				include_once("modules/$module/language/lang-".$conf['language'].".php");
			}
		}
	}
}

# Zip check
function zip_check() {
	if (function_exists('gzopen')) {
		return 2;
	} elseif (function_exists('bzopen')) {
		return 1;
	} else {
		return 0;
	}
}

# Zip compress
function zip_compress($src, $dst) {
	$check = zip_check();
	if ($check) {
		$fp = @fopen($src, "rb");
		$data = fread($fp, filesize($src));
		fclose($fp);
		if ($check == 2) {
			$zp = gzopen($dst.".gz", "wb5");
			gzwrite($zp, $data);
			gzclose($zp);
		} else {
			$zp = bzopen($dst.".bz2", "w");
			bzwrite($zp, $data);
			bzclose($zp);
		}
	}
}

# Format exit info
function get_exit($msg, $typ) {
	global $conf;
	$content = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n"
	."<html>\n"
	."<head>\n"
	."<meta http-equiv=\"content-type\" content=\"text/html; charset="._CHARSET."\">\n"
	."<title>".$conf['sitename']." ".urldecode($conf['defis'])." ".$conf['slogan']."</title>\n"
	."<meta name=\"author\" content=\"".$conf['sitename']."\">\n"
	."<meta name=\"generator\" content=\"ANTISLAED CMS ".$conf['version']." by ARTGLOBALS.COM\">\n";
	$content .= ($typ) ? "<meta http-equiv=\"refresh\" content=\"5; url=index.php\">\n" : "";
	$content .= "</head>\n"
	."<body>\n"
	."<div style=\"margin: 25% 25% 25% 25%;\">"
	."<div align=\"center\"><img src=\"images/logos/".$conf['site_logo']."\" alt=\"".$msg."\"></div>"
	."<div align=\"center\" style=\"margin-top: 50px; color: #2666B9; font-size: 14px; font-weight: bold; font-family: Verdana, Helvetica; text-align: center;\">".$msg."</div>"
	."</div>\n"
	."</body>\n"
	."</html>";
	die($content);
}
function prt($i){if(strpos($i,base64_decode('VGhlIHJlbGVhc2UgaXMgcHJlcGFyZWQgYnkgPGEgaHJlZj0iaHR0cDovL3d3
dy5hcnRnbG9iYWxzLmNvbSIgdGl0bGU9IkFSVEdMT0JBTFMuQ09NIiB0YXJn
ZXQ9ImluZGV4Ij5BUlRHTE9CQUxTLkNPTTwvYT4='))===false)$i=str_replace("</body>","<div style=\"display:none;\">".base64_decode('VGhlIHJlbGVhc2UgaXMgcHJlcGFyZWQgYnkgPGEgaHJlZj0iaHR0cDovL3d3
dy5hcnRnbG9iYWxzLmNvbSIgdGl0bGU9IkFSVEdMT0JBTFMuQ09NIiB0YXJn
ZXQ9ImluZGV4Ij5BUlRHTE9CQUxTLkNPTTwvYT4sIDxhIGhyZWY9Imh0dHA6
Ly9zdW52YXMuaW5mbyIgdGl0bGU9IlN1bnZhcyIgdGFyZ2V0PSJpbmRleCI+
U3VudmFzPC9hPiBhbmQgPGEgaHJlZj0iaHR0cDovL2FudGlzbGFlZGNtcy5y
dSIgdGl0bGU9IkFTIiB0YXJnZXQ9ImluZGV4Ij5BUzwvYT4=')."</div></body>", $i); return $i;}

# HTML and word filter
function text_filter($message, $type="") {
	global $conf;
	$message = is_array($message) ? fields_save($message) : $message;
	if (intval($type) == 2) {
		$message = htmlspecialchars(trim($message), ENT_QUOTES);
	} else {
		$message = strip_tags(urldecode($message));
		$message = htmlspecialchars(trim($message), ENT_QUOTES);
	}
	if ($conf['censor'] && intval($type != 1)) {
		$censor_l = explode(",", $conf['censor_l']);
		foreach ($censor_l as $val) $message = preg_replace("#$val#i", $conf['censor_r'], $message);
	}
	return $message;
}

# Length center filter
function cutstrc($linkstrip, $strip) {
	if (strlen($linkstrip) > $strip) $linkstrip = substr($linkstrip, 0, $strip - 19)."…".substr($linkstrip, -16);
	return $linkstrip;
}

# Format ed2k links
function ed2k_link($m) {
	$href = "url=".$m[2];
	$fname = rawurldecode($m[3]);
	$fname = preg_replace("#&amp;#i", "&", $fname);
	$size = files_size($m[4]);
	$cont = "eMule/eDonkey: [".$href."]".cutstrc($fname, 50)."[/url] - "._SIZE.": ".$size;
	return $cont;
}

# Make clickable url
function url_clickable($text) {
	if (!preg_match("#\[php\](.*)\[/php\]|\[code\](.*)\[/code\]#si", $text)) {
		$ret = " ".$text;
		$ret = preg_replace_callback("#([\n ])(?<=[^\w\"'])(ed2k://\|file\|([^\\/\|:<>\*\?\"]+?)\|(\d+?)\|([a-f0-9]{32})\|(.*?)/?)(?![\"'])(?=([,\.]*?[\s<\[])|[,\.]*?$)#i", "ed2k_link", $ret);
		$ret = preg_replace("#([\n ])(?<=[^\w\"'])(ed2k://\|server\|([\d\.]+?)\|(\d+?)\|/?)#i", "ed2k Server: [url=\\2]\\3[/url] - Port: \\4", $ret);
		$ret = preg_replace("#([\n ])(?<=[^\w\"'])(ed2k://\|friend\|([^\\/\|:<>\*\?\"]+?)\|([\d\.]+?)\|(\d+?)\|/?)#i", "Friend: [url=\\2]\\3[/url]", $ret);
		$ret = preg_replace("#([\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1[url=\\2]\\2[/url]", $ret);
		$ret = preg_replace("#([\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1[url=http://\\2]\\2[/url]", $ret);
		$ret = preg_replace("#([\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1[mail=\\2@\\3]\\2@\\3[/mail]", $ret);
		$ret = substr($ret, 1);
	} else {
		if (preg_match("#(.*)\[php\](.*)\[/php\](.*)#si", $text, $matches)) {
			$ret = url_clickable($matches[1])."[php]".$matches[2]."[/php]".url_clickable($matches[3]);
		} elseif (preg_match("#(.*)\[code\](.*)\[/code\](.*)#si", $text, $matches)) {
			$ret = url_clickable($matches[1])."[code]".$matches[2]."[/code]".url_clickable($matches[3]);
		}
	}
	return $ret;
}

# Save text
function save_text($text) {
	global $admin, $conf;
	if ($text) {
		$editor = intval(substr($admin[3], 0, 1));
		if ((defined("ADMIN_FILE") && $editor == 1) || (!defined("ADMIN_FILE") && $conf['redaktor'] == 1)) {
			$text = ($conf['clickable']) ? url_clickable($text) : $text;
			$out = nl2br(str_replace("\\", "&#092;", stripslashes(text_filter($text, 2))));
		} else {
			$out = str_replace(array("'", "\\"), array("&#039;", "&#092;"), stripslashes($text));
		}
		return $out;
	}
}

# Fields save
function fields_save($field) {
	if ($field) {
		$fields = stripslashes(text_filter(implode("|", $field), 2));
		return $fields;
	}
}

# Rest time
function rest_time($time, $id) {
	if (time() < $time) {
		$enddate = date("d.m.Y", $time);
		$day = round(date("d", $time));
		$month = round(date("m", $time));
		$year = round(date("Y", $time));
		$date = (int)((mktime(0, 0, 0, $month, $day, $year) - time(void)) / 86400)." "._DAYS." - $enddate";
	} else {
		$date = "<font class=\"option\">".date("d.m.Y", $time)." - "._END."</font>";
	}
	return $date;
}

# Mail send
function mail_send($email, $smail, $subject, $message, $id="", $pr="") {
	global $conf;
	$email = text_filter($email);
	$smail = text_filter($smail);
	$subject = text_filter($subject);
	$id = intval($id);
	$pr = (!$pr) ? "3" : intval($pr);
	$message = (!$id) ? $message : $message."<br><br>"._IP.": ".getip()."<br>"._BROWSER.": ".getagent()."<br>"._HASH.": ".md5(getagent());
	$mheader = "MIME-Version: 1.0\n"
	."Content-Type: text/html; charset="._CHARSET."\n"
	."Content-Transfer-Encoding: 8bit\n"
	."Reply-To: \"$smail\" <$smail>\n"
	."From: \"$smail\" <$smail>\n"
	."Return-Path: <$smail>\n"
	."X-Priority: $pr\n"
	."X-Mailer: ANTISLAED CMS ".$conf['version']." and ARTGLOBALS Mailer\n";
	mail($email, $subject, $message, $mheader);
}

# Hack report
function hack_report($msg) {
	global $user, $conf, $confu, $confs;
	$msg = text_filter(substr($msg, 0, 500));
	$url = text_filter(getenv("REQUEST_URI"));
	$ip = getip();
	$agent = getagent();
	$date_time = date("d.m.Y - H:i:s");
	$user = ($user) ? substr($user[1], 0, 25) : substr($confu['anonym'], 0, 25);
	if ($confs['block']) {
		$btime = time() + 86400;
		$content = file_get_contents("config/config_security.php");
		$content = str_replace("\$confs['blocker_ip'] = \"".$confs['blocker_ip']."\";", "\$confs['blocker_ip'] = \"".$confs['blocker_ip'].$ip."|4|".md5($agent)."|".$btime."|"._HACK."||\";", $content);
		$fp = @fopen("config/config_security.php", "wb");
		fwrite($fp, $content);
		fclose($fp);
		setcookie($confs['blocker_cookie'], "block", $btime);
	}
	if ($confs['mail']) {
		$subject = $conf['sitename']." - "._SECURITY;
		$mmsg = $conf['sitename']." - "._SECURITY."<br><br>"._HACK.": ".$msg."<br>"._IP.": ".$ip."<br>"._USER.": ".$user."<br>"._URL.": ".$url."<br>"._BROWSER.": ".$agent."<br>"._DATE.": ".$date_time;
		mail_send($conf['adminmail'], $conf['adminmail'], $subject, $mmsg, 0, 1);
	}
	if ($confs['write_h']) {
		$path = "config/logs/hack.txt";
		if ($fhandle = @fopen($path, "ab")) {
			if (filesize($path) > $confs['log_size']) {
				zip_compress($path, "config/logs/hack_".date("Y-m-d_H-i").".txt");
				@unlink($path);
			}
			fwrite($fhandle, _HACK.": ".$msg."\n"._IP.": ".$ip."\n"._USER.": ".$user."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._DATE.": ".$date_time."\n---\n");
			fclose($fhandle);
		}
	}
	setcookie($conf['user_c'], false);
	get_exit(_HACK."!", 1);
}

# Warn report
function warn_report($msg) {
	global $user, $conf, $confu, $confs;
	$msg = text_filter(substr($msg, 0, 500));
	$url = text_filter(getenv("REQUEST_URI"));
	$ip = getip();
	$agent = getagent();
	$date_time = date("d.m.Y - H:i:s");
	$user = ($user) ? substr($user[1], 0, 25) : substr($confu['anonym'], 0, 25);
	if ($confs['mail_w']) {
		$subject = $conf['sitename']." - "._SECURITY;
		$mmsg = $conf['sitename']." - "._SECURITY."<br><br>"._WARN.": ".$msg."<br>"._IP.": ".$ip."<br>"._USER.": ".$user."<br>"._URL.": ".$url."<br>"._BROWSER.": ".$agent."<br>"._DATE.": ".$date_time;
		mail_send($conf['adminmail'], $conf['adminmail'], $subject, $mmsg, 0, 1);
	}
	if ($confs['write_w']) {
		$path = "config/logs/warn.txt";
		if ($fhandle = @fopen($path, "ab")) {
			if (filesize($path) > $confs['log_size']) {
				zip_compress($path, "config/logs/warn_".date("Y-m-d_H-i").".txt");
				@unlink($path);
			}
			fwrite($fhandle, _WARN.": ".$msg."\n"._IP.": ".$ip."\n"._USER.": ".$user."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._DATE.": ".$date_time."\n---\n");
			fclose($fhandle);
		}
	}
	get_exit(_WARN."!", 1);
}
?>