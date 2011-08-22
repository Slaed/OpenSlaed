<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

echo PHP_EOL.'<link rel="stylesheet" href="images/awards/awards.css?'.filemtime("images/awards/awards.css").'" type="text/css">';
# Если у вас уже подключена библиотека JQuery, то строку ниже можно удалить
echo PHP_EOL.'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>';
echo PHP_EOL.'<script type="text/javascript" src="images/awards/awards.js?'.filemtime("images/awards/awards.js").'"></script>';

?>