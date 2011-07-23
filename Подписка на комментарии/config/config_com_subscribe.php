<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

define('_CSUB_1','Мои подписки на комментарии');
define('_CSUB_2','Информация о пользователе');
define('_CSUB_3','Просмотреть комментарий');
define('_CSUB_4','Отписаться от рассылки');
define('_CSUB_5','Вы успешно подписались на комментарии!');
define('_CSUB_6','Ошибка, запрашиваемая публикация не найдена!');
define('_CSUB_7','Ошибка, вы уже подписаны на комментарии к данной публикации!');
define('_CSUB_8','Ошибка, для данного модуля подписка на комметарии отключена!');
define('_CSUB_9','Ошибка, неверно заданы параметры подписки!');
define('_CSUB_10','Ошибка, подписка с такими параметрами не найдена, либо вы уже отписаны от рассылки!');
define('_CSUB_11','Вы успешно отписались от рассылки на комментарии!');
define('_CSUB_12','Ошибка, неверно заданы параметры для отписки, убедитесь в правильности URL!');
define('_CSUB_13','Подписаться на комментарии');
define('_CSUB_14','У вас пока нет подписок!');
define('_CSUB_15','№');
define('_CSUB_16','Заголовок');
define('_CSUB_17','Дата');
define('_CSUB_18','Статус');
define('_CSUB_19','Удалить');
define('_CSUB_20','Не активна');
define('_CSUB_21','Активна');
define('_CSUB_22','Удалить выбранные');
define('_CSUB_23','Снять/выбрать всё');

# Кодировка (utf-8,windows-1251) + пересохраните данный файл в нужной кодировке
$subscribe['charset']='utf-8';
# Не отсылать уведомления пока предыдущие комментарии не прочитаны (1 - да)
$subscribe['type']=1;
# Кол-во подписок на странице (в пользовательской части, нумерация страниц)
$subscribe['num']=10;
# Настройка доп. модулей: $subscribe['mods'][ИМЯ_МОДУЛЯ]=array... Пример см. ниже:
$subscribe['mods']['news']=array(
# Разрешить подписку в данном модуле (1 - да)
'status'=>1,
# Рассылать уведомления о добавленном комментарии для данного модуля (1 - да)
'send'=>1,
# SQL запрос для выборки заголовка и номера (id) публикации, {prefix} - префикс к БД, {id} - список запрашиваемых id
'sql'=>'SELECT `title`,`sid` FROM {prefix}_stories WHERE `sid` IN ({id})',
# Ссылка на публикацию {mod} - название модуля, {id} - id публикации
'url'=>'index.php?name={mod}&op=view&id={id}',
# Заголовок письма уведомления о добавленном комментарии для данного модуля
'title'=>'Добавлен новый комменарий к новости!',
# Текст письма уведомления, где:
## {title} - ссылка на комментарий с заголовоком новости
## {url} - ссылка на комментарий
## {author} - автор комментария
## {text} - текст комментария
## {unsubscribe} - ссылка на отписку от рассылки к данной статье
'text'=>'Здравствуйте, сообщаем вам о новом комментарии к новости - {title}

<b>Ссылка на комментарий:</b> {url}
<b>Автор комменария:</b> {author}
<b>Текст комментария:</b> {text}

Новые уведомления о комментариях к данной новости НЕ будут отправлены вам пока вы не просмотрите уже существующие комментарии по ссылке выше!

Это письмо вам было отправлено как подписчику на комментарии к новости {title}.
<b><font color="orange">Вы в любой момент можете отказаться от рассылки, для этого перейдите по следующей ссылке:</font></b>
{unsubscribe}'
);

function csub_send ($in) {
global $conf,$prefix,$db,$subscribe,$user;
if ($subscribe['mods'][$in['mod']]['send']==1) {
list($title)=$db->sql_fetchrow($db->sql_query(str_replace(array('{prefix}','{id}'),array($prefix,$in['id']),$subscribe['mods'][$in['mod']]['sql'])));
if ($title!=null) {
$author=(is_user())?"<a href='".rtrim($conf['homeurl'],'/')."/index.php?name=account&op=info&uname=".$user[1]."' title='"._CSUB_2."'>".$user[1]."</a>":"<b>".$in['author']."</b>";
$text['surl']=rtrim($conf['homeurl'],'/').'/'.str_replace(array('{mod}','{id}'),array($in['mod'],$in['id']),$subscribe['mods'][$in['mod']]['url']);
$result = $db->sql_query("SELECT s.id,s.uid,s.hash,u.user_email FROM ".$prefix."_com_subscribe AS s LEFT JOIN ".$prefix."_users AS u ON (s.uid=u.user_id) WHERE s.iid='".$in['id']."' AND s.mod='".$in['mod']."'".(($subscribe['type']==1)?" AND s.status!='0'":'').(((is_user())?" AND s.uid!='".intval($user[0])."'":'')));
if ($db->sql_numrows($result) > 0) {
while (list($id,$uid,$hash,$mail) = $db->sql_fetchrow($result)) {
$text['unsubscribe']=$text['surl']."&uid=$uid&hash=$hash";
$text['url'] = $text['surl']."&uid=$uid&sact=$hash#".$in['cid'];
$text['title']="<a href='".$text['url']."' title='"._CSUB_3."'>$title</a>";
$text['text']=str_replace(array('{title}','{url}','{author}','{text}','{unsubscribe}'),array($text['title'],"<a href='".$text['url']."' title='"._CSUB_3."'>".$text['url']."</a>",$author,bb_decode($in['text']),"<a href='".$text['unsubscribe']."' title='"._CSUB_4."'>".$text['unsubscribe']."</a>"),$subscribe['mods'][$in['mod']]['text']);
mail_send($mail,$conf['adminmail'],$subscribe['mods'][$in['mod']]['title'],nl2br($text['text']));
if ($subscribe['type']==1) $db->sql_query("UPDATE ".$prefix."_com_subscribe SET `status`='0' WHERE `id`='$id'");
}
}
}
}
}

function generate_code($length=8) { $num = range(0, 9); $alf = range('a', 'z'); $symbols = array_merge($num, $alf); shuffle($symbols); $code_array = array_slice($symbols, 0, (int)$length); $code = implode("", $code_array); return $code; }
function subscribe_encode ($a) { global $subscribe; if ($subscribe['charset']=='windows-1251') { header('Content-type: text/html; charset='.$thanks['charset']); foreach ($a as $b=>$c) $d[]='"'.$b.'":"'.addcslashes(str_replace(array("\r","\n"),"",$c), '"').'"'; return '{'.implode(',',$d).'}'; } else return json_encode($a); }

##&name=news&id=5 - для подписки
function comments_sub () {
global $user,$db,$prefix,$subscribe;
$out['status']=0;
if (is_user() && isset($_GET['name']) && isset($_GET['id'])) {
$mod=text_filter($_GET['name']);
$id=intval($_GET['id']);
$uid=intval($user[0]);
if (is_array($subscribe['mods'][$mod]) && $subscribe['mods'][$mod]['status']==1) {
list($check['subs'])=$db->sql_fetchrow($db->sql_query("SELECT COUNT(*) FROM ".$prefix."_com_subscribe WHERE `iid`='$id' AND `mod`='$mod' AND `uid`='$uid'"));
if ($check['subs']==0) {
$check['count']=$db->sql_numrows($db->sql_query(str_replace(array('{prefix}','{id}'),array($prefix,$id),$subscribe['mods'][$mod]['sql'])));
if ($check['count']>0) {
$db->sql_query("INSERT INTO ".$prefix."_com_subscribe VALUES (NULL,'$mod','$id','$uid',now(),'".generate_code()."',1)");
$out=array('status'=>1,'text'=>_CSUB_5);
} else $out['text']=_CSUB_6;
} else $out['text']=_CSUB_7;
} else $out['text']=_CSUB_8;
} else $out['text']=_CSUB_9;
return $out;
}

#&uid=4&hash=2qo6cd8z - для отписки (гость)
#&hash=2qo6cd8z - для отписки (пользователь)
function comments_unsub () {
global $db,$prefix,$user;
if (is_user()) $_GET['uid']=$user[0];
if (isset($_GET['uid'],$_GET['hash']) && text_filter($_GET['hash'])!='' && intval($_GET['uid'])>0) {
$db->sql_query("DELETE FROM ".$prefix."_com_subscribe WHERE `uid`='".intval($_GET['uid'])."' AND `hash`='".text_filter($_GET['hash'])."'");
if ($db->sql_affectedrows($sql) == 0) $out=array('status'=>0,'text'=>_CSUB_10);
else $out=array('status'=>1,'text'=>_CSUB_11);
} else $out=array('status'=>0,'text'=>_CSUB_12);
return $out;
}

#subscribe_button ('news',5);
#&uid=1&sact=gfh5xd0z - для активации подписки (гость), авто (для пользователя)
function subscribe_button ($mod,$id) {
global $user,$db,$prefix,$subscribe;
$out['html']='';
if (is_user()) {
list($hash,$status)=$db->sql_fetchrow($db->sql_query("SELECT `hash`,`status` FROM ".$prefix."_com_subscribe WHERE `iid`='$id' AND `mod`='$mod' AND `uid`='".intval($user[0])."'"));
if ($status=='0') $db->sql_query("UPDATE ".$prefix."_com_subscribe SET `status`='1' WHERE `iid`='$id' AND `mod`='$mod' AND `uid`='".intval($user[0])."'");
if (is_array($subscribe['mods'][$mod]) && $subscribe['mods'][$mod]['status']==1 && $hash==null) $out['html']="<div id='subscribe_comm'><a href='#' class='button blue medium' onclick='comm_subscribe(\"$mod\",$id,1); return false;'>"._CSUB_13."</a></div><div class='clear'></div>";
elseif ($hash!=null) $out['html']="<div id='subscribe_comm'><a href='#' class='button orange medium' onclick='comm_subscribe(\"$mod\",$id,\"$hash\"); return false;'>"._CSUB_4."</a></div><div class='clear'></div>";
} elseif (isset($_GET['sact'],$_GET['uid']) && text_filter($_GET['sact'])!='' && intval($_GET['uid'])>0)  $db->sql_query("UPDATE ".$prefix."_com_subscribe SET `status`='1' WHERE `hash`='".text_filter($_GET['sact'])."' AND `uid`='".intval($_GET['uid'])."'");
return $out['html'];
}

function ajax_subscribe() {
if (intval($_GET['hash'])==1) $out=comments_sub();
else $out=comments_unsub();
$out['html']=subscribe_button(text_filter($_GET['name']),intval($_GET['id']));
echo subscribe_encode($out);
}

function paste_onuser ($cid) {
global $conf;
$out = subscribe_button($conf['name'],$cid);
if (isset($_GET['hash'])) {
$unsub=comments_unsub();
$out .= "<script language='JavaScript' type='text/javascript'>$(function (){ $.jGrowl.defaults.closerTemplate = '<div><strong>[ Закрыть все уведомления ]</strong></div>'; $.jGrowl('".$unsub['text']."', {life: 10000, header: 'Уведомление', theme: 'smoke ".(($unsub['status']==1)?'green':'red')."'}); });</script>";
}
return $out;
}

function subscribe_account() {
global $db,$prefix,$user,$conf,$subscribe,$pagetitle;
if (is_user()) {
if (isset($_POST['delete']) && count($_POST['delete'])>0) $db->sql_query("DELETE FROM ".$prefix."_com_subscribe WHERE `id` IN (".implode(',',array_map('intval',$_POST['delete'])).") AND `uid`='".intval($user[0])."'");
$pagetitle = $conf['defis']." "._CSUB_1;
head();
title(_CSUB_1);
navi('0');
open();
$num = isset($_GET['num'])?intval($_GET['num']):"1";
$i=($num-1)*$subscribe['num'];
$result = $db->sql_query("SELECT `id`,`mod`,`iid`,`date`,`hash`,`status` FROM ".$prefix."_com_subscribe WHERE `uid`='".intval($user[0])."' ORDER BY `date` DESC LIMIT ".(($num-1)*$subscribe['num']).", ".$subscribe['num']);
$count = $db->sql_numrows($db->sql_query("SELECT `id` FROM ".$prefix."_com_subscribe WHERE `uid`='".intval($user[0])."'"));
if ($count==0) warning(_CSUB_14, '', '', 0);
else {
echo "<div id='subscribe_comm'><a href='#' class='button blue medium' id='subcheckall'>"._CSUB_23."</a></div><div class='clear'></div><div></div>";
echo '<form id="sub_del" name="sub_del" action="index.php?name=account&op=subscribe_account" method="post">';
echo "<table width='100%' border='0' cellpadding='2' cellspacing='1' class='sort' id='sort_id'><tr><th>"._CSUB_15."</th><th>"._CSUB_16."</th><th>"._CSUB_17."</th><th>"._CSUB_18."</th><th>"._CSUB_19."</th></tr>";
while (list($id,$mod,$iid,$date,$hash,$status) = $db->sql_fetchrow($result)) {
$status=($status==0)?'<font color="red"><i>'._CSUB_20.'</i></font>':'<font color="green"><i>'._CSUB_21.'</i></font>';
$url=str_replace(array('{mod}','{id}'),array($mod,$iid),$subscribe['mods'][$mod]['url']);
$arr[$mod][]=$iid;
$i++;
$out .="<tr class='bgcolor1'>"
."<td style='width:35px;' align='center'>$i</td>"
."<td><a href='$url' title='{".$mod."-$iid}'>{".$mod."-$iid}</a></td>"
."<td>$date</td>"
."<td>$status</td>"
.'<td><input type="checkbox" class="checkbox subscribes" name="delete[]" value="'.$id.'"></td></tr>';
}
foreach ($arr as $a=>$b) {
unset($r,$t);
$r=$db->sql_query(str_replace(array('{prefix}','{id}'),array($prefix,implode(',',$arr[$a])),$subscribe['mods'][$a]['sql']));
while (list($t,$i) = $db->sql_fetchrow($r)) {$mass['i'][]='{'.$a.'-'.$i.'}';$mass['o'][]=$t;}
}
echo str_replace($mass['i'],$mass['o'],$out);
echo "</table>";
echo "<div style='margin-top:10px;' id='subscribe_comm'><a href='#' id='subb_del' class='button red medium'>"._CSUB_22."</a></div><div class='clear'></div><div></div>";
echo "</form>";
num_page($conf['name'],$count,ceil($count/$subscribe['num']),$subscribe['num'],"op=subscribe_account&");
}
close();
foot();
} else edithome();
}

?>