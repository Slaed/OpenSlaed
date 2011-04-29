<?php
# Copyright © 2005 - 2009 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

include("config/config_global.php");
include("config/config_users.php");
include("config/config_news.php");

function user_navi() {
	global $admin_file;
	panel();
	open();
	echo "<h1>"._USERADMIN."</h1>"
	."<form method=\"post\" action=\"".$admin_file.".php\">"
	."<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" align=\"center\">"
	."<tr><td><select name=\"search\">"
	."<option value='1'";
	if ($_POST['search'] == "1") echo " selected";
	echo ">"._ID."</option>"
	."<option value='2'";
	if ($_POST['search'] == "2" || !$_POST['search']) echo " selected";
	echo ">"._NICKNAME."</option>"
	."<option value='3'";
	if ($_POST['search'] == "3") echo " selected";
	echo ">"._EMAIL."</option>"
	."<option value='4'";
	if ($_POST['search'] == "4") echo " selected";
	echo ">"._IP."</option>"
	."<option value='5'";
	if ($_POST['search'] == "5") echo " selected";
	echo ">"._URL."</option>"
	."</select></td>"
	."<td>".get_user_search("chng_user", $_POST['chng_user'], "30", "30", "200")."</td><td><input type=\"hidden\" name=\"op\" value=\"user_show\"><input type=\"submit\" value=\""._SEARCH."\" class=\"fbutton\"></td>"
	."</tr></form></table><br>"
	."<h5>[ <a href=\"".$admin_file.".php?op=user_show\">"._HOME."</a>"
	." | <a href=\"".$admin_file.".php?op=user_add\">"._ADD."</a>"
	." | <a href=\"".$admin_file.".php?op=user_new\">"._NEW_USER."</a>"
	." | <a href=\"".$admin_file.".php?op=user_points_null\">"._NULLPOINTS."</a>"
	." | <a href=\"".$admin_file.".php?op=user_conf\">"._PREFERENCES."</a> ]</h5>";
	close();
}

function user_show() {
	global $prefix, $db, $admin_file, $conf, $confu;
	$chng_user = (isset($_POST['chng_user'])) ? $_POST['chng_user'] : $_GET['chng_user'];
	$search = (isset($_POST['search'])) ? $_POST['search'] : $_GET['search'];
	head();
	user_navi();
	if (isset($_GET['send'])) warning(""._MAIL_SEND."", "", "", 2);
	if ($search == 1 && $chng_user) {
		$sqlstring = "WHERE user_id LIKE'%".$chng_user."%' ORDER BY user_id ASC";
	} elseif ($search == 2 && $chng_user) {
		$sqlstring = "WHERE user_name LIKE '%".$chng_user."%' ORDER BY user_name ASC";
	} elseif ($search == 3 && $chng_user) {
		$sqlstring = "WHERE user_email LIKE '%".$chng_user."%' ORDER BY user_email ASC";
	} elseif ($search == 4 && $chng_user) {
		$sqlstring = "WHERE user_last_ip LIKE '%".$chng_user."%' ORDER BY user_last_ip ASC";
	} elseif ($search == 5 && $chng_user) {
		$sqlstring = "WHERE user_website LIKE '%".$chng_user."%' ORDER BY user_website ASC";
	} elseif ($search == 6 && $chng_user) {
		$sqlstring = "WHERE user_group=".$chng_user." ORDER BY user_id ASC";
	} elseif ($search == 7 && $chng_user) {
		$sqlstring = "WHERE user_points>=".$chng_user." ORDER BY user_id ASC";
	} else {
		$sqlstring = "ORDER BY user_id DESC";
	}
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $conf['anum'];
	$result = $db->sql_query("SELECT u.user_id, u.user_name, u.user_email, u.user_website, u.user_avatar, u.user_regdate, u.user_icq, u.user_occ, u.user_from, u.user_interests, u.user_sig, u.user_viewemail, u.user_aim, u.user_yim, u.user_msnm, u.user_password, u.user_storynum, u.user_blockon, u.user_block, u.user_theme, u.user_newsletter, u.user_lastvisit, u.user_lang, u.user_points, u.user_last_ip, u.user_warnings, u.user_group, u.user_birthday, u.user_gender, u.user_votes, u.user_totalvotes, u.user_field, u.user_agent, g.name, g.rank FROM ".$prefix."_users AS u LEFT JOIN ".$prefix."_groups AS g ON (g.id=u.user_group) ".$sqlstring." LIMIT ".$offset.", ".$conf['anum']."");
	if ($db->sql_numrows($result) > 0) {
		open();
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr>"
		."<th>"._ID."</th><th>"._NICKNAME."</th><th>"._EMAIL."</th><th>"._IP."</th><th>"._HASH."</th><th>"._FUNCTIONS."</th></tr>";
		while (list($user_id, $user_name, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_occ, $user_from, $user_interests, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msnm, $user_password, $user_storynum, $user_blockon, $user_block, $user_theme, $user_newsletter, $user_lastvisit, $user_lang, $user_points, $user_last_ip, $user_warnings, $user_group, $user_birthday, $user_gender, $user_votes, $user_totalvotes, $user_field, $user_agent, $gname, $grank) = $db->sql_fetchrow($result)) {
			echo "<tr class=\"bgcolor1\">"
			."<td align=\"center\">".$user_id."</td>"
			."<td class=\"help\" OnMouseOver=\"Tip('<div style=&quot;width: 500px;&quot;>".get_info($user_id, $user_name, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_occ, $user_from, $user_interests, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msnm, $user_password, $user_storynum, $user_blockon, $user_block, $user_theme, $user_newsletter, $user_lastvisit, $user_lang, $user_points, $user_last_ip, $user_warnings, $user_group, $user_birthday, $user_gender, $user_votes, $user_totalvotes, $user_field, $user_agent, $gname, $grank, 0)."</div>')\">".search_color(user_info($user_name, 1), $chng_user)."</td>"
			."<td>".search_color($user_email, $chng_user)."</td>"
			."<td>".user_geo_ip($user_last_ip, 4)."</td>"
			."<td>".md5($user_agent)."</td>"
			."<td align=\"center\">".ad_bann("".$admin_file.".php?op=security_block&new_ip=".$user_last_ip."", $user_last_ip)." ".ad_edit("".$admin_file.".php?op=user_add&id=".$user_id."")." ".ad_delete("".$admin_file.".php?op=user_del&id=".$user_id."&refer=1", $user_name)."</td></tr>";
		}
		echo "</table>";
		close();
		list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(user_id) FROM ".$prefix."_users ".$sqlstring.""));
		$numpages = ceil($numstories / $conf['anum']);
		$lsear = ($search) ? "&search=".$search."" : "";
		$lchng = ($chng_user) ? "&chng_user=".$chng_user."" : "";
		num_page("", $numstories, $numpages, $conf['anum'], "op=user_show".$lsear."".$lchng."&");
	} else {
		warning(""._USERNOEXIST."", "", "", 2);
	}
	foot();
}

function user_add() {
	global $prefix, $db, $admin_file, $conf, $confu, $confn, $stop;
	if (isset($_REQUEST['id'])) {
		$uid = intval($_REQUEST['id']);
		$result = $db->sql_query("SELECT user_id, user_name, user_email, user_website, user_avatar, user_regdate, user_icq, user_occ, user_from, user_interests, user_sig, user_viewemail, user_aim, user_yim, user_msnm, user_password, user_storynum, user_blockon, user_block, user_theme, user_newsletter, user_lang, user_points, user_warnings, user_group, user_birthday, user_gender, user_field FROM ".$prefix."_users WHERE user_id='$uid'");
		list($user_id, $user_name, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_occ, $user_from, $user_interests, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msnm, $user_password, $user_storynum, $user_blockon, $user_block, $user_theme, $user_newsletter, $user_lang, $user_points, $user_warnings, $user_group, $user_birthday, $user_gender, $user_field) = $db->sql_fetchrow($result);
	} else {
		$user_id = $_POST['user_id'];
		$user_name = $_POST['user_name'];
		$user_email = $_POST['user_email'];
		$user_website = (isset($_POST['user_website'])) ? $_POST['user_website'] : "http://";
		$user_avatar = $_POST['user_avatar'];
		$user_regdate = $_POST['user_regdate'];
		$user_icq = $_POST['user_icq'];
		$user_occ = $_POST['user_occ'];
		$user_from = $_POST['user_from'];
		$user_interests = $_POST['user_interests'];
		$user_sig = $_POST['user_sig'];
		$user_viewemail = $_POST['user_viewemail'];
		$user_aim = $_POST['user_aim'];
		$user_yim = $_POST['user_yim'];
		$user_msnm = $_POST['user_msnm'];
		$user_password = $_POST['user_password'];
		$user_storynum = $_POST['user_storynum'];
		$user_blockon = $_POST['user_blockon'];
		$user_block = $_POST['user_block'];
		$user_theme = $_POST['user_theme'];
		$user_newsletter = $_POST['user_newsletter'];
		$user_lang = $_POST['user_lang'];
		$user_points = $_POST['user_points'];
		$user_warnings = $_POST['user_warnings'];
		$user_group = $_POST['user_group'];
		$user_birthday = $_POST['user_birthday'];
		$user_gender = $_POST['user_gender'];
		$user_field = $_POST['user_field'];
	}
	head();
	user_navi();
	if ($stop) warning($stop, "", "", 1);
	open();
	echo "<form name=\"post\" action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._NICKNAME.":</div><div class=\"center\"><input type=\"text\" name=\"user_name\" value=\"".$user_name."\" maxlength=\"25\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._EMAIL.":</div><div class=\"center\"><input type=\"text\" name=\"user_email\" value=\"".$user_email."\" maxlength=\"255\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._SITEURL.":</div><div class=\"center\"><input type=\"text\" name=\"user_website\" value=\"".$user_website."\" maxlength=\"255\" size=\"65\" class=\"admin\"></div>";
	if ($user_avatar) echo "<div class=\"left\">"._AVATAR.":</div><div class=\"center\"><input type=\"text\" name=\"user_avatar\" value=\"".$user_avatar."\" maxlength=\"255\" size=\"65\" class=\"admin\"></div>";
	echo "<div class=\"left\">"._REG_DATE.":</div><div class=\"center\" style=\"white-space: nowrap;\">".datetime(1, $user_regdate)."</div>"
	."<div class=\"left\">"._ICQ.":</div><div class=\"center\"><input type=\"text\" name=\"user_icq\" value=\"".$user_icq."\" maxlength=\"15\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._AIM.":</div><div class=\"center\"><input type=\"text\" name=\"user_aim\" value=\"".$user_aim."\" maxlength=\"18\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._YIM.":</div><div class=\"center\"><input type=\"text\" name=\"user_yim\" value=\"".$user_yim."\" maxlength=\"25\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._MSN.":</div><div class=\"center\"><input type=\"text\" name=\"user_msnm\" value=\"".$user_msnm."\" maxlength=\"25\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._OCCUPATION.":</div><div class=\"center\"><input type=\"text\" name=\"user_occ\" value=\"".$user_occ."\" maxlength=\"100\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._LOCATION.":</div><div class=\"center\"><input type=\"text\" name=\"user_from\" value=\"".$user_from."\" maxlength=\"100\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._INTERESTS.":</div><div class=\"center\"><input type=\"text\" name=\"user_interests\" value=\"".$user_interests."\" maxlength=\"150\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._SIGNATURE.":<br><font class=\"small\">"._SIGNATURE_TEXT."</font></div><div class=\"center\">".textarea("1", "user_sig", $user_sig, "account", "5")."</div>"
	."<div class=\"left\">"._ALLOWUSERS."</div><div class=\"center\">".radio_form($user_viewemail, "user_viewemail")."</div>";
	if ($confu['news'] == 1) {
		echo "<div class=\"left\">"._NEWSINHOME.":</div><div class=\"center\"><select name=\"user_storynum\" class=\"admin\">"
		."<option value=\"3\"";
		if ($user_storynum == "3") echo " selected";
		echo ">"._NEWSINHOME." - 3</option>"
		."<option value=\"5\"";
		if ($user_storynum == "5") echo " selected";
		echo ">"._NEWSINHOME." - 5</option>"
		."<option value=\"10\"";
		if ($user_storynum == "10") echo " selected";
		echo ">"._NEWSINHOME." - 10</option>"
		."<option value=\"15\"";
		if ($user_storynum == "15") echo " selected";
		echo ">"._NEWSINHOME." - 15</option>"
		."<option value=\"20\"";
		if ($user_storynum == "20") echo " selected";
		echo ">"._NEWSINHOME." - 20</option></select></div>";
	} else {
		echo "<input type=\"hidden\" name=\"user_storynum\" value=\"".$confn['newnum']."\">";
	}
	echo "<div class=\"left\">"._ACTIVATEPERSONAL."</div><div class=\"center\">".radio_form($user_blockon, "user_blockon")."</div>"
	."<div class=\"left\">"._MENUCONF.":<br><font class=\"small\">"._MENUINFO."</font></div><div class=\"center\">".textarea("2", "user_block", $user_block, "account", "5")."</div>";
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
		if ($tcount > 1) echo "<div class=\"left\">"._SELECTTHEME.":</div><div class=\"center\"><select name=\"user_theme\" class=\"admin\">".$tcategory."</select></div>";
	}
	echo "<div class=\"left\">"._RNEWSLETTER.":</div><div class=\"center\">".radio_form($user_newsletter, "user_newsletter")."</div>";
	if ($conf['multilingual'] == 1) echo "<div class=\"left\">"._LANGUAGE.":</div><div class=\"center\"><select name=\"language\" class=\"admin\">".language($language)."</select></div>";
	echo "<div class=\"left\">"._POINTS.":</div><div class=\"center\"><input type=\"text\" name=\"user_points\" value=\"".$user_points."\" maxlength=\"10\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._WARNINGS.":</div><div class=\"center\"><input type=\"text\" name=\"user_warnings\" value=\"".$user_warnings."\" maxlength=\"5\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._SPEC_GROUP.":</div><div class=\"center\"><select name=\"user_group\" class=\"admin\">"
	."<option value=\"0\">"._NO."</option>";
	$result = $db->sql_query("SELECT id, name FROM ".$prefix."_groups WHERE extra='1'");
	while(list($grid, $grname) = $db->sql_fetchrow($result)) {
		$sel = ($grid == $user_group) ? "selected" : "";
		echo "<option value=\"$grid\" $sel>$grname</option>";
	}
	echo "</select></div>"
	."<div class=\"left\">"._BIRTHDAY.":</div><div class=\"center\" style=\"white-space: nowrap;\">".datetime(2, $user_birthday)."</div>"
	."<div class=\"left\">"._GENDER.":</div><div class=\"center\">";
	if ($user_gender == 0) {
		echo "<input type=\"radio\" name=\"user_gender\" value=\"1\"> "._MAN." <input type=\"radio\" name=\"user_gender\" value=\"2\"> "._WOMAN." <input type=\"radio\" name=\"user_gender\" value=\"0\" checked> "._NO_INFO."";
	} elseif ($user_gender == 1) {
		echo "<input type=\"radio\" name=\"user_gender\" value=\"1\" checked> "._MAN." <input type=\"radio\" name=\"user_gender\" value=\"2\"> "._WOMAN." <input type=\"radio\" name=\"user_gender\" value=\"0\"> "._NO_INFO."";
	} else {
		echo "<input type=\"radio\" name=\"user_gender\" value=\"1\"> "._MAN." <input type=\"radio\" name=\"user_gender\" value=\"2\" checked> "._WOMAN." <input type=\"radio\" name=\"user_gender\" value=\"0\"> "._NO_INFO."";
	}
	$check = ($_COOKIE['pas'] == "hide") ? "" : "checked";
	echo "</div>"
	."".fields_in($user_field, "account").""
	."<div class=\"left\">"._PASSWORD.":</div><div class=\"center\"><input type=\"password\" name=\"user_password\" value=\"\" maxlength=\"25\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._RETYPEPASSWORD.":</div><div class=\"center\"><input type=\"password\" name=\"user_password2\" value=\"\" maxlength=\"25\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._MAIL_SENDE."</div><div id=\"pas-title\" class=\"center\"><input type=\"checkbox\" name=\"mail\" value=\"1\" ".$check."></div>"
	."<div id=\"pas\"><div class=\"left\">"._MAIL_TEXT.":<br><font class=\"small\">"._MAIL_PASS_INFO."</font></div><div class=\"center\">".textarea("3", "mailtext", replace_break(str_replace("[text]", ""._FOLLOWINGMEM."\n\n"._NICKNAME.": [login]\n"._PASSWORD.": [pass]", $conf['mtemp'])), "account", "10")."</div></div><script language=\"JavaScript\" type=\"text/javascript\">var admpa = new SwitchCont('pas', '1');</script>"
	."<div class=\"button\"><input type=\"hidden\" name=\"user_id\" value=\"".$user_id."\"><input type=\"hidden\" name=\"op\" value=\"user_add_save\"><input type=\"submit\" value=\""._SAVE."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function user_add_save() {
	global $prefix, $db, $admin_file, $conf, $stop;
	$user_id = (isset($_POST['user_id'])) ? intval($_POST['user_id']) : "";
	$user_name = $_POST['user_name'];
	$user_email = $_POST['user_email'];
	$user_website = url_filter($_POST['user_website']);
	$user_avatar = (isset($_POST['user_avatar'])) ? $_POST['user_avatar'] : "00.gif";
	$user_regdate = save_datetime();
	$user_icq = $_POST['user_icq'];
	$user_occ = $_POST['user_occ'];
	$user_from = $_POST['user_from'];
	$user_interests = $_POST['user_interests'];
	$user_sig = nl2br(text_filter($_POST['user_sig']));
	$user_viewemail = $_POST['user_viewemail'];
	$user_aim = $_POST['user_aim'];
	$user_yim = $_POST['user_yim'];
	$user_msnm = $_POST['user_msnm'];
	$user_password = isset($_POST['user_password']) ? $_POST['user_password'] : 0;
	$user_password2 = isset($_POST['user_password2']) ? $_POST['user_password2'] : 0;
	$user_storynum = $_POST['user_storynum'];
	$user_blockon = $_POST['user_blockon'];
	$user_block = nl2br(text_filter($_POST['user_block']));
	$user_theme = $_POST['user_theme'];
	$user_newsletter = $_POST['user_newsletter'];
	$user_lang = $_POST['user_lang'];
	$user_points = $_POST['user_points'];
	$user_warnings = $_POST['user_warnings'];
	$user_group = $_POST['user_group'];
	$user_birthday = save_date();
	$user_gender = $_POST['user_gender'];
	$user_field = fields_save($_POST['field']);
	$mail = $_POST['mail'];
	if (!$user_id && (!$user_name || !$user_email || !$user_password || !$user_password2)) $stop = ""._ERROR_ALL."";
	if ($user_name) {
		list($uid, $uname) = $db->sql_fetchrow($db->sql_query("SELECT user_id, user_name FROM ".$prefix."_users WHERE user_name='".$user_name."'"));
		list($tuid, $tuname) = $db->sql_fetchrow($db->sql_query("SELECT user_id, user_name FROM ".$prefix."_users_temp WHERE user_name='".$user_name."'"));
		if (($user_id != $uid && $user_name == $uname) || ($user_id != $tuid && $user_name == $tuname)) $stop = ""._USEREXIST."";
		list($uid, $email) = $db->sql_fetchrow($db->sql_query("SELECT user_id, user_email FROM ".$prefix."_users WHERE user_email='$user_email'"));
		list($tuid, $temail) = $db->sql_fetchrow($db->sql_query("SELECT user_id, user_email FROM ".$prefix."_users_temp WHERE user_email='$user_email'"));
		if (($user_id != $uid && $user_email == $email) || ($user_id != $tuid && $user_email == $temail)) $stop = ""._ERROR_EMAIL."";
	} else {
		$stop = _ERROR_ALL;
	}
	if (!analyze_name($user_name)) $stop = _ERRORINVNICK;
	checkemail($user_email);
	if ($user_password != $user_password2) $stop = _ERROR_PASS;
	if (!$stop) {
		if ($user_id) {
			if ($user_password && $user_password == $user_password2) {
				$newpass = md5_salt($user_password);
				$db->sql_query("UPDATE ".$prefix."_users SET user_name='$user_name', user_email='$user_email', user_website='$user_website', user_viewemail='$user_viewemail', user_avatar='$user_avatar', user_regdate='$user_regdate', user_icq='$user_icq', user_occ='$user_occ', user_from='$user_from', user_interests='$user_interests', user_sig='$user_sig', user_viewemail='$user_viewemail', user_aim='$user_aim', user_yim='$user_yim', user_msnm='$user_msnm', user_password='$newpass', user_storynum='$user_storynum', user_blockon='$user_blockon', user_block='$user_block', user_theme='$user_theme', user_newsletter='$user_newsletter', user_lang='$user_lang', user_points='$user_points', user_warnings='$user_warnings', user_group='$user_group', user_birthday='$user_birthday', user_gender='$user_gender', user_field='$user_field' WHERE user_id='$user_id'");
			} else {
				$db->sql_query("UPDATE ".$prefix."_users SET user_name='$user_name', user_email='$user_email', user_website='$user_website', user_viewemail='$user_viewemail', user_avatar='$user_avatar', user_regdate='$user_regdate', user_icq='$user_icq', user_occ='$user_occ', user_from='$user_from', user_interests='$user_interests', user_sig='$user_sig', user_viewemail='$user_viewemail', user_aim='$user_aim', user_yim='$user_yim', user_msnm='$user_msnm', user_storynum='$user_storynum', user_blockon='$user_blockon', user_block='$user_block', user_theme='$user_theme', user_newsletter='$user_newsletter', user_lang='$user_lang', user_points='$user_points', user_warnings='$user_warnings', user_group='$user_group', user_birthday='$user_birthday', user_gender='$user_gender', user_field='$user_field' WHERE user_id='$user_id'");
			}
		} else {
			$user_password = md5_salt($user_password);
			$db->sql_query("INSERT INTO ".$prefix."_users (user_id, user_name, user_email, user_website, user_avatar, user_regdate, user_icq, user_occ, user_from, user_interests, user_sig, user_viewemail, user_aim, user_yim, user_msnm, user_password, user_storynum, user_blockon, user_block, user_theme, user_newsletter, user_lang, user_points, user_warnings, user_group, user_birthday, user_gender, user_field) VALUES (NULL, '$user_name', '$user_email', '$user_website', '$user_avatar', '$user_regdate', '$user_icq', '$user_occ', '$user_from', '$user_interests', '$user_sig', '$user_viewemail', '$user_aim', '$user_yim', '$user_msnm', '$user_password', '$user_storynum', '$user_blockon', '$user_block', '$user_theme', '$user_newsletter', '$user_lang', '$user_points', '$user_warnings', '$user_group', '$user_birthday', '$user_gender', '$user_field')");
		}
		if ($mail) {
			$subject = $conf['sitename']." - "._USERPASSWORD." ".$user_name;
			$msg = nl2br(bb_decode(str_replace("[pass]", $user_password, str_replace("[login]", $user_name, $_POST['mailtext'])), "account"));
			mail_send($user_email, $conf['adminmail'], $subject, $msg, 0, 3);
			$send = "&send=1";
		}
		Header("Location: ".$admin_file.".php?op=user_show".$send."");
	} else {
		user_add();
	}
}

function user_new() {
	global $prefix, $db, $admin_file, $conf;
	head();
	user_navi();
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$offset = ($num-1) * $conf['anum'];
	$result = $db->sql_query("SELECT user_id, user_name, user_email, user_regdate, check_num FROM ".$prefix."_users_temp WHERE user_id LIMIT ".$offset.", ".$conf['anum']."");
	if ($db->sql_numrows($result) > 0) {
		open();
		echo '<script type="text/javascript">function checkAll(oForm, cbName, checked){for (var i=0; i < oForm[cbName].length; i++) oForm[cbName][i].checked = checked;}</script>';
		echo "<form action='".$admin_file.".php' method='post' id='del_new_users'>";
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"sort\" id=\"sort_id\"><tr>"
		."<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick=\"checkAll(this.form,'delete[]',this.checked)\" type='checkbox' value='delete'> Select all</span>"
		."<th>"._ID."</th><th>"._NICKNAME."</th><th>"._EMAIL."</th><th>"._REG_DATE."</th><th>"._FUNCTIONS."</th></tr>";
		while (list($user_id, $user_name, $user_email, $user_regdate, $check_num) = $db->sql_fetchrow($result)) {
			echo "<tr class=\"bgcolor1\">"
			."<td align=\"center\">".$user_id."</td>"
			."<td>".$user_name."</td>"
			."<td>".$user_email."</td>"
			."<td align=\"center\">".$user_regdate."</td>"
			."<td align=\"center\"><input type='checkbox' name='delete[]' value='".$user_id."'> ".ad_status("".$conf['homeurl']."/index.php?name=account&op=activate&user=".urlencode($user_name)."&num=".$check_num."", $active)." ".ad_delete("".$admin_file.".php?op=user_new_del&id=".$user_id."&refer=1", $user_name)."</td></tr>";
		}
		echo "</table>";
		echo "<div class='button'><input type='hidden' name='op' value='user_new_delete'><input type='submit' value='Delete selected' class='fbutton'></div></form>";
		close();
		list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(user_id) FROM ".$prefix."_users_temp WHERE user_id"));
		$numpages = ceil($numstories / $conf['anum']);
		num_page("", $numstories, $numpages, $conf['anum'], "op=user_new&");
	} else {
		warning(_NO_INFO, "", "", 2);
	}
	foot();
}

function user_conf() {
	global $admin_file, $confu;
	head();
	user_navi();
	$permtest = end_chmod("config/config_users.php", 666);
	if ($permtest) warning($permtest, "", "", 1);
	open();
	echo "<h2>"._GENSITEINFO."</h2>"
	."<form action='".$admin_file.".php' method='post'>"
	."<div class=\"left\">"._ANONYMOUSNAME.":</div><div class=\"center\"><input type='text' name='anonym' value='".$confu['anonym']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._ADIR.":</div><div class=\"center\"><input type='text' name='adirectory' value='".$confu['adirectory']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._ATYPE.":</div><div class=\"center\"><input type='text' name='atypefile' value='".$confu['atypefile']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._ASIZE.":</div><div class=\"center\"><input type='text' name='amaxsize' value='".$confu['amaxsize']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._AWIDTH.""._AIN.":</div><div class=\"center\"><input type='text' name='awidth' value='".$confu['awidth']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._AHEIGHT.""._AIN.":</div><div class=\"center\"><input type='text' name='aheight' value='".$confu['aheight']."' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._VOTING_TIME.":</div><div class=\"center\"><input type='text' name='user_t' value='".intval($confu['user_t'] / 86400)."' maxlength='25' size='65' class=\"admin\"></div>"
	."<div class=\"left\">"._PASSWDLEN.":</div><div class=\"center\">"
	."<select name='minpass' class=\"admin\">"
	."<option value='3'";
	if ($confu['minpass'] == "3") echo " selected";
	echo ">3</option>"
	."<option value='5'";
	if ($confu['minpass'] == "5") echo " selected";
	echo ">5</option>"
	."<option value='8'";
	if ($confu['minpass'] == "8") echo " selected";
	echo ">8</option>"
	."<option value='10'";
	if ($confu['minpass'] == "10") echo " selected";
	echo ">10</option>"
	."</select></div>"
	."<div class=\"left\">"._LOGINFL.":</div><div class=\"center\">"
	."<select name='enter' class=\"admin\">"
	."<option value='0'";
	if ($confu['enter'] == "0") echo " selected";
	echo ">"._LOGINL."</option>"
	."<option value='1'";
	if ($confu['enter'] == "1") echo " selected";
	echo ">"._LOGINF."</option>"
	."</select></div>"
	."<div class=\"left\">"._UPDATE_POINTS."</div><div class=\"center\">".radio_form($confu['point'], "point")."</div>"
	."<div class=\"left\">"._AUPLOAD."</div><div class=\"center\">".radio_form($confu['aupload'], "aupload")."</div>"
	."<div class=\"left\">"._NO_MAIL_REG."</div><div class=\"center\">".radio_form($confu['nomail'], "nomail")."</div>"
	."<div class=\"left\">"._USERSHOMENUM."</div><div class=\"center\">".radio_form($confu['news'], "news")."</div>"
	."<div class=\"left\">"._USERIPCHECK."</div><div class=\"center\">".radio_form($confu['check'], "check")."</div>"
	."<div class=\"left\">"._REGACT."</div><div class=\"center\">".radio_form($confu['reg'], "reg")."</div>"
	."<div class=\"left\">"._SELTHEME."</div><div class=\"center\">".radio_form($confu['theme'], "theme")."</div>"
	."<div class=\"left\">"._RULACT."</div><div class=\"center\">".radio_form($confu['rule'], "rule")."</div>"
	."<div class=\"left\">"._RULES.":</div><div class=\"center\"><textarea name=\"rules\" cols=\"65\" rows=\"5\" class=\"admin\">".$confu['rules']."</textarea></div>"
	."<div class=\"left\">"._NAME_BLOCK.":</div><div class=\"center\"><textarea name=\"name_b\" cols=\"65\" rows=\"5\" class=\"admin\">".$confu['name_b']."</textarea></div>"
	."<div class=\"left\">"._MAIL_BLOCK.":</div><div class=\"center\"><textarea name=\"mail_b\" cols=\"65\" rows=\"5\" class=\"admin\">".$confu['mail_b']."</textarea></div>"
	."<div class=\"button\"><input type='hidden' name='op' value='user_save'><input type='submit' value=\""._SAVECHANGES."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function user_save() {
	global $admin_file, $confu;
	$protect = array("\n" => "", "\t" => "", "\r" => "", " " => "");
	$xatypefile = (!$_POST['atypefile']) ? "gif,jpg,jpeg,png" : strtolower(strtr($_POST['atypefile'], $protect));
	$xamaxsize = (!intval($_POST['amaxsize'])) ? 51200 : $_POST['amaxsize'];
	$xawidth = (!intval($_POST['awidth'])) ? 100 : $_POST['awidth'];
	$xaheight = (!intval($_POST['aheight'])) ? 100 : $_POST['aheight'];
	$xuser_t = (!$_POST['user_t']) ? 2592000 : intval($_POST['user_t'] * 86400);
	$xname_b = strtolower(strtr($_POST['name_b'], $protect));
	$xmail_b = strtolower(strtr($_POST['mail_b'], $protect));
	$content = "\$confu = array();\n"
	."\$confu['anonym'] = \"".$_POST['anonym']."\";\n"
	."\$confu['adirectory'] = \"".$_POST['adirectory']."\";\n"
	."\$confu['atypefile'] = \"".$xatypefile."\";\n"
	."\$confu['amaxsize'] = \"".$xamaxsize."\";\n"
	."\$confu['awidth'] = \"".$xawidth."\";\n"
	."\$confu['aheight'] = \"".$xaheight."\";\n"
	."\$confu['user_t'] = \"".$xuser_t."\";\n"
	."\$confu['minpass'] = \"".$_POST['minpass']."\";\n"
	."\$confu['enter'] = \"".$_POST['enter']."\";\n"
	."\$confu['point'] = \"".$_POST['point']."\";\n"
	."\$confu['aupload'] = \"".$_POST['aupload']."\";\n"
	."\$confu['nomail'] = \"".$_POST['nomail']."\";\n"
	."\$confu['news'] = \"".$_POST['news']."\";\n"
	."\$confu['check'] = \"".$_POST['check']."\";\n"
	."\$confu['reg'] = \"".$_POST['reg']."\";\n"
	."\$confu['theme'] = \"".$_POST['theme']."\";\n"
	."\$confu['rule'] = \"".$_POST['rule']."\";\n"
	."\$confu['rules'] = \"".text_filter($_POST['rules'], 1)."\";\n"
	."\$confu['name_b'] = \"".$xname_b."\";\n"
	."\$confu['mail_b'] = \"".$xmail_b."\";\n"
	."\$confu['points'] = \"".$confu['points']."\";\n";
	save_conf("config/config_users.php", $content);
	Header("Location: ".$admin_file.".php?op=user_conf");
}

switch($op) {
	case "user_show":
	user_show();
	break;
	
	case "user_add":
	user_add();
	break;
	
	case "user_add_save":
	user_add_save();
	break;
	
	case "user_new":
	user_new();
	break;
	
	case "user_new_del":
	$db->sql_query("DELETE FROM ".$prefix."_users_temp WHERE user_id='".$id."'");
	referer("".$admin_file.".php?op=user_show");
	break;
	
	case "user_del":
	$db->sql_query("DELETE FROM ".$prefix."_users WHERE user_id='".$id."'");
	#$db->sql_query("DELETE FROM ".$prefix."_comment WHERE uid='".$id."'");
	referer("".$admin_file.".php?op=user_show");
	break;
	
	case "user_points_null":
	$db->sql_query("UPDATE ".$prefix."_users SET user_points='0'");
	Header("Location: ".$admin_file.".php?op=user_show");
	break;
	
	case "user_conf":
	user_conf();
	break;
	
	case "user_save":
	user_save();
	break;
	
	case "user_new_delete":
	if (count($_POST['delete'])>0) {
	$db->sql_query("DELETE FROM ".$prefix."_users_temp WHERE user_id IN (".implode(',',$_POST['delete']).")");
	}
	referer($admin_file.".php?op=user_new");
	break;

}
?>