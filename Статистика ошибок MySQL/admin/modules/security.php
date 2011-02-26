<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

function security_navi() {
	global $admin_file;
	panel();
	open();
	echo "<h1>"._SECURITY."</h1>"
	."<h5>[ <a href=\"".$admin_file.".php?op=security_show\">"._HOME."</a>"
	." | <a href=\"".$admin_file.".php?op=security_block\">"._BANNED."</a>"
	." | <a href=\"".$admin_file.".php?op=security_pass\">"._SECURITY_PASS."</a>"
	." | <a href=\"".$admin_file.".php?op=security_conf\">"._PREFERENCES."</a> ]</h5>";
	close();
}

function security_show() {
	global $admin_file;
	head();
	security_navi();
	$permtest = end_chmod("config/logs", 777);
	if ($permtest) warning($permtest, "", "", 1);
	$content = "<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr>"
	."<th>"._TITLE."</th><th>"._SIZE."</th><th>"._DATE."</th><th>"._FUNCTIONS."</th></tr>";
	$i = 0;
	$handle = opendir("config/logs");
	while ($file = readdir($handle)) {
		if (preg_match("#hack\.txt|warn\.txt|error\.txt|error_site\.txt#", $file, $matches)) {
			$name = str_replace(".txt", "", $matches[0]);
			if ($matches[0] == "hack.txt") $title = ""._SECURITY_STAT_HACK."";
			if ($matches[0] == "warn.txt") $title = ""._SECURITY_STAT_WARN."";
			if ($matches[0] == "error.txt") $title = ""._SECURITY_STAT_ERROR_D."";
			if ($matches[0] == "error_site.txt") $title = ""._SECURITY_STAT_ERROR_S."";
			$content .= "<tr class=\"bgcolor1\">"
			."<td align=\"center\">".$title."</td>"
			."<td align=\"center\">".files_size(filesize("config/logs/".$file.""))."</td>"
			."<td align=\"center\">".date ("d.m.Y H:i:s", filemtime("config/logs/".$file.""))."</td>"
			."<td align=\"center\"><span id=\"s".$i."-title\"><img src=\"".img_find("all/about")."\" align=\"center\" title=\""._INFO."\" alt=\""._INFO."\"></span>"
			." <a href=\"".$admin_file.".php?op=security_down&".$name."=1\" title=\""._DOWN."\"><img src=\"".img_find("all/files")."\" border=\"0\" align=\"center\" alt=\""._DOWN."\"></a>"
			." ".ad_delete("".$admin_file.".php?op=security_del&".$name."=1", $title)."</td></tr>";
			$content2 .= "<div id=\"s".$i."\"><table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" class=\"blockline\"><tr><td align=\"center\"><textarea cols=\"125\" rows=\"20\">".file_get_contents("config/logs/".$file."")."</textarea></td></tr></table></div>"
			."<script language=\"JavaScript\" type=\"text/javascript\">var adms = new SwitchCont('s".$i."', '2');</script>";
			$i++;
		}
	}
	closedir($handle);
	$content .= "</table>";
	open();
	echo "".$content."".$content2."";
	close();
	foot();
}

function security_block() {
	global $admin_file, $conf;
	head();
	security_navi();
	include("config/config_security.php");
	$permtest = end_chmod("config/config_security.php", 666);
	if ($permtest) warning($permtest, "", "", 1);
	if (isset($_GET['send'])) warning(""._MAIL_SEND."", "", "", 2);
	open();
	echo "<h2>"._BANNED_IP."</h2>";
	$bip = explode("||", $confs['blocker_ip']);
	if ($confs['blocker_ip']) {
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr>"
		."<th>"._IP."</th><th>"._IP_MASK."</th><th>"._HASH."</th><th>"._DATE."</th><th>"._BANN_REAS."</th><th>"._FUNCTIONS."</th></tr>";
		foreach ($bip as $val) {
			if ($val != "") {
				$binfo = explode("|", $val);
				if ($binfo[1] == 4) $mask = "255.255.255.255";
				if ($binfo[1] == 3) $mask = "255.255.255.***";
				if ($binfo[1] == 2) $mask = "255.255.***.***";
				if ($binfo[1] == 1) $mask = "255.***.***.***";
				echo "<tr class=\"bgcolor1\">"
				."<td>".$binfo[0]."</td>"
				."<td align=\"center\">".$mask."</td>"
				."<td>".$binfo[2]."</td>"
				."<td align=\"center\">".rest_time($binfo[3], 1)."</td>"
				."<td>".$binfo[4]."</td>"
				."<td align=\"center\">".ad_delete("".$admin_file.".php?op=security_block_save&ip=".$binfo[0]."&ip_mask=".$binfo[1]."&hash=".$binfo[2]."&time=".$binfo[3]."&id=1", $binfo[0])."</td></tr>";
			}
		}
		echo "</table>";
	}
	$ip = (isset($_GET['new_ip'])) ? "".$_GET['new_ip']."" : "";
	echo "<form action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._IP.":</div><div class=\"center\"><input type=\"text\" name=\"ip\" value=\"".$ip."\" maxlength=\"255\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._IP_MASK.":</div><div class=\"center\"><select name=\"ip_mask\" class=\"admin\">"
	. "<option value=\"4\"";
	if ($ip_mask == 4) echo " selected";
	echo ">255.255.255.255</option>"
	."<option value=\"3\"";
	if ($ip_mask == 3) echo " selected";
	echo ">255.255.255.***</option>"
	."<option value=\"2\"";
	if ($ip_mask == 2) echo " selected";
	echo ">255.255.***.***</option>"
	."<option value=\"1\"";
	if ($ip_mask == 1) echo " selected";
	echo ">255.***.***.***</option>"
	."</select></div>"
	."<div class=\"left\">"._HASH.":</div><div class=\"center\"><input type=\"text\" name=\"hash\" value=\"".$hash."\" size=\"65\" maxlength=\"255\" class=\"admin\"></div>"
	."<div class=\"left\">"._TIME.":</div><div class=\"center\"><input type=\"text\" name=\"time\" value=\"".$time."\" size=\"65\" maxlength=\"4\" class=\"admin\"></div>"
	."<div class=\"left\">"._BANN_REAS.":</div><div class=\"center\"><textarea name=\"info\" cols=\"65\" rows=\"5\" class=\"admin\">".$info."</textarea></div>"
	."<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"security_block_save\"><input type=\"hidden\" name=\"id\" value=\"2\"><input type=\"submit\" value=\""._ADD."\" class=\"fbutton\"></div></form>";
	close();
	open();
	echo "<h2>"._BANNED_USERS."</h2>";
	$bip = explode("||", $confs['blocker_user']);
	if ($confs['blocker_user']) {
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr>"
		."<th>"._NICKNAME."</th><th>"._DATE."</th><th>"._BANN_REAS."</th><th>"._FUNCTIONS."</th></tr>";
		foreach ($bip as $val) {
			if ($val != "") {
				$binfo = explode("|", $val);
				echo "<tr class=\"bgcolor1\">"
				."<td>".$binfo[0]."</td>"
				."<td align=\"center\">".rest_time($binfo[1], 1)."</td>"
				."<td>".$binfo[2]."</td>"
				."<td align=\"center\">".ad_delete("".$admin_file.".php?op=security_block_save&name=".$binfo[0]."&time=".$binfo[1]."&id=3", $binfo[0])."</td></tr>";
			}
		}
		echo "</table>";
	}
	$name = (isset($_GET['name'])) ? "".$_GET['name']."" : "";
	echo "<form action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._NICKNAME.":</div><div class=\"center\"><input type=\"text\" name=\"name\" value=\"".$name."\" size=\"65\" maxlength=\"255\" class=\"admin\"></div>"
	."<div class=\"left\">"._TIME.":</div><div class=\"center\"><input type=\"text\" name=\"time\" value=\"".$time."\" size=\"65\" maxlength=\"4\" class=\"admin\"></div>"
	."<div class=\"left\">"._BANN_REAS.":</div><div class=\"center\"><textarea name=\"info\" cols=\"65\" rows=\"5\" class=\"admin\">".$info."</textarea></div>"
	."<div class=\"left\">"._MAIL_SENDE."</div><div id=\"sb-title\" class=\"center\"><input type=\"checkbox\" name=\"mail\" value=\"1\"></div>"
	."<div id=\"sb\" style=\"display: none;\"><div class=\"left\">"._MAIL_TEXT.":<br><font class=\"small\">"._MAIL_INFO."</font></div><div class=\"center\">".textarea("1", "mailtext", replace_break(str_replace("[text]", ""._BANN_INFO."\n\n"._BANN_TERM.": [time]\n"._BANN_REAS.": [info]", $conf['mtemp'])), "all", "10")."</div></div><script language=\"JavaScript\" type=\"text/javascript\">var admsb = new SwitchCont('sb', '');</script>"
	."<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"security_block_save\"><input type=\"hidden\" name=\"id\" value=\"4\"><input type=\"submit\" value=\""._ADD."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function security_block_save() {
	global $prefix, $db, $admin_file, $conf;
	$send = "";
	$id = $_REQUEST['id'];
	$ip = $_REQUEST['ip'];
	$name = $_REQUEST['name'];
	$mail = $_POST['mail'];
	include("config/config_security.php");
	if ($id == 1 && $ip) {
		$ip_mask = $_GET['ip_mask'];
		$hash = $_GET['hash'];
		$time = $_GET['time'];
		$blocker_ip = preg_replace("/".$ip."\|".$ip_mask."\|".$hash."\|".$time."\|(.*)\|\|/iU", "", $confs['blocker_ip']);
		$content = file_get_contents("config/config_security.php");
		$content = str_replace("\$confs['blocker_ip'] = \"".$confs['blocker_ip']."\";", "\$confs['blocker_ip'] = \"".$blocker_ip."\";", $content);
		$fp = fopen("config/config_security.php", "wb");
		fwrite($fp, $content);
		fclose($fp);
	} elseif ($id == 2 && $ip) {
		$ip_mask = $_POST['ip_mask'];
		$hash = ($_POST['hash']) ? $_POST['hash'] : "0";
		$time = (is_numeric($_POST['time'])) ? time() + ($_POST['time'] * 86400) : time() + 2592000;
		$info = (trim($_POST['info'])) ? text_filter($_POST['info']) : ""._BANN_INFO."";
		$content = file_get_contents("config/config_security.php");
		$content = str_replace("\$confs['blocker_ip'] = \"".$confs['blocker_ip']."\";", "\$confs['blocker_ip'] = \"".$confs['blocker_ip']."".$ip."|".$ip_mask."|".$hash."|".$time."|".$info."||\";", $content);
		$fp = fopen("config/config_security.php", "wb");
		fwrite($fp, $content);
		fclose($fp);
	} elseif ($id == 3 && $name) {
		$time = $_GET['time'];
		$blocker_user = preg_replace("/".$name."\|".$time."\|(.*)\|\|/iU", "", $confs['blocker_user']);
		$content = file_get_contents("config/config_security.php");
		$content = str_replace("\$confs['blocker_user'] = \"".$confs['blocker_user']."\";", "\$confs['blocker_user'] = \"".$blocker_user."\";", $content);
		$fp = fopen("config/config_security.php", "wb");
		fwrite($fp, $content);
		fclose($fp);
	} elseif ($id == 4 && $name) {
		$time = (is_numeric($_POST['time'])) ? time() + ($_POST['time'] * 86400) : time() + 2592000;
		$info = (trim($_POST['info'])) ? text_filter($_POST['info']) : ""._BANN_INFO."";
		$content = file_get_contents("config/config_security.php");
		$content = str_replace("\$confs['blocker_user'] = \"".$confs['blocker_user']."\";", "\$confs['blocker_user'] = \"".$confs['blocker_user']."".$name."|".$time."|".$info."||\";", $content);
		$fp = fopen("config/config_security.php", "wb");
		fwrite($fp, $content);
		fclose($fp);
		if ($mail) {
			list($mail) = $db->sql_fetchrow($db->sql_query("SELECT user_email FROM ".$prefix."_users WHERE user_name='$name'"));
			$subject = "".$conf['sitename']." - "._SECURITY."";
			$msg = nl2br(bb_decode(str_replace("[time]", rest_time($time, 1), str_replace("[info]", $_POST['info'], $_POST['mailtext'])), "all"));
			mail_send($mail, $conf['adminmail'], $subject, $msg, 0, 3);
			$send = "&send=1";
		}
	}
	Header("Location: ".$admin_file.".php?op=security_block".$send."");
}

function security_pass() {
	global $admin_file;
	head();
	security_navi();
	include("config/config_secure.php");
	$permtest = end_chmod("config/config_secure.php", 666);
	if ($permtest) warning($permtest, "", "", 1);
	open();
	echo "<h2>"._SECURITY_PASS."</h2>"
	."<form action='".$admin_file.".php' method='post'>"
	."<div class=\"left\">"._SECURITY_IP_MSG.":</div><div class=\"center\"><input type='text' name='ip_msg' value='".$confsp['ip_msg']."' maxlength='255' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._SECURITY_PASS_MSG.":</div><div class=\"center\"><input type='text' name='pass_msg' value='".$confsp['pass_msg']."' maxlength='255' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._SECURITY_ADMIN_MASK.":</div><div class=\"center\"><select name='admin_mask' class=\"admin\">";
	echo "<option value='4'";
	if ($confsp['admin_mask'] == 4) echo " selected";
	echo ">255.255.255.255</option>"
	."<option value='3'";
	if ($confsp['admin_mask'] == 3) echo " selected";
	echo ">255.255.255.***</option>"
	."<option value='2'";
	if ($confsp['admin_mask'] == 2) echo " selected";
	echo ">255.255.***.***</option>"
	."<option value='1'";
	if ($confsp['admin_mask'] == 1) echo " selected";
	echo ">255.***.***.***</option></select></div>"
	."<div class=\"left\">"._SECURITY_ADMIN_IP.":</div><div class=\"center\"><textarea name='admin_ip' cols='65' rows='5' class=\"admin\">".$confsp['admin_ip']."</textarea></div>"
	."<div class=\"left\">"._SECURITY_LOGIN.":</div><div class=\"center\"><input type='text' name='login' value='".$confsp['login']."' maxlength='255' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._SECURITY_PASSWORD.":</div><div class=\"center\"><input type='text' name='password' value='".$confsp['password']."' maxlength='255' size='65' class=\"admin\"></div>"
	."<div class=\"button\"><input type='hidden' name='op' value='security_pass_save'><input type='submit' value='"._SAVECHANGES."' class=\"fbutton\"></div></form>";
	close();
	foot();
}

function security_pass_save() {
	global $admin_file;
	$protect = array("\n" => "", "\t" => "", "\r" => "", " " => "");
	$xadmin_ip = strtr($_POST['admin_ip'], $protect);
	$content = "\$confsp = array();\n"
	."\$confsp['ip_msg'] = \"".$_POST['ip_msg']."\";\n"
	."\$confsp['pass_msg'] = \"".$_POST['pass_msg']."\";\n"
	."\$confsp['admin_mask'] = \"".$_POST['admin_mask']."\";\n"
	."\$confsp['admin_ip'] = \"$xadmin_ip\";\n"
	."\$confsp['login'] = \"".$_POST['login']."\";\n"
	."\$confsp['password'] = \"".$_POST['password']."\";\n";
	save_conf("config/config_secure.php", $content, 1);
	Header("Location: ".$admin_file.".php?op=security_pass");
}

function security_conf() {
	global $admin_file;
	head();
	security_navi();
	include("config/config_security.php");
	$permtest = end_chmod("config/config_security.php", 666);
	if ($permtest) warning($permtest, "", "", 1);
	open();
	echo "<h2>"._GENSITEINFO."</h2>"
	."<form action='".$admin_file.".php' method='post'>"
	."<div class=\"left\">"._SFLOOD.":</div><div class=\"center\"><select name='flood' class=\"admin\">"
	. "<option value='0'";
	if ($confs['flood'] == 0) echo " selected";
	echo ">"._NO."</option>"
	."<option value='1'";
	if ($confs['flood'] == 1) echo " selected";
	echo ">"._SFLOOD_1."</option>"
	."<option value='2'";
	if ($confs['flood'] == 2) echo " selected";
	echo ">"._SFLOOD_2."</option>"
	."</select></div>"
	."<div class=\"left\">"._SFLOD_T.":</div><div class=\"center\"><input type='text' name='flood_t' value='".$confs['flood_t']."' maxlength='255' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._SECURITY_COOKIE.":</div><div class=\"center\"><input type='text' name='blocker_cookie' value='".$confs['blocker_cookie']."' maxlength='255' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._SECURITY_VIEW."</div><div class=\"center\">".radio_form($confs['error'], "error")."</div>"
	."<div class=\"left\">"._SECURITY_VIEW_JAVA."</div><div class=\"center\">".radio_form($confs['error_java'], "error_java")."</div>"
	."<div class=\"left\">"._SECURITY_STAT."</div><div class=\"center\">".radio_form($confs['error_log'], "error_log")."</div>"
	."<div class=\"left\">"._SECURITY_URL_GET."</div><div class=\"center\">".radio_form($confs['url_get'], "url_get")."</div>"
	."<div class=\"left\">"._SECURITY_URL_POST."</div><div class=\"center\">".radio_form($confs['url_post'], "url_post")."</div>"
	."<div class=\"left\">"._SECURITY_MAIL_SEND."</div><div class=\"center\">".radio_form($confs['mail'], "mail")."</div>"
	."<div class=\"left\">"._SECURITY_HACK_STAT."</div><div class=\"center\">".radio_form($confs['write_h'], "write_h")."</div>"
	."<div class=\"left\">"._SECURITY_WARN_STAT."</div><div class=\"center\">".radio_form($confs['write_w'], "write_w")."</div>"
	."<div class=\"left\">"._SECURITY_WARN_BLOCK."</div><div class=\"center\">".radio_form($confs['block'], "block")."</div>"
	."<div class=\"button\"><input type='hidden' name='op' value='security_conf_save'><input type='submit' value='"._SAVECHANGES."' class=\"fbutton\"></div></form>";
	close();
	foot();
}

function security_conf_save() {
	global $admin_file;
	include("config/config_security.php");
	$xflood_t = (!intval($_POST['flood_t'])) ? 1 : $_POST['flood_t'];
	$content = "\$confs = array();\n"
	."\$confs['flood'] = \"".$_POST['flood']."\";\n"
	."\$confs['flood_t'] = \"".$xflood_t."\";\n"
	."\$confs['blocker_cookie'] = \"".$_POST['blocker_cookie']."\";\n"
	."\$confs['blocker_ip'] = \"".$confs['blocker_ip']."\";\n"
	."\$confs['blocker_user'] = \"".$confs['blocker_user']."\";\n"
	."\$confs['error'] = \"".$_POST['error']."\";\n"
	."\$confs['error_java'] = \"".$_POST['error_java']."\";\n"
	."\$confs['error_log'] = \"".$_POST['error_log']."\";\n"
	."\$confs['url_get'] = \"".$_POST['url_get']."\";\n"
	."\$confs['url_post'] = \"".$_POST['url_post']."\";\n"
	."\$confs['mail'] = \"".$_POST['mail']."\";\n"
	."\$confs['write_h'] = \"".$_POST['write_h']."\";\n"
	."\$confs['write_w'] = \"".$_POST['write_w']."\";\n"
	."\$confs['block'] = \"".$_POST['block']."\";\n";
	save_conf("config/config_security.php", $content);
	Header("Location: ".$admin_file.".php?op=security_conf");
}

switch($op) {
	case "security_show":
	security_show();
	break;
	
	case "security_down":
	if ($_GET['hack']) {
		stream("config/logs/hack.txt", "".date ("d.m.Y")."_hack.txt");
	} elseif ($_GET['warn']) {
		stream("config/logs/warn.txt", "".date ("d.m.Y")."_warn.txt");
	} elseif ($_GET['error']) {
		stream("config/logs/error.txt", "".date ("d.m.Y")."_error.txt");
	} elseif ($_GET['error_site']) {
		stream("config/logs/error_site.txt", "".date ("d.m.Y")."_error_site.txt");
	} else {
		Header("Location: ".$admin_file.".php?op=security_show");
	}
	break;
	
	case "security_del":
	if ($_GET['hack']) {
		unlink("config/logs/hack.txt");
	} elseif ($_GET['warn']) {
		unlink("config/logs/warn.txt");
	} elseif ($_GET['error']) {
		unlink("config/logs/error.txt");
	} elseif ($_GET['error_site']) {
		unlink("config/logs/error_site.txt");
	}
	Header("Location: ".$admin_file.".php?op=security_show");
	break;
	
	case "security_block":
	security_block();
	break;
	
	case "security_block_save":
	security_block_save();
	break;
	
	case "security_pass":
	security_pass();
	break;
	
	case "security_pass_save":
	security_pass_save();
	break;
	
	case "security_conf":
	security_conf();
	break;
	
	case "security_conf_save":
	security_conf_save();
	break;
}
?>