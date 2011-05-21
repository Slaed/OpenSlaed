<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

$invate['status'] = 1;
$invate['email'] = 1;
$invate['coast'] = 100;
$invate['live'] = 14;
$invate['mindate'] = 60;
$invate['text'] = 'Здравствуйте, Вам был отправлен инвайт {user} для регистрации на сайте: <a href=\"{site_url}\" title=\"Перейти на сайт\">{site_name}</a>!

Ваш инвайт: {invate}
Срок окончания действия инвайта: {expire}

Также, для регистрации на сайте вы можете просто перейти по следующей ссылке:
{invite_url}



С Уважением, Администрация сайта <a href=\"{site_url}\" title=\"Перейти на сайт\">{site_name}</a>!';
$invate['title'] = 'OpenSlaed - инвайт для регистрации на сайте';

?>