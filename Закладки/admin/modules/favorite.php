<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

function favorites() {
global $admin_file;
head();
panel();
title(_NEW_FAV_38);
include("config/config_favorites.php");
$permtest = end_chmod("config/config_favorites.php", 666);
if ($permtest) warning($permtest, "", "", 1);
$mods = array("files", "news");
$lang = array(_FILES, _NEWS);
$i = 0;
foreach ($mods as $val) {
if ($val != "") {
$con = explode("|", $confra[$val]);
$content .= "<h2>"._MODUL.": ".$lang[$i]."</h2>"
."<div class='left'>"._NEW_FAV_39."</div><div class='center'>".radio_form($fav['mods'][$val]['active'], $i."active")."</div>"
."<div class='left'>"._NEW_FAV_40."</div><div class='center'>".radio_form($fav['mods'][$val]['cut'], $i."cut")."</div>";
$i++;
}
}
open();
echo "<form action='".$admin_file.".php' method='post'>"
."<div class='left'>"._NEW_FAV_41."</div><div class='center'>".radio_form($fav['panel'],"panel")."</div>"
."<div class='left'>"._NEW_FAV_42."</div><div class='center'><input type='text' name='max' value='".$fav['max']."' maxlength='25' size='45' class='admin'></div>"
."<div class='left'>"._NEW_FAV_43."</div><div class='center'><input type='text' name='count' value='".$fav['num']."' maxlength='25' size='45' class='admin'></div>"
.$content
."<div class='button'><input type='hidden' name='op' value='favorites_save_conf'><input type='submit' value='"._SAVECHANGES."' class='fbutton'></div></form>";
close();
foot();
}

function favorites_save_conf() {
global $admin_file;
include("config/config_favorites.php");
$content = "\$fav['num']=".intval($_POST['count']).";\n";
$content .= "\$fav['max']=".intval($_POST['max']).";\n";
$content .= "\$fav['panel']=".intval($_POST['panel']).";\n";
$mods = array("files", "news");
$i = 0;
foreach ($mods as $val) {
if ($val != "") {
$xtime = (!intval($_POST['time'][$i])) ? 2592000 : $_POST['time'][$i] * 86400;
$content .= "\$fav['mods']['$val'] = array('active'=>".intval($_POST[$i.'active']).",'cut'=>".intval($_POST[$i.'cut']).");\n";
$i++;
}
}
save_conf("config/config_favorites.php", $content);
Header("Location: ".$admin_file.".php?op=favorites");
}

switch($op) {
	case "favorites":
	favorites();
	break;
	
	case "favorites_save_conf":
	favorites_save_conf();
	break;
}
?>