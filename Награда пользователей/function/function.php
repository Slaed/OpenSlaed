<?php
# Copyright © 2005 - 2009 SLAED
# Website: http://www.slaed.net

if (!defined("MODULE_FILE") && !defined("ADMIN_FILE")) die("Illegal File Access");

define("BLOCK_FILE", true);
define("FUNC_FILE", true);

# Security file include
include("function/security.php");

if (defined("MODULE_FILE")) {
	if (file_exists("config/config_function.php")) include("config/config_function.php");
	include("function/user.php");
} elseif (defined("ADMIN_FILE")) {
	include("function/secure.php");
	include("function/admin.php");
}

# Format theme file
function get_theme_file($name) {
	global $home, $conf, $op;
	$theme = get_theme();
	$cat = (isset($_GET['cat'])) ? intval($_GET['cat']) : "";
	if ($home) {
		if (file_exists("templates/".$theme."/".$name."-home.html")) {
			$fname = "".$name."-home";
		} else {
			$fname = $name;
		}
	} elseif (isset($conf['template'])) {
		if (file_exists("templates/".$theme."/".$name."-".$conf['template'].".html")) {
			$fname = "".$name."-".$conf['template']."";
		} else {
			$fname = $name;
		}
	} elseif ($conf['name'] && $op) {
		if (file_exists("templates/".$theme."/".$name."-".$conf['name']."-".$op.".html")) {
			$fname = "".$name."-".$conf['name']."-".$op."";
		} elseif (file_exists("templates/".$theme."/".$name."-".$conf['name'].".html")) {
			$fname = "".$name."-".$conf['name']."";
		} else {
			$fname = $name;
		}
	} elseif ($conf['name'] && $cat) {
		$cat = intval($_GET['cat']);
		if (file_exists("templates/".$theme."/".$name."-".$conf['name']."-cat-".$cat.".html")) {
			$fname = "".$name."-".$conf['name']."-cat-".$cat."";
		} elseif (file_exists("templates/".$theme."/".$name."-".$conf['name'].".html")) {
			$fname = "".$name."-".$conf['name']."";
		} else {
			$fname = $name;
		}
	} elseif ($conf['name']) {
		if (file_exists("templates/".$theme."/".$name."-".$conf['name'].".html")) {
			$fname = "".$name."-".$conf['name']."";
		} else {
			$fname = $name;
		}
	} else {
		$fname = $name;
	}
	$index = (file_exists("templates/".$theme."/".$fname.".html")) ? "templates/".$theme."/".$fname.".html" : 0;
	return $index;
}

# Format Time
function datetime($id, $time="") {
	if ($id == 1) {
		if ($time && !isset($_POST['year'])) {
			$time = $time;
		} else {
			if (isset($_POST['year'])) {
				$time = "".$_POST['year']."-".$_POST['mon']."-".$_POST['day']." ".$_POST['hour'].":".$_POST['min'].":00";
			} else {
				$today = getdate();
				$time = "".$today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":00";
			}
		}
		preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $time, $datetime);
		$content = ""._HOURS.": <select name=\"hour\">";
		$hour = 0;
		while ($hour <= 23) {
			$sel = ($hour == $datetime[4]) ? "selected" : "";
			$hour = ($hour < 10) ? "0".$hour."" : $hour;
			$content .= "<option value=\"$hour\" $sel>$hour</option>";
			$hour++;
		}
		$content .= "</select>:<select name=\"min\">";
		$min = 0;
		while ($min <= 60) {
			$sel = ($min == $datetime[5]) ? "selected" : "";
			$min = ($min < 10) ? "0".$min."" : $min;
			$content .= "<option value=\"$min\" $sel>$min</option>";
			$min++;
		}
		$content .= "</select> "._DAY.": <select name=\"day\">";
		$day = 1;
		while ($day <= 31) {
			$sel = ($day == $datetime[3]) ? "selected" : "";
			$content .= "<option value=\"$day\" $sel>$day</option>";
			$day++;
		}
		$content .= "</select> "._UMONTH.": <select name=\"mon\">";
		$mon = 1;
		while ($mon <= 12) {
			$sel = ($mon == $datetime[2]) ? "selected" : "";
			$content .= "<option value=\"$mon\" $sel>$mon</option>";
			$mon++;
		}
		$content .= "</select> "._YEAR.": <select name=\"year\">";
		$date = getdate();
		$year = $date[year] + 10;
		$years = $date[year] - 10;
		while ($years <= $year) {
			$sel = ($year == $datetime[1]) ? "selected" : "";
			$content .= "<option value=\"$year\" $sel>$year</option>";
			$year--;
		}
		$content .= "</select>";
	} elseif ($id == 2) {
		if ($time && !isset($_POST['dyear'])) {
			$time = $time;
		} else {
			if (isset($_POST['dyear'])) {
				$time = "".$_POST['dyear']."-".$_POST['dmon']."-".$_POST['dday']."";
			} else {
				$today = getdate();
				$time = "".$today['year']."-".$today['mon']."-".$today['mday']."";
			}
		}
		preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $time, $datetime);
		$content = "</select> "._DAY.": <select name=\"dday\">";
		$day = 1;
		while ($day <= 31) {
			$sel = ($day == $datetime[3]) ? "selected" : "";
			$content .= "<option value=\"$day\" $sel>$day</option>";
			$day++;
		}
		$content .= "</select> "._UMONTH.": <select name=\"dmon\">";
		$mon = 1;
		while ($mon <= 12) {
			$sel = ($mon == $datetime[2]) ? "selected" : "";
			$content .= "<option value=\"$mon\" $sel>$mon</option>";
			$mon++;
		}
		$content .= "</select> "._YEAR.": <select name=\"dyear\">";
		$date = getdate();
		$year = $date[year];
		$years = $date[year] - 100;
		while ($years <= $year) {
			$sel = ($year == $datetime[1]) ? "selected" : "";
			$content .= "<option value=\"$year\" $sel>$year</option>";
			$year--;
		}
		$content .= "</select>";
	} else {
		$content = "";
	}
	return $content;
}

# Save Time
function save_datetime() {
	if (isset($_POST['year'])) {
		$content = "".$_POST['year']."-".$_POST['mon']."-".$_POST['day']." ".$_POST['hour'].":".$_POST['min'].":00";
	} else {
		$today = getdate();
		$content = "".$today['year']."-".$today['mon']."-".$today['mday']." ".$today['hours'].":".$today['minutes'].":00";
	}
	return $content;
}

# Save Date
function save_date() {
	if (isset($_POST['dyear'])) {
		$content = "".$_POST['dyear']."-".$_POST['dmon']."-".$_POST['dday']."";
	} else {
		$today = getdate();
		$content = "".$today['year']."-".$today['mon']."-".$today['mday']."";
	}
	return $content;
}

# Format radio form
function radio_form($var, $name, $id="") {
	if ($id == 1) {
		$sel1 = (!$var) ? "checked" : "";
		$sel2 = ($var) ? "checked" : "";
		$content = "<input type=\"radio\" name=\"$name\" value=\"0\" $sel1>"._YES." &nbsp;<input type=\"radio\" name=\"$name\" value=\"1\" $sel2>"._NO."";
	} else {
		$sel1 = ($var) ? "checked" : "";
		$sel2 = (!$var) ? "checked" : "";
		$content = "<input type=\"radio\" name=\"$name\" value=\"1\" $sel1>"._YES." &nbsp;<input type=\"radio\" name=\"$name\" value=\"0\" $sel2>"._NO."";
	}
	return $content;
}

# Format gender
function gender($gender, $id) {
	if ($id == 1) {
		if ($gender == 2) {
			$gen = ""._WOMAN."";
		} elseif ($gender == 1) {
			$gen = ""._MAN."";
		} else {
			$gen = "<i>"._NO_INFO."</i>";
		}
	} elseif ($id == 2) {
		if ($gender == 2) {
			$gen = "<font style=\"display: none;\">$gender</font><img src=\"".img_find("all/woman")."\" border=\"0\" align=\"center\" width=\"16\" height=\"16\" title=\""._WOMAN."\" alt=\""._WOMAN."\">";
		} elseif ($gender == 1) {
			$gen = "<font style=\"display: none;\">$gender</font><img src=\"".img_find("all/man")."\" border=\"0\" align=\"center\" width=\"16\" height=\"16\" title=\""._MAN."\" alt=\""._MAN."\">";
		} else {
			$gen = "";
		}
	} else {
		$gen = "";
	}
	return $gen;
}

# Format attach
function encode_attach($sourse, $mod) {
	include("config/config_uploads.php");
	$match_count = preg_match_all("#\[attach=([a-zA-Zà-ÿÀ-ß0-9\_\-\. ]+) align=([a-zA-Zà-ÿÀ-ß0-9\_\-\.\"\ ]+) title=([a-zA-Zà-ÿÀ-ß0-9\_\-\.\"\ ]+)\]#si", $sourse, $date);
	$con = explode("|", $confup[$mod]);
	$file = "";
	for ($i = 0; $i < $match_count; $i++) {
		$file = "uploads/".$mod."/".$date[1][$i]."";
		$tfile = "uploads/".$mod."/thumb/".$date[1][$i]."";
		$dtfile = "uploads/".$mod."/thumb";
		if ($mod != "" && file_exists($file) && !file_exists($tfile)) {
			if (!file_exists($dtfile)) mkdir($dtfile);
			$thumb = create_img_gd($file, $tfile, $con[6]);
			$timg = ($thumb) ? $tfile : $file;
		} else {
			$timg = $tfile;
		}
		$img = $file;
		if (file_exists($img)) list($imgwidth, $imgheight) = getimagesize($img);
		$cont[] = "<img src=\"".$timg."\" width=\"".$con[6]."\" align=\"".$date[2][$i]."\" style=\"cursor: pointer;\" OnMouseOver=\"Tip('<img src=&quot;".$img."&quot; width=&quot;".$imgwidth."&quot;>')\" OnClick=\"CaricaFoto('".$img."')\">";
		$text = preg_replace($date[0], $cont, $sourse);
	}
	$sourse = preg_replace("#\[(.*?)\]#", "\\1", $text);
	return $sourse;
}

# Format search highlight
function search_color($sourse, $word) {
	global $conf;
	$word = urldecode($word);
	if ($word) {
		if (strstr($word, " ")) {
			$warray = explode(" ", str_replace("  ", " ", $word));
		} else {
			$warray[] = $word;
		}
		preg_match_all("#<[^>]*>#", $sourse, $tags);
		array_unique($tags);
		$taglist = array();
		$k = 0;
		foreach($tags[0] as $i) {
			$k++;
			$taglist[$k] = $i;
			$sourse = str_replace($i, "<".$k.">", $sourse);
		}
		foreach($warray as $i) if (!is_numeric($i)) $sourse=preg_replace("#".$i."#i", "<font style=\"background-color: #FFFF00; color: #FF0000;\">$0</font>", $sourse);
		foreach($taglist as $k => $i) $sourse = str_replace("<" . $k . ">", $i, $sourse);
	}
	return $sourse;
}

# Replace break
function replace_break($text) {
	global $conf;
	$out = (($conf['redaktor'] == 1 && defined('ADMIN_FILE')) || (!defined('ADMIN_FILE'))) ? preg_replace("/<br>|<br \/>/", "", $text) : $text;
	return $out;
}

# Mail send
function mail_send($email, $smail, $subject, $message, $id="", $pr="") {
	$email = text_filter($email);
	$smail = text_filter($smail);
	$subject = text_filter($subject);
	$id = intval($id);
	$pr = (!$pr) ? "3" : "".intval($pr)."";
	$message = (!$id) ? "".$message."" : "".$message."<br><br>"._IP.": ".getip()."<br>"._BROWSER.": ".getagent()."<br>"._HASH.": ".md5(getagent())."";
	$mheader = "MIME-Version: 1.0\n"
	."Content-Type: text/html; charset="._CHARSET."\n"
	."Content-Transfer-Encoding: 8bit\n"
	."Reply-To: \"$smail\" <$smail>\n"
	."From: \"$smail\" <$smail>\n"
	."Return-Path: <$smail>\n"
	."X-Priority: $pr\n"
	."X-Mailer: Open SLAED Mailer\n";
	mail($email, $subject, $message, $mheader);
}

# User country information
function user_geo_ip($ip, $id) {
	global $conf;
	if ((phpversion() >= "5") && $conf['geo_ip'] && $ip) {
		include_once("function/geo_ip.php");
		$geoip = geo_ip::getInstance("function/geo_ip.dat");
		if ($id == 1) {
			$cont = $geoip->lookupCountryCode($ip);
		} elseif ($id == 2) {
			$cont = $geoip->lookupCountryName($ip);
		} elseif ($id == 3) {
			$name = $geoip->lookupCountryName($ip);
			$img = str_replace(" ", "_", strtolower($name));
			if (file_exists(img_find("language/".$img.""))) {
				$cont = "<img src=\"".img_find("language/".$img."")."\" border=\"0\" align=\"center\" alt=\"".$name."\" title=\"".$name."\">";
			} else {
				$cont = "<img src=\"".img_find("all/question")."\" border=\"0\" align=\"center\" alt=\"".$name."\" title=\"".$name."\">";
			}
		} elseif ($id == 4) {
			$name = $geoip->lookupCountryName($ip);
			$img = str_replace(" ", "_", strtolower($name));
			if (file_exists(img_find("language/".$img.""))) {
				$cont = "<img src=\"".img_find("language/".$img."")."\" border=\"0\" align=\"center\" alt=\"".$name."\" title=\"".$name."\"> <a href=\"".$conf['ip_link']."".$ip."\" title=\""._IP.": $ip\" target=\"_blank\">$ip</a>";
			} else {
				$cont = "<img src=\"".img_find("all/question")."\" border=\"0\" align=\"center\" alt=\"".$name."\" title=\"".$name."\"> <a href=\"".$conf['ip_link']."".$ip."\" title=\""._IP.": $ip\" target=\"_blank\">$ip</a>";
			}
		}
		return $cont;
	} else {
		return;
	}
}

# User information for user
function user_sinfo() {
	global $prefix, $db, $conf;
	if ($conf['session']) {
		$who_online = ""; $m = 0; $b = 0; $u = 0; $i = 0;
		$result = $db->sql_query("SELECT uname, UNIX_TIMESTAMP(now())-time AS time, host_addr, guest, module FROM ".$prefix."_session ORDER BY uname");
		while (list($uname, $time, $host, $guest, $module) = $db->sql_fetchrow($result)) {
			$strip = cutstr($uname, 10);
			$linkstrip = str_replace("_", " ", cutstr($module, 7));
			if ($guest == 2) {
				$who_online .= "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"index.php?name=account&op=info&uname=$uname\" title=\"".display_time($time)."\">$strip</a></td><td align=\"right\">$linkstrip</td></tr>";
				$m++;
			} elseif ($guest == 1 && $conf['botsact']) {
				$who_online .= "<tr><td>".user_geo_ip($host, 3)."</td><td title=\"".display_time($time)."\">$strip</td><td align=\"right\">$linkstrip</td></tr>";
				$b++;
			} else {
				$who_online .= "";
				$u++;
			}
			$i++;
		}
		$content = "<hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">"
		."<tr><td><img src=\"".img_find("all/member")."\" title=\""._BMEM."\" alt=\""._BMEM."\"></td><td>"._BMEM.":</td><td align=\"right\">$m</td></tr>";
		if ($conf['botsact']) $content .= "<tr><td><img src=\"".img_find("all/bots")."\" title=\""._BOTS."\" alt=\""._BOTS."\"></td><td>"._BOTS.":</td><td align=\"right\">$b</td></tr>";
		$content .= "<tr><td><img src=\"".img_find("all/anony")."\" title=\""._BVIS."\" alt=\""._BVIS."\"></td><td>"._BVIS.":</td><td align=\"right\">$u</td></tr></table>";
		if ($who_online) {
			$content .= "<hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/refresh")."\" style=\"cursor: pointer;\" OnClick=\"ajax('ajax.php?go=6&op=user_sinfo', 'sinfo');\" border=\"0\" alt=\""._UPDATE."\" title=\""._UPDATE."\"></td><td><span id=\"cont\" OnClick=\"SwitchMenu('usbl')\" style=\"cursor: pointer;\"><img src=\"".img_find("all/plus")."\" title=\""._READMORE."\" alt=\""._READMORE."\"></span></td><td><img src=\"".img_find("all/group")."\" title=\""._OVERALL."\" alt=\""._OVERALL."\"></td><td width=\"80%\">"._OVERALL.":</td><td align=\"right\">$i</td></tr></table>"
			."<div id=\"usbl\" style=\"display: none;\"><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">".$who_online."</table></div>";
		} else {
			$content .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/refresh")."\" style=\"cursor: pointer;\" OnClick=\"ajax('ajax.php?go=6&op=user_sinfo', 'sinfo');\" border=\"0\" alt=\""._UPDATE."\" title=\""._UPDATE."\"></td><td><img src=\"".img_find("all/group")."\" title=\""._OVERALL."\" alt=\""._OVERALL."\"></td><td width=\"80%\">"._OVERALL.":</td><td align=\"right\">".$i."</td></tr></table>";
		}
		echo $content;
	}
}

# User information for admin
function user_sainfo() {
	global $prefix, $db, $conf;
	if ($conf['session'] && is_admin()) {
		$a = 0; $b = 0; $m = 0; $u = 0; $i = 0;
		$who_online = array("0" => "", "1" => "", "2" => "", "3" => "");
		$content_who = "";
		$result = $db->sql_query("SELECT uname, UNIX_TIMESTAMP(now())-time AS time, host_addr, guest, module, url FROM ".$prefix."_session ORDER BY uname");
		while (list($s_uname, $s_time, $host, $s_guest, $s_module, $s_url) = $db->sql_fetchrow($result)) {
			$namestrip = cutstr($s_uname, 10);
			$linkstrip = cutstr($s_module, 7);
			$linkstrip_admin = cutstr($s_url, 7);
			$guest = intval($s_guest);
			if ($guest == 3) {
				$title_who = "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"".$conf['ip_link']."".$host."\" title=\"".display_time($s_time)." - "._IP.": $host\" target=\"_blank\">$namestrip</a></td><td align=\"right\"><a href=\"$s_url\" title=\"$s_url\" target=\"_blank\">$linkstrip_admin</a></td></tr>";
				$a++;
			} elseif ($guest == 2) {
				if ($linkstrip != "") {
					$title_who = "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"index.php?name=account&op=info&uname=$s_uname\" title=\"".display_time($s_time)." - "._IP.": $host\" target=\"_blank\">$namestrip</a></td><td align=\"right\"><a href=\"$s_url\" title=\"$s_url\" target=\"_blank\">$linkstrip</a></td></tr>";
					$m++;
				} else {
					$title_who = "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"index.php?name=account&op=info&uname=$s_uname\" title=\"".display_time($s_time)." - "._IP.": $host\" target=\"_blank\">$namestrip</a></td><td align=\"right\"><a href=\"$s_url\" title=\"$s_url\" target=\"_blank\">$linkstrip_admin</a></td></tr>";
				}
			} elseif ($guest == 1) {
				$title_who = "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"".$conf['ip_link']."".$host."\" title=\"".display_time($s_time)." - "._IP.": $host\" target=\"_blank\">$namestrip</a></td><td align=\"right\"><a href=\"$s_url\" title=\"$s_url\" target=\"_blank\">$linkstrip</a></td></tr>";
				$b++;
			} else {
				$title_who = "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"".$conf['ip_link']."".$host."\" title=\"".display_time($s_time)."\" target=\"_blank\">$s_uname</a></td><td align=\"right\"><a href=\"$s_url\" title=\"$s_url\" target=\"_blank\">$linkstrip</a></td></tr>";
				$u++;
			}
			$who_online[$guest] .= $title_who;
			$i++;
		}
		if (is_admin_god()) {
			$content_who .= "<table id=\"cont\" OnClick=\"SwitchMenu('a".$a."')\" style=\"cursor: pointer;\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/plus")."\" title=\""._READMORE."\" alt=\""._READMORE."\"></td><td><img src=\"".img_find("all/admin")."\" title=\""._ADMINS."\" alt=\""._ADMINS."\"></td><td width=\"80%\">"._ADMINS.":</td><td align=\"right\">$a</td></tr></table>"
			."<div id=\"a".$a."\" style=\"display: none;\"><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">".$who_online[3]."</table><hr></div>";
		}
		$content_who .= "<table id=\"cont\" OnClick=\"SwitchMenu('m".$m."')\" style=\"cursor: pointer;\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/plus")."\" title=\""._READMORE."\" alt=\""._READMORE."\"></td><td><img src=\"".img_find("all/member")."\" title=\""._BMEM."\" alt=\""._BMEM."\"></td><td width=\"80%\">"._BMEM.":</td><td align=\"right\">$m</td></tr></table>"
		."<div id=\"m".$m."\" style=\"display: none;\"><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">".$who_online[2]."</table><hr></div>";
		$content_who .= "<table id=\"cont\" OnClick=\"SwitchMenu('b".$b."')\" style=\"cursor: pointer;\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/plus")."\" title=\""._READMORE."\" alt=\""._READMORE."\"></td><td><img src=\"".img_find("all/bots")."\" title=\""._BOTS."\" alt=\""._BOTS."\"></td><td width=\"80%\">"._BOTS.":</td><td align=\"right\">$b</td></tr></table>"
		."<div id=\"b".$b."\" style=\"display: none;\"><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">".$who_online[1]."</table><hr></div>";
		$content_who .= "<table id=\"cont\" OnClick=\"SwitchMenu('u".$u."')\" style=\"cursor: pointer;\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/plus")."\" title=\""._READMORE."\" alt=\""._READMORE."\"></td><td><img src=\"".img_find("all/anony")."\" title=\""._BVIS."\" alt=\""._BVIS."\"></td><td width=\"80%\">"._BVIS.":</td><td align=\"right\">$u</td></tr></table>"
		."<div id=\"u".$u."\" style=\"display: none;\"><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">".$who_online[0]."</table></div>";
		$content_who .= "<hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/refresh")."\" style=\"cursor: pointer;\" OnClick=\"ajax('ajax.php?go=5&op=user_sainfo', 'sainfo');\" border=\"0\" alt=\""._UPDATE."\" title=\""._UPDATE."\"></td><td><img src=\"".img_find("all/group")."\" title=\""._OVERALL."\" alt=\""._OVERALL."\"></td><td width=\"80%\">"._OVERALL.":</td><td align=\"right\">$i</td></tr></table>";
		echo $content_who;
	}
}

# Format admin block
function adminblock() {
	global $prefix, $db, $conf, $admin_file;
	if (is_admin()) {
		$a_content = "<table><tr><td><img src=\"".img_find("misc/navi")."\" border=\"0\"></td><td><a href=\"".$admin_file.".php\" title=\""._HOME."\">"._HOME."</a></td></tr>"
		."<tr><td><img src=\"".img_find("misc/navi")."\" border=\"0\"></td><td><a href=\"".$admin_file.".php?op=logout\"  title=\""._LOGOUT."\">"._LOGOUT."</a></td></tr></table>";
		if (is_admin_god()) {
			list($title, $content) = $db->sql_fetchrow($db->sql_query("SELECT title, content FROM ".$prefix."_blocks WHERE bkey='admin'"));
			$a_content .= $content;
		}
		$a_title = ($title) ? $title : ""._ADMINS."";
		themesidebox($a_title, $a_content, 5);
		themesidebox(""._WHO."", "<script type=\"text/javascript\">ajax('ajax.php?go=5&op=user_sainfo', 'sainfo');</script><div id=\"sainfo\"></div> ", 6);
	}
}

# View article
function view_article($mod, $id, $com="") {
	$com = ($com) ? "#".$com."" : "";
	if ($mod) {
		$link = "index.php?name=".$mod."&op=view&id=".$id."".$com."";
	} else {
		$link = "";
	}
	return $link;
}

# User info link
function user_info($name, $id) {
	if ($name && $id == 1) {
		$link = "<a href=\"index.php?name=account&op=info&uname=".urlencode($name)."\" title=\""._PERSONALINFO."\">$name</a>";
	} elseif ($name && $id == 2) {
		$link = "<a href=\"index.php?name=account&op=info&uname=".urlencode($name)."\" title=\""._PERSONALINFO."\"><img src=\"".img_find("all/about")."\" border=\"0\" align=\"center\" width=\"16\" height=\"16\" alt=\""._PERSONALINFO."\"></a>";
	} else {
		$link = "";
	}
	return $link;
}

# Format vote graphic
function vote_graphic($votes, $total) {
	$votes = (intval($votes)) ? $votes : 1;
	$width = number_format($total / $votes, 2) * 17;
	$result = substr($total / $votes, 0, 4);
	$title = (intval($votes) && intval($total)) ? "title=\""._REITING.": $result/$votes "._AVERAGESCORE.": $result\"" : "title=\""._REITING.": 0/0 "._AVERAGESCORE.": 0\"";
	$content ="<font style=\"display: none;\">$result</font><ul class=\"urating\" ".$title."><li class=\"crating\" style=\"width: ".$width."px;\"></li></ul>";
	return $content;
}

# Format ajax rating
function ajax_rating($typ, $id, $mod, $rat, $scor) {
	include("config/config_ratings.php");
	$con = explode("|", $confra[strtolower($mod)]);
	if (($con[1] && $id && $mod) || ($rat && $scor)) {
		$content = (($con[1] &&$typ) || ($con[1] && !$con[2] && !$typ)) ? "<script type=\"text/javascript\">ajax('ajax.php?go=1&op=rating&mod=".$mod."&id=$id', 'rate".$id."');</script><div class=\"rate\" id=\"rate".$id."\"></div>" : "<div class=\"rate\">".vote_graphic($rat, $scor)."</div>";
		return $content;
	}
}

# Show editor files
function show_files() {
	global $user;
	include("config/config_uploads.php");
	$id = intval($_GET['id']);
	$dir = strtolower($_GET['mod']);
	$typ = intval($_GET['typ']);
	$con = explode("|", $confup[$dir]);
	$connum = ($con[7]) ? $con[7] : "50";
	$file = $_GET['file'];
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	if ($typ == 1 && is_moder()) {
		$path = ($id == 1) ? "uploads/".$dir."/" : "uploads/".$dir."/thumb/";
		if ($file && $dir) unlink($path.$file);
		$dh = opendir($path);
		while ($entry = readdir($dh)) {
			if ($entry != "." && $entry != ".." && $entry != "index.html" && preg_match("/\./", $entry)) {
				$files[] = array(filemtime($path.$entry), $entry);
			}
		}
		closedir($dh);
		if ($files) {
			rsort($files);
			foreach ($files as $entry) {
				preg_match("/([a-zA-Z0-9]+)\-([a-zA-Z0-9]+)\-([0-9]+)\.([a-zA-Z0-9]+)/", $entry[1], $date);
				$uname = ($user[0]) ? intval($user[0]) : 0;
				if (($uname == $date[3] && $date[2] && $date[1]) || is_moder()) {
					$filesize = filesize($path.$entry[1]);
					list($imgwidth, $imgheight) = getimagesize($path.$entry[1]);
					$show = ($imgwidth && $imgheight) ? "<img src=\"".img_find("all/view")."\" align=\"center\" style=\"cursor: pointer;\" OnMouseOver=\"Tip('<img src=&quot;".$path."".$entry[1]."&quot; width=&quot;".$imgwidth."&quot;>')\" OnClick=\"CaricaFoto('".$path."".$entry[1]."')\"> <img src=\"".img_find("all/delete")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=".$dir."&id=".$id."&file=".$entry[1]."&typ=1', 'f".$id."');\" alt=\""._DELETE."\" title=\""._DELETE."\">" : "<img src=\"".img_find("all/delete")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=".$dir."&id=".$id."&file=".$entry[1]."&typ=1', 'f".$id."');\" alt=\""._DELETE."\" title=\""._DELETE."\">";
					$img = ($imgwidth && $imgheight) ? "".$imgwidth." x ".$imgheight."" : ""._NO."";
					$contents[] = "<tr class=\"bgcolor1\"><td align=\"center\">".$show."</td><td>".$path."".$entry[1]."</td><td align=\"center\">".date ("d.m.Y H:i:s", $entry[0])."</td><td align=\"center\">".files_size($filesize)."</td><td align=\"center\">".$img."</td></tr>";
					$a++;
				}
			}
		}
		$numpages = ceil($a / $connum);
		$offset = ($num - 1) * $connum;
		$tnum = ($offset) ? $connum + $offset : $connum;
		for ($i = $offset; $i < $tnum; $i++) {
			if ($contents[$i] != "") {
				$cont .= "".$contents[$i]."";
			}
		}
		$contnum = ($a > $connum) ? num_ajax($a, $numpages, $connum, "go=3&op=show_files&mod=$dir&id=$id&typ=1&") : "";
		$content = ($cont) ? "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"bgcolor4\"><tr><th>"._FUNCTIONS."</th><th>"._FILE."</th><th>"._DATE."</th>"
		."<th>"._SIZE."</th><th>"._WIDTH." x "._HEIGHT."</th></tr>".$cont."</table>".$contnum."" : "";
		open();
		echo $content;
		close();
	} else {
		$path = "uploads/".$dir."/";
		if (is_moder($dir) && $file && $dir) unlink($path.$file);
		$dh = opendir($path);
		while ($entry = readdir($dh)) {
			if ($entry != "." && $entry != ".." && $entry != "index.html" && preg_match("/\./", $entry)) {
				$files[] = array(filemtime($path.$entry), $entry);
			}
		}
		closedir($dh);
		if ($files) {
			rsort($files);
			foreach ($files as $entry) {
				preg_match("/([a-zA-Z0-9]+)\-([a-zA-Z0-9]+)\-([0-9]+)\.([a-zA-Z0-9]+)/", $entry[1], $date);
				$uname = ($user[0]) ? intval($user[0]) : 0;
				if (($uname == $date[3] && $date[2] && $date[1]) || is_moder($dir)) {
					$filesize = filesize($path.$entry[1]);
					list($imgwidth, $imgheight) = getimagesize($path.$entry[1]);
					$show = ($imgwidth && $imgheight) ? "<img src=\"".img_find("all/view")."\" align=\"center\" style=\"cursor: pointer;\" OnMouseOver=\"Tip('<img src=&quot;".$path."".$entry[1]."&quot; width=&quot;".$imgwidth."&quot;>')\" OnClick=\"CaricaFoto('".$path."".$entry[1]."')\"> <img src=\"".img_find("editor/export")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"InsertCode('attach', '".$entry[1]."', '', '', '".$id."')\" alt=\""._INSERT." ".$imgwidth." x ".$imgheight."\"  title=\""._INSERT." ".$imgwidth." x ".$imgheight."\"> <img src=\"".img_find("editor/img")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"InsertCode('img', '".$path."".$entry[1]."', '', '', '".$id."')\" alt=\""._EIMG." ".$imgwidth." x ".$imgheight."\"  title=\""._EIMG." ".$imgwidth." x ".$imgheight."\"> " : "<img src=\"".img_find("editor/export")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"InsertCode('attach', '".$path."".$entry[1]."', '', '', '".$id."')\" alt=\""._EIMG."\"  title=\""._EIMG."\"> ";
					$show .= (is_moder($dir)) ? "<img src=\"".img_find("all/delete")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=".$dir."&id=".$id."&file=".$entry[1]."', 'f".$id."');\" alt=\""._DELETE."\" title=\""._DELETE."\">" : "";
					$contents[] = "<tr class=\"bgcolor1\"><td align=\"center\">".$show."</td><td>".$entry[1]."</td><td align=\"center\">".files_size($filesize)."</td></tr>";
					$a++;
				}
			}
		}
		$numpages = ceil($a / $connum);
		$offset = ($num - 1) * $connum;
		$tnum = ($offset) ? $connum + $offset : $connum;
		for ($i = $offset; $i < $tnum; $i++) {
			if ($contents[$i] != "") {
				$cont .= "".$contents[$i]."";
			}
		}
		$contnum = ($a > $connum) ? num_ajax($a, $numpages, $connum, "go=3&op=show_files&mod=$dir&id=$id&") : "";
		$content = ($cont) ? "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\"><tr><td><table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"bgcolor4\"><tr><th>"._FUNCTIONS."</th><th>"._FILE."</th><th>"._SIZE."</th></tr>".$cont."</table>".$contnum."</td></tr></table>" : "";
		echo $content;
	}
}

# Format Nummer Page
function num_ajax($numstories, $numpages, $storynum, $module_link="") {
	global $admin_file;
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$id = intval($_GET['id']);
	if ($numpages > 1) {
		$content = "<div align=\"center\" class=\"pagelink\"><h4>"._OVERALL." $numstories "._ON." $numpages "._PAGE_S." $storynum "._PERPAGE."</h4>";
		if ($num > 1) {
			$prevpage = $num - 1;
			$content .= "<span OnClick=\"ajax('ajax.php?".$module_link."num=$prevpage', 'f".$id."');\" title=\"&lt;&lt;\">&lt;&lt;</span> ";
		}
		for ($i = 1; $i < $numpages+1; $i++) {
			if ($i == $num) {
				$content .= "<span title=\"$i\">$i</span>";
			} else {
				if ((($i > ($num - 8)) && ($i < ($num + 8))) OR ($i == $numpages) || ($i == 1)) $content .= "<span OnClick=\"ajax('ajax.php?".$module_link."num=$i', 'f".$id."');\" title=\"$i\">$i</span>";
			}
			if ($i < $numpages) {
				if (($i > ($num - 9)) && ($i < ($num + 8))) $content .= " ";
				if (($num > 9) && ($i == 1)) $content .= " <span>...</span>";
				if (($num < ($numpages - 8)) && ($i == ($numpages - 1))) $content .= "<span>...</span> ";
			}
		}
		if ($num < $numpages) {
			$nextpage = $num + 1;
			$content .= " <span OnClick=\"ajax('ajax.php?".$module_link."num=$nextpage', 'f".$id."');\" title=\"&gt;&gt;\">&gt;&gt;</span>";
		}
		$content .= "</div>";
		return $content;
	}
}

# Add downloads
function stream($url, $name) {
	Header("Content-Type: application/force-download");
	Header("Content-Range: bytes");
	Header("Content-Length: ".filesize($url)."");
	Header("Content-Disposition: attachment; filename=".$name."");
	readfile($url);
}

# Anti spam
function anti_spam($mail) {
	preg_match("/^(.*?)(@)(.*?)$/", $mail, $info);
	$content = "<script type=\"text/javascript\">
	String.prototype.AddMail = function (prefix, postfix) { 
		hamper = prefix+\"@\"+postfix;
		document.write((hamper).link(\"mailto:\"+hamper));
	}
	</script>";
	$content .= "<script type=\"text/javascript\">\"mysi\".AddMail('".$info[1]."', '".$info[3]."');</script>"
	."<noscript>".$info[1]."<!-- slaed --><span>&#64;</span><!-- slaed -->".$info[3]."</noscript>";
	return $content;
}

# Format letter
function letter($mod) {
	$content = "<div class=\"letter\"><a href=\"index.php?name=".$mod."&op=liste\" title=\""._ALL."\">"._ALL."</a> ";
	foreach(range(0, 9) as $num) $content .= " | <a href=\"index.php?name=$mod&op=liste&let=$num\" title=\"$num\">$num</a>";
	if (substr(""._LOCALE."", 0, 2) == "ru") {
		$content .= "</div><div class=\"letter\"><a href=\"index.php?name=".$mod."&op=liste\" title=\""._ALL."\">"._ALL."</a> ";
		foreach(range("À", "ß") as $rus) {
			$rus = iconv("cp1251", "utf-8", $rus);
			$content .= " | <a href=\"index.php?name=$mod&op=liste&let=".urlencode($rus)."\" title=\"$rus\">$rus</a>";
		}
	}
	$content .= "</div><div class=\"letter\"><a href=\"index.php?name=".$mod."&op=liste\" title=\""._ALL."\">"._ALL."</a> ";
	foreach(range("A", "Z") as $eng) $content .= " | <a href=\"index.php?name=$mod&op=liste&let=$eng\" title=\"$eng\">$eng</a>";
	$content .= "</div>";
	echo $content;
}

# Admin status
function ad_status($link, $id) {
	if ($link) {
		$content = ($id == 1) ? "<a href=\"".$link."\" title=\""._DEACTIVATE."\"><img src=\"".img_find("all/activate")."\" border=\"0\" align=\"center\" alt=\""._DEACTIVATE."\"></a>" : "<a href=\"".$link."\" title=\""._ACTIVATE."\"><img src=\"".img_find("all/inactive")."\" border=\"0\" align=\"center\" alt=\""._ACTIVATE."\"></a>";
	} else {
		$content = ($id == 1) ? "<img src=\"".img_find("all/activate")."\" border=\"0\" align=\"center\" alt=\""._ACT."\" title=\""._ACT."\">" : "<img src=\"".img_find("all/inactive")."\" border=\"0\" align=\"center\" alt=\""._DEACT."\" title=\""._DEACT."\">";
	}
	return $content;
}

# Admin edit
function ad_edit($link) {
	return "<a href=\"".$link."\" title=\""._EDIT."\"><img src=\"".img_find("all/edit")."\" border=\"0\" align=\"center\" alt=\""._EDIT."\"></a>";
}

# Admin delete
function ad_delete($link, $title) {
	return "<a href=\"".$link."\" OnClick=\"return DelCheck(this, '"._DELETE." &quot;$title&quot;?');\" title=\""._DELETE."\"><img src=\"".img_find("all/delete")."\" border=\"0\" align=\"center\" alt=\""._DELETE."\"></a>";
}

# Admin delete
function ad_bann($link, $title) {
	return "<a href=\"".$link."\" OnClick=\"return DelCheck(this, '"._BANIPSENDER." &quot;$title&quot;?');\" title=\""._BANIPSENDER."\"><img src=\"".img_find("all/close")."\" border=\"0\" align=\"center\" alt=\""._BANIPSENDER."\"></a>";
}

# Admin brocen
function ad_broc($link) {
	return "<a href=\"".$link."\" title=\""._IGNORE."\"><img src=\"".img_find("all/warning")."\" border=\"0\" align=\"center\" alt=\""._IGNORE."\"></a>";
}

# Add print
function ad_print($link) {
	return "<a href=\"".$link."\"><img src=\"".img_find("all/print")."\" border=\"0\" alt=\""._PRINTER."\" title=\""._PRINTER."\" align=\"center\"></a>";
}

# Add print
function ad_view($link) {
	return "<a href=\"".$link."\"><img src=\"".img_find("all/view")."\" border=\"0\" alt=\""._SHOW."\" title=\""._SHOW."\" align=\"center\"></a>";
}

# Add mailto
function mailto($mail) {
	global $conf;
	return "<a href=\"mailto:".$mail."?subject=".$conf['sitename']."\" target=\"_blank\">".$mail."</a>";
}

# Find img
function img_find($img) {
	$theme = get_theme();
	if (file_exists("templates/".$theme."/images/".$img.".png")) {
		$img = "templates/".$theme."/images/".$img.".png";
	} elseif (file_exists("templates/".$theme."/images/".$img.".gif")) {
		$img = "templates/".$theme."/images/".$img.".gif";
	} elseif (file_exists("templates/".$theme."/images/".$img.".jpg")) {
		$img = "templates/".$theme."/images/".$img.".jpg";
	} elseif (file_exists("images/".$img.".png")) {
		$img = "images/".$img.".png";
	} elseif (file_exists("images/".$img.".gif")) {
		$img = "images/".$img.".gif";
	} else {
		$img = "images/".$img.".jpg";
	}
	return $img;
}

# Format num article
function num_article($mod, $num, $field, $sid, $table, $cat, $order) {
	global $prefix, $db, $conf, $currentlang;
	if (!defined("ADMIN_FILE")) {
		if ($conf['multilingual']) {
			$where = "WHERE modul='$mod' AND (language='$currentlang' OR language='')";
		} else {
			$where = "WHERE modul='$mod'";
		}
		$result = $db->sql_query("SELECT id FROM ".$prefix."_categories ".$where." ORDER BY id");
		$a = 0;
		while (list($cid) = $db->sql_fetchrow($result)) {
			if ($a == 0) {
				$catid = $cid;
				$a++;
			} else {
				$catid .= ",".$cid."";
			}
		}
		if ($catid) {
			$where = "WHERE ".$cat." IN (".$catid.") AND ".$order."";
		} else {
			$where = "WHERE ".$order."";
		}
	} else {
		$where = "WHERE ".$order."";
	}
	list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(".$sid.") FROM ".$prefix."".$table." ".$where.""));
	$numpages = ceil($numstories / $num);
	num_page($mod, $numstories, $numpages, $num, "".$field."");
}

# Format select RSS
function rss_select() {
	global $conf;
	include("config/config_rss.php");
	$fieldc = explode("||", $confrs['rss']);
	$content = "";
	foreach ($fieldc as $val) {
		if ($val != "") {
			preg_match("#(.*)\|(.*)\|(.*)#i", $val, $out);
			if ($out[1] != "0" && $out[2] != "0") {
				$sel = ($_POST['url'] == $out[2]) ? "selected" : "";
				$link = (!preg_match("#http\:\/\/#i", $out[2])) ? "".$conf['homeurl']."/".$out[2]."" : $out[2];
				$content .= "<option value=\"".$link."\" $sel>".$out[1]."</option>";
			}
		}
	}
	return $content;
}

# Read RSS
function rss_read($url, $id) {
	if ($url) {
		include("config/config_rss.php");
		$url = (!preg_match("#http\:\/\/#i", $url)) ? "http://$url" : $url;
		$content = @file_get_contents($url);
		preg_match("#encoding=\"(.*)\"#i", $content, $val);
		if (strtolower($val[1]) == "windows-1251") $content = iconv("cp1251", _CHARSET, $content);
		if ($content) {
			$title = parse_url($url);
			$title = $title['host'];
			preg_match_all("#<item>(.*)</item>#Uism", $content, $items, PREG_PATTERN_ORDER);
			if ($items[1]) {
				$number = ($confrs['max'] > count($items[1])) ? count($items[1]) : $confrs['max'];
				$cont = "";
				for ($i = 0; $i < $number; $i++) {
					preg_match("#<title>(.*)</title>#Uism", $items[1][$i], $rss_title);
					preg_match("#<pubDate>(.*)</pubDate>#Uism", $items[1][$i], $rss_date);
					preg_match("#<guid>(.*)</guid>(.*)#Uism", $items[1][$i], $rss_guid);
					preg_match("#<description>(.*)</description>#Uism", $items[1][$i], $rss_desc);
					$temp = $confrs['temp'];
					$temp = str_replace("[title]", $rss_title[1], $temp);
					$temp = str_replace("[date]", date(""._DATESTRING."", strtotime($rss_date[1])), $temp);
					$temp = str_replace("[guid]", $rss_guid[1], $temp);
					$temp = str_replace("[description]", text_filter(html_entity_decode(str_replace("]]>", "", $rss_desc[1]))), $temp);
					$cont .= $temp;
				}
				$cont = ($id) ? $cont : "<h2>"._RSS_FROM.": <a href=\"".$url."\" target=\"_blank\" title=\""._RSS_FROM.": ".$title."\">".$title."</a></h2>".$cont."";
			} else {
				$cont = ($id) ? "" : "".warning(""._RSS_PROBLEM."", "", "", 1)."";
			}
		} else {
			$cont = ($id) ? "" : "".warning(""._RSS_PROBLEM."", "", "", 1)."";
		}
		return $cont;
	}
}

# Load RSS
function rss_load($bid) {
	global $prefix, $db;
	$bid = intval($bid);
	list($title, $content, $url, $refresh, $otime) = $db->sql_fetchrow($db->sql_query("SELECT title, content, url, refresh, time FROM ".$prefix."_blocks WHERE bid='$bid'"));
	$past = time() - $refresh;
	if ($otime < $past) {
		$btime = time();
		$content = rss_read($url, 1);
		$db->sql_query("UPDATE ".$prefix."_blocks SET content='$content', time='$btime' WHERE bid='$bid'");
	}
	themesidebox($title, $content);
}

# Preview
function preview() {
	$arg = func_get_args();
	$fields1 = ($arg[1]) ? "<br><br>".bb_decode($arg[1], $arg[4])."" : "";
	$fields2 = ($arg[2]) ? "<br><br>".bb_decode($arg[2], $arg[4])."" : "";
	$fields3 = ($arg[3]) ? "<br><br>".fields_out(bb_decode($arg[3], $arg[4]), $arg[4])."" : "";
	open();
	echo "<fieldset><legend>"._PREVIEW."</legend><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\"><tr><td><b>".$arg[0]."</b>".$fields1."".$fields2."".$fields3."</td></tr></table></fieldset>";
	close();
}

# Fields in
function fields_in($fieldb, $mod) {
	include("config/config_fields.php");
	$mod = strtolower($mod);
	$style = (defined("ADMIN_FILE")) ? "admin" : $mod;
	$fieldc = $conffi[$mod];
	if (!isset($_POST['field'])) {
		$fieldb = $fieldb;
	} else {
		$fieldb = fields_save($_POST['field']);
	}
	$fieldb = explode("|", $fieldb);
	$fieldc = explode("||", $fieldc);
	$i = 0;
	foreach ($fieldc as $val) {
		if ($val != "") {
			preg_match("#(.*)\|(.*)\|(.*)\|(.*)#i", $val, $out);
			if ($out[1] != "0") {
				$fieldin = ($fieldb[$i]) ? $fieldb[$i] : $out[2];
				if ($out[3] == 1) {
					$field = "<input type=\"text\" name=\"field[]\" value=\"".$fieldin."\" size=\"65\" class=\"".$style."\">";
				} elseif ($out[3] == 2) {
					$field = "<textarea name=\"field[]\" cols=\"15\" rows=\"5\" class=\"".$style."\">".$fieldin."</textarea>";
				} elseif ($out[3] == 3) {
					$field = "<select name=\"field[]\" class=\"".$style."\">";
					$field .= "<option value=\"0\">"._NO."</option>";
					$fieldcs = explode(",", $out[2]);
					foreach ($fieldcs as $val) {
						if ($val != "") {
							$sel = ($val == $fieldin) ? "selected" : "";
							$field .= "<option value=\"$val\" $sel>$val</option>\n";
						}
					}
					$field .= "</select>";
				}
				$fields .= "<div class=\"left\">".$out[1].":</div><div class=\"center\">".$field."</div>";
			}
		}
		$i++;
	}
	return $fields;
}

# Fields out
function fields_out($fieldb, $mod) {
	include("config/config_fields.php");
	$mod = strtolower($mod);
	$fieldc = $conffi[$mod];
	$fieldb = explode("|", $fieldb);
	$fieldc = explode("||", $fieldc);
	$i = 0;
	$fields = "";
	foreach ($fieldc as $val) {
		if ($val != "" && $fieldb[$i] != "" && $fieldb[$i] != "0") {
			preg_match("#(.*)\|(.*)\|(.*)\|(.*)#i", $val, $out);
			$fields .= "".$out[1].": ".$fieldb[$i]."<br>";
		}
		$i++;
	}
	return $fields;
}

# Format domain
function domain($url) {
	$i = 0;
	$massiv = explode(",", $url);
	foreach ($massiv as $val) {
		if ($val != "") {
			if ($i == 0) {
				$domain = "<a href=\"$val\" target=\"_blank\" title=\""._DOWNLLINK."\">".preg_replace("/http\:\/\/|www./", "", $val)."</a>";
			} else {
				$domain .= ", <a href=\"$val\" target=\"_blank\" title=\""._DOWNLLINK."\">".preg_replace("/http\:\/\/|www./", "", $val)."</a>";
			}
			$i++;
		}
	}
	return $domain;
}

# Format user info
function get_info() {
	global $prefix, $db, $admin_file, $conf, $confu;
	$info = func_get_args();
	$id = (is_moder()) ? "<tr><td>"._ID.":</td><td>".$info[0]."</td></tr>" : "";
	$name = $info[1];
	$mail = ((is_moder($conf['name']) || $info[11]) && $info[2]) ? (($info[35]) ? anti_spam($info[2]) : $info[2]) : "<i>"._NO_INFO."</i>";
	$site = ($info[3]) ? domain($info[3]) : "<i>"._NO_INFO."</i>";
	$avatar = ($info[4] && file_exists("".$confu['adirectory']."/".$info[4]."")) ? "<img src=\"".$confu['adirectory']."/".$info[4]."\" alt=\"".$info[1]."\" title=\"".$info[1]."\">" : "<img src=\"".$confu['adirectory']."/00.gif\" alt=\"".$info[1]."\" title=\"".$info[1]."\">";
	if (!defined("ADMIN_FILE")) $avatar .= user_awards ($info[0],3);
	$regdate = ($info[5]) ? $info[5] : "<i>"._NO_INFO."</i>";
	$icq = ($info[6]) ? $info[6] : "<i>"._NO_INFO."</i>";
	$occup = ($info[7]) ? $info[7] : "<i>"._NO_INFO."</i>";
	$local = ($info[8]) ? $info[8] : "<i>"._NO_INFO."</i>";
	$inter = ($info[9]) ? $info[9] : "<i>"._NO_INFO."</i>";
	$sign = ($info[10] && $info[35]) ? "<tr><td colspan=\"2\"><hr></td></tr><tr><td colspan=\"2\">".bb_decode($info[10], $conf['name'])."</td></tr>" : "";
	$aim = ($info[12]) ? $info[12] : "<i>"._NO_INFO."</i>";
	$yim = ($info[13]) ? $info[13] : "<i>"._NO_INFO."</i>";
	$msn = ($info[14]) ? $info[14] : "<i>"._NO_INFO."</i>";
	$lastvisit = ($info[21]) ? $info[21] : "<i>"._NO_INFO."</i>";
	$points = ($confu['point'] && $info[23]) ? "<tr><td>"._POINTS.":</td><td>".$info[23]."</td></tr>" : "";
	$ip = (is_moder($conf['name'])) ? "<tr><td>"._IP.":</td><td>".$info[24]."</td></tr>" : "";
	if ($info[27]) {
		preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $info[27], $datetime);
		$birthday = "".$datetime[3].".".$datetime[2].".".$datetime[1]."";
	} else {
		$birthday = "<i>"._NO_INFO."</i>";
	}
	$gender = gender($info[28], 1);
	$rating = ($info[35]) ? ajax_rating(1, $info[0], $conf['name'], $info[29], $info[30]) : vote_graphic($info[29], $info[30]);
	$field = ($info[31] && $info[35]) ? "<tr><td colspan=\"2\">".fields_out($info[31], $conf['name'])."</td></tr>" : "";
	$agent = (is_moder($conf['name']) && $info[32]) ? "<tr><td>"._BROWSER.":</td><td>".$info[32]."</td></tr>" : "";
	$sgroup = ($info[33]) ? $info[33] : "<i>"._NO."</i>";
	if ($confu['point'] && $info[23] && $info[35]) {
		$result = $db->sql_query("SELECT name, rank FROM ".$prefix."_groups WHERE points<='".intval($info[23])."' AND extra!='1' ORDER BY points ASC");
		$a = 1;
		$group = "";
		while(list($uname, $urank) = $db->sql_fetchrow($result)) {
			$group .= ($a == 1) ? "".$uname."" : ", ".$uname."";
			$grank = $urank;
			$a++;
		}
		$group = ($group) ? $group : "<i>"._NO_INFO."</i>";
		$group = "<tr><td>"._USER_GROUPS.":</td><td>".$group."</td></tr>";
		$info[34] = ($info[34]) ? $info[34] : $grank;
	}
	$rank = ($info[34] && file_exists("images/ranks/".$info[34]."")) ? "<tr><td>"._RANK.":</td><td><img src=\"images/ranks/".$info[34]."\" border=\"0\" alt=\""._RANK."\" title=\""._RANK."\"></td></tr>" : "";
	$admin = ($info[35] && is_moder($conf['name'])) ? "<tr><td width=\"100%\" class=\"bgcolor1\" colspan=\"2\" align=\"center\">".ad_bann("".$admin_file.".php?op=security_block&new_ip=".$info[24]."", $info[24])." ".ad_edit("".$admin_file.".php?op=user_add&id=".$info[0]."")." ".ad_delete("".$admin_file.".php?op=user_del&user_id=".$info[0]."", $info[1])."</td></tr>" : "";
	$infos = "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\" class=\"bgcolor4\"><tr><th colspan=\"2\">"._PERSONALINFO."</th></tr><tr class=\"bgcolor1\"><td width=\"20%\" align=\"middle\">".$avatar."</td><td width=\"80%\" valign=\"top\" rowspan=\"2\"><table width=\"100%\">"
	."".$id."".$ip.""
	."<tr><td width=\"40%\">"._NICKNAME.":</td><td width=\"60%\">".$name."</td></tr>"
	."<tr><td>"._BIRTHDAY.":</td><td>".$birthday."</td></tr>"
	."<tr><td>"._GENDER.":</td><td>".$gender."</td></tr>"
	."<tr><td>"._REG_DATE.":</td><td>".$regdate."</td></tr>"
	."<tr><td>"._LAST_VISIT.":</td><td>".$lastvisit."</td></tr>"
	."".$points."".$group.""
	."<tr><td>"._SPEC_GROUP.":</td><td>".$sgroup."</td></tr>"
	."<tr><td>"._OCCUPATION.":</td><td>".$occup."</td></tr>"
	."<tr><td>"._LOCALITYLANG.":</td><td>".$local."</td></tr>"
	."<tr><td>"._INTERESTS.":</td><td>".$inter."</td></tr>"
	."<tr><td>"._REITING.":</td><td align=\"left\">".$rating."</td></tr>"
	."".$rank."".$agent."".$field."".$sign .""
	."</table></td></tr><tr class=\"bgcolor1\"><td><table width=\"100%\">"
	."<tr><td>".user_geo_ip($info[24], 3)."</td><td>".user_geo_ip($info[24], 2)."</td></tr>"
	."<tr><td><img src=\"".img_find("all/contact")."\" border=\"0\" align=\"center\" alt=\""._EMAIL."\" title=\""._EMAIL."\"></td><td>".$mail."</td></tr>"
	."<tr><td><img src=\"".img_find("all/home")."\" border=\"0\" align=\"center\" alt=\""._SITEURL."\" title=\""._SITEURL."\"></td><td>".$site."</td></tr>"
	."<tr><td><img src=\"".img_find("all/icq")."\" border=\"0\" align=\"center\" alt=\""._ICQ."\" title=\""._ICQ."\"></td><td>".$icq."</td></tr>"
	."<tr><td><img src=\"".img_find("all/aim")."\" border=\"0\" align=\"center\" alt=\""._AIM."\" title=\""._AIM."\"></td><td>".$aim."</td></tr>"
	."<tr><td><img src=\"".img_find("all/yim")."\" border=\"0\" align=\"center\" alt=\""._YIM."\" title=\""._YIM."\"></td><td>".$yim."</td></tr>"
	."<tr><td><img src=\"".img_find("all/msn")."\" border=\"0\" align=\"center\" alt=\""._MSN."\" title=\""._MSN."\"></td><td>".$msn."</td></tr>"
	."</table></td></tr>".$admin."</table>";
	$infos = ($info[35]) ? $infos : str_replace('"', "&quot;", $infos);
	return $infos;
}

# Format user name
function get_user() {
	global $prefix, $db;
	$let = analyze_name($_GET['letters']);
	if ($let){
		$result = $db->sql_query("SELECT user_id, user_name FROM ".$prefix."_users WHERE user_name LIKE '".$let."%'");
		while(list($user_id, $user_name) = $db->sql_fetchrow($result)){
			echo "".$user_id."###".$user_name."|";
		}
	}
}

# Check user
function is_user($usr="") {
	global $prefix, $db, $user, $confu;
	static $usertrue;
	if (!isset($usertrue)) {
		$uid = intval(substr($user[0], 0, 11));
		$una = htmlspecialchars(substr($user[1], 0, 25));
		$pwd = htmlspecialchars(substr($user[2], 0, 40));
		$ip = getip();
		if ($uid != "" && $pwd != "") {
			if ($confu['check'] == "0") {
				list($pass) = $db->sql_fetchrow($db->sql_query("SELECT user_password FROM ".$prefix."_users WHERE user_id='$uid' AND user_name='$una'"));
				if ($pass == $pwd && $pass != "") {
					$usertrue = 1;
					return 1;
				}
			} else {
				list($pass, $last_ip) = $db->sql_fetchrow($db->sql_query("SELECT user_password, user_last_ip FROM ".$prefix."_users WHERE user_id='$uid' AND user_name='$una'"));
				if ($pass == $pwd && $pass != "" && $last_ip == $ip && $last_ip != "") {
					$usertrue = 1;
					return 1;
				}
			}
		}
		$usertrue = 0;
		return 0;
	}
	if ($usertrue == 1) {
		return 1;
	} else {
		return 0;
	}
}

# Get user id
function is_user_id($name) {
	global $prefix, $db;
	$name = text_filter(substr($name, 0, 25));
	list($uid) = $db->sql_fetchrow($db->sql_query("SELECT user_id FROM ".$prefix."_users WHERE user_name='".$name."'"));
	return intval($uid);
}

# Check admin
function is_admin($adm="") {
	global $prefix, $db, $admin;
	static $admintrue;
	if (!isset($admintrue)) {
		$id = intval(substr($admin[0], 0, 11));
		$name = htmlspecialchars(substr($admin[1], 0, 25));
		$pwd = htmlspecialchars(substr($admin[2], 0, 40));
		$ip = getip();
		if ($id != "" && $name != "" && $pwd != "" && $ip != "") {
			list($aname, $apwd, $aip) = $db->sql_fetchrow($db->sql_query("SELECT name, pwd, ip FROM ".$prefix."_admins WHERE id='$id'"));
			if ($aname == $name && $aname != "" && $apwd == $pwd && $apwd != "" && $aip == $ip && $aip != "") {
				$admintrue = 1;
				return 1;
			}
		}
		$admintrue = 0;
		return 0;
	}
	if ($admintrue == 1) {
		return 1;
	} else {
		return 0;
	}
}

# Check modul admin
function is_admin_modul($modul) {
	global $prefix, $db, $admin;
	$aid = intval(substr($admin[0], 0, 11));
	$modul = addslashes(trim(substr($modul, 0, 25)));
	static $modules;
	if (!is_array($modules)) {
		$result = $db->sql_query("SELECT mid, title FROM ".$prefix."_modules");
		while (list($mid, $title) = $db->sql_fetchrow($result)) $modules[] = array($mid, $title);
	}
	static $amodules;
	if (!is_array($amodules)) {
		list($amodules) = $db->sql_fetchrow($db->sql_query("SELECT modules FROM ".$prefix."_admins WHERE id='$aid'"));
		$amodules = explode(",", $amodules);
	}
	foreach ($modules as $val) {
		if ($modul == $val[1] && $modul != "") {
			$admuser = 0;
			foreach ($amodules as $val2) {
				if ($val[0] == $val2) $admuser = 1;
			}
			if (is_admin_god() || $admuser == 1) return 1;
		}
	}
	return 0;
}

# Check moderator
function is_moder($modul="") {
	$modul = ($modul) ? addslashes(trim(substr($modul, 0, 25))) : 0;
	if ((is_admin() && is_admin_god()) || ($modul && is_admin() && is_admin_modul($modul))) {
		return 1;
	} else {
		return 0;
	}
}

# Format theme
function get_theme() {
	global $user, $conf;
	if (!defined("ADMIN_FILE") && is_user()) {
		$utheme = $user[5];
		if (file_exists("templates/$utheme") && $utheme != "") {
			$theme = $utheme;
		} else {
			$theme = $conf['theme'];
		}
	} elseif (!defined("ADMIN_FILE")) {
		$theme = $conf['theme'];
	} elseif (defined("ADMIN_FILE")) {
		$theme = "admin";
	}
	return $theme;
}

# Format user name search
function get_user_search($name, $var, $msize, $size, $width) {
	$content = "<style type=\"text/css\">
	#ajax_listOfOptions {
		position: absolute;
		width: ".$width."px;
		height: 250px;
		overflow: auto;
		border: 1px solid #E8EBEE;
		background-color: #FFF;
		text-align: left;
		font-size: 11px;
		font-weight: normal;
		font-family: Verdana, Helvetica;
		z-index: 100;
	}
	#ajax_listOfOptions div {
		margin: 1px;
		padding: 1px;
		cursor: pointer;
		font-size: 11px;
		font-weight: normal;
		font-family: Verdana, Helvetica;
	}
	#ajax_listOfOptions .optionDiv {
		/* Div for each item in list */
	}
	#ajax_listOfOptions .optionDivSelected {
		background-color: #2666B9;
		color: #FFF;
	}
	#ajax_listOfOptions_iframe {
		background-color: #F00;
		position: absolute;
		z-index: 5;
	}
	</style>
	<script type=\"text/javascript\" src=\"ajax/dynamic_list.js\"></script>";
	$content .= "<input type=\"text\" name=\"".$name."\" value=\"".$var."\" OnKeyUp=\"ajax_showOptions(this,'getCountriesByLetters',event)\" maxlength=\"".$msize."\" size=\"".$size."\" style=\"width: ".$width."px\">";
	return $content;
}

# Format Password
function gen_pass($m) {
	$m = intval($m);
	$pass = "";
	for ($i = 0; $i < $m; $i++) {
		$te = mt_rand(48, 122);
		if (($te > 57 && $te < 65) || ($te > 90 && $te < 97)) $te = $te - 9;
		$pass .= chr($te);
	}
	return $pass;
}

# Get referer
function referer($url) {
	$referer = getenv("HTTP_REFERER");
	if (isset($_REQUEST['refer']) && $referer != "" && !preg_match("/^unknown/i", $referer) && !preg_match("/^bookmark/i", $referer)) {
		Header("Location: ".$referer."");
	} else {
		Header("Location: ".$url."");
	}
}

# Analyze name
function analyze_name($name) {
	$name = ($name) ? ((preg_match("#\"|\'|\.|\:|\;|\/|\*#", $name)) ? "" : $name) : "";
	return $name;
}

# URL filter
function url_filter($url) {
	$url = strtolower($url);
	$url = (preg_match("#http\:\/\/#i", $url)) ? $url : "http://".$url."";
	$url = ($url == "http://") ? "" : text_filter($url);
	return $url;
}

# Format head
function head() {
	global $prefix, $db, $home, $index, $conf, $confs, $user, $admin, $name, $bodytext, $hometext, $pagetitle, $key_words;
	unset($_SESSION[$conf['user_c']]);
	$_SESSION[$conf['user_c']] = $conf['user_c'];
	if (!defined("ADMIN_FILE") && $conf['close']) {
		if (!is_admin()) get_exit(""._CLOSE_TEXT."", 0);
	}
	if ((!defined("ADMIN_FILE") && $conf['cache'] == 1) || (!defined("ADMIN_FILE") && $conf['cache'] == 2 && $home)) {
		$url = str_replace("/", "", $_SERVER['REQUEST_URI']);
		$url = (!$url) ? "index.php" : $url;
		$match = preg_match("/index/", $url);
		if ($match && !is_user() && !is_admin()) {
			$cacheurl = "config/cache/".md5($url).".txt";
			if (file_exists($cacheurl) && filesize($cacheurl) != 0 && (time() - $conf['cache_t']) < filemtime($cacheurl)) {
				readfile($cacheurl);
				exit;
			}
		}
	}
	if ($conf['session']) {
		$ip = getip();
		$url = htmlspecialchars(getenv("REQUEST_URI"));
		if ($confs['flood'] && (isset($_GET) || isset($_POST))) {
			$ftime = time() - $confs['flood_t'];
			list($flood) = $db->sql_fetchrow($db->sql_query("SELECT Count(uname) FROM ".$prefix."_session WHERE host_addr = '$ip' AND time > '$ftime'"));
			if (isset($_POST) && $flood) {
				$a = 0;
				foreach ($_POST as $var_name=>$var_value) {
					if ($a == 0) {
						$info = "".$var_name."=".$var_value."";
					} else {
						$info .= ", ".$var_name."=".$var_value."";
					}
					$a++;
				}
				if ($a) warn_report("Flood in POST - ".$info."");
			}
			if ($confs['flood'] == 2 && isset($_GET) && $flood) {
				$a = 0;
				foreach ($_GET as $var_name=>$var_value) {
					if ($a == 0) {
						$info = "".$var_name."=".$var_value."";
					} else {
						$info .= ", ".$var_name."=".$var_value."";
					}
					$a++;
				}
				if ($a) warn_report("Flood in GET - ".$info."");
			}
		}
		if (is_admin()) {
			$uname = text_filter(substr($admin[1], 0, 25), 1);
			$guest = 3;
		} elseif (!defined("ADMIN_FILE") && is_user()) {
			$uname = text_filter(substr($user[1], 0, 25), 1);
			$guest = 2;
		} elseif (!defined("ADMIN_FILE") && !is_user()) {
			$bots = explode(",", $conf['bots']);
			for ($i = 0; $i < count($bots); $i++) {
				list($uagent, $bname) = explode("=", $bots[$i]);
				if (preg_match("/$uagent/i", getagent())) {
					$uname = text_filter(substr($bname, 0, 25), 1);
					$guest = 1;
					break;
				} else {
					$uname = $ip;
					$guest = 0;
				}
			}
		}
		$sess_f = "config/counter/sess.txt";
		$sess_t = (file_exists($sess_f) && filesize($sess_f) != 0) ? file_get_contents($sess_f) : 0;
		$past = time() - intval($conf['sess_t']);
		if ($sess_t < $past) {
			$db->sql_query("DELETE FROM ".$prefix."_session WHERE time < '$past'");
			if (is_user()) {
				$uvisit = save_datetime();
				$uagent = getagent();
				$unam = text_filter(substr($user[1], 0, 25), 1);
				$db->sql_query("UPDATE ".$prefix."_users SET user_last_ip='$ip', user_lastvisit='$uvisit', user_agent='$uagent' WHERE user_name='$unam'");
			}
			unlink($sess_f);
			$fp = fopen($sess_f, "wb");
			fwrite($fp, time());
			fclose($fp);
		}
		$ctime = time();
		if ($uname) {
			$db->sql_query("UPDATE ".$prefix."_session SET uname='$uname', time='$ctime', host_addr='$ip', guest='$guest', module='$name', url='$url' WHERE uname='$uname'");
			$e = @mysql_info();
			preg_match("#^\D+(\d+)#", $e, $matches);
			if ($matches[1] == 0) $db->sql_query("INSERT INTO ".$prefix."_session (uname, time, host_addr, guest, module, url) VALUES ('$uname', '$ctime', '$ip', '$guest', '$name', '$url')");
		}
	}
	$ThemeSel = get_theme();
	if (file_exists("templates/$ThemeSel/index.php")) {
		include("templates/$ThemeSel/index.php");
	} else {
		include("function/template.php");
	}
	$defis_dec = urldecode($conf['defis']);
	$index = file_get_contents(get_theme_file("index"));
	if ($conf['lic_h'] != "UG93ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vd3d3LnNsYWVkLm5ldCIgdGFyZ2V0PSJfYmxhbmsiIHRpdGxlPSJPcGVuIFNMQUVEIj5PcGVuIFNMQUVEPC9hPiAmY29weTsgMjAwNS0=" || $conf['lic_f'] != "IFNMQUVELiBBbGwgcmlnaHRzIHJlc2VydmVkLg==" || !preg_match("#{%LICENSE%}#", $index)) get_exit(""._NO_LICENSE."", 0);
	$licens = "".base64_decode($conf['lic_h'])."".date("Y")."".base64_decode($conf['lic_f'])."";
	$index = str_replace("{%LICENSE%}", $licens, $index);
	preg_match("#^(.*){%MODULE%}#iUs", $index, $head);
	$head = (isset($head[1])) ? $head[1] : die("Error in Head!");
	preg_match("#{%MODULE%}(.*)$#iUs", $index, $index);
	$index = (isset($index[1])) ? $index[1] : die("Error in Foot!");
	$strhead = "<meta http-equiv=\"content-type\" content=\"text/html; charset="._CHARSET."\">\n";
	if ($home) {
		$strhead .= "<title>".$conf['sitename']." $defis_dec ".$conf['slogan']."</title>\n";
	} else {
		$ptitle_dec = explode($conf['defis'], $pagetitle);
		$i = 0;
		$pagetitle_dec = "";
		foreach ($ptitle_dec as $val) $i++;
		foreach ($ptitle_dec as $val) {
			if ($val) $pagetitle_dec .= "".trim($ptitle_dec[$i])." ".$defis_dec." ";
			$i--;
		}
		$strhead .= "<title>".$pagetitle_dec."".$conf['sitename']."</title>\n";
	}
	if (!defined("ADMIN_FILE")) {
		include("function/no-cache.php");
		$strhead .= "<meta name=\"resource-type\" content=\"document\">\n"
		."<meta name=\"document-state\" content=\"dynamic\">\n"
		."<meta name=\"distribution\" content=\"global\">\n"
		."<meta name=\"author\" content=\"".$conf['sitename']."\">\n"
		."<meta name=\"copyright\" content=\"Copyright (c) Open SLAED ".$conf['version']."\">\n";
		$pagetitle = trim(str_replace(" ".$conf['defis']." ", ", ", $pagetitle));
		$pagetitle = trim(str_replace("".$conf['defis']." ", "", $pagetitle));
		if ((($hometext == "") && ($bodytext == "")) || ($conf['keywords_s'] == "0")) {
			$strhead .= "<meta name=\"keywords\" content=\"$pagetitle, ".$conf['keywords']."\">\n"
			."<meta name=\"description\" content=\"".$conf['slogan'].", $pagetitle.\">\n";
		} else {
			$key_words = "$pagetitle";
			$keywords_gen = "$hometext $bodytext";
			$keywords_gen = text_filter(bb_decode($keywords_gen, ""), 1);
			$keywords_gen = trim(str_replace(array("'", '"', "!", "?", ":", ";", ".", "(", ")"), " ", $keywords_gen));
			$keywords_gen = preg_replace("/( |".CHR(10)."|".CHR(13).")+/", ",", $keywords_gen);
			$keywords_gen = substr($keywords_gen, 0, 1600);
			$keywords_gen = array_unique(explode(",", $keywords_gen));
			for ($a = 0, $b = 7; $a < sizeof($keywords_gen) && $b < 800; $a++) {
				if (($c = strlen($keywords_gen[$a])) > 3) {
					$key_words = "".$key_words.", ".$keywords_gen[$a]."";
					$b += $c+2;
				}
			}
			$strhead .= "<meta name=\"keywords\" content=\"$key_words\">\n"
			."<meta name=\"description\" content=\"".$conf['slogan'].", $pagetitle.\">\n";
		}
		$strhead .= "<meta name=\"robots\" content=\"index, follow\">\n"
		."<meta name=\"revisit-after\" content=\"1 days\">\n"
		."<meta name=\"rating\" content=\"general\">\n"
		."<meta name=\"generator\" content=\"Open SLAED ".$conf['version']."\">\n"
		."<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"favicon.ico\" >\n";
		include("config/config_rss.php");
		if ($confrs['act']) {
			$fieldc = explode("||", $confrs['rss']);
			foreach ($fieldc as $val) {
				if ($val != "") {
					preg_match("#(.*)\|(.*)\|(.*)#i", $val, $out);
					if ($out[1] != "0" && $out[2] != "0" && $out[3] == "1") {
						$strhead .= "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$out[2]."\" title=\"".$out[1]."\">\n";
					}
				}
			}
		}
		$strhead .= "<link rel=\"search\" type=\"application/opensearchdescription+xml\" href=\"".$conf['homeurl']."/search.php\" title=\"".$conf['sitename']." - Search\">\n";
	}
	$strhead .= (file_exists("templates/$ThemeSel/style.css")) ? "<link rel=\"stylesheet\" href=\"templates/$ThemeSel/style.css\" type=\"text/css\">\n" : "";
	$strhead .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"ajax/global_func.js\"></script>\n";
	$strhead .= (!$confs['error_java']) ? "<script language=\"JavaScript\" type=\"text/javascript\" src=\"ajax/block_error.js\"></script>\n" : "";
	if (!defined("ADMIN_FILE")) {
		if (file_exists("config/config_header.php")) {
			ob_start();
			include("config/config_header.php");
			$strhead .= ob_get_contents();
			ob_end_clean();
		}
	}
	$head = addblocks($head);
	$head = str_replace("{%HEAD%}", $strhead, $head);
	themeheader($head);
	unset($head, $strhead);
	if (!defined("ADMIN_FILE")) update_points(1);
}

# Format foot
function foot() {
	global $home, $module, $name, $index, $conf, $do_gzip_compress;
	$index = addblocks($index);
	if ($module == 1 && file_exists("modules/$name/copyright.php")) {
		$index = "<div align=\"right\"><a href=\"javascript:OpenWindow('modules/$name/copyright.php', 'Copyright', '400', '200')\">".str_replace("_", " ", $name)." &copy;</a></div>".$index."";
	}
	themefooter($index);
	unset($index);
	if (!defined("ADMIN_FILE") && $conf['rewrite']) rewrite();
	if ((!defined("ADMIN_FILE") && $conf['cache'] == 1) || (!defined("ADMIN_FILE") && $conf['cache'] == 2 && $home)) {
		$url = str_replace("/", "", $_SERVER['REQUEST_URI']);
		$url = (!$url) ? "index.php" : $url;
		$match = preg_match("/index/", $url);
		$cont = ob_get_contents();
		if ($cont && $match && !is_user() && !is_admin()) {
			$fp = fopen("config/cache/".md5($url).".txt", "wb");
			fwrite($fp, $cont);
			fclose($fp);
		}
	}
	if ($conf['gzip']) {
		if ($do_gzip_compress) {
			$gzip_contents = ob_get_contents();
			ob_end_clean();
			$gzip_size = strlen($gzip_contents);
			$gzip_crc = crc32($gzip_contents);
			$gzip_contents = gzcompress($gzip_contents, 9);
			$gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);
			echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
			echo $gzip_contents;
			echo pack('V', $gzip_crc);
			echo pack('V', $gzip_size);
		}
	}
	ob_end_flush();
	exit;
}

# Format Time filter
function format_time($time) {
	setlocale(LC_TIME, ""._LOCALE."");
	preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $time, $datetime);
	$datetime = date(""._DATESTRING."", mktime($datetime[4], $datetime[5], $datetime[6], $datetime[2], $datetime[3], $datetime[1]));
	return $datetime;
}

# Display Time filter
function display_time($sec) {
	$minutes = floor($sec / 60);
	$seconds = $sec % 60;
	$content = ($minutes == 0) ? "".$seconds." "._SEC."." : "".$minutes." "._MIN.". ".$seconds." "._SEC.".";
	return $content;
}

# Size filter
function files_size($size) {
	$name = array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
	$mysize = $size ? "".round($size / pow(1024, ($i = floor(log($size, 1024)))), 2)." ".$name[$i]."" : "".$size." Bytes";
	return $mysize;
}

# Format new graphic
function new_graphic($data) {
	$data = mktime() - $data;
	$img = "";
	if ($data < 86400) $img = "<img src=\"".img_find("misc/new_day")."\" alt=\""._NEWTODAY."\" title=\""._NEWTODAY."\">";
	if (($data > 86400) && ($data < 259200)) $img = "<img src=\"".img_find("misc/new_3day")."\" alt=\""._NEWLAST3DAYS."\" title=\""._NEWLAST3DAYS."\">";
	if (($data > 259200) && ($data < 604800)) $img = "<img src=\"".img_find("misc/new_week")."\" alt=\""._NEWTHISWEEK."\" title=\""._NEWTHISWEEK."\">";
	return $img;
}

# Format categories
function categories($mod, $tab, $sub, $desc, $id="") {
	global $prefix, $db, $user, $conf, $currentlang;
	if (!preg_match("/[^a-zA-Z0-9_]/", $mod)) {
		$id = (intval($id)) ? $id : 0;
		if ($id) {
			$where = "WHERE modul='$mod' AND parentid = '$id'";
		} elseif ($id && $conf['multilingual']) {
			$where = "WHERE modul='$mod' AND parentid = '$id' AND (language='$currentlang' OR language='')";
		} elseif ($conf['multilingual']) {
			$where = "WHERE modul='$mod' AND (language='$currentlang' OR language='')";
		} else {
			$where = "WHERE modul='$mod'";
		}
		$tdwidth = intval(100/$tab);
		$cat_num = 0;
		$result = $db->sql_query("SELECT id, title, description, img, parentid FROM ".$prefix."_categories ".$where." ORDER BY title");
		while (list($cid, $title, $description, $img, $parentid) = $db->sql_fetchrow($result)) {
			$massiv[] = array($cid, $title, $description, $img, $parentid);
			$cat_num++;
		}
		if ($massiv) {
			$a = 0;
			foreach ($massiv as $val) {
				if ($val[4] == $id) {
					if ($a == 0) {
						$catid = $val[0];
						$a++;
					} else {
						$catid .= ",".$val[0];
					}
					if ($val[3]) {
						$description = ($desc) ? "<br><i>".$val[2]."</i>" : "";
						$ccontent .= "<td valign=\"top\" width=\"".$tdwidth."%\"><table width=\"100%\" border=\"0\"><tr><td><a href=\"index.php?name=$mod&cat=$val[0]\" title=\"$val[1]\"><img src=\"images/categories/".$val[3]."\" border=\"0\" title=\"".$val[1]."\"></a></td><td width=\"100%\"><a href=\"index.php?name=$mod&cat=$val[0]\" title=\"$val[1]\"><b>$val[1]</b></a>".$description."</td></tr>";
					} else {
						$description = ($desc) ? "<tr><td colspan=\"2\"><i>".$val[2]."</i></td></tr>" : "";
						$ccontent .= "<td valign=\"top\" width=\"".$tdwidth."%\"><table width=\"100%\" border=\"0\"><tr><td><a href=\"index.php?name=$mod&cat=$val[0]\" title=\"$val[1]\"><img src=\"".img_find("all/".strtolower($mod)."")."\" border=\"0\" title=\"".$val[1]."\"></a></td><td width=\"100%\"><a href=\"index.php?name=$mod&cat=$val[0]\" title=\"$val[1]\"><b>$val[1]</b></a></td></tr>".$description."";
					}
					foreach ($massiv as $val2) {
						if ($val[0] == $val2[4]) {
							$catid .= ",".$val2[0];
							if ($sub == 1) $ccontent .= "<tr><td colspan=\"2\"><img border=\"0\" src=\"".img_find("misc/navi")."\" title=\"$val2[1]\"> <a href=\"index.php?name=$mod&cat=$val2[0]\" title=\"$val2[1]\">$val2[1]</a></td></tr>";
						}
					}
					$ccontent .= "</table></td>";
					if ($cont == ($tab - 1)) {
						$ccontent .= "</tr><tr>";
						$cont = 0;
					} else {
						$cont++;
					}
				}
			}
		}
		if ($ccontent) {
			if ($mod == "files") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(lid) FROM ".$prefix."_files WHERE cid IN ($catid) AND date <= now() AND status!='0'"));
				$in = _INF;
			} elseif ($mod == "news") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(sid) FROM ".$prefix."_stories WHERE catid IN ($catid) AND time <= now() AND status!='0'"));
				$in = _INN;
			}
			open();
			echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\"><tr>".$ccontent."</td></tr></table><hr><center>"._ALLIN.": <b>".$pages_num."</b> ".$in." <b>".$cat_num."</b> "._ALLINC."</center>";
			close();
		}
	}
}

# Format categories select
function getcat($mod, $id="") {
	global $prefix, $db;
	if (!preg_match("/[^a-zA-Z0-9_]/", $mod)) {
		$content = ($id == "no") ? "" : "<option value=\"\">"._HOMECAT."</option>";
		$result = $db->sql_query("SELECT id, title, parentid FROM ".$prefix."_categories WHERE modul='$mod'");
		if ($db->sql_numrows($result) > 0) {
			while (list($cid, $title, $parentid) = $db->sql_fetchrow($result)) $massiv[$cid] = array($title, $parentid);
			foreach ($massiv as $key => $val) {
				$cont[$key] = $val[0];
				$flag = $val[1];
				while ($flag != "0") {
					$cont[$key] = $massiv[$flag][0]." / ".$cont[$key];
					$flag = intval($massiv[$flag][1]);
				}
			}
			asort($cont);
			foreach ($cont as $key => $val) {
				$sel = ($id == $key) ? "selected" : "";
				$content .= "<option value=\"$key\" $sel>$val</option>";
			}
		}
		return $content;
	}
}

# Format categories parent
function getparent($id, $title) {
	global $prefix, $db;
	if (intval($id)) {
		$result = $db->sql_query("SELECT title, parentid FROM ".$prefix."_categories WHERE id='$id'");
		list($ptitle, $pid) = $db->sql_fetchrow($result);
		if ($ptitle) $title = $ptitle." / ".$title;
		if ($pid) $title = getparent($pid, $title);
		return $title;
	}
}

# Lang filter
function cutstr($linkstrip, $strip) {
	$linkstrip = stripslashes($linkstrip);
	if (strlen($linkstrip) > $strip) $linkstrip = mb_substr($linkstrip, 0, $strip, "utf-8")."...";
	return $linkstrip;
}

# Analyzer of variables
function variables() {
	if ($_GET) {
		$cont = array();
		foreach ($_GET as $var_name => $var_value) $cont[] = "<b>".$var_name."</b>=".$var_value;
		$content = "<br><br><font color=\"blue\"><b>GET</b></font> - ".implode(", ", $cont);
	}
	if ($_POST) {
		$cont = array();
		foreach ($_POST as $var_name => $var_value) {
			$var_value = is_array($var_value) ? fields_save($var_value) : $var_value;
			$var_value = str_replace(array("[", "]"), array("&#091;", "&#093;"), htmlspecialchars($var_value));
			$cont[] = "<b>".$var_name."</b>=".$var_value."";
		}
		$content .= "<br><br><font color=\"blue\"><b>POST</b></font> - ".implode(", ", $cont);
	}
	if ($_COOKIE) {
		$cont = array();
		foreach ($_COOKIE as $var_name => $var_value) $cont[] = "<b>".$var_name."</b>=".$var_value;
		$content .= "<br><br><font color=\"blue\"><b>COOKIE</b></font> - ".implode(", ", $cont);
	}
	if ($_FILES) {
		$cont = array();
		foreach ($_FILES as $var_name => $var_value) $cont[] = "<b>".$var_name."</b>=".$var_value;
		$content .= "<br><br><font color=\"blue\"><b>FILES</b></font> - ".implode(", ", $cont);
	}
	if ($_SESSION) {
		$cont = array();
		foreach ($_SESSION as $var_name => $var_value) $cont[] = "<b>".$var_name."</b>=".$var_value;
		$content .= "<br><br><font color=\"blue\"><b>SESSION</b></font> - ".implode(", ", $cont);
	}
	return $content;
}

# Check module
function is_active($module) {
	global $prefix, $db;
	static $name;
	if (!is_array($name)) {
		$result = $db->sql_query("SELECT title FROM ".$prefix."_modules WHERE active='1'");
		while (list($title) = $db->sql_fetchrow($result)) $name[] = $title;
	}
	foreach ($name as $val) {
		if ($val == $module) {
			$a = 1;
			break;
		} else {
			$a = 0;
		}
	}
	return $a;
}

# Rewrite mod
function rewrite() { 
	$contents = ob_get_contents(); 
	ob_end_clean();
	include("config/config_rewrite.php");
	$rewrite = preg_replace($in, $out, $contents);
	echo $rewrite;
}

# Decode BB
function bb_decode($sourse, $mod) {
	if (!preg_match("#\[php\](.*)\[/php\]|\[code\](.*)\[/code\]#si", $sourse)) {
		$bb[] = "#\[img\]([^?](?:[^\[]+|\[(?!url))*?)\[/img\]#i";
		$html[] = "<img src=\"\\1\" border=\"0\" alt=\"\\1\" title=\"\\1\">";
		$bb[] = "#\[img=([a-zA-Z]+)\]([^?](?:[^\[]+|\[(?!url))*?)\[/img\]#is";
		$html[] = "<img src=\"\\2\" align=\"\\1\" border=\"0\" alt=\"\\2\" title=\"\\2\">";
		$bb[] = "#\[img\ alt=([a-zA-Zà-ÿÀ-ß0-9\_\-\. ]+)\]([^?](?:[^\[]+|\[(?!url))*?)\[/img\]#is";
		$html[] = "<img src=\"\\2\" align=\"\\1\" border=\"0\" alt=\"\\1\" title=\"\\1\">";
		$bb[] = "#\[img=([a-zA-Z]+) alt=([a-zA-Zà-ÿÀ-ß0-9\_\-\. ]+)\]([^?](?:[^\[]+|\[(?!url))*?)\[/img\]#is";
		$html[] = "<img src=\"\\3\" align=\"\\1\" border=\"0\" alt=\"\\2\" title=\"\\2\">";
		$bb[] = "#\[url\]([\w]+?://([\w\#$%&~/.\-;:=,?@\]+]+|\[(?!url=))*?)\[/url\]#is";
		$html[] = "<a href=\"\\1\" target=\"_blank\" title=\"\\1\">\\1</a>";
		$bb[] = "#\[url\]((www|ftp)\.([\w\#$%&~/.\-;:=,?@\]+]+|\[(?!url=))*?)\[/url\]#is";
		$html[] = "<a href=\"http://\\1\" target=\"_blank\" title=\"\\1\">\\1</a>";
		$bb[] = "#\[url=([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/url\]#is";
		$html[] = "<a href=\"\\1\" target=\"_blank\" title=\"\\1\">\\2</a>";
		$bb[] = "#\[url=((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/url\]#is";
		$html[] = "<a href=\"http://\\1\" target=\"_blank\" title=\"\\1\">\\3</a>";
		$bb[] = "#\[mail\](\S+?)\[/mail\]#i";
		$html[] = "<a href=\"mailto:\\1\">\\1</a>";
		$bb[] = "#\[mail\s*=\s*([\.\w\-]+\@[\.\w\-]+\.[\w\-]+)\s*\](.*?)\[\/mail\]#i";
		$html[] = "<a href=\"mailto:\\1\">\\2</a>";
		$bb[] = "#\[color=(\#[0-9A-F]{6}|[a-z]+)\](.*?)\[/color\]#si";
		$html[] = "<span style=\"color: \\1\">\\2</span>";
		$bb[] = "#\[family=([A-Za-z ]+)\](.*?)\[/family\]#si";
		$html[] = "<span style=\"font-family: \\1\">\\2</span>";
		$bb[] = "#\[size=([0-9]{1,2}+)\](.*?)\[/size\]#si";
		$html[] = "<span style=\"font-size: \\1\">\\2</span>";
		$bb[] = "#\[(left|right|center|justify)\](.*?)\[/\\1\]#is";
		$html[] = "<div align=\"\\1\">\\2</div>";
		$bb[] = "#\[b\](.*?)\[/b\]#si";
		$html[] = "<b>\\1</b>";
		$bb[] = "#\[i\](.*?)\[/i\]#si";
		$html[] = "<i>\\1</i>";
		$bb[] = "#\[u\](.*?)\[/u\]#si";
		$html[] = "<u>\\1</u>";
		$bb[] = "#\[s\](.*?)\[/s\]#si";
		$html[] = "<s>\\1</s>";
		$bb[] = "#\[li\]#si";
		$html[] = "<li>";
		$bb[] = "#\[hr\]#si";
		$html[] = "<hr>";
		$bb[] = "#\*(\d{2})#";
		$html[] = "<img src=\"images/smilies/\\1.gif\" border=\"0\">";
	
		$bb[] = "#javascript:#si";
		$html[] = "Java Script";
		$bb[] = "#about:#si";
		$html[] = "About";
		$bb[] = "#vbscript:#si";
		$html[] = "VB Script";
	
		$sourse = preg_replace($bb, $html, $sourse);
		if (preg_match("#\[quote\](.*?)\[/quote\]#si", $sourse)) $sourse = encode_quote($sourse);
		if (preg_match("#\[hide\](.*?)\[/hide\]#si", $sourse)) $sourse = encode_hide($sourse);
		if (preg_match("#\[attach=(.*?)\]#si", $sourse)) $sourse = encode_attach($sourse, strtolower($mod));
	} else {
		if (preg_match("#(.*)\[php\](.*)\[/php\](.*)#si", $sourse, $matches)) {
			$sourse = bb_decode($matches[1], $mod).encode_php($matches[2]).bb_decode($matches[3], $mod);
		} elseif (preg_match("#(.*)\[code\](.*)\[/code\](.*)#si", $sourse, $matches)) {
			$sourse = bb_decode($matches[1], $mod).encode_code($matches[2]).bb_decode($matches[3], $mod);
		}
	}
	return $sourse; 
}

# Format hide
function encode_hide($text) {
	$start_html = "<fieldset style=\"width: 95%; overflow: auto;\"><legend style=\"color: red;\">"._HIDE."</legend><div style=\"margin: 3px;\">";
	$end_html = "</div></fieldset>";
	$text = (defined("ADMIN_FILE") || is_user()) ? preg_replace("#\[hide\](.*?)\[/hide\]#si", "".$start_html."\\1".$end_html."", $text) : preg_replace("#\[hide\](.*?)\[/hide\]#si", "".$start_html.""._HIDETEXT."".$end_html."", $text);
	return $text;
} 

# Format quote
function encode_quote($text) {
	$start_html = "<fieldset style=\"width: 95%; overflow: auto;\"><legend style=\"color: green;\">"._QUOTE."</legend><div style=\"margin: 3px;\">";
	$end_html = "</div></fieldset>";
	while (preg_match("#\[quote\](.*?)\[/quote\]#si", $text)) $text = preg_replace("#\[quote\](.*?)\[/quote\]#si", "".$start_html."\\1".$end_html."", $text);
	return $text;
}

# Format code
function encode_code($text) {
	$text = "<fieldset style=\"width: 95%; overflow: auto;\"><legend style=\"color: blue;\">"._CODE."</legend><div class=\"code\">".$text."</div></fieldset>";
	return $text;
}

# Format PHP code
function encode_php($text) {
	$replace = trim($text);
	$in = array("&lt;", "&gt;", "&quot;", "&#039;", "&amp;", "&#092;");
	$out = array("<", ">", '"', "'", "&", "\\");
	$replace = str_replace($in, $out, $replace);
	$replace = preg_replace("#<br \/>#i", "", $replace);
	$replace = (substr($replace, 0, 2) != "<?") ? "<?php".$replace : $replace;
	$replace = (substr($replace, -2) != "?>") ? $replace."?>" : $replace;
	ob_start();
	highlight_string($replace);
	$replace = ob_get_contents();
	ob_end_clean();
	$replace = str_replace("&nbsp;", " ", $replace);
	$replace = str_replace(array("&lt;?php", "?&gt;"), "", $replace);
	$text = "<fieldset style=\"width: 95%; overflow: auto;\"><legend style=\"color: purple;\">PHP - "._CODE."</legend><div class=\"code\">".$replace."</div></fieldset>";
	return $text;
}

# Mail check
function checkemail($mail) {
	global $stop;
	$mail = strtolower(text_filter($mail, 1));
	if ((!$mail) || ($mail=="") || (!preg_match("/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,6}$/", $mail))) $stop = ""._ERROR1."<br>"._ERROR2." (<b>email@domain.com</b>)";
	if ((strlen($mail) >= 4) && (substr($mail, 0, 4) == "www.")) $stop = ""._ERROR1."<br>"._ERROR3." (<b>www.</b>)";
	if (strrpos($mail, " ") > 0) $stop = ""._ERROR1."<br>"._ERROR4.".";
	return $stop;
}

# Format add block
function addblocks($str) {
	global $blocks, $blocks_c, $home, $showbanners, $foot, $db, $start_time, $conf, $foot;
	preg_match_all('#{%BLOCKS([^%]+)%}#iUs', $str, $blk);
	$ci = sizeof($blk[1]);
	for ($i = 0; $i < $ci; $i++) {
		$blk[0][$i] = '#'.$blk[0][$i].'#';
		$telo = trim($blk[1][$i]);
		$pos = strtolower($telo[0]);
		switch($pos) {
			case 'l':
			if ($blocks == "" || $blocks == "0"|| $blocks == "1") {
				ob_start();
				blocks('l');
				$blk[1][$i] = ob_get_contents();
				ob_end_clean();
			} else {
				$blk[1][$i] = "";
			}
			break;
			case 'r':
			if ($blocks == "" || $blocks == "0"|| $blocks == "2") {
				ob_start();
				blocks('r');
				$blk[1][$i] = ob_get_contents();
				ob_end_clean();
			} else {
				$blk[1][$i] = "";
			}
			break;
			case 'c':
			if ($blocks_c == "" || $blocks_c == "0" || $blocks_c == "1") {
				ob_start();
				blocks('c');
				$blk[1][$i] = ob_get_contents();
				ob_end_clean();
			} else {
				$blk[1][$i] = "";
			}
			break;
			case 'd':
			if ($blocks_c == "" || $blocks_c == "0"|| $blocks_c == "2") {
				ob_start();
				blocks('d');
				$blk[1][$i] = ob_get_contents();
				ob_end_clean();
			} else {
				$blk[1][$i] = "";
			}
			break;
			case 'b':
			blocks('b');
			$blk[1][$i] = $showbanners;
			break;
			case 'f':
			blocks('f');
			$blk[1][$i] = $foot;
			break;
			case 'm':
			if ($home == 1) {
				ob_start();
				message_box();
				$blk[1][$i] = ob_get_contents();
				ob_end_clean();
			} else {
				$blk[1][$i] = "";
			}
			break;
			case 't':
			if ($conf['db_t'] == "1") {
				$total_time = round(array_sum(explode(" ", microtime())) - $start_time, 3);
				$sqlnums = $db->num_queries;
				$total_time_db = round($db->total_time_db, 3);
				$total_time = ""._GENERATION.": ".$total_time." "._SEC.". "._AND." ".$sqlnums." "._GENERATION_DB." ".$total_time_db." "._SEC.".";
				$footer = $total_time;
			}
			$blk[1][$i] = $footer;
			break;
			case 'v':
			$blk[1][$i] = ($conf['variables'] == "1" && is_moder()) ? "<div align=\"left\">".variables()."</div>" : "";
			break;
			case 'q':
			if ($conf['db_t_q'] == "1" && is_moder()) {
				$total_time_query = $db->time_query;
				$blk[1][$i] = "<br><br><div align=\"left\">".$total_time_query."</div>";
			} else {
				$blk[1][$i] = "";
			}
			break;
			default:
			$telo = explode(",", $telo);
			ob_start();
			blocks($telo[0], $telo[1]);
			$blk[1][$i] = ob_get_contents();
			ob_end_clean();
		}
	}
	return preg_replace($blk[0], $blk[1], $str);
}

# Format block
function blocks($side, $fly="") {
	global $prefix, $db, $conf, $currentlang, $name, $home, $pos, $b_id, $blockfile;
	static $barr;
	$querylang = ($conf['multilingual'] == 1) ? "AND (blanguage='$currentlang' OR blanguage='')" : "";
	$pos = strtolower($side[0]);
	$side = $pos;
	if (!isset($barr)) {
		$result = $db->sql_query("SELECT bid, bkey, title, content, url, blockfile, view, expire, action, bposition, which FROM ".$prefix."_blocks WHERE active='1' $querylang ORDER BY weight ASC");
		while(list($bid, $bkey, $title, $content, $url, $blockfile, $view, $expire, $action, $bposition, $which) = $db->sql_fetchrow($result)) {
			$bid = intval($bid);
			$view = intval($view);
			$where_mas = explode(",", $which);
			$barr[] = array($bid, $bkey, $title, $content, $url, $blockfile, $view, $expire, $action, $bposition, $where_mas);
		}
	}
	if ($fly != "") {
		$b_id = 0;
		$flag = 0;
		$blockfile = "";
		if (false === strpos($fly, "-")) {
			$b_id = intval($fly);
		} else {
			$blockfile = trim($fly);
		}
		$ci = sizeof($barr);
		for ($i = 0; $i < $ci; $i++) {
			if (($b_id != 0 && $barr[$i][0] == $b_id) || ($blockfile != "" && $barr[$i][5] == $blockfile)) {
				list($bid, $bkey, $title, $content, $url, $blockfile, $view, $expire, $action, $bposition, $where_mas) = $barr[$i];
				$b_id = $bid;
				$flag = 1;
				break;
			}
		}
		if ($flag == 1) {
			if (in_array("flyfix", $where_mas)) {
				switch ($where_mas[0]) {
					case "all":
					$flag_where = 1;
					break;
					case "":
					$flag_where = 1;
					break;
					case "infly":
					$flag_where = 0;
					break;
					case "home":
					$flag_where = ($home == 1) ? 1 : 0;
					break;
					case "ihome":
					if ($home == 1) $flag_where = 1;
					default:
					if (!$home){
						foreach ($where_mas as $val) {
							if ($val == $name) $flag_where = 1;
						}
					}
					break;
				}
				if (in_array("otricanie", $where_mas)) $flag_where = ($flag_where) ? 0 : 1;
			} else {
				$flag_where = 1;
			}
			if ($flag_where == 1) {
				if ($view == 0) {
					return render_blocks($side, $blockfile, $title, $content, $bid, $url);
				} elseif ($view == 1 && is_user() || is_moder()) {
					return render_blocks($side, $blockfile, $title, $content, $bid, $url);
				} elseif ($view == 2 && is_moder()) {
					return render_blocks($side, $blockfile, $title, $content, $bid, $url);
				} elseif ($view == 3 && !is_user() || is_moder()) {
					return render_blocks($side, $blockfile, $title, $content, $bid, $url);
				}
			}
		}
	} else {
		$ci = sizeof($barr);
		for ($i = 0; $i < $ci; $i++) {
			if ($barr[$i][9] != $side) continue;
			$flag_where = 0;
			$where_mas = $barr[$i][10];
			switch ($where_mas[0]) {
				case "all":
				$flag_where = 1;
				break;
				case "":
				$flag_where = 1;
				break;
				case "infly":
				$flag_where = 0;
				break;
				case "home":
				$flag_where = ($home == 1) ? 1 : 0;
				break;
				case "ihome":
				if ($home == 1) $flag_where = 1;
				default:
				if (!$home) {
					foreach ($where_mas as $val) {
						if ($val == $name) $flag_where = 1;
					}
				}
				break;
			}
			if (in_array("otricanie", $where_mas)) $flag_where = ($flag_where) ? 0 : 1;
			if ($flag_where == 1) {
				list($bid, $bkey, $title, $content, $url, $blockfile, $view, $expire, $action, $bposition, $where_mas) = $barr[$i];
				$b_id = $bid;
				if ($expire && $expire < time()) {
					if ($action == "d") {
						$db->sql_query("UPDATE ".$prefix."_blocks SET active='0', expire='0' WHERE bid='$bid'");
						return;
					} elseif ($action == "r") {
						$db->sql_query("DELETE FROM ".$prefix."_blocks WHERE bid='$bid'");
						return;
					}
				}
				switch ($bkey) {
					case "admin":
					adminblock();
					break;
					case "userbox":
					userblock();
					break;
					default:
					if ($view == 0) {
						render_blocks($side, $blockfile, $title, $content, $bid, $url);
					} elseif ($view == 1 && is_user() || is_moder()) {
						render_blocks($side, $blockfile, $title, $content, $bid, $url);
					} elseif ($view == 2 && is_moder()) {
						render_blocks($side, $blockfile, $title, $content, $bid, $url);
					} elseif ($view == 3 && !is_user() || is_moder()) {
						render_blocks($side, $blockfile, $title, $content, $bid, $url);
					}
					break;
				}
			}
		}
	}
}

# Format block
function render_blocks($side, $blockfile, $blocktitle, $content, $bid, $url) {
	global $showbanners, $foot;
	if ($url == "") {
		if ($blockfile != "") {
			if (file_exists("blocks/".$blockfile."")) {
				include("blocks/".$blockfile."");
			} else {
				$content = "<center>"._BLOCKPROBLEM."</center>";
			}
		}
		if (!isset($content) || empty($content)) $content = "<center>"._BLOCKPROBLEM2."</center>";
		switch($side) {
			case "b":
			$showbanners = $content;
			break;
			case "f":
			$foot = $content;
			break;
			case "n":
			echo $content;
			break;
			case "p":
			return $content;
			break;
			case "o":
			return themesidebox($blocktitle, $content);
			break;
			default:
			themesidebox($blocktitle, $content);
			break;
		}
	} else {
		rss_load($bid);
	}
}

# Format rating
function rating() {
	global $db, $prefix, $user;
	include("config/config_ratings.php");
	$id = (isset($_GET['id'])) ? intval($_GET['id']) : "";
	$mod = (isset($_GET['mod'])) ? analyze($_GET['mod']) : "";
	$rating = (isset($_GET['rating'])) ? intval($_GET['rating']) : 0;
	$con = explode("|", $confra[strtolower($mod)]);
	if ($id && $mod) {
		if ($mod == "account") {
			$query = "user_votes, user_totalvotes FROM ".$prefix."_users WHERE user_id='$id'";
		} elseif ($mod == "files") {
			$query = "votes, totalvotes FROM ".$prefix."_files WHERE lid='$id'";
		} elseif ($mod == "news") {
			$query = "ratings, score FROM ".$prefix."_stories WHERE sid='$id'";
		}
		$ip = getip();
		$past = time() - intval($con[0]);
		$cookies = (isset($_COOKIE[''.substr($mod, 0, 2).'-'.$id.''])) ? intval($_COOKIE[''.substr($mod, 0, 2).'-'.$id.'']) : "";
		$uid = (is_user()) ? intval(substr($user[0], 0, 11)) : 0;
		$db->sql_query("DELETE FROM ".$prefix."_rating WHERE time<'$past' AND modul='$mod'");
		list($num) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_rating WHERE (mid='$id' AND modul='$mod' AND host='$ip') OR (mid='$id' AND modul='$mod' AND uid='$uid' AND uid!='0')"));
		if ($cookies == $id || $num > 0) {
			list($votes, $totalvotes) = $db->sql_fetchrow($db->sql_query("SELECT ".$query.""));
			echo "".vote_graphic($votes, $totalvotes)."";
		} elseif (!$cookies && !$num && !$rating) {
			list($votes, $totalvotes) = $db->sql_fetchrow($db->sql_query("SELECT ".$query.""));
			$votes = (intval($votes)) ? $votes : 1;
			$width = number_format($totalvotes/$votes, 2) * 17;
			echo "<ul class=\"urating\">"
			."<li class=\"crating\" style=\"width:".$width."px;\"></li>"
			."<li><div class=\"out1\" OnMouseOver=\"this.className='over1';\" OnMouseOut=\"this.className='out1';\" OnClick=\"ajax('ajax.php?go=1&op=rating&mod=$mod&id=$id&rating=1', 'rate".$id."');\" title=\""._RATE1."\"></div></li>"
			."<li><div class=\"out2\" OnMouseOver=\"this.className='over2';\" OnMouseOut=\"this.className='out2';\" OnClick=\"ajax('ajax.php?go=1&op=rating&mod=$mod&id=$id&rating=2', 'rate".$id."');\" title=\""._RATE2."\"></div></li>"
			."<li><div class=\"out3\" OnMouseOver=\"this.className='over3';\" OnMouseOut=\"this.className='out3';\" OnClick=\"ajax('ajax.php?go=1&op=rating&mod=$mod&id=$id&rating=3', 'rate".$id."');\" title=\""._RATE3."\"></div></li>"
			."<li><div class=\"out4\" OnMouseOver=\"this.className='over4';\" OnMouseOut=\"this.className='out4';\" OnClick=\"ajax('ajax.php?go=1&op=rating&mod=$mod&id=$id&rating=4', 'rate".$id."');\" title=\""._RATE4."\"></div></li>"
			."<li><div class=\"out5\" OnMouseOver=\"this.className='over5';\" OnMouseOut=\"this.className='out5';\" OnClick=\"ajax('ajax.php?go=1&op=rating&mod=$mod&id=$id&rating=5', 'rate".$id."');\" title=\""._RATE5."\"></div></li>"
			."</ul>";
		} elseif (!$cookies && !$num && $rating) {
			setcookie("".substr($mod, 0, 2)."-".$id."", $id, time() + intval($con[0]));
			$new = time();
			$db->sql_query("INSERT INTO ".$prefix."_rating VALUES (NULL, '$id', '$mod', '$new', '$uid', '$ip')");
			 if ($mod == "account" || $mod == "members") {
				$db->sql_query("UPDATE ".$prefix."_users SET user_votes=user_votes+1, user_totalvotes=user_totalvotes+$rating WHERE user_id='$id'");
				update_points(2);
			} elseif ($mod == "files") {
				$db->sql_query("UPDATE ".$prefix."_files SET votes=votes+1, totalvotes=totalvotes+$rating WHERE lid='$id'");
				update_points(12);
			} elseif ($mod == "news") {
				$db->sql_query("UPDATE ".$prefix."_stories SET score=score+$rating, ratings=ratings+1 WHERE sid='$id'");
				update_points(33);
			}
			list($votes, $totalvotes) = $db->sql_fetchrow($db->sql_query("SELECT ".$query.""));
			echo "".vote_graphic($votes, $totalvotes)."";
		}
	}
}

# Format BB Code and Smilies
function textarea($id, $name, $var, $mod, $rows) {
	global $conf;
	$desc = ($var) ? $var : save_text($_POST[$name]);
	include("config/config_uploads.php");
	$con = explode("|", $confup[strtolower($mod)]);
	$style = (defined("ADMIN_FILE")) ? "admin" : strtolower($mod);
	if ((defined("ADMIN_FILE") && $conf['redaktor'] == 1) || (!defined("ADMIN_FILE"))) {
		$code = "<script language=\"JavaScript\" type=\"text/javascript\" src=\"ajax/insert_code.js\"></script>"
		."<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td><div class=\"editor\">"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"RowsTextarea('".$id."', 1)\"><img src=\"".img_find("editor/plus")."\" title=\""._EPLUS."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"RowsTextarea('".$id."', 0)\"><img src=\"".img_find("editor/minus")."\" title=\""._EMINUS."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('b', '', '', '', '".$id."')\"><img src=\"".img_find("editor/bold")."\" title=\""._EBOLD."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('i', '', '', '', '".$id."')\"><img src=\"".img_find("editor/italic")."\" title=\""._EITALIC."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('u', '', '', '', '".$id."')\"><img src=\"".img_find("editor/underline")."\" title=\""._EUNDERLINE."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('s', '', '', '', '".$id."')\"><img src=\"".img_find("editor/striket")."\" title=\""._ESTRIKET."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('li', '', '', '', '".$id."')\"><img src=\"".img_find("editor/li")."\" title=\""._ELI."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('hr', '', '', '', '".$id."')\"><img src=\"".img_find("editor/hr")."\" title=\""._EHR."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('left', '', '', '', '".$id."')\"><img src=\"".img_find("editor/left")."\" title=\""._ELEFT."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('center', '', '', '', '".$id."')\"><img src=\"".img_find("editor/center")."\" title=\""._ECENTER."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('right', '', '', '', '".$id."')\"><img src=\"".img_find("editor/right")."\" title=\""._ERIGHT."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('justify', '', '', '', '".$id."')\"><img src=\"".img_find("editor/justify")."\" title=\""._EYUSTIFY."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('code', '', '', '', '".$id."')\"><img src=\"".img_find("editor/code")."\" title=\""._CODE."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('php', '', '', '', '".$id."')\"><img src=\"".img_find("editor/php")."\" title=\"PHP - "._CODE."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('hide', '', '', '', '".$id."')\"><img src=\"".img_find("editor/hide")."\" title=\""._HIDE."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('url', '"._JINFO."', '"._JTYPE."', '"._JERROR."', '".$id."')\"><img src=\"".img_find("editor/url")."\" title=\""._EURL."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('mail', '"._JINFO."', '"._JTYPE."', '"._JERROR."', '".$id."')\"><img src=\"".img_find("editor/mail")."\" title=\""._EEMAIL."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('img', '', '', '', '".$id."')\"><img src=\"".img_find("editor/img")."\" title=\""._EIMG."\"></div>"
		."</div>"
		."<textarea id=\"".$id."\" name=\"".$name."\" cols=\"65\" rows=\"".$rows."\" class=\"".$style."\" OnKeyPress=\"TransliteFeld(this, event)\" OnSelect=\"FieldName(this, this.name)\" OnClick=\"FieldName(this, this.name)\" OnKeyUp=\"FieldName(this, this.name)\">".replace_break($desc)."</textarea>"
		."<div class=\"editor\">";
		if ((defined("ADMIN_FILE") && $con[8] == 1) || (is_user() && $con[8] == 1) || (!is_user() && $con[9] == 1)) $code .= "<div id=\"af".$id."-title\" class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\"><img title=\""._EUPLOAD."\" src=\"".img_find("editor/upload")."\"></div>";
		if (!$conf['smilies']) $code .= "<div id=\"sm".$id."-title\" class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\"><img src=\"".img_find("editor/smilie")."\" title=\""._ESMILIE."\"></div>";
		$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('quote', '', '', '', '".$id."')\"><img src=\"".img_find("editor/quote")."\" title=\""._EQUOTE."\"></div>";
		if (substr(""._LOCALE."", 0, 2) == "ru") {
			$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"translateAlltoCyrillic()\"><img src=\"".img_find("editor/rus")."\" title=\""._ERUS."\"></div>"
			."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"translateAlltoLatin()\"><img src=\"".img_find("editor/eng")."\" title=\""._ELAT."\"></div>"
			."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"changelanguage()\"><img src=\"".img_find("editor/auto")."\" title=\""._EAUTOTR."\"></div>";
		}
		$fonts = 0;
		$font = array("Arial", "Courier New", "Mistral", "Impact", "Sans Serif", "Tahoma", "Helvetica", "Verdana");
		foreach ($font as $val) if ($val != "") $fonts .= "<option style=\"font-family: ".$val.";\" value=\"".$val."\">".$val."</option>";
		$colors = 0;
		$color = array("black", "silver", "gray", "white", "maroon", "orange", "orangered", "red", "purple", "fuchsia", "green", "lime", "olive", "yellow", "navy", "blue", "teal", "aqua");
		foreach ($color as $val) if ($val != "") $colors .= "<option style=\"color: ".$val.";\" value=\"".$val."\">"._ECOLOR."</option>";
		$fsizes = 0;
		$fsize = array("8", "10", "12", "14", "16", "18", "20", "22", "24", "26", "28", "30", "32");
		foreach ($fsize as $val) if ($val != "") $fsizes .= "<option value=\"".$val."\">"._ESIZE." ".$val."</option>";
		$code .= "<div class=\"editorselect\"><select name=\"family\" OnChange=\"InsertCode('family', this.options[this.selectedIndex].value, '', '', '".$id."')\">".$fonts."</select></div>"
		."<div class=\"editorselect\"><select name=\"color\" OnChange=\"InsertCode('color', this.options[this.selectedIndex].value, '', '', '".$id."')\">".$colors."</select></div>"
		."<div class=\"editorselect\"><select name=\"size\" OnChange=\"InsertCode('size', this.options[this.selectedIndex].value, '', '', '".$id."')\">".$fsizes."</select></div></div>";
		if ($conf['smilies'] == 1) {
			$code .= "<div class=\"smilies\">";
			for ($i = 1; $i < 19; $i++) {
				$i = ($i < 10) ? "0".$i ."" : $i;
				$code .= " <img src=\"images/smilies/$i.gif\" OnClick=\"AddSmile(' *$i');\" style=\"cursor: pointer; margin: 3px 2px 0px 0px;\" alt=\""._SMILIE." - $i\" title=\""._SMILIE." - $i\">";
			}
			$code .= "</div>";
		} elseif ($conf['smilies'] == 2) {
			$code .= "<div class=\"smilies\">";
			$i = 1;
			$dir = opendir("images/smilies");
			while ($entry = readdir($dir)) {
				if (preg_match("/(\.gif|\.png|\.jpg|\.jpeg)$/is", $entry) && $entry != "." && $entry != "..") {
					$i = ($i < 10) ? "0".$i ."" : $i;
					$code .= " <img src=\"images/smilies/$i.gif\" OnClick=\"AddSmile(' *$i');\" style=\"cursor: pointer; margin: 3px 2px 0px 0px;\" alt=\""._SMILIE." - $i\" title=\""._SMILIE." - $i\">";
					$i++;
				}
			}
			closedir($dir);
			$code .= "</div>";
		} else {
			$code .= "<div id=\"sm".$id."\" class=\"smilies\"><script language=\"JavaScript\" type=\"text/javascript\">var edits = new SwitchCont('sm".$id."', '2');</script>";
			$i = 1;
			$dir = opendir("images/smilies");
			while ($entry = readdir($dir)) {
				if (preg_match("/(\.gif|\.png|\.jpg|\.jpeg)$/is", $entry) && $entry != "." && $entry != "..") {
					$i = ($i < 10) ? "0".$i ."" : $i;
					$code .= " <img src=\"images/smilies/$i.gif\" OnClick=\"AddSmile(' *$i');\" style=\"cursor: pointer; margin: 3px 2px 0px 0px;\" alt=\""._SMILIE." - $i\" title=\""._SMILIE." - $i\">";
					$i++;
				}
			}
			closedir($dir);
			$code .= "</div>";
		}
		if ((defined("ADMIN_FILE") && $con[8] == 1) || (is_user() && $con[8] == 1) || (!is_user() && $con[9] == 1)) {
			$code .= "<div id=\"af".$id."\" class=\"smilies\">";
			if ($id != 2) {
				$fsizel = $con[2] / 1048576;
				$code .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"ajax/swfupload/swfupload.css\">
				<script type=\"text/javascript\" src=\"ajax/swfupload/swfupload.js\"></script>
				<script type=\"text/javascript\" src=\"ajax/swfupload/swfupload.swfobject.js\"></script>
				<script type=\"text/javascript\" src=\"ajax/swfupload/swfupload.queue.js\"></script>
				<script type=\"text/javascript\" src=\"ajax/swfupload/fileprogress.js\"></script>
				<script type=\"text/javascript\" src=\"ajax/swfupload/handlers.js\"></script>
				<script type=\"text/javascript\">
				var swfu;
				SWFUpload.onload = function () {
					var settings = {
						flash_url : \"upload.swf\",
						upload_url: \"ajax.php?go=4&mod=".$mod."\",
						file_size_limit : \"".$fsizel." MB\",
						file_types : \"*.".str_replace(",", ";*.", $con[0])."\",
						file_types_description : \"All Files\",
						file_upload_limit : ".$con[5].",
						file_queue_limit : 0,
						custom_settings : {
							progressTarget : \"fsUploadProgress\",
							cancelButtonId : \"btnCancel\"
						},
						debug: false,
						
						button_placeholder_id : \"spanButtonPlaceholder\",
						button_width: 80,
						button_height: 20,
						button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
						button_cursor: SWFUpload.CURSOR.HAND,
						
						swfupload_loaded_handler : swfUploadLoaded,
						file_queued_handler : fileQueued,
						file_queue_error_handler : fileQueueError,
						file_dialog_complete_handler : fileDialogComplete,
						upload_start_handler : uploadStart,
						upload_progress_handler : uploadProgress,
						upload_error_handler : uploadError,
						upload_success_handler : uploadSuccess,
						upload_complete_handler : uploadComplete,
						queue_complete_handler : queueComplete,
						
						minimum_flash_version : \"9.0.28\",
						swfupload_pre_load_handler : swfUploadPreLoad,
						swfupload_load_failed_handler : swfUploadLoadFailed
					};
					swfu = new SWFUpload(settings);
				}
				</script>
				
				<div id=\"divSWFUploadUI\">
				<fieldset id=\"fsUploadProgress\" style=\"margin: 5px;\">
				<legend>"._UPLOADINFO."</legend><div align=\"left\" style=\"margin: 3px;\">"
				._FTYPE.": ".str_replace(",", ", ", $con[0])."<br>"
				._FSIZEALL.": ".files_size($con[1])."<br>"
				._FSIZE.": ".files_size($con[2])."<br>"
				._AWIDTH.": ".$con[3]." px<br>"
				._AHEIGHT.": ".$con[4]." px<br>"
				._FILEUP.": ".$con[5]."<br>"
				."</div></fieldset>
				<p id=\"divStatus\">0 "._FILEISUP."</p>
				<p>
				<span id=\"spanButtonPlaceholder\"></span>
				<input id=\"btnUpload\" type=\"button\" value=\""._UPLOAD."\" class=\"fbutton\">
				<input id=\"btnCancel\" type=\"button\" value=\""._CANALLUP."\" disabled=\"disabled\" class=\"fbutton\">
				<input type=\"button\" value=\""._UPDATE."\" class=\"fbutton\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=$mod&id=$id', 'f".$id."');\"></p><br></div>
				<noscript>Were sorry. SWFUpload could not load. You must have JavaScript enabled to enjoy SWFUpload.</noscript>
				<div id=\"divLoadingContent\" style=\"display: none;\">SWFUpload is loading. Please wait a moment...</div>
				<div id=\"divLongLoading\" style=\"display: none;\">SWFUpload is taking a long time to load or the load has failed. Please make sure that the Flash Plugin is enabled and that a working version of the Adobe Flash Player is installed.</div>
				<div id=\"divAlternateContent\" style=\"display: none;\">Were sorry.  SWFUpload could not load.  You may need to install or upgrade Flash Player. Visit the <a href=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" target=\"_blank\">Adobe website</a> to get the Flash Player.</div>";
			} else {
				$code .= "<input type=\"button\" value=\""._UPDATE."\" style=\"margin: 5px 0 0 0;\" class=\"fbutton\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=$mod&id=$id', 'f".$id."');\">";
			}
			$code .= "<script type=\"text/javascript\">ajax('ajax.php?go=3&op=show_files&mod=$mod&id=$id', 'f".$id."');</script><div id=\"f".$id."\"></div></div><script language=\"JavaScript\" type=\"text/javascript\">var editu = new SwitchCont('af".$id."', '2');</script>";
		}
	} elseif (defined("ADMIN_FILE") && $conf['redaktor'] == 2) {
		if (!preg_match("#blocks|configure|editor|groups|rss_conf|security|template|style#i", $_GET['op'])) {
			static $jscript;
			if (!isset($jscript)) {
				$code = "<script type=\"text/javascript\" src=\"modules/tiny_mce/tiny_mce.js\"></script>
				<script type=\"text/javascript\">
				tinyMCE.init({
					mode : \"textareas\",
					theme : \"advanced\",
					plugins : \"safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,images,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template\",
					
					theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect\",
					theme_advanced_buttons2 : \"bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code\",
					theme_advanced_buttons3 : \"save,newdocument,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,forecolor,backcolor,|,preview,images\",
					theme_advanced_buttons4 : \"hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen\",
					theme_advanced_buttons5 : \"tablecontrols\",
					theme_advanced_buttons6 : \"insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak\",
					
					theme_advanced_toolbar_location : \"top\",
					theme_advanced_toolbar_align : \"center\",
					theme_advanced_statusbar_location : \"bottom\",
					theme_advanced_resizing : true,
					
					language: \"".substr(_LOCALE, 0, 2)."\",
					content_css : \"css/content.css\",
					
					template_external_list_url : \"lists/template_list.js\",
					external_link_list_url : \"lists/link_list.js\",
					external_image_list_url : \"lists/image_list.js\",
					media_external_list_url : \"lists/media_list.js\",
					
					template_replace_values : {
						username : \"Some User\",
						staffid : \"991234\"
					}
				});
				</script>";
				$jscript = 1;
			} else {
				$code = "";
			}
		}
		$code .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>"
		."<textarea id=\"".$id."\" name=\"".$name."\" cols=\"65\" rows=\"".$rows."\" class=\"".$style."\">".$desc."</textarea>";
	} elseif (defined("ADMIN_FILE") && $conf['redaktor'] == 3) {
		ob_start();
		include("modules/spaw2/spaw.inc.php");
		$sp = new SpawEditor($name, $desc);
		$sp->show();
		$code = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>".ob_get_contents()."";
		ob_end_clean();
	} elseif (defined("ADMIN_FILE") && $conf['redaktor'] == 4) {
		ob_start();
		include_once("modules/fckeditor/fckeditor.php") ;
		$oFCKeditor = new FCKeditor($name);
		$oFCKeditor->BasePath = "modules/fckeditor/";
		$oFCKeditor->Config['AutoDetectLanguage'] = false;
		$oFCKeditor->Config['DefaultLanguage'] = substr(_LOCALE, 0, 2);
		$oFCKeditor->Width = "400px";
		$oFCKeditor->Height = "400px";
		$oFCKeditor->Value = $desc;
		$oFCKeditor->Create() ;
		$code = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>".ob_get_contents()."";
		ob_end_clean();
	} else {
		$code = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>"
		."<textarea id=\"".$id."\" name=\"".$name."\" cols=\"65\" rows=\"".$rows."\" class=\"".$style."\">".$desc."</textarea>";
	}
	$code .= "</td></tr></table>";
	return $code;
}

# Format Page
function get_page($mod) {
	open();
	echo "<h5>[ <a href=\"javascript:history.go(-1)\" title=\""._PAGEBACK."\">"._PAGEBACK."</a> | <a href=\"index.php?name=$mod\" title=\""._PAGEHOME."\">"._PAGEHOME."</a> | <a href=\"#\" title=\""._PAGETOP."\">"._PAGETOP."</a> ]</h5>";
	close();
}

# Format Nummer Page
function num_page($mod="", $numstories, $numpages, $storynum, $module_link="") {
	global $admin_file;
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	if ($numpages > 1) {
		if (defined("ADMIN_FILE")) {
			$index = $admin_file;
			$module = "";
		} else {
			$index = "index";
			$module = "name=".$mod."&";
		}
		open();
		echo "<div align=\"center\" class=\"pagelink\"><h4>"._OVERALL." $numstories "._ON." $numpages "._PAGE_S." $storynum "._PERPAGE."</h4>";
		if ($num > 1) {
			$prevpage = $num - 1;
			echo "<a href=\"".$index.".php?".$module."".$module_link."num=$prevpage\" title=\"&lt;&lt;\">&lt;&lt;</a> ";
		}
		for ($i = 1; $i < $numpages+1; $i++) {
			if ($i == $num) {
				echo "<span title=\"$i\">$i</span>";
			} else {
				if ((($i > ($num - 8)) && ($i < ($num + 8))) OR ($i == $numpages) || ($i == 1)) echo "<a href=\"".$index.".php?".$module."".$module_link."num=$i\" title=\"$i\">$i</a>";
			}
			if ($i < $numpages) {
				if (($i > ($num - 9)) && ($i < ($num + 8))) echo " ";
				if (($num > 9) && ($i == 1)) echo " <span>...</span>";
				if (($num < ($numpages - 8)) && ($i == ($numpages - 1))) echo "<span>...</span> ";
			}
		}
		if ($num < $numpages) {
			$nextpage = $num + 1;
			echo " <a href=\"".$index.".php?".$module."".$module_link."num=$nextpage\" title=\"&gt;&gt;\">&gt;&gt;</a>";
		}
		echo "</div>";
		close();
	}
}

# Format Nummer Pages
function num_pages($mod="", $numstories, $numpages, $storynum, $module_link="") {
	global $admin_file;
	$pag = isset($_GET['pag']) ? intval($_GET['pag']) : "1";
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	if ($numpages > 1) {
		if (defined("ADMIN_FILE")) {
			$index = $admin_file;
			$module = "";
		} else {
			$index = "index";
			$module = "name=".$mod."&";
		}
		open();
		echo "<div align=\"center\" class=\"pagelink\"><h4>"._OVERALL." $numstories "._ON." $numpages "._PAGE_S." $storynum "._PERPAGE."</h4>";
		if ($pag > 1) {
			$prevpage = $pag - 1;
			echo "<a href=\"".$index.".php?".$module."".$module_link."pag=$prevpage&num=$num\" title=\"&lt;&lt;\">&lt;&lt;</a> ";
		}
		for ($i = 1; $i < $numpages+1; $i++) {
			if ($i == $pag) {
				echo "<span title=\"$i\">$i</span>";
			} else {
				if ((($i > ($pag - 8)) && ($i < ($pag + 8))) OR ($i == $numpages) || ($i == 1)) echo "<a href=\"".$index.".php?".$module."".$module_link."pag=$i&num=$num\" title=\"$i\"><b>$i</b></a>";
			}
			if ($i < $numpages) {
				if (($i > ($pag - 9)) && ($i < ($pag + 8))) echo " ";
				if (($pag > 9) && ($i == 1)) echo " <span>...</span>";
				if (($pag < ($numpages - 8)) && ($i == ($numpages - 1))) echo "<span>...</span> ";
			}
		}
		if ($pag < $numpages) {
			$nextpage = $pag + 1;
			echo " <a href=\"".$index.".php?".$module."".$module_link."pag=$nextpage&num=$num\" title=\"&gt;&gt;\">&gt;&gt;</a>";
		}
		echo "</div>";
		close();
	}
}

# Check type upload file
function check_file($type, $typefile) {
	$strtypefile = str_replace(",", "|", $typefile);
	if (!preg_match("#".$strtypefile."#i", $type) || preg_match("#php.*|js|htm|html|phtml|cgi|pl|perl|asp#i", $type)) return ""._ERROR_FILE."";
}

# Check size upload file
function check_size($file, $width, $height) {
	list($imgwidth, $imgheight) = getimagesize($file);
	if ($imgwidth > $width || $imgheight > $height) return ""._ERROR_SIZE."";
}

# Crypted md5 and salt
function md5_salt($pass) {
	global $conf;
	$crypt = md5(md5($conf['lic_f']).md5($pass));
	return $crypt;
}

# Upload file
function upload($typ, $directory, $typefile, $maxsize, $namefile, $width, $height) {
	global $user, $stop;
	if ($typ >= 1 && intval($_FILES['userfile']['size'])) {
		if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
			if ($_FILES['userfile']['size'] > $maxsize) {
				$stop = _ERROR_BIG;
				return 0;
			} else {
				$type = strtolower(end(explode(".", $_FILES['userfile']['name'])));
				if (!check_file($type, $typefile) && !check_size($_FILES['userfile']['tmp_name'], $width, $height)) {
					$newname = ($namefile) ? $namefile."-".gen_pass(10).".".$type : gen_pass(15).".".$type;
					if (file_exists($directory."/".$newname)) {
						$stop = _ERROR_EXIST;
						return 0;
					} else {
						$res = copy($_FILES['userfile']['tmp_name'], $directory."/".$newname);
						if (!$res) {
							$stop = _ERROR_UP;
							return 0;
						} else {
							return $newname;
						}
					}
				} else {
					$stop = (!check_file($type, $typefile)) ? check_size($_FILES['userfile']['tmp_name'], $width, $height) : check_file($type, $typefile);
					return 0;
				}
			}
		} else {
			$stop = _ERROR_DOWN;
			return 0;
		}
	}
	if ($typ >= 2 && intval($_FILES['Filedata']['size'])) {
		if (isset($_FILES["Filedata"]) && is_uploaded_file($_FILES["Filedata"]["tmp_name"]) && $_FILES["Filedata"]["error"] == 0) {
			if ($_FILES['Filedata']['size'] > $maxsize) {
				header("HTTP/1.1 500 Internal Server Error");
				exit(_ERROR_BIG);
			} else {
				$type = strtolower(end(explode(".", $_FILES['Filedata']['name'])));
				if (!check_file($type, $typefile) && !check_size($_FILES['Filedata']['tmp_name'], $width, $height)) {
					if (is_admin() && !is_user()) {
						$newname = ($namefile) ? $namefile."-".gen_pass(10).".".$type : gen_pass(15).".".$type;
					} else {
						$uname = (is_user()) ? intval($user[0]) : "0";
						$newname = ($namefile) ? $namefile."-".gen_pass(10)."-".$uname.".".$type : gen_pass(15).".".$type;
					}
					if (file_exists($directory."/".$newname)) {
						header("HTTP/1.1 500 Internal Server Error");
						exit(_ERROR_EXIST);
					} else {
						$res = copy($_FILES['Filedata']['tmp_name'], $directory."/".$newname);
						if (!$res) {
							header("HTTP/1.1 500 Internal Server Error");
							exit(_ERROR_UP);
						} else {
							echo $newname;
						}
					}
				} else {
					$info = (!check_file($type, $typefile)) ? check_size($_FILES['Filedata']['tmp_name'], $width, $height) : check_file($type, $typefile);
					header("HTTP/1.1 500 Internal Server Error");
					exit($info);
				}
			}
		} else {
			header("HTTP/1.1 500 Internal Server Error");
			exit(_ERROR_DOWN);
		}
	}
	if ($typ >= 3 && $_POST['sitefile'] != "") {
		$afile = str_replace(array("&", "?", "#"), "", $_POST['sitefile']);
		$type = strtolower(end(explode(".", $afile)));
		if (!check_file($type, $typefile) && !check_size($_POST['sitefile'], $width, $height)) {
			$fn = $_POST['sitefile'];
			$path_sitefile = @fopen($fn, "rb");
			if (!$path_sitefile) {
				$stop = _ERROR_DOWN;
				return 0;
			} else {
				$newname = ($namefile) ? $namefile."-".gen_pass(10).".".$type : gen_pass(15).".".$type;
				$directoryp = $directory."/".basename($newname);
				if (file_exists($directoryp)) {
					$stop = _ERROR_EXIST;
					return 0;
				} else {
					while (!feof($path_sitefile)) $data .= fread($path_sitefile, 1024);
					fclose($path_sitefile);
					$path_sitefile = @fopen($directory."/".basename($newname), "wb");
					if (!$path_sitefile) {
						$stop = _ERROR_UP;
						return 0;
					} else {
						fwrite($path_sitefile, $data);
						fclose($path_sitefile);
						if (file_exists($directoryp)) {
							if (filesize($directoryp) > $maxsize) {
								@unlink($directoryp);
								$stop = _ERROR_BIG;
								return 0;
							} else {
								return $newname;
							}
						}
					}
				}
			}
		} else {
			$stop = (!check_file($type, $typefile)) ? check_size($_POST['sitefile'], $width, $height) : check_file($type, $typefile);
			return 0;
		}
	}
}

# Format language
function language($lang="") {
	$dir = opendir("language");
	$content = "<option value=\"\">"._ALL."</option>";
	while ($file = readdir($dir)) {
		if (preg_match("/^lang\-(.+)\.php/", $file, $matches)) {
			$langfound = $matches[1];
			$title = ucfirst($langfound);
			$selected = ($lang == $langfound) ? "selected" : "";
			$content .= "<option value=\"$langfound\" $selected>".$title."</option>";
		}
	}
	closedir($dir);
	return $content;
}

# Format module
function modul($modul="") {
	$dir = opendir("modules");
	while ($file = readdir($dir)) {
		if (!preg_match("/\./", $file)) {
			$selected = ($modul == $file) ? "selected" : "";
			$content .= "<option value=\"$file\" $selected>".$file."</option>";
		}
	}
	closedir($dir);
	return $content;
}

# Format categorie module
function cat_modul($name, $class, $modul) {
	$content = "<select name=\"".$name."\" class=\"".$class."\">";
	$cname = array(_FILES, _NEWS);
	$mods = array("files", "news");
	for ($i = 0; $i < count($mods); $i++) {
		$selected = ($modul == $mods[$i]) ? "selected" : "";
		$content .= "<option value=\"".$mods[$i]."\" $selected>".$cname[$i]." - ".$mods[$i]."</option>";
	}
	$content .= "</select>";
	return $content;
}

# Format image preview PHP GD
function create_img_gd($imgfile, $imgthumb, $newwidth) {
	if (function_exists("imagecreate")) {
		$imginfo = getimagesize($imgfile);
		switch($imginfo[2]) {
			case 1:
			$type = IMG_GIF;
			break;
			case 2:
			$type = IMG_JPG;
			break;
			case 3:
			$type = IMG_PNG;
			break;
			case 4:
			$type = IMG_WBMP;
			break;
			default:
			return $imgfile;
			break;
		}
		switch($type) {
			case IMG_GIF:
			if (!function_exists("imagecreatefromgif")) return $imgfile;
			$srcImage = imagecreatefromgif("$imgfile");
			break;
			case IMG_JPG:
			if (!function_exists("imagecreatefromjpeg")) return $imgfile;
			$srcImage = imagecreatefromjpeg($imgfile);
			break;
			case IMG_PNG:
			if(!function_exists("imagecreatefrompng")) return $imgfile;
			$srcImage = imagecreatefrompng("$imgfile");
			break;
			case IMG_WBMP:
			if (!function_exists("imagecreatefromwbmp")) return $imgfile;
			$srcImage = imagecreatefromwbmp("$imgfile");
			break;
			default:
			return $imgfile;
		}
		if ($srcImage){
			$srcWidth = $imginfo[0];
			$srcHeight = $imginfo[1];
			$ratioWidth = $srcWidth / $newwidth;
			$destWidth = $newwidth;
			$destHeight = $srcHeight / $ratioWidth;
			$destImage = imagecreatetruecolor($destWidth, $destHeight);
			imagealphablending($destImage, true);
			imagealphablending($srcImage, false);
			imagecopyresized($destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);
			switch($type) {
				case IMG_GIF:
				imagegif($destImage, "$imgthumb");
				break;
				case IMG_JPG:
				imagejpeg($destImage, "$imgthumb");
				break;
				case IMG_PNG:
				imagepng($destImage, "$imgthumb");
				break;
				case IMG_WBMP:
				imagewbmp($destImage, "$imgthumb");
				break;
			}
			imagedestroy($srcImage);
			imagedestroy($destImage);
			return $imgthumb;
		} else {
			return $imgfile;
		}
	} else {
		return $imgfile;
	}
}

# Format captcha random
function captcha_random($id="") {
	global $conf;
	if ((extension_loaded("gd") && $id == 2) || (extension_loaded("gd") && !is_user())) {
		$content = "<div class=\"left\">"._SECURITYCODE.":</div><div class=\"center\"><img src=\"index.php?captcha=1\" border=\"1\" title=\""._SECURITYCODE."\" alt=\""._SECURITYCODE."\"></div>"
		."<div class=\"left\">"._TYPESECCODE.":</div><div class=\"center\"><input type=\"text\" name=\"check\" size=\"10\" maxlength=\"6\" style=\"width: 75px;\" class=\"".$conf['style']."\"></div>";
		return $content;
	}
}

# Format captcha check
function captcha_check($id="") {
	global $conf;
	if (($id == 2) || ($id == 1 && !is_user()) || ($_POST['posttype'] == "save" && !is_user())) {
		$code = substr(hexdec(md5("".date("F j")."".$_SESSION['captcha']."".$conf['sitekey']."")), 2, 6);
		unset($_SESSION['captcha']);
		if (extension_loaded("gd") && $code != intval($_POST['check'])) {
			return 1;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}

# Format image key for captcha
switch(isset($_GET['captcha'])) {
	case "1":
	unset($_SESSION['captcha']);
	$random = gen_pass(10);
	$_SESSION['captcha'] = $random;
	$code = substr(hexdec(md5("".date("F j")."".$random."".$conf['sitekey']."")), 2, 6);
	Header("Content-type: image/jpeg");
	$image = imagecreatefromjpeg(img_find("misc/code_bg"));
	$color = imagecolorallocate($image, 100, 100, 100);
	imagettftext($image, 14, rand(-3, 3), rand(5, 15), 18, $color, "config/font/".$conf['font'].".ttf", $code);
	imagejpeg($image, "", $conf['quality']);
	imagedestroy($image);
	exit;
	break;
}
?>