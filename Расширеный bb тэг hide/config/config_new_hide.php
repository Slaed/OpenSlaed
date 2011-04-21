<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

#Комментарии
$nhide['types']['comments']=array(1,_NEW_HIDE_1,_NEW_HIDE_9,'sql'=>array('table'=>'comment','select'=>'COUNT(id)','where'=>'`uid`'));
#Рейтинг (в формате +/- 1)
$nhide['types']['ratings']=array(1,_NEW_HIDE_2,_NEW_HIDE_10,'sql'=>array('table'=>'users','select'=>'(2*floor(user_totalvotes/5)-user_votes)','where'=>'`user_id`'));
#Пункты
$nhide['types']['points']=array(1,_NEW_HIDE_3,_NEW_HIDE_11,'sql'=>array('table'=>'users','select'=>'`user_points`','where'=>'`user_id`'));
#Новости
$nhide['types']['news']=array(1,_NEW_HIDE_5,_NEW_HIDE_13,'sql'=>array('table'=>'stories','select'=>'COUNT(sid)','where'=>'`status`="1" AND `uid`'));
#Файлы
$nhide['types']['files']=array(1,_NEW_HIDE_6,_NEW_HIDE_14,'sql'=>array('table'=>'files','select'=>'COUNT(lid)','where'=>'`status`="1" AND `uid`'));
#Комментарии в блогах
$nhide['types']['blogs']=array(0,_NEW_HIDE_4,_NEW_HIDE_12,'sql'=>array('table'=>'blogs_comment','select'=>'COUNT(id)','where'=>'`uid`'));
#Топики
$nhide['types']['topics']=array(0,_NEW_HIDE_16,_NEW_HIDE_15,'sql'=>array('table'=>'blogs_topics','select'=>'COUNT(id)','where'=>'`uid`'));

?>