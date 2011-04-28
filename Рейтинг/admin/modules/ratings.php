<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

function ratings() {
	global $admin_file;
	head();
	panel();
	title(""._RATINGS."");
	ratings_navig();
	include("config/config_ratings.php");
	$permtest = end_chmod("config/config_ratings.php", 666);
	if ($permtest) warning($permtest, "", "", 1);
	##Свой модуль-1##
	$mods = array("account", "files", "news");
	$lang = array(_ACCOUNT, _FILES, _NEWS);
	##Свой модуль-1##
	$i = 0;
	$content="<div class=\"left\">"._NEW_RATE_22."</div><div class=\"center\"><select name='useronly'><option value='1'".(($nnewrate['useronly']==1)?" selected='selected'":"").">"._NEW_RATE_26."</option><option value='0'".(($nnewrate['useronly']==0)?" selected='selected'":"").">"._NEW_RATE_27."</option><option value='2'".(($nnewrate['useronly']==2)?" selected='selected'":"").">"._NEW_RATE_28."</option></select></div>";
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
	##Свой модуль-2##
	$mods = array("account", "files", "news");
	##Свой модуль-2##
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

function ratings_navig() {
global $admin_file;
open();
echo "<h5>[ <a href=\"".$admin_file.".php?op=ratings\">"._NEW_RATE_31."</a> | <a href=\"".$admin_file.".php?op=ratings_whoiswho\">"._NEW_RATE_30."</a> ]</h5>";
close();
}

function ratings_whoiswho () {
global $db,$prefix,$admin_file;
##Свой модуль-3##
$arr=array('news'=>_NEW_RATE_36,'files'=>_NEW_RATE_37,'account'=>_NEW_RATE_38);
##Свой модуль-3##
$conf['anum']=10;
$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
$offset = ($num-1) * $conf['anum'] ;
head();
panel();
title(_NEW_RATE_30);
ratings_navig();
open();
$s=$ss='';
if (isset($_GET['filter']) && isset($_GET['var'])) {
if (is_array($_GET['filter'])) foreach ($_GET['filter'] as $a=>$b) {$s[]=text_filter($b)."='".text_filter($_GET['var'][$a])."'";$ss[]="w.".text_filter($b)."='".text_filter($_GET['var'][$a])."'";}
else {$s[]=" ".text_filter($_GET['filter'])."='".text_filter($_GET['var'])."'";$ss[]=" w.".text_filter($_GET['filter'])."='".text_filter($_GET['var'])."'";}
if ($s!='') {$s=' WHERE '.implode(' AND ',$s);$ss=' WHERE '.implode(' AND ',$ss);}
unset($a);
}
$result = $db->sql_query("SELECT w.id, w.iid, w.module, w.uid, w.date, w.ip, w.vote, w.comment, u.user_name FROM ".$prefix."_whoiswho AS w LEFT JOIN ".$prefix."_users AS u ON (w.uid=u.user_id)".$ss." ORDER BY w.date DESC LIMIT ".$offset.", ".$conf['anum']);
if ($db->sql_numrows($result) > 0) {
while (list($id,$iid,$mod,$uid,$date,$ip,$vote,$com,$user) = $db->sql_fetchrow($result)) {
$out['ids'][$mod][]=$iid;
$out['ratings'][$id]=array('mod'=>$mod,'iid'=>$iid,'uid'=>$uid,'date'=>$date,'vote'=>$vote,'comment'=>$com,'name'=>$user,'ip'=>$ip);
}
foreach ($out['ids'] as $a=>$b) {
if ($a=='news') {
$res=$db->sql_query("SELECT `sid`,`title` FROM  ".$prefix."_stories WHERE `sid` IN (".implode(',',$b).")");
while(list($id,$title) = $db->sql_fetchrow($res)) {$in[$a][$id]=array('title'=>$title,'url'=>'index.php?name='.$a.'&op=view&id='.$id);}
} elseif ($a=='files') {
$res=$db->sql_query("SELECT `lid`,`title` FROM  ".$prefix."_files WHERE `lid` IN (".implode(',',$b).")");
while(list($id,$title) = $db->sql_fetchrow($res)) {$in[$a][$id]=array('title'=>$title,'url'=>'index.php?name='.$a.'&op=view&id='.$id);}
##Свой модуль-4##
} elseif ($a=='account') {
$res=$db->sql_query("SELECT `user_id`,`user_name` FROM  ".$prefix."_users WHERE `user_id` IN (".implode(',',$b).")");
while(list($id,$title) = $db->sql_fetchrow($res)) {$in[$a][$id]=array('title'=>_NEW_RATE_32.$title,'url'=>'index.php?name='.$a.'&op=info&uname='.$title);}
}
##Свой модуль-4##
unset($res,$id,$title);
}
unset($a,$b);
echo "<script language='JavaScript' type='text/javascript'>
function CheckAll(Element,Name){if(document.getElementById) {thisCheckBoxes =  document.getElementById('del_rate').getElementsByTagName('input'); for (i = 1; i < thisCheckBoxes.length; i++){if (thisCheckBoxes[i].className == Name){thisCheckBoxes[i].checked = Element.checked; Colorize(document.getElementById(thisCheckBoxes[i].id.replace('cb','tr')), thisCheckBoxes[i]); } } } } 
function Colorize(Element, CBElement){if(document.getElementById) {if(Element && CBElement){Element.className = ( CBElement.checked ? 'selected' : 'default' );}}} 
function CheckCB(Element){if(document.getElementById) {if(document.getElementById(Element.id.replace('cb','tr'))){Element.checked = !Element.checked;}}}
</script>";
echo "<form action='".$admin_file.".php' method='post' id='del_rate'>";
echo "<table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_id'><tr>"
."<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick='CheckAll(this,\"ids_del\")' type='checkbox' value='delete'>"._NEW_RATE_40."</span>"
."<th>"._NEW_RATE_33."</th><th>"._NEW_RATE_34."</th><th>"._DATE."</th><th>"._COMMENT."</th><th>"._NEW_RATE_35."</th><th>"._FUNCTIONS."</th></tr>";
foreach ($out['ratings'] as $a=>$b) {
if ($b['uid']>0) $name='<a href="'.$admin_file.'.php?op=ratings_whoiswho&filter=uid&var='.$b['uid'].'" style="font-weight:bold;" title="'._NEW_RATE_43.'">[?]</a>&nbsp;'.user_info($b['name'],1);
else $name='<a href="'.$admin_file.'.php?op=ratings_whoiswho&filter=ip&var='.$b['ip'].'" style="font-weight:bold;" title="'._NEW_RATE_44.'">[?]</a>&nbsp;<font color="#FF5000">'.$b['ip'].'</font>';
if ($b['vote']==1) $rate='<font color="green" style="font-weight:bold;">+1</font> <a href="'.$admin_file.'.php?op=ratings_whoiswho&filter=vote&var=1" style="font-weight:bold;" title="'._NEW_RATE_45.'">[?]</a>';
else $rate='<font color="red" style="font-weight:bold;">-1</font> <a href="'.$admin_file.'.php?op=ratings_whoiswho&filter=vote&var=-1" style="font-weight:bold;" title="'._NEW_RATE_46.'">[?]</a>';
echo "<tr class='bgcolor1'>"
."<td align='left'><a href='".$admin_file.".php?op=ratings_whoiswho&filter=module&var=".$b['mod']."' style='font-weight:bold;' title='"._NEW_RATE_47."'>[?]</a>&nbsp;".$arr[$b['mod']].'<br /><a href="'.$admin_file.'.php?op=ratings_whoiswho&filter[]=module&filter[]=iid&var[]='.$b['mod'].'&var[]='.$b['iid'].'" style="font-weight:bold;" title="'._NEW_RATE_42.'">[?]</a> <a href="'.$in[$b['mod']][$b['iid']]['url'].'" title="'._NEW_RATE_39.'">'.$in[$b['mod']][$b['iid']]['title']."</a></td>"
."<td align='left'>".$name."</td>"
."<td align='center'>".$b['date']."</td>"
."<td align='left'>".$b['comment']."</td>"
."<td align='center'>".$rate."</td>"
."<td align='center'><input class='ids_del' type='checkbox' name='delete[".$b['mod']."][$a]' value='".$b['iid']."'><input type='hidden' name='rat[$a]' value='".(($b['vote']==1)?1:0)."'></td>"
."</tr>";
}
echo "</table>";
echo "<div class='button'><input type='hidden' name='op' value='ratings_delete'><input type='submit' value='"._NEW_RATE_41."' class='fbutton'></div></form>";
list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_whoiswho".$s));
$numpages = ceil($numstories/$conf['anum']);
num_page("", $numstories, $numpages, $conf['anum'], "op=ratings_whoiswho&");
} else warning(_NO_INFO, "", "", 2);
close();
foot();
}

function ratings_delete() {
global $db,$prefix,$admin_file;
if (count($_POST['delete'])>0) {
foreach ($_POST['delete'] as $a=>$b) {
foreach ($b as $c=>$d) {
$e[]=$c;
if ($a=='news') {
$db->sql_query("UPDATE ".$prefix."_stories SET ratings=ratings-1, score=score-5*".$_POST['rat'][$c]." WHERE `sid`='$d'");
} elseif ($a=='files') {
$db->sql_query("UPDATE ".$prefix."_files SET votes=votes-1, totalvotes=totalvotes-5*".$_POST['rat'][$c]." WHERE `lid`='$d'");
##Свой модуль-5##
} elseif ($a=='account') {
$db->sql_query("UPDATE ".$prefix."_users SET user_votes=user_votes-1, user_totalvotes=user_totalvotes-5*".$_POST['rat'][$c]." WHERE `user_id`='$d'");
}
##Свой модуль-5##
}
}
$db->sql_query("DELETE FROM  ".$prefix."_whoiswho WHERE `id` IN (".implode(',',$e).")");
}
referer($admin_file.".php?op=ratings_whoiswho");
}

switch($op) {
	case "ratings":
	ratings();
	break;
	
	case "ratings_save_conf":
	ratings_save_conf();
	break;
	
	case "ratings_whoiswho":
	ratings_whoiswho();
	break;
	
	case "ratings_delete":
	ratings_delete();
	break;
}
?>