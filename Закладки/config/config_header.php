<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

global $fav;
echo PHP_EOL.'<link rel="stylesheet" type="text/css" href="images/favorite/favorite.css?'.filemtime("images/favorite/favorite.css").'" />';
echo PHP_EOL.'<script type="text/javascript" src="images/favorite/favorite.js?'.filemtime("images/favorite/favorite.js").'"></script>';
if (is_user() && $fav['panel']==1) echo PHP_EOL.'<script type="text/javascript">$(function (){ EnvatoWidget.init(); });</script>';
?>