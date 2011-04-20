<?php
# Copyright © 2005 - 2008 SLAED
# Website: http://www.slaed.net

if (!defined("FUNC_FILE")) die("Illegal File Access");

$out[] = "offline.html";
$in[] = "'(?<!/)index.php\?name=offline'";
$out[] = "rss-\\1-\\3-num-\\5.html";
$in[] = "'(?<!/)rss.php\?name=([a-zA-Z0-9_]*)&(amp;)?cat=([0-9]*)&(amp;)?num=([0-9]*)'";
$out[] = "rss-\\1-\\3.html";
$in[] = "'(?<!/)rss.php\?name=([a-zA-Z0-9_]*)&(amp;)?cat=([0-9]*)'";
$out[] = "rss-\\1-num-\\3.html";
$in[] = "'(?<!/)rss.php\?name=([a-zA-Z0-9_]*)&(amp;)?num=([0-9]*)'";
$out[] = "rss-num-\\1.html";
$in[] = "'(?<!/)rss.php\?num=([0-9]*)'";
$out[] = "rss-\\1-id-\\3.html";
$in[] = "'(?<!/)rss.php\?name=([a-zA-Z0-9_]*)&(amp;)?id=([0-9]*)'";
$out[] = "rss-\\1.html";
$in[] = "'(?<!/)rss.php\?name=([a-zA-Z0-9_]*)'";
$out[] = "rss.html";
$in[] = "'(?<!/)rss.php'";

$massiv = array("account", "contact", "content", "files", "news", "recommend", "rss_info", "search", "top_users", "voting");
foreach ($massiv as $val) {
	$out[] = "".$val."-edithome.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=edithome'";
	$out[] = "".$val."-logout.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=logout'";
	$out[] = "".$val."-newuser.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=newuser'";
	$out[] = "".$val."-passlost.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=passlost'";
	$out[] = "".$val."-info-\\3.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=info&(amp;)?uname=([%&;/:|\s\-\'{}().&_a-zA-Zа-яА-Я0-9+=-]*)'";
	$out[] = "".$val."-avatar-\\3-\\5.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=saveavatar&(amp;)?category=([a-zA-Z0-9_]*)&(amp;)?avatar=([%&;/:|\s\-\'{}().&_a-zA-Zа-яА-Я0-9+=-]*)'";
	
	$out[] = "".$val."-print-\\3.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=printe&(amp;)?id=([0-9]*)'";
	$out[] = "".$val."-view-\\3-\\5-\\7.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=view&(amp;)?id=([0-9]*)&(amp;)?pag=([0-9]*)&(amp;)?num=([0-9]*)'";
	$out[] = "".$val."-view-\\3-\\5.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=view&(amp;)?id=([0-9]*)&(amp;)?num=([0-9]*)'";
	$out[] = "".$val."-view-\\3-word-\\5.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=view&(amp;)?id=([0-9]*)&(amp;)?word=([%&;/:|\s\-\'{}().&_a-zA-Zа-яА-Я0-9+=-]*)'";
	$out[] = "".$val."-view-\\3.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=view&(amp;)?id=([0-9]*)'";
	$out[] = "".$val."-broken-\\3.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=broken&(amp;)?id=([0-9]*)'";
	$out[] = "".$val."-let-\\3-\\5.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=liste&(amp;)?let=([%a-zA-Zа-яА-Я0-9]*)&(amp;)?num=([0-9]*)'";
	$out[] = "".$val."-let-\\3.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=liste&(amp;)?let=([%a-zA-Zа-яА-Я0-9]*)'";
	$out[] = "".$val."-list-\\3.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=liste&(amp;)?num=([0-9]*)'";
	$out[] = "".$val."-list.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=liste'";
	$out[] = "".$val."-add.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?op=add'";
	$out[] = "".$val."-new-\\3.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?new=1&(amp;)?num=([0-9]*)'";
	$out[] = "".$val."-new.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?new=1'";
	$out[] = "".$val."-best-\\3.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?best=1&(amp;)?num=([0-9]*)'";
	$out[] = "".$val."-best.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?best=1'";
	$out[] = "".$val."-hits-\\3.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?hits=1&(amp;)?num=([0-9]*)'";
	$out[] = "".$val."-hits.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?hits=1'";
	$out[] = "".$val."-cat-\\2-word-\\4.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?cat=([0-9]*)&(amp;)?word=([%&;/:|\s\-\'{}().&_a-zA-Zа-яА-Я0-9+=-]*)'";
	$out[] = "".$val."-cat-\\2-\\4.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?cat=([0-9]*)&(amp;)?num=([0-9]*)'";
	$out[] = "".$val."-cat-\\2.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?cat=([0-9]*)'";
	$out[] = "".$val."-\\2-word-\\4-\\6.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?mod=([a-zA-Z0-9_]*)&(amp;)?word=([%&;/:|\s\-\'{}().&_a-zA-Zа-яА-Я0-9+=-]*)&(amp;)?num=([0-9]*)'";
	$out[] = "".$val."-\\2.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?mod=([a-zA-Z0-9_]*)'";
	$out[] = "".$val."-\\2.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?num=([0-9]*)'";
	$out[] = "".$val."-word-\\2.html";
	$in[] = "'(?<!/)index.php\?name=".$val."&(amp;)?word=([%&;/:|\s\-\'{}().&_a-zA-Zа-яА-Я0-9+=-]*)'";
	$out[] = "".$val.".html";
	$in[] = "'(?<!/)index.php\?name=".$val."'";
}
?>