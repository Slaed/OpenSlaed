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

if (!defined("BLOCK_FILE")) {
	Header("Location: ../index.php");
	exit;
}


global $prefix, $db, $conf, $confu;
	$content = "<table align=\"center\"><form action=\"index.php?name=search\" method=\"post\"><tr>"
."<td align=\"center\"><img src=\"".img_find("all/search")."\" alt=\""._SEARCH."\" title=\""._SEARCH."\"></td>"
."<td align=\"center\"><input type=\"text\" name=\"word\" size=\"180\" maxlength=\"100\" class=\"binput\"></td>"
."<td align=\"center\"><input type=\"submit\" title=\""._SEARCH."\" value=\""._OK."\" class=\"fbutton\"></td>"
."";
if (is_user()) {
	$userinfo = getusrinfo();
	$uname = $userinfo['user_name'];
	$user_id = intval($userinfo['user_id']);
	$user_avatar = ($userinfo['user_avatar']) ? $userinfo['user_avatar'] : "00.gif";

	$content = "<center><img src=\"".$confu['adirectory']."/".$user_avatar."\"><br>"
	."<b>"._BWEL.",<br><span style=\"font-size: 16px;color: green;\"><h10>$uname</h10></b></span></center><hr>"
	."<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tr valign=\"middle\"><td><a href=\"index.php?name=account&op=logout\" title=\""._LOGOUT."\"><img src=\"".img_find("all/exit")."\" border=\"0\" alt=\""._LOGOUT."\"></td><td><a href=\"index.php?name=account&op=logout\" title=\""._LOGOUT."\">"._LOGOUT."</a></td></tr></table>";
} else {
	
	$content = "<center><img src=\"".$confu['adirectory']."/0.gif\"><br>"
	."<b>"._WELCOMETO.",<br><span style=\"font-size: 16px;color: green;\">".$confu['anonym']."</b></span></center><hr>"
	."<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><form action=\"index.php?name=account\" method=\"post\">"
	."<tr valign=\"middle\"><td><a href=\"index.php?name=account&op=newuser\" title=\""._BREG."\"><img src=\"".img_find("all/newuser")."\" border=\"0\" alt=\""._BREG."\"></td><td><a href=\"index.php?name=account&op=newuser\" title=\""._BREG."\">"._BREG."</a></td></tr>"
	."<tr valign=\"middle\"><td><a href=\"index.php?name=account&op=passlost\" title=\""._PASSFOR."\"><img src=\"".img_find("all/passlost")."\" border=\"0\" alt=\""._PASSFOR."\"></td><td><a href=\"index.php?name=account&op=passlost\" title=\""._PASSFOR."\">"._PASSFOR."</a></td></tr></table><hr>"
	."<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"1\" align=\"center\">"
	."<tr><td>"._NICKNAME.":</td><td><input type=\"text\" name=\"user_name\" size=\"10\" maxlength=\"25\" class=\"binput\"></td></tr>"
	."<tr><td>"._PASSWORD.":</td><td><input type=\"password\" name=\"user_password\" size=\"10\" maxlength=\"25\" class=\"binput\"></td></tr>";
	if (extension_loaded("gd") && ($conf['gfx_chk'] == 2 || $conf['gfx_chk'] == 4 || $conf['gfx_chk'] == 5 || $conf['gfx_chk'] == 7)) {
		$content .= "<tr><td>"._CODE.":</td><td><img src=\"captcha.php\" onclick=\"if(!this.adress)this.adress = this.src; this.src=adress+'?rand='+Math.random();\" border=\"1\" title=\"Нажмите, чтобы обновить картинку\" style=\"cursor:pointer;\" alt=\""._SECURITYCODE."\"></td></tr>"
		."<tr><td>"._TYPESECCODE.":</td><td><input type=\"text\" name=\"check\" size=\"10\" maxlength=\"6\" style=\"width: 75px;\" class=\"binput\"></td></tr>";
	}
	$content .= "<tr><td colspan=\"2\" align=\"center\"><input type=\"hidden\" name=\"op\" value=\"login\"><input type=\"submit\" value=\""._LOGIN."\" class=\"fbutton\"></td></tr></form></table>";
$content .= "<br><span style=\"font-size: 12px;margin:4px; padding:4px; border: outset #FFCF4C;background-color: #FFCF4C;;color: green;cursor: help;\"><img src=\"images/loginza_widget.gif\"><script src=\"ajax/loginza.js\" type=\"text/javascript\"></script>
 <a href=\"https://loginza.ru/api/widget?token_url=http://news.maximuma.net/index.php?name=loginza\" class=\"loginza\">Войти через OpenID</a></span>";

}

if (is_user()) {


	$newpms = $db->sql_numrows($db->sql_query("SELECT id, t_user FROM ".$prefix."_private WHERE t_user = '$uname' AND status !='1' AND status !='3'AND status !='4' "));
     if ($newpms != 0) {
	$content .= "<table border=\"0\"><tr valign=\"middle\"><td><a href=\"index.php?name=private\"><img src=\"images/private/pm.gif\" border=\"0\" title=\""._BUNREAD."\" hspace=\"4\" alt=\"\"></a></td><td><a href=\"index.php?name=private\" title=\""._BUNREAD."\"> Cообщения</a> : $newpms</td></tr></table>";
     } else {
	$content .= "<table border=\"0\"><tr valign=\"middle\"><td><a href=\"index.php?name=private\"><img src=\"".img_find("all/private")."\" border=\"0\" title=\"Нет новых сообщений\" alt=\"\"></a></td><td><a href=\"index.php?name=private\" title=\"Нет новых сообщений\">Cообщения</a> : $newpms</td></tr></table>";
     }}
if ($conf['session']) $content .= "<div id=\"repsinfo\">".user_sinfo(1)."</div>";

if (!defined('BLOCK_FILE')) {
	Header("Location: ../index.php");
	exit;
}
global $prefix, $db;


$cmreload=60;
$cmip=$_SERVER['REMOTE_ADDR'];
$cmzeit=time();
$cmheute=date("l");

global $db, $prefix, $bgcolor1, $bgcolor2, $bgcolor3, $bgcolor4;
$online = $db->sql_fetchrow($db->sql_query("SELECT count(*) FROM ".$prefix."_session", $db));

# Abfragen ob Tabelle existiert (ggf. schreiben)
$db->sql_query("CREATE TABLE IF NOT EXISTS ".$prefix."_countomat(
Monday int(10) NOT NULL default '0',
Tuesday int(10) NOT NULL default '0',
Wednesday int(10) NOT NULL default '0',
Thursday int(10) NOT NULL default '0',
Friday int(10) NOT NULL default '0',
Saturday int(10) NOT NULL default '0',
Sunday int(10) NOT NULL default '0',
gesamt int(10) NOT NULL default '0',
rekord int(10) NOT NULL default '0',
heute varchar(10) NOT NULL default '*')");
$db->sql_query("CREATE TABLE IF NOT EXISTS ".$prefix."_countomat_ip(
Zeit varchar(10) NOT NULL default '0',
Ip varchar(15) NOT NULL default '0')");


$db->sql_query("CREATE TABLE IF NOT EXISTS ".$prefix."_session (
 `session_ip` varchar(16) NOT NULL default '',
 `session_time` int(11) unsigned NOT NULL default '0',
 PRIMARY KEY  (`session_ip`)
)");

$online = $db->sql_fetchrow($db->sql_query("SELECT count(*) FROM ".$prefix."_session", $db));


# Tageswechsel
$anfrage = $db->sql_query("select * from ".$prefix."_countomat",$db);
if($dat = $db->sql_fetchrow($anfrage)) {
if ($cmheute!=$dat['heute']) {
$sql = "DELETE FROM ".$prefix."_countomat_ip";
$result = $db->sql_query($sql,$db);
$sql = "UPDATE ".$prefix."_countomat SET $cmheute='0', heute='$cmheute'";
$result = $db->sql_query($sql,$db);
}} else { $db->sql_query("INSERT INTO ".$prefix."_countomat VALUES (0,0,0,0,0,0,0,0,0,'$cmheute')");}

# Lцschen nach x Minuten
$sql = "DELETE FROM ".$prefix."_countomat_ip WHERE Zeit<".($cmzeit-($cmreload*60));
$result = $db->sql_query($sql,$db);

# User abfragen
$anfrage2 = $db->sql_query("select * from ".$prefix."_countomat_ip WHERE ip='$cmip'",$db);
if($dat2 = $db->sql_fetchrow($anfrage2)) {
$sql = "UPDATE ".$prefix."_countomat_ip SET zeit='$cmzeit' WHERE ip='$cmip'";
$result = $db->sql_query($sql,$db);
} else {
$anfrage = $db->sql_query("select * from ".$prefix."_countomat",$db);
if($dat = $db->sql_fetchrow($anfrage)) {
$sql = "INSERT INTO ".$prefix."_countomat_ip (zeit, ip) VALUES ('$cmzeit','$cmip')";
$result = $db->sql_query($sql,$db);
if (($dat[$cmheute]+1)>$dat['rekord'])
$cmupdate=", rekord=".$dat[$cmheute]."+1";
else
$cmupdate="";
$sql = "UPDATE ".$prefix."_countomat SET $cmheute=$cmheute+1, gesamt=gesamt+1".$cmupdate;
$result = $db->sql_query($sql,$db);
} }
$content.='<div id="cont" OnClick="SwitchMenu(\'privADD2\')" style="cursor:pointer;"><br/> <center><img src=\"images/all/strelka.gif\">  <span style=\"font-size: 12px;color: red;\"><h13><b><u>»»Посещаемость««</u></b><h/13></span></center> </div>';
    
    $content.='<div id="privADD2" style="display:none;">';
# Anzeige wenn $cmanzeige
if (!$cmanzeige) {
$anfrage = $db->sql_query("select * from ".$prefix."_countomat",$db);
if($dat = $db->sql_fetchrow($anfrage)) {
$cmc = array('Monday' => $bgcolor1, 'Tuesday' => $bgcolor3, 'Wednesday' => $bgcolor1, 'Thursday' => $bgcolor3, 'Friday' => $bgcolor1, 'Saturday' => $bgcolor3, 'Sunday' => $bgcolor1);
if ($dat["heute"]==$cmheute)
$cmc[$cmheute]=$bgcolor2;
$dat[$cmheute]="<span style=\"font-size: 12px;color: blue;\"><b>".$dat[$cmheute]."</b></span>";
$content .= "<table border=0 width=\"100%\" cellspacing=1 cellpadding=1 align=\"center\">
<tr><td><font class=\"content\"><b><i>Понедельник</i></b></font></td><td align=\"right\"><font class=\"content\">".$dat["Monday"]."</font></td></tr>
<tr><td><font class=\"content\"><b><i>Вторник</i></b></font></td><td align=\"right\"><font class=\"content\">".$dat["Tuesday"]."</font></td></tr>
<tr><td><font class=\"content\"><b><i>Среда</i></b></font></td><td align=\"right\"><font class=\"content\">".$dat["Wednesday"]."</font></td></tr>
<tr><td><font class=\"content\"><b><i>Четверг</i></b></font></td><td align=\"right\"><font class=\"content\">".$dat["Thursday"]."</font></td></tr>
<tr><td><font class=\"content\"><b><i>Пятница</i></b></font></td><td align=\"right\"><font class=\"content\">".$dat["Friday"]."</font></td></tr>
<tr><td><font class=\"content\"><b><i><u> Суббота</u></i></b></font></td><td align=\"right\"><font class=\"content\">".$dat["Saturday"]."</font></td></tr>
<tr><td><font class=\"content\"><b><i><u> Воскресенье</u></i></b></font></td><td align=\"right\"><font class=\"content\">".$dat["Sunday"]."</font></td></tr>

<tr><td align=\"right\"><font class=\"content\"><b>Было всего:</b></font></td><td align=\"right\"><font class=\"content\"><span style=\"font-size: 12px;color: green;\"><b>".$dat["gesamt"]."</b></span></font></td></tr>

<tr><td align=\"right\"><font class=\"content\"><b>Рекорд:</b></font></td><td align=\"right\" bgcolor=\"$bgcolor2\"><font class=\"content\"><span style=\"font-size: 12px;color: red;\"><b>".$dat["rekord"]."</b></span></font></td></tr>
</table>";$content.='</div>';
}}




?>