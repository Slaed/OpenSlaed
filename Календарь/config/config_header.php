<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

echo PHP_EOL.'<link rel="stylesheet" href="/images/calendar/calendar.css?'.filemtime("images/calendar/calendar.css").'"/>';
#Если библиотека JQuery у вас уже подключена строку ниже можно удалить...
echo PHP_EOL.'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>';
echo PHP_EOL.'<script type="text/javascript" src="/images/calendar/calendar.js?'.filemtime("images/calendar/calendar.js").'"></script>';
?>