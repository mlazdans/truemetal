<?php

if(substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
{
	ob_start("ob_gzhandler");
} else {
	ob_start();
}

header("Content-type: text/javascript");
header("Expires: ".gmdate("D, d M Y H:i:s", time() + (7 * 24 * 3600)) . " GMT");

if($SCRIPTS = get('s'))
{
	foreach($SCRIPTS as $js)
	{
		if(is_file("js/$js"))
			readfile("js/$js");
	}
}

ob_end_flush();