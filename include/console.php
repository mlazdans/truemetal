<?php declare(strict_types = 1);

ini_set('error_prepend_string', '');
ini_set('error_append_string', '');
ini_set('html_errors', false);
ini_set('memory_limit', -1);
set_time_limit(0);

$CONS_START = microtime(true);

register_shutdown_function(function(){
	global $CONS_START;

	printf("\nMem usage: %s MB\n", number_format(memory_get_peak_usage(true)/1024/1204, 2));
	printf("Finished in: %s sec\n", number_format(microtime(true) - $CONS_START, 2));
});
