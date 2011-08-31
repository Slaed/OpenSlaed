<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

echo PHP_EOL.'<link rel="stylesheet" href="images/strawberry/strawberry.css?'.filemtime("images/strawberry/strawberry.css").'" type="text/css">';
# Если у вас уже подключена библиотека JQuery, то строку ниже можно удалить 
echo PHP_EOL.'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>';
echo PHP_EOL.'<script type="text/javascript" src="images/strawberry/strawberry.js?'.filemtime("images/strawberry/strawberry.js").'"></script>';

?>