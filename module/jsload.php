<?php declare(strict_types = 1);

header("Content-Type: text/javascript");

$v = get('v');

if($v != $sys_script_version)
{
	header($_SERVER["SERVER_PROTOCOL"]." 410 Gone");
	return;
}

if(isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
{
	ini_set('zlib.output_compression', 1);
}

// if(!$sys_debug)
{
	$expires = (7 * 24 * 3600);
	header("Cache-Control: max-age=$expires");
	header("Expires: ".gmdate("D, d M Y H:i:s", time() + $expires) . " GMT");
}

$SCRIPTS = ($sys_debug || User::is_admin()) ? $sys_admin_js : $sys_js;

foreach($SCRIPTS as $js){
	$path = join_paths($sys_root, "js", "$js.js");
	if(is_file($path)){
		readfile($path);
	}
}
