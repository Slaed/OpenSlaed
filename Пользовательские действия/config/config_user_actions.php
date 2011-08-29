<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

function act_to_array ($uid,$type = 0) {
global $db, $prefix;
$usations = array();

$act['status'] = array (
'comments' =>   1,     #Комментарии (стандартные)
'modules' =>    1,     #Модули + регистрация + награды + инвайтпоинты + инвайты
'thanks' =>     1,     #Спасибо (http://os.mymobilka.net.ru/topic/hak-spasibo-dlya-publikatsij.html)
'new_rating' => 1,     #Новый рейтинг (http://os.mymobilka.net.ru/topic/hak-rejting-1-dlya-openslaed.html)
'rating' =>     1,     #Стандартный рейтинг
'com_blogs' =>  1,     #Комментарии в блогах
'rat_blogs' =>  1,     #Рейтинг в блогах (комментарии/опросы/топики)
);

###################---Стандартные комментарии
$act['comments'] = array (
# Можно использовать параметр 'optional'=>'url' - где url - поле в БД, из которого извлекаем идентификатор для ссылки (по умол. иденитификатор равен параметру в 'id')
'news' => array('table'=>'stories', 'id'=>'sid', 'title'=>'title', 'lang'=>'Добавил комментарий к новости: {url}', 'url'=>'index.php?name=news&op=view&id={id}'),
'files' => array('table'=>'files', 'id'=>'lid', 'title'=>'title', 'lang'=>'Добавил комментарий к файлу: {url}', 'url'=>'index.php?name=files&op=view&id={id}'),
);
if ($act['status']['comments'] == 1 && is_array($act['comments']) && count($act['comments']) > 0) {
foreach ($act['comments'] as $mod => $sql) {
$left[] = 'LEFT JOIN `'.$prefix.'_'.$sql['table'].'` AS '.$mod.' ON (c.cid='.$mod.'.'.$sql['id'].')';
if (!$sql['optional']) $data[] = $mod.'.'.$sql['id'].' AS url_'.$mod;
else $data[] = $mod.'.'.$sql['optional'].' AS url_'.$mod;
$data[] = $mod.'.'.$sql['title'].' AS '.$mod;
$where[] = "'".$mod."'";
}
$query = $db->sql_query("SELECT ".implode(',',$data).", c.id, c.cid, c.modul, c.date FROM `".$prefix."_comment` AS c ".implode(' ',$left)." WHERE c.modul IN (".implode(',',$where).") AND c.uid='$uid' ORDER BY c.date DESC");
if ($db->sql_numrows($query) > 0) {
while($rows = $db->sql_fetchrow($query)) {
$time = strtotime($rows['date']);
$text = str_replace (array('{title}','{url}'),array($rows[$rows['modul']],'<a href="'.str_replace("{id}", $rows['url_'.$rows['modul']], $act['comments'][$rows['modul']]['url']).'#'.$rows['id'].'" target="_blank">'.$rows[$rows['modul']].'</a>'),$act['comments'][$rows['modul']]['lang']);
$usations[] = array ('strtotime' => $time, 'date' => date('Y-m-d', $time), 'time' => date('H:i', $time), 'text' => $text);
}
}
unset($mod,$sql,$left,$data,$where,$query,$rows,$time,$text);
}
###

###################---Комментарии в блогах
if ($act['status']['com_blogs'] == 1) {
$query = $db->sql_query("SELECT c.id, t.url, t.title, c.pid, c.date FROM `".$prefix."_blogs_comment` AS c LEFT JOIN `".$prefix."_blogs_topics` AS t ON (c.tid=t.id) WHERE c.uid='$uid' ORDER BY c.date DESC");
if ($db->sql_numrows($query) > 0) {
while($rows = $db->sql_fetchrow($query)) {
$time = strtotime($rows['date']);
$usations[] = array ('strtotime' => $time, 'date' => date('Y-m-d', $time), 'time' => date('H:i', $time), 'text' => (($rows['pid']!=0)?'Ответил на комментарий к топику':'Добавил комментарий к топику').': <a href="index.php?name=blogs&topic='.$rows['url'].'#comment-'.$rows['id'].'" target="_blank">'.$rows['title'].'</a>');
}
}
unset($query,$rows,$time);
}
###

###################---Рейтинг в блогах
if ($act['status']['rat_blogs'] == 1) {
$query = $db->sql_query("SELECT r.iid, t.url, t.title, r.type, r.date FROM `".$prefix."_blogs_vote` AS r LEFT JOIN `".$prefix."_blogs_comment` AS c ON (c.id=r.iid AND r.type='comment') LEFT JOIN `".$prefix."_blogs_topics` AS t ON (c.tid=t.id AND r.type='comment' OR r.iid=t.id AND r.type!='comment') WHERE r.uid='$uid' ORDER BY r.date DESC");
if ($db->sql_numrows($query) > 0) {
while($rows = $db->sql_fetchrow($query)) {
$time = strtotime($rows['date']);
if ($rows['type']=='comment') $text = 'Оценил комментарий к топику: <a href="index.php?name=blogs&topic='.$rows['url'].'#comment-'.$rows['iid'].'" target="_blank">'.$rows['title'].'</a>';
elseif ($rows['type']=='voting') $text = 'Принял участие в опросе в топике: <a href="index.php?name=blogs&topic='.$rows['url'].'" target="_blank">'.$rows['title'].'</a>';
else $text = 'Оценил топик: <a href="index.php?name=blogs&topic='.$rows['url'].'" target="_blank">'.$rows['title'].'</a>';
$usations[] = array ('strtotime' => $time, 'date' => date('Y-m-d', $time), 'time' => date('H:i', $time), 'text' => $text);
}
}
unset($query,$rows,$time,$text);
}
###

###################---Модули
$act['modules'] = array (
'news' => array('sql'=>"SELECT time, title, sid FROM ".$prefix."_stories WHERE uid='{uid}' ORDER BY time DESC", 'lang'=>'Добавил новость: {url}', 'url'=>'index.php?name=news&op=view&id={id}'),
'files' => array('sql'=>"SELECT date, title, lid FROM ".$prefix."_files WHERE uid='{uid}' ORDER BY date DESC", 'lang'=>'Добавил файл: {url}', 'url'=>'index.php?name=files&op=view&id={id}'),
'blogs' => array('sql'=>"SELECT date, title, url FROM ".$prefix."_blogs_topics WHERE uid='{uid}' ORDER BY date DESC", 'lang'=>'Добавил топик: {url}', 'url'=>'index.php?name=blogs&topic={id}'),
'account' => array('sql'=>"SELECT user_regdate, '', '' FROM ".$prefix."_users WHERE user_id='{uid}' ORDER BY user_regdate DESC", 'lang'=>'Зарегистрировался', 'url'=>''),
'awards' => array('sql'=>"SELECT a.date, c.title, '' FROM ".$prefix."_awarded AS a LEFT JOIN ".$prefix."_awards AS c ON (a.cid=c.id) WHERE a.uid='{uid}' ORDER BY a.date DESC", 'lang'=>'Получил награду: <b>"{title}"</b>', 'url'=>''),
'invite_points' => array('sql'=>"SELECT date, count, '' FROM ".$prefix."_invates_bonus WHERE uid='{uid}' ORDER BY date DESC", 'lang'=>'Получил <b>{title}</b> бонусных инвайтпоинтов от Администрации', 'url'=>''),
'invites' => array('sql'=>"SELECT date, nuid, nuid FROM ".$prefix."_invates WHERE uid='{uid}' ORDER BY date DESC", 'lang'=>'Пригласил на сайт: {url}', 'url'=>'index.php?name=account&op=info&uname={id}', 'alt'=>'Создал инвайт'),
);
if ($act['status']['modules'] == 1 && is_array($act['modules']) && count($act['modules']) > 0) {
foreach ($act['modules'] as $mod => $sql) {
$query = $db->sql_query(str_replace('{uid}',$uid,$sql['sql']));
if ($db->sql_numrows($query) > 0) {
while (list($date,$title,$url) = $db->sql_fetchrow($query)) {
$time = strtotime($date);
$text = str_replace (array('{title}','{url}'),array($title,'<a href="'.str_replace("{id}",$url,$sql['url']).'" target="_blank">'.$title.'</a>'),($mod=='invites'&&$title=='0')?$sql['alt']:$sql['lang']);
$usations[] = array ('strtotime' => $time, 'date' => date('Y-m-d', $time), 'time' => date('H:i', $time), 'text' => $text);
}
}
}
unset($mod,$sql,$query,$date,$title,$url,$time,$text);
}
###

###################---Спасибо
$act['thanks'] = array (
# Можно использовать параметр 'optional'=>'url' - где url - поле в БД, из которого извлекаем идентификатор для ссылки (по умол. иденитификатор равен параметру в 'id')
'news' => array('table'=>'stories', 'id'=>'sid', 'title'=>'title', 'lang'=>'Сказал спасибо за новость: {url}', 'url'=>'index.php?name=news&op=view&id={id}'),
'files' => array('table'=>'files', 'id'=>'lid', 'title'=>'title', 'lang'=>'Сказал спасибо за файл: {url}', 'url'=>'index.php?name=files&op=view&id={id}'),
);
if ($act['status']['thanks'] == 1 && is_array($act['thanks']) && count($act['thanks']) > 0) {
foreach ($act['thanks'] as $mod => $sql) {
$left[] = 'LEFT JOIN `'.$prefix.'_'.$sql['table'].'` AS '.$mod.' ON (th.mid='.$mod.'.'.$sql['id'].')';
if (!$sql['optional']) $data[] = $mod.'.'.$sql['id'].' AS url_'.$mod;
else $data[] = $mod.'.'.$sql['optional'].' AS url_'.$mod;
$data[] = $mod.'.'.$sql['title'].' AS '.$mod;
$where[] = "'".$mod."'";
}
$query = $db->sql_query("SELECT ".implode(',',$data).", th.module, th.date FROM `".$prefix."_thanks` AS th ".implode(' ',$left)." LEFT JOIN `".$prefix."_users` AS u ON (th.name=u.user_name) WHERE th.module IN (".implode(',',$where).") AND u.user_id='$uid' ORDER BY th.date DESC");
if ($db->sql_numrows($query) > 0) {
while($rows = $db->sql_fetchrow($query)) {
$time = strtotime($rows['date']);
$text = str_replace (array('{title}','{url}'),array($rows[$rows['module']],'<a href="'.str_replace("{id}", $rows['url_'.$rows['module']], $act['thanks'][$rows['module']]['url']).'" target="_blank">'.$rows[$rows['module']].'</a>'),$act['thanks'][$rows['module']]['lang']);
$usations[] = array ('strtotime' => $time, 'date' => date('Y-m-d', $time), 'time' => date('H:i', $time), 'text' => $text);
}
}
unset($mod,$sql,$left,$data,$where,$query,$rows,$time,$text);
}
###

###################---Новый рейтинг
$act['new_rating'] = array (
# Можно использовать параметр 'optional'=>'url' - где url - поле в БД, из которого извлекаем идентификатор для ссылки (по умол. иденитификатор равен параметру в 'id')
'news' => array('table'=>'stories', 'id'=>'sid', 'title'=>'title', 'lang'=>'Оценил новость: {url}', 'url'=>'index.php?name=news&op=view&id={id}'),
'files' => array('table'=>'files', 'id'=>'lid', 'title'=>'title', 'lang'=>'Оценил файл: {url}', 'url'=>'index.php?name=files&op=view&id={id}'),
'account' => array('table'=>'users', 'id'=>'user_id', 'title'=>'user_name', 'lang'=>'Оценил пользователя: {url}', 'url'=>'index.php?name=account&op=info&uname={id}', 'optional'=>'user_name'),
);
if ($act['status']['new_rating'] == 1 && is_array($act['new_rating']) && count($act['new_rating']) > 0) {
foreach ($act['new_rating'] as $mod => $sql) {
$left[] = 'LEFT JOIN `'.$prefix.'_'.$sql['table'].'` AS '.$mod.' ON (nr.iid='.$mod.'.'.$sql['id'].')';
if (!$sql['optional']) $data[] = $mod.'.'.$sql['id'].' AS url_'.$mod;
else $data[] = $mod.'.'.$sql['optional'].' AS url_'.$mod;
$data[] = $mod.'.'.$sql['title'].' AS '.$mod;
$where[] = "'".$mod."'";
}
$query = $db->sql_query("SELECT ".implode(',',$data).", nr.module, nr.date FROM `".$prefix."_whoiswho` AS nr ".implode(' ',$left)." WHERE nr.module IN (".implode(',',$where).") AND nr.uid='$uid' ORDER BY nr.date DESC");
if ($db->sql_numrows($query) > 0) {
while($rows = $db->sql_fetchrow($query)) {
$time = strtotime($rows['date']);
$text = str_replace (array('{title}','{url}'),array($rows[$rows['module']],'<a href="'.str_replace("{id}", $rows['url_'.$rows['module']], $act['new_rating'][$rows['module']]['url']).'" target="_blank">'.$rows[$rows['module']].'</a>'),$act['new_rating'][$rows['module']]['lang']);
$usations[] = array ('strtotime' => $time, 'date' => date('Y-m-d', $time), 'time' => date('H:i', $time), 'text' => $text);
}
}
unset($mod,$sql,$left,$data,$where,$query,$rows,$time,$text);
}
###

###################---Стандартный рейтинг
$act['rating'] = array (
# Можно использовать параметр 'optional'=>'url' - где url - поле в БД, из которого извлекаем идентификатор для ссылки (по умол. иденитификатор равен параметру в 'id')
'news' => array('table'=>'stories', 'id'=>'sid', 'title'=>'title', 'lang'=>'Оценил новость: {url}', 'url'=>'index.php?name=news&op=view&id={id}'),
'files' => array('table'=>'files', 'id'=>'lid', 'title'=>'title', 'lang'=>'Оценил файл: {url}', 'url'=>'index.php?name=files&op=view&id={id}'),
'account' => array('table'=>'users', 'id'=>'user_id', 'title'=>'user_name', 'lang'=>'Оценил пользователя: {url}', 'url'=>'index.php?name=account&op=info&uname={id}', 'optional'=>'user_name'),
);
if ($act['status']['rating'] == 1 && is_array($act['rating']) && count($act['rating']) > 0) {
foreach ($act['rating'] as $mod => $sql) {
$left[] = 'LEFT JOIN `'.$prefix.'_'.$sql['table'].'` AS '.$mod.' ON (r.mid='.$mod.'.'.$sql['id'].')';
if (!$sql['optional']) $data[] = $mod.'.'.$sql['id'].' AS url_'.$mod;
else $data[] = $mod.'.'.$sql['optional'].' AS url_'.$mod;
$data[] = $mod.'.'.$sql['title'].' AS '.$mod;
$where[] = "'".$mod."'";
}
$query = $db->sql_query("SELECT ".implode(',',$data).", r.modul, r.time FROM `".$prefix."_rating` AS r ".implode(' ',$left)." WHERE r.modul IN (".implode(',',$where).") AND r.uid='$uid' ORDER BY r.time DESC");
if ($db->sql_numrows($query) > 0) {
while($rows = $db->sql_fetchrow($query)) {
$time = $rows['time'];
$text = str_replace (array('{title}','{url}'),array($rows[$rows['modul']],'<a href="'.str_replace("{id}", $rows['url_'.$rows['modul']], $act['rating'][$rows['modul']]['url']).'" target="_blank">'.$rows[$rows['modul']].'</a>'),$act['rating'][$rows['modul']]['lang']);
$usations[] = array ('strtotime' => $time, 'date' => date('Y-m-d', $time), 'time' => date('H:i', $time), 'text' => $text);
}
}
unset($mod,$sql,$left,$data,$where,$query,$rows,$time,$text);
}
###
return users_actions($usations, $type, $uid);
}

function uact_date ($date, $is_time=false, $type='rus') { if (is_integer($date)) {$date=intval($date);$date=date('Y-m-d H:i:s', $date);} list($day, $time) = explode(' ', $date); if ($type=='-') return $day; elseif ($type!='rus') return implode($type, explode('-',$day)); switch($day) { case date('Y-m-d'):$result = 'Сегодня';break; case date( 'Y-m-d', mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")) ):$result = 'Вчера';break; default: { list($y, $m, $d)  = explode('-', $day); $result = $d.' '.str_replace(array('01','02','03','04','05','06','07','08','09','10','11','12'), array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'), $m).' '.$y; } } if($is_time) { list($h, $m, $s)  = explode(':', $time); $result .= ' в '.$h.':'.$m; } return $result; }
function sortbytime($a,$b) { if ($a['strtotime'] == $b['strtotime']) return 0; return ($a['strtotime'] > $b['strtotime']) ? -1 : 1; }
function uact_sclon ($a, $text){$slova = explode('|',$text);if (count($slova)!='3') return $text;else {$i=intval($a);if($i%10==1 && $i%100!=11)$out_str=$slova[0];elseif($i%10>=2&&$i%10<=4&&($i%100<10||$i%100>=20)) $out_str=$slova[1];else $out_str=$slova[2];return $out_str;}}
function users_actions_encode ($a) { if (constant("_CHARSET")=='windows-1251') { header('Content-type: text/html; charset='._CHARSET); foreach ($a as $b=>$c) $d[]='"'.$b.'":"'.addcslashes(str_replace(array("\r","\n"),"",$c), '"').'"'; return '{'.implode(',',$d).'}'; } else return json_encode($a); }
function users_actions($arr1=array(),$type,$uid) {
$length = 20;#Сколько записей выводим по умолчанию
if ($type != 0) $length = intval($_GET['c']);
$load = 10; #Сколько записей может дозагрузить пользователь
$content = $head = $body = $foot = '';
$style = array ('first','second','third','fourth');
$out = array ('body'=>'','head'=>'','foot'=>'');
$c = 0;
if (is_array($arr1) && count($arr1) > 0) {
usort($arr1, 'sortbytime');
$arr2 = array_slice ($arr1, 0, $length);
if (count($arr2) > 0) {
foreach ($arr2 as $inf) $arr3[$inf['date']][] = '<li'.(is_array($arr3[$inf['date']])?' style="margin-top:-4px"':'').'><span>'.$inf['time'].'</span> '.$inf['text'].'</li>';
$content .= '<div class="modal-uact"><div class="modal-uacth">';
$head = '<h3 id="modal-load-1">'.count($arr2).uact_sclon (count($arr2),' последнее| последних| последниx').uact_sclon (count($arr2),' действие| действия| действий').' пользователя</h3>';
$content .= $head;
$content .= '</div><div class="modal-uactb" id="uact-load-2">';
foreach ($arr3 as $date => $text) {if ($c == count($style)) $c = 0; $body .= '<div class="uact-textads ua-'.$style[$c].'"><ul><span class="uact-outer"><span class="uact-inner">'.uact_date($date).'</span></span>'.implode('',$text).'</ul></div>';$c++;}
$content .= $body;
$content .= '<div class="clear"></div></div><div class="modal-uactf">';
if ((count($arr1) - $length) < $load) $load = count($arr1) - $length;
if (count($arr1) > $length) $foot .= '<div class="uact-button uact-orange" id="uact-load-3" rel="'.$uid.'-'.($length + $load).'-'.$length.'">Загрузить ещё '.$load.' '.uact_sclon ($load,'действие|действия|действий').' (ещё '.(count($arr1)-count($arr2)).')</div>';
$content .= $foot;
$content .= '</div></div>';
$out = array ('body' => $body, 'head' => $head, 'foot' => $foot);
}
}
if ($type != 0) echo users_actions_encode ($out);
else return $content;
}
?>