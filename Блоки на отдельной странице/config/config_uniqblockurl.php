<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

function uniq_block_url ($a) {$b = explode(' ', $a);if ($b[0] != '[regexp]') $a = preg_quote($a); else $a = $b[1]; return $a;}
function unique_blocku ($uniq) {
$flag_where = 0;
$uniq_url = preg_split("/\r?\n/", $uniq, -1, PREG_SPLIT_NO_EMPTY);
if (is_array($uniq_url)) {
$uniq_url = array_map('uniq_block_url',$uniq_url);
$uniq_url = '^('.implode('|',$uniq_url).')$';
if (preg_match("/$uniq_url/si", ltrim(getenv("REQUEST_URI"),'/'))) $flag_where = 1;
}
return $flag_where;
}

?>