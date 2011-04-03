<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

echo PHP_EOL.'<link rel="stylesheet" type="text/css" href="images/rating/rating.css?'.filemtime("images/rating/rating.css").'" />';
#Если у Вас уже подключена библиотека JQuery, то строку ниже можно удалить
echo PHP_EOL.'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>';
echo PHP_EOL.'<script type="text/javascript" src="images/rating/rating.js?'.filemtime("images/rating/rating.js").'"></script>';
?>