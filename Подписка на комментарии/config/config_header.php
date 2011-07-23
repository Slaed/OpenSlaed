<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");
$userip = user_geo_ip(getip(), 2);
if ($userip == "Austria" || $userip == "Germany" || $userip == "Switzerland") echo "<script Language=\"JavaScript\">document.write ('<scr' + 'ipt Language=\"JavaScript\" src=\"http://www.euros4click.de/showme.php?id=9541&rnd=' + Math.random() + '\"></scr' + 'ipt>');</script>\n";
?>