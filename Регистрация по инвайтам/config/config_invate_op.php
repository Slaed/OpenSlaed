<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

$invate['points']['comments']=2;
$invate['points']['ratings']=10;
$invate['points']['points']=0.001;
$invate['points']['blogs']=2;
$invate['points']['news']=5;
$invate['points']['files']=5;
$invate['points']['topics']=5;

$ipoints['types']['comments']=array(1,_INVATE_7,'sql'=>array('table'=>'comment','select'=>'COUNT(id)','where'=>'`uid`'));
$ipoints['types']['ratings']=array(1,_INVATE_8,'sql'=>array('table'=>'users','select'=>'(2*floor(user_totalvotes/5)-user_votes)','where'=>'`user_id`'));
$ipoints['types']['points']=array(1,_INVATE_9,'sql'=>array('table'=>'users','select'=>'`user_points`','where'=>'`user_id`'));
$ipoints['types']['blogs']=array(1,_INVATE_10,'sql'=>array('table'=>'blogs_comment','select'=>'COUNT(id)','where'=>'`uid`'));
$ipoints['types']['news']=array(1,_INVATE_11,'sql'=>array('table'=>'stories','select'=>'COUNT(sid)','where'=>'`status`="1" AND `uid`'));
$ipoints['types']['files']=array(1,_INVATE_12,'sql'=>array('table'=>'files','select'=>'COUNT(lid)','where'=>'`status`="1" AND `uid`'));
$ipoints['types']['topics']=array(1,_INVATE_13,'sql'=>array('table'=>'blogs_topics','select'=>'COUNT(id)','where'=>'`uid`'));

?>