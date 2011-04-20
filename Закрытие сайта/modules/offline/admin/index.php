<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_modul("offline")) die("Illegal File Access");

function offline_conf() {
	global $admin_file;
	head();
	panel();
	include("config/config_offline.php");
	$permtest = end_chmod("config/config_offline.php", 666);
	if ($permtest) warning($permtest, "", "", 1);
	open();
	echo "<h2>"._ADMIN_OFFLINE_1."</h2>"
	."<form name=\"post\" action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._ADMIN_OFFLINE_2."</div><div class=\"center\">".radio_form($offline['status'], "status")."</div>"
	."<div class=\"left\">"._ADMIN_OFFLINE_3."</div><div class=\"center\">".radio_form($offline['type'], "type")."</div>"
	."<div class=\"left\">"._ADMIN_OFFLINE_4.":</div><div class=\"center\"><input type='text' name='reason' value='".$offline['reason']."' maxlength='100' size='45' class=\"admin\"></div>"
	."<div class=\"left\">"._ADMIN_OFFLINE_5."</div><div class=\"center\"><input type='text' name='close' value='".$offline['close']."' maxlength='25' size='45' class=\"admin\">"._ADMIN_OFFLINE_8."</div>"
	."<div class=\"left\">"._ADMIN_OFFLINE_6."</div><div class=\"center\"><input type='text' name='open' value='".$offline['open']."' maxlength='25' size='45' class=\"admin\">"._ADMIN_OFFLINE_8."</div>"
	."<div class=\"left\">"._ADMIN_OFFLINE_7."</div><div class=\"center\">".radio_form($offline['autorun'], "autorun")."</div>"
	."<div class=\"button\"><input type='hidden' name='op' value='offline_conf_save'><input type='submit' value='"._SAVECHANGES."' class=\"fbutton\"></div></form>";
	close();
	foot();
}

function offline_conf_save() {
	global $admin_file;
	$content = "\$offline['status'] = \"".intval($_POST['status'])."\";\n"
	."\$offline['type'] = \"".intval($_POST['type'])."\";\n"
	."\$offline['reason'] = \"".text_filter($_POST['reason'])."\";\n"
	."\$offline['autorun'] = \"".intval($_POST['autorun'])."\";\n"
	."\$offline['close'] = \"".$_POST['close']."\";\n"
	."\$offline['open'] = \"".$_POST['open']."\";\n";
	save_conf("config/config_offline.php", $content);
	Header("Location: ".$admin_file.".php?op=offline_conf");
}

switch($op) {
	case "offline_conf":
	offline_conf();
	break;
	
	case "offline_conf_save":
	offline_conf_save();
	break;
}
?>