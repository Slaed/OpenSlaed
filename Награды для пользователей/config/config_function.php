<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

function user_awards ($id = 0, $c = 0) {
global $awards;
$i = 0;
$out = '';
if (!isset($awards) || !is_array($awards)) include("config/config_awards.php");
if (intval($id)>0 && count($awards['cats']) > 0 && is_array($awards['users'][$id]) && count($awards['users'][$id]) > 0) {
$out .='<div align="center">';
foreach ($awards['users'][$id] as $a => $b) {
$i++;
$out .='<img src="/images/awards/'.$awards['cats'][$b['cid']][1].'" alt="'.$awards['cats'][$b['cid']][0].'" id="awards-'.$a.'-'.$id.'" title="'.$awards['cats'][$b['cid']][0].'" class="awards_uid" />';
if ($c > 0 && $i!=count($awards['users'][$id]) && ($i%$c) == 0) $out .='<br />';
}
$out .='</div>';
}
return $out;
}


function awards_see () {
global $db,$prefix;
$charset = _CHARSET; #utf-8, windows-1251
header('Content-type: text/html; charset='.$charset);
$out='';
$i=1;
if (isset($_GET['uid']) && intval($_GET['uid'])>0) {
$result=$db->sql_query("SELECT a.comment, a.date, c.title, c.img FROM `".$prefix."_awarded` AS a LEFT JOIN `".$prefix."_awards` AS c ON (a.cid=c.id) WHERE a.uid='".intval($_GET['uid'])."' ORDER BY a.date DESC");
while(list($comment,$date,$title,$img) = $db->sql_fetchrow($result)) { 
$out .='<tr'.(($i%2)?'':' class="odd"').'><td><img src="/images/awards/'.$img.'" title="'.$title.'" alt="'.$title.'"></td><td class="alft">'.$title.'</td><td class="alft">'.$comment.'</td><td class="alft">'.$date.'</td></tr>';
$i++; 
}
}
if (!$out) echo '<br /><table class="awards_see" summary="Награды пользователя"><caption>У данного пользователя пока нет наград!</caption></table>';
else echo '<br /><table class="awards_see" summary="Награды пользователя"><caption>Награды пользователя</caption><thead><tr class="odd"><th class="col" abbr="Изображение награды">Награда</th><th scope="col" abbr="Название награды">Название</th><th scope="col" abbr="Комментарий к награде">Комментарий</th><th scope="col" abbr="Дата награждения">Дата</th></tr></thead><tbody>'.$out.'</tbody></table>'; 
}
?>