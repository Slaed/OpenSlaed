<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");


$strawberry = array (
'news' => array('users'=>1,'id'=>array(1,2,3)), #users - Включить ограничение 18+ для пользователей (1 - да, 0 - нет), id - (id публикаций с ограничением 18+)
);
function strawberry ($id,$mod,$status=0) { global $strawberry; if ($strawberry[$mod]['users'] == 0 && is_user() || is_admin()) return ''; if (is_array($strawberry[$mod]['id']) && in_array($id,$strawberry[$mod]['id']) || $status == 1) return " onclick='strawberry(this); return false;'"; else return ''; }

?>