<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

echo PHP_EOL.'<link rel="stylesheet" type="text/css" href="images/thanks/thanks.css?'.filemtime("images/thanks/thanks.css").'" />';
#Если библиотека JQuery у вас уже подключена строку ниже можно удалить...
echo PHP_EOL.'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>';
echo PHP_EOL.'<script type="text/javascript" src="images/thanks/thanks.js?'.filemtime("images/thanks/thanks.js").'"></script>';
?>