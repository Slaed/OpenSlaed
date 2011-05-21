<?php
if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

include('config/config_invate.php');

function invites_navi($a='main') {
global $admin_file;
open();
if ($a=='main') {
echo "<h5>[ <a href=\"".$admin_file.".php?op=invites\">"._INVATE_A_1."</a>"
." | <a href=\"".$admin_file.".php?op=invites_create\">"._INVATE_A_14."</a>"
." | <a href=\"".$admin_file.".php?op=invites_points\">"._INVATE_A_15."</a>"
." | <a href=\"".$admin_file.".php?op=invites_conf\">"._INVATE_A_2."</a> ]</h5>";
} elseif ($a=='points') {
echo "<h5>[ <a href=\"".$admin_file.".php?op=invites_points\">"._INVATE_A_15."</a>"
." | <a href=\"".$admin_file.".php?op=invites_bonus\">"._INVATE_A_17."</a> ]</h5>";
} elseif ($a=='invites') {
echo "<h5>[ <a href=\"".$admin_file.".php?op=invites\">"._INVATE_A_37."</a>"
." | <a href=\"".$admin_file.".php?op=invites&status=2\">"._INVATE_A_12."</a>"
." | <a href=\"".$admin_file.".php?op=invites&status=3\">"._INVATE_A_13."</a> ]</h5>";
}
close();
}

function invites_conf () {
global $admin_file,$invate;
$permtest = end_chmod("config/config_invate.php", 666);
head();
panel();
title(_INVATE_A_1);
invites_navi();
open();
if ($permtest) warning($permtest, "", "", 1);
$content .= "
<h2>"._INVATE_A_11."</h2>"
."<div class='left'>"._INVATE_A_3."</div><div class='center'>".radio_form($invate['status'],"status")."</div>"
."<div class='left'>"._INVATE_A_4."</div><div class='center'>".radio_form($invate['email'],"email")."</div>"
."<div class='left'>"._INVATE_A_5."</div><div class='center'><input type='text' name='coast' value='".intval($invate['coast'])."' maxlength='5' size='45' class='admin'></div>"
."<div class='left'>"._INVATE_A_6."</div><div class='center'><input type='text' name='live' value='".intval($invate['live'])."' maxlength='3' size='45' class='admin'>"._INVATE_A_10."</div>"
."<div class='left'>"._INVATE_A_7."</div><div class='center'><input type='text' name='mindate' value='".intval($invate['mindate'])."' maxlength='3' size='45' class='admin'>"._INVATE_A_10."</div>"
."<div class='left'>"._INVATE_A_8."</div><div class='center'><input type='text' name='title' value='".stripslashes($invate['title'])."' maxlength='400' size='45' class='admin'></div>"
."<div class='left'>"._INVATE_A_9._INVATE_A_16."</div><div class='center'><textarea name='text' cols='65' rows='25' class='admin' wrap='off'>".stripslashes($invate['text'])."</textarea></div></div>";
echo "<form action='".$admin_file.".php' method='post'>";
echo $content;
echo "<div class='button'><input type='hidden' name='op' value='invites_conf_save'><input type='submit' value='"._SAVECHANGES."' class='fbutton'></div></form>";
close();
foot();
}

function invites_conf_save() {
global $admin_file;
$content = "\$invate['status'] = ".intval($_POST['status']).";\n";
$content .= "\$invate['email'] = ".intval($_POST['email']).";\n";
$content .= "\$invate['coast'] = ".intval($_POST['coast']).";\n";
$content .= "\$invate['live'] = ".intval($_POST['live']).";\n";
$content .= "\$invate['mindate'] = ".intval($_POST['mindate']).";\n";
$content .= "\$invate['text'] = '".$_POST['text']."';\n";
$content .= "\$invate['title'] = '".$_POST['title']."';\n";
save_conf("config/config_invate.php", $content);
Header("Location: ".$admin_file.".php?op=invites_conf");
}

function invites_points() {
global $db,$prefix,$admin_file;
head();
panel();
title(_INVATE_A_1);
invites_navi();
invites_navi('points');
warning(_INVATE_A_24, "", "", 2);
open();
$conf['anum']=10;
$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
$offset = ($num-1) * $conf['anum'];
$sql=$db->sql_query("SELECT i.points,i.bonus,i.expend,u.user_name,u.user_id FROM ".$prefix."_invates_points AS i LEFT JOIN ".$prefix."_users AS u ON (u.user_id=i.uid) ORDER BY i.id DESC LIMIT $offset, ".$conf['anum']);
list($count) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_invates_points"));
if ($count > 0) {
echo "<table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_id'><tr>"
."<th>"._INVATE_A_22."</th><th>"._INVATE_A_18."</th><th>"._INVATE_A_20."</th><th>"._INVATE_A_19."</th><th>"._INVATE_A_21."</th></tr>";
while(list($points,$bonus,$expend,$name,$uid) = $db->sql_fetchrow($sql)) {
echo "<tr class='bgcolor1'>"
."<td align='left'><a href='$admin_file.php?op=invites&status=4&uid=$uid' title='"._INVATE_A_25."'><b>[?]</b></a>&nbsp;<a href='index.php?name=account&op=info&uname=$name' title='"._INVATE_A_23."'>$name</a></td>"
."<td align='center'><b style='color:green;'>".$points."</b></td>"
."<td align='center'><a href='$admin_file.php?op=invites_bonus&filter=uid&var=$uid' title='"._INVATE_A_26."'><b>[?]</b></a>&nbsp;<b style='color:blue;'>".$bonus."</b>&nbsp;<a href='$admin_file.php?op=invites_bonus&add_bonus=$name' title='"._INVATE_A_27."'><b>[+/-]</b></a></td>"
."<td align='center'><b style='color:red;'>".$expend."</b></td>"
."<td align='center'><b style='color:#FF5000;'>".($points+$bonus-$expend)."</b></td>"
."</tr>";
}
echo "</table><div>&nbsp;</div>";
$numpages = ceil($count/$conf['anum']);
num_page("", $count, $numpages, $conf['anum'], "op=invites_points&");
} else warning(_NO_INFO, "", "", 2);
close();
foot();
}

function invites_bonus() {
global $db,$prefix,$admin_file;
head();
panel();
title(_INVATE_A_1);
invites_navi();
invites_navi('points');
open();
warning(_INVATE_A_35, "", "", 2);
echo "<form action='".$admin_file.".php' method='post' id='del_rate'>";
echo "<table width='100%' border='0' cellpadding='2' cellspacing='1'><tr class='bgcolor1'><td style='color:green;font-weight:bold;'>"._INVATE_A_33."&nbsp;</td><td><input type='text' name='users' size='55' value='".((isset($_GET['add_bonus']))?text_filter($_GET['add_bonus']):'')."' /></td><td style='color:#FF5000;font-weight:bold;'>&nbsp;"._INVATE_A_34."&nbsp;</td><td><input type='text' size='10' name='bonus' /></td></tr></table>";
echo "<div class='button'><input type='hidden' name='op' value='invites_bonus_add'><input type='submit' value='"._INVATE_A_36."' class='fbutton'></div></form>";
close();
open();
$conf['anum']=10;
$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
$offset = ($num-1) * $conf['anum'];
if (isset($_GET['filter'], $_GET['var'])) {$where=array(0=>" WHERE i.".text_filter($_GET['filter'])."='".text_filter($_GET['var'])."'",1=>" WHERE ".text_filter($_GET['filter'])."='".text_filter($_GET['var'])."'");$url[]="filter=".text_filter($_GET['filter'])."&var=".text_filter($_GET['var'])."&";}
else $url=$where=array();
$sql=$db->sql_query("SELECT i.id,i.count,i.date,u.user_name,u.user_id FROM ".$prefix."_invates_bonus AS i LEFT JOIN ".$prefix."_users AS u ON (u.user_id=i.uid)".$where[0]." ORDER BY i.date DESC LIMIT $offset, ".$conf['anum']);
list($count) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_invates_bonus".$where[1]));
if ($count > 0) {
warning(_INVATE_A_32, "", "", 2);
echo '<script type="text/javascript">function checkAll(oForm, cbName, checked){for (var i=0; i < oForm[cbName].length; i++) oForm[cbName][i].checked = checked;}</script>';
echo "<form action='".$admin_file.".php' method='post' id='del_rate'>";
echo "<table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_id'><tr>"
."<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick=\"checkAll(this.form,'delete[]',this.checked)\" type='checkbox' value='delete'> "._INVATE_A_31."</span>"
."<th>"._INVATE_A_22."</th><th>"._INVATE_A_28."</th><th>"._INVATE_A_20."</th><th>"._INVATE_A_29."</th></tr>";
while(list($id,$bonus,$date,$name,$uid) = $db->sql_fetchrow($sql)) {
echo "<tr class='bgcolor1'>"
."<td align='left'><a href='$admin_file.php?op=invites&status=4&uid=$uid' title='"._INVATE_A_25."'><b>[?]</b></a>&nbsp;<a href='index.php?name=account&op=info&uname=$name' title='"._INVATE_A_23."'>$name</a></td>"
."<td align='center'><b style='color:green;'>".$date."</b></td>"
."<td align='center'><a href='$admin_file.php?op=invites_bonus&filter=uid&var=$uid' title='"._INVATE_A_26."'><b>[?]</b></a>&nbsp;<b style='color:blue;'>".$bonus."</b>&nbsp;<a href='$admin_file.php?op=invites_bonus&add_bonus=$name' title='"._INVATE_A_27."'><b>[+/-]</b></a></td>"
."<td align='center'><input type='checkbox' name='delete[]' value='".$id."'></td>"
."</tr>";
}
echo "</table>";
echo "<div class='button'><input type='hidden' name='op' value='invites_bonus_del'><input type='submit' value='"._INVATE_A_30."' class='fbutton'></div></form><div>&nbsp;</div>";
$numpages = ceil($count/$conf['anum']);
num_page("", $count, $numpages, $conf['anum'], "op=invites_bonus&".$url[0]);
} else warning(_NO_INFO, "", "", 2);
close();
foot();
}

function invites_create() {
global $db,$prefix,$admin_file,$invate;
head();
panel();
if (!function_exists('invate_date')) include ('config/config_function.php');
title(_INVATE_A_1);
invites_navi();
warning(_INVATE_A_41, "", "", 1);
open();
warning(_INVATE_A_38, "", "", 2);
echo "<form action='".$admin_file.".php' method='post' id='del_rate'>";
echo "<table width='100%' border='0' cellpadding='2' cellspacing='1'><tr class='bgcolor1'><td style='color:green;font-weight:bold;'>"._INVATE_A_39."&nbsp;</td><td><input type='text' name='mail' size='35' value='' /></td><td style='color:#FF5000;font-weight:bold;'>&nbsp;"._INVATE_A_6."&nbsp;</td><td><input type='text' size='30' name='live' value='".$invate['live']."' /></td></tr></table>";
echo "<div class='button'><input type='hidden' name='op' value='invites_create_add'><input type='submit' value='"._INVATE_A_40."' class='fbutton'></div></form>";
close();
open();
$conf['anum']=10;
$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
$offset = ($num-1) * $conf['anum'];
$where=array();
if (isset($_GET['status'])) {
if (intval($_GET['status'])==0) {
$where[]=" AND i.end_date=0";
$where[]=" AND end_date=0";
} elseif (intval($_GET['status'])==1) {
$where[]=" AND i.end_date!=0";
$where[]=" AND end_date!=0";
} elseif (intval($_GET['status'])==2) {
$where[]=" AND i.nuid='0'";
$where[]=" AND nuid='0'";
} elseif (intval($_GET['status'])==3) {
$where[]=" AND i.nuid!='0'";
$where[]=" AND nuid!='0'";
}
$url[]='status='.intval($_GET['status']).'&';
}

$sql=$db->sql_query("SELECT i.id,i.invate,i.email,i.nuid,i.date,i.end_date,i.coast,u.user_id FROM ".$prefix."_invates AS i LEFT JOIN ".$prefix."_users AS u ON (u.user_name=i.nuid) WHERE i.uid='-1'".$where[0]." ORDER BY i.date DESC LIMIT $offset, ".$conf['anum']);
list($count) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_invates WHERE uid='-1'".$where[1]));
if ($count > 0) {
echo '<script type="text/javascript">function invate_select(elm) {if(typeof(elm) == "string") elm = getElementById(elm);if (elm) {elm.focus(); elm.select();}} function checkAll(oForm, cbName, checked){for (var i=0; i < oForm[cbName].length; i++) oForm[cbName][i].checked = checked;}</script>';
echo "<form action='".$admin_file.".php' method='post' id='del_rate'>";
echo "<table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_id'><tr>"
."<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick=\"checkAll(this.form,'id[]',this.checked)\" type='checkbox' value='delete'> "._INVATE_A_31."</span>"
."<th>"._INVATE_A_42."</th><th>"._INVATE_A_44."</th><th>"._INVATE_A_45."</th><th>"._INVATE_A_46."</th><th>"._INVATE_A_48."</th></tr>";
while(list($id,$invt,$mail,$name,$date,$end_date,$coast,$nuid) = $db->sql_fetchrow($sql)) {
if ($name=='0') $name="<b style='color:red;'><a href='$admin_file.php?op=invites_create&status=2' title='"._INVATE_A_52."'>[?]</a>&nbsp;"._INVATE_39."</span></b>";
elseif ($nuid) $name="<b><a href='$admin_file.php?op=invites_create&status=3' title='"._INVATE_A_51."'>[?]</a>&nbsp;<a href='index.php?name=account&op=info&uname=$name' title='"._INVATE_40."'>$name</a></b>";
else $name="<b style='color:blue;'><a href='$admin_file.php?op=invites_create&status=3' title='"._INVATE_A_51."'>[?]</a>&nbsp;<span title='"._INVATE_58."'>$name</span></b>";
echo "<tr class='bgcolor1'>"
."<td align='left'><a href='index.php?name=account&op=newuser&invate=$invt".(($mail!='')?"&mail=$mail":"")."'><img src='/images/invate/icon.png' title='"._INVATE_41."' /></a>&nbsp;<textarea style='overflow:hidden;' wrap='off' rows='1' cols='30' onclick='invate_select(this);' onblur='this.value +=\" \";this.value=this.value.slice(0, -1);'>$invt</textarea></td>"
."<td align='left'>".(($mail!='')?"<b><a href='mailto:$mail' title='"._INVATE_A_49.$mail."'>@</a></b>&nbsp;":'').$name."</td>"
."<td align='center'><b style='color:green;'>".invate_date($date,true)."</b></td>"
."<td align='left'><b style='color:".(($end_date==0)?'blue':'#FF5000').";'>".(($end_date==0)?"<a href='$admin_file.php?op=invites_create&status=0'>[?]</a>&nbsp;"._INVATE_37:"<a href='$admin_file.php?op=invites_create&status=1'>[?]</a>&nbsp;".invate_date($end_date,true))."</b></td>"
."<td align='center'><input type='checkbox' name='id[]' value='".$id."'></td>"
."</tr>";
}
echo "</table>";
echo "<div class='button'><input type='hidden' name='op' value='invites_invites_del'><input type='submit' value='"._INVATE_A_50."' class='fbutton'></div></form><div>&nbsp;</div>";
$numpages = ceil($count/$conf['anum']);
num_page("", $count, $numpages, $conf['anum'], "op=invites_create&".$url[0]);
} else warning(_NO_INFO, "", "", 2);
close();
foot();
}

function invites() {
global $db,$prefix,$admin_file,$invate;
head();
panel();
if (!function_exists('invate_date')) include ('config/config_function.php');
title(_INVATE_A_1);
invites_navi();
warning(_INVATE_A_41, "", "", 1);
invites_navi('invites');
open();
$conf['anum']=10;
$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
$offset = ($num-1) * $conf['anum'];
$where=array();
if (isset($_GET['status'])) {
$url[0]='status='.intval($_GET['status']).'&';
if (intval($_GET['status'])==0) {
$where[]=" WHERE i.end_date=0";
$where[]=" WHERE end_date=0";
} elseif (intval($_GET['status'])==1) {
$where[]=" WHERE i.end_date!=0";
$where[]=" WHERE end_date!=0";
} elseif (intval($_GET['status'])==2) {
$where[]=" WHERE i.nuid='0'";
$where[]=" WHERE nuid='0'";
} elseif (intval($_GET['status'])==3) {
$where[]=" WHERE i.nuid!='0'";
$where[]=" WHERE nuid!='0'";
} elseif (intval($_GET['status'])==4) {
$where[]=" WHERE i.uid=".intval($_GET['uid']);
$where[]=" WHERE uid=".intval($_GET['uid']);
$url[0].='uid='.intval($_GET['uid']).'&';
}
}
$sql=$db->sql_query("SELECT i.uid,i.id,i.invate,i.email,i.nuid,i.date,i.end_date,i.coast,u.user_id,m.user_name FROM ".$prefix."_invates AS i LEFT JOIN ".$prefix."_users AS u ON (u.user_name=i.nuid) LEFT JOIN ".$prefix."_users AS m ON (i.uid=m.user_id)".$where[0]." ORDER BY i.date DESC LIMIT $offset, ".$conf['anum']);
list($count) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_invates".$where[1]));
if ($count > 0) {
echo '<script type="text/javascript">function invate_select(elm) {if(typeof(elm) == "string") elm = getElementById(elm);if (elm) {elm.focus(); elm.select();}} function checkAll(oForm, cbName, checked){for (var i=0; i < oForm[cbName].length; i++) oForm[cbName][i].checked = checked;}</script>';
echo "<form action='".$admin_file.".php?h=1' method='post' id='del_rate'>";
echo "<table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_id'><tr>"
."<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick=\"checkAll(this.form,'id[]',this.checked)\" type='checkbox' value='delete'> "._INVATE_A_31."</span>"
."<th>"._INVATE_A_42."</th><th>"._INVATE_A_43."</th><th>"._INVATE_A_44."</th><th>"._INVATE_A_45."</th><th>"._INVATE_A_46."</th><th>"._INVATE_A_47."</th><th>"._INVATE_A_48."</th></tr>";
while(list($uid,$id,$invt,$mail,$name,$date,$end_date,$coast,$nuid,$username) = $db->sql_fetchrow($sql)) {
if ($uid==-1) {$coast='-';$bonus='';}
else $bonus="<a href='$admin_file.php?op=invites_bonus&filter=uid&var=$uid' title='"._INVATE_A_26."'><b>[Б]</b></a>&nbsp;";
if ($name=='0') $name="<b style='color:red;'><a href='$admin_file.php?op=invites&status=2' title='"._INVATE_A_52."'>[?]</a>&nbsp;"._INVATE_39."</span></b>";
elseif ($nuid) $name="<b><a href='$admin_file.php?op=invites&status=3' title='"._INVATE_A_51."'>[?]</a>&nbsp;<a href='index.php?name=account&op=info&uname=$name' title='"._INVATE_40."'>$name</a></b>";
else $name="<b style='color:blue;'><a href='$admin_file.php?op=invites&status=3' title='"._INVATE_A_51."'>[?]</a>&nbsp;<span title='"._INVATE_58."'>$name</span></b>";
echo "<tr class='bgcolor1'>"
."<td align='left'><a href='index.php?name=account&op=newuser&invate=$invt".(($mail!='')?"&mail=$mail":"")."'><img src='/images/invate/icon.png' title='"._INVATE_41."' /></a>&nbsp;<textarea style='overflow:hidden;' wrap='off' rows='1' cols='20' onclick='invate_select(this);' onblur='this.value +=\" \";this.value=this.value.slice(0, -1);'>$invt</textarea></td>"
."<td align='left'><b><a href='$admin_file.php?op=invites&status=4&uid=$uid' title='"._INVATE_A_54."'>[?]</a></b>&nbsp;".$bonus.(($uid==-1)?"<b style='color:#FF5000;'>"._INVATE_A_53."</b>":"<b><a href='index.php?name=account&op=info&uname=$username' title='"._INVATE_40."'>$username</a></b>")."</td>"
."<td align='left'>".(($mail!='')?"<b><a href='mailto:$mail' title='"._INVATE_A_49.$mail."'>@</a></b>&nbsp;":'').$name."</td>"
."<td align='center'><b style='color:green;'>".invate_date($date)."</b></td>"
."<td align='left'><b style='color:".(($end_date==0)?'blue':'#FF5000').";'>".(($end_date==0)?"<a href='$admin_file.php?op=invites&status=0'>[?]</a>&nbsp;"._INVATE_37:"<a href='$admin_file.php?op=invites&status=1'>[?]</a>&nbsp;".invate_date($end_date))."</b></td>"
."<td align='center'><b style='color:green;'>".$coast."</b></td>"
."<td align='center'><input type='checkbox' name='id[]' value='".$id."'></td>"
."</tr>";
}
echo "</table>";
echo "<div class='button'><input type='hidden' name='op' value='invites_invites_del'><input type='submit' value='"._INVATE_A_50."' class='fbutton'></div></form><div>&nbsp;</div>";
$numpages = ceil($count/$conf['anum']);
num_page("", $count, $numpages, $conf['anum'], "op=invites&".$url[0]);
} else warning(_NO_INFO, "", "", 2);
close();
foot();
}

switch ($op) {
case "invites_conf":
invites_conf();
break;

case "invites_conf_save":
invites_conf_save();
break;

case "invites_points":
invites_points();
break;

case "invites_bonus":
invites_bonus();
break;

case "invites_bonus_del":
if (count($_POST['delete'])>0) $db->sql_query("DELETE FROM  ".$prefix."_invates_bonus WHERE `id` IN (".implode(',',$_POST['delete']).")");
referer($admin_file.".php?op=invites_bonus");
break;

case "invites_bonus_add":
if (isset($_POST['users'],$_POST['bonus']) && intval($_POST['bonus'])!=0) {
foreach (explode(';',$_POST['users']) as $a) if (trim($a)!='') $u[]="'".trim($a)."'";
$sql=$db->sql_query("SELECT `user_id` FROM `".$prefix."_users` WHERE `user_name` IN (".implode(',',$u).")");
while(list($id) = $db->sql_fetchrow($sql)) $ids[]=$id;
if (count($ids)>0) {
foreach ($ids as $b) {
$db->sql_query("INSERT INTO ".$prefix."_invates_points (id,uid,points,expend,bonus) VALUES (NULL,'".$b."',0,0,".intval($_POST['bonus']).") ON DUPLICATE KEY UPDATE bonus=bonus+".$_POST['bonus']);
$db->sql_query("INSERT INTO ".$prefix."_invates_bonus (`id`,`uid`,`count`,`date`) VALUES (NULL,'".$b."','".intval($_POST['bonus'])."',now())");
}
}
}
referer($admin_file.".php?op=invites_bonus");
break;

case "invites":
invites();
break;

case "invites_create":
invites_create();
break;

case "invites_create_add":
if (!function_exists('getGuid')) include ('config/config_function.php');
$repl=invate_save(array('uid'=>'-1','expire'=>intval($_POST['live']),'mail'=>text_filter($_POST['mail'])));
if (text_filter($_POST['mail'])!='') {
$message=nl2br(strtr(stripslashes($invate['text']),$repl));
mail_send(text_filter($_POST['mail']), $conf['adminmail'], $invate['title'], $message, 0, 3);
}
referer($admin_file.".php?op=invites_create");
break;

case "invites_invites_del":
if (!function_exists('invate_expire')) include ('config/config_function.php');
invate_expire(1);
if (intval($_GET['h'])==0) referer($admin_file.".php?op=invites_create");
else referer($admin_file.".php?op=invites");
break;

}
?>