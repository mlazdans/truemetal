tinyMCE.init({
	theme: "advanced",
	mode: "specific_textareas",
	editor_selector: "edit",
	theme_advanced_toolbar_location: "top",
	theme_advanced_toolbar_align: "left",
	language: 'en',
	file_browser_callback: "tinyBrowser",
	relative_urls: false,
	content_css: "/editor.css",

//	plugins : "safari,pagebreak,style,layer,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
//	plugins: "advhr",

	theme_advanced_buttons1: "undo,redo,|,forecolor,backcolor,|,bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,outdent,indent,blockquote,|,bullist,numlist,|,hr",
	theme_advanced_buttons2: "link,unlink,anchor,image,media,|,removeformat,cleanup,visualchars,|,charmaphr,insertdate,inserttime,|,print,fullscreen,code",
	theme_advanced_buttons3: "",
/*
	theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
*/
	plugin_insertdate_dateFormat: "%d.%m.%Y",
	plugin_insertdate_timeFormat: "%H:%M:%S"
});


