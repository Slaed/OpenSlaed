<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

function admspam_navi() {
global $admin_file, $chng_user, $search;
panel();
open();
echo "<h1>"._ANTI_SPAM_ADM_1."</h1>"
."<h5>[ <a href='".$admin_file.".php?op=admspam_show'>"._ANTI_SPAM_ADM_2."</a> | <a href='".$admin_file.".php?op=admspam_conf'>"._ANTI_SPAM_ADM_3."</a> ]</h5>";
close();
}

function admspam_show() {
global $prefix, $db, $admin_file, $conf, $confu;
$arr=array('news'=>_NEWS,'files'=>_FILES,'voting'=>_VOTING);
head();
admspam_navi();
$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
$offset = ($num-1) * $conf['anum'] ;
$result = $db->sql_query("SELECT a.host_name, a.id, a.cid, a.modul, a.name, a.comment, b.user_name, c.status FROM ".$prefix."_spambase AS c LEFT JOIN ".$prefix."_comment AS a ON (c.iid=a.id) LEFT JOIN ".$prefix."_users AS b ON (a.uid=b.user_id) WHERE c.type='comment' AND a.status='0' ORDER BY date DESC LIMIT ".$offset.", ".$conf['anum']."");
if ($db->sql_numrows($result) > 0) {
open();
echo "<script language='JavaScript' type='text/javascript'>
function CheckAll(Element,Name){if(document.getElementById) {thisCheckBoxes =  document.getElementById('spam_form').getElementsByTagName('input'); for (i = 1; i < thisCheckBoxes.length; i++){if (thisCheckBoxes[i].className == Name){thisCheckBoxes[i].checked = Element.checked; Colorize(document.getElementById(thisCheckBoxes[i].id.replace('cb','tr')), thisCheckBoxes[i]); } } } } 
function Colorize(Element, CBElement){if(document.getElementById) {if(Element && CBElement){Element.className = ( CBElement.checked ? 'selected' : 'default' );}}} 
function CheckCB(Element){if(document.getElementById) {if(document.getElementById(Element.id.replace('cb','tr'))){Element.checked = !Element.checked;}}}
</script>";
echo "<form action='".$admin_file.".php' method='post' id='spam_form'>";
echo "<table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_id'><tr>"
."<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick='CheckAll(this,\"ids_del\")' type='checkbox' value='delete'>"._ANTI_SPAM_ADM_14."</span>"
."<th>"._ANTI_SPAM_ADM_15."</th><th>"._MODUL."</th><th>"._NICKNAME."</th><th width='60%'>"._COMMENT."</th><th>"._FUNCTIONS."</th></tr>";
while (list($ip, $id, $cid, $com_modul, $com_name, $com_text, $user_name, $status) = $db->sql_fetchrow($result)) {
$status=($status==2)?'<font color="orange">'._ANTI_SPAM_ADM_17.'</font>':'<font color="green">'._ANTI_SPAM_ADM_16.'</font>';
$com_name = ($user_name)?user_info($user_name,1):(($com_name)?$com_name:$confu['anonym']);
echo "<tr class='bgcolor1'>"
."<td align='center'><b>$status</b></td>"
."<td align='center'><a href='".view_article($com_modul, $cid, $id)."' title='"._READMORE."'>$arr[$com_modul]</a></td>"
."<td align='center'>$com_name <a href='".$admin_file.".php?op=security_block&new_ip=".$ip."' style='font-weight:bold;' title='"._BANIPSENDER."'>X</a></td>"
."<td><textarea rows='6' name='update[$id][text]' wrap='off' style='width:100%;'>".$com_text."</textarea></td>"
."<td align='center'>"
." <input onclick='return CheckCB(this);' class='ids_del' name='id[]' type='checkbox' value='$id'>"
."<input name='update[$id][mod]' type='hidden' value='$com_modul'>"
."<input name='update[$id][iid]' type='hidden' value='$cid'>"
."<input name='update[$id][name]' type='hidden' value='".(($user_name)?$user_name:(($com_name)?$com_name:$confu['anonym']))."'>"
."<input name='update[$id][ip]' type='hidden' value='$ip'>"
."</td></tr>";
}
echo "</table>";
echo "<div style='margin-top:5px;float:right;'>"._ANTI_SPAM_ADM_4."<select name='nospam' style='width:200px;'><option value='1' selected='selected'>"._ANTI_SPAM_ADM_5."</option><option value='0'>"._ANTI_SPAM_ADM_6."</option></select></div>";
echo "<div style='display:block; clear:both; height:0px; line-height:0px; font-size:0px;'></div>";
echo "<div style='margin-top:5px;float:right;'><input name='send_i' type='checkbox' checked='checked' value='1'>"._ANTI_SPAM_ADM_7."</div>";
echo "<div class='button'><input type='hidden' name='op' value='spam_nospam'><input type='submit' value='"._ANTI_SPAM_ADM_8."' class='fbutton'></div></form>";
close();
list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_spambase WHERE type='comment'"));
$numpages = ceil($numstories / $conf['anum']);
num_page("", $numstories, $numpages, $conf['anum'], "op=admspam_show&");
} else warning(_NO_INFO, "", "", 2);
foot();
}

function admspam_conf() {
global $admin_file;
head();
admspam_navi();
include("config/config_spam.php");
$permtest = end_chmod("config/config_spam.php", 666);
if ($permtest) warning($permtest, "", "", 1);
open();
echo "<h2>"._GENSITEINFO."</h2>"
."<form action='".$admin_file.".php' method='post'>"
."<div class='left'>"._ANTI_SPAM_ADM_9."</div><div class='center'>".radio_form($spam['akismet'], "akismet")."</div>"
."<div class='left'>"._ANTI_SPAM_ADM_11."</div><div class='center'><input type='text' name='key' value='".$spam['key']."' size='65' class='admin'></div>"
."<div class='left'>"._ANTI_SPAM_ADM_12."</div><div class='center'>".radio_form($spam['url'], "url")."</div>"
."<div class='left'>"._ANTI_SPAM_ADM_13."</div><div class='center'>".radio_form($spam['user'], "user")."</div>"
."<div class='button'><input type='hidden' name='op' value='admspam_conf_save'><input type='submit' value='"._SAVECHANGES."' class='fbutton'></div></form>";
close();
foot();
}

function admspam_conf_save() {
global $admin_file;
$content = "\$spam['key']='".$_POST['key']."';\n"
."\$spam['akismet']=".intval($_POST['akismet']).";\n"
."\$spam['url']=".intval($_POST['url']).";\n"
."\$spam['user']=".intval($_POST['user']).";\n";
save_conf("config/config_spam.php", $content);
Header("Location: ".$admin_file.".php?op=admspam_conf");
}

function spam_nospam () {
global $prefix, $db, $admin_file;
if (intval($_POST['send_i'])==1 && count($_POST['id'])>0) {
include('config/config_spam.php');
include_once('function/akismet.class.php');
$akismet = new Akismet($conf['homeurl'], $spam['key']);
if($akismet->isKeyValid()) {
foreach ($_POST['update'] as $m=>$n) {
if (in_array($m,$_POST['id'])) {
$akismet->setCommentAuthor($n['name']);
$akismet->setCommentContent($n['text']);
$akismet->setUserIP($n['ip']);
if (intval($_POST['nospam'])!=1) $akismet->submitSpam();
else $akismet->submitHam();
}
}
}
}
if (count($_POST['id'])>0 && intval($_POST['nospam'])==1) {
$db->sql_query("UPDATE ".$prefix."_comment SET status='1' WHERE id IN (".implode(',',$_POST['id']).")");
foreach ($_POST['update'] as $a=>$b) {
if (in_array($a,$_POST['id'])) {
if ($b['mod']=='files') $db->sql_query("UPDATE ".$prefix."_files SET totalcomments=totalcomments+1 WHERE lid='".$b['iid']."'");
elseif ($b['mod']=='news') $db->sql_query("UPDATE ".$prefix."_stories SET comments=comments+1 WHERE sid='".$b['iid']."'");
elseif ($b['mod']=='voting') $db->sql_query("UPDATE ".$prefix."_survey SET pool_comments=pool_comments+1 WHERE poll_id='".$b['iid']."'");
}
}
$db->sql_query("DELETE FROM ".$prefix."_spambase WHERE iid IN (".implode(',',$_POST['id']).") AND `type`='comment'");
} elseif (count($_POST['id'])>0) {
$db->sql_query("DELETE FROM ".$prefix."_comment WHERE id IN (".implode(',',$_POST['id']).")");
$db->sql_query("DELETE FROM ".$prefix."_spambase WHERE iid IN (".implode(',',$_POST['id']).") AND `type`='comment'");
}
referer($admin_file.".php?op=admspam_show");
}

switch($op) {
	case "admspam_show":
	admspam_show();
	break;
	
	case "spam_nospam":
  spam_nospam();
	break;
	
	case "admspam_conf":
	admspam_conf();
	break;
	
	case "admspam_conf_save":
	admspam_conf_save();
	break;
}
?>