<?php

if(
	isset($_SERVER['HTTP_ACCEPT_ENCODING']) &&
	substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
	)
{
	ob_start("ob_gzhandler");
} else {
	ob_start();
}

header("Content-type: text/css");
header("Expires: ".gmdate("D, d M Y H:i:s", time() + (7 * 24 * 3600)) . " GMT");

if($SCRIPTS = get('s'))
{
	foreach($SCRIPTS as $js)
	{
		if(is_file("css/$js"))
			readfile("css/$js");
	}
}

ob_end_flush();
