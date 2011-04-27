<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

function ratings() {
	global $admin_file;
	head();
	panel();
	title(""._RATINGS."");
	include("config/config_ratings.php");
	$permtest = end_chmod("config/config_ratings.php", 666);
	if ($permtest) warning($permtest, "", "", 1);
	$mods = array("account", "files", "news");
	$lang = array(_ACCOUNT, _FILES, _NEWS);
	$i = 0;
	$content="<div class=\"left\">"._NEW_RATE_22."</div><div class=\"center\">".radio_form($nnewrate['useronly'], "useronly")."</div>";
	$content.="<div class=\"left\">"._NEW_RATE_24."</div><div class=\"center\">".radio_form($nnewrate['allowcom'], "allowcom")."</div>";
	$content.="<div class=\"left\">"._NEW_RATE_25."</div><div class=\"center\"><input type=\"text\" name=\"maxhistory\" value='".$nnewrate['maxhistory']."' maxlength=\"25\" size=\"45\" class=\"admin\"></div>";
	foreach ($mods as $val) {
		if ($val != "") {
			$con = explode("|", $confra[$val]);
			$content .= "<h2>"._MODUL.": ".$lang[$i]."</h2>"
			."<div class=\"left\">"._VOTING_TIME.":</div><div class=\"center\"><input type=\"text\" name=\"time[]\" value='".intval($con[0] / 86400)."' maxlength=\"25\" size=\"45\" class=\"admin\"></div>"
			."<div class=\"left\">"._C_21."</div><div class=\"center\">".radio_form($con[1], "".$i."in")."</div>"
			."<div class=\"left\">"._NEW_RATE_9."</div><div class=\"center\">".radio_form($con[3], "".$i."type")."</div>"
			."<div class=\"left\">"._NEW_RATE_10."</div><div class=\"center\">".radio_form($con[4], $i."useronly")."</div>"
			."<div class=\"left\">"._C_22."</div><div class=\"center\">".radio_form($con[2], "".$i."view")."</div>";
			$i++;
		}
	}
	open();
	echo "<form action=\"".$admin_file.".php\" method=\"post\">".$content.""
	."<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"ratings_save_conf\"><input type=\"submit\" value=\""._SAVECHANGES."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function ratings_save_conf() {
	global $admin_file;
	include("config/config_ratings.php");
	$content = "\$confra = array();\n";
	$mods = array("account", "files", "news");
	$i = 0;
	$content = "\$nnewrate['useronly'] = ".intval($_POST['useronly']).";\n";
	$content .= "\$nnewrate['allowcom'] = ".intval($_POST['allowcom']).";\n";
	$content .= "\$nnewrate['maxhistory'] = ".intval($_POST['maxhistory']).";\n";
	foreach ($mods as $val) {
		if ($val != "") {
			$xtime = (!intval($_POST['time'][$i])) ? 2592000 : $_POST['time'][$i] * 86400;
			$content .= "\$confra['$val'] = \"".$xtime."|".$_POST[''.$i.'in']."|".$_POST[''.$i.'view']."|".$_POST[$i.'type']."|".$_POST[$i.'useronly']."\";\n";
			$i++;
		}
	}
	save_conf("config/config_ratings.php", $content);
	Header("Location: ".$admin_file.".php?op=ratings");
}

switch($op) {
	case "ratings":
	ratings();
	break;
	
	case "ratings_save_conf":
	ratings_save_conf();
	break;
}
?>