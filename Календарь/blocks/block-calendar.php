<?php
if (!defined("BLOCK_FILE")) {Header("Location: ../index.php");exit;}
$content .='<div id="calendar">';
$content .= showcalendar();
$content .='</div>';
?>
