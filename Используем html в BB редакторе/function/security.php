<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("FUNC_FILE")) die("Illegal File Access");

# Global config file include
include("config/config_global.php");

# Users config file include
include("config/config_users.php");

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
			Header('Content-Encoding: gzip');
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

# Security cookies blocker or ip blocker and member blocker
$bcookie = (isset($_COOKIE[$confs['blocker_cookie']])) ? $_COOKIE[$confs['blocker_cookie']] : "";
if ($bcookie == "block") {
	get_exit(""._BANN_INFO."", 0);
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
						$btext = ""._BANN_INFO."<br>"._BANN_TERM.": ".rest_time($binfo[3], 1)."<br>"._BANN_REAS.": ".$binfo[4]."";
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
				$tus = explode(":", addslashes(base64_decode($_COOKIE[$conf['user_c']])));
				$tus = substr("".$tus[1]."", 0, 25);
				$uinfo = explode("|", $val);
				if (time() <= $uinfo[1]) {
					if ($tus == $uinfo[0]) {
						setcookie($confs['blocker_cookie'], "block", $uinfo[1]);
						$utext = ""._BANN_INFO."<br>"._BANN_TERM.": ".rest_time($uinfo[1], 1)."<br>"._BANN_REAS.": ".$uinfo[2]."";
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
			$url = htmlspecialchars(trim(getenv("REQUEST_URI")), ENT_QUOTES);
			if ($fhandle = @fopen("config/logs/error_site.txt", "a")) {
				if (filesize("config/logs/error_site.txt") > 1048576) unlink("config/logs/error_site.txt");
				fwrite($fhandle, ""._ERROR.": ".$error_log."\n"._IP.": ".$ip."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._STARTDATE.": ".date("d.m.y - H:i:s")."\n---\n");
				fclose($fhandle);
			}
		}
		unset($error_log, $http);
	}
	function error_reporting_log($error_num, $error_var, $error_file, $error_line) {
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
			$url = htmlspecialchars(trim(getenv("REQUEST_URI")), ENT_QUOTES);
			if ($fhandle = @fopen("config/logs/error.txt", "a")) {
				if (filesize("config/logs/error.txt") > 1048576) unlink("config/logs/error.txt");
				fwrite($fhandle, ""._ERROR.": ".$error_desc.": ".$error_var." Line: ".$error_line." in file ".$error_file."\n"._IP.": ".$ip."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._STARTDATE.": ".date("d.m.y - H:i:s")."\n---\n");
				fclose($fhandle);
			}
		}
	}
	set_error_handler('error_reporting_log');
}

# Security GET, POST, COOKIE, FILES
if (!is_admin_god()) {
	foreach ($_GET as $var_name=>$var_value) {
		if (preg_match("#<.*?(script|body|object|iframe|applet|meta|style|form|img|onmouseover).*?>#i", urldecode($var_value)) || preg_match("#\([^>]*\"?[^)]*\)#", $var_value)) warn_report("HTML in GET - ".$var_name." = ". $var_value."");
		if ($confs['url_get']) if (preg_match("#^(http\:\/\/|ftp\:\/\/|\/\/|https:\/\/|php:\/\/|\/\/)#i", $var_value)) warn_report("URL in GET - ".$var_name." = ". $var_value);
		if (preg_match("#\"|\'|\.\.\/|\*#", $var_value)) hack_report("Hack in GET - ".$var_name." = ". $var_value."");
		$security_string = "#UNION|OUTFILE|SELECT|ALTER|INSERT|DROP|TRUNCATE|".$prefix."_admins|".$prefix."_users|admins_show|admins_add|admins_save|admins_del#i";
		$security_decode = base64_decode($var_value);
		if (preg_match($security_string, $security_decode)) hack_report("XSS base64 in GET - ".$var_name." = ". $var_value."");
		if (preg_match($security_string, $var_value)) hack_report("XSS in GET - ".$var_name." = ". $var_value."");
		$security_slash = preg_replace("#\/\*.*?\*\/#", "", $var_value);
		if (preg_match($security_string, $security_slash)) hack_report("XSS in GET - ".$var_name." = ". $var_value."");
	}
	foreach ($_POST as $var_name=>$var_value) {
		$var_value = is_array($var_value) ? fields_save($var_value) : $var_value;
		if (preg_match("#<.*?(script|body|object|iframe|applet|meta|style|form|onmouseover).*?>#i", urldecode($var_value))) warn_report("HTML in POST - ".$var_name." = ". $var_value."");
		if ($confs['url_post']) if (preg_match("#^(http\:\/\/|ftp\:\/\/|\/\/|https:\/\/|php:\/\/|\/\/)#i", $var_value)) warn_report("URL in POST - ".$var_name." = ". $var_value);
		$security_string = "#UNION|OUTFILE|SELECT|ALTER|INSERT|DROP|TRUNCATE|".$prefix."_admins|".$prefix."_users|admins_show|admins_add|admins_save|admins_del#i";
		$security_decode = base64_decode($var_value);
		if (preg_match($security_string, $security_decode)) hack_report("XSS base64 in POST - ".$var_name." = ". $var_value."");
		if (preg_match($security_string, $var_value)) hack_report("XSS in POST - ".$var_name." = ". $var_value."");
		$security_slash = preg_replace("#\/\*.*?\*\/#", "", $var_value);
		if (preg_match($security_string, $security_slash)) hack_report("XSS in POST - ".$var_name." = ". $var_value."");
	}
}

foreach ($_COOKIE as $var_name=>$var_value) {
	if (preg_match("#<.*?(script|body|object|iframe|applet|meta|style|form|img|onmouseover).*?>#i", $var_value)) hack_report("HTML in COOKIE - ".$var_name." = ". $var_value."");
	if (preg_match("#^(http\:\/\/|ftp\:\/\/|\/\/|https:\/\/|php:\/\/|\/\/)#i", $var_value)) hack_report("URL in COOKIE - ".$var_name." = ". $var_value);
	$security_string = "#UNION|OUTFILE|SELECT|ALTER|INSERT|DROP|TRUNCATE|FROM|WHERE|UPDATE|".$prefix."_admins|".$prefix."_users|admins_show|admins_add|admins_save|admins_del#i";
	$security_decode = base64_decode($var_value);
	if (preg_match($security_string, $security_decode)) hack_report("XSS base64 in COOKIE - ".$var_name." = ". $var_value."");
	if (preg_match($security_string, $var_value)) hack_report("XSS in COOKIE - ".$var_name." = ". $var_value."");
	$security_slash = preg_replace("#\/\*.*?\*\/#", "", $var_value);
	if (preg_match($security_string, $security_slash)) hack_report("XSS in COOKIE - ".$var_name." = ". $var_value."");
}

foreach ($_FILES as $var_name=>$var_value) {
	$var_value = end(explode(".", $_FILES['userfile']['name']));
	if (preg_match("#php.*|js|htm|html|phtml|cgi|pl|perl|asp#i", $var_value)) hack_report("Hack in FILES - ".$var_name." = ". $var_value."");
	$var_value = end(explode(".", $_FILES['Filedata']['name']));
	if (preg_match("#php.*|js|htm|html|phtml|cgi|pl|perl|asp#i", $var_value)) hack_report("Hack in FILES - ".$var_name." = ". $var_value."");
}

reset($_GET);
reset($_POST);
reset($_COOKIE);
reset($_FILES);

# Check super admin
function is_admin_god() {
	global $prefix, $db, $conf;
	static $godtrue;
	$admin = (isset($_COOKIE[$conf['admin_c']])) ? explode(":", addslashes(base64_decode($_COOKIE[$conf['admin_c']]))) : false;
	if (isset($admin)) {
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
		$ip = "unknown";
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

# Format exit info
function get_exit($msg, $typ) {
	global $conf;
	$content = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n"
	."<html>\n"
	."<head>\n"
	."<meta http-equiv=\"content-type\" content=\"text/html; charset="._CHARSET."\">\n"
	."<title>".$conf['sitename']." ".urldecode($conf['defis'])." ".$conf['slogan']."</title>\n"
	."<meta name=\"author\" content=\"".$conf['sitename']."\">\n"
	."<meta name=\"copyright\" content=\"Copyright (c) Open SLAED ".$conf['version']."\">\n";
	$content .= ($typ) ? "<meta http-equiv=\"refresh\" content=\"5; url=index.php\">\n" : "";
	$content .= "</head>\n"
	."<body>\n"
	."<div style=\"margin: 25% 25% 25% 25%;\">"
	."<div align=\"center\"><img src=\"images/logos/".$conf['site_logo']."\" alt=\"".$msg."\"></div>"
	."<div align=\"center\" style=\"margin-top: 50px; color: #3CA20E; font-size: 14px; font-weight: bold; font-family: Verdana, Helvetica; text-align: center;\">".$msg."</div>"
	."</div>\n"
	."</body>\n"
	."</html>";
	die($content);
}

# HTML and word filter
function text_filter($message, $type="") {
	global $conf;
	if (!is_admin()) $message=preg_replace('#\[(usehtml|/usehtml)\]#si','',$message);
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

# Save text
function save_text($text) {
	global $conf;
	if ($text) {
		$out = (defined("ADMIN_FILE") && $conf['redaktor'] != 1) ? str_replace(array("'", "\\"), array("&#039;", "&#092;"), stripslashes($text)) : nl2br(str_replace("\\", "&#092;", stripslashes(text_filter($text, 2))));
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
		$date = "".(int)((mktime(0, 0, 0, $month, $day, $year) - time(void)) / 86400)." "._DAYS." - $enddate";
	} else {
		$date = "<font class=\"option\">".date("d.m.Y", $time)." - "._END."</font>";
	}
	return $date;
}

# Hack report
function hack_report($msg) {
	global $conf, $confu, $confs;
	$msg = htmlspecialchars(trim(substr($msg, 0, 500)), ENT_QUOTES);
	$url = htmlspecialchars(trim(getenv("REQUEST_URI")), ENT_QUOTES);
	$ip = getip();
	$agent = getagent();
	$date_time = date("d.m.y - H:i:s");
	if (isset($_COOKIE[$conf['user_c']])) {
		$user = $_COOKIE[$conf['user_c']];
		$user = explode(":", addslashes(base64_decode($user)));
		$user = substr("".$user[1]."", 0, 25);
		$user_block = "".$user.",";
	} else {
		$user = substr($confu['anonym'], 0, 25);
	}
	if ($confs['block']) {
		$btime = time() + 86400;
		$content = file_get_contents("config/config_security.php");
		$content = str_replace("\$confs['blocker_ip'] = \"".$confs['blocker_ip']."\";", "\$confs['blocker_ip'] = \"".$confs['blocker_ip']."".$ip."|4|".md5($agent)."|".$btime."|"._HACK."||\";", $content);
		$fp = @fopen("config/config_security.php", "wb");
		fwrite($fp, $content);
		fclose($fp);
		setcookie($confs['blocker_cookie'], "block", $btime);
	}
	if ($confs['mail']) {
		$subject = "".$conf['sitename']." - "._SECURITY."";
		$msg = "".$conf['sitename']." - "._SECURITY."\n\n"
		.""._HACK.": ".$msg."\n"._IP.": ".$ip."\n"._USER.": ".$user."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._STARTDATE.": ".$date_time."\n";
		$mheader = "MIME-Version: 1.0\n"
		."Content-Type: text/plain; charset="._CHARSET."\n"
		."Content-Transfer-Encoding: 8bit\n"
		."Reply-To: \"".$conf['adminmail']."\" <".$conf['adminmail'].">\n"
		."From: \"".$conf['adminmail']."\" <".$conf['adminmail'].">\n"
		."Return-Path: <".$conf['adminmail'].">\n"
		."X-Priority: 1\n"
		."X-Mailer: Open SLAED Mailer\n";
		mail($conf['adminmail'], $subject, $msg, $mheader);
	}
	if ($confs['write_h']) {
		if ($fhandle = @fopen("config/logs/hack.txt", "a")) {
			if (filesize("config/logs/hack.txt") > 1048576) unlink("config/logs/hack.txt");
			fwrite($fhandle, ""._HACK.": ".$msg."\n"._IP.": ".$ip."\n"._USER.": ".$user."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._STARTDATE.": ".$date_time."\n---\n");
			fclose($fhandle);
		}
	}
	setcookie($conf['user_c'], false);
	get_exit(""._HACK."!", 1);
}

# Warn report
function warn_report($msg) {
	global $conf, $confu, $confs;
	$msg = htmlspecialchars(trim(substr($msg, 0, 500)), ENT_QUOTES);
	$url = htmlspecialchars(trim(getenv("REQUEST_URI")), ENT_QUOTES);
	$ip = getip();
	$agent = getagent();
	$date_time = date("d.m.y - H:i:s");
	if (isset($_COOKIE[$conf['user_c']])) {
		$user = $_COOKIE[$conf['user_c']];
		$user = explode(":", addslashes(base64_decode($user)));
		$user = substr("".$user[1]."", 0, 25);
	} else {
		$user = substr($confu['anonym'], 0, 25);
	}
	if ($confs['mail']) {
		$subject = "".$conf['sitename']." - "._SECURITY."";
		$msg = "".$conf['sitename']." - "._SECURITY."\n\n"
		.""._WARN.": ".$msg."\n"._IP.": ".$ip."\n"._USER.": ".$user."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._STARTDATE.": ".$date_time."\n";
		$mheader = "MIME-Version: 1.0\n"
		."Content-Type: text/plain; charset="._CHARSET."\n"
		."Content-Transfer-Encoding: 8bit\n"
		."Reply-To: \"".$conf['adminmail']."\" <".$conf['adminmail'].">\n"
		."From: \"".$conf['adminmail']."\" <".$conf['adminmail'].">\n"
		."Return-Path: <".$conf['adminmail'].">\n"
		."X-Priority: 1\n"
		."X-Mailer: Open SLAED Mailer\n";
		mail($conf['adminmail'], $subject, $msg, $mheader);
	}
	if ($confs['write_w']) {
		if ($fhandle = @fopen("config/logs/warn.txt", "a")) {
			if (filesize("config/logs/warn.txt") > 1048576) unlink("config/logs/warn.txt");
			fwrite($fhandle, ""._WARN.": ".$msg."\n"._IP.": ".$ip."\n"._USER.": ".$user."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._STARTDATE.": ".$date_time."\n---\n");
			fclose($fhandle);
		}
	}
	get_exit(""._WARN."!", 1);
}

# Format admin variable
$admin = (isset($_COOKIE[$conf['admin_c']])) ? explode(":", addslashes(base64_decode($_COOKIE[$conf['admin_c']]))) : false;

# Format user variable
$user = (isset($_COOKIE[$conf['user_c']])) ? explode(":", addslashes(base64_decode($_COOKIE[$conf['user_c']]))) : false;
?>