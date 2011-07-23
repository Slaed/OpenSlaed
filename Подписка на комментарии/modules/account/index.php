<?php
# Copyright © 2005 - 2009 SLAED
# Website: http://www.slaed.net

if (!defined("MODULE_FILE")) {
	Header("Location: ../../index.php");
	exit;
}
get_lang($conf['name']);
include("config/config_news.php");
include("config/config_rss.php");
if ($conf['forum']) include("function/forum.php");

function account() {
	global $conf, $stop;
	if (!is_user()) {
		head();
		title(""._USERREGLOGIN."");
		if ($stop) warning($stop, "", "", 1);
		open();
		echo "<form action=\"index.php?name=".$conf['name']."\" method=\"post\">"
		."<div class=\"left\">"._NICKNAME.":</div><div class=\"center\"><input type=\"text\" name=\"user_name\" size=\"10\" maxlength=\"25\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._PASSWORD.":</div><div class=\"center\"><input type=\"password\" name=\"user_password\" size=\"10\" maxlength=\"25\" class=\"".$conf['style']."\"></div>";
		if (extension_loaded("gd") && ($conf['gfx_chk'] == 2 || $conf['gfx_chk'] == 4 || $conf['gfx_chk'] == 5 || $conf['gfx_chk'] == 7)) {
			echo "<div class=\"left\">"._SECURITYCODE.":</div><div class=\"center\"><img src=\"index.php?captcha=1\" border=\"1\" alt=\""._SECURITYCODE."\" title=\""._SECURITYCODE."\"></div>"
			."<div class=\"left\">"._TYPESECCODE.":</div><div class=\"center\"><input type=\"text\" name=\"check\" size=\"10\" maxlength=\"6\" style=\"width: 75px;\" class=\"".$conf['style']."\"></div>";
		}
		echo "<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"login\"><input type=\"submit\" value=\""._USERLOGIN."\" class=\"fbutton\"></div></form>"
		."<h5>[ <a href=\"index.php?name=".$conf['name']."&op=passlost\" title=\""._PASSWORDLOST."\">"._PASSWORDLOST."</a> | <a href=\"index.php?name=".$conf['name']."&op=newuser\" title=\""._REGNEWUSER."\">"._REGNEWUSER."</a> ]</h5>";
		close();
		foot();
	} elseif (is_user()) {
		profil();
	}
}

function checkuser($user_name, $user_email, $rulescheck) {
	global $prefix, $db, $conf, $confu, $stop;
	if ($confu['rule'] && $rulescheck != "1") $stop = _ERROR_RULES;
	checkemail($user_email);
	$mail_b = explode(",", $confu['mail_b']);
	foreach ($mail_b as $val) if ($val != "" && $val == strtolower($user_email)) $stop = _MAIL_BLOCK;
	$name_b = explode(",", $confu['name_b']);
	foreach ($name_b as $val) if ($val != "" && $val == strtolower($user_name)) $stop = _NAME_BLOCK;
	if (!$user_name || !analyze_name($user_name)) $stop = _ERRORINVNICK;
	if (strlen($user_name) > 25) $stop = _NICKLONG;
	if ($db->sql_numrows($db->sql_query("SELECT user_name FROM ".$prefix."_users WHERE user_name='$user_name'")) > 0) $stop = _NICKTAKEN;
	if ($db->sql_numrows($db->sql_query("SELECT user_name FROM ".$prefix."_users_temp WHERE user_name='$user_name'")) > 0) $stop = _NICKTAKEN;
	if ($db->sql_numrows($db->sql_query("SELECT user_email FROM ".$prefix."_users WHERE user_email='$user_email'")) > 0) $stop = _ERROR_EMAIL;
	if ($db->sql_numrows($db->sql_query("SELECT user_email FROM ".$prefix."_users_temp WHERE user_email='$user_email'")) > 0) $stop = _ERROR_EMAIL;
	return($stop);
}

function newuser() {
	global $db, $conf, $confu, $stop;
	if (!is_user()) {
		head();
		if ($stop) {
			title(_NEWUSERERROR);
			warning($stop, "", "", 1);
		} else {
			title(_REGNEWUSER);
		}
		if (!$confu['reg']) {
			warning(_NOREG, "", "", 1);
		} else {
			$user_name = (isset($_POST['user_name'])) ? text_filter(substr($_POST['user_name'], 0, 25)) : "";
			$user_email = (isset($_POST['user_email'])) ? text_filter($_POST['user_email']) : "";
			open();
			echo "<form action=\"index.php?name=".$conf['name']."\" method=\"post\">"
			."<div class=\"left\">"._NICKNAME.":</div><div class=\"center\"><input type=\"text\" name=\"".$conf['sitekey']."\" value=\"".$user_name."\" size=\"30\" maxlength=\"25\" class=\"".$conf['style']."\"></div>"
			."<div class=\"left\">"._EMAIL.":</div><div class=\"center\"><input type=\"text\" name=\"user_email\" value=\"".$user_email."\" size=\"30\" maxlength=\"255\" class=\"".$conf['style']."\"></div>"
			."<div class=\"left\">"._PASSWORD.":</div><div class=\"center\" OnMouseOver=\"Tip('"._BLANKFORAUTO."')\"><input type=\"password\" name=\"user_password\" size=\"25\" maxlength=\"25\" class=\"".$conf['style']."\"></div>"
			."<div class=\"left\">"._RETYPEPASSWORD.":</div><div class=\"center\" OnMouseOver=\"Tip('"._BLANKFORAUTO."')\"><input type=\"password\" name=\"user_password2\" size=\"25\" maxlength=\"25\" class=\"".$conf['style']."\"></div>";
			if (extension_loaded("gd") AND ($conf['gfx_chk'] == 3 OR $conf['gfx_chk'] == 4 OR $conf['gfx_chk'] == 6 OR $conf['gfx_chk'] == 7)) {
				echo "<div class=\"left\">"._SECURITYCODE.":</div><div class=\"center\"><img src=\"index.php?captcha=1\" border=\"1\" alt=\""._SECURITYCODE."\" title=\""._SECURITYCODE."\"></div>"
				."<div class=\"left\">"._TYPESECCODE.":</div><div class=\"center\"><input type=\"text\" name=\"check\" size=\"10\" maxlength=\"6\" style=\"width: 75px;\" class=\"".$conf['style']."\"></div>";
			}
			if ($confu['rule']) {
				echo "<div class=\"left\">"._RULES.":</div><div class=\"center\"><textarea cols=\"50\" rows=\"10\" class=\"".$conf['style']."\">".$confu['rules']."</textarea></div>"
				."<div class=\"left\">"._RULES_OK."</div><div class=\"center\"><input type=\"checkbox\" name=\"rulescheck\" value=\"1\"></div>";
			}
			echo "<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"finnewuser\"><input type=\"submit\" value=\""._NEWUSER."\" class=\"fbutton\"></div></form>"
			."<h5>[ <a href=\"index.php?name=".$conf['name']."\" title=\""._USERLOGIN."\">"._USERLOGIN."</a> | <a href=\"index.php?name=".$conf['name']."&op=passlost\" title=\""._PASSWORDLOST."\">"._PASSWORDLOST."</a> ]</h5>";
			close();
		}
		foot();
	} elseif (is_user()) {
		profil();
	}
}

function finnewuser() {
	global $prefix, $db, $conf, $confu, $stop;
	if (!$confu['reg']) {
		head();
		warning(_NOREG, "", "", 1);
		foot();
	} else {
		$user_name = text_filter($_POST[$conf['sitekey']], 1);
		$user_email = text_filter($_POST['user_email'], 1);
		checkuser($user_name, $user_email, $_POST['rulescheck']);
		$user_password = htmlspecialchars(substr($_POST['user_password'], 0, 40));
		$user_password2 = htmlspecialchars(substr($_POST['user_password2'], 0, 40));
		$code = substr(hexdec(md5(date("F j").$_SESSION['captcha'].$conf['sitekey'])), 2, 6);
		unset($_SESSION['captcha']);
		if (extension_loaded("gd") AND $code != intval($_POST['check']) AND ($conf['gfx_chk'] == 3 OR $conf['gfx_chk'] == 4 OR $conf['gfx_chk'] == 6 OR $conf['gfx_chk'] == 7)) $stop = ""._SECCODEINCOR."";
		if ($user_password == "" && $user_password2 == "") {
			$user_password = gen_pass($confu['minpass']);
		} elseif ($user_password != $user_password2) {
			$stop = _ERROR_PASS;
		} elseif ($user_password == $user_password2 && strlen($user_password) < $confu['minpass']) {
			$stop = _CHARMIN.": ".$confu['minpass'];
		}
		if (!$stop) {
			$check_num = md5(gen_pass(10));
			$time = time();
			$finishlink = "".$conf['homeurl']."/index.php?name=".$conf['name']."&op=activate&user=".urlencode($user_name)."&num=".$check_num."";
			$user_name = text_filter($user_name);
			$user_email = text_filter($user_email);
			$db->sql_query("INSERT INTO ".$prefix."_users_temp (user_id, user_name, user_email, user_password, user_regdate, check_num, time) VALUES (NULL, '$user_name', '$user_email', '$user_password', now(), '$check_num', '$time')");
			head();
			if ($confu['nomail'] == 1) {
				title(_ACCOUNTCREATED);
				warning(_TOFINISHUSERN, "", "", 2);
				open(); 
				echo "<form action=\"index.php\" method=\"get\">"
				."<h2>"._FOLLOWINGMEM."</h2>"
				."<div class=\"left\">"._UNICKNAME.":</div><div class=\"center\">".$user_name."</div>"
				."<div class=\"left\">"._UPASSWORD.":</div><div class=\"center\">".$user_password."</div>"
				."<div class=\"button\"><input type=\"hidden\" name=\"name\" value=\"".$conf['name']."\"><input type=\"hidden\" name=\"op\" value=\"activate\"><input type=\"hidden\" name=\"user\" value=\"".urlencode($user_name)."\"><input type=\"hidden\" name=\"num\" value=\"".$check_num."\"><input type=\"submit\" value=\""._ACTIVATIONSUB."\" class=\"fbutton\"></div></form>";
				close();
			} else {
				$link = "<a href=\"".$finishlink."\">".$finishlink."</a>";
				$subject = $conf['sitename']." - "._ACTIVATIONSUB;
				$message = str_replace("[text]", "".sprintf(""._PASSFSEND."", $user_email, $conf['sitename'], $link, $user_name, $user_password)."<br><br>"._IFYOUDIDNOTASK."", $conf['mtemp']);
				mail_send($user_email, $conf['adminmail'], $subject, $message, 0, 3);
				title(_ACCOUNTCREATED);
				warning(_YOUAREREGISTERED."<br><br>"._FINISHUSERCONF."<br><br>"._THANKSUSER, "", 30, 2);
			}
			foot();
		} else {
			newuser();
		}
	}
}

function activate() {
	global $db, $prefix, $conf;
	$uname = htmlspecialchars(substr(urldecode($_GET['user']), 0, 25));
	$cnum = htmlspecialchars(substr($_GET['num'], 0, 40));
	$past = time() - 86400;
	$db->sql_query("DELETE FROM ".$prefix."_users_temp WHERE time < '$past'");
	$result = $db->sql_query("SELECT user_name, user_email, user_password, user_regdate, check_num FROM ".$prefix."_users_temp WHERE user_name='$uname' AND check_num='$cnum'");
	head();
	if ($db->sql_numrows($result) == 1) {
		list($user_name, $user_email, $user_password, $user_regdate, $check_num) = $db->sql_fetchrow($result);
		if ($cnum == $check_num) {
			$uip = getip();
			$uagent = getagent();
			$db->sql_query("INSERT INTO ".$prefix."_users (user_id, user_name, user_email, user_password, user_avatar, user_regdate, user_lang, user_last_ip, user_agent) VALUES (NULL, '".$user_name."', '".$user_email."', '".md5_salt($user_password)."', '00.gif', '".$user_regdate."', '".$language."', '".$uip."', '".$uagent."')");
			$db->sql_query("DELETE FROM ".$prefix."_users_temp WHERE user_name='$user_name' AND check_num='$check_num'");
			if ($conf['forum']) new_user($user_name, $user_password, $user_email);
			title(_ACTIVATIONYES);
			warning(_ACTMSG, "?name=account", 15, 2);
		} else {
			title(_ACTIVATIONERROR);
			warning(_ACTERROR1, "?name=account", 15, 1);
		}
	} else {
		title(_ACTIVATIONERROR);
		warning(_ACTERROR2, "?name=account", 15, 1);
	}
	foot();
}

function info() {
	global $prefix, $db, $conf, $confu, $pagetitle, $admin_file;
	$user_name = htmlspecialchars(substr($_GET['uname'], 0, 25));
	$result = $db->sql_query("SELECT u.user_id, u.user_name, u.user_email, u.user_website, u.user_avatar, u.user_regdate, u.user_icq, u.user_occ, u.user_from, u.user_interests, u.user_sig, u.user_viewemail, u.user_aim, u.user_yim, u.user_msnm, u.user_password, u.user_storynum, u.user_blockon, u.user_block, u.user_theme, u.user_newsletter, u.user_lastvisit, u.user_lang, u.user_points, u.user_last_ip, u.user_warnings, u.user_group, u.user_birthday, u.user_gender, u.user_votes, u.user_totalvotes, u.user_field, u.user_agent, g.name, g.rank FROM ".$prefix."_users AS u LEFT JOIN ".$prefix."_groups AS g ON (g.id=u.user_group) WHERE user_name='$user_name'");
	if ($db->sql_numrows($result) > 0) {
		list($user_id, $user_name, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_occ, $user_from, $user_interests, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msnm, $user_password, $user_storynum, $user_blockon, $user_block, $user_theme, $user_newsletter, $user_lastvisit, $user_lang, $user_points, $user_last_ip, $user_warnings, $user_group, $user_birthday, $user_gender, $user_votes, $user_totalvotes, $user_field, $user_agent, $gname, $grank) = $db->sql_fetchrow($result);
		head();
		open();
		echo get_info($user_id, $user_name, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_occ, $user_from, $user_interests, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msnm, $user_password, $user_storynum, $user_blockon, $user_block, $user_theme, $user_newsletter, $user_lastvisit, $user_lang, $user_points, $user_last_ip, $user_warnings, $user_group, $user_birthday, $user_gender, $user_votes, $user_totalvotes, $user_field, $user_agent, $gname, $grank, 1);
		close();
		last($user_id, $user_name);
		foot();
	} else {
		$pagetitle = "".$conf['defis']." "._PERSONALINFO."";
		head();
		warning(""._USERNOEXIST."", "", 3, 2);
		foot();
	}
}

function profil() {
	global $prefix, $db, $user, $conf, $confrs, $pagetitle;
	if (is_user()) {
		$pagetitle = "".$conf['defis']." "._THISISYOURPAGE."";
		head();
		title(""._THISISYOURPAGE."");
		navi('1');
		if ($confrs['use'] == 1) {
			$url = (isset($_POST['url'])) ? $_POST['url'] : "";
			$link = ($url) ? $url : "http://";
			open();
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\" class=\"bgcolor4\">"
			."<tr><th colspan=\"2\">"._MYHEADLINES."</th></tr>"
			."<form action=\"index.php?name=".$conf['name']."\" method=\"post\">"
			."<input type=\"hidden\" name=\"op\" value=\"profil\">"
			."<tr class=\"bgcolor1\"><td>"._SELECTASITE.":</td><td>"
			."<select name=\"url\" style=\"width: 250px\" class=\"".$conf['style']."\">".rss_select()."</select>"
			." <input type=\"submit\" value=\""._OK."\" class=\"fbutton\"></td></tr></form>"
			."<form action=\"index.php?name=".$conf['name']."\" method=\"post\">"
			."<input type=\"hidden\" name=\"op\" value=\"profil\">"
			."<tr class=\"bgcolor1\"><td>"._ORTYPEURL.":</td><td>"
			."<input type=\"text\" name=\"url\" value=\"".$link."\" maxlength=\"200\" size=\"40\" style=\"width: 250px\" class=\"".$conf['style']."\">"
			." <input type=\"submit\" value=\""._OK."\" class=\"fbutton\"></td></tr></form></table>"
			."".rss_read($url, "")."";
			close();
		}
		last($user[0], $user[1]);
		foot();
	} else {
		account();
	}
}

function last($uid, $name) {
	global $prefix, $db, $conf;
	$user_id = intval($uid);
	$user_name = htmlspecialchars(substr($name, 0, 25));
	$result = $db->sql_query("SELECT id, cid, modul, date, comment FROM ".$prefix."_comment WHERE uid='".$user_id."' ORDER BY id DESC LIMIT 0, 10");
	if ($db->sql_numrows($result) > 0) {
		open();
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\" class=\"bgcolor4\">"
		."<tr><th colspan=\"2\">"._LAST10COMMENTS."</th></tr>"
		."<tr class=\"bgcolor1\"><td><table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
		while(list($id, $cid, $modul, $date, $comment) = $db->sql_fetchrow($result)) {
			$date= text_filter($date);
			$comment = cutstr(text_filter(bb_decode($comment, $conf['name'])), 70);
			echo "<tr><td><img src=\"".img_find("misc/arrow")."\" border=\"0\"></td><td>$date - <a href=\"".view_article($modul, $cid, $id)."\" title=\"$comment\">$comment</a></td></tr>";
		}
		echo "</table></td></tr></table>";
		close();
	}
	$result = $db->sql_query("SELECT sid, title, time FROM ".$prefix."_stories WHERE aid='".$user_name."' AND time <= now() AND status!='0' ORDER BY sid DESC LIMIT 0,10");
	if ($db->sql_numrows($result) > 0) {
		open();
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\" class=\"bgcolor4\">"
		."<tr><th colspan=\"2\">"._LAST10SUBMISSIONS."</th></tr>"
		."<tr class=\"bgcolor1\"><td><table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
		while(list($sid, $title, $time) = $db->sql_fetchrow($result)) {
			$sid = intval($sid);
			$title = text_filter($title);
			$time = text_filter($time);
			echo "<tr><td><img src=\"".img_find("misc/arrow")."\" border=\"0\"></td><td>$time - <a href=\"index.php?name=news&op=view&id=$sid\" title=\"$title\">$title</a></td></tr>";
		}
		echo "</table></td></tr></table>";
		close();
	}
}

function passlost() {
	global $conf, $stop;
	$code = (isset($_GET['code'])) ? substr($_GET['code'], 0, 10) : false;
	$email = (isset($_GET['email'])) ? $_GET['email'] : false;
	if ($email) checkemail($email);
	if (!is_user()) {
		head();
		title(""._PASSWORDLOST."");
		$info = ($email) ? ""._PASSLOSP."" : ""._PASSLOSC."";
		$send = ($email) ? ""._SENDPASSWORD."" : ""._SEND."";
		if ($stop) warning($stop, "", "", 1);
		warning($info, "", "", 2);
		open();
		echo "<form action=\"index.php?name=".$conf['name']."\" method=\"post\">"
		."<div class=\"left\">"._EMAIL.":</div><div class=\"center\"><input type=\"text\" name=\"email\" value=\"".$email."\" maxlength=\"255\" size=\"45\" class=\"".$conf['style']."\"></div>";
		if ($email) echo "<div class=\"left\">"._CONFIRMATIONCODE.":</div><div class=\"center\"><input type=\"text\" name=\"code\" value=\"".$code."\" maxlength=\"10\" size=\"45\" class=\"".$conf['style']."\"></div>";
		echo "<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"passmail\"><input type=\"submit\" value=\"".$send."\" class=\"fbutton\"></div></form>"
		."<h5>[ <a href=\"index.php?name=".$conf['name']."\" title=\""._USERLOGIN."\">"._USERLOGIN."</a> | <a href=\"index.php?name=".$conf['name']."&op=newuser\" title=\""._REGNEWUSER."\">"._REGNEWUSER."</a> ]</h5>";
		close();
		foot();
	} elseif (is_user()) {
		profil();
	}
}

function passmail() {
	global $prefix, $db, $conf, $confu, $stop;
	$email = $_POST['email'];
	$code = (isset($_POST['code'])) ? substr($_POST['code'], 0, 10) : false;
	checkemail($email);
	if (!$stop) {
		$result = $db->sql_query("SELECT user_name, user_email, user_password FROM ".$prefix."_users WHERE user_email='$email'");
		if ($db->sql_numrows($result) == 0) $stop = ""._NOUSERINFO."";
	}
	if (!$stop) {
		list($user_name, $user_email, $user_password) = $db->sql_fetchrow($result);
		$subpass = substr(md5($user_password), 0, 10);
		if ($code && $subpass == $code) {
			$newpass = gen_pass($confu['minpass']);
			$cryptpass = md5_salt($newpass);
			$db->sql_query("UPDATE ".$prefix."_users SET user_password='$cryptpass' WHERE user_email='$email'");
			if ($conf['forum']) new_pass($user_name, $newpass, $user_email);
			$link = "<a href=\"".$conf['homeurl']."/index.php?name=".$conf['name']."\">".$conf['homeurl']."/index.php?name=".$conf['name']."</a>";
			$subject = "".$conf['sitename']." - "._USERPASSWORD." ".$user_name."";
			$message = str_replace("[text]", sprintf(""._PASSSEND."", $user_name, $conf['sitename'], $user_name, $newpass, $link), $conf['mtemp']);
			mail_send($user_email, $conf['adminmail'], $subject, $message, 0, 3);
			head();
			title(""._PASSWORDLOST."");
			warning(""._USERPASSWORD." ".$user_name." "._MAILED."", "?name=".$conf['name']."", 10, 2);
			foot();
		} else {
			$link = "<a href=\"".$conf['homeurl']."/index.php?name=".$conf['name']."&op=passlost&code=".$subpass."&email=".$email."\">".$conf['homeurl']."/index.php?name=".$conf['name']."&op=passlost&code=".$subpass."&email=".$email."</a>";
			$subject = "".$conf['sitename']." - "._CODEFOR." ".$user_name."";
			$message = str_replace("[text]", "".sprintf(""._PASSCSEND."", $user_name, $conf['sitename'], $subpass, $link)."<br><br>"._IFYOUDIDNOTASK."", $conf['mtemp']);
			mail_send($user_email, $conf['adminmail'], $subject, $message, 0, 3);
			Header("Location: index.php?name=".$conf['name']."&op=passlost&email=".$email."");
		}
	} else {
		passlost();
	}
}

function login() {
	global $prefix, $db, $conf, $stop;
	$code = substr(hexdec(md5(date("F j").$_SESSION['captcha'].$conf['sitekey'])), 2, 6);
	unset($_SESSION['captcha']);
	if (extension_loaded("gd") && $code != intval($_POST['check']) && ($conf['gfx_chk'] == 2 || $conf['gfx_chk'] == 4 || $conf['gfx_chk'] == 5 || $conf['gfx_chk'] == 7)) $stop = ""._SECCODEINCOR."";
	$uname = htmlspecialchars(trim(substr($_POST['user_name'], 0, 25)));
	$upass = htmlspecialchars(trim(substr($_POST['user_password'], 0, 25)));
	if (!$uname || !$upass) $stop = _LOGININCOR;
	$result = $db->sql_query("SELECT user_id, user_name, user_email, user_password, user_storynum, user_blockon, user_theme FROM ".$prefix."_users WHERE user_name='$uname' AND user_password='".md5_salt($upass)."'");
	if ($db->sql_numrows($result) != 1) $stop = _LOGININCOR;
	list($user_id, $user_name, $user_email, $user_password, $user_storynum, $user_blockon, $user_theme) = $db->sql_fetchrow($result);
	if (!$user_id || $user_name != $uname || $user_password != md5_salt($upass)) $stop = _LOGININCOR;
	if (!$stop) {
		cookieset($user_id, $user_name, $user_password, $user_storynum, $user_blockon, $user_theme);
		$uip = getip();
		$uvisit = save_datetime();
		$uagent = getagent();
		$db->sql_query("DELETE FROM ".$prefix."_session WHERE uname='$uip' AND guest='0'");
		$db->sql_query("UPDATE ".$prefix."_users SET user_last_ip='$uip', user_lastvisit='$uvisit', user_agent='$uagent' WHERE user_name='$user_name'");
		if ($conf['forum']) {
			new_user($user_name, $upass, $user_email);
			log_in($uname, $upass);
		}
		$referer = getenv("HTTP_REFERER");
		if ($referer != "" && !preg_match("/^unknown/i", $referer) && !preg_match("/^bookmark/i", $referer)) {
			Header("Location: ".$referer."");
		} else {
			Header("Location: index.php?name=".$conf['name']."&op=profil");
		}
	} else {
		if ($conf['forum']) check_user($uname, $upass);
		account();
	}
}

function logout() {
	global $prefix, $db, $user, $redirect, $conf;
	$user_name = htmlspecialchars(substr($user[1], 0, 25));
	setcookie($conf['user_c'], false);
	$db->sql_query("DELETE FROM ".$prefix."_session WHERE uname='$user_name' AND guest='2'");
	if ($conf['forum']) log_out();
	unset($user);
	head();
	title(""._YOUARELOGGEDOUT."");
	$redirect = ($redirect) ? "?name=".$redirect."" : "";
	echo "<meta http-equiv=\"refresh\" content=\"3; url=index.php".$redirect."\">";
	foot();
}

function edithome() {
	global $user_newsletter, $pagetitle, $conf, $confu, $confn, $stop;
	$pagetitle = "".$conf['defis']." "._CHANGE."";
	if (is_user()) {
		$userinfo = getusrinfo();
		head();
		title(""._CHANGE."");
		navi('0');
		$userinfo['user_theme'] = (!$userinfo['user_theme']) ? $conf['theme'] : $userinfo['user_theme'];
		if ($stop) warning($stop, "", "", 1);
		open();
		echo "<form name=\"post\" action=\"index.php?name=".$conf['name']."\" method=\"post\">"
		."<h2>"._PERSONALINFO."</h2>"
		."<div class=\"left\">"._IP.":</div><div class=\"center\">".$userinfo['user_last_ip']."</div>"
		."<div class=\"left\">"._REG_DATE.":</div><div class=\"center\">".format_time($userinfo['user_regdate'])."</div>";
		if ($confu['point']) echo "<div class=\"left\">"._POINTS.":</div><div class=\"center\">".$userinfo['user_points']."</div>";
		echo "<div class=\"left\">"._YOURNAME.":</div><div class=\"center\">".$userinfo['user_name']."</div>"
		."<div class=\"left\">"._BIRTHDAY.": </div><div class=\"center\" style=\"white-space: nowrap;\">".datetime(2, $userinfo['user_birthday'])."</div>"
		."<div class=\"left\">"._GENDER.": </div><div class=\"center\">";
		if ($userinfo['user_gender'] == 0) {
			echo "<input type=\"radio\" name=\"user_gender\" value=\"1\"> "._MAN." <input type=\"radio\" name=\"user_gender\" value=\"2\"> "._WOMAN." <input type=\"radio\" name=\"user_gender\" value=\"0\" checked> "._NO_INFO."";
		} elseif ($userinfo['user_gender'] == 1) {
			echo "<input type=\"radio\" name=\"user_gender\" value=\"1\" checked> "._MAN." <input type=\"radio\" name=\"user_gender\" value=\"2\"> "._WOMAN." <input type=\"radio\" name=\"user_gender\" value=\"0\"> "._NO_INFO."";
		} else {
			echo "<input type=\"radio\" name=\"user_gender\" value=\"1\"> "._MAN." <input type=\"radio\" name=\"user_gender\" value=\"2\" checked> "._WOMAN." <input type=\"radio\" name=\"user_gender\" value=\"0\"> "._NO_INFO."";
		}
		echo "</div>"
		."<div class=\"left\">"._YOUREMAIL.": <font class=\"option\">*</font></div><div class=\"center\"><input type=\"text\" name=\"user_email\" value=\"".$userinfo['user_email']."\" maxlength=\"60\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._ICQ.":</div><div class=\"center\"><input type=\"text\" name=\"user_icq\" value=\"".$userinfo['user_icq']."\" maxlength=\"15\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._AIM.":</div><div class=\"center\"><input type=\"text\" name=\"user_aim\" value=\"".$userinfo['user_aim']."\" maxlength=\"18\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._YIM.":</div><div class=\"center\"><input type=\"text\" name=\"user_msnm\" value=\"".$userinfo['user_msnm']."\" maxlength=\"25\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._MSN.":</div><div class=\"center\"><input type=\"text\" name=\"user_yim\" value=\"".$userinfo['user_yim']."\" maxlength=\"25\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._SITEURL.":</div><div class=\"center\"><input type=\"text\" name=\"user_website\" value=\"".$userinfo['user_website']."\" maxlength=\"100\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._OCCUPATION.":</div><div class=\"center\"><input type=\"text\" name=\"user_occ\" value=\"".$userinfo['user_occ']."\" maxlength=\"100\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._LOCALITYLANG.":</div><div class=\"center\"><input type=\"text\" name=\"user_from\" value=\"".$userinfo['user_from']."\" maxlength=\"100\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._INTERESTS.":</div><div class=\"center\"><input type=\"text\" name=\"user_interests\" value=\"".$userinfo['user_interests']."\" maxlength=\"150\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._SIGNATURE.":<br><font class=\"small\">"._SIGNATURE_TEXT."</font></div><div class=\"center\">".textarea("1", "comment", $userinfo['user_sig'], $conf['name'], "5")."</div>"
		."".fields_in($userinfo['user_field'], $conf['name'])."";
		if ($confu['news'] == 1) {
			echo "<div class=\"left\">"._NEWSINHOME.":</div><div class=\"center\"><select name=\"user_storynum\" class=\"".$conf['style']."\">";
			echo "<option value=\"3\"";
			if ($userinfo['user_storynum'] == "3") echo " selected";
			echo ">"._NEWSINHOME." - 3</option>";
			echo "<option value=\"5\"";
			if ($userinfo['user_storynum'] == "5") echo " selected";
			echo ">"._NEWSINHOME." - 5</option>";
			echo "<option value=\"10\"";
			if ($userinfo['user_storynum'] == "10") echo " selected";
			echo ">"._NEWSINHOME." - 10</option>";
			echo "<option value=\"15\"";
			if ($userinfo['user_storynum'] == "15") echo " selected";
			echo ">"._NEWSINHOME." - 15</option>";
			echo "<option value=\"20\"";
			if ($userinfo['user_storynum'] == "20") echo " selected";
			echo ">"._NEWSINHOME." - 20</option></select></div>";
		} else {
			echo "<input type=\"hidden\" name=\"user_storynum\" value=\"".$confn['newnum']."\">";
		}
		echo "<div class=\"left\">"._ALLOWUSERS."</div><div class=\"center\">".radio_form($userinfo['user_viewemail'], "user_viewemail")."</div>"
		."<div class=\"left\">"._ACTIVATEPERSONAL."</div><div class=\"center\">".radio_form($userinfo['user_blockon'], "user_blockon")."</div>"
		."<div class=\"left\">"._MENUCONF.":<br><font class=\"small\">"._MENUINFO."</font></div><div class=\"center\">".textarea("2", "comment2", $userinfo['user_block'], $conf['name'], "5")."</div>";
		if ($confu['theme']) {
			$tdir = opendir("templates");
			while ($tfile = readdir($tdir)) {
				if (!preg_match("/\./", $tfile) && $tfile != "admin") {
					if ($tfile == $userinfo['user_theme']) {
						$tcategory .= "<option value=\"$tfile\" selected>".$tfile."</option>";
					} else {
						$tcategory .= "<option value=\"$tfile\">".$tfile."</option>";
					}
					$tcount++;
				}
			}
			closedir($tdir);
			if ($tcount > 1) echo "<div class=\"left\">"._SELECTTHEME.":</div><div class=\"center\"><select name=\"user_theme\" class=\"".$conf['style']."\">".$tcategory."</select></div>";
		}
		echo "<div class=\"button\"><input type=\"hidden\" name=\"user_name\" value=\"$userinfo[user_name]\">"
		."<input type=\"hidden\" name=\"op\" value=\"savehome\"><input type=\"submit\" value=\""._SAVECHANGES."\" class=\"fbutton\"></div></form>"
		."<h2>"._AVATARSETUP."</h2>";
		$user_avatar = (!$userinfo['user_avatar']) ? "00.gif" : $userinfo['user_avatar'];
		echo "<div class=\"left\">"._AVATAR.":<br><font class=\"small\">".sprintf(""._AVATARINFO."", $confu['awidth'], $confu['aheight'], files_size($confu['amaxsize']))."</font></div><div class=\"center\" align=\"center\"><img src=\"".$confu['adirectory']."/".$user_avatar."\"></div>";
		$adir = opendir("".$confu['adirectory']."");
		while ($afile = readdir($adir)) {
			if (!preg_match("/\.|thumb/", $afile)) { 
				$atitle = str_replace("_", " ", $afile);
				$acategory .= "<option value=\"$afile\">"._ALBUM." - ".$atitle."</option>";
				$acount++;
			}
		}
		closedir($adir);
		if ($acount >= 1) {
			echo "<form action=\"index.php?name=".$conf['name']."\" method=\"post\">"
			."<div class=\"left\">"._AVATARSELECT.":</div><div class=\"center\">"
			."<select name=\"category\" style=\"width:350px\">".$acategory."</select>"
			." <input type=\"hidden\" name=\"op\" value=\"avatar\">"
			."<input type=\"submit\" value=\""._OK."\" class=\"fbutton\"></div></form>";
		}
		if ($confu['aupload']) {
			echo "<form enctype=\"multipart/form-data\" action=\"index.php?name=".$conf['name']."\" method=\"post\">"
			."<div class=\"left\">"._AVATAR_USER.":</div><div class=\"center\"><input type=\"file\" name=\"userfile\" value=\"\" size=\"65\" class=\"".$conf['style']."\"></div>"
			."<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"saveavatar\"><input type=\"submit\" value=\""._SAVECHANGES."\" class=\"fbutton\"></div></form>";
		}
		echo "<h2>"._PASSSETUP."</h2>"
		."<form action=\"index.php?name=".$conf['name']."\" method=\"post\">"
		."<div class=\"left\">"._PASSNEW.":</div><div class=\"center\"><input type=\"text\" name=\"newpass\" maxlength=\"25\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._PASSNEW2.":</div><div class=\"center\"><input type=\"text\" name=\"newpass2\" maxlength=\"25\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"left\">"._PASSOLD.":<br><font class=\"small\">"._PASSTEXT."</font></div><div class=\"center\"><input type=\"text\" name=\"oldpass\" maxlength=\"25\" size=\"45\" class=\"".$conf['style']."\"></div>"
		."<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"savepass\"><input type=\"submit\" value=\""._SAVECHANGES."\" class=\"fbutton\"></div></form>";
		close();
		foot();
	} else {
		account();
	}
}

function savehome() {
	global $user, $prefix, $db, $conf, $stop;
	$user_email = text_filter($_POST['user_email']);
	checkemail($user_email);
	if (!$stop) {
		$user_id = intval($user[0]);
		$checkn = htmlspecialchars(substr($user[1], 0, 25));
		$checkp = htmlspecialchars($user[2]);
		list($id, $name, $pass) = $db->sql_fetchrow($db->sql_query("SELECT user_id, user_name, user_password FROM ".$prefix."_users WHERE user_id='$user_id'"));
		if ($id == $user_id && $name == $checkn && $pass == $checkp) {
			$user_website = url_filter($_POST['user_website']);
			$user_icq = text_filter($_POST['user_icq']);
			$user_occ = text_filter($_POST['user_occ']);
			$user_from = text_filter($_POST['user_from']);
			$user_interests = text_filter($_POST['user_interests']);
			$user_sig = nl2br(text_filter($_POST['comment'], 2));
			$user_viewemail = intval($_POST['user_viewemail']);
			$user_aim = text_filter($_POST['user_aim']);
			$user_yim = text_filter($_POST['user_yim']);
			$user_msnm = text_filter($_POST['user_msnm']);
			$user_storynum = intval($_POST['user_storynum']);
			$user_blockon = intval($_POST['user_blockon']);
			$user_block = nl2br(text_filter($_POST['comment2']));
			$user_theme = text_filter($_POST['user_theme']);
			$user_newsletter = intval($_POST['user_newsletter']);
			$user_birthday = save_date();
			$user_gender = intval($_POST['user_gender']);
			$user_field = fields_save($_POST['field']);
			$db->sql_query("UPDATE ".$prefix."_users SET user_email='$user_email', user_website='$user_website', user_viewemail='$user_viewemail', user_icq='$user_icq', user_occ='$user_occ', user_from='$user_from', user_interests='$user_interests', user_sig='$user_sig', user_aim='$user_aim', user_yim='$user_yim', user_msnm='$user_msnm', user_storynum='$user_storynum', user_blockon='$user_blockon', user_block='$user_block', user_theme='$user_theme', user_newsletter='$user_newsletter', user_birthday='$user_birthday', user_gender='$user_gender', user_field='$user_field' WHERE user_id='$user_id'");
			$userinfo = getusrinfo();
			cookieset($userinfo['user_id'], $userinfo['user_name'], $userinfo['user_password'], $userinfo['user_storynum'], $userinfo['user_blockon'], $userinfo['user_theme']);
			Header("Location: index.php?name=".$conf['name']."&op=edithome");
		}
	} else {
		edithome();
	}
}

function avatar() {
	global $pagetitle, $conf, $confu;
	$pagetitle = "".$conf['defis']." "._AVATARSETUP."";
	$category = analyze($_POST['category']);
	if (is_user() && $category) {
		head();
		title(""._AVATARSETUP."");
		navi('0');
		open();
		echo "<center>";
		$dir = opendir("".$confu['adirectory']."/".$category."");
		$temcount = 1;
		while ($entry = readdir($dir)) {
			if (preg_match("/(\.gif|\.png|\.jpg|\.jpeg)$/is", $entry) && $entry != "." && $entry != "..") {
				$entryname = str_replace("_", " ", preg_replace("/^(.*)\..*$/", "\\1", $entry));
				echo "<a href=\"index.php?name=".$conf['name']."&op=saveavatar&category=$category&avatar=$entry\"><img src=\"".$confu['adirectory']."/$category/$entry\" border=\"0\" alt=\""._AVATARSAVE." - ".$entryname."\" title=\""._AVATARSAVE." - ".$entryname."\" hspace=\"5\" vspace=\"5\"></a>";
				if ($temcount == 5) {
					echo "<br>";
					$temcount -= 5;
				}
				$temcount ++;
			}
		}
		closedir($dir);
		echo "</center>";
		close();
		get_page($conf['name']);
		foot();
	} else {
		account();
	}
}

function saveavatar() {
	global $user, $prefix, $db, $conf, $confu, $stop;
	$category = (isset($_POST['category'])) ? $_POST['category'] : $_GET['category'];
	$avatar = (isset($_POST['avatar'])) ? $_POST['avatar'] : $_GET['avatar'];
	if (is_user()) {
		$user_id = intval($user[0]);
		if (!$avatar && !$category && $confu['aupload']) {
			$upload_avatar = upload(1, $confu['adirectory'], $confu['atypefile'], $confu['amaxsize'], $user_id, $confu['awidth'], $confu['aheight']);
			$avatar = (!$upload_avatar) ? $avatar : $upload_avatar;
		} elseif ($avatar && $category) {
			$avatar = $category."/".$avatar;
		}
		if (!$stop && $avatar) {
			$avatar = text_filter($avatar);
			$db->sql_query("UPDATE ".$prefix."_users SET user_avatar='$avatar' WHERE user_id='$user_id'");
			Header("Location: index.php?name=".$conf['name']."&op=edithome");
		} else {
			edithome();
		}
	} else {
		edithome();
	}
}

function savepass() {
	global $user, $prefix, $db, $confu, $conf, $stop;
	$newpass = (isset($_POST['newpass'])) ? $_POST['newpass'] : false;
	$newpass2 = (isset($_POST['newpass2'])) ? $_POST['newpass2'] : false;
	$oldpass = (isset($_POST['oldpass'])) ? $_POST['oldpass'] : false;
	if (is_user() && $oldpass && $newpass && $newpass2) {
		if (strlen($newpass) >= $confu['minpass']) {
			$oldpass = md5_salt($oldpass);
			$user_id = intval($user[0]);
			list($pass) = $db->sql_fetchrow($db->sql_query("SELECT user_password FROM ".$prefix."_users WHERE user_id='$user_id'"));
			if ($pass == $oldpass && $pass != "") {
				if ($newpass == $newpass2) {
					$userinfo = getusrinfo();
					$user_email = $userinfo['user_email'];
					$user_name = $userinfo['user_name'];
					$link = "<a href=\"".$conf['homeurl']."/index.php?name=".$conf['name']."\">".$conf['homeurl']."/index.php?name=".$conf['name']."</a>";
					$subject = $conf['sitename']." - "._USERPASSWORD." ".$user_name;
					$message = str_replace("[text]", sprintf(""._PASSESEND."", $user_name, $conf['sitename'], $user_name, $newpass, $link), $conf['mtemp']);
					mail_send($user_email, $conf['adminmail'], $subject, $message, 0, 3);
					$newpass = md5_salt($newpass);
					$db->sql_query("UPDATE ".$prefix."_users SET user_password='$newpass' WHERE user_id='$user_id'");
					if ($conf['forum']) new_pass($user_name, $newpass2, $user_email);
					Header("Location: index.php?name=".$conf['name']."");
				} else {
					$stop = _ERROR_PASS;
					edithome();
				}
			} else {
				$stop = _ERROROLD;
				edithome();
			}
		} else {
			$stop = _CHARMIN.": ".$confu['minpass'];
			edithome();
		}
	} else {
		edithome();
	}
}

switch($op) {
	default:
	account();
	break;
	
	case "newuser":
	newuser();
	break;
	
	case "finnewuser":
	finnewuser();
	break;
	
	case "info":
	info();
	break;
	
	case "login":
	login();
	break;
	
	case "logout":
	logout();
	break;
	
	case "edithome":
	edithome();
	break;
	
	case "savehome":
	savehome();
	break;
	
	case "passlost":
	passlost();
	break;
	
	case "passmail":
	passmail();
	break;
	
	case "activate":
	activate();
	break;
	
	case "avatar":
	avatar();
	break;
	
	case "saveavatar":
	saveavatar();
	break;
	
	case "savepass":
	savepass();
	break;
	
	case "subscribe_account":subscribe_account();break;
}
?>