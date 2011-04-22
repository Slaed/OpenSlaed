<?php
# Copyright © 2005 - 2009 SLAED
# Website: http://www.slaed.net

# Config
$path = 'ajax/cumulus/';
$movie = 'ajax/cumulus/tagcloud.swf';
$divname = 'cumuluscontent';

$options['width'] = '150';
$options['height'] = '150';
$options['tcolor'] = '666666';
$options['bgcolor'] = 'FFFFFF';
$options['speed'] = '100';
$options['trans'] = 'false';
$options['distr'] = 'true';
$options['args'] = '';
$options['mode'] = 'tags';

global $conf, $key_words;
$words = ($key_words) ? $key_words : $conf['keywords'];
$words = explode(",", $words);
if ($words) {
	foreach ($words as $val) {
	$val=trim($val);
		if ($val != '') $kwords[] = "<a style='10' href='index.php?name=search&word=".$val."'>".$val."</a>";
	}
	$tagcloud = preg_replace(array('/\?/s', '/(&amp;|&)/s'), array('%3F', '%26'), implode('', $kwords));
	$flashtag = '<script type="text/javascript" src="'.$path.'swfobject.js"></script>';
	$flashtag .= '<div id="'.$divname.'"><p style="display:none">';
	
	# Alternate content
	if ($options['mode'] != "cats") $flashtag .= urldecode($tagcloud);
	if ($options['mode'] != "tags") $flashtag .= urldecode($cats);
	$flashtag .= '</p></div>';
	$flashtag .= '<script type="text/javascript">';
	$flashtag .= 'var rnumber = Math.floor(Math.random()*9999999);'; # force loading of movie to fix IE weirdness
	$flashtag .= 'var so = new SWFObject("'.$movie.'?r="+rnumber, "tagcloudflash", "'.$options['width'].'", "'.$options['height'].'", "9", "#'.$options['bgcolor'].'");';
	if ($options['trans'] == 'true') $flashtag .= 'so.addParam("wmode", "transparent");';
	$flashtag .= 'so.addParam("allowScriptAccess", "always");';
	$flashtag .= 'so.addVariable("tcolor", "0x'.$options['tcolor'].'");';
	$flashtag .= 'so.addVariable("tspeed", "'.$options['speed'].'");';
	$flashtag .= 'so.addVariable("distr", "'.$options['distr'].'");';
	$flashtag .= 'so.addVariable("mode", "'.$options['mode'].'");';
	
	# Put tags in flashvar
	if ($options['mode'] != 'cats') $flashtag .= 'so.addVariable("tagcloud", "'.urlencode('<tags>') .''.$tagcloud.''. urlencode('</tags>').'");';

	# Put categories in flashvar
	if ($options['mode'] != 'tags' ) $flashtag .= 'so.addVariable("categories", "' . $cats . '");';
	$flashtag .= 'so.write("'.$divname.'");';
	$flashtag .= '</script>';
	
	$content = $flashtag;
}
?>