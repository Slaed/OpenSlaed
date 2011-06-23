<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

#Здесь указываем имя администратора (модератора) и доступ к каким файлам мы ему разрешаем, пример см. ниже
/*
В данном примере мы разрешаем админам модераторам с именами: name_user_1 и name_user_2
выполнять все функции из файлов системных модулей (те файлы которые находятся в папке admin/modules): comments.php и modules.php
т.е данные пользователи смогут управлять комментариями и модулями
*/
$admods['access']=array(
'name_user_1'=>array('comments.php','modules.php'),
'name_user_2'=>array('comments.php','modules.php')
);
?>