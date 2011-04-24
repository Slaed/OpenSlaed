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

# Theme include
function get_theme_inc() {
	global $theme;
	$theme = ($theme) ? $theme : get_theme();
	if (file_exists("templates/".$theme."/index.php")) {
		include_once("templates/".$theme."/index.php");
	} else {
		include_once("function/template.php");
	}
}

# Format theme file
function get_theme_file($name) {
	global $home, $conf, $op;
	$theme = get_theme();
	$cat = (isset($_GET['cat'])) ? intval($_GET['cat']) : "";
	if ($home) {
		if (file_exists("templates/".$theme."/".$name."-home.html")) {
			$fname = $name."-home";
		} else {
			$fname = $name;
		}
	} elseif (isset($conf['template'])) {
		if (file_exists("templates/".$theme."/".$name."-".$conf['template'].".html")) {
			$fname = $name."-".$conf['template'];
		} else {
			$fname = $name;
		}
	} elseif (isset($conf['name']) && $op) {
		if (file_exists("templates/".$theme."/".$name."-".$conf['name']."-".$op.".html")) {
			$fname = $name."-".$conf['name']."-".$op;
		} elseif (file_exists("templates/".$theme."/".$name."-".$conf['name'].".html")) {
			$fname = $name."-".$conf['name'];
		} else {
			$fname = $name;
		}
	} elseif (isset($conf['name']) && $cat) {
		$cat = intval($_GET['cat']);
		if (file_exists("templates/".$theme."/".$name."-".$conf['name']."-cat-".$cat.".html")) {
			$fname = $name."-".$conf['name']."-cat-".$cat;
		} elseif (file_exists("templates/".$theme."/".$name."-".$conf['name'].".html")) {
			$fname = $name."-".$conf['name'];
		} else {
			$fname = $name;
		}
	} elseif (isset($conf['name'])) {
		if (file_exists("templates/".$theme."/".$name."-".$conf['name'].".html")) {
			$fname = $name."-".$conf['name'];
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
function datetime($id, $name, $time, $max, $size, $width, $class) {
	global $theme;
	static $jscript;
	$time = ($time) ? substr($time, 0, $max) : (($id == 1) ? date("Y-m-d H:i") : date("Y-m-d"));
	$format = ($id == 1) ? "%Y-%m-%d %H:%M" : "%Y-%m-%d";
	$showt = ($id == 1) ? true : false;
	if (!isset($jscript)) {
		$content = (file_exists("templates/".$theme."/calendar.css")) ? "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/".$theme."/calendar.css\">\n" : "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/admin/calendar.css\">\n";
		$content .= "<script type=\"text/javascript\" src=\"ajax/calendar/calendar.js\"></script>\n"
		."<script type=\"text/javascript\" src=\"ajax/calendar/lang/calendar-".substr(_LOCALE, 0, 2).".js\"></script>\n"
		."<script type=\"text/javascript\" src=\"ajax/calendar/calendar-setup.js\"></script>\n";
		$jscript = 1;
	} else {
		$content = "";
	}
	$content .= "<img src=\"".img_find("all/calendar")."\" border=\"0\" align=\"center\" id=\"img_".$name."\" style=\"cursor: pointer;\" title=\""._OPCAL."\"> <input type=\"text\" name=\"".$name."\" id=\"".$name."\" value=\"".$time."\" maxlength=\"".$max."\" size=\"".$size."\" style=\"width: ".$width."px\" class=\"".$class."\">"
	."<script type=\"text/javascript\">
	Calendar.setup({
		inputField: \"".$name."\",
		ifFormat: \"".$format."\",
		showsTime: \"".$showt."\",
		button: \"img_".$name."\",
		singleClick: true,
		step: 1
	});
	</script>";
	return $content;
}

# Save date and time
function save_datetime($id, $name) {
	$date = (isset($_POST[$name])) ? ((preg_match("#[^0-9- :]#", $_POST[$name])) ? "" : $_POST[$name]) : "";
	if ($id == 1) {
		if (preg_match("#^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9])$#", $date, $matches)) {
			$content = (checkdate($matches[2], $matches[3], $matches[1])) ? $date.":00" : date("Y-m-d H:i:s");
		} else {
			$content = date("Y-m-d H:i:s");
		}
	} else {
		if (preg_match("#^(\d{4})-(\d{2})-(\d{2})$#", $date, $matches)) {
			$content = (checkdate($matches[2], $matches[3], $matches[1])) ? $date : date("Y-m-d");
		} else {
			$content = date("Y-m-d");
		}
	}
	return $content;
}

function code_links($title)
{
echo <<<HTML
<script type="text/javascript">
function jFocus(elm) {if(typeof(elm) == 'string') elm = getElementById(elm);
if (elm) {      elm.focus(); elm.select();}}
</script>
<h2 style="margin: 0 0 5px 0;">Ссылки новости</h2>
<table border="0" cellspacing="0" cellpadding="2">
<script>
var today=new Date()
document.write('<tr><td><b>HTML</b>:</td><td><input type="text" value="<a href=&#34'+window.location+'&#34>{$title}</a>" size="60" onclick="jFocus(this)"></td></tr>')
</script>
<script>
var today=new Date()
document.write('<tr><td><b>BBC</b>:</td><td><input type="text" value="[url='+window.location+']{$title}[/url]"size="60" onclick="jFocus(this)"></td></tr>')
</script>
<script>
var today=new Date()
document.write('<tr><td><b>ССЫЛКА</b>:</td><td><input type="text" value="'+window.location+'"size="60" onclick="jFocus(this)"></td></tr> ')
</script>
</table>
HTML;
}

# Format radio form
function radio_form($var, $name, $id="") {
	if ($id == 1) {
		$sel1 = (!$var) ? "checked" : "";
		$sel2 = ($var) ? "checked" : "";
		$content = "<input type=\"radio\" name=\"$name\" value=\"0\" $sel1>"._YES." &nbsp;<input type=\"radio\" name=\"$name\" value=\"1\" $sel2>"._NO;
	} else {
		$sel1 = ($var) ? "checked" : "";
		$sel2 = (!$var) ? "checked" : "";
		$content = "<input type=\"radio\" name=\"$name\" value=\"1\" $sel1>"._YES." &nbsp;<input type=\"radio\" name=\"$name\" value=\"0\" $sel2>"._NO;
	}
	return $content;
}

# Format gender
function gender($gender, $id) {
	if ($id == 1) {
		if ($gender == 2) {
			$gen = _WOMAN;
		} elseif ($gender == 1) {
			$gen = _MAN;
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
function encode_attach($sourse, $mod, $title="") {
	include("config/config_uploads.php");
	include("config/config_templ.php");
	$match_count = (preg_match("#width=#i", $sourse)) ? preg_match_all("#\[attach=([a-zA-Zа-яА-Я0-9\_\-\. ]+) align=([a-zA-Zа-яА-Я0-9\_\-\.\"\ ]+) title=([a-zA-Zа-яА-Я0-9\_\-\.\"\ ]+) width=([0-5]?[0-9]?[0-9]+) height=([0-5]?[0-9]?[0-9]+)\]#si", $sourse, $date) : preg_match_all("#\[attach=([a-zA-Zа-яА-Я0-9\_\-\. ]+) align=([a-zA-Zа-яА-Я0-9\_\-\.\"\ ]+) title=([a-zA-Zа-яА-Я0-9\_\-\.\"\ ]+)\]#si", $sourse, $date);
	$con = explode("|", $confup[$mod]);
	$file = "";
	for ($i = 0; $i < $match_count; $i++) {
		$type = substr(strtolower(end(explode(".", $date[1][$i]))), 0, 5);
		$file = "uploads/".$mod."/".$date[1][$i];
		if ($type == "gif" || $type == "jpg" || $type == "jpeg" || $type == "png" || $type == "bmp") {
			$tfile = "uploads/".$mod."/thumb/".$date[1][$i];
			$dtfile = "uploads/".$mod."/thumb";
			if ($mod != "" && file_exists($file) && !file_exists($tfile)) {
				if (!file_exists($dtfile)) mkdir($dtfile);
				$thumb = create_img_gd($file, $tfile, $con[6]);
				$timg = ($thumb) ? $tfile : $file;
			} else {
				$timg = $tfile;
			}
			if (file_exists($file)) list($width, $height) = getimagesize($file);
		} else {
			$width = $date[4][$i];
			$height = $date[5][$i];
		}
		$temp = $conftp[$type];
		$temp = str_replace("[src]", $file, $temp);
		$temp = str_replace("[tsrc]", $timg, $temp);
		$temp = (intval($width)) ? str_replace("[width]", $width, $temp) : $confup['width'];
		$temp = str_replace("[twidth]", $con[6], $temp);
		$temp = (intval($height)) ? str_replace("[height]", $height, $temp) : $confup['height'];
		$temp = str_replace("[align]", $date[2][$i], $temp);
		$temp = str_replace("[title]", $date[3][$i], $temp);
		$temp = str_replace("[quot]", "&quot;", $temp);
		$cont[] = $temp;
		$text = preg_replace($date[0], $cont, $sourse);
	}
	$sourse = str_replace(array("[", "]"), "", $text);
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
	global $admin, $conf;
	if ($text) {
		$editor = intval(substr($admin[3], 0, 1));
		$out = ((defined("ADMIN_FILE") && $editor == 1) || (!defined("ADMIN_FILE") && $conf['redaktor'] == 1)) ? preg_replace('#<br ?>|<br ?\/>#i', '', $text) : $text;
		return $out;
	}
}

# User news
function user_news($unum, $mnum) {
	global $confu;
	$num = (isset($unum) && $unum <= $mnum && $confu['news'] == 1) ? intval($unum) : intval($mnum);
	return $num;
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
			if (file_exists(img_find("language/".$img))) {
				$cont = "<img src=\"".img_find("language/".$img)."\" border=\"0\" align=\"center\" alt=\"".$name."\" title=\"".$name."\">";
			} else {
				$cont = "<img src=\"".img_find("all/question")."\" border=\"0\" align=\"center\" alt=\"".$name."\" title=\"".$name."\">";
			}
		} elseif ($id == 4) {
			$name = $geoip->lookupCountryName($ip);
			$img = str_replace(" ", "_", strtolower($name));
			if (file_exists(img_find("language/".$img))) {
				$cont = "<img src=\"".img_find("language/".$img)."\" border=\"0\" align=\"center\" alt=\"".$name."\" title=\"".$name."\"> <a href=\"".$conf['ip_link'].$ip."\" title=\""._IP.": $ip\" target=\"_blank\">$ip</a>";
			} else {
				$cont = "<img src=\"".img_find("all/question")."\" border=\"0\" align=\"center\" alt=\"".$name."\" title=\"".$name."\"> <a href=\"".$conf['ip_link'].$ip."\" title=\""._IP.": $ip\" target=\"_blank\">$ip</a>";
			}
		}
		return $cont;
	} else {
		return;
	}
}

# User information for user
function user_sinfo($id="") {
	global $prefix, $db, $conf;
	if ($conf['session']) {
		$a = 0; $m = 0; $b = 0; $u = 0; $i = 0;
		$result = $db->sql_query("SELECT uname, UNIX_TIMESTAMP(now())-time AS time, host_addr, guest, module, url, error FROM ".$prefix."_session ORDER BY uname");
		while (list($uname, $time, $host, $guest, $module, $url, $error) = $db->sql_fetchrow($result)) {
                  preg_match("#^%2F(.*)$#", $url, $matches);
                  $url = preg_replace('#\s#', '%20', $matches[1]);
                  $linkstrip = ($error) ? $error : ((preg_match("/^(index([.a-z]*))?$/i", $url)) ? "Mainpage" : ucwords(str_replace("_", " ", cutstr($module, 8))));
			$strip = cutstr($uname, 10);                     
                  if ($guest == 3) { 
                  if ($conf['session'] && is_admin()){
				$admin .= "<tr><td width=\"15%\">".user_geo_ip($host, 3)."</td><td><a href=\"index.php?name=account&amp;op=info&amp;uname=".rawurlencode($uname)."\" title=\"".display_time($time)."\">$strip</a></td><td align=\"right\"><a href=\"$url\">$linkstrip</a></td></tr>";
                        $admins = "<tr><td colspan=\"100%\" align=\"center\"><b>"._ADMINS."</b></td></tr>$admin";
                        }
				$a++;
			}
			elseif ($guest == 2) {
				$user .= "<tr><td width=\"15%\">".user_geo_ip($host, 3)."</td><td><a href=\"index.php?name=account&amp;op=info&amp;uname=".rawurlencode($uname)."\" title=\"".display_time($time)."\">$strip</a></td><td align=\"right\"><a href=\"$url\">$linkstrip</a></td></tr>";
                        $users = "<tr><td colspan=\"100%\" align=\"center\"><b>"._BMEM."</b></td></tr>$user";   
				$m++;
			} elseif ($guest == 1 && $conf['botsact']) {
				$bot .= "<tr><td width=\"15%\">".user_geo_ip($host, 3)."</td><td title=\"".display_time($time)."\">$strip</td><td align=\"right\"><a href=\"$url\">$linkstrip</a></td></tr>";
                        $bots = "<tr><td colspan=\"100%\" align=\"center\"><b>"._BOTS."</b></td></tr>$bot";   
				$b++;
			}			
			else {
                  if ($conf['session'] && is_admin()){
				$visitor .= "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"".$conf['ip_link'].$host."\" title=\"".display_time($time)."\" target=\"_blank\">$uname</a></td><td align=\"right\"><a href=\"$url\">$linkstrip</a></td></tr>";
                        $guests = "<tr><td colspan=\"100%\" align=\"center\"><b>"._BVIS."</b></td></tr>$visitor";
                  }
				$u++;
			}
			$i++;
		}
		$content .= "<hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">"
		."<tr><td><img src=\"".img_find("all/admin")."\" title=\""._ADMINS."\" alt=\""._ADMINS."\"></td><td>"._ADMINS.":</td><td align=\"right\">$a</td></tr>"
		."<tr><td><img src=\"".img_find("all/member")."\" title=\""._BMEM."\" alt=\""._BMEM."\"></td><td>"._BMEM.":</td><td align=\"right\">$m</td></tr>";
		if ($conf['botsact']) $content .= "<tr><td><img src=\"".img_find("all/bots")."\" title=\""._BOTS."\" alt=\""._BOTS."\"></td><td>"._BOTS.":</td><td align=\"right\">$b</td></tr>";
		$content .= "<tr><td><img src=\"".img_find("all/anony")."\" title=\""._BVIS."\" alt=\""._BVIS."\"></td><td>"._BVIS.":</td><td align=\"right\">$u</td></tr></table>";
		if ($admins || $users || $bots || $guests) {
			$content .= "<hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/refresh")."\" style=\"cursor: pointer;\" onclick=\"LoadGet('1', 'sinfo', '5', 'user_sinfo', '', '', '', '', ''); return false;\" border=\"0\" alt=\""._UPDATE."\" title=\""._UPDATE."\"></td><td><span id=\"cont\" onclick=\"SwitchMenu('usbl')\" style=\"cursor: pointer;\"><img src=\"".img_find("all/plus")."\" title=\""._READMORE."\" alt=\""._READMORE."\"></span></td><td><img src=\"".img_find("all/group")."\" title=\""._OVERALL."\" alt=\""._OVERALL."\"></td><td width=\"80%\">"._OVERALL.":</td><td align=\"right\">$i</td></tr></table>"
			."<div id=\"usbl\" style=\"display: none;\"><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">$admins$users$bots$guests</table></div>";
		} else {
			$content .= "<hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/refresh")."\" style=\"cursor: pointer;\" onclick=\"LoadGet('1', 'sinfo', '5', 'user_sinfo', '', '', '', '', ''); return false;\" OnDblClick=\"LoadGet('1', 'sinfo', '5', 'user_sinfo', '', '', '', '', ''); return false;\" alt=\""._UPDATE."\" title=\""._UPDATE."\"></td><td><img src=\"".img_find("all/group")."\" title=\""._OVERALL."\" alt=\""._OVERALL."\"></td><td width=\"80%\">"._OVERALL.":</td><td align=\"right\">".$i."</td></tr></table>";
		}
		if ($id) return $content; else echo $content;
            rewrite();
		}

}

# User information for admin
function user_sainfo($id="") {
	global $prefix, $db, $conf;
	if ($conf['session'] && is_admin()) {
		$a = 0; $b = 0; $m = 0; $u = 0; $i = 0;
		$who_online = array("0" => "", "1" => "", "2" => "", "3" => "");
		$content_who = "";
		$result = $db->sql_query("SELECT uname, UNIX_TIMESTAMP(now())-time AS time, host_addr, guest, module, url FROM ".$prefix."_session ORDER BY uname");
		while (list($s_uname, $s_time, $host, $s_guest, $s_module, $s_url) = $db->sql_fetchrow($result)) {
			$namestrip = cutstr($s_uname, 10);
			$lstrip = cutstr($s_module, 7);
			$alink = urldecode($s_url);
			$alstrip = cutstr($alink, 7);
			$guest = intval($s_guest);
			if ($guest == 3) {
				$title_who = "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"".$conf['ip_link'].$host."\" title=\"".display_time($s_time)." - "._IP.": $host\" target=\"_blank\">$namestrip</a></td><td align=\"right\"><a href=\"$alink\" title=\"$alink\" target=\"_blank\">$alstrip</a></td></tr>";
				$a++;
			} elseif ($guest == 2) {
				if ($lstrip != "") {
					$title_who = "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"index.php?name=account&op=info&uname=$s_uname\" title=\"".display_time($s_time)." - "._IP.": $host\" target=\"_blank\">$namestrip</a></td><td align=\"right\"><a href=\"$alink\" title=\"$alink\" target=\"_blank\">$lstrip</a></td></tr>";
					$m++;
				} else {
					$title_who = "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"index.php?name=account&op=info&uname=$s_uname\" title=\"".display_time($s_time)." - "._IP.": $host\" target=\"_blank\">$namestrip</a></td><td align=\"right\"><a href=\"$alink\" title=\"$alink\" target=\"_blank\">$alstrip</a></td></tr>";
				}
			} elseif ($guest == 1) {
				$title_who = "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"".$conf['ip_link'].$host."\" title=\"".display_time($s_time)." - "._IP.": $host\" target=\"_blank\">$namestrip</a></td><td align=\"right\"><a href=\"$alink\" title=\"$alink\" target=\"_blank\">$lstrip</a></td></tr>";
				$b++;
			} else {
				$title_who = ($u < 250) ? "<tr><td>".user_geo_ip($host, 3)."</td><td><a href=\"".$conf['ip_link'].$host."\" title=\"".display_time($s_time)."\" target=\"_blank\">$s_uname</a></td><td align=\"right\"><a href=\"$alink\" title=\"$alink\" target=\"_blank\">$lstrip</a></td></tr>" : "";
				$u++;
			}
			$who_online[$guest] .= $title_who;
			$i++;
		}
		if (is_admin_god()) {
			$content_who .= "<table id=\"cont\" OnClick=\"SwitchMenu('aadbl')\" style=\"cursor: pointer;\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/plus")."\" title=\""._READMORE."\" alt=\""._READMORE."\"></td><td><img src=\"".img_find("all/admin")."\" title=\""._ADMINS."\" alt=\""._ADMINS."\"></td><td width=\"80%\">"._ADMINS.":</td><td align=\"right\">$a</td></tr></table>"
			."<div id=\"aadbl\" style=\"display: none;\"><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">".$who_online[3]."</table><hr></div>";
		}
		$content_who .= "<table id=\"cont\" OnClick=\"SwitchMenu('mabbl')\" style=\"cursor: pointer;\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/plus")."\" title=\""._READMORE."\" alt=\""._READMORE."\"></td><td><img src=\"".img_find("all/member")."\" title=\""._BMEM."\" alt=\""._BMEM."\"></td><td width=\"80%\">"._BMEM.":</td><td align=\"right\">$m</td></tr></table>"
		."<div id=\"mabbl\" style=\"display: none;\"><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">".$who_online[2]."</table><hr></div>";
		$content_who .= "<table id=\"cont\" OnClick=\"SwitchMenu('babbl')\" style=\"cursor: pointer;\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/plus")."\" title=\""._READMORE."\" alt=\""._READMORE."\"></td><td><img src=\"".img_find("all/bots")."\" title=\""._BOTS."\" alt=\""._BOTS."\"></td><td width=\"80%\">"._BOTS.":</td><td align=\"right\">$b</td></tr></table>"
		."<div id=\"babbl\" style=\"display: none;\"><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">".$who_online[1]."</table><hr></div>";
		$content_who .= "<table id=\"cont\" OnClick=\"SwitchMenu('uabbl')\" style=\"cursor: pointer;\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/plus")."\" title=\""._READMORE."\" alt=\""._READMORE."\"></td><td><img src=\"".img_find("all/anony")."\" title=\""._BVIS."\" alt=\""._BVIS."\"></td><td width=\"80%\">"._BVIS.":</td><td align=\"right\">$u</td></tr></table>"
		."<div id=\"uabbl\" style=\"display: none;\"><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">".$who_online[0]."</table></div>";
		$content_who .= "<hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr><td><img src=\"".img_find("all/refresh")."\" OnClick=\"LoadGet('1', 'sainfo', '5', 'user_sainfo', '', '', '', '', ''); return false;\" OnDblClick=\"LoadGet('1', 'sainfo', '5', 'user_sainfo', '', '', '', '', ''); return false;\" border=\"0\" alt=\""._UPDATE."\" title=\""._UPDATE."\" style=\"cursor: pointer;\"></td><td><img src=\"".img_find("all/group")."\" title=\""._OVERALL."\" alt=\""._OVERALL."\"></td><td width=\"80%\">"._OVERALL.":</td><td align=\"right\">$i</td></tr></table>";
		if ($id) { return $content_who; } else { echo $content_who; }
	}
}

# Format admin block
function adminblock() {
	global $prefix, $db, $conf, $admin_file;
	if (is_admin()) {
		$a_content = "<table><tr><td><img src=\"".img_find("misc/navi")."\" border=\"0\"></td><td><a href=\"".$admin_file.".php\" title=\""._HOME."\">"._HOME."</a></td></tr>"
		."<tr><td><img src=\"".img_find("misc/navi")."\" border=\"0\"></td><td><a href=\"".$admin_file.".php?op=logout\" title=\""._LOGOUT."\">"._LOGOUT."</a></td></tr></table>";
		if (is_admin_god()) {
			list($title, $content) = $db->sql_fetchrow($db->sql_query("SELECT title, content FROM ".$prefix."_blocks WHERE bkey='admin'"));
			$a_content .= $content;
		}
		$a_title = ($title) ? $title : _ADMINS;
		themesidebox($a_title, $a_content, 5);
		themesidebox(_WHO, "<div id=\"repsainfo\">".user_sainfo(1)."</div>", 6);
	}
}

# Newsletter send
function newsletter() {
	global $prefix, $db, $conf;
	if ($conf['newsletter']) {
		list($id, $title, $content, $mails) = $db->sql_fetchrow($db->sql_query("SELECT id, title, content, mails FROM ".$prefix."_newsletter WHERE mails!=''"));
		$id = intval($id);
		$umails = explode(",", $mails);
		for ($i = 0; $i < $conf['newslettercount']; $i++) {
			if ($umails[$i] != "") {
				$outmail .= $umails[$i].",";
				$a++;
			}
		}
		$inmail = str_replace($outmail, "", $mails);
		$db->sql_query("UPDATE ".$prefix."_newsletter SET mails='$inmail', send=send+$a, endtime=now() WHERE id='$id'");
		$mail = explode(",", $outmail);
		foreach ($mail as $val) {
			if ($val != "") mail_send($val, $conf['adminmail'], $title, bb_decode($content, ""), 0, 3);
		}
		if (!$mails) {
			$content = file_get_contents("config/config_global.php");
			$content = str_replace("\$conf['newsletter'] = \"".$conf['newsletter']."\";", "\$conf['newsletter'] = \"0\";", $content);
			$fp = @fopen("config/config_global.php", "wb");
			fwrite($fp, $content);
			fclose($fp);
		}
	}
}

# View article
function view_article($mod, $id, $com="") {
	$com = ($com) ? "#".$com : "";
	$link = ($mod) ? "index.php?name=".$mod."&op=view&id=".$id.$com : "";
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

# Show kasse
function show_kasse($info="") {
	global $db, $prefix, $confso;
	$info = (!$info) ? base64_decode($_COOKIE['shop']) : base64_decode($info);
	$cookies = (preg_match("/[^0-9,]/", $info)) ? "" : $info;
	if ($cookies) {
		$result = $db->sql_query("SELECT product_id, product_title, product_preis FROM ".$prefix."_products WHERE product_id IN ($cookies)");
		while(list($id, $title, $preis) = $db->sql_fetchrow($result)) {
			$massiv = explode(",", $cookies);
			$i = 0;
			foreach ($massiv as $val) {
				if ($val == $id) $i++;
			}
			$preis = $preis * $i;
			$preistotal += $preis;
			$content .= "<tr class=\"bgcolor1\"><td align=\"center\">".$id."</td><td align=\"center\">".$i."</td><td>".$title."</td><td align=\"center\">".$preis." ".$confso['valute']."</td><td align=\"center\"><img src=\"".img_find("all/cart")."\" OnClick=\"LoadGet('', 'kasse', '2', 'add_kasse', '".$id."', '', '', '', ''); return false;\" OnDblClick=\"LoadGet('', 'kasse', '2', 'add_kasse', '".$id."', '', '', '', ''); return false;\" border=\"0\" alt=\""._SCART."\" title=\""._SCART."\" style=\"cursor: pointer;\"> <img src=\"".img_find("all/delete")."\" OnClick=\"LoadGet('', 'kasse', '2', 'del_kasse', '".$id."', '', '', '', ''); AddBasket('".$product_id."'); return false;\" OnDblClick=\"LoadGet('', 'kasse', '2', 'del_kasse', '".$id."', '', '', '', ''); return false;\" border=\"0\" alt=\""._DELETE."\" title=\""._DELETE."\" style=\"cursor: pointer;\"></td></tr>";
		}
		ob_start();
		open();
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" class=\"bgcolor4\"><tr>"
		."<th>"._ID."</th><th>"._QUANTITY."</th><th>"._PRODUCT."</th><th>"._PREIS."</th><th>"._FUNCTIONS."</th></tr>"
		.$content
		."<tr class=\"bgcolor1\"><td colspan=\"5\"><div style=\"float: left; margin-right: 3px;\"><a href=\"index.php?name=shop&op=kasse\" title=\""._SCACH."\"><img src=\"".img_find("all/shop")."\" border=\"0\" alt=\""._SCACH."\" title=\""._SCACH."\"></a></div><div style=\"float: left; margin-right: 9px;\"><a href=\"index.php?name=shop&op=kasse\" title=\""._SCACH."\"><b>"._SCACH."</b></a></div><div style=\"float: left; margin-right: 3px;\"><img src=\"".img_find("all/partners")."\" border=\"0\" alt=\""._PARTNERGES."\" title=\""._PARTNERGES."\"></div><div style=\"float: left; margin-right: 3px;\"><b>"._PARTNERGES.": ".$preistotal." ".$confso['valute']."</b></div></td></tr></table>";
		close();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

# Add kasse
function add_kasse() {
	global $db, $prefix, $confso;
	$id = (isset($_GET['id'])) ? intval($_GET['id']) : "";
	$cookies = (preg_match("/[^0-9,]/", base64_decode($_COOKIE['shop']))) ? "" : base64_decode($_COOKIE['shop']);
	if ($id) {
		setcookie("shop", false);
		if ($cookies) {
			$info = base64_encode($cookies.",".$id);
			setcookie("shop", $info, time() + $confso['shop_t']);
		} else {
			$info = base64_encode($id);
			setcookie("shop", $info, time() + $confso['shop_t']);
		}
	}
	echo show_kasse($info);
}

# Delete kasse
function del_kasse() {
	global $confso;
	$id = (isset($_GET['id'])) ? intval($_GET['id']) : "";
	$cookies = (preg_match("/[^0-9,]/", base64_decode($_COOKIE['shop']))) ? "" : base64_decode($_COOKIE['shop']);
	if ($id && $cookies) {
		$massiv = explode(",", $cookies);
		setcookie("shop", false);
		$i = 0;
		$a = 0;
		$b = 0;
		foreach ($massiv as $val) {
			if ($val == $id && $a == 0) {
				$i++;
				$a++;
				$val = "";
			} else {
				if ($b == 0) {
					$info = $val;
					$b++;
				} else {
					$info .= ",".$val;
				}
			}
		}
		$info = base64_encode($info);
		setcookie("shop", $info, time() + $confso['shop_t']);
	}
	echo show_kasse($info);
}

# Format user warnings graphic
function warn_graphic($total) {
	$width = number_format($total / 2, 2) * 17;
	$title = ($total) ? "title=\""._UWARN.": $total "._WFROM."\"" : "title=\""._UWARN.": 0 "._WFROM."\"";
	$content ="<ul class=\"uwarn\" ".$title."><li class=\"cwarn\" style=\"width: ".$width."px;\"></li></ul>";
	return $content;
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
function ajax_rating($typ, $id, $mod, $rat, $scor, $obj="") {
	include("config/config_ratings.php");
	$con = explode("|", $confra[strtolower($mod)]);
	if (($con[1] && $id && $mod) || ($rat && $scor)) {
if ($con[3]==1 && !defined("ADMIN_FILE")) $content="<div class='rate' id='rate".$id."'>".new_vote_graphic(array('total'=>$scor,'votes'=>$rat,'bodytext'=>$con[2],'isbody'=>$typ,'mod'=>$mod,'id'=>$id,'useronly'=>$con[4]))."</div>";
else $content = (($con[1] && $typ) || ($con[1] && !$con[2] && !$typ)) ? "<script type=\"text/javascript\">ajax('ajax.php?go=1&op=rating&mod=".$mod."&id=$id', 'rate".$id."');</script><div class=\"rate\" id=\"rate".$id."\"></div>" : "<div class=\"rate\">".vote_graphic($rat, $scor)."</div>";
		return $content;
	}
}

# Show editor files
function show_files() {
	global $user;
	include("config/config_uploads.php");
	$id = (isset($_GET['id'])) ? analyze($_GET['id']) : 0;
	$dir = (isset($_GET['mod'])) ? strtolower($_GET['mod']) : "";
	$gzip = (isset($_GET['cid'])) ? intval($_GET['cid']) : 0;
	$typ = (isset($_GET['typ'])) ? intval($_GET['typ']) : 0;
	$con = explode("|", $confup[$dir]);
	$connum = ($con[7]) ? $con[7] : "50";
	$file = (isset($_GET['text'])) ? text_filter($_GET['text']) : "";
	$num = ($gzip) ? $gzip : "1";
	$uname = (is_user()) ? intval($user[0]) : 0;
	if ($typ == 1 && is_moder()) {
		$path = ($id == 1) ? "uploads/".$dir."/" : "uploads/".$dir."/thumb/";
		if ($file && $dir) {
			if (!$gzip) {
				@unlink($path.$file);
			} else {
				zip_compress($path.$file, $path.$file);
			}
		}
		$dh = opendir($path);
		while ($entry = readdir($dh)) {
if ($entry != "." && $entry != ".." && $entry != "index.html" && preg_match("/\./", $entry)) {$ft=filemtime($path.$entry); if (intval($_GET['text'])==0 || intval($_GET['text'])>0 && $ft>=(time()-3600)) $files[] = array($ft, $entry);}
		}
		closedir($dh);
		if ($files) {
			rsort($files);
			foreach ($files as $entry) {
				preg_match("/([a-zA-Z0-9]+)\-([a-zA-Z0-9]+)\-([0-9]+)\.([a-zA-Z0-9]+)/", $entry[1], $date);
				if (($date[3] == 0 && $date[2] && $date[1]) || is_moder()) {
					$filesize = filesize($path.$entry[1]);
					list($imgwidth, $imgheight) = getimagesize($path.$entry[1]);
					$type = substr(strtolower(end(explode(".", $entry[1]))), 0, 5);
					$isimg = ($type == "gif" || $type == "jpg" || $type == "jpeg" || $type == "png" || $type == "bmp") ? 1 : 0;
					$show = ($isimg && $imgwidth && $imgheight) ? "<img src=\"".img_find("all/view")."\" align=\"center\" OnMouseOver=\"Tip('<img src=&quot;".$path.$entry[1]."&quot; width=&quot;".$imgwidth."&quot;>')\" OnClick=\"CaricaFoto('".$path.$entry[1]."')\" style=\"cursor: pointer;\">" : "";
					$show .= (zip_check()) ? " <img src=\"".img_find("all/gzip")."\" border=\"0\" align=\"center\" OnClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '1', '1', '".$dir."', '".$entry[1]."'); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '1', '1', '".$dir."', '".$entry[1]."'); return false;\" alt=\""._ZIP."\" title=\""._ZIP."\" style=\"cursor: pointer;\">" : "";
					$show .= " <img src=\"".img_find("all/delete")."\" border=\"0\" align=\"center\" OnClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '0', '1', '".$dir."', '".$entry[1]."'); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '0', '1', '".$dir."', '".$entry[1]."'); return false;\" alt=\""._DELETE."\" title=\""._DELETE."\" style=\"cursor: pointer;\">";
					$img = ($imgwidth && $imgheight) ? $imgwidth." x ".$imgheight : _NO;
					$contents[] = "<tr class=\"bgcolor1\"><td align=\"center\">".$show."</td><td>".$path.$entry[1]."</td><td align=\"center\">".date ("d.m.Y H:i:s", $entry[0])."</td><td align=\"center\">".files_size($filesize)."</td><td align=\"center\">".$img."</td></tr>";
					$a++;
				}
			}
		}
		$numpages = ceil($a / $connum);
		$offset = ($num - 1) * $connum;
		$tnum = ($offset) ? $connum + $offset : $connum;
		$cont = "";
		for ($i = $offset; $i < $tnum; $i++) {
			if ($contents[$i] != "") $cont .= $contents[$i];
		}
		$contnum = ($a > $connum) ? num_ajax($a, $numpages, $connum, "", $num, "3", "show_files", $id, "1", $dir) : "";
		$content = ($cont) ? "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"bgcolor4\"><tr><th>"._FUNCTIONS."</th><th>"._FILE."</th><th>"._DATE."</th>"
		."<th>"._SIZE."</th><th>"._WIDTH." x "._HEIGHT."</th></tr>".$cont."</table>".$contnum : "";
		open();
		echo $content;
		close();
	} else {
		$path = "uploads/".$dir."/";
		if (is_moder($dir) && $file && $dir) {
			if (!$gzip) {
				@unlink($path.$file);
			} else {
				zip_compress($path.$file, $path.$file);
			}
		}
		$dh = opendir($path);
		while ($entry = readdir($dh)) {
if ($entry != "." && $entry != ".." && $entry != "index.html" && preg_match("/\./", $entry)) {$ft=filemtime($path.$entry); if (intval($_GET['text'])==0 || intval($_GET['text'])>0 && $ft>=(time()-3600)) $files[] = array($ft, $entry);}
		}
		closedir($dh);
		if ($files) {
			rsort($files);
			foreach ($files as $entry) {
				preg_match("/([a-zA-Z0-9]+)\-([a-zA-Z0-9]+)\-([0-9]+)\.([a-zA-Z0-9]+)/", $entry[1], $date);
				if (($date[3] == 0 && $date[2] && $date[1]) || is_moder($dir)) {
					$filesize = filesize($path.$entry[1]);
					list($imgwidth, $imgheight) = getimagesize($path.$entry[1]);
					$type = substr(strtolower(end(explode(".", $entry[1]))), 0, 5);
					$isimg = ($type == "gif" || $type == "jpg" || $type == "jpeg" || $type == "png" || $type == "bmp") ? 1 : 0;
					$show = ($isimg && $imgwidth && $imgheight) ? "<img src=\"".img_find("all/view")."\" align=\"center\" OnMouseOver=\"Tip('<img src=&quot;".$path.$entry[1]."&quot; width=&quot;".$imgwidth."&quot;>')\" OnClick=\"CaricaFoto('".$path.$entry[1]."')\" style=\"cursor: pointer;\"> <img src=\"".img_find("all/export")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"InsertCode('attach', '".$entry[1]."', '', '', '".$id."')\" alt=\""._INSERT." ".$imgwidth." x ".$imgheight."\" title=\""._INSERT." ".$imgwidth." x ".$imgheight."\"> <img src=\"".img_find("all/img")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"InsertCode('img', '".$path.$entry[1]."', '', '', '".$id."')\" alt=\""._EIMG." ".$imgwidth." x ".$imgheight."\" title=\""._EIMG." ".$imgwidth." x ".$imgheight."\">" : "<img src=\"".img_find("all/export")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"InsertCode('attach', '".$entry[1]."', '', '', '".$id."')\" alt=\""._INSERT."\" title=\""._INSERT."\">";
					if (is_moder($dir)) {
						$show .= (zip_check()) ? " <img src=\"".img_find("all/gzip")."\" border=\"0\" align=\"center\" OnClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '1', '0', '".$dir."', '".$entry[1]."'); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '1', '0', '".$dir."', '".$entry[1]."'); return false;\" alt=\""._ZIP."\" title=\""._ZIP."\" style=\"cursor: pointer;\">" : "";
						$show .= " <img src=\"".img_find("all/delete")."\" border=\"0\" align=\"center\" OnClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '0', '0', '".$dir."', '".$entry[1]."'); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '0', '0', '".$dir."', '".$entry[1]."'); return false;\" alt=\""._DELETE."\" title=\""._DELETE."\" style=\"cursor: pointer;\">";
					}
					$contents[] = "<tr class=\"bgcolor1\"><td align=\"center\">".$show."</td><td>".$entry[1]."</td><td align=\"center\">".files_size($filesize)."</td></tr>";
					$a++;
				}
			}
		}
		$numpages = ceil($a / $connum);
		$offset = ($num - 1) * $connum;
		$tnum = ($offset) ? $connum + $offset : $connum;
		$cont .= "";
		for ($i = $offset; $i < $tnum; $i++) {
			if ($contents[$i] != "") $cont .= $contents[$i];
		}
		$contnum = ($a > $connum) ? num_ajax($a, $numpages, $connum, "", $num, "3", "show_files", $id, "0", $dir) : "";
		$content = ($cont) ? "<table width=\"385px\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"bgcolor4\"><tr><th>"._FUNCTIONS."</th><th>"._FILE."</th><th>"._SIZE."</th></tr>".$cont."</table>".$contnum."</td></tr></table>" : "";
		echo $content;
	}
}

# Add downloads
function stream($url, $name) {
	header("Content-Type: application/force-download");
	header("Content-Range: bytes");
	header("Content-Length: ".filesize($url));
	header("Content-Disposition: attachment; filename=".$name);
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
	."<noscript>".$info[1]."<!-- antislaed --><span>&#64;</span><!-- antislaed -->".$info[3]."</noscript>";
	return $content;
}

# Format letter
function letter($mod) {
	$content = "<div class=\"letter\"><a href=\"index.php?name=".$mod."&op=liste\" title=\""._ALL."\">"._ALL."</a> ";
	foreach(range(0, 9) as $num) $content .= " | <a href=\"index.php?name=$mod&op=liste&let=$num\" title=\"$num\">$num</a>";
	if (substr(_LOCALE, 0, 2) == "ru") {
		$content .= "</div><div class=\"letter\"><a href=\"index.php?name=".$mod."&op=liste\" title=\""._ALL."\">"._ALL."</a> ";
		foreach(range("А", "Я") as $rus) $content .= " | <a href=\"index.php?name=$mod&op=liste&let=".urlencode($rus)."\" title=\"$rus\">$rus</a>";
	}
	$content .= "</div><div class=\"letter\"><a href=\"index.php?name=".$mod."&op=liste\" title=\""._ALL."\">"._ALL."</a> ";
	foreach(range("A", "Z") as $eng) $content .= " | <a href=\"index.php?name=$mod&op=liste&let=$eng\" title=\"$eng\">$eng</a>";
	$content .= "</div>";
	echo $content;
}

# Format admin menu
function add_menu($id, $input) {
	$links = explode("||", $input);
	if ($id && $input) {
		$cont = "<div id=\"menu".$id."\" OnMouseOver=\"MenuHover('menu".$id."');\" class=\"menu\"><div><img src=\"".img_find("misc/edit")."\"><ul>";
		foreach ($links as $val) if ($val != "") $cont .= "<li>".$val."</li>";
		$cont .= "</ul></div></div>";
		return $cont;
	}
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
				$link = (!preg_match("#http\:\/\/#i", $out[2])) ? $conf['homeurl']."/".$out[2] : $out[2];
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
		if (strtolower($val[1]) == "utf-8") $content = iconv("utf-8", _CHARSET, $content);
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
					$temp = str_replace("[date]", date(_DATESTRING, strtotime($rss_date[1])), $temp);
					$temp = str_replace("[guid]", $rss_guid[1], $temp);
					$temp = str_replace("[description]", text_filter(html_entity_decode(str_replace("]]>", "", $rss_desc[1]))), $temp);
					$cont .= $temp;
				}
				$cont = ($id) ? $cont : "<h2>"._RSS_FROM.": <a href=\"".$url."\" target=\"_blank\" title=\""._RSS_FROM.": ".$title."\">".$title."</a></h2>".$cont;
			} else {
				$cont = ($id) ? "" : warning(_RSS_PROBLEM, "", "", 1);
			}
		} else {
			$cont = ($id) ? "" : warning(_RSS_PROBLEM, "", "", 1);
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
	$fields = ($arg[0]) ? "<b>".$arg[0]."</b>" : "";
	$fields1 = ($arg[1]) ? (($fields) ? "<br><br>".bb_decode($arg[1], $arg[4]) : bb_decode($arg[1], $arg[4])) : "";
	$fields2 = ($arg[2]) ? "<br><br>".bb_decode($arg[2], $arg[4]) : "";
	$fields3 = ($arg[3]) ? "<br><br>".fields_out(bb_decode($arg[3], $arg[4]), $arg[4]) : "";
	if ($arg[5]) {
		open("", _PREVIEW);
		echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\"><tr><td>".$fields.$fields1.$fields2.$fields3."</td></tr></table>";
		close();
	} else {
		open();
		echo "<fieldset><legend>"._PREVIEW."</legend><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\"><tr><td>".$fields.$fields1.$fields2.$fields3."</td></tr></table></fieldset>";
		close();
	}
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
	if ($fieldb && $mod) {
		$fieldc = $conffi[$mod];
		$fieldb = explode("|", $fieldb);
		$fieldc = explode("||", $fieldc);
		$i = 0;
		$fields = "";
		foreach ($fieldc as $val) {
			if ($val != "" && $fieldb[$i] != "" && $fieldb[$i] != "0") {
				preg_match("#(.*)\|(.*)\|(.*)\|(.*)#i", $val, $out);
				$fields .= $out[1].": ".$fieldb[$i]."<br>";
			}
			$i++;
		}
		return $fields;
	}
}

# Format domain
function domain($url, $str="") {
	$massiv = explode(",", $url);
	$str = intval($str);
	foreach ($massiv as $val) $dom[] = "<a href=\"$val\" target=\"_blank\" title=\""._DOWNLLINK."\">".(($str) ? cutstr(preg_replace("/http\:\/\/|www./", "", $val), $str) : preg_replace("/http\:\/\/|www./", "", $val))."</a>";
	return implode(", ", $dom);
}

# Format user info
function get_info() {
	global $prefix, $db, $admin_file, $conf, $confu;
	$info = func_get_args();
	$id = (is_moder()) ? "<tr><td>"._ID.":</td><td>".$info[0]."</td></tr>" : "";
	$name = $info[1];
	$urank = ($info[2]) ? "<tr><td>"._URANK.":</td><td>".$info[2]."</td></tr>" : "";
	$mail = ((is_moder($conf['name']) || $info[12]) && $info[3]) ? (($info[36]) ? anti_spam($info[3]) : $info[3]) : "<i>"._NO_INFO."</i>";
	$site = ($info[4]) ? domain($info[4]) : "<i>"._NO_INFO."</i>";
	$avatar = ($info[5] && file_exists($confu['adirectory']."/".$info[5])) ? "<img src=\"".$confu['adirectory']."/".$info[5]."\" alt=\"".$info[1]."\" title=\"".$info[1]."\">" : "<img src=\"".$confu['adirectory']."/00.gif\" alt=\"".$info[1]."\" title=\"".$info[1]."\">";
	$regdate = ($info[6]) ? format_time($info[6], _TIMESTRING) : "<i>"._NO_INFO."</i>";
	$icq = ($info[7]) ? $info[7] : "<i>"._NO_INFO."</i>";
	$occup = ($info[8]) ? $info[8] : "<i>"._NO_INFO."</i>";
	$local = ($info[9]) ? $info[9] : "<i>"._NO_INFO."</i>";
	$inter = ($info[10]) ? $info[10] : "<i>"._NO_INFO."</i>";
	$sign = ($info[11] && $info[36]) ? "<tr><td colspan=\"2\"><hr></td></tr><tr><td colspan=\"2\">".bb_decode($info[11], $conf['name'])."</td></tr>" : "";
	$aim = ($info[13]) ? $info[13] : "<i>"._NO_INFO."</i>";
	$yim = ($info[14]) ? $info[14] : "<i>"._NO_INFO."</i>";
	$msn = ($info[15]) ? $info[15] : "<i>"._NO_INFO."</i>";
	$lastvisit = $info[22];
	$points = ($confu['point'] && $info[24]) ? "<tr><td>"._POINTS.":</td><td>".$info[24]."</td></tr>" : "";
	$ip = (is_moder($conf['name'])) ? "<tr><td>"._IP.":</td><td>".$info[25]."</td></tr>" : "";
	if ($info[28]) {
		preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $info[28], $datetime);
		$birthday = $datetime[3].".".$datetime[2].".".$datetime[1];
	} else {
		$birthday = "<i>"._NO_INFO."</i>";
	}
	$gender = gender($info[29], 1);
	$rating = ($info[36]) ? ajax_rating(1, $info[0], $conf['name'], $info[30], $info[31], "") : vote_graphic($info[30], $info[31]);
	$field = ($info[32] && $info[36]) ? "<tr><td colspan=\"2\">".fields_out($info[32], $conf['name'])."</td></tr>" : "";
	$agent = (is_moder($conf['name']) && $info[33]) ? "<tr><td>"._BROWSER.":</td><td>".$info[33]."</td></tr>" : "";
	$sgroup = ($info[34]) ? $info[34] : "<i>"._NO."</i>";
	if ($confu['point'] && $info[24] && $info[36]) {
		$result = $db->sql_query("SELECT name, rank, color FROM ".$prefix."_groups WHERE points<='".intval($info[24])."' AND extra!='1' ORDER BY points ASC");
		$group = array();
		while(list($uname, $gurank, $gcolor) = $db->sql_fetchrow($result)) {
			$group[] = "<span style=\"color: ".$gcolor."\">".$uname."</span>";
			$grank = $gurank;
		}
		$group = ($group) ? implode(", ", $group) : "<i>"._NO_INFO."</i>";
		$group = "<tr><td>"._USER_GROUPS.":</td><td>".$group."</td></tr>";
		$info[35] = ($info[35]) ? $info[35] : $grank;
	}
	$rank = ($info[35] && file_exists("images/ranks/".$info[35])) ? "<tr><td>"._RANK.":</td><td><img src=\"images/ranks/".$info[35]."\" border=\"0\" alt=\""._RANK."\" title=\""._RANK."\"></td></tr>" : "";
	$admin = ($info[36] && is_moder($conf['name'])) ? "<tr><td width=\"100%\" class=\"bgcolor1\" colspan=\"2\" align=\"center\">".ad_bann($admin_file.".php?op=security_block&new_ip=".$info[25], $info[25])." ".ad_edit($admin_file.".php?op=user_add&id=".$info[0])." ".ad_delete($admin_file.".php?op=user_del&id=".$info[0], $info[1])."</td></tr>" : "";
	$infos = "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"bgcolor4\"><tr align=\"center\"><th colspan=\"2\">"._PERSONALINFO."</th></tr><tr class=\"bgcolor1\"><td width=\"20%\" align=\"center\"><table><tr align=\"center\"><td>".$avatar."</td></tr><tr align=\"center\"><td><form action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"name\" value=\"private\"><input type=\"hidden\" name=\"op\" value=\"message\"><input type=\"hidden\" name=\"uname\" value=\"$name\"><input type=\"submit\" value=\"Написать сообщение\" class=\"fbutton\"></form></td></tr></table></td><td width=\"80%\" valign=\"top\" rowspan=\"2\"><table width=\"100%\">"
	.$id.$ip
	."<tr><td width=\"40%\">"._NICKNAME.":</td><td width=\"60%\">".$name."</td></tr>"
	.$urank
	."<tr><td>"._BIRTHDAY.":</td><td>".$birthday."</td></tr>"
	."<tr><td>"._GENDER.":</td><td>".$gender."</td></tr>"
	."<tr><td>"._REG_DATE.":</td><td>".$regdate."</td></tr>"
	."<tr><td>"._LAST_VISIT.":</td><td>".$lastvisit."</td></tr>"
	.$points.$group
	."<tr><td>"._SPEC_GROUP.":</td><td><span style=\"color: ".$info[37]."\">".$sgroup."</span></td></tr>"
	."<tr><td>"._OCCUPATION.":</td><td>".$occup."</td></tr>"
	."<tr><td>"._LOCALITYLANG.":</td><td>".$local."</td></tr>"
	."<tr><td>"._INTERESTS.":</td><td>".$inter."</td></tr>"
	.$rank
		."<tr><td>"._USER_COMS.":</td><td align=\"left\">".$info[36]."</td></tr>"
	."<tr><td>"._REITING.":</td><td align=\"left\">".$rating."</td></tr>"
	."<tr><td>"._UWARN.":</td><td>".warn_graphic($info[26])."</td></tr>"
	.$agent.$field.$sign
	."</table></td></tr><tr class=\"bgcolor1\"><td><table width=\"100%\">"
	."<tr><td>".user_geo_ip($info[25], 3)."</td><td>".user_geo_ip($info[25], 2)."</td></tr>"
	."<tr><td><img src=\"".img_find("all/contact")."\" border=\"0\" align=\"center\" alt=\""._EMAIL."\" title=\""._EMAIL."\"></td><td>".$mail."</td></tr>"
	."<tr><td><img src=\"".img_find("all/home")."\" border=\"0\" align=\"center\" alt=\""._SITEURL."\" title=\""._SITEURL."\"></td><td>".$site."</td></tr>"
	."<tr><td><img src=\"".img_find("all/icq")."\" border=\"0\" align=\"center\" alt=\""._ICQ."\" title=\""._ICQ."\"></td><td>".$icq."</td></tr>"
	."<tr><td><img src=\"".img_find("all/aim")."\" border=\"0\" align=\"center\" alt=\""._AIM."\" title=\""._AIM."\"></td><td>".$aim."</td></tr>"
	."<tr><td><img src=\"".img_find("all/yim")."\" border=\"0\" align=\"center\" alt=\""._YIM."\" title=\""._YIM."\"></td><td>".$yim."</td></tr>"
	."<tr><td><img src=\"".img_find("all/msn")."\" border=\"0\" align=\"center\" alt=\""._MSN."\" title=\""._MSN."\"></td><td>".$msn."</td></tr>"
	."</table></td></tr>".$admin."</table>";
	$infos = ($info[36]) ? $infos : str_replace('"', "&quot;", $infos);
	return $infos;
}

# Format user name
function get_user() {
	global $prefix, $db;
	$let = analyze_name($_GET['letters']);
	if ($let){
		$result = $db->sql_query("SELECT user_id, user_name FROM ".$prefix."_users WHERE user_name LIKE '".$let."%'");
		while(list($user_id, $user_name) = $db->sql_fetchrow($result)){
			echo $user_id."###".$user_name."|";
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
	if (isset($admin)) {
		if (!isset($admintrue)) {
			$id = intval(substr($admin[0], 0, 11));
			$name = htmlspecialchars(substr($admin[1], 0, 25));
			$pwd = htmlspecialchars(substr($admin[2], 0, 40));
			$ip = getip();
			if ($id && $name && $pwd && $ip) {
				list($aname, $apwd, $aip) = $db->sql_fetchrow($db->sql_query("SELECT name, pwd, ip FROM ".$prefix."_admins WHERE id='$id'"));
				if ($aname == $name && $aname != "" && $apwd == $pwd && $apwd != "" && $aip == $ip && $aip != "") {
					$admintrue = 1;
					return $admintrue;
				}
			}
			$admintrue = 0;
			return $admintrue;
		} else {
			return $admintrue;
		}
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

# Redirect referer
function referer($url) {
	$referer = getenv("HTTP_REFERER");
	if (isset($_REQUEST['refer']) && $referer != "" && !preg_match("/^unknown/i", $referer) && !preg_match("/^bookmark/i", $referer)) {
		header("Location: ".$referer);
	} else {
		header("Location: ".$url);
	}
}

# Get referer
function get_referer() {
	$referer = text_filter(getenv("HTTP_REFERER"));
	if (!empty($referer) && $referer != "" && !preg_match("/^unknown/i", $referer) && !preg_match("/^bookmark/i", $referer) && !strpos($referer, $_SERVER["HTTP_HOST"])) {
		$refer = $referer;
	} else {
		$refer = "";
	}
	return $refer;
}

# Analyze name
function analyze_name($name) {
	$name = ($name) ? ((preg_match("#\"|\'|\.|\:|\;|\/|\*#", $name)) ? "" : $name) : "";
	return $name;
}

# URL filter
function url_filter($url) {
	$url = strtolower($url);
	$url = (preg_match("#http\:\/\/#i", $url)) ? $url : "http://".$url;
	$url = ($url == "http://") ? "" : text_filter($url);
	return $url;
}

# Check ip
function check_ip() {
	$f = "config/counter/ips.txt";
	$ip = explode(".", getip());
	$ip = chr($ip[0]).chr($ip[1]).chr($ip[2]).chr($ip[3]);
	if (file_exists($f)) {
		$fp = @fopen($f, "rb");
		flock($fp, 2);
		while($str = fread($fp, 4)) {
			if ($ip == $str) {
				return false;
				break;
			}
		}
		flock($fp, 3);
		fclose($fp);
	}
	$fp = @fopen($f, "ab");
	flock($fp, 2);
	fwrite($fp, $ip);
	flock($fp, 3);
	fclose($fp);
	return true;
}

# Format head
function head() {
	global $prefix, $db, $home, $index, $conf, $confs, $user, $admin, $name, $error, $bodytext, $hometext, $pagetitle, $key_words, $theme;
if (!defined("ADMIN_FILE")) open_offline();
	if ($conf['session']) {
		$ip = getip();
		$url = urlencode(getenv("REQUEST_URI"));
		if ($confs['flood'] && (isset($_GET) || isset($_POST))) {
			$ftime = time() - intval($confs['flood_t']);
			list($flood) = $db->sql_fetchrow($db->sql_query("SELECT Count(uname) FROM ".$prefix."_session WHERE host_addr = '$ip' AND time > '$ftime'"));
			if (isset($_POST) && $flood) {
				$a = 0;
				$info = array();
				foreach ($_POST as $var_name=>$var_value) {
					$info[] = $var_name."=".$var_value;
					$a++;
				}
				if ($a) warn_report("Flood in POST - ".implode(", ", $info));
			}
			if ($confs['flood'] == 2 && isset($_GET) && $flood) {
				$a = 0;
				$info = array();
				foreach ($_GET as $var_name=>$var_value) {
					$info[] = $var_name."=".$var_value;
					$a++;
				}
				if ($a) warn_report("Flood in GET - ".implode(", ", $info));
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
				$uvisit = save_datetime(1, "");
				$uagent = getagent();
				$unam = text_filter(substr($user[1], 0, 25), 1);
				$db->sql_query("UPDATE ".$prefix."_users SET user_last_ip='$ip', user_lastvisit='$uvisit', user_agent='$uagent' WHERE user_name='$unam'");
			}
			@unlink($sess_f);
			$fp = @fopen($sess_f, "wb");
			fwrite($fp, time());
			fclose($fp);
		}
		$ctime = time();
		if ($uname) {
			$db->sql_query("UPDATE ".$prefix."_session SET uname='$uname', time='$ctime', host_addr='$ip', guest='$guest', module='$name', error='$error', url='$url' WHERE uname='$uname'");
			$e = @mysql_info();
			preg_match("/^\D+(\d+)/", $e, $matches);
			if ($matches[1] == 0) $db->sql_query("INSERT INTO ".$prefix."_session (uname, time, host_addr, guest, module, url, error) VALUES ('$uname', '$ctime', '$ip', '$guest', '$name', '$url', '$error')");
		}
	}
	if ($conf['refer']) {
		$referer = get_referer();
		if ($referer) {
			$refer_f = "config/counter/refer.txt";
			$refer_t = (file_exists($refer_f) && filesize($refer_f) != 0) ? file_get_contents($refer_f) : 0;
			$past = time() - intval($conf['refer_t']);
			if ($refer_t < $past) {
				$db->sql_query("DELETE FROM ".$prefix."_referer");
				@unlink($refer_f);
				$fp = @fopen($refer_f, "wb");
				fwrite($fp, time());
				fclose($fp);
			}
			$ip = getip();
			$uid = intval($user[0]);
			$link = text_filter(getenv("REQUEST_URI"));
			list($exist) = $db->sql_fetchrow($db->sql_query("SELECT ip FROM ".$prefix."_referer WHERE ip='$ip' AND lid!='0'"));
			if ($exist) {
				$db->sql_query("INSERT INTO ".$prefix."_referer VALUES (NULL, '".$uid."', '".$uname."', '".$ip."', '".$referer."', '".$link."', now(), '0')");
			
			} else {
				if(file_get_contents("config/cache/referer.dat")){unlink("config/cache/referer.dat");}
				$result = $db->sql_query("SELECT link FROM ".$prefix."_auto_links");
				while(list($slink) = $db->sql_fetchrow($result)) {
					if (preg_match("#".$slink."#i", $referer)) {
						$islink = 1;
						break;
					} else {
						$islink = 0;
					}
				}
				if ($islink) {
					$db->sql_query("UPDATE ".$prefix."_auto_links SET hits=hits+1 WHERE link='".$slink."'");
					list($lid) = $db->sql_fetchrow($db->sql_query("SELECT id FROM ".$prefix."_auto_links WHERE link='".$slink."'"));
					$db->sql_query("INSERT INTO ".$prefix."_referer VALUES (NULL, '".$uid."', '".$uname."', '".$ip."', '".$referer."', '".$link."', now(), '".$lid."')");
				} else {
					$db->sql_query("INSERT INTO ".$prefix."_referer VALUES (NULL, '".$uid."', '".$uname."', '".$ip."', '".$referer."', '".$link."', now(), '0')");
				}
			}
		}
	}
	include("config/config_stat.php");
	if ($confst['stat']) {
		$sreferer = get_referer();
		$sreqhom = text_filter(getenv("REQUEST_URI"));
		$spath = "config/counter/";
		$sdate = file($spath."stat.txt");
		if ($sdate) {
			$con = explode("|", trim($sdate[0]));
			if (date("d.m.Y") != $con[0]) {
				$fpd = @fopen($spath."days.txt", "ab");
				flock($fpd, 2);
				fwrite($fpd, $sdate[0]."\r\n");
				flock($fpd, 3);
				fclose($fpd);
				@unlink($spath."stat.txt");
				@unlink($spath."ips.txt");
				if (substr($con[0], 3) != date("m.Y")) {
					$month = date("Y-m", strtotime("-1 month"));
					@rename($spath."days.txt", $spath."stat/stat_".$month.".txt");
					@unlink($spath."days.txt");
				}
				$ahits = ($con[3]) ? ($con[3]+1) : "1";
				$sengine = ($conf['session'] && $guest == 1) ? "1" : "0";
				$srefer = ($sreferer) ? "1" : "0";
				$reqhom = ($sreqhom == "/" || $sreqhom == "/index.html" || $sreqhom == "/index.php") ? "1" : "0";
				$suser = ($conf['session'] && $guest == 2) ? "1" : "0";
				$wc = date("d.m.Y")."|1|1|".$ahits."|".$sengine."|".$srefer."|".$reqhom."|".$suser;
			} else {
				$check = check_ip();
				$shost = ($check) ? intval($con[1]+1) : $con[1];
				$sengine = ($check && $conf['session'] && $guest == 1) ? intval($con[4]+1) : $con[4];
				$srefer = ($check && $sreferer) ? intval($con[5]+1) : $con[5];
				$reqhom = ($sreqhom == "/" || $sreqhom == "/index.html" || $sreqhom == "/index.php") ? intval($con[6]+1) : $con[6];
				$suser = ($check && $conf['session'] && $guest == 2) ? intval($con[7]+1) : $con[7];
				$wc = $con[0]."|".$shost."|".intval($con[2]+1)."|".intval($con[3]+1)."|".$sengine."|".$srefer."|".$reqhom."|".$suser;
			}
			$fps = @fopen($spath."stat.txt", "wb");
			flock($fps, 2);
			fwrite($fps, $wc);
			flock($fps, 3);
			fclose($fps);
		} elseif (!file_exists($spath."stat.txt")) {
			$sengine = ($conf['session'] && $guest == 1) ? "1" : "0";
			$srefer = ($sreferer) ? "1" : "0";
			$reqhom = ($sreqhom == "/" || $sreqhom == "/index.html" || $sreqhom == "/index.php") ? "1" : "0";
			$suser = ($conf['session'] && $guest == 2) ? "1" : "0";
			$wc = date("d.m.Y")."|1|1|1|".$sengine."|".$srefer."|".$reqhom."|".$suser;
			$fps = @fopen($spath."stat.txt", "wb");
			flock($fps, 2);
			fwrite($fps, $wc);
			flock($fps, 3);
			fclose($fps);
		}
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
	get_theme_inc();
	$defis_dec = urldecode($conf['defis']);
	$index = file_get_contents(get_theme_file("index"));
	$index = str_replace("{%LICENSE%}", $conf['copy'], $index);
      $index = str_replace("{%LICENSE2%}", $conf['copy2'], $index);
	preg_match("#^(.*){%MODULE%}#iUs", $index, $head);
	$head = (isset($head[1])) ? $head[1] : die("Error in Head!");
	preg_match("#{%MODULE%}(.*)$#iUs", prt($index), $index);
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
			if ($val) $pagetitle_dec .= trim($ptitle_dec[$i])." ".$defis_dec." ";
			$i--;
		}
		$strhead .= "<title>".$pagetitle_dec.$conf['sitename']."</title>\n";
	}
	if (!defined("ADMIN_FILE")) {
		include("function/no-cache.php");
		$strhead .= "<meta name=\"resource-type\" content=\"document\">\n"
		."<meta name=\"document-state\" content=\"dynamic\">\n"
		."<meta name=\"distribution\" content=\"global\">\n"
		."<meta name=\"author\" content=\"".$conf['sitename']."\">\n";
		$pagetitle = trim(str_replace(" ".$conf['defis']." ", ", ", $pagetitle));
		$pagetitle = trim(str_replace($conf['defis']." ", "", $pagetitle));
		if ((($hometext == "") && ($bodytext == "")) || ($conf['keywords_s'] == "0")) {
			$strhead .= "<meta name=\"keywords\" content=\"$pagetitle, ".$conf['keywords']."\">\n"
			."<meta name=\"description\" content=\"".$conf['slogan'].", $pagetitle.\">\n";
		} else {
			$key_gen = "$pagetitle $hometext $bodytext";
			$key_gen = substr($key_gen, 0, 1500);
			$key_gen = text_filter(bb_decode($key_gen, ""), 1);
			$key_gen = trim(preg_replace("/[^a-zA-Zа-яА-Я0-9]/", " ", $key_gen));
			$key_gen = preg_replace("/( |".CHR(10)."|".CHR(13).")+/", ",", $key_gen);
			$key_gen = array_unique(explode(",", $key_gen));
			foreach ($key_gen as $val) if (strlen($val) > 3) $key_words[] = $val;
			$key_words = implode(", ", $key_words);
			$strhead .= "<meta name=\"keywords\" content=\"$key_words\">\n"
			."<meta name=\"description\" content=\"".$conf['slogan'].", $pagetitle.\">\n";
		}
		$strhead .= "<meta name=\"robots\" content=\"index, follow\">\n"
		."<meta name=\"revisit-after\" content=\"1 days\">\n"
		."<meta name=\"rating\" content=\"general\">\n"
		."<meta name=\"generator\" content=\"ANTISLAED CMS ".$conf['version']." by ARTGLOBALS.COM\">\n"
		."<base href=\"".$conf['homeurl']."/\" >\n" 
		."<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"favicon.ico\">\n";
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
	$strhead .= (file_exists("templates/".$theme."/style.css")) ? "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/".$theme."/style.css\">\n" : "";
      if (is_user() && $name != 'private') {
      $sql = 'SELECT a.status FROM '.$prefix.'_private  AS a LEFT JOIN '.$prefix.'_session AS b ON (a.t_user=b.uname) WHERE a.t_user = "'.$user[1].'" AND (status ="0" OR status ="2")';
      $result = $db->sql_query($sql);
      $row = $db->sql_fetchrow($result);
      $status = $row['status'];
      if ($status == '0' OR $status == '2'){
      $style .= "<style type=\"text/css\">
      #popwingui {
	background-image: url(templates/".$theme."/images/cellpic.gif);
	border:1px outset #c0c0c0;
      }
      #popwindiv {
      }
      #popwiniframe {
	border:1px inset #c0c0c0;
      }
</style>\n";
      $script .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"ajax/popWindow.js\"></script>\n";
      }}
	$strhead .= $style.$script."<script type=\"text/javascript\" src=\"ajax/global_func.js\"></script>\n<script type=\"text/javascript\" src=\"ajax/load.js\"></script>\n";
	$strhead .= (!$confs['error_java']) ? "<script type=\"text/javascript\" src=\"ajax/block_error.js\"></script>\n" : "";
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
	global $home, $module, $name, $index, $conf, $confs, $do_gzip_compress;
	$index = addblocks($index);
	if ($module == 1 && file_exists("modules/$name/copyright.php")) {
		$index = "<div align=\"right\"><a href=\"javascript:OpenWindow('modules/$name/copyright.php', 'Copyright', '400', '200')\">".str_replace("_", " ", $name)." &copy;</a></div>".$index;
	}
	if ($confs['log_d']) {
		$sess_f = "config/counter/dump.txt";
		$sess_d = (file_exists($sess_f) && filesize($sess_f) != 0) ? file_get_contents($sess_f) : 0;
		$past = time() - intval($confs['sess_d']);
		if ($sess_d < $past) $index .= "<body OnLoad=\"LoadGet('1', 'filereport', '8', 'filereport', '', '', '', '', ''); return false;\"><div id=\"repfilereport\"></div></body>";
	}
	if ($conf['newsletter']) $index .= "<body OnLoad=\"LoadGet('1', 'newsletter', '8', 'newsletter', '', '', '', '', ''); return false;\"><div id=\"repnewsletter\"></div></body>";
	if ($confs['log_b']) {
		$sess_f = "config/counter/backup.txt";
		$sess_b = (file_exists($sess_f) && filesize($sess_f) != 0) ? file_get_contents($sess_f) : 0;
		$past = time() - intval($confs['sess_b']);
		if ($sess_b < $past) $index .= "<body OnLoad=\"LoadGet('1', 'backup', '8', 'backup', '', '', '', '', ''); return false;\"><div id=\"repbackup\"></div></body>";
	}
	themefooter($index);
	unset($index);
	if (!defined("ADMIN_FILE")) rewrite();
	if ((!defined("ADMIN_FILE") && $conf['cache'] == 1) || (!defined("ADMIN_FILE") && $conf['cache'] == 2 && $home)) {
		$url = str_replace("/", "", $_SERVER['REQUEST_URI']);
		$url = (!$url) ? "index.php" : $url;
		$match = preg_match("/index/", $url);
		$cont = ob_get_contents();
		if ($cont && $match && !is_user() && !is_admin()) {
			$fp = @fopen("config/cache/".md5($url).".txt", "wb");
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

# Log files report
function create_dump($dir, &$log) {
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if ($file == "." || $file == "..") continue;
				$location = $dir.$file;
				if (filetype($location) == "dir") {
					create_dump($location."/", $log);
				} else {
					$log[$location] = md5_file($location);
				}
			}
			closedir($dh);
		}
	}
}

function write_dump($dump, $file) {
	if ($fp = @fopen($file, "wb")) {
		$new = "";
		foreach ($dump as $location => $md5) $new .= $location."||".$md5."\n";
		flock($fp, 2);
		fwrite($fp, $new);
		flock($fp, 3);
		fclose($fp);
	}
	return ($fp) ? true : false;
}

function write_log($log, $file) {
	global $confs;
	if ($fp = @fopen($file, "ab")) {
		if (filesize($file) > $confs['log_size']) {
			zip_compress($file, "config/logs/dump_log_".date("Y-m-d_H-i").".txt");
			@unlink($file);
		}
		$log = ($log) ? implode("\n", $log) : _NO;
		flock($fp, 2);
		fwrite($fp, $log."\n"._DATE.": ".date("d.m.Y - H:i:s")."\n---\n");
		flock($fp, 3);
		fclose($fp);
	}
	return ($fp) ? true : false;
}

function diff_dump($dump, $old) {
	$log = array();
	foreach ($old as $string) {
		list($location, $md5) = explode("||", trim($string));
		$new[$location] = $md5;
	}
	foreach ($new as $location => $md5) {
		if (!isset($dump[$location])) $log[] = _D_DEL.": ".$location;
	}
	$filedump = dirname($_SERVER['PHP_SELF'])."/config/logs/dump.txt";
	$filelog = dirname($_SERVER['PHP_SELF'])."/config/logs/dump_log.txt";
	foreach ($dump as $location => $md5) {
		if (strpos($filedump, substr($location, 2)) !== false || strpos($filelog, substr($location, 2))) continue;
		if (!isset($new[$location])) {
			$log[] = _D_NEW.": ".$location;
		} else if ($new[$location] != $dump[$location]) {
			$log[] = _D_EDIT.": ".$location;
		}
	}
	return (count($log) > 0) ? $log : false;
}

function filereport() {
	global $conf, $confs;
	if ($confs['log_d']) {
		$sess_f = "config/counter/dump.txt";
		$sess_d = (file_exists($sess_f) && filesize($sess_f) != 0) ? file_get_contents($sess_f) : 0;
		$past = time() - intval($confs['sess_d']);
		if ($sess_d < $past) {
			@unlink($sess_f);
			$fp = @fopen($sess_f, "wb");
			fwrite($fp, time());
			fclose($fp);

			$safe = ini_get("safe_mode") == "1" ? 1 : 0;
			if (!$safe && function_exists("set_time_limit")) set_time_limit(600);

			$dump = array();
			create_dump("./", $dump);
			if (file_exists("config/logs/dump.txt") && filesize("config/logs/dump.txt") != 0) {
				if ($log = diff_dump($dump, file("config/logs/dump.txt"))) sort($log);
			} else {
				$log = false;
			}
			write_log($log, "config/logs/dump_log.txt");
			write_dump($dump, "config/logs/dump.txt");
			if ($confs['mail_d']) {
				$log = ($log) ? implode("<br>", $log) : _NO;
				$subject = $conf['sitename']." - "._SECURITY;
				$mmsg = $conf['sitename']." - "._SECURITY."<br><br>".$log."<br>"._DATE.": ".date("d.m.Y - H:i:s");
				mail_send($conf['adminmail'], $conf['adminmail'], $subject, $mmsg, 0, 1);
			}
		}
	}
}

# User and admin login report
function login_report($id, $typ, $login, $pass) {
	global $admin, $user, $confs;
	$id = ($id) ? "admin" : "user";
	if (($confs['log_a'] && $id) || ($confs['log_u'] && !$id)) {
		$typ = ($typ) ? _YES : _NO;
		$ip = getip();
		$login = ($login) ? "\n"._NICKNAME.": ".substr($login, 0, 25) : "";
		$lpass = ($pass) ? "\n"._PASSWORD.": ".substr($pass, 0, 25) : "";
		$agent = getagent();
		$url = text_filter(getenv("REQUEST_URI"));
		$ladmin = ($admin) ? "\n"._ADMIN.": ".substr($admin[1], 0, 25) : "";
		$luser = ($user) ? "\n"._USER.": ".substr($user[1], 0, 25) : "";
		$path = "config/logs/log_".$id.".txt";
		if ($fhandle = @fopen($path, "ab")) {
			if (filesize($path) > $confs['log_size']) {
				zip_compress($path, "config/logs/log_".$id."_".date("Y-m-d_H-i").".txt");
				@unlink($path);
			}
			fwrite($fhandle, _INPUT.": ".$typ."\n"._IP.": ".$ip.$login.$lpass.$ladmin.$luser."\n"._URL.": ".$url."\n"._BROWSER.": ".$agent."\n"._DATE.": ".date("d.m.Y - H:i:s")."\n---\n");
			fclose($fhandle);
		}
	}
}

# Backup DB
function backup() {
	global $dbhost, $dbuname, $dbpass, $dbname, $dbtype, $confs;
	if ($confs['log_b'] && $dbtype == "mysql") {
		$sess_f = "config/counter/backup.txt";
		$sess_b = (file_exists($sess_f) && filesize($sess_f) != 0) ? file_get_contents($sess_f) : 0;
		$past = time() - intval($confs['sess_b']);
		if ($sess_b < $past) {
			@unlink($sess_f);
			$fp = @fopen($sess_f, "wb");
			fwrite($fp, time());
			fclose($fp);

			$safe = ini_get("safe_mode") == "1" ? 1 : 0;
			if (!$safe && function_exists("set_time_limit")) set_time_limit(600);
			mysql_connect($dbhost, $dbuname, $dbpass);

			# Кодировка соединения с MySQL
			# auto - автоматический выбор (устанавливается кодировка таблицы), latin1, cp1251, utf8 и т.п.
			$ccharset = "auto";

			# Типы таблиц у которых сохраняется только структура, разделенные запятой
			$conlycreate = "MRG_MyISAM,MERGE,HEAP,MEMORY";

			# В фильтре таблиц указываются специальные шаблоны по которым отбираются таблицы. В шаблонах можно использовать следующие специальные символы:
			# символ * — означает любое количество символов;
			# символ ? — означает один любой символ;
			# символ ^ — означает исключение из списка таблицы или таблиц.

			# Примеры:
			# slaed_* все таблицы начинающиеся с "slaed_" (все таблицы форума invision board)
			# slaed_*, ^slaed_session все таблицы начинающиеся с "slaed_", кроме "slaed_session"
			# slaed_s*s, ^slaed_session все таблицы начинающиеся с "slaed_s" и заканчивающиеся буквой "s", кроме "slaed_session"
			# ^*s все таблицы, кроме таблиц заканчивающихся буквой "s"
			# ^slaed_???? все таблицы, кроме таблиц, которые начинаются с "slaed_" и содержат 4 символа после знака подчеркивания
			$ctables = "^ipb_*";

			$bsize = 0;
			preg_match("/^(\d+)\.(\d+)\.(\d+)/", mysql_get_server_info(), $m);
			$bmysql_ver = sprintf("%d%02d%02d", $m[1], $m[2], $m[3]);
			$bonly_create = explode(",", $conlycreate);

			$btables_exclude = !empty($ctables) && $ctables{0} == '^' ? 1 : 0;
			$btables = isset($ctables) ? $ctables : "";
			$btables = explode(",", $btables);
			if (!empty($ctables)) {
				foreach($btables as $table) {
					$table = preg_replace("/[^\w*?^]/", "", $table);
					$pattern = array("/\?/", "/\*/");
					$replace = array(".", ".*?");
					$tbls[] = preg_replace($pattern, $replace, $table);
				}
			} else {
				$btables_exclude = 1;
			}
			$db = $dbname;
			mysql_select_db($db) or trigger_error("Не удается выбрать базу данных.<br>" . mysql_error(), E_USER_ERROR);
			$tables = array();
			$result = mysql_query("SHOW TABLES");
			$all = 0;
			while($row = mysql_fetch_array($result)) {
				$status = 0;
				if (!empty($tbls)) {
					foreach ($tbls as $table) {
						$exclude = preg_match("/^\^/", $table) ? true : false;
						if (!$exclude) {
							if (preg_match("/^{$table}$/i", $row[0])) $status = 1;
							$all = 1;
						}
						if ($exclude && preg_match("/{$table}$/i", $row[0])) $status = -1;
					}
				} else {
					$status = 1;
				}
				if ($status >= $all) $tables[] = $row[0];
			}
			$tabs = count($tables);
			$result = mysql_query("SHOW TABLE STATUS");
			$tabinfo = array();
			$tab_charset = array();
			$tab_type = array();
			$tabinfo[0] = 0;
			while($item = mysql_fetch_assoc($result)){
				if (in_array($item['Name'], $tables)) {
					$item['Rows'] = empty($item['Rows']) ? 0 : $item['Rows'];
					$tabinfo[0] += $item['Rows'];
					$tabinfo[$item['Name']] = $item['Rows'];
					$bsize += $item['Data_length'];
					$tabsize[$item['Name']] = 1 + round(1048576 / ($item['Avg_row_length'] + 1));
					if (!empty($item['Collation']) && preg_match("/^([a-z0-9]+)_/i", $item['Collation'], $m)) {
						$tab_charset[$item['Name']] = $m[1];
					}
					$tab_type[$item['Name']] = isset($item['Engine']) ? $item['Engine'] : $item['Type'];
				}
			}
			$name = $db."_".date("Y-m-d_H-i");
			$fp = @fopen("config/backup/".$name.".sql", "wb");
			fwrite($fp, "# DB: ".$db."\n# Tables: ".$tabs."\n# Size: ".round($bsize / 1048576, 2)." MB\n# Lines: ".number_format($tabinfo[0], 0, ",", " ")."\n# Date: ".date("Y.m.d H:i:s")."\n\n");
			$result = mysql_query("SET SQL_QUOTE_SHOW_CREATE = 1");
			if ($bmysql_ver > 40101 && $ccharset != 'auto') {
				mysql_query("SET NAMES '".$ccharset."'") or trigger_error("Неудается изменить кодировку соединения.<br>".mysql_error(), E_USER_ERROR);
				$last_charset = $ccharset;
			} else{
				$last_charset = "";
			}
			foreach ($tables as $table) {
				if ($bmysql_ver > 40101 && $tab_charset[$table] != $last_charset) {
					if ($ccharset == "auto") {
						mysql_query("SET NAMES '" . $tab_charset[$table] . "'") or trigger_error("Неудается изменить кодировку соединения.<br>".mysql_error(), E_USER_ERROR);
						$last_charset = $tab_charset[$table];
					}
				}
				$result = mysql_query("SHOW CREATE TABLE `{$table}`");
				$tab = mysql_fetch_array($result);
				$tab = preg_replace('/(default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP|DEFAULT CHARSET=\w+|COLLATE=\w+|character set \w+|collate \w+)/i', '/*!40101 \\1 */', $tab);
				fwrite($fp, "DROP TABLE IF EXISTS `{$table}`;\n{$tab[1]};\n\n");
				if (in_array($tab_type[$table], $bonly_create)) continue;
				$NumericColumn = array();
				$result = mysql_query("SHOW COLUMNS FROM `{$table}`");
				$field = 0;
				while($col = mysql_fetch_row($result)) $NumericColumn[$field++] = preg_match("/^(\w*int|year)/", $col[1]) ? 1 : 0;
				$fields = $field;
				$from = 0;
				$limit = $tabsize[$table];
				if ($tabinfo[$table] > 0) {
					$i = 0;
					fwrite($fp, "INSERT INTO `{$table}` VALUES");
					while(($result = mysql_query("SELECT * FROM `{$table}` LIMIT {$from}, {$limit}")) && ($total = mysql_num_rows($result))) {
						while($row = mysql_fetch_row($result)) {
							$i++;
							for($k = 0; $k < $fields; $k++) {
								if ($NumericColumn[$k]) {
									$row[$k] = isset($row[$k]) ? $row[$k] : "NULL";
								} else {
									$row[$k] = isset($row[$k]) ? "'".mysql_escape_string($row[$k])."'" : "NULL";
								}
							}
							fwrite($fp, ($i == 1 ? "" : ",")."\n(".implode(", ", $row).")");
						}
						mysql_free_result($result);
						if ($total < $limit) break;
						$from += $limit;
					}
					fwrite($fp, ";\n\n");
				}
			}
			fclose($fp);
			mysql_close();
			zip_compress("config/backup/".$name.".sql", "config/backup/".$name.".sql");
			@unlink("config/backup/".$name.".sql");
		}
	}
}

# Format Time filter
function format_time($time, $string="") {
	$string = ($string) ? $string : _DATESTRING;
	setlocale(LC_TIME, _LOCALE);
	preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $time, $datetime);
	$datetime = date($string, mktime($datetime[4], $datetime[5], $datetime[6], $datetime[2], $datetime[3], $datetime[1]));
	return $datetime;
}

# Display Time filter
function display_time($sec) {
	$minutes = floor($sec / 60);
	$seconds = $sec % 60;
	$content = ($minutes == 0) ? $seconds." "._SEC."." : $minutes." "._MIN.". ".$seconds." "._SEC.".";
	return $content;
}

# Size filter
function files_size($size) {
	$name = array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
	$mysize = $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2)." ".$name[$i] : $size." Bytes";
	return $mysize;
}

# Format new graphic
function new_graphic($data) {
      $mktime = date(_DATESTRING, mktime());
      $dtime = date(_DATESTRING, $data);
      $data1 = mktime(0, 0, 0, date("m", $data), date("d", $data)+1, date("Y", $data));
      $data2 = mktime() - $data;
	$img = "";
	if ($mktime == $dtime) $img = "<img src=\"".img_find("misc/new_day")."\" alt=\""._NEWTODAY."\" title=\""._NEWTODAY."\">";
	if ((mktime()  > $data1) && ($data2 < 259200)) $img = "<img src=\"".img_find("misc/new_3day")."\" alt=\""._NEWLAST3DAYS."\" title=\""._NEWLAST3DAYS."\">";
	if (($data2 > 259200) && ($data2 < 604800)) $img = "<img src=\"".img_find("misc/new_week")."\" alt=\""._NEWTHISWEEK."\" title=\""._NEWTHISWEEK."\">";
	return $img;
}

# Format categories
function categories($mod, $tab, $sub, $desc, $id="") {
	global $prefix, $db, $user, $conf, $currentlang;
	if (analyze($mod)) {
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
		$result = $db->sql_query("SELECT id, title, description, img, parentid, auth_view, auth_read FROM ".$prefix."_categories ".$where." ORDER BY ordern, title");
		while (list($cid, $title, $description, $img, $parentid, $auth_view, $auth_read) = $db->sql_fetchrow($result)) {
			$massiv[] = array($cid, $title, $description, $img, $parentid, $auth_view, $auth_read);
			unset($cid, $title, $description, $img, $parentid, $auth_view, $auth_read);
			$cat_num++;
		}
		if ($massiv) {
			foreach ($massiv as $val) {
				if ($val[4] == $id && is_acess($val[5])) {
					$catid[] = $val[0];
					if (is_acess($val[6])) {
						$hidden = "";
						$htitle = $val[1];
						$ilink = ($val[3]) ? "<a href=\"index.php?name=$mod&cat=$val[0]\" title=\"".$htitle."\"><img src=\"images/categories/".$val[3]."\" border=\"0\" alt=\"".$htitle."\" title=\"".$htitle."\"></a>" : "<a href=\"index.php?name=$mod&cat=$val[0]\" title=\"".$htitle."\"><img src=\"".img_find("all/".strtolower($mod))."\" border=\"0\" alt=\"".$htitle."\" title=\"".$htitle."\"></a>";
						$alink = "<a href=\"index.php?name=$mod&cat=$val[0]\" title=\"".$htitle."\"><b>$val[1]</b></a>";
					} else {
						$hidden = "class=\"hidden\"";
						$htitle = $val[1]." - "._CCLOSED;
						$ilink = ($val[3]) ? "<img src=\"images/categories/".$val[3]."\" border=\"0\" alt=\"".$htitle."\" title=\"".$htitle."\">" : "<img src=\"".img_find("all/".strtolower($mod))."\" border=\"0\" alt=\"".$htitle."\" title=\"".$htitle."\">";
						$alink = "<b>".$val[1]."</b>";
					}
					if ($val[3]) {
						$description = ($desc) ? "<br><i>".$val[2]."</i>" : "";
						$ccontent .= "<td valign=\"top\" width=\"".$tdwidth."%\"><table width=\"100%\" border=\"0\" $hidden><tr><td>".$ilink."</td><td width=\"100%\">".$alink.$description."</td></tr>";
					} else {
						$description = ($desc) ? "<tr><td colspan=\"2\"><i>".$val[2]."</i></td></tr>" : "";
						$ccontent .= "<td valign=\"top\" width=\"".$tdwidth."%\"><table width=\"100%\" border=\"0\" $hidden><tr><td>".$ilink."</td><td width=\"100%\">".$alink."</td></tr>".$description;
					}
					foreach ($massiv as $val2) {
						if ($val[0] == $val2[4] && is_acess($val2[5])) {
							$catid[] = $val2[0];
							if ($sub == 1) {
								$alink = (is_acess($val2[6])) ? " <a href=\"index.php?name=$mod&cat=$val2[0]\" title=\"".$val2[1]."\">".$val2[1]."</a>" : "";
								$ccontent .= "<tr><td colspan=\"2\"><img border=\"0\" src=\"".img_find("misc/navi")."\" title=\"".$val2[1]."\">".$alink."</td></tr>";
							}
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
			$catid = implode(", ", $catid);
			if ($mod == "faq") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(fid) FROM ".$prefix."_faq WHERE catid IN ($catid) AND time <= now() AND status!='0'"));
				$in = _INFA;
			} elseif ($mod == "files") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(lid) FROM ".$prefix."_files WHERE cid IN ($catid) AND date <= now() AND status!='0'"));
				$in = _INF;
			} elseif ($mod == "help") {
				$uid = intval($user[0]);
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(sid) FROM ".$prefix."_help WHERE catid IN ($catid) AND time <= now() AND pid='0' AND uid='$uid'"));
				$in = _INH;
			} elseif ($mod == "jokes") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(jokeid) FROM ".$prefix."_jokes WHERE cat IN ($catid) AND date <= now() AND status!='0'"));
				$in = _INJ;
			} elseif ($mod == "links") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(lid) FROM ".$prefix."_links WHERE cid IN ($catid) AND date <= now() AND status!='0'"));
				$in = _INL;
			} elseif ($mod == "media") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_media WHERE cid IN ($catid) AND date <= now() AND status!='0'"));
				$in = _INM;
			} elseif ($mod == "news") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(sid) FROM ".$prefix."_stories WHERE catid IN ($catid) AND time <= now() AND status!='0'"));
				$in = _INN;
				} elseif ($mod == "gumor") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(sid) FROM ".$prefix."_gumor WHERE catid IN ($catid) AND time <= now() AND status!='0'"));
				$in = _INN;
					} elseif ($mod == "games") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(sid) FROM ".$prefix."_games WHERE catid IN ($catid) AND time <= now() AND status!='0'"));
				$in = _INN;
					} elseif ($mod == "kinonews") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(sid) FROM ".$prefix."_kinonews WHERE catid IN ($catid) AND time <= now() AND status!='0'"));
				$in = _INN;
			} elseif ($mod == "pages") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(pid) FROM ".$prefix."_page WHERE catid IN ($catid) AND time <= now() AND status!='0'"));
				$in = _INP;
			} elseif ($mod == "shop") {
				list($pages_num) = $db->sql_fetchrow($db->sql_query("SELECT Count(product_id) FROM ".$prefix."_products WHERE product_cid IN ($catid) AND product_time <= now() AND product_active !='0'"));
				$in = _INS;
			}
			open();
			echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\"><tr>".$ccontent."</td></tr></table>"
			."<hr><center>"._ALLIN.": <b>".$pages_num."</b> ".$in." <b>".$cat_num."</b> "._ALLINC."</center>";
			close();
		}
	}
}

# Check user acess
function is_acess($ids) {
	global $prefix, $db, $user, $conf;
	if ($ids) {
		$id = explode("|", $ids);
		if (is_moder($conf['name'])) {
			$isa = true;
		} elseif (is_user() && $id[1]) {
			$uid = intval($user[0]);
			$mid = explode(",", $id[1]);
			foreach ($mid as $val) if ($val) $dmid[] = "g.id=".$val;
			$dmid = implode(" OR ", $dmid);
			list($uid) = $db->sql_fetchrow($db->sql_query("SELECT Count(u.user_id) FROM ".$prefix."_users AS u LEFT JOIN ".$prefix."_groups AS g ON ((g.extra=1 AND u.user_group=g.id) OR (g.extra!=1 AND u.user_points>=g.points)) WHERE u.user_id='$uid' AND (".$dmid.")"));
			$isa = ($uid) ? true : false;
		} elseif (is_user() && !$id[1]) {
			$isa = (1 >= $id[0]) ? true : false;
		} else {
			$isa = (0 >= $id[0] && !$id[1]) ? true : false;
		}
	} else {
		$isa = false;
	}
	return $isa;
}

# Format categories select
function getcat() {
	global $prefix, $db;
	$arg = func_get_args();
	$mod = analyze($arg[0]);
	$id = intval($arg[1]);
	$where = ($mod) ? "WHERE modul='$mod'" : "";
	$result = $db->sql_query("SELECT id, title, parentid, auth_view FROM ".$prefix."_categories ".$where);
	if ($db->sql_numrows($result) > 0) {
		$content = "<select name=\"".$arg[2]."\" class=\"".$arg[3]."\" title=\""._CATEGORIES."\">";
		$content .= ($arg[4]) ? $arg[4] : "";
		while (list($cid, $title, $parentid, $auth_view) = $db->sql_fetchrow($result)) if (is_acess($auth_view)) $massiv[$cid] = array($title, $parentid);
		foreach ($massiv as $key => $val) {
			$cont[$key] = $val[0];
			$flag = $val[1];
			while ($flag != 0) {
				$cont[$key] = $massiv[$flag][0]." / ".$cont[$key];
				$flag = intval($massiv[$flag][1]);
			}
		}
		asort($cont);
		foreach ($cont as $key => $val) {
			$sel = ($id == $key) ? "selected" : "";
			$content .= "<option value=\"$key\" $sel>$val</option>";
		}
		return $content."</select>";
	} elseif ($arg[4]) {
		return "<select name=\"".$arg[2]."\" class=\"".$arg[3]."\" title=\""._CATEGORIES."\">".$arg[4]."</select>";
	}
}

# Format categories links
function catlink() {
	global $prefix, $db, $conf;
	$arg = func_get_args();
	$mod = analyze($arg[0]);
	$id = intval($arg[1]);
	$defis = ($arg[2]) ? " ".urldecode($arg[2])." " : " ".urldecode($conf['defis'])." ";
	$content = ($arg[3]) ? "<a href=\"index.php?name=".$conf['name']."\" title=\"".$arg[3]."\">".$arg[3]."</a>".$defis : "";
	$where = ($mod) ? "WHERE modul='$mod'" : "";
	$result = $db->sql_query("SELECT id, title, parentid FROM ".$prefix."_categories ".$where);
	if ($db->sql_numrows($result) > 0) {
		while (list($cid, $title, $parentid) = $db->sql_fetchrow($result)) $massiv[$cid] = array($title, $parentid);
		foreach ($massiv as $key => $val) {
			$cont[$key] = $val[0];
			$cptitle= $val[0];
			$flag = $val[1];
			while ($flag != 0) {
				$cont[$key] = "<a href=\"index.php?name=".$conf['name']."&cat=".$flag."\" title=\"".$massiv[$flag][0]."\">".$massiv[$flag][0]."</a>".$defis."<a href=\"index.php?name=".$conf['name']."&cat=".$key."\" title=\"".$cptitle."\">".$cont[$key]."</a>";
				$flag = intval($massiv[$flag][1]);
			}
		}
		foreach ($cont as $key => $val) {
			if ($id == $key) {
				$content .= $val;
				break;
			}
		}
		return $content;
	}
}

# Format categories IDs
function catids() {
	global $prefix, $db, $conf;
	$arg = func_get_args();
	$mod = analyze($arg[0]);
	$id = intval($arg[1]);
	$where = ($mod) ? "WHERE modul='$mod'" : "";
	$result = $db->sql_query("SELECT id, parentid FROM ".$prefix."_categories ".$where);
	if ($db->sql_numrows($result) > 0) {
		while (list($cid, $parentid) = $db->sql_fetchrow($result)) $massiv[$cid] = array($parentid);
		foreach ($massiv as $key => $val) {
			$cont[$key] = $key;
			$flag = $val[0];
			while ($flag != 0) {
				$cont[$key] = $flag.", ".$cont[$key];
				$flag = intval($massiv[$flag][0]);
			}
		}
		foreach ($cont as $key => $val) {
			if ($id == $key) {
				$content = $val;
				break;
			}
		}
		return $content;
	}
}

# Format categories IDs from module
function catmids() {
	global $prefix, $db, $conf, $currentlang;
	$arg = func_get_args();
	$where = ($conf['multilingual']) ? "WHERE modul='".$arg[0]."' AND (language='$currentlang' OR language='')" : "WHERE modul='".$arg[0]."'";
	$result = $db->sql_query("SELECT id, auth_read FROM ".$prefix."_categories ".$where." ORDER BY id");
	while (list($cid, $auth_read) = $db->sql_fetchrow($result)) if (is_acess($auth_read)) $catid[] = $cid;
	$where = ($catid) ? "AND ".$arg[1]." IN (".implode(", ", $catid).")" : "";
	return $where;
}

# Length end filter
function cutstr($linkstrip, $strip) {
	if (strlen($linkstrip) > $strip) $linkstrip = substr($linkstrip, 0, $strip)."…";
	return $linkstrip;
}

# Analyzer of variables
function variables() {
	$content = "";
	if ($_GET) {
		$cont = array();
		foreach ($_GET as $var_name => $var_value) $cont[] = "<b>".$var_name."</b>=".$var_value;
		$content = "<br><br><span style=\"color: blue; font-weight: bold;\">GET</span> - ".implode(", ", $cont);
	}
	if ($_POST) {
		$cont = array();
		foreach ($_POST as $var_name => $var_value) {
			$var_value = is_array($var_value) ? fields_save($var_value) : $var_value;
			$var_value = str_replace(array("[", "]"), array("&#091;", "&#093;"), htmlspecialchars($var_value));
			$cont[] = "<b>".$var_name."</b>=".$var_value;
		}
		$content .= "<br><br><span style=\"color: blue; font-weight: bold;\">POST</span> - ".implode(", ", $cont);
	}
	if ($_COOKIE) {
		$cont = array();
		foreach ($_COOKIE as $var_name => $var_value) $cont[] = "<b>".$var_name."</b>=".$var_value;
		$content .= "<br><br><span style=\"color: blue; font-weight: bold;\">COOKIE</span> - ".implode(", ", $cont);
	}
	if ($_FILES) {
		$cont = array();
		foreach ($_FILES as $var_name => $var_value) $cont[] = "<b>".$var_name."</b>=".$var_value;
		$content .= "<br><br><span style=\"color: blue; font-weight: bold;\">FILES</span> - ".implode(", ", $cont);
	}
	if ($_SESSION) {
		$cont = array();
		foreach ($_SESSION as $var_name => $var_value) $cont[] = "<b>".$var_name."</b>=".$var_value;
		$content .= "<br><br><span style=\"color: blue; font-weight: bold;\">SESSION</span> - ".implode(", ", $cont);
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
      global $conf;
      if($conf['out'] || $conf['rewrite']){
	  $contents = ob_get_contents();
	  ob_end_clean();
      }
      if($conf['out']){
        preg_match_all("#<a(\s+)href=('|\")http://([^\"']+)('|\")([^>]*)>#i", $contents, $matches);
        for ($i=0;$i<count($matches[0]);$i++){
        $host = explode('/', $matches[3][$i]);
          if ($conf['homeurl'] && $conf['homeurl'] != 'http://'.$host[0]){ 
            if(preg_match("#target=(\"|')index(\"|')#i", $matches[5][$i])==false){
              $mt_rand = mt_rand(1000000, 1000000000);
              $contents = str_ireplace($matches[0][$i], '<a'.$matches[1][$i].'href='.$matches[2][$i].'/index.php?go='.$mt_rand.$matches[4][$i].' onmouseover="this.href=\'http://'.$matches[3][$i].'\'" onmouseout="this.href=\'/index.php?go='.$mt_rand.'\'"'.$matches[5][$i].'>', $contents);
            }
          }
        }
      }
      if ($conf['rewrite']){
	include("config/config_rewrite.php");
	$contents = preg_replace($in, $out, $contents);
     	}
	echo $contents;
}

# Decode BB
function bb_decode($sourse, $mod,$title="") {
	if (!preg_match("#\[php\](.*)\[/php\]|\[code\](.*)\[/code\]#si", $sourse)) {
		$sourse=bb_pro($sourse);
		$bb = array();
		$html = array();
$bb[] = "#\[img\]([^?](?:[^\[]+|\[(?!url))*?)\[/img\]#i";
$html[] = "<img src=\"\\1\" border=\"0\" alt=\"\\1\" title=\"\\1\">";
$bb[] = "#\[img=([a-zA-Z]+)\]([^?](?:[^\[]+|\[(?!url))*?)\[/img\]#is";
$html[] = "<img src=\"\\2\" align=\"\\1\" border=\"0\" alt=\"\\2\" title=\"\\2\">";
$bb[] = "#\[img\ alt=([a-zA-Zа-яА-Я0-9\_\-\. ]+)\]([^?](?:[^\[]+|\[(?!url))*?)\[/img\]#is";
$html[] = "<img src=\"\\2\" align=\"\\1\" border=\"0\" alt=\"\\1\" title=\"\\1\">";
$bb[] = "#\[img=([a-zA-Z]+) alt=([a-zA-Zа-яА-Я0-9\_\-\. ]+)\]([^?](?:[^\[]+|\[(?!url))*?)\[/img\]#is";
$html[] = "<img src=\"\\3\" align=\"\\1\" border=\"0\" alt=\"$title\" title=\"$title\" style=\"padding: 5px; 10px;\">";

		$bb[] = "#\[url\](ed2k://\|file\|(.*?)\|\d+\|\w+\|(h=\w+\|)?/?)\[/url\]#is";
		$html[] = "eMule/eDonkey: <a href=\"\\1\" target=\"_blank\" title=\"\\2\">\\2</a>";
		$bb[] = "#\[url=(ed2k://\|file\|(.*?)\|\d+\|\w+\|(h=\w+\|)?/?)\](.*?)\[/url\]#si";
		$html[] = "<a href=\"\\1\" target=\"_blank\" title=\"\\2\">\\4</a>";
		$bb[] = "#\[url\](ed2k://\|server\|([\d\.]+?)\|(\d+?)\|/?)\[/url\]#si";
		$html[] = "ed2k Server: <a href=\"\\1\" target=\"_blank\" title=\"\\2\">\\2</a> - Port: \\3";
		$bb[] = "#\[url=(ed2k://\|server\|[\d\.]+\|\d+\|/?)\](.*?)\[/url\]#si";
		$html[] = "<a href=\"\\1\" target=\"_blank\" title=\"\\2\">\\2</a>";
		$bb[] = "#\[url\](ed2k://\|friend\|(.*?)\|[\d\.]+\|\d+\|/?)\[/url\]#si";
		$html[] = "Friend: <a href=\"\\1\" target=\"_blank\" title=\"\\2\">\\2</a>";
		$bb[] = "#\[url=(ed2k://\|friend\|(.*?)\|[\d\.]+\|\d+\|/?)\](.*?)\[/url\]#si";
		$html[] = "<a href=\"\\1\" target=\"_blank\" title=\"\\3\">\\3</a>";
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
$bb[]='#\[rtext\](.*?)\[/rtext\]#si';

    $html[]='<div style="float:right; margin:1px; padding:2px; border: double #648B43;
        -moz-border-radius: 10px; 
    -webkit-border-radius: 10px; 
    border-radius: 10px;
        width:auto;">\1</div>';
        
$bb[]='#\[ltext\](.*?)\[/ltext\]#si';

$html[]='<div style="float:left; margin:5px; padding:2px; border: double #648B43;
        -moz-border-radius: 10px; 
    -webkit-border-radius: 10px; 
    border-radius: 10px;
        width:auto;">\1</div>';
        
        $bb[]='#\[centerp\](.*?)\[/centerp\]#si';

$html[]='<div style="text-align:center; margin:5px; padding:2px; 
       
        width:auto;">\1</div>';
        
        
$bb[] = "#\[flv\](\S+?)\[/flv\]#is";


$html[] ="<object id=\"videoplayer453\" width=\"434\" height=\"375\"><param name=\"bgcolor\" value=\"#ffffff\" /><param name=\"allowFullScreen\" value=\"true\" /><param name=\"allowScriptAccess\" value=\"always\" /><param name=\"movie\" value=\"uppod.swf\" /><param name=\"flashvars\" value=\"comment=news.maximuma.net&amp;st=video46-1086.txt&amp;file=\\1\" /><embed src=\"uppod.swf\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" flashvars=\"comment=news.maximuma.net&amp;st=/video46-1086.txt&amp;file=\\1\" bgcolor=\"#ffffff\" width=\"545\" height=\"375\"></embed></object>";

$bb[] = "#\[flv2\](\S+?)\[/flv2\]#is";

$html[] ="<object id=\"videoplayer453\" width=\"275\" height=\"180\"><param name=\"bgcolor\" value=\"#ffffff\" /><param name=\"allowFullScreen\" value=\"true\" /><param name=\"allowScriptAccess\" value=\"always\" /><param name=\"movie\" value=\"uppod.swf\" /><param name=\"flashvars\" value=\"comment=news.maximuma.net&amp;st=video46-1426.txt&amp;file=\\1\" /><embed src=\"uppod.swf\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" flashvars=\"comment=news.maximuma.net&amp;st=/video46-1426.txt&amp;file=\\1\" bgcolor=\"#ffffff\" width=\"275\" height=\"180\"></embed></object>";
$bb[] = "#\[mp3\](.*?)\[/mp3\]#si";
$html[] = "<object type=\"application/x-shockwave-flash\" data=\"ajax/audioplayer/player.swf\" id=\"\\1\" height=\"24\" width=\"290\">
<param name=\"movie\" value=\"ajax/audioplayer/player.swf\">
<param name=\"FlashVars\" value=\"playerID=\\1&soundFile=\\1\">
<param name=\"quality\" value=\"high\">
<param name=\"menu\" value=\"false\">
<param name=\"wmode\" value=\"transparent\">
</object>";

$bb[] = "#\[video\](.*?)\[/video\]#si";
    $html[] = "<p align=\"center\"><br><fieldset><legend>Просмотр видео</legend><embed type=\"application/x-mplayer2\" pluginspage=\"http://www.microsoft.com/Windows/Downloads/Contents/MediaPlayer/\" width=\"500\" height=\"435\" src=\"\\1\" filename=\"\\1\" autostart=\"True\" showcontrols=\"True\" Volume=\"100\"  id='mediaPlayer' displaysize='5' autosize='1'  showstatusbar=\"True\" showdisplay=\"False\" autorewind=\"False\"></embed><br><font class=small>Для просмотра видео, оно должно изначально загрузится.</font></fieldset></p>";
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
		$sourse = preg_replace($bb, $html, $sourse);
		if (preg_match("#(.*)\[tab\](.*)\[/tab\](.*)#si",$sourse,$matches)){ 
$sourse = bb_decode($matches[1], $mod).build_tabs($matches[2]).bb_decode($matches[3], $mod); 
}
		if (preg_match("#\[quote\](.*?)\[/quote\]#si", $sourse)) $sourse = encode_quote($sourse);
if (preg_match("#(.*)\[spoiler(.*)\](.*)\[/spoiler\](.*)#is",$sourse,$matches)){ 
$sourse = bb_decode($matches[1], $mod).build_spoiler($matches[2],$matches[3]).bb_decode($matches[4], $mod); 
}		if (preg_match("#\[hide\](.*?)\[/hide\]#si", $sourse)) $sourse = encode_hide($sourse);
		if (preg_match("#\[quote|name=(.*?)\](.*?)\[/quote\]#si", $sourse)) $sourse = parse_quotes($sourse);
if (preg_match("#\[attach=(.*?)\]#si", $sourse)) $sourse = encode_attach($sourse, strtolower($mod), $title);
	} else {
		if (preg_match("#(.*)\[php\](.*)\[/php\](.*)#si", $sourse, $matches)) {
			$sourse = bb_decode($matches[1], $mod).encode_php($matches[2]).bb_decode($matches[3], $mod);
		} elseif (preg_match("#(.*)\[code\](.*)\[/code\](.*)#si", $sourse, $matches)) {
			$sourse = bb_decode($matches[1], $mod).encode_code($matches[2]).bb_decode($matches[3], $mod);
		}
	}
	return $sourse;
}

function build_spoiler($title,$sourse) {
$title = ($title) ? str_replace("=","",$title) : "» Нажмите, чтобы показать спойлер - нажмите опять, чтобы скрыть... «";
$i = md5( microtime());
$sourse=str_replace($sourse,"<div class=\"title_spoiler\">&nbsp;<a 

href=\"javascript:ShowOrHide('".$i."')\"><h17>$title</h17></a></div><div id=\"".$i."\" class=\"text_spoiler\" 

style=\"display:none;\">$sourse</div>",$sourse);
return $sourse; }

# Format hide
function encode_hide($text) {
	$start_html = "<fieldset style=\"width: 95%; overflow: auto;\"><legend style=\"color: red;\">"._HIDE."</legend><div style=\"margin: 3px;\">";
	$end_html = "</div></fieldset>";
	$text = (defined("ADMIN_FILE") || is_user()) ? preg_replace("#\[hide\](.*?)\[/hide\]#si", $start_html."\\1".$end_html, $text) : preg_replace("#\[hide\](.*?)\[/hide\]#si", $start_html._HIDETEXT.$end_html, $text);
	return $text;
}

# Format quote
function encode_quote($text) {
	$start_html = "<fieldset style=\"width: 95%; overflow: auto;\"><legend style=\"color: green;\">"._QUOTE."</legend><div style=\"margin: 3px;\">";
	$end_html = "</div></fieldset>";
	while (preg_match("#\[quote\](.*?)\[/quote\]#si", $text)) $text = preg_replace("#\[quote\](.*?)\[/quote\]#si", $start_html."\\1".$end_html, $text);
	return $text;
}
// Foramt quotes
function parse_quotes($s)
{
    $start_html = "<div class=post><fieldset style=\"width: auto; overflow: auto;\"><legend style=\"color: green;\">"._QUOTE.": ";
    $centr_html = "</legend><div style=\"color: blue;font-size: 11; overflow: auto;\">";
    $end_html = "</div></fieldset></div>";
    while (preg_match("#\[quote([^\]]+?)?\](.+?)\[/quote\]#si", $s))
    $s = preg_replace("#\[quote([^\]]+?)?\](.+?)\[/quote\]#si", "$start_html\\1$centr_html\\2$end_html", $s);
    $s = str_replace("|name=","",$s);
return $s;
}

function bb_pro($sourse){
include("config/config_bb.php");
foreach ($confbb as $val) {
if ($val != "") {
preg_match("#(.*)\|(.*)\|(.*)#i",$val, $out);
$out[3]=str_replace("{code}","\\1",$out[3]);
$sourse = preg_replace("#\[".$out[1]."\](.*?)\[/".$out[1]."\]#si",$out[3], $sourse);
}}
return $sourse;
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
	if ((!$mail) || ($mail=="") || (!preg_match("/^[_\.a-z0-9-]+@([a-z0-9_-]+\.)+[a-z]{2,6}$/", $mail))) $stop = _ERROR1."<br>"._ERROR2." (<b>email@domain.com</b>)";
	if ((strlen($mail) >= 4) && (substr($mail, 0, 4) == "www.")) $stop = _ERROR1."<br>"._ERROR3." (<b>www.</b>)";
	if (strrpos($mail, " ") > 0) $stop = _ERROR1."<br>"._ERROR4.".";
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
				$total_time = _GENERATION.": ".$total_time." "._SEC.". "._AND." ".$sqlnums." "._GENERATION_DB." ".$total_time_db." "._SEC.".";
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
			if (file_exists("blocks/".$blockfile)) {
				include("blocks/".$blockfile);
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

function tabs(){
$tabs .="
<script language=\"JavaScript\" type=\"text/javascript\" src=\"ajax/insert_code.js\"></script>
<div class=\"left\">Tabs:</div><div class=\"center\">";
$tabs .="<input class=\"fbutton\" type=\"button\" onclick='AddSmile(\"[tab]\\n\\n{tabs=title}text{/tabs}\\n{tabs=title}text{/tabs}\\n{tabs=title}text{/tabs}\\n{tabs=title}text{/tabs}\\n\\n[/tab]\")' value=\"Tabs\"></div>";
return $tabs;
}


function build_tabs($sourse) {
$content = "<div class=\"section\">";
$match_count = preg_match_all("#\{tabs=(.*?)}(.*?){/tabs}#si",$sourse, $date);
for ($i = 0; $i < $match_count; $i++) {
$one = ($i == 0) ? " class=\"current\"" : "";
$two = ($i == 0) ? "class=\"box visible\"" : "class=\"box\"";
$title .= "<li$one title=\"".$date[1][$i]."\">".$date[1][$i]."</li>";
$text .= "<div $two>".$date[2][$i]."</div>";
}

$content .="<ul class=\"tabs\">$title</ul>";
$content .="$text";
$content .="</div>";
$sourse = str_replace($sourse,$content, $sourse);
return $sourse;}


# Format rating
function rating() {
	global $db, $prefix, $user;
	include("config/config_ratings.php");
	$id = (isset($_GET['id'])) ? intval($_GET['id']) : "";
	$typ = (isset($_GET['typ'])) ? analyze($_GET['typ']) : "";
	$mod = (isset($_GET['mod'])) ? analyze($_GET['mod']) : "";
	$rating = (isset($_GET['text'])) ? intval($_GET['text']) : 0;
	$con = explode("|", $confra[strtolower($mod)]);
	if ($id && $mod) {
		if ($mod == "account") {
			$query = "user_votes, user_totalvotes FROM ".$prefix."_users WHERE user_id='$id'";
		} elseif ($mod == "faq") {
			$query = "ratings, score FROM ".$prefix."_faq WHERE fid='$id'";
		} elseif ($mod == "files") {
			$query = "votes, totalvotes FROM ".$prefix."_files WHERE lid='$id'";
		} elseif ($mod == "forum") {
			$query = "ratings, score FROM ".$prefix."_forum WHERE id='$id'";
		} elseif ($mod == "jokes") {
			$query = "ratingtot, rating FROM ".$prefix."_jokes WHERE jokeid='$id'";
		} elseif ($mod == "links") {
			$query = "votes, totalvotes FROM ".$prefix."_links WHERE lid='$id'";
		} elseif ($mod == "media") {
			$query = "votes, totalvotes FROM ".$prefix."_media WHERE id='$id'";
		} elseif ($mod == "news") {
			$query = "ratings, score FROM ".$prefix."_stories WHERE sid='$id'";
				} elseif ($mod == "gumor") {
			$query = "ratings, score FROM ".$prefix."_gumor WHERE sid='$id'";
				} elseif ($mod == "games") {
			$query = "ratings, score FROM ".$prefix."_games WHERE sid='$id'";
				} elseif ($mod == "kinonews") {
			$query = "ratings, score FROM ".$prefix."_kinonews WHERE sid='$id'";
		} elseif ($mod == "pages") {
			$query = "ratings, score FROM ".$prefix."_page WHERE pid='$id'";
		} elseif ($mod == "shop") {
			$query = "product_votes, product_totalvotes FROM ".$prefix."_products WHERE product_id='$id'";
		}
		$ip = getip();
		$past = time() - intval($con[0]);
		$cookies = (isset($_COOKIE[substr($mod, 0, 2).'-'.$id])) ? intval($_COOKIE[substr($mod, 0, 2).'-'.$id]) : "";
		$uid = (is_user()) ? intval(substr($user[0], 0, 11)) : 0;
		$db->sql_query("DELETE FROM ".$prefix."_rating WHERE time<'$past' AND modul='$mod'");
		list($num) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_rating WHERE (mid='$id' AND modul='$mod' AND host='$ip') OR (mid='$id' AND modul='$mod' AND uid='$uid' AND uid!='0')"));
		if ($cookies == $id || $num > 0) {
			list($votes, $totalvotes) = $db->sql_fetchrow($db->sql_query("SELECT ".$query));
			echo vote_graphic($votes, $totalvotes);
		} elseif (!$cookies && !$num && !$rating) {
			list($votes, $totalvotes) = $db->sql_fetchrow($db->sql_query("SELECT ".$query));
			$votes = (intval($votes)) ? $votes : 1;
			$width = number_format($totalvotes/$votes, 2) * 17;
			echo "<ul class=\"urating\">"
			."<li class=\"crating\" style=\"width:".$width."px;\"></li>"
			."<li><div class=\"out1\" OnMouseOver=\"this.className='over1';\" OnMouseOut=\"this.className='out1';\" OnClick=\"LoadGet('1', '".$id.$typ."', '1', 'rating', '".$id."', '', '".$typ."', '".$mod."', '1'); return false;\" OnDblClick=\"LoadGet('1', '".$id.$typ."', '1', 'rating', '".$id."', '', '".$typ."', '".$mod."', '1'); return false;\" title=\""._RATE1."\"></div></li>"
			."<li><div class=\"out2\" OnMouseOver=\"this.className='over2';\" OnMouseOut=\"this.className='out2';\" OnClick=\"LoadGet('1', '".$id.$typ."', '1', 'rating', '".$id."', '', '".$typ."', '".$mod."', '2'); return false;\" OnDblClick=\"LoadGet('1', '".$id.$typ."', '1', 'rating', '".$id."', '', '".$typ."', '".$mod."', '2'); return false;\" title=\""._RATE2."\"></div></li>"
			."<li><div class=\"out3\" OnMouseOver=\"this.className='over3';\" OnMouseOut=\"this.className='out3';\" OnClick=\"LoadGet('1', '".$id.$typ."', '1', 'rating', '".$id."', '', '".$typ."', '".$mod."', '3'); return false;\" OnDblClick=\"LoadGet('1', '".$id.$typ."', '1', 'rating', '".$id."', '', '".$typ."', '".$mod."', '3'); return false;\" title=\""._RATE3."\"></div></li>"
			."<li><div class=\"out4\" OnMouseOver=\"this.className='over4';\" OnMouseOut=\"this.className='out4';\" OnClick=\"LoadGet('1', '".$id.$typ."', '1', 'rating', '".$id."', '', '".$typ."', '".$mod."', '4'); return false;\" OnDblClick=\"LoadGet('1', '".$id.$typ."', '1', 'rating', '".$id."', '', '".$typ."', '".$mod."', '4'); return false;\" title=\""._RATE4."\"></div></li>"
			."<li><div class=\"out5\" OnMouseOver=\"this.className='over5';\" OnMouseOut=\"this.className='out5';\" OnClick=\"LoadGet('1', '".$id.$typ."', '1', 'rating', '".$id."', '', '".$typ."', '".$mod."', '5'); return false;\" OnDblClick=\"LoadGet('1', '".$id.$typ."', '1', 'rating', '".$id."', '', '".$typ."', '".$mod."', '5'); return false;\" title=\""._RATE5."\"></div></li>"
			."</ul>";
		} elseif (!$cookies && !$num && $rating) {
			setcookie(substr($mod, 0, 2)."-".$id, $id, time() + intval($con[0]));
			$new = time();
			$db->sql_query("INSERT INTO ".$prefix."_rating VALUES (NULL, '$id', '$mod', '$new', '$uid', '$ip')");
			 if ($mod == "account" || $mod == "members") {
				$db->sql_query("UPDATE ".$prefix."_users SET user_votes=user_votes+1, user_totalvotes=user_totalvotes+$rating WHERE user_id='$id'");
				update_points(2);
			} elseif ($mod == "faq") {
				$db->sql_query("UPDATE ".$prefix."_faq SET score=score+$rating, ratings=ratings+1 WHERE fid='$id'");
				update_points(8);
			} elseif ($mod == "files") {
				$db->sql_query("UPDATE ".$prefix."_files SET votes=votes+1, totalvotes=totalvotes+$rating WHERE lid='$id'");
				update_points(12);
			} elseif ($mod == "forum") {
				$db->sql_query("UPDATE ".$prefix."_forum SET score=score+$rating, ratings=ratings+1 WHERE id='$id'");
				update_points(15);
			} elseif ($mod == "gallery") {
				#$db->sql_query("UPDATE ".$prefix."_gallery SET votes=votes+1, totalvotes=totalvotes+$rating WHERE lid='$id'");
				update_points(18);
			} elseif ($mod == "jokes") {
				$db->sql_query("UPDATE ".$prefix."_jokes SET rating=rating+$rating, ratingtot=ratingtot+1 WHERE jokeid='$id'");
				update_points(20);
			} elseif ($mod == "links") {
				$db->sql_query("UPDATE ".$prefix."_links SET votes=votes+1, totalvotes=totalvotes+$rating WHERE lid='$id'");
				update_points(24);
			} elseif ($mod == "media") {
				$db->sql_query("UPDATE ".$prefix."_media SET votes=votes+1, totalvotes=totalvotes+$rating WHERE id='$id'");
				update_points(27);
			} elseif ($mod == "multimedia") {
				#$db->sql_query("UPDATE ".$prefix."_multimedia SET votes=votes+1, totalvotes=totalvotes+$rating WHERE id='$id'");
				update_points(88);
			} elseif ($mod == "news") {
				$db->sql_query("UPDATE ".$prefix."_stories SET score=score+$rating, ratings=ratings+1 WHERE sid='$id'");
				update_points(33);
					} elseif ($mod == "gumor") {
				$db->sql_query("UPDATE ".$prefix."_gumor SET score=score+$rating, ratings=ratings+1 WHERE sid='$id'");
				update_points(39);
					} elseif ($mod == "games") {
				$db->sql_query("UPDATE ".$prefix."_games SET score=score+$rating, ratings=ratings+1 WHERE sid='$id'");
				update_points(27);
					} elseif ($mod == "kinonews") {
				$db->sql_query("UPDATE ".$prefix."_kinonews SET score=score+$rating, ratings=ratings+1 WHERE sid='$id'");
				update_points(30);
			} elseif ($mod == "pages") {
				$db->sql_query("UPDATE ".$prefix."_page SET score=score+$rating, ratings=ratings+1 WHERE pid='$id'");
				update_points(37);
			} elseif ($mod == "shop") {
				$db->sql_query("UPDATE ".$prefix."_products SET product_votes=product_votes+1, product_totalvotes=product_totalvotes+$rating WHERE product_id='$id'");
				update_points(41);
			}
			list($votes, $totalvotes) = $db->sql_fetchrow($db->sql_query("SELECT ".$query));
			echo vote_graphic($votes, $totalvotes);
		}
	}
}

# Format BB Code and Smilies
function textarea($id, $name, $var, $mod, $rows) {
	global $conf, $admin;
	$desc = ($var) ? $var : save_text($_POST[$name]);
	include("config/config_uploads.php");
	$con = explode("|", $confup[strtolower($mod)]);
	$style = (defined("ADMIN_FILE")) ? "admin" : strtolower($mod);
	$editor = intval(substr($admin[3], 0, 1));
	if ((defined("ADMIN_FILE") && $editor == 1) || (!defined("ADMIN_FILE") && $conf['redaktor'] == 1)) {
		$code = "<script type=\"text/javascript\" src=\"ajax/insert_code.js\"></script>"
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
		."<div class=\"editorbutton\" onmouseover=\"this.className='editorbuttonover';\" onmouseout=\"this.className='editorbutton';\" onclick=\"InsertCode('rtext', '', '', '', '".$id."')\"><img src=\"".img_find("editor/RT1")."\" title=\""._ERTEXT."\"></div>"
        ."<div class=\"editorbutton\" onmouseover=\"this.className='editorbuttonover';\" onmouseout=\"this.className='editorbutton';\" onclick=\"InsertCode('ltext', '', '', '', '".$id."')\"><img src=\"".img_find("editor/LT1")."\" title=\""._ELTEXT."\"></div>"

        ."<div class=\"editorbutton\" onmouseover=\"this.className='editorbuttonover';\" onmouseout=\"this.className='editorbutton';\" onclick=\"InsertCode('centerp', '', '', '', '".$id."')\"><img src=\"".img_find("editor/centerp")."\" title=\""._CLTEXT."\"></div>"
		 ."<div class=\"editorbutton\" onmouseover=\"this.className='editorbuttonover';\" onmouseout=\"this.className='editorbutton';\" onclick=\"InsertCode('video', '', '', '', '".$id."')\"><img src=\"".img_find("editor/video")."\" title=\""._VIDEO."\"></div>"
		."</div><br><br>"
		."<textarea id=\"".$id."\" name=\"".$name."\" cols=\"65\" rows=\"20%".$rows."\" class=\"".$style."\" OnKeyPress=\"TransliteFeld(this, event)\" OnSelect=\"FieldName(this, this.name)\" OnClick=\"FieldName(this, this.name)\" OnKeyUp=\"FieldName(this, this.name)\">".replace_break(htmlspecialchars_decode($desc))."</textarea>"
		."<div class=\"editor\">";
		if ((defined("ADMIN_FILE") && $con[8] == 1) || (is_user() && $con[8] == 1) || (!is_user() && $con[9] == 1)) $code .= "<div id=\"af".$id."-title\" class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\"><img src=\"".img_find("editor/upload")."\" OnClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '', '', '".$mod."', ''); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '', '', '".$mod."', ''); return false;\" title=\""._EUPLOAD."\"></div>";
		if (!$conf['smilies']) $code .= "<div id=\"sm".$id."-title\" class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\"><img src=\"".img_find("editor/smilie")."\" title=\""._ESMILIE."\"></div>";
		$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('quote', '', '', '', '".$id."')\"><img src=\"".img_find("editor/quote")."\" title=\""._EQUOTE."\"></div>";
			$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('spoiler', '', '', '', '".$id."')\"><img src=\"".img_find("editor/sploer")."\" title=\"Sploier\"></div>";
		$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('flv', '', '', '', '".$id."')\"><img src=\"".img_find("editor/flv")."\" title=\"FLV\"></div>";
		if (defined("ADMIN_FILE")){
$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('ratekp', '', '', '', '".$id."')\"><img src=\"".img_find("editor/rating")."\" title=\"Rating Kinopoisc\"></div>";}

	$code .= "<link rel=\"stylesheet\" href=\"ajax/color/dropdown.css\" type=\"text/css\" />
<script type=\"text/javascript\" src=\"ajax/color/dropdown.js\"></script>
<div class=\"floatleft\">
  <div class=\"editorbutton\" id=\"".$id."-ddheader\" onmouseover=\"ddMenu('".$id."',1);\" onmouseout=\"ddMenu('".$id."',-1);\"><img src=\"".img_find("editor/backcolor")."\" title=\""._ECOLOR."\"></div>
  <div class=\"ddcontent\" id=\"".$id."-ddcontent\" onmouseover=\"cancelHide('".$id."');\" onmouseout=\"ddMenu('".$id."',-1);\">
    <div class=\"ddinner\">
      <ul>
        <li class=\"underline\"><table width=\"150\" height=\"100\" cellpadding=\"0\" cellspacing=\"1\" border=\"1\" align=\"center\">
	<tr>
 <td  OnClick=\"InsertCode('color', '#FFFFFF', '', '', '".$id."')\"  bgcolor=\"#FFFFFF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FFCCCC', '', '', '".$id."')\"  bgcolor=\"#FFCCCC\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FFCC99', '', '', '".$id."')\"  bgcolor=\"#FFCC99\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FFFF99', '', '', '".$id."')\"  bgcolor=\"#FFFF99\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FFFFCC', '', '', '".$id."')\"  bgcolor=\"#FFFFCC\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#99FF99', '', '', '".$id."')\"  bgcolor=\"#99FF99\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#99FFFF', '', '', '".$id."')\"  bgcolor=\"#99FFFF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#CCFFFF', '', '', '".$id."')\"  bgcolor=\"#CCFFFF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#CCCCFF', '', '', '".$id."')\"  bgcolor=\"#CCCCFF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FFCCFF', '', '', '".$id."')\"  bgcolor=\"#FFCCFF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
	</tr>
	<tr>
		<td OnClick=\"InsertCode('color', '#CCCCCC', '', '', '".$id."')\"  bgcolor=\"#CCCCCC\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FF6666', '', '', '".$id."')\"  bgcolor=\"#FF6666\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FF9966', '', '', '".$id."')\"  bgcolor=\"#FF9966\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FFFF66', '', '', '".$id."')\"  bgcolor=\"#FFFF66\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FFFF33', '', '', '".$id."')\"  bgcolor=\"#FFFF33\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#66FF99', '', '', '".$id."')\"  bgcolor=\"#66FF99\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#33FFFF', '', '', '".$id."')\"  bgcolor=\"#33FFFF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#66FFFF', '', '', '".$id."')\"  bgcolor=\"#66FFFF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#9999FF', '', '', '".$id."')\"  bgcolor=\"#9999FF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FF99FF', '', '', '".$id."')\"  bgcolor=\"#FF99FF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
	</tr>
	<tr>
		<td OnClick=\"InsertCode('color', '#C0C0C0', '', '', '".$id."')\"  bgcolor=\"#C0C0C0\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FF0000', '', '', '".$id."')\"  bgcolor=\"#FF0000\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FF9900', '', '', '".$id."')\"  bgcolor=\"#FF9900\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FFCC66', '', '', '".$id."')\"  bgcolor=\"#FFCC66\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FFFF00', '', '', '".$id."')\"  bgcolor=\"#FFFF00\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#33FF33', '', '', '".$id."')\"  bgcolor=\"#33FF33\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#66CCCC', '', '', '".$id."')\"  bgcolor=\"#66CCCC\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#33CCFF', '', '', '".$id."')\"  bgcolor=\"#33CCFF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#6666CC', '', '', '".$id."')\"  bgcolor=\"#6666CC\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#CC66CC', '', '', '".$id."')\"  bgcolor=\"#CC66CC\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
	</tr>
	<tr>
		<td OnClick=\"InsertCode('color', '#999999', '', '', '".$id."')\"  bgcolor=\"#999999\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#CC0000', '', '', '".$id."')\"  bgcolor=\"#CC0000\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FF6600', '', '', '".$id."')\"  bgcolor=\"#FF6600\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FFCC33', '', '', '".$id."')\"  bgcolor=\"#FFCC33\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#FFCC00', '', '', '".$id."')\"  bgcolor=\"#FFCC00\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#33CC00', '', '', '".$id."')\"  bgcolor=\"#33CC00\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#00CCCC', '', '', '".$id."')\"  bgcolor=\"#00CCCC\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#3366FF', '', '', '".$id."')\"  bgcolor=\"#3366FF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#6633FF', '', '', '".$id."')\"  bgcolor=\"#6633FF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#CC33CC', '', '', '".$id."')\"  bgcolor=\"#CC33CC\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
	</tr>
	<tr>
		<td OnClick=\"InsertCode('color', '#666666', '', '', '".$id."')\"  bgcolor=\"#666666\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#990000', '', '', '".$id."')\"  bgcolor=\"#990000\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#CC6600', '', '', '".$id."')\"  bgcolor=\"#CC6600\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#CC9933', '', '', '".$id."')\"  bgcolor=\"#CC9933\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#999900', '', '', '".$id."')\"  bgcolor=\"#999900\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#009900', '', '', '".$id."')\"  bgcolor=\"#009900\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#339999', '', '', '".$id."')\"  bgcolor=\"#339999\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#3333FF', '', '', '".$id."')\"  bgcolor=\"#3333FF\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#6600CC', '', '', '".$id."')\"  bgcolor=\"#6600CC\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#993399', '', '', '".$id."')\"  bgcolor=\"#993399\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
	</tr>
	<tr>
		<td OnClick=\"InsertCode('color', '#333333', '', '', '".$id."')\"  bgcolor=\"#333333\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#660000', '', '', '".$id."')\"  bgcolor=\"#660000\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#993300', '', '', '".$id."')\"  bgcolor=\"#993300\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#996633', '', '', '".$id."')\"  bgcolor=\"#996633\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#666600', '', '', '".$id."')\"  bgcolor=\"#666600\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#006600', '', '', '".$id."')\"  bgcolor=\"#006600\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#336666', '', '', '".$id."')\"  bgcolor=\"#336666\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#000099', '', '', '".$id."')\"  bgcolor=\"#000099\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#333399', '', '', '".$id."')\"  bgcolor=\"#333399\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#663366', '', '', '".$id."')\"  bgcolor=\"#663366\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
	</tr>
	<tr>
		<td OnClick=\"InsertCode('color', '#000000', '', '', '".$id."')\"  bgcolor=\"#000000\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#330000', '', '', '".$id."')\"  bgcolor=\"#330000\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#663300', '', '', '".$id."')\"  bgcolor=\"#663300\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#663333', '', '', '".$id."')\"  bgcolor=\"#663333\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#333300', '', '', '".$id."')\"  bgcolor=\"#333300\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#003300', '', '', '".$id."')\"  bgcolor=\"#003300\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#003333', '', '', '".$id."')\"  bgcolor=\"#003333\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#000066', '', '', '".$id."')\"  bgcolor=\"#000066\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#330099', '', '', '".$id."')\"  bgcolor=\"#330099\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
		<td OnClick=\"InsertCode('color', '#330033', '', '', '".$id."')\"  bgcolor=\"#330033\" width=\"10\" height=\"10\"><img width=\"1\" height=\"1\"></td>
	</tr>
</table></li>
        
       
      </ul>
    </div>
  </div>
</div>

";
$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" 
OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('mp3', '', '', '', '".$id."')\"><img src=\"/images/editor/mp3.gif\" 
title=\"MP3\"></div>";
//my bb code
include("config/config_bb.php");
foreach ($confbb as $val) {
if ($val != "") {
preg_match("#(.*)\|(.*)\|(.*)#i",$val, $out);
$image = (file_exists(img_find("editor/$out[1]"))) ? img_find("editor/$out[1]") : "images_for_bb.php?name=$out[1]";
$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('$out[1]', '', '', '', '".$id."')\"><img src=\"$image\" title=\"$out[2]\"></div>";
}}
//my bb code
		if (substr(_LOCALE, 0, 2) == "ru") {
			$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('flv2', '', '', '', '".$id."')\"><img src=\"".img_find("editor/flv2")."\" title=\"FLV2\"></div>";

			$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"translateAlltoCyrillic()\"><img src=\"".img_find("editor/rus")."\" title=\""._ERUS."\"></div>"
			."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"translateAlltoLatin()\"><img src=\"".img_find("editor/eng")."\" title=\""._ELAT."\"></div>"
			."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"changelanguage()\"><img src=\"".img_find("editor/auto")."\" title=\""._EAUTOTR."\"></div>";
		}
		$fonts = 0;
		$font = array(_FONT, "Arial", "Courier New", "Mistral", "Impact", "Sans Serif", "Tahoma", "Helvetica", "Verdana");
		foreach ($font as $val) if ($val != "") $fonts .= "<option style=\"font-family: ".$val.";\" value=\"".$val."\">".$val."</option>";
		$fsizes = 0;
		$fsize = array(_ESIZE, "8", "10", "12", "14", "16", "18", "20", "22", "24", "26", "28", "30", "32");
		foreach ($fsize as $val) if ($val != "") $fsizes .= "<option value=\"".$val."\">".$val."</option>";
		$code .= "<div class=\"editorselect\"><select name=\"family\" OnChange=\"InsertCode('family', this.options[this.selectedIndex].value, '', '', '".$id."'); this.selectedIndex=0;\">".$fonts."</select></div>"
		."<div class=\"editorselect\"><select name=\"size\" OnChange=\"InsertCode('size', this.options[this.selectedIndex].value, '', '', '".$id."'); this.selectedIndex=0;\">".$fsizes."</select></div></div><br><br>";
		if ($conf['smilies'] == 1) {
			$code .= "<div class=\"smilies\">";
			for ($i = 1; $i < 19; $i++) {
				$i = ($i < 10) ? "0".$i  : $i;
				$code .= " <img src=\"images/smilies/$i.gif\" OnClick=\"AddSmile(' *$i');\" style=\"cursor: pointer; margin: 3px 2px 0px 0px;\" alt=\""._SMILIE." - $i\" title=\""._SMILIE." - $i\">";
			}
			$code .= "</div>";
		} elseif ($conf['smilies'] == 2) {
			$code .= "<div class=\"smilies\">";
			$i = 1;
			$dir = opendir("images/smilies");
			while ($entry = readdir($dir)) {
				if (preg_match("/(\.gif|\.png|\.jpg|\.jpeg)$/is", $entry) && $entry != "." && $entry != "..") {
					$i = ($i < 10) ? "0".$i  : $i;
					$code .= " <img src=\"images/smilies/$i.gif\" OnClick=\"AddSmile(' *$i');\" style=\"cursor: pointer; margin: 3px 2px 0px 0px;\" alt=\""._SMILIE." - $i\" title=\""._SMILIE." - $i\">";
					$i++;
				}
			}
			closedir($dir);
			$code .= "</div>";
		} else {
			$code .= "<div id=\"sm".$id."\" class=\"smilies\"><script type=\"text/javascript\">var edits = new SwitchCont('sm".$id."', '2');</script>";
			$i = 1;
			$dir = opendir("images/smilies");
			while ($entry = readdir($dir)) {
				if (preg_match("/(\.gif|\.png|\.jpg|\.jpeg)$/is", $entry) && $entry != "." && $entry != "..") {
					$i = ($i < 10) ? "0".$i  : $i;
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
					<input type=\"button\" value=\"NEW\" OnClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '', '', '".$mod."', '1'); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '', '', '".$mod."', '1'); return false;\" class=\"fbutton\">
				<input type=\"button\" value=\""._UPDATE."\" OnClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '', '', '".$mod."', ''); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '', '', '".$mod."', ''); return false;\" class=\"fbutton\"></p><br></div>
				<noscript>Were sorry. SWFUpload could not load. You must have JavaScript enabled to enjoy SWFUpload.</noscript>
				<div id=\"divLoadingContent\" style=\"display: none;\">SWFUpload is loading. Please wait a moment...</div>
				<div id=\"divLongLoading\" style=\"display: none;\">SWFUpload is taking a long time to load or the load has failed. Please make sure that the Flash Plugin is enabled and that a working version of the Adobe Flash Player is installed.</div>
				<div id=\"divAlternateContent\" style=\"display: none;\">Were sorry.  SWFUpload could not load.  You may need to install or upgrade Flash Player. Visit the <a href=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" target=\"_blank\">Adobe website</a> to get the Flash Player.</div>";
			} else {
				$code .= "<input type=\"button\" value=\"NEW\" OnClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '', '', '".$mod."', '1'); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '', '', '".$mod."', '1'); return false;\" style=\"margin-top: 5px; margin-right:5px;\" class=\"fbutton\">";
				$code .= "<input type=\"button\" value=\""._UPDATE."\" OnClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '', '', '".$mod."', ''); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '3', 'show_files', '".$id."', '', '', '".$mod."', ''); return false;\" style=\"margin-top: 5px;\" class=\"fbutton\">";
			}
			$code .= "<div id=\"repf".$id."\" style=\"margin: 5px;\"></div></div><script type=\"text/javascript\">var editu = new SwitchCont('af".$id."', '2');</script>";
		}
	} elseif ((defined("ADMIN_FILE") && $editor == 2) || (!defined("ADMIN_FILE") && $conf['redaktor'] == 2)) {
		if (defined("ADMIN_FILE") && $editor == 2 && !preg_match("#blocks|configure|editor|groups|rss_conf|security|template|style#i", $_GET['op'])) {
			static $jscript;
			if (!isset($jscript)) {
				$code = "<script type=\"text/javascript\" src=\"modules/tiny_mce/tiny_mce.js\"></script>
				<script type=\"text/javascript\">
				tinyMCE.init({
					mode : \"textareas\",
					theme : \"advanced\",
					plugins : \"safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template\",

					theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect\",
					theme_advanced_buttons2 : \"bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code\",
					theme_advanced_buttons3 : \"save,newdocument,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,forecolor,backcolor,|,preview\",
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
		} elseif ((!defined("ADMIN_FILE") && $conf['redaktor'] == 2)) {
			static $jscript;
			if (!isset($jscript)) {
				$code = "<script type=\"text/javascript\" src=\"modules/tiny_mce/tiny_mce.js\"></script>
				<script type=\"text/javascript\">
				tinyMCE.init({
					mode : \"textareas\",
					theme : \"advanced\",
					plugins : \"safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template\",

					theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect\",
					theme_advanced_buttons2 : \"bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code\",
					theme_advanced_buttons3 : \"save,newdocument,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,forecolor,backcolor,|,preview\",

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
		."<textarea id=\"".$id."\" name=\"".$name."\" cols=\"65\" rows=\"".$rows."\" class=\"".$style."\" OnMouseOver=\"tinyMCE.get('".$id."').show();\">".$desc."</textarea>";
	} elseif (defined("ADMIN_FILE") && $editor == 3) {
		ob_start();
		include("modules/spaw2/spaw.inc.php");
		$sp = new SpawEditor($name, $desc);
		$sp->show();
		$code = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>".ob_get_contents();
		ob_end_clean();
	} elseif (defined("ADMIN_FILE") && $editor == 4) {
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
		$code = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>".ob_get_contents();
		ob_end_clean();
	} else {
		$code = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>"
		."<textarea id=\"".$id."\" name=\"".$name."\" cols=\"65\" rows=\"".$rows."\" class=\"".$style."\">".$desc."</textarea>";
	}
	$code .= "</td></tr></table>";
	return $code;
}

# Format ajax edit
function textareae($obj, $go, $op, $id, $cid, $typ, $mod, $text, $rows) {
	global $conf, $admin;
	$style = (defined("ADMIN_FILE")) ? "admin" : strtolower($mod);
	$editor = intval(substr($admin[3], 0, 1));
	$desc = ((defined("ADMIN_FILE") && $editor == 1) || (!defined("ADMIN_FILE") && $conf['redaktor'] == 1)) ? replace_break($text) : $text;
	$code = "<form id=\"form".$obj."\" method=\"post\" action=\"ajax.php\">"
	."<input type=\"hidden\" name=\"method\" value=\"POST\">"
	."<input type=\"hidden\" id=\"go\" name=\"go\" value=\"".$go."\">"
	."<input type=\"hidden\" id=\"op\" name=\"op\" value=\"".$op."\">"
	."<input type=\"hidden\" id=\"id\" name=\"id\" value=\"".$id."\">"
	."<input type=\"hidden\" id=\"cid\" name=\"cid\" value=\"".$cid."\">"
	."<input type=\"hidden\" id=\"typ\" name=\"typ\" value=\"".$typ."\">"
	."<input type=\"hidden\" id=\"mod\" name=\"mod\" value=\"".$mod."\">"
	."<input type=\"hidden\" id=\"name\" name=\"name\" value=\"\">"
	."<input type=\"hidden\" id=\"title\" name=\"title\" value=\"\">"
	."<textarea id=\"text\" name=\"text\" cols=\"65\" rows=\"".$rows."\" class=\"earea\">".$desc."</textarea>"
	."<input type=\"hidden\" id=\"ftext\" name=\"ftext\" value=\"\">"
	."<input type=\"hidden\" id=\"check\" name=\"check\" value=\"0\">"
	."<div style=\"clear: both;\"><input type=\"submit\" value=\""._SAVE."\" OnClick=\"LoadPost('1', '".$obj."'); return false;\" OnDblClick=\"LoadPost('1', '".$obj."'); return false;\" title=\""._SAVE."\" class=\"ebutton\"></form>"
	."<input type=\"submit\" value=\""._BACK."\" OnClick=\"LoadGet('1', '".$obj."', '".$go."', '".$op."', '".$id."', '".$cid."', '".$typ."', '".$mod."', ''); return false;\" OnDblClick=\"LoadGet('1', '".$obj."', '".$go."', '".$op."', '".$id."', '".$cid."', '".$typ."', '".$mod."', ''); return false;\" title=\""._BACK."\" class=\"bbutton\"></div>";
	return $code;
}

# Format Page
function get_page($mod) {
	open();
	echo "<h5>[ <a href=\"javascript:history.go(-1)\" title=\""._BACK."\">"._BACK."</a> | <a href=\"index.php?name=$mod\" title=\""._PAGEHOME."\">"._PAGEHOME."</a> | <a href=\"#\" title=\""._PAGETOP."\">"._PAGETOP."</a> ]</h5>";
	close();
}

# Format num article
function num_article() {
	global $prefix, $db, $conf, $currentlang;
	$pnum = func_get_args();
	if (!defined("ADMIN_FILE")) {
		$where = ($conf['multilingual']) ? "WHERE modul='".$pnum[0]."' AND (language='$currentlang' OR language='')" : "WHERE modul='".$pnum[0]."'";
		$result = $db->sql_query("SELECT id, auth_read FROM ".$prefix."_categories ".$where." ORDER BY id");
		while (list($cid, $auth_read) = $db->sql_fetchrow($result)) if (is_acess($auth_read)) $catid[] = $cid;
		$where = ($catid) ? "WHERE ".$pnum[5]." IN (".implode(", ", $catid).") AND ".$pnum[6] : "WHERE ".$pnum[6];
	} else {
		$where = "WHERE ".$pnum[6];
	}
	list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(".$pnum[3].") FROM ".$prefix.$pnum[4]." ".$where));
	$numpages = ceil($numstories / $pnum[1]);
	num_page($pnum[0], $numstories, $numpages, $pnum[1], $pnum[2]);
}

# Format Nummer Page
function num_page() {
	global $admin_file;
	$pnum = func_get_args();
	$num = (isset($pnum[6])) ? intval($pnum[6]) : ((isset($_GET['num'])) ? intval($_GET['num']) : "1");
	$mnum = (isset($pnum[5])) ? $pnum[5] : 8;
	$nnum = $mnum + 1;
	if ($pnum[2] > 1) {
		if (defined("ADMIN_FILE")) {
			$index = $admin_file;
			$module = "";
		} else {
			$index = "index";
			$module = "name=".$pnum[0]."&";
		}
		$content = "";
		if ($num > 1) {
			$prevpage = $num - 1;
			$content .= "<a href=\"".$index.".php?".$module.$pnum[4]."num=$prevpage\" title=\"&lt;&lt;\">&lt;&lt;</a> ";
		}
		for ($i = 1; $i < $pnum[2]+1; $i++) {
			if ($i == $num) {
				$content .= "<span title=\"$i\">$i</span>";
			} else {
				if ((($i > ($num - $mnum)) && ($i < ($num + $mnum))) || ($i == $pnum[2]) || ($i == 1)) $content .= "<a href=\"".$index.".php?".$module.$pnum[4]."num=$i\" title=\"$i\">$i</a>";
			}
			if ($i < $pnum[2]) {
				if (($i > ($num - $nnum)) && ($i < ($num + $mnum))) $content .= " ";
				if (($num > $nnum) && ($i == 1)) $content .= " <span>...</span>";
				if (($num < ($pnum[2] - $mnum)) && ($i == ($pnum[2] - 1))) $content .= "<span>...</span> ";
			}
		}
		if ($num < $pnum[2]) {
			$nextpage = $num + 1;
			$content .= " <a href=\"".$index.".php?".$module.$pnum[4]."num=$nextpage\" title=\"&gt;&gt;\">&gt;&gt;</a>";
		}
		pagenum(_OVERALL, $pnum[1], _ON, $pnum[2], _PAGE_S, $pnum[3], _PERPAGE, $content);
		echo " 
<script type=\"text/javascript\" src=\"ajax/paginator/common.js\"></script> 
<script type=\"text/javascript\" src=\"ajax/paginator/paginator.js\"></script> 
<link rel=\"stylesheet\" type=\"text/css\" href=\"ajax/paginator/paginator.css\" /> 
<div class=\"paginator\" id=\"paginator_news\"></div> 
<script type=\"text/javascript\"> 
pag = new Paginator('paginator_news',".$pnum[2].",10,$num,\"".$index.".php?".$module.$pnum[4]."num=\"); 
</script>";
	}



}

# Format Nummer Pages
function num_pages() {
	global $admin_file;
	$pnum = func_get_args();
	$pag = isset($_GET['pag']) ? intval($_GET['pag']) : "1";
	$num = (isset($pnum[6])) ? intval($pnum[6]) : ((isset($_GET['num'])) ? intval($_GET['num']) : "1");
	$mnum = (isset($pnum[5])) ? $pnum[5] : 8;
	$nnum = $mnum + 1;
	if ($pnum[2] > 1) {
		if (defined("ADMIN_FILE")) {
			$index = $admin_file;
			$module = "";
		} else {
			$index = "index";
			$module = "name=".$pnum[0]."&";
		}
		$content = "";
		if ($pag > 1) {
			$prevpage = $pag - 1;
			$content .= "<a href=\"".$index.".php?".$module.$pnum[4]."pag=$prevpage&num=$num\" title=\"&lt;&lt;\">&lt;&lt;</a> ";
		}
		for ($i = 1; $i < $pnum[2]+1; $i++) {
			if ($i == $pag) {
				$content .= "<span title=\"$i\">$i</span>";
			} else {
				if ((($i > ($pag - $mnum)) && ($i < ($pag + $mnum))) || ($i == $pnum[2]) || ($i == 1)) $content .= "<a href=\"".$index.".php?".$module.$pnum[4]."pag=$i&num=$num\" title=\"$i\">$i</a>";
			}
			if ($i < $pnum[2]) {
				if (($i > ($pag - $nnum)) && ($i < ($pag + $mnum))) $content .= " ";
				if (($pag > $nnum) && ($i == 1)) $content .= " <span>...</span>";
				if (($pag < ($pnum[2] - $mnum)) && ($i == ($pnum[2] - 1))) $content .= "<span>...</span> ";
			}
		}
		if ($pag < $pnum[2]) {
			$nextpage = $pag + 1;
			$content .= " <a href=\"".$index.".php?".$module.$pnum[4]."pag=$nextpage&num=$num\" title=\"&gt;&gt;\">&gt;&gt;</a>";
		}
		pagenum(_OVERALL, $pnum[1], _ON, $pnum[2], _PAGE_S, $pnum[3], _PERPAGE, $content);
	}
}

# Format Nummer Page
function num_ajax() {
	global $admin_file;
	$pnum = func_get_args();
	$num = ($pnum[4]) ? $pnum[4] : 1;
	$mnum = ($pnum[3]) ? $pnum[3] : 8;
	$go = ($pnum[5]) ? $pnum[5] : 0;
	$op = ($pnum[6]) ? $pnum[6] : "";
	$id = ($pnum[7]) ? $pnum[7] : 0;
	$typ = ($pnum[8]) ? $pnum[8] : 0;
	$mod = ($pnum[9]) ? $pnum[9] : "";
	$nnum = $mnum + 1;
	if ($pnum[1] > 1) {
		$content = "";
		if ($num > 1) {
			$prevpage = $num - 1;
$content .= "<a href=\"#\" OnClick=\"LoadGet('1', 'f".$id."', '".$go."', '".$op."', '".$id."', '".$prevpage."', '".$typ."', '".$mod."', '".intval($_GET['text'])."'); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '".$go."', '".$op."', '".$id."', '".$prevpage."', '".$typ."', '".$mod."', '".intval($_GET['text'])."'); return false;\" title=\"&lt;&lt;\">&lt;&lt;</a> ";
		}
		for ($i = 1; $i < $pnum[1]+1; $i++) {
			if ($i == $num) {
				$content .= "<span title=\"$i\">$i</span>";
			} else {
 if ((($i > ($num - $mnum)) && ($i < ($num + $mnum))) OR ($i == $pnum[1]) || ($i == 1)) $content .= "<a href=\"#\" OnClick=\"LoadGet('1', 'f".$id."', '".$go."', '".$op."', '".$id."', '".$i."', '".$typ."', '".$mod."', '".intval($_GET['text'])."'); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '".$go."', '".$op."', '".$id."', '".$i."', '".$typ."', '".$mod."', '".intval($_GET['text'])."'); return false;\" title=\"$i\">$i</a>"; 
			}
			if ($i < $pnum[1]) {
				if (($i > ($num - $nnum)) && ($i < ($num + $mnum))) $content .= " ";
				if (($num > $nnum) && ($i == 1)) $content .= " <span>...</span>";
				if (($num < ($pnum[1] - $mnum)) && ($i == ($pnum[1] - 1))) $content .= "<span>...</span> ";
			}
		}
		if ($num < $pnum[1]) {
			$nextpage = $num + 1;
$content .= " <a href=\"#\" OnClick=\"LoadGet('1', 'f".$id."', '".$go."', '".$op."', '".$id."', '".$nextpage."', '".$typ."', '".$mod."', '".intval($_GET['text'])."'); return false;\" OnDblClick=\"LoadGet('1', 'f".$id."', '".$go."', '".$op."', '".$id."', '".$nextpage."', '".$typ."', '".$mod."', '".intval($_GET['text'])."'); return false;\" title=\"&gt;&gt;\">&gt;&gt;</a>";
		}
		ob_start();
		pagenum(_OVERALL, $pnum[0], _ON, $pnum[1], _PAGE_S, $pnum[2], _PERPAGE, $content);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

# Check type upload file
function check_file($type, $typefile) {
	$strtypefile = str_replace(",", "|", $typefile);
	if (!preg_match("#".$strtypefile."#i", $type) || preg_match("#php.*|js|htm|html|phtml|cgi|pl|perl|asp#i", $type)) return _ERROR_FILE;
}

# Check size upload file
function check_size($file, $width, $height) {
	list($imgwidth, $imgheight) = getimagesize($file);
	if ($imgwidth > $width || $imgheight > $height) return _ERROR_SIZE;
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
					$newname = ($namefile) ? $namefile."-".gen_pass(10)."-0.".$type : gen_pass(15).".".$type;
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
		if (preg_match("#^lang\-(.+)\.php#", $file, $matches)) {
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
function modul($name, $class, $modul) {
	$modul = explode(",", $modul);
	$content = "<select name=\"".$name."[]\" size=\"5\" multiple=\"multiple\" class=\"".$class."\">";
	$dir = opendir("modules");
	while ($file = readdir($dir)) {
		if (!preg_match("#\.#", $file)) {
			foreach ($modul as $val) {
				if ($val != "" && $val == $file) {
					$selected = "selected";
					break;
				} else {
					$selected = "";
				}
			}
			$content .= "<option value=\"".$file."\" $selected>".$file."</option>";
		}
	}
	closedir($dir);
	$content .= "</select>";
	return $content;
}

# Format categorie module
function cat_modul() {
	$arg = func_get_args();
	$submit = ($arg[3]) ? "OnChange=\"submit()\"" : "";
	$content = "<select name=\"".$arg[0]."\" class=\"".$arg[1]."\" $submit>";
	$cname = array(_FAQ, _FILES, _FORUM, _HELP, _JOKES, _LINKS, _MEDIA, _NEWS, _GUMOR, _GAMES, _KINONEWS, _PAGES, _SHOP);
	$mods = array("faq", "files", "forum", "help", "jokes", "links", "media", "news", "gumor", "games", "kinonews", "pages", "shop");
	for ($i = 0; $i < count($mods); $i++) {
		$selected = ($arg[2] == $mods[$i]) ? "selected" : "";
		$content .= "<option value=\"".$mods[$i]."\" $selected>".$cname[$i]." - ".$mods[$i]."</option>";
	}
	$content .= "</select>";
	return $content;
}

# Format editor
function redaktor($id, $name, $class, $editor) {
	$content = "<select name=\"".$name."\" class=\"".$class."\">";
	$ename = ($id == 1) ? array(_NO, _REDAKTOR_BB, _REDAKTOR_HTML." TinyMCE", _REDAKTOR_HTML." Spaw 2", _REDAKTOR_HTML." FCKeditor") : array(_NO, _REDAKTOR_BB, _REDAKTOR_HTML." TinyMCE");
	$editors = ($id == 1) ? array("0", "1", "2", "3", "4") : array("0", "1", "2");
	for ($i = 0; $i < count($editors); $i++) {
		$selected = ($editor == $editors[$i]) ? "selected" : "";
		$content .= "<option value=\"".$editors[$i]."\" $selected>".$ename[$i]."</option>";
	}
	$content .= "</select>";
	return $content;
}

# Show comments
function ashowcom() {
	global $prefix, $db, $admin_file, $conf, $confu, $confc, $user, $currentlang;
	$arg = func_get_args();
	$cid = intval($arg[0]);
	$mod = analyze($arg[1]);
	$plnum = (intval($arg[2])) ? intval($arg[2]) : 8;
	if (defined("ADMIN_FILE")) {
		$ordern = "";
		$ccnum = $conf['anum'];
	} else {
		$ordern = (is_moder($mod)) ? "WHERE cid='$cid' AND modul='".$mod."'" : "WHERE cid='$cid' AND modul='".$mod."' AND status!='0'";
		$ccnum = $confc['num'];
	}
	list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(cid) FROM ".$prefix."_comment ".$ordern));
	ob_start();
	if ($numstories > 0) {
		$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
		$offset = ($num - 1) * $ccnum;
		$numpages = ceil($numstories / $ccnum);
		if ($confc['sort']) {
			$sort = "ASC";
			$a = ($num) ? $offset+1 : 1;
		} else {
			$sort = "DESC";
			$a = $numstories;
			if ($numstories > $offset) $a -= $offset;
		}
		$result = $db->sql_query("SELECT id, cid, modul, UNIX_TIMESTAMP(date) as format, uid, name, host_name, comment, status FROM ".$prefix."_comment ".$ordern." ORDER BY date ".$sort." LIMIT ".$offset.", ".$ccnum);
		while (list($com_id, $com_cid, $com_modul, $com_date, $com_uid, $com_name, $com_host, $com_text, $com_status) = $db->sql_fetchrow($result)) {
			$cmassiv[] = array($com_id, $com_cid, $com_modul, $com_date, $com_uid, $com_name, $com_host, $com_text, $com_status);
			if ($com_uid) $where[] = $com_uid;
			unset($com_id, $com_cid, $com_modul, $com_date, $com_uid, $com_name, $com_host, $com_text, $com_status);
		}
		if ($where) {
			$result2 = $db->sql_query("SELECT u.user_id, u.user_name, u.user_rank, u.user_email, u.user_website, u.user_avatar, u.user_regdate, u.user_icq, u.user_from, u.user_sig, u.user_viewemail, u.user_aim, u.user_yim, u.user_msnm, u.user_points, u.user_warnings, u.user_gender, u.user_votes, u.user_totalvotes, g.name, g.rank, g.color FROM ".$prefix."_users AS u LEFT JOIN ".$prefix."_groups AS g ON ((g.extra=1 AND u.user_group=g.id) OR (g.extra!=1 AND u.user_points>=g.points)) WHERE u.user_id IN (".implode(", ", $where).") ORDER BY g.extra ASC, g.points ASC");
			while (list($user_id, $user_name, $user_rank, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_from, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msn, $user_points, $user_warnings, $user_gender, $user_votes, $user_totalvotes, $user_gname, $user_grank, $user_gcolor) = $db->sql_fetchrow($result2)) {
				$umassiv[] = array($user_id, $user_name, $user_rank, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_from, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msn, $user_points, $user_warnings, $user_gender, $user_votes, $user_totalvotes, $user_gname, $user_grank, $user_gcolor);
				unset($user_id, $user_name, $user_rank, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_from, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msn, $user_points, $user_warnings, $user_gender, $user_votes, $user_totalvotes, $user_gname, $user_grank, $user_gcolor);
			}
		}
		open();
		if (defined("ADMIN_FILE")) {
			echo "<form name=\"comm\" action=\"".$admin_file.".php\" method=\"post\">";
			$bnum = $ccnum - 1;
			$b = 0;
		}
		foreach ($cmassiv as $val) {
			$com_id = $val[0];
			$com_cid = $val[1];
			$com_modul = $val[2];
			$com_date = $val[3];
			$com_uid = $val[4];
			$com_name = $val[5];
			$com_host = $val[6];
			$com_text = $val[7];
			$com_status = $val[8];
			unset($user_id, $user_name, $user_rank, $user_email, $user_website, $user_avatar, $user_regdate, $user_icq, $user_from, $user_sig, $user_viewemail, $user_aim, $user_yim, $user_msn, $user_points, $user_warnings, $user_gender, $user_votes, $user_totalvotes, $user_gname, $user_grank, $user_gcolor);
			if ($umassiv) {
				foreach ($umassiv as $val2) {
					if (strtolower($com_uid) == strtolower($val2[0])) {
						$user_id = $val2[0];
						$user_name = $val2[1];
						$user_rank = $val2[2];
						$user_email = $val2[3];
						$user_website = $val2[4];
						$user_avatar = $val2[5];
						$user_regdate = $val2[6];
						$user_icq = $val2[7];
						$user_from = $val2[8];
						$user_sig = $val2[9];
						$user_viewemail = $val2[10];
						$user_aim = $val2[11];
						$user_yim = $val2[12];
						$user_msn = $val2[13];
						$user_points = $val2[14];
						$user_warnings = $val2[15];
						$user_gender = $val2[16];
						$user_votes = $val2[17];
						$user_totalvotes = $val2[18];
						$user_gname = $val2[19];
						$user_grank = $val2[20];
						$user_gcolor = $val2[21];
					}
				}
			}
			$poster = ($user_name) ? user_info($user_name, 1) : $com_name." (".$confu['anonym'].")";
			$avname = ($user_name) ? $user_name : $com_name." (".$confu['anonym'].")";
			$alink = ($user_avatar && file_exists($confu['adirectory']."/".$user_avatar)) ? $user_avatar : "00.gif";
			$avatar = "<img src=\"".$confu['adirectory']."/".$alink."\" alt=\"$avname\" title=\"$avname\">";
			$ctime = date("Y-m-d H:i:s", $com_date);
			$date = "<img src=\"".img_find("forum/t_post")."\" border=\"0\" alt=\""._PADD."\" title=\""._PADD."\"> ".format_time($ctime, _TIMESTRING);
			$ip = (is_moder($com_modul)) ? user_geo_ip($com_host, 4)." |" : "";
			$amess = "<a href=\"".$_SERVER['REQUEST_URI']."#".$com_id."\" title=\""._COMMENT.": ".$a."\">"._COMMENT.": ".$a."</a>";
			$rank = ($user_rank) ? $user_rank : "";
			$rlink = ($user_grank && file_exists("images/ranks/".$user_grank)) ? "<img src=\"images/ranks/".$user_grank."\" alt=\""._RANK."\" title=\""._RANK."\">" : $user_grank;
			$rate = ajax_rating(0, $user_id, "account", $user_votes, $user_totalvotes, $com_id);
			$rwarn = ($user_warnings) ? warn_graphic($user_warnings) : "";
			$group = ($user_gname) ? _GROUP.": <span style=\"color: ".$user_gcolor."\">".$user_gname."</span>" : "";
			$point = ($confu['point'] && $user_points) ? _POINTS.": ".$user_points : "";
			$regdate = ($user_regdate) ? _REG_DATE.": ".format_time($user_regdate) : "";
			$gender = ($user_gender) ? _GENDER.": ".gender($user_gender, 1) : "";
			$from = ($user_from) ? _FROM.": ".$user_from : "";
			$sig = ($user_sig) ? "<hr>".$user_sig : "";
			$personal = (is_moder($com_modul) || is_user() || $confc['anonpost'] != 0) ? "<a href=\"javascript: InsertCode('name', '".$avname."', '', '', 'acom');\" title=\""._PERSONAL."\"><img src=\"".img_find("forum/$currentlang/personal")."\" border=\"0\" alt=\""._PERSONAL."\" title=\""._PERSONAL."\"></a>" : "";
			$down = "<a href=\"javascript: scroll(0, 100000);\" title=\""._PDOWN."\"><img src=\"".img_find("forum/down")."\" border=\"0\" alt=\""._PDOWN."\" title=\""._PDOWN."\"></a>";
			$up = "<a href=\"javascript: scroll(0, 0);\" title=\""._PUP."\"><img src=\"".img_find("forum/up")."\" border=\"0\" alt=\""._PUP."\" title=\""._PUP."\"></a>";
			#$privat = ($confc['privat'] && is_active('messages')) ? "<a href=\"javascript: scroll(0, 0);\" title=\""._SENDMES."\"><img src=\"".img_find("forum/$currentlang/privat")."\" border=\"0\" alt=\""._SENDMES."\" title=\""._SENDMES."\"></a>" : "";
			$profil = ($confc['profil'] && $user_name) ? "<a href=\"index.php?name=account&op=info&uname=".urlencode($user_name)."\" title=\""._PERSONALINFO."\"><img src=\"".img_find("forum/$currentlang/profil")."\" border=\"0\" alt=\""._PERSONALINFO."\" title=\""._PERSONALINFO."\"></a>" : "";
			$web = ($confc['web'] && $user_website) ? "<a href=\"".$user_website."\" target=\"_blank\" title=\""._DOWNLLINK."\"><img src=\"".img_find("forum/$currentlang/web")."\" border=\"0\" alt=\""._DOWNLLINK."\" title=\""._DOWNLLINK."\"></a>" : "";
			#$warn = "<a href=\"javascript: scroll(0, 0);\" title=\""._WARNM."\"><img src=\"".img_find("forum/$currentlang/warn")."\" border=\"0\" alt=\""._WARNM."\" title=\""._WARNM."\"></a>";

			$thank = (is_moder($com_modul) || is_user() || $confc['anonpost'] != 0) ? "<a href=\"javascript:scroll(0, 100000);javascript:copyQ('');javascript: Zitata('name', '".$avname."', '', '', 'acom');\" title=\""._THANK."\"><img src=\"".img_find("forum/$currentlang/qreply")."\" border=\"0\" alt=\"Цитата\" title=\"Цитата\"></a>" : "";			if (is_moder($com_modul)) {
				if (defined("ADMIN_FILE")) {
					$edit = add_menu("com".$com_id, "<a href=\"".view_article($com_modul, $com_cid, $com_id)."\" title=\""._MVIEW."\">"._MVIEW."</a>||<a href=\"".$admin_file.".php?op=comm_edit&id=".$com_id."\" title=\""._FULLEDIT."\">"._FULLEDIT."</a>||<a href=\"".$admin_file.".php?op=comm_del&id=$com_id&refer=1\" OnClick=\"return DelCheck(this, '"._DELETE." &quot;".cutstr(text_filter(bb_decode($com_text, $com_modul)), 10)."&quot;?');\" title=\""._ONDELETE."\">"._ONDELETE."</a>");
				} else {
					$edit = add_menu("com".$com_id, "<a href=\"#\" OnClick=\"LoadGet('1', 'com".$com_id."', '6', 'editcom', '".$com_id."', '', '1', '".$com_modul."', ''); return false;\" OnDblClick=\"LoadGet('1', 'com".$com_id."', '6', 'editcom', '".$com_id."', '', '1', '".$com_modul."', ''); return false;\" title=\""._ONEDIT."\">"._ONEDIT."</a>||<a href=\"".$admin_file.".php?op=comm_edit&id=".$com_id."\" title=\""._FULLEDIT."\">"._FULLEDIT."</a>||<a href=\"".$admin_file.".php?op=comm_del&id=$com_id\" OnClick=\"return DelCheck(this, '"._DELETE." &quot;".cutstr(text_filter(bb_decode($com_text, $com_modul)), 10)."&quot;?');\" title=\""._ONDELETE."\">"._ONDELETE."</a>");
				}
			} else {
				$stime = $com_date + $confc['edit'];
				$edit = (is_user() && $user_id == intval($user[0]) && time() < $stime) ? add_menu("com".$com_id, "<a href=\"#\" OnClick=\"LoadGet('1', 'com".$com_id."', '6', 'editcom', '".$com_id."', '', '1', '".$com_modul."', ''); return false;\" OnDblClick=\"LoadGet('1', 'com".$com_id."', '6', 'editcom', '".$com_id."', '', '1', '".$com_modul."', ''); return false;\" title=\""._ONEDIT."\">"._ONEDIT."</a>") : "";
			}
			$hclass = (!$com_status) ? "class=\"hidden\" title=\""._PCLOSED."\"" : "";
			$text = "<div id=\"repcom".$com_id."\">".search_color(bb_decode($com_text, $com_modul), $word)."</div>";
			if (defined("ADMIN_FILE")) {
				$checkb = (!$b) ? "| "._CHECKALL." <input type=\"checkbox\" name=\"markcheck\" onclick=\"MarkAll('".$bnum."', 'chk_', document.comm.markcheck.checked)\"> | <input type=\"checkbox\" name=\"id[]\" id=\"chk_".$b."\" value=\"".$com_id."\">" : "| <input type=\"checkbox\" name=\"id[]\" id=\"chk_".$b."\" value=\"".$com_id."\">";
				$b++;
			} else {
				$checkb = "";
			}
			comment($com_id, $poster, $date, $ip, $amess, $avatar, $rank, $rlink, $rate, $rwarn, $group, $point, $regdate, $gender, $from, $text, bb_decode($sig, $com_modul), $personal, $down, $up, $privat, $profil, $web, $warn, $thank, $edit, $hclass, $checkb);
			if ($confc['sort']) { $a++; } else { $a--; }
		}
		close();
		if (defined("ADMIN_FILE")) {
			$selms = "<select name=\"op\"><option value=\"comm_act\">"._ACTIVATE."</option><option value=\"comm_del\">"._DELETE."</option></select> <input type=\"hidden\" name=\"refer\" value=\"1\" class=\"fbutton\"><input type=\"submit\" value=\""._OK."\" class=\"fbutton\"></form>";
			templ("cmod", _CHECKOP.":", $selms);
			$pag = "op=comm_show";
		} else {
			$pag = isset($_GET['pag']) ? "op=view&id=".$cid."&pag=".intval($_GET['pag']) : "op=view&id=".$cid;
		}
		num_page($com_modul, $numstories, $numpages, $ccnum, $pag."&", $plnum);
	} else {
		$winfo = (defined("ADMIN_FILE")) ? _NO_INFO : _NOCOMMENTS;
		warning($winfo, "", "", 2);
	}
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

# Save edit comments
function editcom() {
	global $prefix, $db, $user, $confc;
	$id = (isset($_POST['id'])) ? ((isset($_POST['id'])) ? intval($_POST['id']) : "") : ((isset($_GET['id'])) ? intval($_GET['id']) : "");
	$typ = (isset($_POST['typ'])) ? ((isset($_POST['typ'])) ? intval($_POST['typ']) : "") : ((isset($_GET['typ'])) ? intval($_GET['typ']) : "");
	$mod = (isset($_POST['mod'])) ? ((isset($_POST['mod'])) ? analyze($_POST['mod']) : "") : ((isset($_GET['mod'])) ? analyze($_GET['mod']) : "");
	$text = (isset($_POST['text'])) ? ((isset($_POST['text'])) ? $_POST['text'] : "") : ((isset($_GET['text'])) ? $_GET['text'] : "");
	list($uid, $date, $comment) = $db->sql_fetchrow($db->sql_query("SELECT uid, UNIX_TIMESTAMP(date) as format, comment FROM ".$prefix."_comment WHERE id='$id'"));
	$stime = $date + $confc['edit'];
	if (is_moder($mod) || (is_user() && $uid == intval($user[0]) && time() < $stime)) {
		if ($id && $mod && !$text) {
			$content = ($typ) ? textareae("com".$id, "6", "editcom", $id, "0", "0", $mod, $comment, "10") : bb_decode($comment, $mod);
			echo $content;
		} elseif ($id && $mod && $text) {
			$comment = iconv("utf-8", _CHARSET, $text);
			$checks = str_replace(array("\n", "\r", "\t"), " ", $comment);
			$e = explode(" ", $checks);
			for ($a = 0; $a < sizeof($e); $a++) $o = strlen($e[$a]);
			$stop = "";
			if ($comment == "") $stop = _CERROR1;
			if ($o > $confc['letter']) $stop = _CERROR2;
			if (!$stop) {
				$comm = save_text($comment);
				$db->sql_query("UPDATE ".$prefix."_comment SET comment='$comm' WHERE id='$id'");
				echo bb_decode($comm, $mod);
			} else {
				return warning($stop, "", "", 1);
			}
		}
	} else {
		$info = sprintf(_PEDEND, intval($confc['edit'] / 60));
		return warning($info, "", "", 1);
	}
}

function button(){
include("config/config_filling.php");
$i=0;
if($confbb){$button .="
<script language=\"JavaScript\" type=\"text/javascript\" src=\"ajax/insert_code.js\"></script>
<div class=\"right\">"._FILLING.":</div><div class=\"center\">";
foreach ($confbb as $val) {
if ($val != "") {
preg_match("#(.*)\|(.*)\|(.*)#i",$val, $out);
$button .="<input class=\"fbutton\" type=\"button\" onclick='AddSmile(\"".str_replace("<br>","\\n",$out[3])."\")' value=\"$out[2]\">&#160;";
if($i==7)$button .="<br><p>";
if($i==16)$button .="<br><p>";
$i++;
}}$button .="</div>";
return $button;
}}

# Format statistic image
function create_stat() {
	global $conf;
	include("config/config_stat.php");
	$arg = func_get_args();
	$report = ($arg[0]) ? intval($arg[0]) : ((isset($_GET['report'])) ? intval($_GET['report']) : 0);
	$mday = ($arg[1]) ? intval($arg[1]) : ((isset($_GET['day'])) ? intval($_GET['day']) : "15");
	$file = ($arg[2]) ? text_filter($arg[2]) : ((isset($_GET['file'])) ? text_filter($_GET['file']) : "");
	$off = 1;

	if (!$report) header("Content-type: image/png");
	$image = imagecreate(750, 340);
	$white = imagecolorallocate($image, 255, 255, 255);
	$red = imagecolorallocate($image, 255, 0, 0);
	$green = imagecolorallocate($image, 0, 128, 0);
	$purple = imagecolorallocate($image, 200, 0, 200);

	$black = imagecolorallocate($image, 0, 0, 0);
	$wblue = imagecolorallocate($image, 67, 116, 223);
	$wwblue = imagecolorallocate($image, 63, 158, 255);
	$gray = imagecolorallocate($image, 200, 200, 200);
	$lblue = imagecolorallocate($image, 153, 204, 255);
	$llgray = imagecolorallocate($image, 250, 250, 250);

	imagefilledrectangle($image, 0, 252, 750, 340, $llgray);

	$f = array();
	if ($report) {
		$f = (file_exists("config/counter/days.txt")) ? @file("config/counter/days.txt") : @file("config/counter/stat.txt");
	} else {
		if ($file) {
			$f = file("config/counter/stat/".$file);
		} else {
			if (file_exists("config/counter/days.txt")) {
				$f = file("config/counter/days.txt");
				$f = array_merge($f, file("config/counter/stat.txt"));
			} else {
				$f = file("config/counter/stat.txt");
			}
		}
	}
	$to = count($f);
	if ($mday > 15) {
		$from = 0;
		$to = 15;
	} else {
		$from = (!$file && date("d") <= 15) ? 0 : 15;
		if ($from < 0) $from = 0;
	}
	$unique = $today = $engines = $sites = $homepage = $auditory = $max1 = $max2 = 0;
	for($i = $from; $i < $to; $i++) {
		$day = explode("|", $f[$i]);
		if ($day[1] > $max1) $max1 = $day[1];
		if ($day[2] > $max2) $max2 = $day[2];
		$unique = $unique + $day[1];
		$today = $today + $day[2];
		$engines = $engines + $day[4];
		$sites = $sites + $day[5];
		$homepage = $homepage + $day[6];
		$auditory = $auditory + $day[1] - ($day[4] + $day[5]);
		if ($auditory < 0) $auditory = 0;
		$regusers = $regusers + $day[7];
	}
	$i = 0;
	for($z = $from; $z < $to; $z++) {
		$day = explode("|", $f[$z]);
		if ($day[2] != "") {
			$w = @round((230/$max2) * $day[2]);
			if ($w < 4) $w = 4;
			$off = 134;
			imagefilledrectangle($image, $off+$confst['bet']*$i+1, 250-$w+1, $off+$confst['bet']*$i+$confst['shi'], 249, $lblue);
			imagerectangle($image, $off+$confst['bet']*$i, 250-$w, $off+$confst['bet']*$i+$confst['shi'], 249, $black);
			imagerectangle($image, $off+$confst['bet']*$i+$confst['shi']+1, 250-$w+3, $off+$confst['bet']*$i+$confst['shi']+2, 249, $gray);
			$w = @round((230/$max1) * $day[1]);
			if ($w < 5) $w = 1;
			$off = 120;

			imagefilledrectangle($image, $off+$confst['bet']*$i+1, 250-$w+1, $off+$confst['bet']*$i+$confst['shi']+3, 249, $wblue);
			imagerectangle($image, $off+$confst['bet']*$i,250-$w, $off+$confst['bet']*$i+$confst['shi']+3, 249, $black);
			imagerectangle($image, $off+$confst['bet']*$i+$confst['shi']+4, 250-$w+4, $off+$confst['bet']*$i+$confst['shi']+5, 249, $black);
			$zzz = $day[1]-$day[4]-$day[5];
			$w = @round((230/$max1)*$zzz);
			if ($w < 4) $w = $w+31;

			imagefilledrectangle($image, $off+$confst['bet']*$i+1, 250-$w+1, $off+$confst['bet']*$i+$confst['shi']+3, 249, $wwblue);
			imagerectangle($image, $off+$confst['bet']*$i, 250-$w, $off+$confst['bet']*$i+$confst['shi']+3, 249, $black);
			imagestring($image, 1, $off+$confst['bet']*$i+2, 250-$w+1-10, $day[1], $white);

			$d = explode(".", $day[0]);
			$d = $d[0] . "." . $d[1];

			imagestring($image, 1, $off+$confst['bet']*$i+1, 255, $d, $wblue);
			imagestring($image, 1, $off+$confst['bet']*$i+1, 265, $day[1], $red);
			imagestring($image, 1, $off+$confst['bet']*$i+1, 275, $day[2], $green);
			imagestring($image, 1, $off+$confst['bet']*$i+1, 285, $day[6], $purple);

			imagestring($image, 1, $off+$confst['bet']*$i+1, 300, $day[5], $wblue);
			imagestring($image, 1, $off+$confst['bet']*$i+1, 310, $day[4], $red);
			imagestring($image, 1, $off+$confst['bet']*$i+1, 320, $zzz, $green);
			imagestring($image, 1, $off+$confst['bet']*$i+1, 330, rtrim($day[7]), $purple);

			imagestring($image, 1, 3, 255, "DATE:", $wblue);
			imagestring($image, 1, 3, 265, "UNIQUE VISITORS:", $red);
			imagestring($image, 1, 3, 275, "SITE HITS:", $green);
			imagestring($image, 1, 3, 285, "HOMEPAGE HITS:", $purple);

			imagestring($image, 1, 3, 300, "OTHER SITES:", $wblue);
			imagestring($image, 1, 3, 310, "SEARCH ENGINES:", $red);
			imagestring($image, 1, 3, 320, "AUDIENCE:", $green);
			imagestring($image, 1, 3, 330, "REGISTERED USERS:", $purple);
		}
		$i++;
	}

	imagefilledrectangle($image, 5, 170, 20, 180, $wblue);
	imagerectangle($image, 5, 170, 20, 180, $black);
	imagestring($image, 1, 25, 171, "UNIQUE VISITORS", $black);

	imagefilledrectangle($image, 5, 185, 20, 195, $wwblue);
	imagerectangle($image, 5, 185, 20, 195, $black);
	imagestring($image, 1, 25, 186, "SITE AUDIENCE", $black);

	imagefilledrectangle($image, 5, 200, 20, 210, $lblue);
	imagerectangle($image, 5, 200, 20, 210, $black);
	imagestring($image, 1, 25, 202, "SITE HITS", $black);

	imagerectangle($image, 0, 296, 749, 339, $lblue);
	imagerectangle($image, 0, 252, 750, 252, $lblue);
	imagerectangle($image, 0, 0, 749, 339, $lblue);

	imagestring($image, 1, 5, 5, "VISITS BY DAYS FOR ".strtoupper($conf['homeurl'])." BY ANTISLAED CMS ".$conf['version']." | ARTGLOBALS.COM - ".format_time(date("Y-m-d H:i:s"), _TIMESTRING), $wblue);

	imagestring($image, 1, 5, 30, "UNIQUES TOTAL: ".$unique, $red);
	imagestring($image, 1, 5, 40, "HITS TOTAL: ".$today, $green);
	imagestring($image, 1, 5, 50, "HOMEPAGE HITS: ".$homepage, $purple);

	imagestring($image, 1, 5, 70, "OTHER SITES: ".$sites, $wblue);
	imagestring($image, 1, 5, 80, "SEARCH ENGINES: ".$engines, $red);
	imagestring($image, 1, 5, 90, "AUDIENCE: ".$auditory, $green);
	imagestring($image, 1, 5, 100, "REG. USERS: ".$regusers, $purple);

	imagestring($image, 1, 5, 120, "PAGES PER VIS.: ".round($today/$unique, 2), $wblue);
	imagestring($image, 1, 5, 130, "AVR. AUDIENCE: ".round($auditory/$i), $wblue);

	if ($report) {
		imagepng($image, "config/counter/stat/".date("m-Y").".png");
	} else {
		imagepng($image);
	}
	imagedestroy($image);
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

# Format function fputcsv for PHP 4
if (!function_exists('fputcsv')) {
	function fputcsv(&$handle, $fields = array(), $delimiter = ',', $enclosure = '"') {
		$str = '';
		$escape_char = '\\';
		foreach ($fields as $value) {
			if (strpos($value, $delimiter) !== false || strpos($value, $enclosure) !== false || strpos($value, "\n") !== false || strpos($value, "\r") !== false || strpos($value, "\t") !== false || strpos($value, ' ') !== false) {
				$str2 = $enclosure;
				$escaped = 0;
				$len = strlen($value);
				for ($i=0; $i < $len; $i++) {
					if ($value[$i] == $escape_char) {
						$escaped = 1;
					} else if (!$escaped && $value[$i] == $enclosure) {
						$str2 .= $enclosure;
					} else {
						$escaped = 0;
					}
					$str2 .= $value[$i];
				}
				$str2 .= $enclosure;
				$str .= $str2.$delimiter;
			} else {
				$str .= $value.$delimiter;
			}
		}
		$str = substr($str,0,-1);
		$str .= "\n";
		return fwrite($handle, $str);
	}
}

function thanks($mod,$id){
global $user;
echo <<<HTML
<script type="text/javascript">
function Thanks ( news_id,mod){
var ajax = new sack();
var varsString = "";
ajax.setVar("news_id", news_id);
ajax.setVar("mod", mod);
ajax.requestFile = "thanks.php";
ajax.method = 'GET';
ajax.element = 'thanks-layer';
ajax.runAJAX();
}
</script>
HTML;
echo"<div id=\"thanks-layer\" style=\"padding-bottom:3px\"><div style=\"font:11px Tahoma, Verdana; color:#666;\">Отблагодарили: ";
if(file_exists("uploads/$mod-thanks.dat")){
$array = unserialize(file_get_contents("uploads/$mod-thanks.dat"));
$i=0;
$thanks = explode(',', $array[$id]);
foreach($thanks as $key => $val){
$zap = ($i==0) ? "" : ",";
echo " $zap<a href=\"index.php?name=account&op=info&uname=$val\">$val</a>";
$i++;}if(in_array($user[1],$thanks))$status=1;}
echo"</div>";
if(is_user() && $status!=1){echo"<button type=\"button\" value=\"Спасибо\" onclick=\"Thanks('".$id."','".$mod."'); return false;\"/>Спасибо</button>";}
echo"</div>";
}


# Format captcha random
function captcha_random($id='') {
	global $conf;
	if ((extension_loaded('gd') && $id == 2) || (extension_loaded('gd') && !is_user())) {
		$content = '<div class="left">'._SECURITYCODE.':</div><div class="center"><img src="captcha.php" onclick="if(!this.adress)this.adress = this.src; this.src=adress+\'?rand=\'+Math.random();" border="1" title="Нажмите, чтобы обновить картинку" style="cursor:pointer;" alt="'._SECURITYCODE.'"></div>'
		.'<div class="left">'._TYPESECCODE.':</div><div class="center"><input type="text" name="check" size="10" maxlength="6" style="width: 75px;" class="'.$conf['style'].'"></div>';
	} else {
		$content = "<input type=\"hidden\" name=\"check\" value=\"0\">";
	}
		return $content;
}

# Format captcha check
function captcha_check($id='') {
	global $conf;
	if (($id == 2) || ($id == 1 && !is_user()) || ($_POST['posttype'] == 'save' && !is_user())) {
		$code = $_SESSION['captcha'];
		unset($_SESSION['captcha']);
		if (extension_loaded('gd') && $code != $_POST['check'] || !$_POST['check']) {
			return 1;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}
?>