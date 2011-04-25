<?php
if (!defined("ADMIN_FILE") || !is_admin_god()) die("Illegal File Access");
function get_effects($c,$b) {foreach (explode(',',$c) as $a) $out[]="<option value='$a'".(($a==$b)?'selected=selected':'').">".ucfirst($a)."</option>";return $out;}

function popup_admin () {
global $admin_file;
$msqa['effects']='blind,bounce,clip,drop,explode,fade,fold,highlight,puff,pulsate,scale,shake,size,slide,transfer';
include('config/config_popup.php');
$permtest = end_chmod("config/config_ratings.php", 666);
head();
panel();
title(_POPUP_MESS_1);
open();
if ($permtest) warning($permtest, "", "", 1);

$content .= "
<h2>"._POPUP_MESS_18."</h2>"
."<div class='left'>"._POPUP_MESS_2."</div><div class='center'>".radio_form($msq['status'],"status")."</div>"
."<div class='left'>"._POPUP_MESS_3."</div><div class='center'>".radio_form($msq['loop'],"loop")."</div>"
."<div class='left'>"._POPUP_MESS_4."</div><div class='center'><input type='text' name='time' value='".intval($msq['time']/60)."' maxlength='25' size='45' class='admin'></div>"
."<h2>"._POPUP_MESS_5."</h2>"
."<div class='left'>"._POPUP_MESS_6."</div><div class='center'>".radio_form($msq['modal'],"modal")."</div>"
."<div class='left'>"._POPUP_MESS_7."</div><div class='center'>".radio_form($msq['draggable'], "draggable")."</div>"
."<div class='left'>"._POPUP_MESS_8."</div><div class='center'>".radio_form($msq['resizable'], "resizable")."</div>"
."<div class='left'>"._POPUP_MESS_9."</div><div class='center'><input type='text' name='timeout' value='".intval($msq['timeout']/1000)."' maxlength='25' size='45' class='admin'></div>"
."<div class='left'>"._POPUP_MESS_10."</div><div class='center'><input type='text' name='position' value='".$msq['position']."' maxlength='25' size='45' class='admin'><br /><small><b>"._POPUP_MESS_13."</b></small></div>"
."<div class='left'>"._POPUP_MESS_11."</div><div class='center'><select name='show' class='admin'>".implode('',get_effects($msqa['effects'],$msq['show']))."</select></div>"
."<div class='left'>"._POPUP_MESS_12."</div><div class='center'><select name='hide' class='admin'>".implode('',get_effects($msqa['effects'],$msq['hide']))."</select></div>"
."<h2>"._POPUP_MESS_14."</h2>"
."<small style='color:green'><b>"._POPUP_MESS_15."<br /></b></small>"
."<div><textarea cols='125' rows='20' name='all' wrap='off'>".stripslashes($msq['text']['all'])."</textarea></div>"
."<h2>"._POPUP_MESS_19."</h2>"
."<small><b style='color:green'>"._POPUP_MESS_15."<br /></b></small>"
."<div><textarea cols='125' rows='20' name='guest' wrap='off'>".stripslashes($msq['text']['guest'])."</textarea></div>"
."<h2>"._POPUP_MESS_20."</h2>"
."<small><b style='color:green'>"._POPUP_MESS_15."<br /></b></small>"
."<small><b style='color:red'>"._POPUP_MESS_16."<br /></b></small>"
."<div><textarea cols='125' rows='20' name='user' wrap='off'>".stripslashes($msq['text']['user'])."</textarea></div>"
."<div class='left'>"._POPUP_MESS_17."</div><div class='center'>".radio_form(0,"old")."<input type='hidden' name='pold' value='".$msq['old']."'></div>
";
echo "<form action='".$admin_file.".php' method='post'>";
echo $content;
echo "<div class='button'><input type='hidden' name='op' value='popup_admin_save'><input type='submit' value='"._SAVECHANGES."' class='fbutton'></div></form>";
close();
foot();
}

function popup_admin_save() {
global $admin_file;
$content = "\$msq['status'] = '".intval($_POST['status'])."';\n";
$content .= "\$msq['loop'] = '".intval($_POST['loop'])."';\n";
$content .= "\$msq['time'] = '".intval($_POST['time']*60)."';\n";
$content .= "\$msq['modal'] = '".intval($_POST['modal'])."';\n";
$content .= "\$msq['draggable'] = '".intval($_POST['draggable'])."';\n";
$content .= "\$msq['resizable'] = '".intval($_POST['resizable'])."';\n";
$content .= "\$msq['timeout'] = '".intval($_POST['timeout']*1000)."';\n";
$content .= "\$msq['position'] = '".$_POST['position']."';\n";
$content .= "\$msq['show'] = '".$_POST['show']."';\n";
$content .= "\$msq['hide'] = '".$_POST['hide']."';\n";
$content .= "\$msq['text']['all'] = '".$_POST['all']."';\n";
$content .= "\$msq['text']['guest'] = '".$_POST['guest']."';\n";
$content .= "\$msq['text']['user'] = '".$_POST['user']."';\n";
$content .= "\$msq['old'] = '".((intval($_POST['old'])==1)?time():intval($_POST['pold']))."';\n";
save_conf("config/config_popup.php", $content);
Header("Location: ".$admin_file.".php?op=popup_admin");
}

switch ($op) {
case "popup_admin":
popup_admin();
break;

case "popup_admin_save":
popup_admin_save();
break;
}
?>