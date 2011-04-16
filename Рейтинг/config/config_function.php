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
if ((!$a['bodytext'] || $a['bodytext'] && $a['isbody']) && ($a['useronly']==0 || $a['useronly']==1 && is_user())) {
$check=new_rating(array($a['mod'],$a['id']),'check',$b);
if ($check==0) $content="<span class='rating_nt ".$out['type']."' title='".$out['title']."' onclick='new_whiiswho(\"".$a['mod']."\",".$a['id'].");'>".$out['sum']."<span class='new_rating_yes' title='"._NEW_RATE_8."'>&nbsp;</span></span>";
elseif ($check==1) $content="<span class='rating_nt ".$out['type']."' title='"._NEW_RATE_6."' onclick='new_whiiswho(\"".$a['mod']."\",".$a['id'].");'>".$out['sum']."</span>";
else $content="<span class='rating_nt'><span class='new_rating plus' title='"._NEW_RATE_3."' onclick=\"new_rating('".$a['id']."','".$a['mod']."','1');\">&nbsp;</span><span class='rating_nt ".$out['type']."' onclick='new_whiiswho(\"".$a['mod']."\",".$a['id'].");'>".$out['sum']."</span><span class='new_rating minus' title='"._NEW_RATE_4."' onclick=\"new_rating('".$a['id']."','".$a['mod']."','0');\">&nbsp;</span></span>";
} else $content="<span class='rating_nt ".$out['type']."' title='".$out['title']."' onclick='new_whiiswho(\"".$a['mod']."\",".$a['id'].");'>".$out['sum']."</span>";
return $content;
}

function new_rating ($a=array(),$b='',$f=1) {
global $db,$prefix,$user,$out;
$n=10;
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
if (!$con[1] || $con[4]==1 && !is_user()) return 1;
elseif ($cookies == $id || in_array($id,$d[$mod])) return 0;
else return 2;
}
if ($b=='update' && $id>0 && $mod!='') {
$e=new_rating(array($mod,$id),'check',$f);
if ($e==2) {
setcookie(substr($mod, 0, 2)."-".$id, $id, time() + intval($con[0]));
$db->sql_query("INSERT INTO ".$prefix."_rating VALUES (NULL, '$id', '$mod', '".time()."', '$uid', '$ip')");
$comment=(isset($_GET['comment']) && save_text($_GET['comment'])!='')?mb_substr(save_text($_GET['comment']),0,30,'utf-8'):'';
$db->sql_query("INSERT INTO ".$prefix."_whoiswho VALUES (NULL, '$id', '$mod', '$uid', now(), '$ip', '".(($rating==1)?1:-1)."', '$comment')");
$m=$db->sql_numrows($db->sql_query("SELECT `id` FROM ".$prefix."_whoiswho WHERE `iid`='".$id."' AND `module`='".$mod."'"));
if ($m>$n) $db->sql_query("DELETE FROM ".$prefix."_whoiswho WHERE `iid`='".$id."' AND `module`='".$mod."' ORDER BY `date` ASC LIMIT ".($m-$n));
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
function new_ratings_date($date, $is_time=false, $type='rus') {if (is_integer($date)) {$date=intval($date);$date=date('Y-m-d H:i:s', $date);}list($day, $time) = explode(' ', $date);if ($type=='-') return $day;elseif ($type!='rus') return implode($type, explode('-',$day));switch($day) {case date('Y-m-d'):$result = _NEW_RATE_18;break;case date( 'Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")) ):$result = _NEW_RATE_19;break;default: {list($y, $m, $d)  = explode('-', $day);$result = $d.' '.str_replace(array('01','02','03','04','05','06','07','08','09','10','11','12'), explode(',',_NEW_RATE_20), $m).' '.$y;}}if($is_time) {list($h, $m, $s)  = explode(':', $time);$result .= ' в '.$h.':'.$m;}return $result;}

function new_whoiswho ($a) {
global $db,$prefix;
include("config/config_ratings.php");
if ($nnewrate['useronly']==1 && !is_user()) {echo '<br /><table class="whoiswho_rating"><caption>'._NEW_RATE_23.'</caption></table>'; exit();}
$i=2;
$result=$db->sql_query("SELECT v.comment,v.date,v.ip,v.vote,u.user_name,v.uid FROM `".$prefix."_whoiswho` AS v LEFT JOIN `".$prefix."_users` AS u ON (v.uid=u.user_id) WHERE `iid`='".$a['id']."' AND `module`='".$a['mod']."' ORDER BY `date` DESC LIMIT 0, 10");
while(list($comment,$date,$ip,$vote,$name,$uid) = $db->sql_fetchrow($result)) {
if ($uid==0) $name='<span title="'._NEW_RATE_16.': '.implode('.',explode('.',$ip,-2)).'.xx.xx" style="color:#FF5000;font-weight:bold;">'.implode('.',explode('.',$ip,-2)).'.xx.xx</span>';
else $name='<a href="index.php?name=account&op=info&uname='.urlencode($name).'" title="'._NEW_RATE_17.'">'.$name.'</a>';
$out .='<tr'.(($i%2)?' class="odd"':'').'><td>'.$name.'</td><th scope="row" class="column1">'.$comment.'</th><td>'.new_ratings_date($date,1,'rus').'</td><td style="font-weight:bold;color:'.((intval($vote)>0)?'green':'red').';">'.((intval($vote)>0)?'+'.$vote:$vote).'</td></tr>';
$i++;
}
header('Content-type: text/html; charset=utf-8');
if (!$out) echo '<br /><table class="whoiswho_rating" summary="'._NEW_RATE_11.'"><caption>'._NEW_RATE_12.'</caption></table>';
else echo '<br /><table class="whoiswho_rating" summary="'._NEW_RATE_11.'"><caption>'._NEW_RATE_11.'</caption><thead><tr class="odd"><th class="col" abbr="'._NEW_RATE_13.'">'._NEW_RATE_13.'</th><th scope="col" abbr="'._NEW_RATE_21.'">'._NEW_RATE_21.'</th><th scope="col" abbr="'._NEW_RATE_14.'">'._NEW_RATE_14.'</th><th scope="col" abbr="'._NEW_RATE_15.'">'._NEW_RATE_15.'</th></tr></thead><tbody>'.$out.'</tbody></table>';
}

?>