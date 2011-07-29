<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("BLOCK_FILE")) {Header("Location: ../index.php");exit;}

global $msq;
$out=msg_text();
$stop=$text=$type='';
if (is_user() && $msq['off']['mu']['status']==1 && intval($_COOKIE['mushow'])==1 || !is_user() && $msq['off']['mg']['status']==1 && intval($_COOKIE['mgshow'])==1) $stop=1;
if (count($out['message'])>0 && $msq['status']==1 && intval($stop)!=1) {
if (is_user()) $a='mu';
else $a='mg';
if ($msq['loop']==1 || !isset($msq['cookie'][$a]['count']) || count($out['message'])>intval($msq['cookie'][$a]['count'])+1) {
if (count($out['message'])<=intval($msq['cookie'][$a]['count'])+1 || !isset($msq['cookie'][$a]['count'])) $next=0;
else $next=intval($msq['cookie'][$a]['count'])+1;
if (in_array($next,$out['types']['oneonly'])) $type=' личное';
elseif (in_array($next,$out['types']['users'])) $type=' для пользователей';
elseif (in_array($next,$out['types']['guest'])) $type=' для гостей';
elseif (in_array($next,$out['types']['all'])) $type=' для всех';
if (msg_chk_cookie($a,$out['message'])) {
if (intval($msq['cookie'][$a]['time'])+$msq['time']<time()) {
msg_cookie($a,intval($msq['cookie'][$a]['count'])+1);
$text=$out['message'][intval($msq['cookie'][$a]['count'])+1];
}} else {msg_cookie($a,0);$text=$out['message'][0];}}}
unset($out,$next);
$position=explode(',',$msq['position']);
array_walk($position, create_function('&$val', '$val = "\"".trim($val)."\"";'));
$content = '<script type="text/javascript">
$(function(){
$("#dialog").dialog({
autoOpen: false,
position: ['.implode(',',$position).'],
show: "'.$msq['show'].'",
hide: "'.$msq['hide'].'",
modal: '.$msq['modal'].',
draggable: '.$msq['draggable'].',
resizable: '.$msq['resizable'].',
zIndex: 99999
});';
if ($text) {
$content .='setTimeout(function() { $("#dialog").dialog("open")}, '.$msq['timeout'].');';
if (is_user() && $msq['off']['mu']['status']==1 || !is_user() && $msq['off']['mg']['status']==1) $text ='<div style="text-align:right;"><small id="msg_disabled"><a href="#" onclick="messages_disabled(\''.$a.'\','.$msq['off'][$a]['time'].'); return false;" title="Отключить уведомления">[ Отключить уведомления ]</a></small></div><p style="color:#0C618E; text-align:left;">'.$text.'</p>';
else $text ='<p style="color:#0C618E; text-align:left;">'.$text.'</p>';
}
$content .='});
</script>
<div id="dialog" title="Уведомление'.$type.'" style="display:none;">'.$text.'</div>
';
?>