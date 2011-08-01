<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

$cal['charset']='utf-8'; # Кодировка (utf-8,windows-1251) + пересохраните данный файл в нужной кодировке
#========Свой=Модуль=1
$cal['lang']['news']=array('новостей','Новости', 'новости');
$cal['lang']['files']=array('файлов','Файлы', 'файлы');
$cal['lang']['quotes']=array('цитат','Цитаты', 'цитаты');
$cal['lang']['holidays']=array('праздников','Праздники', 'праздники');
$cal['lang']['account']=array('дней рождений','Дни рождения', 'дни рождения');
$cal['lang']['blogs']=array('топиков','Блоги', 'топики');
#========Свой=Модуль=1

function cal_sclon ($a, $text){$slova = explode('|',$text);if (count($slova)!='3') return $text;else {$i=intval($a);if($i%10==1 && $i%100!=11)$out_str=$slova[0];elseif($i%10>=2&&$i%10<=4&&($i%100<10||$i%100>=20)) $out_str=$slova[1];else $out_str=$slova[2];return $out_str;}}
function mb_ucasefirst($str){$fc = mb_strtoupper(mb_substr($str,0,1,'UTF8'),'UTF8');return $fc.mb_substr($str,1,mb_strlen($str,'UTF8'),'UTF8');}
function gdate_check ($a,$b) { $c=explode(',', $a); if (preg_match("#[^0-9-]+#", $b)) return false; foreach ($c as $d) { switch (trim($d)) { default:return false;break; case "y-m-d":if (preg_match('#\A(19[7-9][0-9]|20\d\d)([- /.])([1-9]|0[1-9]|1[012])([- /.])(0[1-9]|[12][0-9]|3[01])\z#', $b)) return true;break; case "y-m":if (preg_match('#\A(19[7-9][0-9]|20\d\d)([- /.])([1-9]|0[1-9]|1[012])\z#', $b)) return true;break; case "y":if (preg_match('#\A(19[7-9][0-9]|20\d\d)\z#', $b)) return true;break; case "m-d":if (preg_match('#\A([1-9]|0[1-9]|1[012])([- /.])(0[1-9]|[12][0-9]|3[01])\z#', $b)) return true;break; case "m":if (preg_match('#\A([1-9]|0[1-9]|1[012])\z#', $b)) return true;break; } } return false; }
function showcalendar($ajax=0) {
global $db, $prefix, $conf, $cal;
#========Свой=Модуль=2
$cal['modules']='blogs,news,files,quotes,holidays,account';      #Ненужные модули удалить из данного списка
#========Свой=Модуль=2
$cal['default']='news';                                          #Модуль по умолчанию
if (!$conf['name'] && isset($_GET['cajax'])) $conf['name']=text_filter($_GET['cajax']);
$modules=explode(",", $cal['modules']);
if (in_array($conf['name'], $modules)) $cal['default']=$conf['name'];
if(isset($_GET['cal_date']) && gdate_check('y-m-d,y-m,y,m-d,m',$_GET['cal_date'])==1) {
$gdate = text_filter($_GET['cal_date']);
$gdate = explode("-", $gdate);
if (count($gdate)==3 || (count($gdate)==2 && gdate_check('y',$gdate[0])==1)) {$year=$gdate[0];$month=$gdate[1];}
elseif (count($gdate)==1 && gdate_check('y',$gdate[0])==1) $year=$gdate[0];
else $month=$gdate[0];
if ($month == '') {$time = time();$month = date('n',$time);}
if ($year == '') {$time = time();$year = date('Y',$time);}
}
if($month == '' && $year == '') {$time = time();$month = date('n',$time);$year = date('Y',$time);}
$month=intval($month);$year=intval($year);
$date = getdate(mktime(0,0,0,$month,1,$year));$today = getdate();
$hours = $today['hours'];
$mins = $today['minutes'];
$secs = $today['seconds'];
if(strlen($hours)<2) $hours="0".$hours;
if(strlen($mins)<2) $mins="0".$mins;
if(strlen($secs)<2) $secs="0".$secs;
$days=date("t",mktime(0,0,0,$month,1,$year));
$start = $date['wday'];
if ($start==0) $start=7;
$name = $date['month'];
$year2 = $date['year'];
$offset = $days + $start - 1;
if($month==12) {$next=1;$nexty=$year + 1;}
else {$next=$month + 1;$nexty=$year;}
if($month==1) {$prev=12;$prevy=$year - 1;}
else {$prev=$month - 1;$prevy=$year;}
if($offset <= 28) $weeks=28; 
elseif($offset > 35) $weeks = 42; 
else $weeks = 35;
$rus[1]=array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
$rus[2]=array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
$rus[3]=array('январе','феврале','марте','апреле','мае','июне','июле','августе','сентябре','октябре','ноябре','декабре');
$number=array('01','02','03','04','05','06','07','08','09','10','11','12');
$eng=array('/January/','/February/','/March/','/April/','/May/','/June/','/July/','/August/','/September/','/October/','/November/','/December/');
$nmonth = preg_replace($eng, $rus[1], $name);
$mnumber = preg_replace($eng, $number, $name);
#=========Свой=Модуль=3
$cal['tpl']['url']="index.php?name=".$cal['default']."&cal_date=";#Шаблон ссылки          Пр.: index.php?name=news&cal_date=
$cal['tpl']['day']="{year}-{month}-{day}";                        #Шаблон ссылки на день  Пр.: index.php?name=news&cal_date=2010-12-31
$cal['tpl']['month']="{year}-{month}";                            #Шаблон ссылки на месяц Пр.: index.php?name=news&cal_date=2010-01
$cal['tpl']['head']="{month}, {year}";                            #Шаблон заголовка Пр.: Январь, 2010
$cal['period']="uniq";                                            #Периодичность повторения событий: year - каждый год; uniq - никогда не повторяется
$cal['max_year']=0;                                               #Ограничить календарь текущим годом: 0 - текущим месяцем, 1 - текущим годом, 2 - без ограничения
$cal['more']['foot']=$cal['more']['head']='';                     #Дополнительная информация в foot или head календаря

###===Модуль=Новостей
if ($cal['default']=='news') {
$result = $db->sql_query("SELECT time FROM ".$prefix."_stories WHERE `time` LIKE '$year-$mnumber-%' AND status!='0' AND time <= now()");
###

###===Модуль=Файлов
} elseif ($cal['default']=='files') { 
$result = $db->sql_query("SELECT date FROM ".$prefix."_files WHERE `date` LIKE '$year-$mnumber-%' AND status!='0' AND date <= now()");
###

###===Модуль=Цитат
} elseif ($cal['default']=='quotes') { 
$result = $db->sql_query("SELECT DATE_FORMAT(`time`, '%Y-%m-%d') FROM ".$prefix."_quotes WHERE DATE_FORMAT(`time`, '%Y-%m-%d') LIKE '$year-$mnumber-%'");
###

###===Модуль=Праздников
} elseif ($cal['default']=='holidays') {
$cal['cat']='rossijskie-prazdniki';                                #Категория с праздниками по умолчанию (можно оставить пустой)
$cal['tpl']['day']="{month}-{day}";                                #Делаем ссылку на просмотр праздников за день вида: index.php?name=holidays&cal_date=12-31
$cal['tpl']['month']="{month}";                                    #Делаем ссылку на просмотр праздников за месяц вида: index.php?name=holidays&cal_date=12
$cal['period']="year";                                             #Указываем, что события повторяются каждый год
$cal['max_year']=2;                                                #Убираем ограничение на листание календаря
if (isset($_GET['cat'])) {$cal['cat']=$_GET['cat'];$cal['add']['url']='&cat='.$cal['cat'];}
elseif (intval($_GET['cal'])==1 || isset($_GET['cal_date'])) $cal['cat']='';
if (isset($cal['cat']) && preg_match("#\A([0-9a-z_-])+\z#", $cal['cat'])) {
$result = $db->sql_query("SELECT title FROM ".$prefix."_holiday_cats WHERE `url`='".text_filter($cal['cat'])."'");
list($title) = $db->sql_fetchrow($result);
$cal['more']['head']='<div style="margin-top:5px;"><a href="#" class="holbut ornge" onclick="javascript:navigate('.$month.','.$year2.',\''.$cal['default'].'&cal=1\');return false;" title="Показать все праздники">'.implode(explode(' ', $title, -1), ' ').'</a></div>';
$result = $db->sql_query("SELECT h.date FROM ".$prefix."_holiday AS h LEFT JOIN ".$prefix."_holiday_cats AS c ON (h.cat=c.id) WHERE `date` LIKE '%-$mnumber-%' AND c.url='".text_filter($cal['cat'])."'");
$cal['tpl']['url']="index.php?name=".$cal['default']."&cat=".text_filter($cal['cat'])."&cal_date=";
} else {
include("config/config_holidays.php");
if ($holiday['main']['dublicate']==0) $result = $db->sql_query("SELECT DISTINCT date, id FROM ".$prefix."_holiday WHERE `date` LIKE '%-$mnumber-%' GROUP BY title");
else $result = $db->sql_query("SELECT date FROM ".$prefix."_holiday WHERE `date` LIKE '%-$mnumber-%'");
}
}
###

###===Дни=Рождения
elseif ($cal['default']=='account') {
$cal['bithday']['min_happy']=1;                                    #Минимальный возраст для отображения
$cal['bithday']['cut']=15;                                         #Максимальная длина ника
$result = $db->sql_query("SELECT user_birthday FROM ".$prefix."_users WHERE `user_birthday` LIKE '%-$mnumber-%' AND $year2 - LEFT( user_birthday, 4 ) >= ".$cal['bithday']['min_happy']);
$cal['tpl']['url']=$cal['more']['foot']='';
$cal['tpl']['day']='#" onclick="javascript:navigate('.$month.',\''.$year2.'\',\''.$cal['default'].'&happy_day={month}-{day}\');return false';
$cal['tpl']['month']='#" onclick="javascript:navigate('.$month.',\''.$year2.'\',\''.$cal['default'].'&happy_day={month}\');return false';
$cal['period']="year";
$cal['max_year']=2;
$cal['bithday']['today']=($today['mday']<10) ? '0'.$today['mday'] : $today['mday'];
$cal['bithday']['month']=($month<10) ? '0'.$month : $month;
$cal['bithday']['get']=explode('-', $_GET['happy_day']);
if (!isset ($_GET['happy_day']) || ($cal['bithday']['get'][1]==$cal['bithday']['today'] && $cal['bithday']['get'][1])) $cal['bithday']['mday']='Сегодня';
elseif (isset($_GET['happy_day']) && $cal['bithday']['get'][1]) $cal['bithday']['mday']=intval($cal['bithday']['get'][1]).' '.preg_replace($eng, $rus[2], $name);
$cal['bithday']['text']='<br /><div class="cal_bithday_1">'.$cal['bithday']['mday'].' с Днём Рождения поздравляем:</div><br />';
if (isset($_GET['happy_day']) && gdate_check('m-d',$_GET['happy_day'])==1) {$cal['bithday']['day']='%-'.$_GET['happy_day'];}
elseif (isset($_GET['happy_day']) && gdate_check('m',$_GET['happy_day'])==1) {$cal['bithday']['text']='<br /><div class="cal_bithday_1">День Рождение в '.preg_replace($eng, $rus[3], $name).' отметит:</div><br />';$cal['bithday']['day']='%-'.$_GET['happy_day'].'-%';}
else $cal['bithday']['day']='%-'.$cal['bithday']['month'].'-'.$cal['bithday']['today'];
$sql = $db->sql_query("SELECT user_name, user_birthday FROM ".$prefix."_users WHERE `user_birthday` LIKE '".$cal['bithday']['day']."'");
while (list($user_name, $birthday) = $db->sql_fetchrow($sql)) {
if (mb_strlen($user_name, $cal['charset'])>intval($cal['bithday']['cut'])) $cal['bithday']['name']=mb_substr($user_name,0,intval($cal['bithday']['cut']), $cal['charset']).'...';
else $cal['bithday']['name']=$user_name;
$cal['bithday']['date']=explode("-", $birthday, 1);
$cal['bithday']['old']=$year2-$cal['bithday']['date'][0];
if ($cal['bithday']['old']>=$cal['bithday']['min_happy']) $cal['more']['foot'] .='<div style="margin-bottom:-10px;"><span class="cal_bithday"><a href="index.php?name=account&op=info&uname='.urlencode($user_name).'" title="С Днём Рождения '.$user_name.' !">'.$cal['bithday']['name'].'</a></span><span class="cal_bithday_2">'.$cal['bithday']['old'].' '.cal_sclon($cal['bithday']['old'], 'год|года|лет').'</span></div><div class="clr"></div>';
}
if ($cal['more']['foot']!='') $cal['more']['foot']=$cal['bithday']['text'].$cal['more']['foot'];
}
###

###===Модуль=Блоги
elseif ($cal['default']=='blogs') {
$result = $db->sql_query("SELECT `date` FROM ".$prefix."_blogs_topics WHERE `date` LIKE '$year-$mnumber-%' AND `date` <= now()");
}
###

#=========Свой=Модуль=3

while (list($time) = $db->sql_fetchrow($result)) {
$yymmdd = explode(" ", $time);
$ymd = explode("-", $yymmdd[0]);
if ($cal['period']=='year') $array[$ymd[1]][$ymd[2]]++;
else $array[$ymd[0]][$ymd[1]][$ymd[2]]++;
$count++;
}
$mass=explode(',', $cal['modules']);
$menu ='<ul class="menu" id="menu" title="Выбрать модуль"><li><a href="#" id="show_mmd" class="menulink info">'.$cal['lang'][$cal['default']][1].'<span></span></a>';
if (count($mass)>1) {
$menu .='<ul id="cliplayer">';
foreach ($mass as $element) {unset($img);$img=img_find("all/".$element);if (!file_exists($img)) $img=img_find("misc/navi");if($element!=$cal['default']) $menu .='<li><a class="menulink-hover" href="#" onclick="javascript:navigate('.$month.','.$year2.',\''.$element.'\');return false;">'.$cal['lang'][$element][1].'<span style="background-image:url('.$img.');"></span></a></li>';}
$menu .='</ul>';
}
$menu .='</li></ul>';

$content .='<span class="hdmy hdmyw">';
if ($cal['max_year']==0) {
if ($year2<date('Y') || $year2==date('Y') && $month<=date('n')) $content .='<a href="#" class="dmyl arrleft" onclick="javascript:navigate('.$prev.','.$prevy.',\''.$cal['default'].$cal['add']['url'].'\');return false;" title="Показать предыдущий месяц">&nbsp<span></span></a>';
$content .='<a href="#" class="calmdy" onclick="javascript:navigate(\'\',\'\',\''.$cal['default'].$cal['add']['url'].'\');return false;" title="Показать сегодняшний месяц">'.preg_replace(array('#{year}#','#{month}#'), array($year2,$nmonth), $cal['tpl']['head']).'</a>';
if ($year2<date('Y') || $year2==date('Y') && $month<date('n')) $content .='<a href="#" class="dmyr arrright" onclick="javascript:navigate('.$next.','.$nexty.',\''.$cal['default'].$cal['add']['url'].'\');return false;" title="Показать следующий месяц">&nbsp<span></span></a>';
} else {
if ($month>1 || $cal['max_year']==2) {
$content .='<a href="#" class="dmyl arrleft" onclick="javascript:navigate('.$prev.','.$prevy.',\''.$cal['default'].$cal['add']['url'].'\');return false;" title="Показать предыдущий месяц">&nbsp<span></span></a>';
$content .='<a href="#" class="calmdy" onclick="javascript:navigate(\'\',\'\',\''.$cal['default'].$cal['add']['url'].'\');return false;" title="Показать сегодняшний месяц">'.preg_replace(array('#{year}#','#{month}#'), array($year2,$nmonth), $cal['tpl']['head']).'</a>';
} else {
$content .='<a href="#" class="calmdy" onclick="javascript:navigate(\'\',\'\',\''.$cal['default'].$cal['add']['url'].'\');return false;" title="Показать сегодняшний месяц">'.preg_replace(array('#{year}#','#{month}#'), array($year2,$nmonth), $cal['tpl']['head']).'</a>';
}
if ($month<12 || $cal['max_year']==2) $content .='<a href="#" class="dmyr arrright" onclick="javascript:navigate('.$next.','.$nexty.',\''.$cal['default'].$cal['add']['url'].'\');return false;" title="Показать следующий месяц">&nbsp<span></span></a>';
}
$content .='</span>';

$content .=$cal['more']['head'];
$content .='<table cellspacing="0" class="mycalendar"><thead><tr><th class="mycalendat-th">Пн</th><th class="mycalendat-th">Вт</th><th class="mycalendat-th">Ср</th><th class="mycalendat-th">Чт</th><th class="mycalendat-th">Пт</th><th class="mycalendat-th">Сб</th><th class="mycalendat-th">Вс</th></tr></thead><tbody>';
$col=1;
$cur=1;
$next=$event=0;
for($i=1;$i<=$weeks;$i++) {
if($next==3) $next=0;
if($col==1) $content.='<tr>';
$content.="\n";
if($i <= ($days+($start-1)) && $i >= $start) {
if ($cur<=9) $num="0$cur";
else $num=$cur;
if ($cal['period']=='year') $event=$array[$mnumber][$num];
else $event=$array[$year][$mnumber][$num];
if ($event>0) $content.='<td class="mycalendat-td date_has_event"><a href="'.$cal['tpl']['url'].preg_replace(array('#{year}#','#{month}#','#{day}#'), array($year,$mnumber,$num), $cal['tpl']['day']).'" title="Всего '.$cal['lang'][$cal['default']][0].': '.$event.'">'.$num.'</a></td>';
elseif($cur==$today['mday'] && $name==$today['month'] && $year2==$today['year']) $content.='<td class="mycalendat-td today" title="Сегодня: '.$nmonth.', '.$num.'">'.$num.'</td>';
else $content.= '<td class="mycalendat-td">'.$num.'</td>';
$cur++;$col++; 
} else {$content.= '<td class="mycalendat-td padding"></td>';$col++;}  
if($col==8) {$content.='</tr>';$col=1;}
}
if ($count>0) $text="<small>".(($cal['charset']=='utf-8')?mb_ucasefirst($cal['lang'][$cal['default']][0]):ucfirst($cal['lang'][$cal['default']][0]))." за месяц: <a href=\"".$cal['tpl']['url'].preg_replace(array('#{year}#','#{month}#','#{day}#'), array($year,$mnumber,$num), $cal['tpl']['month'])."\" title='Показать ".$cal['lang'][$cal['default']][2]." за выбранный месяц'>$count</a></small>";
else $text="<small>За $nmonth ".$cal['lang'][$cal['default']][0]." нет!</small>";
$content .='</tbody>';
$content .='<tfoot><th class="mycalendat-ft" colspan="7">'.$text.'</th></tfoot>';
$content .='<tfoot><th class="mycalendat-th">Пн</th><th class="mycalendat-th">Вт</th><th class="mycalendat-th">Ср</th><th class="mycalendat-th">Чт</th><th class="mycalendat-th">Пт</th><th class="mycalendat-th">Сб</th><th class="mycalendat-th">Вс</th></tfoot>';

if ($cal['max_year']!=1) {
$content .='<tfoot><th class="mycalendat-ft" colspan="7">';
$nexty=$year2+1;$prevy=$year2-1;
$content.="<span class='c_nexty'><a href='#' onclick='javascript:navigate($month,$prevy,\"".$cal['default'].$cal['add']['url']."\");return false;' title='Показать предыдущий год'>&#8592;&nbsp;$prevy</a></span><span class='c_prevy'>";
if (date('Y')>=$nexty || $cal['max_year']==2) $content.="<a href='#' onclick='javascript:navigate($month,$nexty,\"".$cal['default'].$cal['add']['url']."\");return false;' title='Показать следующий год'>$nexty&nbsp;&#8594;</a>";
else $content.="$nexty&nbsp;&#8594;";
$content.="</span>";
$content .='</th></tfoot>';
}

$content .='</table>';
$content .="<div class='clear'></div>";
$content .=$menu;
$content .=$cal['more']['foot'];
if ($conf['rewrite']==1) {unset($in,$out);include("config/config_rewrite.php");$content=preg_replace($in,$out,$content);}
if ($ajax==1) {header('Content-type: text/html; charset='.$cal['charset']);echo $content;}
else return $content;
}
?>