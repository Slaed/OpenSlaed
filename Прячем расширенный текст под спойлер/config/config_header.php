<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

echo PHP_EOL."<style type='text/css'>.test_to_spoiler{margin:5px 0;background:#DEF1FF;border:1px solid #79A4E0;border-left:3px solid #79A4E0;} .test_to_spoiler .to_spoiler{padding:5px;width:99%;font-size:11px;font-weight:bold;} .test_to_spoiler .to_spoiler a:link,.test_to_spoiler .to_spoiler a:visited{color:#2D73A6;} .test_to_spoiler .to_spoiler a:focus,.test_to_spoiler .to_spoiler a:hover{color:#3890CF;} .test_to_spoiler .spoiler_text{width:99%;padding-left:5px;padding-top:5px;margin-top:5px;border-top:1px solid #2D73A6;display:none;}</style>";
#Если у Вас уже подключена библиотека JQuery, то строку ниже можно удалить 
echo PHP_EOL.'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>';
echo PHP_EOL."<script language='javascript' type='text/javascript'> $(function (){ $('.test_to_spoiler .to_spoiler').live('click',function(){ $(this).next('div.spoiler_text').slideToggle(200);return false}); }); </script>";
?>