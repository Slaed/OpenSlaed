<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");

function awards_navi() {
global $admin_file;
panel();
open();
echo "<h1>Награды</h1>"
."<h5>[ <a href='".$admin_file.".php?op=awards'>Награжденные</a> | <a href='".$admin_file.".php?op=awards_view'>Награды</a> | <a href='".$admin_file.".php?op=awards_add'>Добавить награду</a> | <a href='".$admin_file.".php?op=awarded_add'>Наградить</a> ]</h5>";
close();
}

function awards_list_img ($not=array(),$def='') {
$a = array();
$dirname = "images/awards/";
$scan    = scandir($dirname);
$ftypes  = "#^(.+)\.(jpg|gif|png|jpeg)$#si";
if (count($scan)==0) return false;
foreach ($scan as $filename) {
if (is_file($dirname.$filename) && preg_match($ftypes, $filename) && (count($not)==0 || !in_array($filename,$not))) {
if ($def != '' && $filename == $def) {
$a[]="<option value='".$dirname.$filename."' selected='selected'>$filename</option>";
$b[0]=$dirname.$filename;
} else {
$a[]="<option value='".$dirname.$filename."'>$filename</option>";
$b[]=$dirname.$filename;
}
}
}
if (count($a)==0) return false;
else return array('o'=>implode($a,''),'i'=>$b[0]);
}

function awards_add () {
global $prefix, $db, $admin_file;
head();
awards_navi();
open();
$id = 0;
$title = '';
$def = '';
$arr = array();
if (!isset($_GET['id']) || intval($_GET['id']) == 0) {
$result = $db->sql_query("SELECT img FROM ".$prefix."_awards");
if ($db->sql_numrows($result) > 0) while (list($imgs) = $db->sql_fetchrow($result)) $arr[]=$imgs;
} else {
$id = intval($_GET['id']);
$result = $db->sql_query("SELECT img, title FROM ".$prefix."_awards WHERE `id`='$id'");
if ($db->sql_numrows($result) > 0) list($def,$title) = $db->sql_fetchrow($result);
else {
warning("Редактируемая награда не найдена!", "", "", 2);
close();
foot();
die();
}
}
$img = awards_list_img($arr,$def);
echo "<h2>Добавление награды</h2>";
if (is_array($img)) {
echo "<script language='JavaScript' type='text/javascript'>function ShowImage(id,m) {var d=document.getElementById(id);if (d && m.value!='') {d.style.display = 'block';d.src = m.value;} else if (m.value=='') d.style.display = 'none'}</script>";
echo "<form name='post' action='".$admin_file.".php' method='post'>";
echo "<div class='left'>Название награды:</div><div class='center'><input type='text' name='title' size='65' class='admin' value='$title'></div>";
echo "<div class='left'>Изображение:</div><div class='center'><select name='img' onChange=\"ShowImage('pictures',this)\" class='admin'>".$img['o']."</select></div>";
echo "<div class='left'>Предпросмотр:</div><div class='center'><img src='".$img['i']."' id='pictures'></div>";
echo "<div class='button'><input type='hidden' name='id' value='$id'><input type='hidden' name='op' value='awards_add_save'>";
echo "<input type='submit' value='Сохранить' class='fbutton'></div></form>";
} else warning("Отсутсвуют изображения наград, либо все изображения уже использованы!", "", "", 2);
close();
foot();
}

function awards_add_save () {
global $prefix, $db, $admin_file;
$in['title'] = htmlspecialchars(stripslashes($_POST['title']),ENT_QUOTES);
if ($in['title']=='') $in['title']='Награда';
$in['img'] = text_filter(basename($_POST['img']));
if (intval($_POST['id'])>0) {
$in['id'] = intval($_POST['id']);
$db->sql_query("UPDATE `".$prefix."_awards` SET `title`='".$in['title']."', `img`='".$in['img']."' WHERE `id`='".$in['id']."'");
} else $db->sql_query("INSERT INTO `".$prefix."_awards` VALUES (NULL,'".$in['title']."','".$in['img']."')");
Header("Location: ".$admin_file.".php?op=awards_view");
}

function awards_view () {
global $prefix, $db, $admin_file;
$p = 20;
head();
awards_navi();
open();
$dirname = "images/awards/";
$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
$offset = ($num-1) * $p;
$result = $db->sql_query("SELECT COUNT(a.id), c.id, c.title, c.img FROM ".$prefix."_awards AS c LEFT JOIN ".$prefix."_awarded AS a ON (c.id=a.cid) GROUP BY c.id ORDER BY c.id DESC LIMIT $offset, $p");
if ($db->sql_numrows($result) > 0) {
warning("Внимание при удалении награды, они будут также удалены и у награжденных!", "", "", 1);
echo '<script type="text/javascript">function checkAll(oForm, cbName, checked){for (var i=0; i < oForm[cbName].length; i++) oForm[cbName][i].checked = checked;}</script>';
echo "<form action='".$admin_file.".php?op=awards_del' method='post'><table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_id'><tr>
<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick=\"checkAll(this.form,'delete[]',this.checked)\" type='checkbox' value='delete'>Выбрать всё</span>
<th>№</th><th>Изображение</th><th>Название</th><th>Награжденных</th><th>Функции</th></tr>";
while (list($count,$id,$title,$img) = $db->sql_fetchrow($result)) {
echo "<tr class='bgcolor1'><td align='center'>".$id."</td>";
echo "<td align='center'><img src='".$dirname.$img."' alt='Награда' /></td>";
echo "<td>".$title."</td>";
echo "<td align='center'><b><a href='$admin_file.php?op=awards&type=cid&var=$id' title='Показать всех награжденных данной наградой'>[ ".$count." ]</a></b></td>";
echo "<td align='center'>".ad_edit($admin_file.".php?op=awards_add&id=".$id)."<input class='ids_del' type='checkbox' name='delete[]' value='$id'></td>";
echo "</tr>";
}
echo "</table><div style='float:right;margin:10px 0;'><input type='hidden' name='op' value='awards_del'><input type='submit' value='Удалить выбранные'></div></form>";
list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(id) FROM ".$prefix."_awards"));
$numpages = ceil($numstories / $p);
num_page("", $numstories, $numpages, $p, "op=awards_view&");
} else warning("Не найдено ни одной награды, добавьте награды!", "", "", 2);
close();
foot();
}

function awarded_add() {
global $prefix, $db, $admin_file;
head();
awards_navi();
open();
$arr = array();
$result = $db->sql_query("SELECT id, title FROM ".$prefix."_awards");
if ($db->sql_numrows($result) > 0) while (list($cid,$title) = $db->sql_fetchrow($result)) $arr[]="<option value='$cid'>$title</option>";
echo "<h2>Награждение пользователей</h2>";
if (count($arr)>0) {
echo "<form name='post' action='".$admin_file.".php' method='post'>";
echo "<div class='left'>ID, награждаемых пользователей, через запятую:</div><div class='center'><input type='text' name='uid' size='65' class='admin'></div>";
echo "<div class='left'>Награда:</div><div class='center'><select name='cid' class='admin'>".implode($arr,'')."</select></div>";
echo "<div class='left'>Комментарий:</div><div class='center'><input type='text' name='comment' size='65' class='admin'></div>";
echo "<div class='button'><input type='hidden' name='op' value='awarded_add_save'>";
echo "<input type='submit' value='Наградить' class='fbutton'></div></form>";
} else warning("Не найдены награды для награждения!", "", "", 2);
close();
foot();
}

function awarded_add_save() {
global $prefix, $db, $admin_file;
$ids = $arr = array();
if ($_POST['uid']!='' && intval($_POST['cid'])>0) {
$arr = array_unique(array_map('intval',explode(',',$_POST['uid'])));
if (count($arr)>0) {
$result = $db->sql_query("SELECT user_id FROM ".$prefix."_users WHERE user_id IN (".implode($arr,',').")");
if ($db->sql_numrows($result) > 0) while (list($uid) = $db->sql_fetchrow($result)) $ids[]=$uid;
if (count($ids)>0) {
foreach ($ids as $id) $db->sql_query("INSERT INTO ".$prefix."_awarded VALUES (NULL, '$id', '".intval($_POST['cid'])."', now(), '".text_filter($_POST['comment'])."')");
}
}
}
Header("Location: ".$admin_file.".php?op=awards");
}

function awards() {
global $prefix, $db, $admin_file;
$p = 20;
if (isset($_GET['type'],$_GET['var']) && in_array($_GET['type'],array('uid','cid'))) {
$sql['where'] = " WHERE a.".text_filter($_GET['type'])."='".intval($_GET['var'])."'";
$numget = 'type='.text_filter($_GET['type'])."&var=".intval($_GET['var']).'&';
} else $sql['where'] = $numget = '';
head();
awards_navi();
open();
$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
$offset = ($num-1) * $p;
$result = $db->sql_query("SELECT a.id, a.uid, u.user_name, c.id, c.title, a.date, a.comment FROM ".$prefix."_awarded AS a LEFT JOIN ".$prefix."_awards AS c ON (a.cid=c.id) LEFT JOIN ".$prefix."_users AS u ON (a.uid=u.user_id)".$sql['where']." ORDER BY a.date DESC LIMIT $offset, $p");
if ($db->sql_numrows($result) > 0) {
echo '<script type="text/javascript">function checkAll(oForm, cbName, checked){for (var i=0; i < oForm[cbName].length; i++) oForm[cbName][i].checked = checked;}</script>';
echo "<form action='".$admin_file.".php?op=awarded_del' method='post'><table width='100%' border='0' cellpadding='3' cellspacing='1' class='sort' id='sort_id'><tr>
<span style='float:right;font-size:11px;margin-bottom:5px;background:#C3F5A4;padding: 5px 10px;border:1px #43B000 solid;'><input onclick=\"checkAll(this.form,'delete[]',this.checked)\" type='checkbox' value='delete'>Выбрать всё</span>
<th>№</th><th>Имя</th><th>Награда</th><th>Комментарий</th><th>Дата</th><th>Функции</th></tr>";
while (list($id,$uid,$uname,$cid,$title,$date,$comment) = $db->sql_fetchrow($result)) {
echo "<tr class='bgcolor1'><td align='center'>".$id."</td>";
echo "<td><b><a href='$admin_file.php?op=awards&type=uid&var=$uid' title='Показать все награды пользователя'>[?]</a>&nbsp;".user_info($uname,1)."</b></td>";
echo "<td><b><a href='$admin_file.php?op=awards&type=cid&var=$cid' title='Показать всех награжденных данной наградой'>$title</a></b></td>";
echo "<td>$comment</td>";
echo "<td>$date</td>";
echo "<td align='center'><input class='ids_del' type='checkbox' name='delete[]' value='$id'></td>";
echo "</tr>";
}
echo "</table><div style='float:right;margin:10px 0;'><input type='hidden' name='op' value='awarded_del'><input type='submit' value='Удалить выбранные'></div></form>";
list($numstories) = $db->sql_fetchrow($db->sql_query("SELECT Count(a.id) FROM ".$prefix."_awarded AS a".$sql['where']));
$numpages = ceil($numstories / $p);
num_page("", $numstories, $numpages, $p, "op=awards&".$numget);
} else warning("Не найдено ни одного награждённого!", "", "", 2);
close();
foot();
}

function awards_to_file () {
global $prefix, $db;
$text = '';
$result = $db->sql_query("SELECT id, title, img FROM ".$prefix."_awards");
if ($db->sql_numrows($result) > 0) {
while (list($id,$title,$img) = $db->sql_fetchrow($result)) $out[]="$id => array ('$title', '$img')";
$text .="\$awards['cats'] = array (".PHP_EOL;
$text .=implode(','.PHP_EOL,$out);
$text .=PHP_EOL.");".PHP_EOL;
} else $text .="\$awards['cats'] = array ();".PHP_EOL;
unset ($id,$title,$result,$out,$img);
$result = $db->sql_query("SELECT id, uid, date, comment, cid FROM ".$prefix."_awarded ORDER BY date ASC");
if ($db->sql_numrows($result) > 0) {
while (list($id,$uid,$date,$comment,$cid) = $db->sql_fetchrow($result)) {
$in[$uid][]="   $id => array ('cid'=>'$cid', 'comment'=>'$comment', 'date' => '$date')";
}
foreach ($in as $a=>$b) {$out[] = "$a => array (".PHP_EOL.implode(','.PHP_EOL,$b).PHP_EOL.")";}
$text .="\$awards['users'] = array (".PHP_EOL;
$text .=implode(','.PHP_EOL,$out);
$text .=PHP_EOL.");".PHP_EOL;
} else $text .="\$awards['users'] = array ();".PHP_EOL;
save_conf("config/config_awards.php", $text);
}

switch($op) {
	case "awards":
	awards();
	break;
	
	case "awards_view":
	awards_view();
	break;
	
	case "awards_add":
	awards_add();
	break;
	
	case "awards_add_save":
	awards_add_save();
	awards_to_file();
	break;
	
	case "awards_del":
  if (count($_POST['delete'])>0) {
  $db->sql_query("DELETE FROM ".$prefix."_awards WHERE id IN (".implode(',',$_POST['delete']).")");
  $db->sql_query("DELETE FROM ".$prefix."_awarded WHERE cid IN (".implode(',',$_POST['delete']).")");
  }
  awards_to_file();
  referer($admin_file.".php?op=awards_view");
  break;
  
	case "awarded_add":
	awarded_add();
	break;
	
	case "awarded_add_save":
	awarded_add_save();
	awards_to_file();
	break;
	
	case "awarded_del":
  if (count($_POST['delete'])>0) {
  $db->sql_query("DELETE FROM ".$prefix."_awarded WHERE id IN (".implode(',',$_POST['delete']).")");
  }
  awards_to_file();
  referer($admin_file.".php?op=awards");
	break;

}
?>