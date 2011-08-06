<?php
if (!defined("ADMIN_FILE") || !is_admin_modul("elfinder")) die("Illegal File Access");

$elf['godonly']='1';

function elfinder_navi() {
	global $admin_file,$elf;
	open();
	echo "<h1>Менеджер файлов</h1>";
	echo "<h5>[ <a href='".$admin_file.".php?op=elfinder'>Менеджер</a>";
if ($elf['godonly']!='1' || is_admin_god())	{
  echo " | <a href='".$admin_file.".php?op=elfinder_log'>Лог</a>";
	echo " | <a href='".$admin_file.".php?op=elfinder_conf'>Настройки</a>";
}	
	echo "]</h5>";
	close();
}

function elfinder_conf() {
	global $admin_file,$elf;
  head();
  panel();
	elfinder_navi();
	include("config/config_elf_set.php");
	$permtest = end_chmod("config/config_elf_set.php", 666);
	if ($permtest) warning($permtest, "", "", 1);
	open();
	echo "<h2>Настройки</h2>"
	."<form name='post' action='".$admin_file.".php' method='post'>"
	."<div class='left'>Записывать лог:</div><div class='center'>".radio_form($elf['log'], "log")."</div>"
	."<div class='left'>Отключить стандартный загрузчик у пользователей:</div><div class='center'>".radio_form($elf['stand'], "stand")."</div>"
	."<div class='left'>Включить менеджер файлов для пользователей:</div><div class='center'>".radio_form($elf['status'], "status")."</div>"
	."<div class='button'><input type='hidden' name='op' value='elfinder_conf_save'><input type='submit' value='"._SAVECHANGES."' class='fbutton'></div></form>";
	close();
	foot();
}

function elfinder_conf_save() {
global $admin_file;
$content = "\$elf['log'] = '".intval($_POST['log'])."';\n"
."\$elf['stand'] = '".intval($_POST['stand'])."';\n"
."\$elf['status'] = '".intval($_POST['status'])."';\n";
save_conf("config/config_elf_set.php", $content);
Header("Location: ".$admin_file.".php?op=elfinder_conf");
}

function elfinder_log() {
global $prefix, $db, $admin_file;
$num['get']=$sql['where']='';
$num['num']=10;
$num['page'] = isset($_GET['num']) ? intval($_GET['num']) : "1";
$num['from'] = ($num['page']-1) * $num['num'];
if (isset($_GET['val'])) $val=text_filter($_GET['val']);
if (isset($_GET['id'])) $idval=intval($_GET['id']);
if (isset($_GET['sort'])) $sort=text_filter($_GET['sort']);
switch($sort) {
case "cmd":
case "ip":
case "date":
case "type":
if ($sort=='date') $sql['where']="e.date LIKE '$val%'";
else $sql['where']="e.$sort='$val'";
$num['get']='&sort='.$sort."&val=".$val;
break;
case "user":
$sql['where']="e.type='$val' AND ".(($val=='1')?'u.user_id':'a.id')."='$idval'";
$num['get']='&sort='.$sort."&val=".$val."&id=".$idval;
break;
}
$commands=array('mkdir'=>array('name'=>'Создание папки','color'=>'#1CABB8'),'mkfile'=>array('name'=>'Создание файла','color'=>'#165CB2'),'rename'=>array('name'=>'Переименование','color'=>'#5A29C5'),'duplicate'=>array('name'=>'Дубликат','color'=>'#589CB3'),'upload'=>array('name'=>'Загрузка','color'=>'#2B9D02'),'rm'=>array('name'=>'Удаление','color'=>'#EC5050'),'paste'=>array('name'=>'Вставка','color'=>'#C59469'),'put'=>array('name'=>'Редактирование','color'=>'#F8A55D'),'extract'=>array('name'=>'Извлечение из архива','color'=>'#282682'),'archive'=>array('name'=>'Архивирование','color'=>'#97AC51'));
head();
panel();
elfinder_navi();
if ($sql['where']!='') $sql['where']=' WHERE '.$sql['where'];
list($count)=$db->sql_fetchrow($db->sql_query("SELECT COUNT(e.id) FROM ".$prefix."_elfinder AS e LEFT JOIN ".$prefix."_users AS u ON (e.uid=u.user_id) LEFT JOIN ".$prefix."_admins AS a ON (e.uid=a.id)".$sql['where']));
$result = $db->sql_query("SELECT e.id,e.type,e.uid,e.ip,e.date,e.cmd,e.info,u.user_name,a.name FROM ".$prefix."_elfinder AS e LEFT JOIN ".$prefix."_users AS u ON (e.uid=u.user_id AND e.type=1) LEFT JOIN ".$prefix."_admins AS a ON (e.uid=a.id AND e.type=2)".$sql['where']." ORDER BY e.date DESC LIMIT ".$num['from'].", ".$num['num']);
if ($count > 0) {
open();
echo '<script type="text/javascript">function checkAll(oForm, cbName, checked){for (var i=0; i < oForm[cbName].length; i++) oForm[cbName][i].checked = checked;}</script>';
echo "<form action='".$admin_file.".php?op=elfinder_del' method='post' id='elfinder_form'><table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_i'><tr>";
echo "<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick=\"checkAll(this.form,'delete[]',this.checked)\" type='checkbox' value='delete'>Выбрать всё</span>";
echo "<th>Команда</th><th>Файл или папка</th><th>Пользователь</th><th>Дата</th><th>Удалить</th></tr>";
while (list($id,$type,$uid,$ip,$date,$cmd,$info,$user_name,$admin_name) = $db->sql_fetchrow($result)) {
$t=explode(' ',$date);
$filter=array (
'cmd'=>"<a href='$admin_file.php?op=elfinder_log&sort=cmd&val=$cmd' style='padding-right:5px;font-weight:bold;' title='Показать все записи с коммандой ".$commands[$cmd]['name']."'>[?]</a>",
'ip'=>"<a href='$admin_file.php?op=elfinder_log&sort=ip&val=$ip' style='padding-right:5px;font-weight:bold;' title='Показать все действия с IP ".$ip."'>[IP]</a>",
'date'=>"<a href='$admin_file.php?op=elfinder_log&sort=date&val=".$t[0]."' style='padding-right:5px;font-weight:bold;' title='Показать все записи за ".$t[0]."'>[?]</a>",
'type'=>"<a href='$admin_file.php?op=elfinder_log&sort=type&val=$type' style='padding-right:5px;font-weight:bold;' title='Показать все действия ".(($type=='2')?'администраторов':'пользователей')."'>[".(($type=='2')?'A':'U')."]</a>",
'user'=>"<a href='$admin_file.php?op=elfinder_log&sort=user&id=$uid&val=$type' style='padding-right:5px;font-weight:bold;' title='Показать все действия данного ".(($type=='2')?'администратора':'пользователя')."'>[?]</a>",
);
unset($name,$inf);
$inf=array();
if ($type=='1') $who="<b>".user_info($user_name,1)."</b>";
else $who="<b style='color:#FF5000' title='Администратор'>".$admin_name."</b>";
$arr=unserialize($info);
if (in_array($cmd,array('mkdir','mkfile','upload','paste','duplicate','rename','put','rm','archive'))) {
if ($cmd=='rm') $fix='removedDetails';
elseif ($cmd=='put') $fix='changed';
else $fix='added';
$name=$arr[$fix][0]['name'];
$inf['dir']="<font color='green'><b>Папка:</b></font> <font color='green'>".$arr[$fix][0]['hash']."</font>";
if ($arr[$fix][0]['mime']=='directory') $inf['mime']="<font color='green'><b>Тип:</b></font> <font color='green'>Директория</font>";
else $inf['mime']="<font color='green'><b>Тип:</b></font> <font color='green'>".$arr[$fix][0]['mime']."</font>";
$inf['size']="<font color='green'><b>Размер:</b></font> <font color='green'>".$arr[$fix][0]['size']." Кб</font>";
if (isset($arr[$fix][0]['tmb'])) $inf['preview']="<font color='green'><b>Миниатюра:</b></font> <font color='green'>Да</font>";
if ($cmd=='duplicate') $inf['dubl']="<font color='green'><b>Дубликат:</b></font> <font color='green'>".$arr['src']['name']."</font>";
if ($cmd=='rename') $inf['renamed']="<font color='green'><b>Первоначальное имя:</b></font> <font color='green'>".$arr['removedDetails'][0]['name']."</font>";
} elseif ($cmd=='extract') {$name='Архив';$inf['dir']="<font color='green'><b>Папка:</b></font> <font color='green'>".$arr['added'][0]['hash']."</font>";}
echo "<tr class='bgcolor1'>"
."<td align='left'>".$filter['cmd']."<b><font color='".$commands[$cmd]['color']."'>".$commands[$cmd]['name']."</font></b></td>";
if (count($inf)>0) echo "<td align='center' onmouseover=\"Tip('".preg_replace('#(\'|\")#si','&quot;',addcslashes(implode('<br />',$inf),'\\'))."',BGCOLOR,'#E7FFC9',BORDERCOLOR,'#70BD13')\"><b><font color='".$commands[$cmd]['color']."'>$name</font></b></td>";
else echo "<td align='center'><b><font color='".$commands[$cmd]['color']."'>$name</font></b></td>";
echo "<td>".$filter['type'].$filter['user'].$filter['ip'].$who."</td>"
."<td align='center'>".$filter['date'].$date."</td>"
."<td align='center'><input class='ids_del' type='checkbox' name='delete[]' value='$id'></td></tr>";
}
echo "</table><div style='float:right;margin:10px 0;'><input type='hidden' name='op' value='elfinder_del'><input type='submit' value='Удалить выбранные'></div></form>";
close();
num_page("", $count, ceil($count/$num['num']), $num['num'], "op=elfinder_log".$num['get']."&");
} else warning(_NO_INFO, "", "", 2);
foot();
}

function elfinder() {
include_once ('config/config_elfinder.php');
head();
panel();
elfinder_navi();
open();
echo elfinder_admin();
close();
foot();
}

switch($op) {
case "elfinder":
elfinder();
break;

case "elfinder_conf":
elfinder_conf();
break;

case "elfinder_conf_save":
elfinder_conf_save();
break;

case "elfinder_log":
elfinder_log();
break;

case "elfinder_del":
if (count($_POST['delete'])>0) {
$db->sql_query("DELETE FROM ".$prefix."_elfinder WHERE id IN (".implode(',',$_POST['delete']).")");
}
referer($admin_file.".php?op=elfinder_log");
break;

}
?>