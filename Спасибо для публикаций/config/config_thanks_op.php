<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");
#uid - id того, кому говорят "Спасибо"
#status - 1 - вкл/0 - выкл кнопку спасибо для данного модуля
$thanks['module']['news']=array('status'=>1,'uid'=>"SELECT `uid` FROM `".$prefix."_stories` WHERE `sid`='{id}'");
$thanks['module']['files']=array('status'=>1,'uid'=>"SELECT `uid` FROM `".$prefix."_files` WHERE `lid`='{id}'");
$thanks['charset']='utf-8'; #utf-8,windows-1251
?>