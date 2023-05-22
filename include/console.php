<?php declare(strict_types = 1);

ini_set('error_prepend_string', '');
ini_set('error_append_string', '');
ini_set('html_errors', false);
ini_set('memory_limit', -1);
set_time_limit(0);

$CONS_START = microtime(true);

register_shutdown_function(function(){
	global $CONS_START;

	print "\nFinished in: ".number_format(microtime(true) - $CONS_START, 3)."\n";
});
