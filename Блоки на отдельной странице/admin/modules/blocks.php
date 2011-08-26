<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

function blocks_navi() {
	global $admin_file;
	panel();
	open();
	echo "<h1>"._BLOCKSADMIN."</h1>"
	."<h5>[ <a href=\"".$admin_file.".php?op=blocks_admin\">"._HOME."</a>"
	." | <a href=\"".$admin_file.".php?op=blocks_new\">"._ADDNEWBLOCK."</a>"
	." | <a href=\"".$admin_file.".php?op=blocks_file\">"._ADDNEWFILEBLOCK."</a>"
	." | <a href=\"".$admin_file.".php?op=blocks_file_edit\">"._EDITBLOCK."</a>"
	." | <a href=\"".$admin_file.".php?op=blocks_fix\">"._FIXBLOCKS."</a> ]</h5>";
	close();
}

function blocks_admin() {
	global $prefix, $db, $currentlang, $conf, $admin_file;
	head();
	blocks_navi();
	open();
	echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"bgcolor4\" width=\"100%\"><tr>"
	."<th>"._ID."</th><th>"._TITLE."</th><th>"._TYPE."</th><th>"._VIEW."</th>";
	if ($conf['multilingual'] == 1) echo "<th>"._LANGUAGE."</th>";
	echo "<th>"._POSITION."</th><th colspan=\"2\">"._WEIGHT."</th><th>"._FUNCTIONS."</th></tr>";
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
		echo "<tr class=\"bgcolor1\"><td align=\"center\">$bid</td><td>$title</td>";
		if ($bposition == "l") {
			$bposition = "<img src=\"".img_find("misc/left")."\" border=\"0\" alt=\""._LEFTBLOCK."\" title=\""._LEFTBLOCK."\"> "._LEFT."";
		} elseif ($bposition == "r") {
			$bposition = ""._RIGHT." <img src=\"".img_find("misc/right")."\" border=\"0\" alt=\""._RIGHTBLOCK."\" title=\""._RIGHTBLOCK."\">";
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
			if ($url == "") {
				$type = "HTML";
			} elseif ($url != "") {
				$type = "RSS/RDF";
			}
			if ($blockfile != "") $type = ""._BLOCKFILE2."";
		} elseif ($bkey != "") {
			$type = ""._BLOCKSYSTEM."";
		}
		echo "<td align=\"center\">$type</td>";
		$block_act = $active;
		if ($view == 0) {
			$who_view = ""._MVALL."";
		} elseif ($view == 1) {
			$who_view = ""._MVUSERS."";
		} elseif ($view == 2) {
			$who_view = ""._MVADMIN."";
		} elseif ($view == 3) {
			$who_view = ""._MVANON."";
		}
		echo "<td align=\"center\">$who_view</td>";
		if ($conf['multilingual'] == 1) {
			$blanguage = (!$blanguage) ? ""._ALL."" : ucfirst($blanguage);
			echo "<td align=\"center\">$blanguage</td>";
		}
		echo "<td align=\"center\">$bposition</td><td align=\"center\">$weight</td><td align=\"center\">";
		if ($con1) echo"<a href=\"".$admin_file.".php?op=blocks_order&weight=$weight&bidori=$bid&weightrep=$weight_minus&bidrep=$con1\" title=\""._BLOCKUP."\"><img src=\"".img_find("all/up")."\" border=\"0\" alt=\""._BLOCKUP."\"></a> ";
		if ($con2) echo "<a href=\"".$admin_file.".php?op=blocks_order&weight=$weight&bidori=$bid&weightrep=$weight_plus&bidrep=$con2\" title=\""._BLOCKDOWN."\"><img src=\"".img_find("all/down")."\" border=\"0\" alt=\""._BLOCKDOWN."\"></a>";
		echo"</td>"
		."<td align=\"center\">".ad_edit("".$admin_file.".php?op=blocks_edit&bid=".$bid."")." ".ad_status("".$admin_file.".php?op=blocks_change&bid=".$bid."", $active)." ".ad_delete("".$admin_file.".php?op=blocks_delete&id=".$bid."", $title)."";
	}
	echo "</td></tr></table>";
	close();
	foot();
}

function blocks_new() {
	global $prefix, $db, $currentlang, $conf, $admin_file;
	head();
	blocks_navi();
	open();
	echo "<h2>"._ADDNEWBLOCK."</h2>"
	."<form action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._TITLE.":</div><div class=\"center\"><input type=\"text\" name=\"title\" size=\"65\" class=\"admin\" maxlength=\"60\"></div>"
	."<div class=\"left\">"._RSSFILE.":<br><font class=\"small\">"._RSSLINESINFO." "._RSSINFO."</font></div><div class=\"center\"><input type=\"text\" name=\"url\" size=\"30\" style=\"width: 195px\">"
	." <select name=\"headline\" style=\"width: 200px\"><option value=\"0\" selected>"._CUSTOM."</option>".rss_select()."</select></div>"
	."<div class=\"left\">"._REFRESHTIME.":<br><font class=\"small\">"._REFINFO."</font></div><div class=\"center\"><select name=\"refresh\" class=\"admin\">"
	."<option name=\"refresh\" value=\"1800\">1/2 "._HOUR."</option>"
	."<option name=\"refresh\" value=\"3600\" selected>1 "._HOUR."</option>"
	."<option name=\"refresh\" value=\"18000\">5 "._HOURS."</option>"
	."<option name=\"refresh\" value=\"36000\">10 "._HOURS."</option>"
	."<option name=\"refresh\" value=\"86400\">24 "._HOURS."</option></select></div>"
	."<div class=\"left\">"._FILENAME.":</div><div class=\"center\">"
	."<select name=\"blockfile\" class=\"admin\">"
	."<option name=\"blockfile\" value=\"\" selected>"._NONE."</option>";
	$handle = opendir("blocks");
	while ($file = readdir($handle)) {
		if (preg_match("/^block\-(.+)\.php/", $file, $matches)) {
			if ($db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_blocks WHERE blockfile='$file'")) == 0) echo "<option value=\"$file\">".$matches[0]."</option>\n";
		}
	}
	closedir($handle);
	echo "</select></div>"
	."<div class=\"left\">"._TENANCE.":</div><div class=\"center\"><textarea name=\"content\" cols=\"65\" rows=\"15\" class=\"admin\"></textarea></div>"
	."<div class=\"left\">"._POSITION.":</div><div class=\"center\"><select name=\"bposition\" class=\"admin\">"
	."<option name=\"bposition\" value=\"l\">"._LEFT."</option>"
	."<option name=\"bposition\" value=\"c\">"._CENTERUP."</option>"
	."<option name=\"bposition\" value=\"d\">"._CENTERDOWN."</option>"
	."<option name=\"bposition\" value=\"r\">"._RIGHT."</option>"
	."<option name=\"bposition\" value=\"b\">"._BANNERUP."</option>"
	."<option name=\"bposition\" value=\"f\">"._BANNERDOWN."</option>"
	."</select></div>"
	."<div class=\"left\">"._BLOCK_VIEW.":</div><div class=\"center\">"
	."<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" align=\"center\" class=\"admin\"><tr>"
	."<td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"ihome\"></td><td>"._HOME."</td>";
	$a = 1;
	$result = $db->sql_query("SELECT title FROM ".$prefix."_modules");
	while (list($title) = $db->sql_fetchrow($result)) {
		echo "<td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"".$title."\"></td><td>".$title."</td>";
		if ($a == 2) {
			echo "</tr><tr>";
			$a = 0;
		} else {
			$a++;
		}
	}
	echo "</tr><tr><td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"all\"></td><td><b>"._BLOCK_ALL."</b></td><td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"home\"></td><td><b>"._INHOME."</b></td><td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"infly\"></td><td><b>"._INFLY."</b></td></tr>"
	."<tr><td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"otricanie\" $oel></td><td><b>"._DENYING."</b></td><td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"flyfix\" $xel></td><td colspan=\"3\"><b>"._FLY_FIX."</b></td></table>"
	."</div>";
	if ($conf['multilingual'] == 1) echo "<div class=\"left\">"._LANGUAGE.":</div><div class=\"center\"><select name=\"blanguage\" class=\"admin\">".language()."</select></div>";
	echo "<div class=\"left\">"._ACTIVATE2."</div><div class=\"center\"><input type=\"radio\" name=\"active\" value=\"1\" checked>"._YES." &nbsp;&nbsp; <input type=\"radio\" name=\"active\" value=\"0\">"._NO."</div>"
	."<div class=\"left\">"._EXPIRATION.":</div><div class=\"center\"><input type=\"text\" name=\"expire\" maxlength=\"3\" value=\"0\" size=\"65\" class=\"admin\"></div>"
	."<div class=\"left\">"._AFTEREXPIRATION.":</div><div class=\"center\"><select name=\"action\" class=\"admin\">"
	."<option name=\"action\" value=\"d\">"._DEACTIVATE."</option>"
	."<option name=\"action\" value=\"r\">"._DELETE."</option></select></div>"
	."<div class=\"left\">"._VIEWPRIV."</div><div class=\"center\"><select name=\"view\" class=\"admin\">"
	."<option value=\"0\" >"._MVALL."</option>"
	."<option value=\"1\" >"._MVUSERS."</option>"
	."<option value=\"2\" >"._MVADMIN."</option>"
	."<option value=\"3\" >"._MVANON."</option>"
	."</select></div>"
	."<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"blocks_add\"><input type=\"submit\" value=\""._CREATEBLOCK."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function blocks_file() {
	global $admin_file;
	head();
	blocks_navi();
	open();
	echo "<h2>"._ADDNEWFILEBLOCK."</h2>"
	."<form action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._FILENAME.":</div><div class=\"center\"><input type=\"text\" name=\"bf\" size=\"65\" class=\"admin\" maxlength=\"200\"></div>"
	."<div class=\"left\">"._TYPE.":</div><div class=\"center\"><input type=\"radio\" name=\"flag\" value=\"php\" checked>PHP &nbsp;&nbsp; <input type=\"radio\" name=\"flag\" value=\"html\">HTML</div>"
	."<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"blocks_bfile\"><input type=\"submit\" value=\""._CREATEBLOCK."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function blocks_file_edit() {
	global $prefix, $db, $admin_file;
	head();
	blocks_navi();
	open();
	echo "<h2>"._EDITBLOCK."</h2>"
	."<form action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._FILENAME.":</div><div class=\"center\">"
	."<select name=\"bf\" class=\"admin\">";
	$handle = opendir("blocks");
	while ($file = readdir($handle)) {
		if (preg_match("/^block\-(.+)\.php/", $file, $matches)) {
			if ($db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_blocks WHERE blockfile='$file'")) == 0) echo "<option value=\"$file\">".$matches[0]."</option>\n";
		}
	}
	closedir($handle);
	echo "</select></div>"
	."<div class=\"button\"><input type=\"hidden\" name=\"op\" value=\"blocks_bfile\"><input type=\"submit\" value=\""._EDITBLOCK."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function blocks_fix() {
	global $prefix, $db, $admin_file;
	$leftpos = "l";
	$rightpos = "r";
	$centerpos = "c";
	$result = $db->sql_query("SELECT bid FROM ".$prefix."_blocks WHERE bposition='$leftpos' ORDER BY weight ASC");
	$weight = 0;
	while ($row = $db->sql_fetchrow($result)) {
		$bid = intval($row['bid']);
		$weight++;
		$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$weight' WHERE bid='$bid'");
	}
	$result2 = $db->sql_query("SELECT bid FROM ".$prefix."_blocks WHERE bposition='$rightpos' ORDER BY weight ASC");
	$weight = 0;
	while ($row2 = $db->sql_fetchrow($result2)) {
		$bid = intval($row2['bid']);
		$weight++;
		$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$weight' WHERE bid='$bid'");
	}
	$result3 = $db->sql_query("SELECT bid FROM ".$prefix."_blocks WHERE bposition='$centerpos' ORDER BY weight ASC");
	$weight = 0;
	while ($row3 = $db->sql_fetchrow($result3)) {
		$bid = intval($row3['bid']);
		$weight++;
		$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$weight' WHERE bid='$bid'");
	}
	Header("Location: ".$admin_file.".php?op=blocks_admin");
}

function blocks_order() {
	global $prefix, $db, $admin_file;
	$weightrep = $_GET['weightrep'];
	$weight = $_GET['weight'];
	$bidrep = $_GET['bidrep'];
	$bidori = $_GET['bidori'];
	$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$weight' WHERE bid='$bidrep'");
	$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$weightrep' WHERE bid='$bidori'");
	Header("Location: ".$admin_file.".php?op=blocks_admin");
}

function blocks_add() {
	global $prefix, $db, $admin_file;
	$title = $_POST['title'];
	$content = $_POST['content'];
	$url = $_POST['url'];
	$bposition = $_POST['bposition'];
	$active = $_POST['active'];
	$refresh = $_POST['refresh'];
	$headline = $_POST['headline'];
	$blanguage = $_POST['blanguage'];
	$blockfile = $_POST['blockfile'];
	$view = $_POST['view'];
	$expire = $_POST['expire'];
	$action = $_POST['action'];
	$url = ($headline) ? $headline : $url;
	$blockwhere = $_POST['blockwhere'];
	list($weight) = $db->sql_fetchrow($db->sql_query("SELECT weight FROM ".$prefix."_blocks WHERE bposition='$bposition' ORDER BY weight DESC"));
	$weight++;
	$bkey = "";
	$btime = "";
	if ($blockfile != "") {
		$url = "";
		if ($title == "") $title = str_replace("_", " ", str_replace(array("block-", ".php"), "", $blockfile));
	}
	if ($url) {
		$btime = time();
		$content = rss_read($url, 1);
	}
	if (($content == "") && ($blockfile == "")) {
		head();
		blocks_navi();
		warning(""._RSSFAIL."<br><br>"._GOBACK."", "", "", 1);
		foot();
	} else {
		if ($expire == "" || $expire == 0) {
			$expire = 0;
		} else {
			$expire = time() + ($expire * 86400);
		}
		if (isset($blockwhere)) {
			$which = "";
			$which = (in_array("all", $blockwhere)) ? "all" : $which;
			$which = (in_array("home", $blockwhere)) ? "home" : $which;
			if ($which == "") $which = implode(",", $blockwhere);
		}
		$db->sql_query("INSERT INTO ".$prefix."_blocks VALUES (NULL, '$bkey', '$title', '$content', '$url', '$bposition', '$weight', '$active', '$refresh', '$btime', '$blanguage', '$blockfile', '$view', '$expire', '$action', '$which')");
		Header("Location: ".$admin_file.".php?op=blocks_admin");
	}
}

function blocks_bfile() {
	global $prefix, $db, $admin_file;
	if ($_REQUEST['bf'] != "") {
		$bf = $_REQUEST['bf'];
		if (isset($_POST['flag'])) {
			$flaged = $_POST['flag'];
			$bf = str_replace(array("block-", ".php"), "", $bf);
			$bf = "block-".$bf.".php";
		} else {
			$bfstr = file_get_contents("blocks/".$bf);
			if (strpos($bfstr,"BLOCKHTML") === false) {
				$flaged = "php";
				preg_match("/<\?php.*if.*\(\!defined\(\"BLOCK_FILE\"\)\).*exit;.*?}(.*)\?>/is", $bfstr, $out);
				unset($out[0]);
			} else {
				$flaged = "html";
				preg_match("/<<<BLOCKHTML(.*)BLOCKHTML;/is", $bfstr, $out);
				unset($out[0]);
			}
		}
		head();
		blocks_navi();
		$permtest = end_chmod("blocks", 777);
		if ($permtest) warning($permtest, "", "", 1);
		open();
		echo "<h2>"._BLOCK.": $bf</h2>"
		."<form action=\"".$admin_file.".php\" method=\"post\">"
		."<div align=\"center\"><textarea name=\"blocktext\" cols=\"125\" rows=\"20\">".trim($out[1])."</textarea></div>"
		."<div class=\"button\"><input type=\"hidden\" name=\"bf\" value=\"".$bf."\">"
		."<input type=\"hidden\" name=\"flag\" value=\"".$flaged."\">"
		."<input type=\"hidden\" name=\"op\" value=\"blocks_bfile_save\">"
		."<input type=\"submit\" value=\""._SAVE."\" class=\"fbutton\"> "._GOBACK."</div></form>";
		close();
		foot();
	} else {
		Header("Location: ".$admin_file.".php?op=blocks_file");
	}
}

function blocks_bfile_save() {
	global $prefix, $db, $admin_file;
	if (isset($_POST['blocktext'])) {
		if (!empty($_POST['blocktext'])) {
			if (isset($_POST['bf'])) {
				$bf = $_POST['bf'];
				if ($handle = fopen("blocks/".$bf, "wb")) {
					$htmlB = "";
					$htmlE = "";
					if (isset($_POST['flag'])) {
						$flaged = $_POST['flag'];
						if ($flaged == 'html') {
							$htmlB = "\$content = <<<BLOCKHTML\r\n";
							$htmlE = "\r\nBLOCKHTML;\r\n";
						}
					}
					$str_set = stripslashes($_POST['blocktext']);
					fwrite($handle, "<?php\r\nif (!defined(\"BLOCK_FILE\")) {\r\n\tHeader(\"Location: ../index.php\");\r\n\texit;\r\n}\r\n".$htmlB.$str_set.$htmlE."\r\n?>");
					Header("Location: ".$admin_file.".php?op=blocks_admin");
				}
				fclose($handle);
			}
		}
	}
}

function blocks_edit() {
	global $prefix, $db, $admin_file, $conf;
	head();
	blocks_navi();
	$bid = intval($_GET['bid']);
	list($bkey, $title, $content, $url, $bposition, $weight, $active, $refresh, $blanguage, $blockfile, $view, $expire, $action, $which) = $db->sql_fetchrow($db->sql_query("SELECT bkey, title, content, url, bposition, weight, active, refresh, blanguage, blockfile, view, expire, action, which FROM ".$prefix."_blocks WHERE bid='$bid'"));
	if ($url != "") {
		$type = "("._BLOCKRSS.")";
	} elseif ($blockfile != "") {
		$type = "("._BLOCKFILE.")";
	} else {
		$type = "("._BLOCKHTML.")";
	}
	open();
	echo "<h2>"._BLOCK.": $title $type</h2>"
	."<form action=\"".$admin_file.".php\" method=\"post\">"
	."<div class=\"left\">"._TITLE.":</div><div class=\"center\"><input type=\"text\" name=\"title\" maxlength=\"50\" size=\"65\" class=\"admin\" value=\"$title\"></div>";
	if ($blockfile != "") {
		echo "<div class=\"left\">"._FILENAME.":</div><div class=\"center\"><select name=\"blockfile\" class=\"admin\">";
		$dir = opendir("blocks");
		while ($file = readdir($dir)) {
			if (preg_match("/^block\-(.+)\.php/", $file, $matches)) {
				$selected = ($blockfile == $file) ? "selected" : "";
				echo "<option value=\"$file\" $selected>".$matches[0]."</option>";
			}
		}
		closedir($dir);
		echo "</select></div>";
	} else {
		if ($url != "") {
			echo "<div class=\"left\">"._RSSFILE.":</div><div class=\"center\"><input type=\"text\" name=\"url\" size=\"50\" maxlength=\"200\" value=\"$url\" class=\"admin\"></div>"
			."<div class=\"left\">"._REFRESHTIME.":</div><div class=\"center\"><select name=\"refresh\" class=\"admin\">"
			."<option value='1800'";
			if ($refresh == "1800") echo " selected";
			echo ">1/2 "._HOUR."</option>"
			."<option value='3600'";
			if ($refresh == "3600") echo " selected";
			echo ">1 "._HOUR."</option>"
			."<option value='18000'";
			if ($refresh == "18000") echo " selected";
			echo ">5 "._HOURS."</option>"
			."<option value='36000'";
			if ($refresh == "36000") echo " selected";
			echo ">10 "._HOURS."</option>"
			."<option value='86400'";
			if ($refresh == "86400") echo " selected";
			echo ">24 "._HOURS."</option>"
			."</select></div>";
		} else {
			echo "<div class=\"left\">"._TENANCE.":</div><div class=\"center\"><textarea name=\"content\" cols=\"65\" rows=\"15\" class=\"admin\">$content</textarea></div>";
		}
	}
	$oldposition = $bposition;
	echo "<input type=\"hidden\" name=\"oldposition\" value=\"$oldposition\">";
	$sel1 = ($bposition == "l") ? "selected" : "";
	$sel2 = ($bposition == "c") ? "selected" : "";
	$sel3 = ($bposition == "r") ? "selected" : "";
	$sel4 = ($bposition == "d") ? "selected" : "";
	$sel5 = ($bposition == "b") ? "selected" : "";
	$sel6 = ($bposition == "f") ? "selected" : "";
	echo "<div class=\"left\">"._POSITION.":</div><div class=\"center\"><select name=\"bposition\" class=\"admin\">"
	."<option name=\"bposition\" value=\"l\" $sel1>"._LEFT."</option>"
	."<option name=\"bposition\" value=\"c\" $sel2>"._CENTERUP."</option>"
	."<option name=\"bposition\" value=\"d\" $sel4>"._CENTERDOWN."</option>"
	."<option name=\"bposition\" value=\"r\" $sel3>"._RIGHT."</option>"
	."<option name=\"bposition\" value=\"b\" $sel5>"._BANNERUP."</option>"
	."<option name=\"bposition\" value=\"f\" $sel6>"._BANNERDOWN."</option>"
	."</select></div>";
	echo "<div class=\"left\">"._BLOCK_VIEW.":</div><div class=\"center\">"
	."<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" align=\"center\" class=\"admin\"><tr>";
	$where_mas = explode(",", $which);
	$cel = ($where_mas[0] == "ihome") ? "checked" : "";
	echo "<td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"ihome\" $cel></td><td>"._HOME."</td>";
	$a = 1;
	$result = $db->sql_query("SELECT title FROM ".$prefix."_modules");
	while (list($title) = $db->sql_fetchrow($result)) {
		$cel = "";
		foreach ($where_mas as $val) {
			if ($val == $title) $cel="checked";
		}
		echo "<td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"".$title."\" $cel></td><td>".$title."</td>";
		if ($a == 2) {
			echo "</tr><tr>";
			$a = 0;
		} else {
			$a++;
		}
	}
	$where_mas = explode(",", $which);
	$cel = "";
	$hel = "";
	if (in_array("infly", $where_mas)) {
		switch ($where_mas[0]) {
			case "all":
			$cel = "checked";
			break;
			case "home":
			$hel = "checked";
			break;
			case "infly":
			$fel = "checked";
			break;
		}
	}
	$hel = (in_array("home", $where_mas)) ? "checked" : "";
	$cel = (in_array("all", $where_mas) && empty($hel)) ? "checked" : "";
	$fel = (in_array("infly", $where_mas)) ? "checked" : "";
	$oel = (in_array("otricanie", $where_mas)) ? "checked" : "";
	$xel = (in_array("flyfix", $where_mas)) ? "checked" : "";
	echo "</tr><tr><td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"all\" $cel></td><td><b>"._BLOCK_ALL."</b></td><td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"home\" $hel></td><td><b>"._INHOME."</b></td><td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"infly\" $fel></td><td><b>"._INFLY."</b></td></tr>"
	."<tr><td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"otricanie\" $oel></td><td><b>"._DENYING."</b></td><td><input type=\"checkbox\" name=\"blockwhere[]\" value=\"flyfix\" $xel></td><td colspan=\"2\"><b>"._FLY_FIX."</b></td></table>"
	."</div>";
	if ($conf['multilingual'] == 1) echo "<div class=\"left\">"._LANGUAGE.":</div><div class=\"center\"><select name=\"blanguage\" class=\"admin\">".language($blanguage)."</select></div>";
	$sel1 = ($active == 1) ? "checked" : "";
	$sel2 = ($active == 0) ? "checked" : "";
	if ($expire != 0) {
		$newexpire = 0;
		$oldexpire = $expire;
		$expire = intval(($expire - time()) / 3600);
		$exp_day = $expire / 24;
		$expire_text = "<input type=\"hidden\" name=\"expire\" value=\"$oldexpire\">"._PURCHASED.": $expire "._HOURS." (".substr($exp_day,0,5)." "._DAYS.")";
	} else {
		$newexpire = 1;
		$expire_text = "<input type=\"text\" name=\"expire\" value=\"0\" maxlength=\"3\" size=\"65\" class=\"admin\">";
	}
	$selact1 = ($action == "d") ? "selected" : "";
	$selact2 = ($action == "r") ? "selected" : "";
	echo "<div class=\"left\">"._ACTIVATE2."</div><div class=\"center\"><input type=\"radio\" name=\"active\" value=\"1\" $sel1>"._YES." &nbsp;&nbsp;"
	."<input type=\"radio\" name=\"active\" value=\"0\" $sel2>"._NO."</div>"
	."<div class=\"left\">"._EXPIRATION.":</div><div class=\"center\">$expire_text</div>"
	."<div class=\"left\">"._AFTEREXPIRATION.":</div><div class=\"center\"><select name=\"action\" class=\"admin\">"
	."<option name=\"action\" value=\"d\" $selact1>"._DEACTIVATE."</option>"
	."<option name=\"action\" value=\"r\" $selact2>"._DELETE."</option></select></div>";
	$sel1 = ($view == 0) ? "selected" : "";
	$sel2 = ($view == 1) ? "selected" : "";
	$sel3 = ($view == 2) ? "selected" : "";
	$sel4 = ($view == 3) ? "selected" : "";
	echo "<div class=\"left\">"._VIEWPRIV."</div><div class=\"center\"><select name=\"view\" class=\"admin\">"
	."<option value=\"0\" $sel1>"._MVALL."</option>"
	."<option value=\"1\" $sel2>"._MVUSERS."</option>"
	."<option value=\"2\" $sel3>"._MVADMIN."</option>"
	."<option value=\"3\" $sel4>"._MVANON."</option>"
	."</select></div>"
	."<div class=\"button\"><input type=\"hidden\" name=\"bid\" value=\"$bid\">"
	."<input type=\"hidden\" name=\"newexpire\" value=\"$newexpire\">"
	."<input type=\"hidden\" name=\"bkey\" value=\"$bkey\">"
	."<input type=\"hidden\" name=\"weight\" value=\"$weight\">"
	."<input type=\"hidden\" name=\"op\" value=\"blocks_edit_save\">"
	."<input type=\"submit\" value=\""._SAVE."\" class=\"fbutton\"></div></form>";
	close();
	foot();
}

function blocks_edit_save() {
	global $prefix, $db, $admin_file;
	$newexpire = $_POST['newexpire'];
	$bid = $_POST['bid'];
	$bkey = $_POST['bkey'];
	$title = $_POST['title'];
	$content = $_POST['content'];
	$url = $_POST['url'];
	$oldposition = $_POST['oldposition'];
	$bposition = $_POST['bposition'];
	$active = $_POST['active'];
	$refresh = $_POST['refresh'];
	$weight = $_POST['weight'];
	$blanguage = $_POST['blanguage'];
	$blockfile = $_POST['blockfile'];
	$view = $_POST['view'];
	$expire = $_POST['expire'];
	$action = $_POST['action'];
	$blockwhere = $_POST['blockwhere'];
	if (isset($blockwhere)) {
		$which = "";
		if (in_array("all", $blockwhere)) $which = "all";
		if (in_array("home", $blockwhere)) $which = "home";
		if ($which == "") {
			$which = implode(",", $blockwhere);
		} else {
			if (in_array("otricanie", $blockwhere)) $which .= ",otricanie";
			if (in_array("flyfix", $blockwhere)) $which .= ",flyfix";
		}
		if (in_array("infly", $blockwhere)) {
			if (in_array("flyfix", $blockwhere)) {
				$which = "infly,".$which;
			} else {
				$which = "infly,";
			}
		}
		$db->sql_query("UPDATE ".$prefix."_blocks SET which='$which' WHERE bid='$bid'");
	} else {
		$db->sql_query("UPDATE ".$prefix."_blocks SET which='' WHERE bid='$bid'");
	}
	if ($url) {
		$bkey = "";
		$btime = time();
		$content = rss_read($url, 1);
		if ($oldposition != $bposition) {
			$result = $db->sql_query("SELECT bid FROM ".$prefix."_blocks WHERE weight>='$weight' AND bposition='$bposition'");
			$fweight = $weight;
			$oweight = $weight;
			while (list($nbid) = $db->sql_fetchrow($result)) {
				$weight++;
				$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$weight' WHERE bid='$nbid'");
			}
			$result2 = $db->sql_query("SELECT bid FROM ".$prefix."_blocks WHERE weight>'$oweight' AND bposition='$oldposition'");
			while (list($obid) = $db->sql_fetchrow($result2)) {
				$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$oweight' WHERE bid='$obid'");
				$oweight++;
			}
			list($lastw) = $db->sql_fetchrow($db->sql_query("SELECT weight FROM ".$prefix."_blocks WHERE bposition='$bposition' ORDER BY weight DESC LIMIT 0,1"));
			if ($lastw <= $fweight) {
				$lastw++;
				$db->sql_query("UPDATE ".$prefix."_blocks SET title='$title', content='$content', bposition='$bposition', weight='$lastw', active='$active', refresh='$refresh', blanguage='$blanguage', blockfile='$blockfile', view='$view' WHERE bid='$bid'");
			} else {
				$db->sql_query("UPDATE ".$prefix."_blocks SET title='$title', content='$content', bposition='$bposition', weight='$fweight', active='$active', refresh='$refresh', blanguage='$blanguage', blockfile='$blockfile', view='$view' WHERE bid='$bid'");
			}
		} else {
			$db->sql_query("UPDATE ".$prefix."_blocks SET bkey='$bkey', title='$title', content='$content', url='$url', bposition='$bposition', weight='$weight', active='$active', refresh='$refresh', blanguage='$blanguage', blockfile='$blockfile', view='$view' WHERE bid='$bid'");
		}
		Header("Location: ".$admin_file.".php?op=blocks_admin");
	} else {
		if ($oldposition != $bposition) {
			$result5 = $db->sql_query("SELECT bid FROM ".$prefix."_blocks WHERE weight>='$weight' AND bposition='$bposition'");
			$fweight = $weight;
			$oweight = $weight;
			while (list($nbid) = $db->sql_fetchrow($result5)) {
				$weight++;
				$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$weight' WHERE bid='$nbid'");
			}
			$result6 = $db->sql_query("SELECT bid FROM ".$prefix."_blocks WHERE weight>'$oweight' AND bposition='$oldposition'");
			while (list($obid) = $db->sql_fetchrow($result6)) {
				$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$oweight' WHERE bid='$obid'");
				$oweight++;
			}
			list($lastw) = $db->sql_fetchrow($db->sql_query("SELECT weight FROM ".$prefix."_blocks WHERE bposition='$bposition' ORDER BY weight DESC LIMIT 0,1"));
			if ($lastw <= $fweight) {
				$lastw++;
				$db->sql_query("UPDATE ".$prefix."_blocks SET title='$title', content='$content', bposition='$bposition', weight='$lastw', active='$active', refresh='$refresh', blanguage='$blanguage', blockfile='$blockfile', view='$view' WHERE bid='$bid'");
			} else {
				$db->sql_query("UPDATE ".$prefix."_blocks SET title='$title', content='$content', bposition='$bposition', weight='$fweight', active='$active', refresh='$refresh', blanguage='$blanguage', blockfile='$blockfile', view='$view' WHERE bid='$bid'");
			}
		} else {
			if ($expire == "") $expire = 0;
			if ($newexpire == 1 && $expire != 0) $expire = time() + ($expire * 86400);
			$result8 = $db->sql_query("UPDATE ".$prefix."_blocks SET bkey='$bkey', title='$title', content='$content', url='$url', bposition='$bposition', weight='$weight', active='$active', refresh='$refresh', blanguage='$blanguage', blockfile='$blockfile', view='$view', expire='$expire', action='$action' WHERE bid='$bid'");
		}
		Header("Location: ".$admin_file.".php?op=blocks_admin");
	}
}

function blocks_change() {
	global $prefix, $db, $admin_file;
	$bid = intval($_GET['bid']);
	$ok = (isset($_GET['ok'])) ? 1 : 0;
	$row = $db->sql_fetchrow($db->sql_query("SELECT active FROM ".$prefix."_blocks WHERE bid='$bid'"));
	$active = intval($row['active']);
	if (($ok) || ($active == 1)) {
		if ($active == 0) {
			$active = 1;
		} elseif ($active == 1) {
			$active = 0;
		}
		$result2 = $db->sql_query("UPDATE ".$prefix."_blocks SET active='$active' WHERE bid='$bid'");
		Header("Location: ".$admin_file.".php?op=blocks_admin");
	} else {
		list($title, $content) = $db->sql_fetchrow($db->sql_query("SELECT title, content FROM ".$prefix."_blocks WHERE bid='$bid'"));
		head();
		panel();
		title(""._BLOCKACTIVATION."");
		open();
		if ($content != "") {
			echo "<center>"._WANT2ACTIVATE." \"$title\"?<br><br>";
			themesidebox($title, $content);
		} else {
			echo "<center>"._WANT2ACTIVATE." \"$title\"?<br><br>";
		}
		echo "[ <a href=\"".$admin_file.".php?op=blocks_change&bid=$bid&ok=1\">"._YES."</a> | <a href=\"".$admin_file.".php?op=blocks_admin\">"._NO."</a> ]</center>";
		close();
		foot();
	}
}

switch($op) {
	case "blocks_admin":
	blocks_admin();
	break;
	
	case "blocks_new":
	blocks_new();
	break;
	
	case "blocks_file":
	blocks_file();
	break;
	
	case "blocks_file_edit":
	blocks_file_edit();
	break;
	
	case "blocks_fix":
	blocks_fix();
	break;
	
	case "blocks_order":
	blocks_order();
	break;
	
	case "blocks_add":
	blocks_add();
	break;
	
	case "blocks_bfile":
	blocks_bfile();
	break;
	
	case "blocks_bfile_save":
	blocks_bfile_save();
	break;
	
	case "blocks_edit":
	blocks_edit();
	break;
	
	case "blocks_edit_save":
	blocks_edit_save();
	break;
	
	case "blocks_change":
	blocks_change();
	break;
	
	case "blocks_delete":
	list($bposition, $weight) = $db->sql_fetchrow($db->sql_query("SELECT bposition, weight FROM ".$prefix."_blocks WHERE bid='".$id."'"));
	$result = $db->sql_query("SELECT bid FROM ".$prefix."_blocks WHERE weight >'$weight' AND bposition='$bposition'");
	while (list($nbid) = $db->sql_fetchrow($result)) {
		$db->sql_query("UPDATE ".$prefix."_blocks SET weight='$weight' WHERE bid='$nbid'");
		$weight++;
	}
	$db->sql_query("DELETE FROM ".$prefix."_blocks WHERE bid='".$id."'");
	Header("Location: ".$admin_file.".php?op=blocks_admin");
	break;
}
?>