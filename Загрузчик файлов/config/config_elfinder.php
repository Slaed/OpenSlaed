<?php
if (!defined('FUNC_FILE')) die('Illegal File Access');

function elfinder_admin() {
global $elfinder;
$out = "
<script src='/elfinder/jquery/jquery-1.6.1.min.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/jquery/jquery-ui-1.8.13.custom.min.js' type='text/javascript' charset='utf-8'></script>
<link rel='stylesheet' href='/elfinder/jquery/ui-themes/smoothness/jquery-ui-1.8.13.custom.css' type='text/css' media='screen' title='no title' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/common.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/dialog.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/toolbar.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/navbar.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/statusbar.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/cwd.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/quicklook.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/commands.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/contexmenu.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/theme.css' type='text/css' media='screen' charset='utf-8'>
<script src='/elfinder/js/elFinder.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/elFinder.version.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/jquery.elfinder.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/elFinder.resources.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/elFinder.options.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/elFinder.history.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/elFinder.command.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/overlay.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/workzone.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/navbar.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/dialog.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/tree.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/cwd.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/toolbar.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/button.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/uploadButton.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/viewbutton.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/searchbutton.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/panel.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/contexmenu.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/path.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/stat.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/places.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/back.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/forward.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/reload.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/up.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/home.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/copy.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/cut.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/paste.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/open.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/rm.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/info.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/duplicate.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/rename.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/help.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/getfile.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/mkdir.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/mkfile.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/upload.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/download.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/edit.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/quicklook.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/quicklook.plugins.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/extract.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/archive.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/search.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/view.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/resize.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/i18n/elfinder.ar.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/i18n/elfinder.en.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/i18n/elfinder.ru.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/proxy/elFinderSupportVer1.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/jquery.dialogelfinder.js' type='text/javascript' charset='utf-8'></script>
";
$out .="
<script type='text/javascript' charset='utf-8'>
$().ready(function() {
var elf = $('#elfinder').elfinder({
// Язык
lang: 'ru',
// Путь к коннектору
url : '/config.connector.php',
uiOptions : {
// Отображаемые кнопки в тулбаре: back,forward,reload,home,up,mkdir,mkfile,upload,open,download,getfile,info,quicklook,copy,paste,cut,rm,duplicate,rename,edit,extract,archive,search,view,reload,help
toolbar : [
['mkdir', 'mkfile', 'upload'],
['open','quicklook','info'],
['copy','paste', 'cut' ,'rm'],
['duplicate', 'rename', 'edit'],
['extract', 'archive'],
['search'],
['view','reload'],
['help']
],
// Опции дерева директорий
tree : {
// Разворачивать первую корневую директорию при инициализации
openRootOnLoad : true,
// Автоматически загружать дочерние директории
syncTree : true
},
// Настройка ширины окна дерева директорий (при изменении размера)
navbar : {
// Минимальная ширина
minWidth : 150,
// Максимальная ширина
maxWidth : 500
}
}
}).elfinder('instance');			
});
</script>
<div id='elfinder'></div>
";
return $out;
}

function elfinder_user($id) {
global $out;
include("config/config_elf_set.php");
if ((!is_admin() && !is_user()) || (!is_admin() && is_user() && $elf['status']!='1')) return array('head'=>'','script'=>'','button'=>'');
if (!isset($out['head'])) {
$out['head'] = "
<script src='/elfinder/jquery/jquery-1.6.1.min.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/jquery/jquery-ui-1.8.13.custom.min.js' type='text/javascript' charset='utf-8'></script>
<link rel='stylesheet' href='/elfinder/jquery/ui-themes/smoothness/jquery-ui-1.8.13.custom.css' type='text/css' media='screen' title='no title' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/common.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/dialog.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/toolbar.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/navbar.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/statusbar.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/cwd.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/quicklook.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/commands.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/contexmenu.css' type='text/css' media='screen' charset='utf-8'>
<link rel='stylesheet' href='/elfinder/css/theme.css' type='text/css' media='screen' charset='utf-8'>
<script src='/elfinder/js/elFinder.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/elFinder.version.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/jquery.elfinder.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/elFinder.resources.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/elFinder.options.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/elFinder.history.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/elFinder.command.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/overlay.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/workzone.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/navbar.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/dialog.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/tree.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/cwd.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/toolbar.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/button.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/uploadButton.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/viewbutton.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/searchbutton.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/panel.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/contexmenu.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/path.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/stat.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/ui/places.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/back.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/forward.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/reload.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/up.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/home.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/copy.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/cut.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/paste.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/open.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/rm.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/info.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/duplicate.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/rename.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/help.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/getfile.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/mkdir.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/mkfile.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/upload.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/download.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/edit.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/quicklook.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/quicklook.plugins.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/extract.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/archive.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/search.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/view.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/commands/resize.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/i18n/elfinder.ar.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/i18n/elfinder.en.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/i18n/elfinder.ru.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/proxy/elFinderSupportVer1.js' type='text/javascript' charset='utf-8'></script>
<script src='/elfinder/js/jquery.dialogelfinder.js' type='text/javascript' charset='utf-8'></script>
<style>
.dialogelfinder {text-align:left;}
.dialogelfinder td, .dialogelfinder div {color:#000;}
.ui-state-hover .elfinder-cwd-filename {color:#fff;}
.elfinder-quicklook-preview {text-align:left;}
</style>

<script type='text/javascript' charset='utf-8'>
function check_on_image (files,id) {
$('#find-images').dialog({
resizable:false,
width:450,
height:140,
modal:true,
buttons: {
'Вставить как картинки': function() {
InsertCode('elfinder',files['images'].join('\\r\\n'),'','',id);
$(this).dialog('close');
},
'Вставить как ссылки': function() {
InsertCode('elfinder',files['urls'].join('\\r\\n'),'','',id);
$(this).dialog('close');
}
}
});
}
function elfinder_paste(files,id) {
if (files.length>0) {
var pastes=new Array();
pastes['images']=new Array();
pastes['urls']=new Array();
pastes['i']=0;
for(i=0;i<files.length;i++) {
pastes['urls'][i]='[url='+files[i]['url']+']'+files[i]['name']+'[/url]';
//var mime=files[i]['mime'].split('/');
if (files[i]['mime'].indexOf('image') !== -1) {pastes['images'][i]='[img]'+files[i]['url']+'[/img]';pastes['i']=1;}
else pastes['images'][i]='[url='+files[i]['url']+']'+files[i]['name']+'[/url]';
}
//alert('Урлы:\\r\\n'+pastes['urls'].join('\\r\\n')+'\\r\\n Картинки:\\r\\n'+pastes['images'].join('\\r\\n'));
if (pastes['i']==1) check_on_image(pastes,id);
else InsertCode('elfinder',pastes['urls'].join('\\r\\n'),'','',id);
}

}
</script>
<div id='find-images' title='Обнаружены изображения' style='display:none;'>Среди выбранных файлов обнаружены изображения, как будем их вставлять?</div>

";
} else unset($out['head']);
$out['script']="
<script type='text/javascript' charset='utf-8'>
$().ready(function() {

var fm;
$('#elfinder-dialog-$id').click(function() {
if (!fm) {
fm = $('<div/>').dialogelfinder({
destroyOnClose : false,
url : '/config.connector.php',
lang : 'ru',
width : 900,
title: 'Менеджер файлов',
getFileCallback : function(files, fm) {
//console.log(files);
elfinder_paste(files,$id);
},
commandsOptions : {
getfile : {
// Возвращать только ссылку на файл
onlyURL: false,
// Закрываем менеджер после выбора файлов (close,destroy)
oncomplete : 'close',
// Разрешить выбор нескольких файлов
multiple : true,
// Разрешить выбор папки
folders : false
}
}
}).dialogelfinder('instance');
} else fm.show();

});
});
</script>";
#OnClick=\"InsertCode('img', '', '', '', '".$id."')\"
$out['button']="<div class='editorbutton' OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\"><img src='".img_find("editor/elfinder")."' title='Менеджер файлов' id='elfinder-dialog-$id'></div>";
return $out;
}
?>