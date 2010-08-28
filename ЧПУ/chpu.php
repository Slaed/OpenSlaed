<?php
define("MODULE_FILE", true);
include("function/function.php");

$result = $db->sql_query("SELECT `title`,`sid` FROM `".$prefix."_stories`");
while(list($title,$sid) = $db->sql_fetchrow($result)) $db->sql_query("UPDATE ".$prefix."_stories SET `url`='".url_uniq(array('url'=>$title, 'table'=>'_stories', 'where'=>'AND `sid`!='.$sid),70)."' WHERE `sid`=".$sid);

$result = $db->sql_query("SELECT `title`,`id` FROM `".$prefix."_categories`");
while(list($title,$id) = $db->sql_fetchrow($result)) $db->sql_query("UPDATE ".$prefix."_categories SET `url`='".url_uniq(array('url'=>$title, 'table'=>'_categories', 'where'=>'AND `id`!='.$id),70)."' WHERE `id`=".$id);

$result = $db->sql_query("SELECT `title`,`lid` FROM `".$prefix."_files`");
while(list($title,$lid) = $db->sql_fetchrow($result)) $db->sql_query("UPDATE ".$prefix."_files SET `chpu`='".url_uniq(array('url'=>$title, 'table'=>'_files', 'where'=>'AND `lid`!='.$lid),70,'chpu')."' WHERE `lid`=".$lid);

?>