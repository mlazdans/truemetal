<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// error handler

error_reporting($sys_error_reporting);

ini_set('display_errors', $sys_debug ? 1 : 0);

$sys_old_error_handler = set_error_handler("__error_handler");

function __error_handler($int_errno, $str_errstr, $str_errfile, $int_errline, $str_errcontext)
{
	$int_error_level = $GLOBALS['sys_error_reporting'];
	$str_err_type = '';

	switch ($int_errno) {
		case E_WARNING:
			$str_err_type = 'E_WARNING';
			break;
		case E_NOTICE:
			$str_err_type = 'E_NOTICE';
			break;
		case E_USER_ERROR:
			$str_err_type = 'E_USER_ERROR';
			break;
		case E_USER_WARNING:
			$str_err_type = 'E_USER_WARNING';
			break;
		case E_USER_NOTICE:
			$str_err_type = 'E_USER_NOTICE';
			break;
		default:
			$str_err_type = 'E_UNKNOWN';
			break;
	}

	if($int_error_level & $int_errno) {

		$str_err_msg = "<hr color=\"#C0000\">\n---- ERROR ----\n";
		$str_err_msg .= date("D, M j H:i:s (O)")."<br>\n";
		$str_err_msg .= "error #: ".$int_errno."<br>\n";
		$str_err_msg .= "error type: ".$str_err_type."<br>\n";
		$str_err_msg .= "message: <font color=\"red\">".$str_errstr."</font><br>\n";
		$str_err_msg .= "script: ".$str_errfile.", line ".$int_errline."<hr color=\"#C0000\">\n";

		if(isset($_GLOBALS['sys_template'])) {
			$_GLOBALS['sys_template']->set_var('block_middle', $str_err_msg);
		} else
			print $str_err_msg;
	}
} // __error_handler

?>