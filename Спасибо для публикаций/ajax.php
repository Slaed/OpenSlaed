<?php
# Copyright © 2005 - 2009 SLAED
# Website: http://www.slaed.net

define("MODULE_FILE", true);
include("function/function.php");
$host = (getenv("HTTP_HOST")) ? getenv("HTTP_HOST") : getenv("SERVER_NAME");
if (!intval($_FILES['Filedata']['size']) && !stristr(getenv("HTTP_REFERER"), $host)) die("Illegal File Access");
$go = (isset($_GET['go'])) ? intval($_GET['go']) : 0;
$op = (isset($_GET['op'])) ? $_GET['op'] : 0;
$mod = (isset($_GET['mod'])) ? strtolower($_GET['mod']) : 0;
if ($go == 1) {
	get_lang();
	$ThemeSel = get_theme();
	if (file_exists("templates/$ThemeSel/index.php")) {
		include("templates/$ThemeSel/index.php");
	} else {
		include("function/template.php");
	}
	include("function/no-cache.php");
	switch($op) {
		default:
		rating();
		break;
	}
} elseif ($go == 3) {
	get_lang();
	$ThemeSel = get_theme();
	if (file_exists("templates/$ThemeSel/index.php")) {
		include("templates/$ThemeSel/index.php");
	} else {
		include("function/template.php");
	}
	include("function/no-cache.php");
	switch($op) {
		default:
		show_files();
		break;
	}
} elseif ($go == 4) {
	include("function/no-cache.php");
	include("config/config_uploads.php");
	switch($go) {
		default:
		$con = explode("|", $confup[$mod]);
		upload(2, "uploads/".$mod."", $con[0], $con[2], $mod, $con[3], $con[4]);
		break;
	}
} elseif ($go == 5) {
	get_lang();
	$ThemeSel = get_theme();
	if (file_exists("templates/$ThemeSel/index.php")) {
		include("templates/$ThemeSel/index.php");
	} else {
		include("function/template.php");
	}
	include("function/no-cache.php");
	switch($op) {
		default:
		user_sainfo();
		break;
	}
} elseif ($go == 6) {
	get_lang();
	$ThemeSel = get_theme();
	if (file_exists("templates/$ThemeSel/index.php")) {
		include("templates/$ThemeSel/index.php");
	} else {
		include("function/template.php");
	}
	include("function/no-cache.php");
	switch($op) {
		default:
		user_sinfo();
		break;
	}
} elseif ($go == 7) {
	get_lang();
	$ThemeSel = get_theme();
	if (file_exists("templates/$ThemeSel/index.php")) {
		include("templates/$ThemeSel/index.php");
	} else {
		include("function/template.php");
	}
	include("function/no-cache.php");
	switch($op) {
		default:
		get_user();
		break;
	}
}
?>