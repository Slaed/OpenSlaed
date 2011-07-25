<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

$subscribe['type']=1;
$subscribe['useronly']=1;
$subscribe['num']=10;
$subscribe['mods']['news']=array(
'name'=>'Новости',
'status'=>1,
'send'=>1,
'sql'=>'SELECT `title`,`sid` FROM {prefix}_stories WHERE `sid` IN ({id})',
'url'=>'index.php?name={mod}&op=view&id={id}',
'title'=>'Добавлен новый комменарий к новости!',
'text'=>'Здравствуйте, сообщаем вам о новом комментарии к новости - {title}

<b>Ссылка на комментарий:</b> {url}
<b>Автор комменария:</b> {author}
<b>Текст комментария:</b> {text}

Новые уведомления о комментариях к данной новости НЕ будут отправлены вам пока вы не просмотрите уже существующие комментарии по ссылке выше!

Это письмо вам было отправлено как подписчику на комментарии к новости {title}.
<b><font color="orange">Вы в любой момент можете отказаться от рассылки, для этого перейдите по следующей ссылке:</font></b>
{unsubscribe}'
);

?>