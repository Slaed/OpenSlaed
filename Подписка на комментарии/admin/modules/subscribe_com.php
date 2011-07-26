<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

function subcom_mod() {
global $prefix, $db, $admin_file;
head();
panel();
subcom_navi();
$result = $db->sql_query("SELECT `id`,`mod`,`info`,`send`,`status` FROM ".$prefix."_com_smod ORDER BY id DESC");
open();
echo "<div style='font-size:12px;float:right; margin:0 10px 10px 0;'><b><a href='$admin_file.php?op=subcom_add'>[+] Добавить модуль</a></b></div><div style='clear:both;'></div>";
close();
if ($db->sql_numrows($result) > 0) {
open();
echo '<script type="text/javascript">function checkAll(oForm, cbName, checked){for (var i=0; i < oForm[cbName].length; i++) oForm[cbName][i].checked = checked;}</script>';
echo "<form action='".$admin_file.".php?op=subcom_mod_act' method='post' id='subcom_form'><table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_i'><tr>";
echo "<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick=\"checkAll(this.form,'sel[]',this.checked)\" type='checkbox' value='sel'>Выбрать всё</span>";
echo "<th>Модуль</th><th>Подписка</th><th>Рассылка</th><th>Функции</th></tr>";
while (list($id,$mod,$info,$send,$status) = $db->sql_fetchrow($result)) {
$info=unserialize($info);
echo "<tr class='bgcolor1'>"
."<td align='center'>".$info['name']."</td>"
."<td align='center'>".(($status==1)?'<b style="color:green">Разрешена</b>':'<b style="color:red">Запрещена</b>')."</a></td>"
."<td align='center'>".(($send==1)?'<b style="color:green">Включена</b>':'<b style="color:red">Выключена</b>')."</b></td>"
.'<td align="center">'.ad_edit($admin_file.".php?op=subcom_add&id=".$id).'<input type="checkbox" class="checkbox subscribes" name="sel[]" value="'.$id.'"></td></tr>';
}
echo "</table><div style='float:right;margin:10px 0;'><input type='hidden' name='op' value='subcom_mod_act'>";
echo "Что делаем с выбранными: <select name='action'>"
."<option value='1'>Разрешить подписку</option>"
."<option value='2'>Запретить подписку</option>"
."<option value='3'>Ативировать рассылку</option>"
."<option value='4'>Деактивировать рассылку</option>"
."<option value='5'>Удалить</option></select>";
echo "<input style='margin-left:10px;' type='submit' value='Выполнить'></div></form>";
close();
} else warning(_NO_INFO, "", "", 2);
foot();
}


function subcom () {
global $prefix, $db, $admin_file;
include('config/config_csubcom.php');
$num['get']=$sql['where']='';
$num['num']=10;
$num['page'] = isset($_GET['num']) ? intval($_GET['num']) : "1";
$num['from'] = ($num['page']-1) * $num['num'];
if (isset($_GET['val'])) $val=text_filter($_GET['val']);
if (isset($_GET['id'])) $idval=intval($_GET['id']);
if (isset($_GET['sort'])) $sort=text_filter($_GET['sort']);
switch($sort) {
case "mod":
case "uid":
case "date":
case "status":
if ($sort=='date') $sql['where']="s.date LIKE '$val%'";
else $sql['where']="s.$sort='$val'";
$num['get']='&sort='.$sort."&val=".$val;
break;
case "iid":
$sql['where']="s.mod='$val' AND s.iid='$idval'";
$num['get']='&sort='.$sort."&val=".$val."&id=".$idval;
break;
}
head();
panel();
subcom_navi();
if ($sql['where']!='') $sql['where']=' WHERE '.$sql['where'];
list($count)=$db->sql_fetchrow($db->sql_query("SELECT COUNT(s.id) FROM ".$prefix."_com_subscribe AS s LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id)".$sql['where']));
$result = $db->sql_query("SELECT s.id, s.mod, s.iid, s.uid, s.date, s.status, u.user_name FROM ".$prefix."_com_subscribe AS s LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id)".$sql['where']." ORDER BY s.date DESC LIMIT ".$num['from'].", ".$num['num']);
if ($count > 0) {
open();
echo '<script type="text/javascript">function checkAll(oForm, cbName, checked){for (var i=0; i < oForm[cbName].length; i++) oForm[cbName][i].checked = checked;}</script>';
echo "<form action='".$admin_file.".php?op=subcom_act' method='post' id='subcom_form'><table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_i'><tr>";
echo "<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick=\"checkAll(this.form,'sel[]',this.checked)\" type='checkbox' value='sel'>Выбрать всё</span>";
echo "<th>Модуль</th><th>Заголовок</th><th>Пользователь</th><th>Дата</th><th>Статус</th><th>Выбрать</th></tr>";
while (list($id,$mod,$iid,$uid,$date,$status,$user_name) = $db->sql_fetchrow($result)) {
$arr[$mod][]=$iid;
$t=explode(' ',$date);
$filter=array (
'mod'=>"<a href='$admin_file.php?op=subcom&sort=mod&val=$mod' style='padding-right:5px;font-weight:bold;' title='Показать все подписки на модуль ".$subscribe['mods'][$mod]['name']."'>[?]</a>",
'uid'=>"<a href='$admin_file.php?op=subcom&sort=uid&val=$uid' style='padding-right:5px;font-weight:bold;' title='Показать все подписки пользователя ".$user_name."'>[?]</a>",
'date'=>"<a href='$admin_file.php?op=subcom&sort=date&val=".$t[0]."' style='padding-right:5px;font-weight:bold;' title='Показать все подписки за ".$t[0]."'>[?]</a>",
'status'=>"<a href='$admin_file.php?op=subcom&sort=status&val=$status' style='padding-right:5px;font-weight:bold;' title='Показать все ".(($status==1)?'активные':'не активные')." подписки'>[".(($status==1)?'А':'Н')."]</a>",
'iid'=>"<a href='$admin_file.php?op=subcom&sort=iid&id=$iid&val=$mod' style='padding-right:5px;font-weight:bold;' title='Показать все подписки на данную публикацию'>[?]</a>",
);
$url=str_replace(array('{mod}','{id}'),array($mod,$iid),$subscribe['mods'][$mod]['url']);
$out .="<tr class='bgcolor1'>"
."<td align='center'>".$filter['mod'].$subscribe['mods'][$mod]['name']."</td>"
."<td>".$filter['iid']."<a href='$url' title='{".$mod."-$iid}'>{".$mod."-$iid}</a></td>"
."<td>".$filter['uid']."<b>".user_info($user_name,1)."</b></td>"
."<td>".$filter['date'].$t[0]."</td>"
."<td>".$filter['status'].(($status==1)?'<b style="color:green;"><i>Активна</i></b>':'<b style="color:red;"><i>Не активна</i></b>')."</td>"
.'<td align="center"><input type="checkbox" class="checkbox subscribes" name="sel[]" value="'.$id.'"></td></tr>';
}
foreach ($arr as $a=>$b) {
unset($r,$t);
$r=$db->sql_query(str_replace(array('{prefix}','{id}'),array($prefix,implode(',',$arr[$a])),$subscribe['mods'][$a]['sql']));
while (list($t,$i) = $db->sql_fetchrow($r)) {$mass['i'][]='{'.$a.'-'.$i.'}';$mass['o'][]=$t;}
}
echo str_replace($mass['i'],$mass['o'],$out);
echo "</table><div style='float:right;margin:10px 0;'><input type='hidden' name='op' value='subcom_act'>";
echo "Что делаем с выбранными: <select name='action'><option value='1'>Активировать</option><option value='2'>Деактивировать</option><option value='3'>Удалить</option></select>";
echo "<input style='margin-left:10px;' type='submit' value='Выполнить'></div></form>";
close();
num_page("", $count, ceil($count/$num['num']), $num['num'], "op=subcom".$num['get']."&");
} else warning(_NO_INFO, "", "", 2);
foot();
}

function subcom_navi() {
global $admin_file;
open();
echo "<h1>Подписки на комментарии</h1>"
."<h5>[ <a href='$admin_file.php?op=subcom'>Подписки</a>"
." | <a href='$admin_file.php?op=subcom_mod'>Настройка модулей</a>"
." | <a href='$admin_file.php?op=subcom_conf'>Общие настройки</a> ]</h5>";
close();
}

function subcom_conf() {
global $admin_file;
head();
panel();
subcom_navi();
include("config/config_csubcom.php");
$permtest = end_chmod("config/config_csubcom.php", 666);
if ($permtest) warning($permtest, "", "", 1);
open();
echo "<form action='".$admin_file.".php' method='post'>"
."<div class='left'>Уведомлять только, когда предыдущие комментарии прочитаны:</div><div class='center'>".radio_form($subscribe['type'],"type")."</div>"
."<div class='left'>Уведомлять только, если комментатор - пользователь:</div><div class='center'>".radio_form($subscribe['useronly'],"useronly")."</div>"
."<div class='left'>Кол-во подписок на странице (в пользовательской части):</div><div class='center'><input type='text' name='num' value='".$subscribe['num']."' maxlength='2' size='10' class='admin'></div>"
."<div class='button'><input type='hidden' name='op' value='subcom_save_conf'><input type='submit' value='"._SAVECHANGES."' class='fbutton'></div></form>";
close();
foot();
}

function subcom_save_conf() {
global $admin_file;
$content = "\$subscribe['type']=".intval($_POST['type']).";\n";
$content .= "\$subscribe['useronly']=".intval($_POST['useronly']).";\n";
$content .= "\$subscribe['num']=".intval($_POST['num']).";\n";
save_conf("config/config_csubcom.php", $content);
subcom_gets();
Header("Location: ".$admin_file.".php?op=subcom_conf");
}

function subcom_add() {
global $admin_file,$db,$prefix;
head();
panel();
subcom_navi();
open();
# Настройки по умолчанию
$mod='';
$out=array(
'name'=>'',
'status'=>1,
'send'=>1,
'param'=>array('table'=>'','id'=>'','title'=>''),
'url'=>'index.php?name={mod}&op=view&id={id}',
'title'=>'Добавлен новый комменарий!',
'text'=>'Здравствуйте, сообщаем вам о новом комментарии к публикации - {title}

<b>Ссылка на комментарий:</b> {url}
<b>Автор комменария:</b> {author}
<b>Текст комментария:</b> {text}

Новые уведомления о комментариях к данной публикации НЕ будут отправлены вам пока вы не просмотрите уже существующие комментарии по ссылке выше!

Это письмо вам было отправлено как подписчику на комментарии к публикации {title}.
<b><font color="orange">Вы в любой момент можете отказаться от рассылки, для этого перейдите по следующей ссылке:</font></b>
{unsubscribe}'
);
if (isset($_GET['id']) && intval($_GET['id'])>0) {
$result = $db->sql_query("SELECT `id`,`mod`,`info`,`send`,`status` FROM ".$prefix."_com_smod WHERE `id`='".intval($_GET['id'])."'");
list($id,$mod,$info,$sn,$st) = $db->sql_fetchrow($result);
if ($id!=null) {
$out=unserialize($info);
$out['status']=$st;
$out['send']=$sn;
}
}
echo "<form action='".$admin_file.".php' method='post'>"
."<h2>Общие настройки модуля</h2>"
."<div class='left'>Заголовок модуля:<br /><small><b>Пример: Новости</b></small></div><div class='center'><input type='text' name='name' value='".$out['name']."' maxlength='50' size='50' class='admin'></div>"
."<div style='clear:both;'></div>"
."<div class='left'>Название модуля:<br /><small><b>Пример: news</b></small></div><div class='center'><input type='text' name='mod' value='".$mod."' maxlength='50' size='50' class='admin'></div>"
."<div style='clear:both;'></div>"
."<div class='left'>Ссылка на публикацию:<br /><small><b>{mod}</b> - будет заменено на имя модуля<br /><b>{id}</b> - будет заменено на id публикации<br /><b>Пример: index.php?name={mod}&op=view&id={id}</b></small></div><div class='center'><input type='text' name='url' value='".$out['url']."' maxlength='50' size='50' class='admin'></div>"
."<div style='clear:both;'></div>"
."<div class='left'>Разрешить подписку в данном модуле:</div><div class='center'>".radio_form($out['status'],"status")."</div>"
."<div style='clear:both;'></div>"
."<div class='left'>Рассылать уведомления о добавленном комментарии для данного модуля:</div><div class='center'>".radio_form($out['send'],"send")."</div>"
."<div style='clear:both;'></div>"
."<h2>Настройка SQL запроса</h2>"
."<div class='left'>Таблица (без префикса) модуля в БД:<br /><small>Та в которой содержится название публикации и ID публикации<br /><b>Пример: stories</b></small></div><div class='center'><input type='text' name='table' value='".$out['param']['table']."' maxlength='50' size='50' class='admin'></div>"
."<div style='clear:both;'></div>"
."<div class='left'>Поле в таблице с id публикации:<br /><small><b>Пример: sid</b></small></div><div class='center'><input type='text' name='tid' value='".$out['param']['id']."' maxlength='50' size='50' class='admin'></div>"
."<div style='clear:both;'></div>"
."<div class='left'>Поле в таблице с заголовком публикации:<br /><small><b>Пример: title</b></small></div><div class='center'><input type='text' name='ttitle' value='".$out['param']['title']."' maxlength='50' size='50' class='admin'></div>"
."<div style='clear:both;'></div>"
."<h2>Настройка содержимого письма</h2>"
."<div class='left'>Заголовок письма:<br /><small><b>Пример: Добавлен новый комменарий к новости!</b></small></div><div class='center'><input type='text' name='title' value='".$out['title']."' maxlength='200' size='200' class='admin'></div>"
."<div style='clear:both;'></div>"
."<div class='left'>Текст письма:<br /><small>Текст письма уведомления, где:<br /><b>{title}</b> - ссылка на комментарий с заголовоком новости<br /><b>{url}</b> - ссылка на комментарий<br /><b>{author}</b> - автор комментария<br /><b>{text}</b> - текст комментария<br /><b>{unsubscribe}</b> - ссылка на отписку от рассылки к данной статье</small></div><div class='center'><textarea name='text' cols='65' rows='25' class='admin' wrap='off'>".$out['text']."</textarea></div>"
."<div style='clear:both;'></div>";
if ($id!=null) echo "<input type='hidden' name='iid' value='$id'>";
echo "<div class='button'><input type='hidden' name='op' value='subcom_add_save'><input type='submit' value='"._SAVECHANGES."' class='fbutton'></div></form>";
close();
foot();
}

function subcom_gets () {
global $db,$prefix;
include('config/config_csubcom.php');
$content = "\$subscribe['type']=".$subscribe['type'].";\n";
$content .= "\$subscribe['useronly']=".$subscribe['useronly'].";\n";
$content .= "\$subscribe['num']=".$subscribe['num'].";\n";
$result = $db->sql_query("SELECT `mod`,`info`,`send`,`status` FROM ".$prefix."_com_smod ORDER BY id DESC");
if ($db->sql_numrows($result) > 0) {
while (list($mod,$info,$send,$status) = $db->sql_fetchrow($result)) {
$info=unserialize($info);
$content .= "\$subscribe['mods']['$mod']=array(
'name'=>'".addcslashes(htmlspecialchars_decode($info['name'],ENT_QUOTES),"'$")."',
'status'=>$status,
'send'=>$send,
'sql'=>'SELECT `".$info['param']['title']."`,`".$info['param']['id']."` FROM {prefix}_".$info['param']['table']." WHERE `".$info['param']['id']."` IN ({id})',
'url'=>'".$info['url']."',
'title'=>'".addcslashes(htmlspecialchars_decode($info['title'],ENT_QUOTES),"'$")."',
'text'=>'".addcslashes(htmlspecialchars_decode($info['text'],ENT_QUOTES),"'$")."'
);\n";
}
}
save_conf("config/config_csubcom.php", $content);
}
switch($op) {

case "subcom_add_save":
$in=array(
'name'=>htmlspecialchars(stripslashes($_POST['name']),ENT_QUOTES),
'param'=>array('table'=>$_POST['table'],'id'=>$_POST['tid'],'title'=>$_POST['ttitle']),
'url'=>$_POST['url'],
'title'=>htmlspecialchars(stripslashes($_POST['title']),ENT_QUOTES),
'text'=>htmlspecialchars(stripslashes($_POST['text']),ENT_QUOTES)
);
if (isset($_POST['iid']) && intval($_POST['iid'])>0) $db->sql_query("UPDATE ".$prefix."_com_smod SET `mod`='".text_filter($_POST['mod'])."',`info`='".serialize($in)."',`send`=".intval($_POST['send']).",`status`=".intval($_POST['status'])." WHERE `id`='".intval($_POST['iid'])."'");
else $db->sql_query("INSERT INTO ".$prefix."_com_smod VALUES (NULL, '".text_filter($_POST['mod'])."','".serialize($in)."',".intval($_POST['send']).",".intval($_POST['status']).")");
subcom_gets();
referer($admin_file.".php?op=subcom_mod");
break;


case "subcom_add":
subcom_add();
break;

case "subcom_mod":
subcom_mod();
break;

case "subcom_mod_act":
if (isset($_POST['sel']) && count($_POST['sel'])>0) {
if (intval($_POST['action'])==5) $db->sql_query("DELETE FROM ".$prefix."_com_smod WHERE id IN (".implode(',',$_POST['sel']).")");
else {
if (intval($_POST['action'])==1) $sql="`status`='1'";
elseif (intval($_POST['action'])==2) $sql="`status`='0'";
elseif (intval($_POST['action'])==3) $sql="`send`='1'";
else $sql="`send`='0'";
$db->sql_query("UPDATE ".$prefix."_com_smod SET $sql WHERE id IN (".implode(',',$_POST['sel']).")");
}
}
subcom_gets();
referer($admin_file.".php?op=subcom_mod");
break;

case "subcom":
subcom();
break;

case "subcom_conf":
subcom_conf();
break;

case "subcom_save_conf":
subcom_save_conf();
break;

case "subcom_act":
if (count($_POST['sel'])>0) {
if (intval($_POST['action'])==3) $db->sql_query("DELETE FROM ".$prefix."_com_subscribe WHERE id IN (".implode(',',$_POST['sel']).")");
elseif (intval($_POST['action'])==2) $db->sql_query("UPDATE ".$prefix."_com_subscribe SET `status`='0' WHERE id IN (".implode(',',$_POST['sel']).")");
elseif (intval($_POST['action'])==1) $db->sql_query("UPDATE ".$prefix."_com_subscribe SET `status`='1' WHERE id IN (".implode(',',$_POST['sel']).")");
}
referer($admin_file.".php?op=subcom");
break;
}
?>