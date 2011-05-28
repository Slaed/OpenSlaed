<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

$alarm['text'] = 'В модуле: <strong>{mod}</strong> был оставлен новый комментарий!
<strong>Текст комментария:</strong>
{text}
<strong>Автор:</strong> {author}
<strong>IP автора:</strong> {ip}
<a href=\'{url}\'>[ Ссылка на публикацию ]</a>';
$alarm['title'] = 'Добавлен новый комментарий!';
$alarm['module']['news'] = 1;
$alarm['module']['files'] = 1;
$alarm['module']['voting'] = 1;

?>