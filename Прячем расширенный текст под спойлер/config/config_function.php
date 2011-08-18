<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

$tts['news']=array('status'=>1,'text'=>'Полная новость (открыть/скрыть)');
function texttospoiler ($str='') {global $tts,$conf; if ($str!='' && is_array($tts[$conf['name']]) && $tts[$conf['name']]['status']==1) return "<div class='test_to_spoiler'><div class='to_spoiler'><a href='#' title='".$tts[$conf['name']]['text']."'>".$tts[$conf['name']]['text']."</a></div><div class='spoiler_text'>$str</div></div>";else return '';}

?>