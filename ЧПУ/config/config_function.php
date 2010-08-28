<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

#Функция для ЧПУ (преобразование в транслит)
function url_urltranslit($string) {
$letters = array("а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"e","ж"=>"zh","з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l","м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h","ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"shch","ы"=>"y","э"=>"e","ю"=>"yu","я"=>"ya");
$string = preg_replace("/[_ .,?!\[\](){}]+/", "-", $string);
$string = mb_strtolower($string, 'utf8');
$string = preg_replace("/(ь|ъ)([аеёиоуыэюя])/u", "j\2", $string);
$string = preg_replace("/(ь|ъ)/u", "", $string);
$string = strtr($string, $letters);
$string = preg_replace("/j{2,}/", "j", $string);
$string = preg_replace("/[^0-9a-z-]+/", "", $string);
$string = preg_replace(array('#union#si','#outfile#si','#select#si','#alter#si','#insert#si','#drop#si','#truncate#si'), array('u-nion','o-utfile','s-elect','a-lter','i-nsert','d-rop','t-runcate'), $string);
$string = preg_replace("/-+/", "-", $string);
$string=trim($string,'-');
return $string;
}
###
#Функция для получения уникального url ЧПУ url_uniq(array('url'=>'Заголовок', 'where'=>'AND `id`!=17', 'table'=>'_stories'));
function url_uniq($in,$cut,$url='url') {
global $db, $prefix;
if (intval($in['id'])==0) {$in['id']=2;$translite=url_urltranslit(url_cut(html_entity_decode($in['url'], ENT_COMPAT, 'utf-8'), $cut));}
else {$translite=url_urltranslit(html_entity_decode($in['url'], ENT_COMPAT, 'utf-8'));}
list($count) = $db->sql_fetchrow($db->sql_query("SELECT COUNT(*) FROM `".$prefix.$in['table']."` WHERE `$url`='$translite' ".$in['where']));
if ($count>0) {
$a=preg_match('#\A([0-9A-z-]+)([- /.])([\d]+)\z#si', $translite, $b);
if ($a>0) {$in['id']=$b[3]+1;$in['url']=preg_replace('#\A([0-9A-z-]+)([- /.])([\d]+)\z#', '$1-'.$in['id'], $translite);}
else $in['url']=$translite.'-'.$in['id'];
return url_uniq($in,$cut,$url);
} else return $translite;
}
###
#Функция для обрезания строки (может обрезать как слева так и справа) url_cut('Строка','Максимальное допустимое кол-во символов','Окончание','l - слева, r - справа')
function url_cut($str,$par=0, $end='',$lr='r') {
if ($par==0) return $str;
if (mb_strlen($str,'utf-8')>intval($par)) {
if ($lr=='r') {$str=preg_replace('~ [^ ]*$~','',mb_substr($str,0,intval($par), 'utf-8'));$str = rtrim($str, ' @"#№;$%:^&*()_=+`~\'<>{}[]|-/\,.!?-').$end;
} else {$str=preg_replace('~\A[^ ]* ~','',mb_substr($str,-intval($par), intval($par),'utf-8'));$str = $end.ltrim($str, ' @"#№;$%:^&*()_=+`~\'<>{}[]|-/\,.!?-');}
}
return $str;
}
###
function url_fun ($a,$b=1) {
if ($b==1) {if (isset($a['url'])) return preg_replace("/[^0-9a-z-]+/", "", $a['url']);else return 0;}
elseif ($b==2) {if (is_numeric($a['url'])) return $a['id']."='".$a['url']."'";else return $a['row']."='".$a['url']."'";}
}
?>