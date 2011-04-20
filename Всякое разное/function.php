<?php
function textarea($id, $name, $var, $mod, $rows) {
	global $conf;
	$desc = ($var) ? $var : save_text($_POST[$name]);
	include("config/config_uploads.php");
	$con = explode("|", $confup[strtolower($mod)]);
	$style = (defined("ADMIN_FILE")) ? "admin" : strtolower($mod);
	if ((defined("ADMIN_FILE") && $conf['redaktor'] == 1) || (!defined("ADMIN_FILE"))) {
		$code = "<script language=\"JavaScript\" type=\"text/javascript\" src=\"ajax/insert_code.js\"></script>"
		."<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td><div class=\"editor\">"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"RowsTextarea('".$id."', 1)\"><img src=\"".img_find("editor/plus")."\" title=\""._EPLUS."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"RowsTextarea('".$id."', 0)\"><img src=\"".img_find("editor/minus")."\" title=\""._EMINUS."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('b', '', '', '', '".$id."')\"><img src=\"".img_find("editor/bold")."\" title=\""._EBOLD."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('i', '', '', '', '".$id."')\"><img src=\"".img_find("editor/italic")."\" title=\""._EITALIC."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('u', '', '', '', '".$id."')\"><img src=\"".img_find("editor/underline")."\" title=\""._EUNDERLINE."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('s', '', '', '', '".$id."')\"><img src=\"".img_find("editor/striket")."\" title=\""._ESTRIKET."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('li', '', '', '', '".$id."')\"><img src=\"".img_find("editor/li")."\" title=\""._ELI."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('hr', '', '', '', '".$id."')\"><img src=\"".img_find("editor/hr")."\" title=\""._EHR."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('left', '', '', '', '".$id."')\"><img src=\"".img_find("editor/left")."\" title=\""._ELEFT."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('center', '', '', '', '".$id."')\"><img src=\"".img_find("editor/center")."\" title=\""._ECENTER."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('right', '', '', '', '".$id."')\"><img src=\"".img_find("editor/right")."\" title=\""._ERIGHT."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('justify', '', '', '', '".$id."')\"><img src=\"".img_find("editor/justify")."\" title=\""._EYUSTIFY."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('code', '', '', '', '".$id."')\"><img src=\"".img_find("editor/code")."\" title=\""._CODE."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('php', '', '', '', '".$id."')\"><img src=\"".img_find("editor/php")."\" title=\"PHP - "._CODE."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('hide', '', '', '', '".$id."')\"><img src=\"".img_find("editor/hide")."\" title=\""._HIDE."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('url', '"._JINFO."', '"._JTYPE."', '"._JERROR."', '".$id."')\"><img src=\"".img_find("editor/url")."\" title=\""._EURL."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('mail', '"._JINFO."', '"._JTYPE."', '"._JERROR."', '".$id."')\"><img src=\"".img_find("editor/mail")."\" title=\""._EEMAIL."\"></div>"
		."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('img', '', '', '', '".$id."')\"><img src=\"".img_find("editor/img")."\" title=\""._EIMG."\"></div>"
		."</div>"
		."<textarea id=\"".$id."\" name=\"".$name."\" cols=\"65\" rows=\"".$rows."\" class=\"".$style."\" OnKeyPress=\"TransliteFeld(this, event)\" OnSelect=\"FieldName(this, this.name)\" OnClick=\"FieldName(this, this.name)\" OnKeyUp=\"FieldName(this, this.name)\">".replace_break($desc)."</textarea>"
		."<div class=\"editor\">";
		if ((defined("ADMIN_FILE") && $con[8] == 1) || (is_user() && $con[8] == 1) || (!is_user() && $con[9] == 1)) $code .= "<div id=\"af".$id."-title\" class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\"><img title=\""._EUPLOAD."\" src=\"".img_find("editor/upload")."\"></div>";
		if (!$conf['smilies']) $code .= "<div id=\"sm".$id."-title\" class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\"><img src=\"".img_find("editor/smilie")."\" title=\""._ESMILIE."\"></div>";
		$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"InsertCode('quote', '', '', '', '".$id."')\"><img src=\"".img_find("editor/quote")."\" title=\""._EQUOTE."\"></div>";
		if (substr(""._LOCALE."", 0, 2) == "ru") {
			$code .= "<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"translateAlltoCyrillic()\"><img src=\"".img_find("editor/rus")."\" title=\""._ERUS."\"></div>"
			."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"translateAlltoLatin()\"><img src=\"".img_find("editor/eng")."\" title=\""._ELAT."\"></div>"
			."<div class=\"editorbutton\" OnMouseOver=\"this.className='editorbuttonover';\" OnMouseOut=\"this.className='editorbutton';\" OnClick=\"changelanguage()\"><img src=\"".img_find("editor/auto")."\" title=\""._EAUTOTR."\"></div>";
		}
		$fonts = 0;
		$font = array("Arial", "Courier New", "Mistral", "Impact", "Sans Serif", "Tahoma", "Helvetica", "Verdana");
		foreach ($font as $val) if ($val != "") $fonts .= "<option style=\"font-family: ".$val.";\" value=\"".$val."\">".$val."</option>";
		$colors = 0;
		$color = array("black", "silver", "gray", "white", "maroon", "orange", "orangered", "red", "purple", "fuchsia", "green", "lime", "olive", "yellow", "navy", "blue", "teal", "aqua");
		foreach ($color as $val) if ($val != "") $colors .= "<option style=\"color: ".$val.";\" value=\"".$val."\">"._ECOLOR."</option>";
		$fsizes = 0;
		$fsize = array("8", "10", "12", "14", "16", "18", "20", "22", "24", "26", "28", "30", "32");
		foreach ($fsize as $val) if ($val != "") $fsizes .= "<option value=\"".$val."\">"._ESIZE." ".$val."</option>";
		$code .= "<div class=\"editorselect\"><select name=\"family\" OnChange=\"InsertCode('family', this.options[this.selectedIndex].value, '', '', '".$id."')\">".$fonts."</select></div>"
		."<div class=\"editorselect\"><select name=\"color\" OnChange=\"InsertCode('color', this.options[this.selectedIndex].value, '', '', '".$id."')\">".$colors."</select></div>"
		."<div class=\"editorselect\"><select name=\"size\" OnChange=\"InsertCode('size', this.options[this.selectedIndex].value, '', '', '".$id."')\">".$fsizes."</select></div></div>";
		if ($conf['smilies'] == 1) {
			$code .= "<div class=\"smilies\">";
			for ($i = 1; $i < 19; $i++) {
				$i = ($i < 10) ? "0".$i ."" : $i;
				$code .= " <img src=\"images/smilies/$i.gif\" OnClick=\"AddSmile(' *$i');\" style=\"cursor: pointer; margin: 3px 2px 0px 0px;\" alt=\""._SMILIE." - $i\" title=\""._SMILIE." - $i\">";
			}
			$code .= "</div>";
		} elseif ($conf['smilies'] == 2) {
			$code .= "<div class=\"smilies\">";
			$i = 1;
			$dir = opendir("images/smilies");
			while ($entry = readdir($dir)) {
				if (preg_match("/(\.gif|\.png|\.jpg|\.jpeg)$/is", $entry) && $entry != "." && $entry != "..") {
					$i = ($i < 10) ? "0".$i ."" : $i;
					$code .= " <img src=\"images/smilies/$i.gif\" OnClick=\"AddSmile(' *$i');\" style=\"cursor: pointer; margin: 3px 2px 0px 0px;\" alt=\""._SMILIE." - $i\" title=\""._SMILIE." - $i\">";
					$i++;
				}
			}
			closedir($dir);
			$code .= "</div>";
		} else {
			$code .= "<div id=\"sm".$id."\" class=\"smilies\"><script language=\"JavaScript\" type=\"text/javascript\">var edits = new SwitchCont('sm".$id."', '2');</script>";
			$i = 1;
			$dir = opendir("images/smilies");
			while ($entry = readdir($dir)) {
				if (preg_match("/(\.gif|\.png|\.jpg|\.jpeg)$/is", $entry) && $entry != "." && $entry != "..") {
					$i = ($i < 10) ? "0".$i ."" : $i;
					$code .= " <img src=\"images/smilies/$i.gif\" OnClick=\"AddSmile(' *$i');\" style=\"cursor: pointer; margin: 3px 2px 0px 0px;\" alt=\""._SMILIE." - $i\" title=\""._SMILIE." - $i\">";
					$i++;
				}
			}
			closedir($dir);
			$code .= "</div>";
		}
		if ((defined("ADMIN_FILE") && $con[8] == 1) || (is_user() && $con[8] == 1) || (!is_user() && $con[9] == 1)) {
			$code .= "<div id=\"af".$id."\" class=\"smilies\">";
			if ($id != 2) {
				$fsizel = $con[2] / 1048576;
				$code .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"ajax/swfupload/swfupload.css\">
				<script type=\"text/javascript\" src=\"ajax/swfupload/swfupload.js\"></script>
				<script type=\"text/javascript\" src=\"ajax/swfupload/swfupload.swfobject.js\"></script>
				<script type=\"text/javascript\" src=\"ajax/swfupload/swfupload.queue.js\"></script>
				<script type=\"text/javascript\" src=\"ajax/swfupload/fileprogress.js\"></script>
				<script type=\"text/javascript\" src=\"ajax/swfupload/handlers.js\"></script>
				<script type=\"text/javascript\">
				var swfu;
				SWFUpload.onload = function () {
					var settings = {
						flash_url : \"upload.swf\",
						upload_url: \"ajax.php?go=4&mod=".$mod."\",
						file_size_limit : \"".$fsizel." MB\",
						file_types : \"*.".str_replace(",", ";*.", $con[0])."\",
						file_types_description : \"All Files\",
						file_upload_limit : ".$con[5].",
						file_queue_limit : 0,
						custom_settings : {
							progressTarget : \"fsUploadProgress\",
							cancelButtonId : \"btnCancel\"
						},
						debug: false,
						
						button_placeholder_id : \"spanButtonPlaceholder\",
						button_width: 80,
						button_height: 20,
						button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
						button_cursor: SWFUpload.CURSOR.HAND,
						
						swfupload_loaded_handler : swfUploadLoaded,
						file_queued_handler : fileQueued,
						file_queue_error_handler : fileQueueError,
						file_dialog_complete_handler : fileDialogComplete,
						upload_start_handler : uploadStart,
						upload_progress_handler : uploadProgress,
						upload_error_handler : uploadError,
						upload_success_handler : uploadSuccess,
						upload_complete_handler : uploadComplete,
						queue_complete_handler : queueComplete,
						
						minimum_flash_version : \"9.0.28\",
						swfupload_pre_load_handler : swfUploadPreLoad,
						swfupload_load_failed_handler : swfUploadLoadFailed
					};
					swfu = new SWFUpload(settings);
				}
				</script>
				
				<div id=\"divSWFUploadUI\">
				<fieldset id=\"fsUploadProgress\" style=\"margin: 5px;\">
				<legend>"._UPLOADINFO."</legend><div align=\"left\" style=\"margin: 3px;\">"
				._FTYPE.": ".str_replace(",", ", ", $con[0])."<br>"
				._FSIZEALL.": ".files_size($con[1])."<br>"
				._FSIZE.": ".files_size($con[2])."<br>"
				._AWIDTH.": ".$con[3]." px<br>"
				._AHEIGHT.": ".$con[4]." px<br>"
				._FILEUP.": ".$con[5]."<br>"
				."</div></fieldset>
				<p id=\"divStatus\">0 "._FILEISUP."</p>
				<p>
				<span id=\"spanButtonPlaceholder\"></span>
				<input id=\"btnUpload\" type=\"button\" value=\""._UPLOAD."\" class=\"fbutton\">
				<input id=\"btnCancel\" type=\"button\" value=\""._CANALLUP."\" disabled=\"disabled\" class=\"fbutton\">
				<input type=\"button\" value=\"NEW\" class=\"fbutton\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=$mod&id=$id&text=1', 'f".$id."');\">
				<input type=\"button\" value=\""._UPDATE."\" class=\"fbutton\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=$mod&id=$id', 'f".$id."');\"></p><br></div>
				<noscript>Were sorry. SWFUpload could not load. You must have JavaScript enabled to enjoy SWFUpload.</noscript>
				<div id=\"divLoadingContent\" style=\"display: none;\">SWFUpload is loading. Please wait a moment...</div>
				<div id=\"divLongLoading\" style=\"display: none;\">SWFUpload is taking a long time to load or the load has failed. Please make sure that the Flash Plugin is enabled and that a working version of the Adobe Flash Player is installed.</div>
				<div id=\"divAlternateContent\" style=\"display: none;\">Were sorry.  SWFUpload could not load.  You may need to install or upgrade Flash Player. Visit the <a href=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" target=\"_blank\">Adobe website</a> to get the Flash Player.</div>";
			} else {
				$code .= "<input type=\"button\" value=\"NEW\" style=\"margin: 5px 5px 0 5px;\" class=\"fbutton\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=$mod&id=$id&text=1', 'f".$id."');\">";
				$code .= "<input type=\"button\" value=\""._UPDATE."\" style=\"margin: 5px 0 0 0;\" class=\"fbutton\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=$mod&id=$id', 'f".$id."');\">";
			}
			$code .= "<script type=\"text/javascript\">ajax('ajax.php?go=3&op=show_files&mod=$mod&id=$id&text=1', 'f".$id."');</script><div id=\"f".$id."\"></div></div><script language=\"JavaScript\" type=\"text/javascript\">var editu = new SwitchCont('af".$id."', '2');</script>";
		}
	} elseif (defined("ADMIN_FILE") && $conf['redaktor'] == 2) {
		if (!preg_match("#blocks|configure|editor|groups|rss_conf|security|template|style#i", $_GET['op'])) {
			static $jscript;
			if (!isset($jscript)) {
				$code = "<script type=\"text/javascript\" src=\"modules/tiny_mce/tiny_mce.js\"></script>
				<script type=\"text/javascript\">
				tinyMCE.init({
					mode : \"textareas\",
					theme : \"advanced\",
					plugins : \"safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,images,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template\",
					
					theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect\",
					theme_advanced_buttons2 : \"bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code\",
					theme_advanced_buttons3 : \"save,newdocument,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,forecolor,backcolor,|,preview,images\",
					theme_advanced_buttons4 : \"hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen\",
					theme_advanced_buttons5 : \"tablecontrols\",
					theme_advanced_buttons6 : \"insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak\",
					
					theme_advanced_toolbar_location : \"top\",
					theme_advanced_toolbar_align : \"center\",
					theme_advanced_statusbar_location : \"bottom\",
					theme_advanced_resizing : true,
					
					language: \"".substr(_LOCALE, 0, 2)."\",
					content_css : \"css/content.css\",
					
					template_external_list_url : \"lists/template_list.js\",
					external_link_list_url : \"lists/link_list.js\",
					external_image_list_url : \"lists/image_list.js\",
					media_external_list_url : \"lists/media_list.js\",
					
					template_replace_values : {
						username : \"Some User\",
						staffid : \"991234\"
					}
				});
				</script>";
				$jscript = 1;
			} else {
				$code = "";
			}
		}
		$code .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>"
		."<textarea id=\"".$id."\" name=\"".$name."\" cols=\"65\" rows=\"".$rows."\" class=\"".$style."\">".$desc."</textarea>";
	} elseif (defined("ADMIN_FILE") && $conf['redaktor'] == 3) {
		ob_start();
		include("modules/spaw2/spaw.inc.php");
		$sp = new SpawEditor($name, $desc);
		$sp->show();
		$code = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>".ob_get_contents()."";
		ob_end_clean();
	} elseif (defined("ADMIN_FILE") && $conf['redaktor'] == 4) {
		ob_start();
		include_once("modules/fckeditor/fckeditor.php") ;
		$oFCKeditor = new FCKeditor($name);
		$oFCKeditor->BasePath = "modules/fckeditor/";
		$oFCKeditor->Config['AutoDetectLanguage'] = false;
		$oFCKeditor->Config['DefaultLanguage'] = substr(_LOCALE, 0, 2);
		$oFCKeditor->Width = "400px";
		$oFCKeditor->Height = "400px";
		$oFCKeditor->Value = $desc;
		$oFCKeditor->Create() ;
		$code = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>".ob_get_contents()."";
		ob_end_clean();
	} else {
		$code = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>"
		."<textarea id=\"".$id."\" name=\"".$name."\" cols=\"65\" rows=\"".$rows."\" class=\"".$style."\">".$desc."</textarea>";
	}
	$code .= "</td></tr></table>";
	return $code;
}

function show_files() {
	global $user;
	include("config/config_uploads.php");
	$id = intval($_GET['id']);
	$dir = strtolower($_GET['mod']);
	$typ = intval($_GET['typ']);
	$con = explode("|", $confup[$dir]);
	$connum = ($con[7]) ? $con[7] : "50";
	$file = $_GET['file'];
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	if ($typ == 1 && is_moder()) {
		$path = ($id == 1) ? "uploads/".$dir."/" : "uploads/".$dir."/thumb/";
		if ($file && $dir) unlink($path.$file);
		$dh = opendir($path);
		while ($entry = readdir($dh)) {
      if ($entry != "." && $entry != ".." && $entry != "index.html" && preg_match("/\./", $entry)) {$ft=filemtime($path.$entry); if (intval($_GET['text'])==0 || intval($_GET['text'])>0 && $ft>=(time()-3600)) $files[] = array($ft, $entry);}
		}
		closedir($dh);
		if ($files) {
			rsort($files);
			foreach ($files as $entry) {
				preg_match("/([a-zA-Z0-9]+)\-([a-zA-Z0-9]+)\-([0-9]+)\.([a-zA-Z0-9]+)/", $entry[1], $date);
				$uname = ($user[0]) ? intval($user[0]) : 0;
				if (($uname == $date[3] && $date[2] && $date[1]) || is_moder()) {
					$filesize = filesize($path.$entry[1]);
					list($imgwidth, $imgheight) = getimagesize($path.$entry[1]);
					$show = ($imgwidth && $imgheight) ? "<img src=\"".img_find("all/view")."\" align=\"center\" style=\"cursor: pointer;\" OnMouseOver=\"Tip('<img src=&quot;".$path."".$entry[1]."&quot; width=&quot;".$imgwidth."&quot;>')\" OnClick=\"CaricaFoto('".$path."".$entry[1]."')\"> <img src=\"".img_find("all/delete")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=".$dir."&id=".$id."&file=".$entry[1]."&typ=1', 'f".$id."');\" alt=\""._DELETE."\" title=\""._DELETE."\">" : "<img src=\"".img_find("all/delete")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=".$dir."&id=".$id."&file=".$entry[1]."&typ=1', 'f".$id."');\" alt=\""._DELETE."\" title=\""._DELETE."\">";
					$img = ($imgwidth && $imgheight) ? "".$imgwidth." x ".$imgheight."" : ""._NO."";
					$contents[] = "<tr class=\"bgcolor1\"><td align=\"center\">".$show."</td><td>".$path."".$entry[1]."</td><td align=\"center\">".date ("d.m.Y H:i:s", $entry[0])."</td><td align=\"center\">".files_size($filesize)."</td><td align=\"center\">".$img."</td></tr>";
					$a++;
				}
			}
		}
		$numpages = ceil($a / $connum);
		$offset = ($num - 1) * $connum;
		$tnum = ($offset) ? $connum + $offset : $connum;
		for ($i = $offset; $i < $tnum; $i++) {
			if ($contents[$i] != "") {
				$cont .= "".$contents[$i]."";
			}
		}
		$contnum = ($a > $connum) ? num_ajax($a, $numpages, $connum, "go=3&op=show_files&mod=$dir&id=$id&typ=1&") : "";
		$content = ($cont) ? "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"bgcolor4\"><tr><th>"._FUNCTIONS."</th><th>"._FILE."</th><th>"._DATE."</th>"
		."<th>"._SIZE."</th><th>"._WIDTH." x "._HEIGHT."</th></tr>".$cont."</table>".$contnum."" : "";
		open();
		echo $content;
		close();
	} else {
		$path = "uploads/".$dir."/";
		if (is_moder($dir) && $file && $dir) unlink($path.$file);
		$dh = opendir($path);
		while ($entry = readdir($dh)) {
			if ($entry != "." && $entry != ".." && $entry != "index.html" && preg_match("/\./", $entry)) {$ft=filemtime($path.$entry); if (intval($_GET['text'])==0 || intval($_GET['text'])>0 && $ft>=(time()-3600)) $files[] = array($ft, $entry);}
		}
		closedir($dh);
		if ($files) {
			rsort($files);
			foreach ($files as $entry) {
				preg_match("/([a-zA-Z0-9]+)\-([a-zA-Z0-9]+)\-([0-9]+)\.([a-zA-Z0-9]+)/", $entry[1], $date);
				$uname = ($user[0]) ? intval($user[0]) : 0;
				if (($uname == $date[3] && $date[2] && $date[1]) || is_moder($dir)) {
					$filesize = filesize($path.$entry[1]);
					list($imgwidth, $imgheight) = getimagesize($path.$entry[1]);
					$show = ($imgwidth && $imgheight) ? "<img src=\"".img_find("all/view")."\" align=\"center\" style=\"cursor: pointer;\" OnMouseOver=\"Tip('<img src=&quot;".$path."".$entry[1]."&quot; width=&quot;".$imgwidth."&quot;>')\" OnClick=\"CaricaFoto('".$path."".$entry[1]."')\"> <img src=\"".img_find("editor/export")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"InsertCode('attach', '".$entry[1]."', '', '', '".$id."')\" alt=\""._INSERT." ".$imgwidth." x ".$imgheight."\"  title=\""._INSERT." ".$imgwidth." x ".$imgheight."\"> <img src=\"".img_find("editor/img")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"InsertCode('img', '".$path."".$entry[1]."', '', '', '".$id."')\" alt=\""._EIMG." ".$imgwidth." x ".$imgheight."\"  title=\""._EIMG." ".$imgwidth." x ".$imgheight."\"> " : "<img src=\"".img_find("editor/export")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"InsertCode('attach', '".$path."".$entry[1]."', '', '', '".$id."')\" alt=\""._EIMG."\"  title=\""._EIMG."\"> ";
					$show .= (is_moder($dir)) ? "<img src=\"".img_find("all/delete")."\" border=\"0\" align=\"center\" style=\"cursor: pointer;\" OnClick=\"ajax('ajax.php?go=3&op=show_files&mod=".$dir."&id=".$id."&file=".$entry[1]."', 'f".$id."');\" alt=\""._DELETE."\" title=\""._DELETE."\">" : "";
					$contents[] = "<tr class=\"bgcolor1\"><td align=\"center\">".$show."</td><td>".$entry[1]."</td><td align=\"center\">".files_size($filesize)."</td></tr>";
					$a++;
				}
			}
		}
		$numpages = ceil($a / $connum);
		$offset = ($num - 1) * $connum;
		$tnum = ($offset) ? $connum + $offset : $connum;
		for ($i = $offset; $i < $tnum; $i++) {
			if ($contents[$i] != "") {
				$cont .= "".$contents[$i]."";
			}
		}
		$contnum = ($a > $connum) ? num_ajax($a, $numpages, $connum, "go=3&op=show_files&mod=$dir&id=$id&") : "";
		$content = ($cont) ? "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\"><tr><td><table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"bgcolor4\"><tr><th>"._FUNCTIONS."</th><th>"._FILE."</th><th>"._SIZE."</th></tr>".$cont."</table>".$contnum."</td></tr></table>" : "";
		echo $content;
	}
}

# Format Nummer Page
function num_ajax($numstories, $numpages, $storynum, $module_link="") {
	global $admin_file;
	$num = isset($_GET['num']) ? intval($_GET['num']) : "1";
	$id = intval($_GET['id']);
	if ($numpages > 1) {
		$content = "<div align=\"center\" class=\"pagelink\"><h4>"._OVERALL." $numstories "._ON." $numpages "._PAGE_S." $storynum "._PERPAGE."</h4>";
		if ($num > 1) {
			$prevpage = $num - 1;
			$content .= "<span OnClick=\"ajax('ajax.php?".$module_link."num=$prevpage&text=".intval($_GET['text'])."', 'f".$id."');\" title=\"&lt;&lt;\">&lt;&lt;</span> ";
		}
		for ($i = 1; $i < $numpages+1; $i++) {
			if ($i == $num) {
				$content .= "<span title=\"$i\">$i</span>";
			} else {
				if ((($i > ($num - 8)) && ($i < ($num + 8))) OR ($i == $numpages) || ($i == 1)) $content .= "<span OnClick=\"ajax('ajax.php?".$module_link."num=$i&text=".intval($_GET['text'])."', 'f".$id."');\" title=\"$i\">$i</span>";
			}
			if ($i < $numpages) {
				if (($i > ($num - 9)) && ($i < ($num + 8))) $content .= " ";
				if (($num > 9) && ($i == 1)) $content .= " <span>...</span>";
				if (($num < ($numpages - 8)) && ($i == ($numpages - 1))) $content .= "<span>...</span> ";
			}
		}
		if ($num < $numpages) {
			$nextpage = $num + 1;
			$content .= " <span OnClick=\"ajax('ajax.php?".$module_link."num=$nextpage&text=".intval($_GET['text'])."', 'f".$id."');\" title=\"&gt;&gt;\">&gt;&gt;</span>";
		}
		$content .= "</div>";
		return $content;
	}
}
?>