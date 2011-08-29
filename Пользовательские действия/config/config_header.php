<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

if ($conf['name']=='account') {
echo PHP_EOL.'<link rel="stylesheet" href="images/user_actions/user_actions.css?'.filemtime("images/user_actions/user_actions.css").'" type="text/css">';
# Если у вас уже подключена библиотека JQuery, то строку ниже можно удалить
echo PHP_EOL.'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>';
echo PHP_EOL.'<script type="text/javascript" src="images/user_actions/user_actions.js?'.filemtime("images/user_actions/user_actions.js").'"></script>';
}

?>