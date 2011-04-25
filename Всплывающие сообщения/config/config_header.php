<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

if ($msq['status']==1) {
#Если у Вас уже подключена библиотека JQuery, то строку ниже можно удалить 
echo PHP_EOL.'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>';
echo PHP_EOL.'<link rel="stylesheet" href="images/message/redmond.css?'.filemtime("images/message/redmond.css").'" type="text/css">';
echo PHP_EOL.'<script type="text/javascript" src="images/message/message.js?'.filemtime("images/message/message.js").'"></script>';
}
?>