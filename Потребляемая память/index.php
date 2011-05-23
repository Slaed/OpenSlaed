<?php
# Copyright © 2005 - 2009 SLAED
# Website: http://www.slaed.net

define("MODULE_FILE", true);
if (function_exists('memory_get_usage')) $start_memory = memory_get_usage();
$start_time = array_sum(explode(" ", microtime()));
include("function/function.php");
$name = (isset($_POST['name'])) ? ((isset($_POST['name'])) ? analyze($_POST['name']) : "") : ((isset($_GET['name'])) ? analyze($_GET['name']) : "");
$op = (isset($_POST['op'])) ? ((isset($_POST['op'])) ? analyze($_POST['op']) : "") : ((isset($_GET['op'])) ? analyze($_GET['op']) : "");
$file = (isset($_POST['file'])) ? ((isset($_POST['file'])) ? analyze($_POST['file']) : "") : ((isset($_GET['file'])) ? analyze($_GET['file']) : "");
$file = ($file) ? $file : "index";
if ($name) {
	$conf['name'] = $name;
	$conf['style'] = strtolower($name);
	$module = 1;
	list($mod_active, $view, $blocks, $blocks_c) = $db->sql_fetchrow($db->sql_query("SELECT active, view, blocks, blocks_c FROM ".$prefix."_modules WHERE title='$name'"));
	if (intval($mod_active) || is_moder($name)) {
		$ThemeSel = get_theme();
		if ($view == 0 && file_exists("modules/".$name."/".$file.".php")) {
			include("modules/".$name."/".$file.".php");
		} else if (($view == 1 && (is_user() && is_group($name)) || is_moder($name)) && file_exists("modules/".$name."/".$file.".php")) {
			include("modules/".$name."/".$file.".php");
		} elseif ($view == 1 && !is_moder($name)) {
			$pagetitle = $conf['defis']." "._ACCESSDENIED;
			head();
			title(""._ACCESSDENIED."");
			if (!is_user()) $infotext = ""._MODULEUSERS." ";
			list($gname) = $db->sql_fetchrow($db->sql_query("SELECT name FROM ".$prefix."_modules LEFT JOIN ".$prefix."_groups ON (mod_group=id) WHERE title='$name'"));
			if ($gname) $infotext .= _ADDITIONALYGRP.": ".$gname;
			warning($infotext, "?name=account&op=newuser", 15, 2);
			foot();
			exit;
		} else if ($view == 2 && is_moder($name) && file_exists("modules/".$name."/".$file.".php")) {
			include("modules/".$name."/".$file.".php");
		} elseif ($view == 2 && !is_moder($name)) {
			$pagetitle = "".$conf['defis']." "._ACCESSDENIED."";
			head();
			title(_ACCESSDENIED);
			warning(_MODULESADMINS, "", 5, 2);
			foot();
			exit;
		} else {
			header("Location: index.php");
			exit;
		}
	} else {
		header("Location: index.php");
		exit;
	}
} else {
	$home = 1;
	$name = $conf['module'];
	$conf['name'] = $name;
	$ThemeSel = get_theme();
	if (file_exists("modules/".$name."/".$file.".php")) {
		include("modules/".$name."/".$file.".php");
	} else {
		head();
		warning(_HOMEPROBLEMUSER, "", "", 1);
		foot();
	}
}
?>