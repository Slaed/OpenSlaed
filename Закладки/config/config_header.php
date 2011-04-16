<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

global $fav;
echo PHP_EOL.'<link rel="stylesheet" type="text/css" href="images/favorite/favorite.css?'.filemtime("images/favorite/favorite.css").'" />';
#Если у Вас уже подключена библиотека JQuery, то строку ниже можно удалить
echo PHP_EOL.'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>';
echo PHP_EOL.'<script type="text/javascript" src="images/favorite/favorite.js?'.filemtime("images/favorite/favorite.js").'"></script>';
if (is_user() && $fav['panel']==1) echo PHP_EOL.'<script type="text/javascript">$(function (){ EnvatoWidget.init(); });</script>';
?>