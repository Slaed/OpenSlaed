<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

include('config/config_popup.php');
if (isset($_COOKIE['mu'])) $msq['cookie']['mu']=unserialize(base64_decode($_COOKIE['mu']));
if (isset($_COOKIE['mg'])) $msq['cookie']['mg']=unserialize(base64_decode($_COOKIE['mg']));
function msg_text () { global $user,$msq; $c['types']['oneonly']=$c['types']['users']=$c['types']['guest']=$c['types']['all']=$out['oneonly']=$out['users']=$out['guest']=$out['all']=array(); if (is_user()) { $a=explode("\n",$msq['text']['user']); foreach ($a as $b) { if (trim($b)!='') { if (preg_match("#^to ([^:]+):#si",$b)) { if (preg_match("#^to ".$user[1].":#si",$b)) $out['oneonly'][]=trim(preg_replace("#^to ".$user[1].":#si","",$b)); } else { $out['users'][]=trim($b); }}} unset($a,$b); } else { $a=explode("\n",$msq['text']['guest']); foreach ($a as $b) {if (trim($b)!='') $out['guest'][]=trim($b);} unset($a,$b); } $a=explode("\n",$msq['text']['all']); foreach ($a as $b) {if (trim($b)!='') $out['all'][]=trim($b);} unset($a,$b); $m=0; foreach (array_reverse($out) as $a=>$b) {foreach ($b as $d=>$e) {$c['types'][$a][]=$m;$m++;}} return array('message'=>array_merge($out['oneonly'],$out['users'],$out['guest'],$out['all']), 'types'=>$c['types']); }
function msg_cookie($a,$b) { global $msq; $c=time()+60*60*24*365; setcookie($a,base64_encode(serialize(array('time'=>time(),'count'=>$b,'old'=>$msq['old']))),$c); }
function msg_chk_cookie($a,$b) { global $msq; if (isset($msq['cookie'][$a]['time'],$msq['cookie'][$a]['count'],$msq['cookie'][$a]['old']) && $msq['cookie'][$a]['old']==$msq['old'] && count($b)>intval($msq['cookie'][$a]['count'])+1) return true; else return false; }

?>