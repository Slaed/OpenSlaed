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

if (!defined("ADMIN_FILE")) die("Illegal File Access");

function admininfo() {
	global $prefix, $db, $admin_file, $conf, $panel;
	if (is_admin()) {
		$panel = (isset($_GET['panel'])) ? $_GET['panel'] : $panel;
		if ($panel) {
			$w_content = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
			if (is_active("account") && is_admin_modul("account")) {
				$num = $db->sql_numrows($db->sql_query("SELECT user_id FROM ".$prefix."_users_temp WHERE user_id"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=user_new\">"._NEW_USER."</a>:</td><td>$num</td></tr>";
			}
			if (is_active("album") && is_admin_modul("album")) {
				$num = $db->sql_numrows($db->sql_query("SELECT pid FROM ".$prefix."_album_pictures_newpicture WHERE pid"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=album&do=validnew&type=checknew\">"._NEWALBUM."</a>:</td><td>$num</td></tr>";
			}
			if (is_active("faq") && is_admin_modul("faq")) {
				$num = $db->sql_numrows($db->sql_query("SELECT fid FROM ".$prefix."_faq WHERE status='0'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=faq&status=1\">"._NEWQ."</a>:</td><td>$num </td></tr>";
			}
			if (is_active("files") && is_admin_modul("files")) {
				$num = $db->sql_numrows($db->sql_query("SELECT lid FROM ".$prefix."_files WHERE status='0'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=files&status=1\">"._NEWFILES."</a>:</td><td>$num</td></tr>";
				$num = $db->sql_numrows($db->sql_query("SELECT lid FROM ".$prefix."_files WHERE status='2'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=files&status=2\">"._BROCFILES."</a>:</td><td>$num</td></tr>";
			}
			if (is_active("help") && is_admin_modul("help")) {
				$num = $db->sql_numrows($db->sql_query("SELECT sid FROM ".$prefix."_help WHERE pid='0' AND status='0'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=help\">"._NEWHELPS."</a>:</td><td>$num</td></tr>";
			}
			if (is_active("jokes") && is_admin_modul("jokes")) {
				$num = $db->sql_numrows($db->sql_query("SELECT jokeid FROM ".$prefix."_jokes WHERE status='0'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=jokes&status=1\">"._NEWJOKES."</a>:</td><td>$num</td></tr>";
			}
			if (is_active("links") && is_admin_modul("links")) {
				$num = $db->sql_numrows($db->sql_query("SELECT lid FROM ".$prefix."_links WHERE status='0'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=links&status=1\">"._NEWLINKS."</a>:</td><td>$num</td></tr>";
				$num = $db->sql_numrows($db->sql_query("SELECT lid FROM ".$prefix."_links WHERE status='2'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=links&status=2\">"._BROCLINKS."</a>:</td><td>$num</td></tr>";
			}
			if (is_active("media") && is_admin_modul("media")) {
				$num = $db->sql_numrows($db->sql_query("SELECT id FROM ".$prefix."_media WHERE status='0'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=media&status=1\">"._NEWMEDIA."</a>:</td><td>$num</td></tr>";
				$num = $db->sql_numrows($db->sql_query("SELECT id FROM ".$prefix."_media WHERE status='2'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=media&status=2\">"._BROCFILES."</a>:</td><td>$num</td></tr>";
			}
			if (is_active("news") && is_admin_modul("news")) {
				$num = $db->sql_numrows($db->sql_query("SELECT sid FROM ".$prefix."_stories WHERE status='0'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=news&status=1\">"._NEWNEWS."</a>:</td><td>$num</td></tr>";
			}
			if (is_active("pages") && is_admin_modul("pages")) {
				$num = $db->sql_numrows($db->sql_query("SELECT pid FROM ".$prefix."_page WHERE status='0'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=page&status=1\">"._NEWPAGES."</a>:</td><td>$num</td></tr>";
			}
			if (is_active("shop") && is_admin_modul("shop")) {
				$num = $db->sql_numrows($db->sql_query("SELECT client_id FROM ".$prefix."_clients WHERE client_active='2'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=shop_new_clients\">"._NEW_CLIENTS."</a>:</td><td>$num</td></tr>";
				$num = $db->sql_numrows($db->sql_query("SELECT partner_id FROM ".$prefix."_partners WHERE partner_active='2'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=shop_new_clients\">"._NEW_PARTNERS."</a>:</td><td>$num</td></tr>";
			}
			if (is_active("whois") && is_admin_modul("whois")) {
				$num = $db->sql_numrows($db->sql_query("SELECT id FROM ".$prefix."_whois WHERE status='0'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=whois&status=1\">"._NEWWHOIS."</a>:</td><td>$num</td></tr>";
			}
			if (is_active("private") && is_admin_modul("private")) {
				$num6 = $db->sql_numrows($db->sql_query("SELECT id FROM ".$prefix."_private_complaint WHERE status='0'"));
				$w_content .= "<tr><td><a href=\"".$admin_file.".php?op=privates\">Поступ. жалобы</a>: </td><td>$num6</td></tr>";
			}
			$w_content .= "</table>";
			themesidebox(_WAITINGCONT, $w_content, 3);
			if ($conf['sblock'] && is_admin_god()) {
				include("config/config_stat.php");
				$phpver = phpversion();
				$osver = php_uname('s');
				$gdver = php_gd();
				$dbver = db_version();
				$phpver = ($phpver >= "4.3.0") ? "<font color=\"green\">$phpver</font>" : "<font color=\"red\">$phpver</font>";
				$gdver = ($gdver >= "2.0") ? "<font color=\"green\">$gdver</font>" : "<font color=\"red\">$gdver</font>";
				$dbverv = ($dbver >= "4.0.0") ? "<font color=\"green\">".cutstr($dbver, 5)."</font>" : "<font color=\"red\">".cutstr($dbver, 5)."</font>";
				$globals = (ini_get('register_globals') == 1) ? "<font color=\"red\">On</font>" : "<font color=\"green\">Off</font>";
				$safe_mode = (ini_get('safe_mode') == 1) ? "<font color=\"green\">On</font>" : "<font color=\"red\">Off</font>";
				$magic_quotes = (ini_get('magic_quotes_gpc') == 1) ? "<font color=\"green\">On</font>" : "<font color=\"red\">Off</font>";
				$p_max = files_size(str_replace("M", "", ini_get('post_max_size')) * 1024 * 1024);
				$u_max = files_size(str_replace("M", "", ini_get('upload_max_filesize')) * 1024 * 1024);
				$m_max = files_size(str_replace("M", "", ini_get('memory_limit')) * 1024 * 1024);
				$mod_rewrite = (function_exists('apache_get_modules')) ? ((array_search("mod_rewrite", apache_get_modules())) ? "<font color=\"green\">On</font>" : "<font color=\"red\">Off</font>") : "<font color=\"red\">Off</font>";
				$gzip = (function_exists('gzopen')) ? "<font color=\"green\">On</font>" : "<font color=\"red\">Off</font>";
				$bzip = (function_exists('bzopen')) ? "<font color=\"green\">On</font>" : "<font color=\"red\">Off</font>";
				$close = (!$conf['close']) ? "<font color=\"green\">On</font>" : "<font color=\"red\">Off</font>";
				$stat = ($confst['stat']) ? "<font color=\"green\">On</font>" : "<font color=\"red\">Off</font>";
				$refer = ($conf['refer']) ? "<font color=\"green\">On</font>" : "<font color=\"red\">Off</font>";
				$newsle = ($conf['newsletter']) ? "<font color=\"green\">On</font>" : "<font color=\"red\">Off</font>";
				$s_content = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">"
				."<tr><td>"._SCLOSE.":</td><td>".$close."</td></tr>"
				."<tr><td>"._STAT.":</td><td>".$stat."</td></tr>"
				."<tr><td>"._REFERERS.":</td><td>".$refer."</td></tr>"
				."<tr><td>"._NEWSLETTER.":</td><td>".$newsle."</td></tr>"
				."<tr><td colspan=\"2\"><hr></td></tr>"
				."<tr><td>ANTISLAED CMS:</td><td title=\"".$conf['version']."\"><font color=\"blue\">".cutstr($conf['version'], 5)."</font></td></tr>"
				."<tr><td>OS:</td><td title=\"".$osver."\"><font color=\"blue\">".cutstr($osver, 5)."</font></td></tr>"
				."<tr><td>PHP:</td><td>".$phpver."</td></tr>"
				."<tr><td>PHP GD:</td><td>".$gdver."</td></tr>"
				."<tr><td>MySQL:</td><td title=\"".$dbver."\">".$dbverv."</td></tr>"
				."<tr><td>Post size:</td><td><font color=\"blue\">".$p_max."</font></td></tr>"
				."<tr><td>Upload file size:</td><td><font color=\"blue\">".$u_max."</font></td></tr>"
				."<tr><td>Memory limit:</td><td><font color=\"blue\">".$m_max."</font></td></tr>"
				."<tr><td>Execution time:</td><td><font color=\"blue\">".ini_get('max_execution_time')." "._SEC.".</font></td></tr>"
				."<tr><td>Mod Rewrite:</td><td>".$mod_rewrite."</td></tr>"
				."<tr><td>GZip compression:</td><td>".$gzip."</td></tr>"
				."<tr><td>BZip2 compression:</td><td>".$bzip."</td></tr>"
				."<tr><td>Register globals:</td><td>".$globals."</td></tr>"
				."<tr><td>Safe mode:</td><td>".$safe_mode."</td></tr>"
				."<tr><td>Magic quotes gpc:</td><td>".$magic_quotes."</td></tr>"
				."</table>";
				themesidebox(_SYSTEM_INFO, $s_content, 4);
			}
		}
	}
}

function php_gd() {
	ob_start();
	phpinfo(8);
	$module_info = ob_get_contents();
	ob_end_clean();
	if (preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches)) {
		$gdversion = $matches[1];
	} else {
		$gdversion = 0;
	}
	return $gdversion;
}

function db_version() {
	global $db;
	list($dbversion) = $db->sql_fetchrow($db->sql_query("SELECT VERSION()"));
	return $dbversion;
}

function end_chmod($dir, $chm) {
	if (file_exists($dir) && intval($chm)) {
		#if (php_uname('s') == "Linux" && $chm == "666") chmod($dir, "0".$chm);
		$pdir = decoct(fileperms($dir));
		$per = substr($pdir, -3);
		if ($per != $chm) return $dir." "._ERRORPERM." CHMOD - ".$chm;
	}
}

function save_conf($fp, $content, $type="") {
	if (file_exists($fp) && $content) {
		$fp = fopen($fp, "wb");
		$content = (intval($type)) ? "<?php\nif (!defined(\"ADMIN_FILE\")) die(\"Illegal File Access\");\n\n".$content."\n?>" : "<?php\nif (!defined(\"FUNC_FILE\")) die(\"Illegal File Access\");\n\n".$content."\n?>";
		fwrite($fp, $content);
		fclose($fp);
	}
}

function ajax_cat() {
	global $prefix, $db, $admin_file, $conf;
	$arg = func_get_args();
	$modul = analyze($arg[0]);
	$obj = analyze($arg[1]);
	$where = ($modul) ? "WHERE a.modul='$modul'" : "";
	$result = $db->sql_query("SELECT a.id, a.modul, a.title, a.description, a.img, a.language, a.parentid, a.ordern,   b.id, b.modul, b.ordern,    c.id, c.modul, c.ordern FROM ".$prefix."_categories AS a LEFT JOIN ".$prefix."_categories AS b ON (b.modul = a.modul AND b.ordern = a.ordern-1) LEFT JOIN ".$prefix."_categories AS c ON (c.modul = a.modul AND c.ordern = a.ordern+1) ".$where." ORDER BY a.modul, a.ordern");
	if ($db->sql_numrows($result) > 0) {
		while (list($id, $modul, $title, $description, $imgcat, $language, $parentid, $ordern, $con1, $modul1, $order1, $con2, $modul2, $order2) = $db->sql_fetchrow($result)) {
			$massiv[$id] = array($id, $modul, $title, $description, $imgcat, $language, $parentid, $ordern, $con1, $modul1, $order1, $con2, $modul2, $order2);
			unset($id, $modul, $title, $description, $imgcat, $language, $parentid, $ordern, $con1, $modul1, $order1, $con2, $modul2, $order2);
		}
		$fcont = "";
		foreach ($massiv as $key => $val) {
			$id = $val[0];
			$modul = $val[1];
			$title = $val[2];
			$description = $val[3];
			$imgcat = $val[4];
			$language = $val[5];
			$parentid = $val[6];
			$ordern = $val[7];
			$con1 = $val[8];
			$modul1 = $val[9];
			$order1 = $val[10];
			$con2 = $val[11];
			$modul2 = $val[12];
			$order2 = $val[13];

			$ordernm = $ordern - 1;
			$ordernp = $ordern + 1;
			$active = ($parentid) ? "<font color=\"#009900\">"._YES."</font>" : "<font color=\"#FF0000\">"._NO."</font>";
			$img = ($imgcat) ? "<font color=\"#009900\">"._YES."</font>" : "<font color=\"#FF0000\">"._NO."</font>";

			$flag = $val[6];
			while ($flag != "") {
				$title = $massiv[$flag][2]." / ".$title;
				$flag = $massiv[$flag][6];
			}
			$fcont .= "<tr class=\"bgcolor1\"><td align=\"center\">".$id."</td>"
			."<td>".$title."</td>";
			if ($conf['multilingual'] == 1) {
				$language = (!$language) ? _ALL : $language;
				$fcont .= "<td align=\"center\">".ucfirst($language)."</td>";
			}
			$fcont .= "<td align=\"center\">".$active."</td>"
			."<td align=\"center\">".$img."</td>"
			."<td align=\"center\">$ordern</td><td align=\"center\">";
			$fcont .= ($con1) ? "<img src=\"".img_find("all/up")."\" border=\"0\" OnClick=\"LoadGet('', 'ajax_cat', '9', 'cat_order', '".$id."', '".$con1."', '".$ordernm."', '".$modul."', '".$ordern."'); return false;\" OnDblClick=\"LoadGet('', 'ajax_cat', '9', 'cat_order', '".$id."', '".$con1."', '".$ordernm."', '".$modul."', '".$ordern."'); return false;\" title=\""._BLOCKUP."\" alt=\""._BLOCKUP."\" style=\"cursor: pointer;\"> " : "";
			$fcont .= ($con2) ? "<img src=\"".img_find("all/down")."\" border=\"0\" OnClick=\"LoadGet('', 'ajax_cat', '9', 'cat_order', '".$id."', '".$con2."', '".$ordernp."', '".$modul."', '".$ordern."'); return false;\" OnDblClick=\"LoadGet('', 'ajax_cat', '9', 'cat_order', '".$id."', '".$con2."', '".$ordernp."', '".$modul."', '".$ordern."'); return false;\" title=\""._BLOCKDOWN."\" alt=\""._BLOCKDOWN."\" style=\"cursor: pointer;\">" : "";
			$fcont .= "</td>"
			."<td align=\"center\">".ad_edit($admin_file.".php?op=cat_edit&cid=".$id)." ".ad_delete($admin_file.".php?op=cat_del&id=".$id."&refer=1", $title)."</td></tr>";
		}
		$cont = "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"bgcolor4\"><tr><th>"._ID."</th><th width=\"50%\">"._CATEGORY."</th>";
		$cont .= ($conf['multilingual'] == 1) ? "<th>"._LANGUAGE."</th>" : "";
		$cont .= "<th>".cutstr(_SUBCATEGORY, 5)."</th><th>".cutstr(_IMG, 5)."</th><th colspan=\"2\">"._WEIGHT."</th><th>"._FUNCTIONS."</th></tr>".$fcont."</table>";
		if ($obj) { return $cont; } else { echo $cont; }
	} else {
		warning(_NO_INFO, "", "", 2);
	}
}

function cat_order() {
	global $prefix, $db, $admin_file;
	$modul = isset($_GET['mod']) ? analyze($_GET['mod']) : 0;
	if ($modul) {
		$typ = intval($_GET['typ']);
		$text = intval($_GET['text']);
		$id = intval($_GET['id']);
		$cid = intval($_GET['cid']);
		$db->sql_query("UPDATE ".$prefix."_categories SET ordern='$typ' WHERE id='$id'");
		$db->sql_query("UPDATE ".$prefix."_categories SET ordern='$text' WHERE id='$cid'");
	}
	return ajax_cat($modul, 0);
}

function catacess() {
	global $prefix, $db;
	$arg = func_get_args();
	$gids = explode("|", $arg[2]);
	$cont = "<select name=\"".$arg[0]."[]\" size=\"5\" multiple=\"multiple\" class=\"".$arg[1]."\">";
	if ($arg[3] < 1) {
		$cont .= "<option value=\"0|0\"";
		$cont .= ($arg[2] == "0|0") ? " selected" : "";
		$cont .= ">"._ALL."</option>";
	}
	if ($arg[3] < 2) {
		$cont .= "<option value=\"1|0\"";
		$cont .= ($arg[2] == "1|0") ? " selected" : "";
		$cont .= ">"._USERS."</option>";
		$where = "";
	} else {
		$where = "WHERE extra='1'";
	}
	$result = $db->sql_query("SELECT id, name, extra FROM ".$prefix."_groups ".$where." ORDER BY extra, points");
	while (list($id, $name, $extra) = $db->sql_fetchrow($result)) {
		if ($gids[0] == 2) {
			$massiv = explode(",", $gids[1]);
			foreach ($massiv as $val) {
				if ($val != "" && $val == $id) {
					$select = "selected";
					break;
				} else {
					$select = "";
				}
			}
		}
		$title = ($extra) ? _SPEC_GROUP." \"".$name."\"" : _GROUP." \"".$name."\"";
		$cont .= "<option value=\"2|".$id."\"$select>".$title."</option>";
	}
	$cont .= "<option value=\"3|0\"";
	$cont .= ($arg[2] == "3|0") ? " selected" : "";
	$cont .= ">"._ADMIN."</option></select>";
	return $cont;
}

function scatacess($auth) {
	$gids = explode("|", $auth);
	foreach ($auth as $val) {
		$gids = explode("|", $val);
		if ($gids[0] == 2) {
			$acess = "2";
			$select[] = $gids[1];
		} else {
			$acess = $gids[0];
			$select = array();
			$select[] = $gids[1];
			break;
		}
	}
	return $acess."|".implode(",", $select);
}

function ajax_block() {
	global $prefix, $db, $currentlang, $conf, $admin_file;
	$fcont = "";
	$result = $db->sql_query("SELECT a.bid, a.bkey, a.title, a.url, a.bposition, a.weight, a.active, a.blanguage, a.blockfile, a.view, a.expire, a.action, b.bid, b.bposition, b.weight, c.bid, c.bposition, c.weight FROM ".$prefix."_blocks AS a LEFT JOIN ".$prefix."_blocks AS b ON (b.bposition = a.bposition AND b.weight = a.weight-1) LEFT JOIN ".$prefix."_blocks AS c ON (c.bposition = a.bposition AND c.weight = a.weight+1) ORDER BY a.bposition, a.weight");
	while (list($bid, $bkey, $title, $url, $bposition, $weight, $active, $blanguage, $blockfile, $view, $expire, $action, $con1, $bposition1, $weight1, $con2, $bposition2, $weight2) = $db->sql_fetchrow($result)) {
		if (($expire && $expire < time()) || (!$active && $expire)) {
			if ($action == "d") {
				$db->sql_query("UPDATE ".$prefix."_blocks SET active='0', expire='0' WHERE bid='$bid'");
			} elseif ($action == "r") {
				$db->sql_query("DELETE FROM ".$prefix."_blocks WHERE bid='$bid'");
			}
		}
		$weight_minus = $weight - 1;
		$weight_plus = $weight + 1;
		$fcont .= "<tr class=\"bgcolor1\"><td align=\"center\">$bid</td><td>$title</td>";
		if ($bposition == "l") {
			$bposition = "<img src=\"".img_find("misc/left")."\" border=\"0\" alt=\""._LEFTBLOCK."\" title=\""._LEFTBLOCK."\"> "._LEFT;
		} elseif ($bposition == "r") {
			$bposition = _RIGHT." <img src=\"".img_find("misc/right")."\" border=\"0\" alt=\""._RIGHTBLOCK."\" title=\""._RIGHTBLOCK."\">";
		} elseif ($bposition == "c") {
			$bposition = "<img src=\"".img_find("misc/right")."\" border=\"0\" alt=\""._CENTERBLOCK."\" title=\""._CENTERBLOCK."\">&nbsp;"._CENTERUP."&nbsp;<img src=\"".img_find("misc/left")."\" border=\"0\" alt=\""._CENTERBLOCK."\" title=\""._CENTERBLOCK."\">";
		} elseif ($bposition == "d") {
			$bposition = "<img src=\"".img_find("misc/right")."\" border=\"0\" alt=\""._CENTERBLOCK."\" title=\""._CENTERBLOCK."\">&nbsp;"._CENTERDOWN."&nbsp;<img src=\"".img_find("misc/left")."\" border=\"0\" alt=\""._CENTERBLOCK."\" title=\""._CENTERBLOCK."\">";
		} elseif ($bposition == "b") {
			$bposition = "<img src=\"".img_find("misc/up")."\" border=\"0\" alt=\""._BANNER."\" title=\""._BANNER."\">&nbsp;"._BANNERUP."&nbsp;<img src=\"".img_find("misc/up")."\" border=\"0\" alt=\""._BANNER."\" title=\""._BANNER."\">";
		} elseif ($bposition == "f") {
			$bposition = "<img src=\"".img_find("misc/down")."\" border=\"0\" alt=\""._BANNER."\" title=\""._BANNER."\">&nbsp;"._BANNERDOWN."&nbsp;<img src=\"".img_find("misc/down")."\" border=\"0\" alt=\""._BANNER."\" title=\""._BANNER."\">";
		}
		if ($bkey == "") {
			$type = ($url) ? "RSS/RDF" : "HTML";
			if ($blockfile != "") $type = _BLOCKFILE2;
		} elseif ($bkey != "") {
			$type = _BLOCKSYSTEM;
		}
		$fcont .= "<td align=\"center\">$type</td>";
		$block_act = $active;
		if ($view == 0) {
			$who_view = _MVALL;
		} elseif ($view == 1) {
			$who_view = _MVUSERS;
		} elseif ($view == 2) {
			$who_view = _MVADMIN;
		} elseif ($view == 3) {
			$who_view = _MVANON;
		}
		$fcont .= "<td align=\"center\">$who_view</td>";
		if ($conf['multilingual'] == 1) {
			$blanguage = (!$blanguage) ? _ALL : ucfirst($blanguage);
			$fcont .= "<td align=\"center\">$blanguage</td>";
		}
		$fcont .= "<td align=\"center\">$bposition</td><td align=\"center\">$weight</td><td align=\"center\">";
		$fcont .= ($con1) ? "<img src=\"".img_find("all/up")."\" border=\"0\" OnClick=\"LoadGet('', 'ajax_block', '9', 'blocks_order', '".$bid."', '".$con1."', '".$weight_minus."', '', '".$weight."'); return false;\" OnDblClick=\"LoadGet('', 'ajax_block', '9', 'blocks_order', '".$bid."', '".$con1."', '".$weight_minus."', '', '".$weight."'); return false;\" title=\""._BLOCKUP."\" alt=\""._BLOCKUP."\" style=\"cursor: pointer;\"> " : "";
		$fcont .= ($con2) ? "<img src=\"".img_find("all/down")."\" border=\"0\" OnClick=\"LoadGet('', 'ajax_block', '9', 'blocks_order', '".$bid."', '".$con2."', '".$weight_plus."', '', '".$weight."'); return false;\" OnDblClick=\"LoadGet('', 'ajax_block', '9', 'blocks_order', '".$bid."', '".$con2."', '".$weight_plus."', '', '".$weight."'); return false;\" title=\""._BLOCKDOWN."\" alt=\""._BLOCKDOWN."\" style=\"cursor: pointer;\">" : "";
		$fcont .= "</td>"
		."<td align=\"center\">".ad_edit($admin_file.".php?op=blocks_edit&bid=".$bid)." ".ad_status($admin_file.".php?op=blocks_change&bid=".$bid, $active)." ".ad_delete($admin_file.".php?op=blocks_delete&id=".$bid, $title)."</td></tr>";
	}
	$cont = "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"bgcolor4\"><tr><th>"._ID."</th><th>"._TITLE."</th><th>"._TYPE."</th><th>"._VIEW."</th>";
	$cont .= ($conf['multilingual'] == 1) ? "<th>"._LANGUAGE."</th>" : "";
	$cont .= "<th>"._POSITION."</th><th colspan=\"2\">"._WEIGHT."</th><th>"._FUNCTIONS."</th></tr>".$fcont."</table>";
	return $cont;
}

function blocks_order() {
	global $prefix, $db, $admin_file;
	$typ = intval($_GET['typ']);
	$text = intval($_GET['text']);
	$id = intval($_GET['id']);
	$cid = intval($_GET['cid']);
	$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$typ' WHERE bid='$id'");
	$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$text' WHERE bid='$cid'");
	echo ajax_block();
}
?>