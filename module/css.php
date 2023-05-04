<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$v = get('v');
//$v = (int)array_shift($sys_parameters);
if($v != $sys_script_version){
	header($_SERVER["SERVER_PROTOCOL"]." 410 Gone");
	return;
}

if(
	isset($_SERVER['HTTP_ACCEPT_ENCODING']) &&
	substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
	)
{
	ini_set('zlib.output_compression', 1);
}

$expires = (7 * 24 * 3600);
header("Content-Type: text/css");
header("Cache-Control: max-age=$expires");
header("Expires: ".gmdate("D, d M Y H:i:s", time() + $expires) . " GMT");

//if($SCRIPTS = get('s')){
if($SCRIPTS = $sys_css){
	foreach($SCRIPTS as $js){
		if(is_file("css/$js.css")){
			readfile("css/$js.css");
		}
	}
}

