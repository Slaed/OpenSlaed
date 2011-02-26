<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

function mysql_elog($a,$b,$c) {
global $confs;
$d='config/logs/mysql.txt';
$e=1048576;
if ($confs['mysql_error']=='1') {
$url = htmlspecialchars(trim(getenv("REQUEST_URI")), ENT_QUOTES);
if ((!file_exists($d) || !stristr(file_get_contents($d)," MySQL: #".$b." - ".$a."\nSQL: ".htmlspecialchars(trim($c))."\n"._URL.": ".$url)) && $fhandle = @fopen($d, "a")) {
if (file_exists($d) && filesize($d) > $e) unlink($d);
fwrite($fhandle, _ERROR." MySQL: #".$b." - ".$a."\nSQL: ".htmlspecialchars(trim($c))."\n"._URL.": ".$url."\n"._IP.": ".getip()."\n"._BROWSER.": ".getagent()."\n"._STARTDATE.": ".date("d.m.y - H:i:s")."\n---\n");
fclose($fhandle);
}
}
}

?>