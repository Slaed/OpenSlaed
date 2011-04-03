<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

function new_vote_graphic ($a,$b=1) {
$vote['plus']=floor($a['total']/5);
$vote['minus']=$a['votes']-$vote['plus'];
$out['sum']=$vote['plus']-$vote['minus'];
$out['title']=_NEW_RATE_1.$vote['plus']._NEW_RATE_2.$vote['minus'];
if ($out['sum']<0) {
$out['type']='mn';
} elseif ($out['sum']>0) {
$out['type']='pl';
$out['sum']='+'.$out['sum'];
} else $out['type']='nt';
if (!$a['bodytext'] || $a['bodytext'] && $a['isbody']) {
$check=new_rating(array($a['mod'],$a['id']),'check',$b);
if ($check==0) $content="<span class='rating_nt ".$out['type']."' title='".$out['title']."'>".$out['sum']."<span class='new_rating_yes' title='"._NEW_RATE_8."'>&nbsp;</span></span>";
elseif ($check==1) $content="<span class='rating_nt ".$out['type']."' title='"._NEW_RATE_6."'>".$out['sum']."</span>";
else $content="<span class='rating_nt ".$out['type']."' title='".$out['title']."'><span class='new_rating plus' title='"._NEW_RATE_3."' onclick=\"new_rating('".$a['id']."','".$a['mod']."','1');\">&nbsp;</span>".$out['sum']."<span class='new_rating minus' title='"._NEW_RATE_4."' onclick=\"new_rating('".$a['id']."','".$a['mod']."','0');\">&nbsp;</span></span>";
} else $content="<span class='rating_nt ".$out['type']."' title='".$out['title']."'>".$out['sum']."</span>";
return $content;
}

function new_rating ($a=array(),$b='',$f=1) {
global $db,$prefix,$user,$out;
$c=array();
$uid = (is_user()) ? intval(substr($user[0], 0, 11)) : 0;
$ip = getip();
include("config/config_ratings.php");
if (isset($a[0])) $mod=analyze($a[0]);
if (isset($a[1])) $id=intval($a[1]);
$rating = (isset($_GET['rating'])) ? intval($_GET['rating']) : 1;
$con = explode("|", $confra[strtolower($mod)]);
$past = time() - intval($con[0]);
$cookies = (isset($_COOKIE[''.substr($mod, 0, 2).'-'.$id.''])) ? intval($_COOKIE[substr($mod, 0, 2).'-'.$id]) : "";
#Настройки
if ($mod=='news') $c=array('select'=>'ratings,score','table'=>'_stories','update'=>array('summ'=>'score', 'count'=>'ratings'),'where'=>'sid','points'=>33);
if ($mod=='files') $c=array('select'=>'votes,totalvotes','table'=>'_files','update'=>array('summ'=>'totalvotes', 'count'=>'votes'),'where'=>'lid','points'=>12);
if ($mod=='account') $c=array('select'=>'user_votes,user_totalvotes','table'=>'_users','update'=>array('summ'=>'user_totalvotes', 'count'=>'user_votes'),'where'=>'user_id','points'=>2);
if ($b=='select') {
if (is_array($out[$mod]) && $f==1) return $out;
$out[$mod]=array();
$db->sql_query("DELETE FROM ".$prefix."_rating WHERE time<'$past' AND modul='$mod'");
$result=$db->sql_query("SELECT mid FROM ".$prefix."_rating WHERE (modul='".$mod."' AND host='$ip') OR (modul='".$mod."' AND uid='$uid' AND uid!='0')");
while (list($ids) = $db->sql_fetchrow($result)) $out[$mod][]=$ids;
return $out;
}
if ($b=='check') {
$d=new_rating(array($mod,$id),'select',$f);
if (!$con[1]) return 1;
elseif ($cookies == $id || in_array($id,$d[$mod])) return 0;
else return 2;
}
if ($b=='update' && $id>0 && $mod!='') {
$e=new_rating(array($mod,$id),'check',$f);
if ($e==2) {
setcookie(substr($mod, 0, 2)."-".$id, $id, time() + intval($con[0]));
$db->sql_query("INSERT INTO ".$prefix."_rating VALUES (NULL, '$id', '$mod', '".time()."', '$uid', '$ip')");
$up=($rating==1)?5:0;
$db->sql_query("UPDATE ".$prefix.$c['table']." SET ".$c['update']['count']."=".$c['update']['count']."+1, ".$c['update']['summ']."=".$c['update']['summ']."+$up WHERE ".$c['where']."='$id'");
update_points($c['points']);
$o['text']=_NEW_RATE_7;
} elseif ($e==0) $o['text']=_NEW_RATE_5;
else $o['text']=_NEW_RATE_6;
list($count,$summ) = $db->sql_fetchrow($db->sql_query("SELECT ".$c['select']." FROM ".$prefix.$c['table']." WHERE ".$c['where']."='$id'"));
$o['html']=new_vote_graphic(array('total'=>$summ,'votes'=>$count,'bodytext'=>0,'isbody'=>1,'mod'=>$mod,'id'=>$id),0);
$o['status']=$e;
echo json_encode($o);
}
}

?>