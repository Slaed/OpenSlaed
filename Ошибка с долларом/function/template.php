<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("FUNC_FILE")) die("Illegal File Access");

function warning() {
	global $BlockGlob, $ThemeSel, $conf;
	$warg = func_get_args();
	$text = $warg[0];
	$redirect = $warg[1];
	$time = $warg[2];
	$type = $warg[3];
	$type = ($type == 1) ? "warning" : "info";
	if ($redirect || intval($time)) $toredirect = "<meta http-equiv=\"refresh\" content=\"".$time."; url=index.php".$redirect."\">";
	$thefile = "\$r_file=\"".addslashes(file_get_contents(get_theme_file("warning")))."\";";
	eval($thefile);
	echo stripslashes($r_file);
}

function prints() {
	global $BlockGlob, $ThemeSel, $conf;
	$parg = func_get_args();
	$title = $parg[0];
	$ptitle = $parg[1];
	$text = $parg[2];
	$url = $parg[3];
	$sitename = $conf['sitename'];
	$homeurl = $conf['homeurl'];
	$site_logo = $conf['site_logo'];
	$charset = ""._CHARSET."";
	$thefile = "\$r_file=\"".addslashes(file_get_contents(get_theme_file("prints")))."\";";
	eval($thefile);
	echo stripslashes($r_file);
}

function title($text) {
	global $BlockGlob, $ThemeSel, $conf;
	$thefile = "\$r_file=\"".addslashes(file_get_contents(get_theme_file("title")))."\";";
	eval($thefile);
	echo stripslashes($r_file);
}

function search() {
	global $BlockGlob, $ThemeSel, $conf;
	$sarg = func_get_args();
	$name = $sarg[0];
	$mod = $sarg[1];
	$navi = $sarg[2];
	$s_search = ""._SEARCH."";
	$thefile = "\$r_file=\"".addslashes(file_get_contents(get_theme_file("search")))."\";";
	eval($thefile);
	echo stripslashes($r_file);
}

function messagebox($title, $content) {
	global $BlockGlob, $ThemeSel;
	static $cache;
	if (!isset($cache)) {
		$str = 'global $BlockGlob, $ThemeSel; echo "'.addslashes(file_get_contents("templates/".$ThemeSel."/message-box.html")).'";';
		$cache = create_function('$title, $content', $str);
	}
	$cache($title, $content);
}

function themeheader($head) {
	global $BlockGlob, $ThemeSel, $user, $conf, $confu;
	$sitename = $conf['sitename'];
	$homeurl = $conf['homeurl'];
	$slogan = $conf['slogan'];
	$site_logo = $conf['site_logo'];
	if (is_user()) {
		$uname = htmlspecialchars(substr($user[1], 0, 25));
		$theuser = "<img src=\"templates/$ThemeSel/images/green_dot.gif\" width=\"10\" height=\"10\" alt=\""._HELLO.", $uname!\"> "._HELLO.", $uname!";
	} else {
		if ($confu['enter'] == 1 && (!$conf['gfx_chk'] || $conf['gfx_chk'] == 1 || $conf['gfx_chk'] == 3 || $conf['gfx_chk'] == 6)) {
			$theuser = "<form action=\"index.php?name=account\" method=\"post\"><div><span>"._NICKNAME.":</span><input type=\"text\" name=\"user_name\" size=\"10\" maxlength=\"25\"></div><div><span>"._PASSWORD.":</span><input type=\"password\" name=\"user_password\" size=\"10\" maxlength=\"25\"></div><input type=\"hidden\" name=\"op\" value=\"login\"><input type=\"submit\" value=\""._LOGIN."\" class=\"fbutton\"></form>";
		} else {
			$theuser = "<img src=\"templates/$ThemeSel/images/red_dot.gif\" width=\"10\" height=\"10\" alt=\""._BREG."\"> <a href=\"index.php?name=account\" title=\""._BREG."\">"._BREG."</a>";
		}
	}
	$harg = array(_HOME, _NEWS, _FORUM, _ACCOUNT, _S_FAVORITEN, _S_STARTSEITE, _ALBUM, _FAQ, _PAGES, _FEEDBACK, _RECOMMEND, _SEARCH);
	$s_home = $harg[0]; $s_news = $harg[1]; $s_forum = $harg[2]; $s_profil = $harg[3]; $s_favoriten = $harg[4]; $s_startseite = $harg[5]; $s_album = $harg[6]; $s_faq = $harg[7]; $s_pages = $harg[8]; $s_feed = $harg[9]; $s_recomm = $harg[10];
	$startseite = "<a href=\"#\" OnClick=\"this.style.behavior='url(#default#homepage)'; this.setHomePage('$homeurl');\" title=\"$s_startseite\">";
	$head .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"ajax/tool_box.js\"></script>";
	$thefile = "\$r_file=\"".addslashes($head)."\";";
	$thefile = str_replace('\\\\$','\$',$thefile);
	eval($thefile);
	echo "".stripslashes($r_file)."";
}

function open() {
	global $BlockGlob, $ThemeSel, $conf;
	static $cache;
	if (!isset($cache)) {
		$str = 'global $BlockGlob, $ThemeSel; echo "'.addslashes(file_get_contents(get_theme_file("table-open"))).'";';
		$cache = create_function('', $str);
	}
	$cache();
}

function close() {
	global $BlockGlob, $ThemeSel, $conf;
	static $cache;
	if (!isset($cache)) {
		$str = 'global $BlockGlob, $ThemeSel; echo "'.addslashes(file_get_contents(get_theme_file("table-close"))).'";';
		$cache = create_function('', $str);
	}
	$cache();
}

function basic() {
	global $BlockGlob, $ThemeSel, $conf;
	static $cache;
	$arg = func_get_args();
	$topicid = $arg[0];
	$topicimage = $arg[1];
	$topictitle = $arg[2];
	$aid = $arg[3];
	$title = $arg[4];
	$content = $arg[5];
	$morelink = $arg[6];
	if (!isset($cache)) {
		$str = 'global $BlockGlob, $ThemeSel; echo "'.addslashes(file_get_contents(get_theme_file("basic"))).'";';
		$cache = create_function('$topicid, $topicimage, $topictitle, $aid, $title, $content, $morelink, $arg', $str);
	}
	$cache($topicid, $topicimage, $topictitle, $aid, $title, $content, $morelink, $arg);
}

function comment() {
	global $BlockGlob, $ThemeSel, $conf;
	$carg = func_get_args();
	$id = $carg[0];
	$name = $carg[1];
	$info = $carg[2];
	$avatar = $carg[3];
	$text = $carg[4];
	$rate = $carg[5];
	$link = $carg[6];
	static $cache;
	if (!isset($cache)) {
		$str = 'global $BlockGlob, $ThemeSel; echo "'.addslashes(file_get_contents(get_theme_file("comment"))).'";';
		$cache = create_function('$id, $name, $info, $avatar, $text, $rate, $link, $carg', $str);
	}
	$cache($id, $name, $info, $avatar, $text, $rate, $link, $carg);
}

function themesidebox($title, $content) {
	global $BlockGlob, $ThemeSel, $pos, $blockfile, $b_id, $home, $conf;
	static $bl_mass;
	if ($pos == "s" || $pos == "o") {
		$bl_name = (empty($blockfile)) ? "fly-block-".$b_id : "fly-".str_replace(".php", "", $blockfile);
	} else {
		$bl_name=(empty($blockfile)) ? "block-".$b_id : str_replace(".php", "", $blockfile);
	}
	if (!isset($bl_mass[$bl_name])) {
		$tmp_file = (file_exists("templates/".$ThemeSel."/".$bl_name.".html")) ? "templates/".$ThemeSel."/".$bl_name.".html" : false;
		if ($tmp_file) {
			$bl_mass[$bl_name]['f'] = create_function('$title, $content', 'global $BlockGlob, $ThemeSel; return( "'.addslashes(file_get_contents($tmp_file))." \");");
		} else {
			switch($pos) {
				case 'l':
				$bl_name ="block-left";
				break;
				case 'r':
				$bl_name ="block-right";
				break;
				case 'c':
				$bl_name ="block-center";
				break;
				case 'd':
				$bl_name ="block-down";
				break;
				case 's':
				$bl_name ="block-fly";
				break;
				case 'o':
				$bl_name ="block-fly";
				break;
				default:
				$bl_name ="block-all";
				break;
			}
			if (!isset($bl_mass[$bl_name])) {
				$tmp_file = get_theme_file($bl_name);
				if ($tmp_file) {
					$bl_mass[$bl_name]['f'] = create_function('$title, $content', 'global $BlockGlob, $ThemeSel; return( "'.addslashes(file_get_contents($tmp_file))." \");");
				} else {
					if (!isset($bl_mass['block-all'])) {
						$tmp_file = get_theme_file("block-all");
						if ($tmp_file) {
							$bl_mass[$bl_name]['f'] = create_function('$title, $content', 'global $BlockGlob, $ThemeSel; return( "'.addslashes(file_get_contents($tmp_file))." \");");
						} else {
							if ($pos == "o") {
								return "<fieldset><legend>".$title."</legend>".$content."</fieldset>";
							} else {
								echo "<fieldset><legend>".$title."</legend>".$content."</fieldset>";
								return;
							}
						}
					}
				}
			}
		}
	}
	if ($pos == "o") {
		return $bl_mass[$bl_name]['f']($title, $content);
	} else {
		echo $bl_mass[$bl_name]['f']($title, $content);
	}
}

function themefooter($foot) {
	global $BlockGlob, $ThemeSel, $conf;
	$farg = array(_HOME, _NEWS, _FORUM, _ACCOUNT, _S_FAVORITEN, _S_STARTSEITE, _ALBUM, _FAQ, _PAGES, _FEEDBACK, _RECOMMEND);
	$s_home = $farg[0]; $s_news = $farg[1]; $s_forum = $farg[2]; $s_profil = $farg[3]; $s_favoriten = $farg[4]; $s_startseite = $farg[5]; $s_album = $farg[6]; $s_faq = $farg[7]; $s_pages = $farg[8]; $s_feed = $farg[9]; $s_recomm = $farg[10];
	$thefile = "\$r_file=\"".addslashes($foot)."\";";
	$thefile = str_replace('\\\\$','\$',$thefile);
	eval($thefile);
	echo stripslashes($r_file);
}
?>