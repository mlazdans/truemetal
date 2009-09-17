<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv-LV">
<head>
<meta http-equiv="Content-Language" content="lv" />
<meta http-equiv="Content-Type" content="text/html; charset={encoding}" />
<title>{title} ({USER_name})</title>
<meta http-equiv="Pragma" content="no-cache" />
<link rel="stylesheet" type="text/css" href="{http_root}/css/admin_styles.css?{script_version}" />
<script type="text/javascript" src="{http_root}/js/jquery.js?{script_version}"></script>
<script type="text/javascript" src="{http_root}/js/truemetal.js?{script_version}"></script>
<script type="text/javascript" src="{http_root}/tiny_mce/tiny_mce.js?{script_version}"></script>
<script type="text/javascript" src="{http_root}/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php?{script_version}"></script>
<script type="text/javascript">
var TINY_OPTIONS = {
	theme: "advanced",
	mode: "specific_textareas",
	editor_selector: "edit",
	theme_advanced_toolbar_location: "top",
	theme_advanced_toolbar_align: "left",
	language: 'en',
	//file_browser_callback: "tinyBrowser",
	elements: "ajaxfilemanager",
	file_browser_callback: "ajaxfilemanager",
	//elements: "ajaxfilemanager",
	relative_urls: false,
	content_css: "/css/tiny_mce.css",
	entities: "",

	//plugins : "safari,pagebreak,style,layer,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	//plugins: "advhr",
	plugins: "advimage,advlink,media,contextmenu,print,visualchars,fullscreen,advhr",

	theme_advanced_buttons1: "undo,redo,|,forecolor,backcolor,|,bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,outdent,indent,blockquote,|,bullist,numlist,|,hr",
	theme_advanced_buttons2: "link,unlink,anchor,image,media,|,removeformat,cleanup,visualchars,|,charmap,|,print,fullscreen,code",
	theme_advanced_buttons3: "",
	/*
	theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
	*/
	plugin_insertdate_dateFormat: "%d.%m.%Y",
	plugin_insertdate_timeFormat: "%H:%M:%S"
};

function initEditor(){
	tinyMCE.init(TINY_OPTIONS);
} // initEditor

function ajaxfilemanager(field_name, url, type, win) {
	var ajaxfilemanagerurl = "/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php";
	switch (type) {
		case "image":
			break;
		case "media":
			break;
		case "flash":
			break;
		case "file":
			break;
		default:
			return false;
	}
	tinyMCE.activeEditor.windowManager.open({
		url: ajaxfilemanagerurl,
		width: 782,
		height: 440,
		inline: "yes",
		close_previous: "no"
	},{
		window: win,
		input: field_name
	});
}

initEditor();

</script>
</head>
<body>

<div id="header">
	<h1>Admin: {USER_name}</h1>
</div>

<div id="menu">
<table border="0">
<tr>
	<!-- BEGIN BLOCK_adminmodules -->
	<td class="{adminmodule_class}">
		<a class="A-cat" href="{admin_root}/{adminmodule_id}/">{adminmodule_name}</a>
	</td>
	<!-- END BLOCK_adminmodules -->
	<td class="TD-menu">
		<a class="A-cat" href="http://aw.dqdp.net/awstats/awstats.pl?config=truemetal.lv">Statistika</a>
	</td>
</tr>
</table>
</div>

<div id="middle">
<!-- BEGIN BLOCK_middle --><!-- END BLOCK_middle -->
</div>

<hr />

<div id="footer">
	Copyright &copy; Norge Datorsistēmas 2003-2008<br/>
	Copyright &copy; dqdp.net 2008-{year}
</div>

</body>
</html>
