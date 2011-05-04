<?php
if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

function smtp_navi() {
global $admin_file;
open();
echo "<h5>[ <a href='".$admin_file.".php?op=smtp'>"._SMTP_A_3."</a>"
." | <a href='".$admin_file.".php?op=smtp_conf'>"._SMTP_A_2."</a> ]</h5>";
close();
}

function smtp_select ($a,$b,$c='',$d=1) { if (($b=='' && $a==$c) || $a==$b) { if ($d==1) $select='checked="checked"'; } else $select=""; return $select; }

function smtp_conf() {
global $admin_file;
head();
panel();
title(_SMTP_A_1);
smtp_navi();
include("config/config_mailer.php");
$permtest = end_chmod("config/config_mailer.php", 666);
if ($permtest) warning($permtest, "", "", 1);
open();
echo "<form name='post' action='".$admin_file.".php' method='post'>"
."<table border='0' align='center' cellpadding='3' cellspacing='0'>"
."<tr><td>"._SMTP_A_4."</td><td><input type='radio' name='smtpconf[status]' value='1' ".smtp_select('1', $smtpconf['status'], '1', 1).">"._SMTP_A_5."<input type='radio' name='smtpconf[status]' value='2' ".smtp_select('2', $smtpconf['status'], '1', 1).">"._SMTP_A_6."</td></tr>"
."<tr><td>"._SMTP_A_7."</td><td><input type='text' name='smtpconf[login]' value='".$smtpconf['login']."' maxlength='100' size='45'></td></tr>"
."<tr><td>"._SMTP_A_8."</td><td><input type='text' name='smtpconf[password]' value='".$smtpconf['password']."' maxlength='100' size='45'></td></tr>"
."<tr><td>"._SMTP_A_9."</td><td><input type='text' name='smtpconf[zone]' value='".$smtpconf['zone']."' maxlength='100' size='45'></td></tr>"
."<tr><td>"._SMTP_A_10."</td><td><input type='text' name='smtpconf[smtp]' value='".$smtpconf['smtp']."' maxlength='100' size='45'></td></tr>"
."<tr><td>"._SMTP_A_11."</td><td><input type='text' name='smtpconf[port]' value='".$smtpconf['port']."' maxlength='5' size='7'></td></tr>"
."<tr><td>"._SMTP_A_12."</td><td><input type='text' name='smtpconf[from]' value='".$smtpconf['from']."' maxlength='100' size='45'></td></tr>"
."<tr><td>"._SMTP_A_13."</td><td><input type='text' name='smtpconf[name]' value='".$smtpconf['name']."' maxlength='100' size='45'></td></tr>"
."<tr><td>"._SMTP_A_14."</td><td><input type='text' name='smtpconf[charset]' value='".$smtpconf['charset']."' maxlength='100' size='45'></td></tr>"
."<tr><td>"._SMTP_A_15."</td><td><input type='text' name='smtpconf[client]' value='".$smtpconf['client']."' maxlength='100' size='45'></td></tr>"
."<tr><td>"._SMTP_A_16."</td><td><input type='radio' name='smtpconf[type]' value='plain' ".smtp_select('plain', $smtpconf['type'], 'html', 1).">"._SMTP_A_18."<input type='radio' name='smtpconf[type]' value='html' ".smtp_select('html', $smtpconf['type'], 'html', 1).">"._SMTP_A_19."</td></tr>"
."<tr><td>"._SMTP_A_17."</td><td><input type='radio' name='smtpconf[log]' value='1' ".smtp_select('1', $smtpconf['log'], '1', 1).">"._SMTP_A_20."<input type='radio' name='smtpconf[log]' value='0' ".smtp_select('0', $smtpconf['log'], '1', 1).">"._SMTP_A_21."</td></tr>"
."</table><div class='button'><input type='hidden' name='op' value='smtp_conf_save'><input type='submit' value='"._SAVECHANGES."' class='fbutton'></div></form>";
close();
foot();
}

function smtp_conf_save() {
global $admin_file;
$content = "\$smtpconf['status']=".intval($_POST['smtpconf']['status']).";".PHP_EOL
."\$smtpconf['login']='".$_POST['smtpconf']['login']."';".PHP_EOL
."\$smtpconf['password']='".htmlspecialchars(stripslashes($_POST['smtpconf']['password']),ENT_QUOTES)."';".PHP_EOL
."\$smtpconf['zone']='".$_POST['smtpconf']['zone']."';".PHP_EOL
."\$smtpconf['smtp']='".$_POST['smtpconf']['smtp']."';".PHP_EOL
."\$smtpconf['port']=".intval($_POST['smtpconf']['port']).";".PHP_EOL
."\$smtpconf['from']='".$_POST['smtpconf']['from']."';".PHP_EOL
."\$smtpconf['name']='".htmlspecialchars(stripslashes($_POST['smtpconf']['name']),ENT_QUOTES)."';".PHP_EOL
."\$smtpconf['charset']='".$_POST['smtpconf']['charset']."';".PHP_EOL
."\$smtpconf['client']='".htmlspecialchars(stripslashes($_POST['smtpconf']['client']),ENT_QUOTES)."';".PHP_EOL
."\$smtpconf['type']='".$_POST['smtpconf']['type']."';".PHP_EOL
."\$smtpconf['log']=".intval($_POST['smtpconf']['log']).";".PHP_EOL;
save_conf("config/config_mailer.php", $content);
Header("Location: ".$admin_file.".php?op=smtp_conf");
}

function smtp() {
global $db,$prefix,$admin_file;
$num['num']=20;
head();
panel();
title(_SMTP_A_1);
smtp_navi();
open();
$num['page'] = isset($_GET['num']) ? intval($_GET['num']) : "1";
$num['from'] = ($num['page']-1) * $num['num'];
if (isset($_GET['uid']) && intval($_GET['uid'])>0) {$sql['where']=" WHERE l.uid='".intval($_GET['uid'])."'";$num['get']='&uid='.intval($_GET['uid']);}
elseif (isset($_GET['status'])) {$sql['where']=" WHERE l.status='".intval($_GET['status'])."'";$num['get']='&status='.intval($_GET['status']);}
elseif (isset($_GET['type']) && intval($_GET['type'])>0) {$sql['where']=" WHERE l.type='".intval($_GET['type'])."'";$num['get']='&type='.intval($_GET['type']);}
elseif (isset($_GET['mail'])) {$sql['where']=" WHERE l.mail='".$_GET['mail']."'";$num['get']='&mail='.$_GET['mail'];}
else $num['get']=$sql['where']='';
list($count)=$db->sql_fetchrow($db->sql_query("SELECT COUNT(id) FROM ".$prefix."_mail_log AS l".$sql['where']));
if ($count > 0) {
echo '<script type="text/javascript">function checkAll(oForm, cbName, checked){for (var i=0; i < oForm[cbName].length; i++) oForm[cbName][i].checked = checked;}</script>';
echo "<form action='".$admin_file.".php?op=smtp_del' method='post' id='blogs_form'><table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_i'><tr>
<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick=\"checkAll(this.form,'delete[]',this.checked)\" type='checkbox' value='delete'>"._SMTP_A_22."</span>
<th>"._SMTP_A_23."</th><th>"._SMTP_A_24."</th><th>"._SMTP_A_25."</th><th>"._SMTP_A_26."</th><th>"._SMTP_A_27."</th><th>"._SMTP_A_28."</th><th>"._SMTP_A_29."</th></tr>";
$result = $db->sql_query("SELECT l.id,l.uid,l.mail,l.date,l.info,l.type,l.status,u.user_name FROM ".$prefix."_mail_log AS l LEFT JOIN ".$prefix."_users AS u ON (l.uid=u.user_id)".$sql['where']." ORDER BY l.date DESC LIMIT ".$num['from'].", ".$num['num']."");
while (list($id,$uid,$mail,$date,$info,$type,$status,$name) = $db->sql_fetchrow($result)) {
$info=unserialize($info);
if ($status==1) $status="<font color='green'><b>"._SMTP_A_30."</b></font><a href='$admin_file.php?op=smtp&status=$status' style='padding-left:5px;font-weight:bold;' title='"._SMTP_A_31."'>[?]</a>";
else $status="<font color='red'><b>"._SMTP_A_32."</b></font><a href='$admin_file.php?op=smtp&status=$status' style='padding-left:5px;font-weight:bold;' title='"._SMTP_A_33."'>[?]</a>";
if ($type==1) $type="<span style='color:blue;font-weight:bold;' onmouseover=\"Tip('"._SMTP_A_34."',BGCOLOR,'#E7FFC9',BORDERCOLOR,'#70BD13')\">"._SMTP_A_35."</span><a href='$admin_file.php?op=smtp&type=$type' style='padding-left:5px;font-weight:bold;' title='"._SMTP_A_36."'>[?]</a>";
else $type="<span style='color:orange;font-weight:bold;' onmouseover=\"Tip('"._SMTP_A_37."',BGCOLOR,'#E7FFC9',BORDERCOLOR,'#70BD13')\">"._SMTP_A_38."</span><a href='$admin_file.php?op=smtp&type=$type' style='padding-left:5px;font-weight:bold;' title='"._SMTP_A_36."'>[?]</a>";
if ($uid!=0) $who="<a href='index.php?name=account&op=info&uname=$name' title='"._SMTP_A_39."'>$name</a>";
else $who="<i>"._SMTP_A_40."</i>";
echo "<tr class=\"bgcolor1\"><td align=\"center\">".$id."</td>"
."<td align=\"left\"><a href='mailto:$mail' title='"._SMTP_A_41."'>$mail</a><a href='$admin_file.php?op=smtp&mail=$mail' style='padding-left:5px;font-weight:bold;' title='"._SMTP_A_42."'>[?]</a></td>"
."<td align=\"center\" onmouseover=\"Tip('".preg_replace('#(\'|\")#si','&quot;',$info['sender'])."',BGCOLOR,'#E7FFC9',BORDERCOLOR,'#70BD13')\">$who<a href='$admin_file.php?op=smtp&uid=$uid' style='padding-left:5px;font-weight:bold;' title='"._SMTP_A_43."'>[?]</a></td>"
."<td align=\"center\">$date</td>"
."<td align=\"center\">$type</td>"
."<td align=\"center\"><span onmouseover=\"Tip('".preg_replace('#(\'|\")#si','&quot;',$info['log'])."',BGCOLOR,'#E7FFC9',BORDERCOLOR,'#70BD13')\">".$status."</span></td>"
."<td align=\"center\"><input class='ids_del' type='checkbox' name='delete[]' value='$id'></td>"
."</tr>";
}
echo "</table><div style='float:right;margin:10px 0;'><input type='hidden' name='op' value='smtp_del'><input type='submit' value='"._SMTP_A_44."'></div></form>";
num_page("", $count, ceil($count/$num['num']), $num['num'], "op=smtp".$num['get']."&");
} else warning(_NO_INFO, "", "", 2);
close();
foot();
}

switch($op) {
case "smtp_conf":
smtp_conf();
break;

case "smtp_conf_save":
smtp_conf_save();
break;

case "smtp":
smtp();
break;

case "smtp_del":
if (count($_POST['delete'])>0) {
$db->sql_query("DELETE FROM ".$prefix."_mail_log WHERE id IN (".implode(',',$_POST['delete']).")");
}
referer($admin_file.".php?op=smtp");
break;
}

?>