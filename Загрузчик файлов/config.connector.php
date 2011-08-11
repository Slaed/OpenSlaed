<?php
error_reporting(E_ALL);
define("ADMIN_FILE", true);
include("function/function.php");
include("config/config_elf_set.php");
if (function_exists('date_default_timezone_set')) {date_default_timezone_set('Europe/Moscow');}
if (is_admin_god() && intval($_GET['op'])==4) die($_SERVER['DOCUMENT_ROOT']);
include_once 'elfinder/php/elFinderConnector.class.php';
include_once 'elfinder/php/elFinder.class.php';
include_once 'elfinder/php/elFinderVolumeDriver.class.php';
include_once 'elfinder/php/elFinderVolumeLocalFileSystem.class.php';
include_once 'elfinder/php/elFinderVolumeMySQL.class.php';

function elf_who_save ($cmd,$result,$voumes) {
global $db,$user,$admin,$elf,$prefix;
if (is_admin()) $info=array('type'=>2,'id'=>intval($admin[0]),'name'=>text_filter($admin[1]));
elseif (is_user()) $info=array('type'=>1,'id'=>intval($user[0]),'name'=>text_filter($user[1]));
if ($cmd=='extract' && isset($result['added'][0]['hash']) && count($result['added'])>1) {
foreach ($result['added'] as $num=>$value) $result['added'][$num]['hash']=$voumes[0]->parent($result['added'][$num]['hash']);
} elseif (isset($result['added'][0]['hash'])) $result['added'][0]['hash']=$voumes[0]->parent($result['added'][0]['hash']);
if (isset($result['removedDetails'][0]['hash'])) $result['removedDetails'][0]['hash']=$voumes[0]->parent($result['removedDetails'][0]['hash']);
if (isset($result['src']['hash'])) $result['src']['hash']=$voumes[0]->parent($result['src']['hash']);
if (isset($result['changed'][0]['hash'])) $result['changed'][0]['hash']=$voumes[0]->parent($result['changed'][0]['hash']);
if ($elf['log']=='1') {
$db->sql_query("INSERT INTO ".$prefix."_elfinder (id,type,uid,ip,date,cmd,info) VALUES (NULL,'".$info['type']."','".$info['id']."','".getip()."',now(),'$cmd','".addcslashes(serialize($result),'\'\\')."')");
} elseif ($elf['log']=='2' && is_dir('config/logs') && is_writeable('config/logs')) {
$log = $cmd.'###'.serialize($result);
$fp = fopen('config/logs/elfinder.txt', 'a');
if ($fp) {
fwrite($fp, $log."\n");
fclose($fp);
}
}
}

#return compressNumber(abs(crc32($path))).'.png';
function compressNumber($n) {
$codeset = "0123456789abcdefghijklmnopqrstuvwxyz";
$base = strlen($codeset);
$converted = "";
while ($n > 0) {
$converted = substr($codeset, ($n % $base), 1) . $converted;
$n = floor($n/$base);
}
return $converted;
}

function write($cmd, $voumes, $result) {
if ($cmd=='upload') {
$pinfo = pathinfo($result['added'][0]['name']);
if (preg_match("#[^0-9a-z-]#si",$pinfo['filename'])) {
$new_name = elf_translit($pinfo['filename']).(($pinfo['extension'])?'.'.$pinfo['extension']:'');
$path = $voumes[0]->parent($result['added'][0]['hash']);
if (is_file($path.DIRECTORY_SEPARATOR.$new_name)) $new_name=$voumes[0]->uniqueName($path,$new_name,'-',false);
if (($file = $voumes[0]->rename($result['added'][0]['hash'],$new_name)) != false) $result['added'][0] = $file;
}
} 
elf_who_save($cmd,$result,$voumes);
return $result;
}

function access($attr, $path, $data, $volume) {
return strpos(basename($path), '.') === 0   // if file/folder begins with '.' (dot)
? !($attr == 'read' || $attr == 'write')  // set read+write to false, other (locked+hidden) set to true
: ($attr == 'read' || $attr == 'write');  // else set read+write to true, locked+hidden to false
}

# Функция для запрета на создание папки/файлов с точкой в начале
function validName($name) {return strpos($name, '.') !== 0;}
###

# Функция для создания файлов по умолчанию в пользовательских папках
function elf_create ($file,$text='',$chmod='',$chown='') {
if(is_dir(dirname($file)) && is_writeable(dirname($file))){
$o=fopen($file,'w+');
if($o){
if ($text!='') fwrite($o,$text);
fclose($o);
}
if ($chmod!='') @chmod($file,intval($chmod,8));
if ($chown!='') @chown($file,$chown);
}
}
###

#Функция преобразование в транслит
function elf_translit($string) {
$letters = array("а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => 
          "e", "ё" => "e",  "ж" => "zh", "з" => "z", "и" => "i", "й" => "j", "к" => "k", 
          "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => 
          "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "ts", "ч" => "ch", 
          "ш" => "sh", "щ" => "shch", "ы" => "y", "э" => "e", "ю" => 
          "yu", "я" => "ya");
$string = preg_replace("/[_ .,?!\[\](){}]+/", "-", $string);
$string = mb_strtolower($string,'utf-8');
$string = mb_strtolower($string,'utf-8');
$string = preg_replace("#(ь|ъ)([аеёиоуыэюя])#u", "j\2", $string);
$string = preg_replace("#(ь|ъ)#u", "", $string);
$string = strtr($string, $letters);
$string = preg_replace("/j{2,}/", "j", $string);
$string = preg_replace("/[^0-9a-z-]+/", "", $string);
$string = preg_replace("/-+/", "-", $string);
$string = trim($string,'-');
return !$string?'untitled':$string;
}
###

# Функция для подсчета размера папки
function elf_dirsize($a) {
if (!is_dir($a)) return -1;
$size = 0;
if ($b = opendir($a)) {
while (($c = readdir($b)) !== false) {
if (is_link($a . '/' . $c) || $c == '.' || $c == '..') continue;
if (is_file($a . '/' . $c)) $size += filesize($a . '/' . $c);
else if (is_dir($a . '/' . $c)) {
$d = elf_dirsize($a . '/' . $c); 
if ($d >= 0) $size += $d; 
else return -1;
}
}
closedir($b);
}
return $size;
}
###

# Функция для преобразования численного значения размера папки в удобочитаемый вид
function elf_format_size($rawSize) {
if ($rawSize / 1048576 > 1) return round($rawSize/1048576, 1) . ' Мб'; 
else if ($rawSize / 1024 > 1) return round($rawSize/1024, 1) . ' Кб'; 
else return round($rawSize, 1) . ' байт';
}
###

# Папка для миниатюр, относительно корневой директории
$elf['tmb']='uploads/tmb';
# Настройки для пользователей
# Максимальный размер папки каждого пользователя (в мегабайтах), 0 - не ограничено
$msize='10';
# Папка в которой будут находится папки каждого пользователя (не забудьте установить на данную папку права 0777)
$dir='uploads/users/';
# Создаём файл ReadMe.txt каждому пользователю при первом его входе в свою папку
$save[] = array (
# Имя файла
'filename' => 'ReadMe.txt',
# Текст в файле
'text' => "Тут что-то надо бы не забыть написать...\r\nСерверное время: ".date('d-m-Y h:i:s',time()),
# При создании попытаться установить следующие права
'chmod' => '0644',
# При создании попытаться установить следующиго владельца
'chown' => '',
# Перезаписывать файл при каждом обращении
'rewrite' => true,
'attributes' => array(
# Атрибуты файла 
'write' => false,               # Разрешить перезапись файла
'read' => true,                 # Разрешить чтение файла
'hidden' => false,              # Сделать файл невидимым. Не запрещает запись/удаление.
'locked' => true                # Запрет на удаление и переименование файла
)
);
# Создаём файл .htaccess каждому пользователю при первом его входе в свою папку
$save[] = array (
'filename' => '.htaccess',
'text' => 'AddHandler  None .php
AddHandler  None .php3
AddHandler  None .php4
AddHandler  None .php5
AddHandler  None .php6
AddHandler  None .shtml
AddHandler  None .pl
AddHandler  None .cgi
AddHandler  None .phtml',
'chmod' => '0644',
'chown' => '',
'rewrite' => true,
'attributes' => array(
'write' => false,
'read' => true,
'hidden' => false,
'locked' => true
)
);


# Коннектор для суперадмина (приведены все настройки для примера)
$options['god'] = array(
'locale' => 'en_US.UTF-8',
# [null] Пишем лог действий
'bind' => array('mkdir mkfile rename duplicate upload rm paste put extract archive' => 'write'),
# [false] Отправлять отладочную информацию клиенту
'debug' => true,
'roots' => array(
array(
# [LocalFileSystem] Драйвер
'driver' => 'LocalFileSystem',
# Корневая папка (лучше указывать абсолютный путь (то что выводит $_SERVER['DOCUMENT_ROOT']) если не работает то укажите относительный путь (../../)
'path'   => $_SERVER['DOCUMENT_ROOT'],
# [''] Открыть данную папку при первоначальной загрузке вместо корневой папки
'startPath' => $_SERVER['DOCUMENT_ROOT'].'/uploads/',
# [1] На какую глубину вложенности загружать дерево папок
'treeDeep' => 1,
# [0777] Устанавливаемые права по умолчанию на созданные папки для миниатюр
'tmbPathMode' => 0777,
# [DIRECTORY_SEPARATOR] Разделитель директорий
'separator' => DIRECTORY_SEPARATOR,
# ['#ffffff'] Цвет бэкгроунда миниатюр изображений (hex '#rrggbb' или 'transparent')
'tmbBgColor' => '#ffffff',
# [0] Как часто очищать каталог с миниатюрами (0 - никогда, 100 - при каждом запросе)
'tmbCleanProb' => 0,
# [true] if true - join new and old directories content on paste
'copyJoin' => false,
# ['j M Y H:i'] Формат даты
'dateFormat' => 'j M Y H:i',
# ['H:i'] Формат времени
'timeFormat' => 'H:i',
# [true] Разрешить копирование/перемещение файлов из этой корневой директории в другие
'copyFrom' => true,
# [true] Разрешить копирование/перемещение файлов из других корневых директорий в эту
'copyTo' => true,
# [array()] Список отключенных команд: array('rename','edit','upload','mkfile','mkdir','rm','cut','copy','duplicate','paste','extract','archive')
'disabled' => array(),
# [''] Адрес сайта, отдаваемый клиенту (если пусто путь до файла будет закодирован)
#'URL' => rtrim($conf['homeurl'],'/'),
'URL' => '/',
# [''] Алиас (название) для корневой директории
'alias' => 'Корневая директория',
# [0755] Права по умолчанию на создаваемые папки
'dirMode' => 0777,
# [0644] Права по умолчанию на создаваемые файлы
'fileMode' => 0666,
# Учитывать регистр
# 'caseSensitive' => false,
# [null] Функция для управления доступом к файлам в зависимости от их имени, расширения и т.д
'accessControl' => 'access',
# [null] some data required by access control
'accessControlData' => array('uid' => 1),
# ['/^[^\.]/'] Регулярное выражение или название функции для валидации нового имени файла/папки Ex.: '/^[^\.]/'
'acceptedName' => 'validName',
# [array('all')] Список разрешенных типов файлов для загрузки. Можно задать точный mimetype (image/jpeg) или группу типов (image)
'uploadAllow' => array('all'),
# [array()] Запрещенные к загрузки mimetype или группы файлов: array('image/png','image/jpeg')
'uploadDeny'  => array('all'),
# ['deny,allow'] Порядок применения ограничивающих загрузку правил:
# ['allow,deny'] - только то, что разрешено, кроме того, что запрещено (AND)
# ['deny,allow'] - то, что не запрещено или разрешено (OR)
'uploadOrder' => 'deny,allow',
# [true] Перезаписывать файлы при загрузке
'uploadOverwrite' => false,
# [true] Перезаписывать файлы и папки при копировании из папки в папку
'copyOverwrite' => true,
# [0] Максимальный размер каждого файла для загрузки: 0 - неограниченный, '10b' - 10 байт, '10k' - 10 килобайт, '10m' - 10 мегабайт, '10g' - 10 гигабайт
'uploadMaxSize' => '10m',
# [auto] Метод определения mime типов файла (auto, internal, finfo, mime_content_type)
'mimeDetect' => 'internal',
# [''] Путь к файлу с mime типами в случае, если в mimeDetect установлен internal (относительно данной директории)
'mimefile' => '',
# [true] Обрезать изображение, подгоняя его под выбранный размер миниатюр (делая его квадратным), иначе уменьшать изображение сохраняя его пропорции
'tmbCrop' => false,
# [auto] Библиотека для создания миниатюр (imagick, gd, auto)
'imgLib' => 'gd',
# ['.tmb'] Папка в которой ищем миниатюры (.tmb)
'tmbURL'    => $elf['tmb'],
# [''] Папка в которую сохраняем миниатюры: [''] - для отключения миниатюр (.tmb)
'tmbPath' => $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$elf['tmb'],
# [false] Исправление проблем с кодировкой (Рекоммендуется для macos)
'utf8fix' => false,
# [48] Размер миниатюр
'tmbSize' => 500,                                                            
# [array()] Список типов архивов, разрешенных для создания. Если не задан, будут разрешены все доступные типы
'archiveMimes' => array(),
# [array()] Информация об архиваторах. Если не задана, коннектор попытается найти и использовать все доступные архиваторы
'archivers'    => array(),
# [array('read' => true,'write' => true)] Права доступа к файлам/директориям по умолчанию
'defaults' => array('read' => true,'write' => true),
# [array()] Настройка индивидуальных атрибутов (запись, чтение, удаление, переименование, скрытие) для файлов и папок
'attributes' => array(
# Пример: Запрещаем доступ к корневой папке admin 
	array(
		'pattern' => '#^\/admin$#',     # Регулярное выражение для проверки на совпадение файлов и папок
		'write' => false,               # Разрешить запись файлов
		'read' => false,                # Разрешить чтение файлов
		'hidden' => false,              # Сделать файл/папку невидимым. Не запрещает запись/удаление.
		'locked' => true                # Запрет на удаление и переименование файла
	)
),
),

# Пример: Добавляем ещё одну корневую директорию
array(
'driver' => 'LocalFileSystem',
'path'   => 'uploads/',
'alias'  => 'Загрузки',
'copyJoin' => false,
'attributes' => array(
# Пример: Делаем скрытыми все файлы и папки содержащих в начале два нижних подчеркивания (__)
 array(
 	'pattern' => '#\/__.*#',
 	'hidden'  => true
 ),
# Пример: Запрещаем удаление/переименование всех файлов и папок начинающихся с точки (.)
 array(
 	'pattern' => '#\/\..*$#',
 	'read'    => true,
 	'write'   => false,
 	'locked'  => true,
 	'hidden'  => false
 ),
# Пример: Запрещаем удаление/переименование/чтение/запись всех папок и файлов с названием admin 
 array(
	'pattern' => '#\/admin$#',
	'write' => false,
	'read' => false,
	'hidden' => false,
	'locked' => true
)
),
),

)
);


# Коннектор для пользователей
$name=preg_replace("#[^a-zA-ZА-Яа-я0-9]#uis","",$user[1]);
if (!$name) $name='Мои файлы';

$options['users'] = array(
'locale' => 'en_US.UTF-8',
# [null] Пишем лог действий: [null] - отключить запись логов
'bind' => array('mkdir mkfile rename duplicate upload rm paste put extract archive' => 'write'),
'roots' => array(
array(
# Драйвер
'driver' => 'LocalFileSystem',
# Корневая папка (лучше указывать абсолютный путь (то что выводит $_SERVER['DOCUMENT_ROOT']) если не работает то укажите относительный путь (../../)
'path'   => $_SERVER['DOCUMENT_ROOT'].'/'.$dir.intval($user[0]).'/',
# [0777] Устанавливаемые права по умолчанию на созданные папки для миниатюр
'tmbPathMode' => 0777,
# [true] if true - join new and old directories content on paste
'copyJoin' => false,
# [true] Разрешить копирование/перемещение файлов из этой корневой директории в другие
'copyFrom' => false,
# [true] Разрешить копирование/перемещение файлов из других корневых директорий в эту
'copyTo' => false,
# [array()] Список отключенных команд: array('rename','edit','upload','mkfile','mkdir','rm','cut','copy','duplicate','paste','extract','archive','resize')
'disabled' => array('edit','mkfile','mkdir','rename','cut','copy','duplicate','paste','extract','archive','resize'),
# [''] Путь дописываемый в начало при вставке файла в textarea (если пусто путь до файла отдаваться не будет)
'URL' => $dir.intval($user[0]),
#'URL' => rtrim($conf['homeurl'],'/'),
# [''] Алиас (название) для корневой директории
'alias' => $name,
# [0755] Права по умолчанию на создаваемые папки
'dirMode' => 0777,
# [0644] Права по умолчанию на создаваемые файлы
'fileMode' => 0666,
# Учитывать регистр
#'caseSensitive' => false,
# [null] Функция для управления доступом к файлам в зависимости от их имени, расширения и т.д
'accessControl' => 'access',
# ['/^[^\.]/'] Регулярное выражение или название функции для валидации нового имени файла/папки Ex.: '/^[^\.]/'
'acceptedName' => 'validName',
# [array('all')] Список разрешенных типов файлов для загрузки. Можно задать точный mimetype (image/jpeg) или группу типов (application,image,audio,video)
'uploadAllow' => array('application/zip','application/x-rar','text/plain','image/jpeg','image/gif','image/png'),
# [array()] Запрещенные к загрузки mimetype или группы файлов: array('image/png','image/jpeg')
'uploadDeny'  => array('all'),
# ['deny,allow'] Порядок применения ограничивающих загрузку правил:
# ['allow,deny'] - только то, что разрешено, кроме того, что запрещено (AND)
# ['deny,allow'] - то, что не запрещено или разрешено (OR)
'uploadOrder' => 'deny,allow',
# [true] Перезаписывать файлы при загрузке
'uploadOverwrite' => false,
# [true] Перезаписывать файлы и папки при копировании из папки в папку
'copyOverwrite' => false,
# [0] Максимальный размер каждого файла для загрузки: 0 - неограниченный, '10b' - 10 байт, '10k' - 10 килобайт, '10m' - 10 мегабайт, '10g' - 10 гигабайт
'uploadMaxSize' => '2m',
# [auto] Метод определения mime типов файла (auto, internal, finfo, mime_content_type)
'mimeDetect' => 'internal',
# [''] Путь к файлу с mime типами в случае, если в mimeDetect установлен internal (относительно данной директории)
'mimefile' => '',
# [true] Обрезать изображение, подгоняя его под выбранный размер миниатюр (делая его квадратным), иначе уменьшать изображение сохраняя его пропорции
'tmbCrop' => false,
# [auto] Библиотека для создания миниатюр (imagick, gd, auto)
'imgLib' => 'gd',
# ['.tmb'] Папка в которой ищем миниатюры ($dir.intval($user[0]).'/.tmb')
'tmbURL'    => $elf['tmb'],
# [''] Папка в которую сохраняем миниатюры: [''] - для отключения миниатюр (.tmb)
'tmbPath' => $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$elf['tmb'],
# [false] Исправление проблем с кодировкой (Рекоммендуется для macos)
'utf8fix' => false,
# [48] Размер миниатюр
'tmbSize' => 500,                                                            
# [array()] Список типов архивов, разрешенных для создания. Если не задан, будут разрешены все доступные типы
'archiveMimes' => array(),
# [array()] Информация об архиваторах. Если не задана, коннектор попытается найти и использовать все доступные архиваторы
'archivers'    => array(),
# [array('read' => true,'write' => true)] Права доступа к файлам/директориям по умолчанию
'defaults' => array('read' => true,'write' => true),
)
)
);

$set=null;
if (is_admin() && intval($admin[0])>0 && is_array($options['admin'][intval($admin[0])])) $set=$options['admin'][intval($admin[0])];
elseif (is_admin_god() && is_array($options['god'])) $set=$options['god'];
elseif (is_admin_modul('elfinder') && is_array($options['moder'])) $set=$options['moder'];
elseif (is_admin() && is_array($options['admins'])) $set=$options['admins'];
elseif ($elf['status']=='1' && is_user() && intval($user[0])>0 && is_array($options['users'])) {
$uid=intval($user[0]);
if (!is_dir($dir.'/'.$uid) && is_writeable($dir)) {mkdir($dir.'/'.$uid);chmod($dir.'/'.$uid,0777);}
if (is_array($save)) {
foreach ($save as $a) {
if (is_array($a) && (!file_exists($dir.'/'.$uid.'/'.$a['filename']) || $a['rewrite']==true)) elf_create($dir.'/'.$uid.'/'.$a['filename'],$a['text'],$a['chmod'],$a['chown']);
if (is_array($a['attributes']) && count($a['attributes'])>0) {
$a['attributes']['pattern']="#^".preg_quote(DIRECTORY_SEPARATOR.$a['filename'])."$#";
$options['users']['roots'][0]['attributes'][]=$a['attributes'];
}
}
}
if (is_dir($dir.'/'.$uid) && is_writeable($dir)) {
$set=$options['users'];
$csize=elf_dirsize($dir.'/'.$uid);
if (intval($msize)>0 && $csize>=$msize*1024*1024) {
elf_create($dir.'/'.$uid.'/ReadMe.txt',"Ваша папка ( ".elf_format_size($csize)." ) превысила максимально допустимый размер ( $msize Мб)!\r\nУдалите ненужные или устаревшие файлы, после этого Вы сможете продолжить загружать файлы в свою папку!",$a['chmod'],$a['chown']);
$set['roots'][0]['attributes'][]=array('pattern' => '#^'.preg_quote(DIRECTORY_SEPARATOR).'(.*)$#','write' => false,'read' => true,'hidden' => false,'locked' => false);
}
}
unset($a,$uid);
}

if ($set) {
#header('Access-Control-Allow-Origin: *');
$connector = new elFinderConnector(new elFinder($set));
$connector->run();
} else die('Access Denied');

?>