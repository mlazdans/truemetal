<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// Custom session handleris

require_once('lib/SessHandler.php');
$cookie_time = 31536000; # 1 year

ini_set('session.save_handler', 'user');
ini_set('session.use_cookies', true);
ini_set('session.name', 'sid');
ini_set('session.cookie_lifetime', $cookie_time);
ini_set('session.serialize_handler', 'php');
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

$sess_handler = new SessHandler();

session_set_save_handler(
	array(&$sess_handler, "sess_open"),
	array(&$sess_handler, "sess_close"),
	array(&$sess_handler, "sess_read"),
	array(&$sess_handler, "sess_write"),
	array(&$sess_handler, "sess_destroy"),
	array(&$sess_handler, "sess_gc")
);

session_set_cookie_params($cookie_time, '/');
session_start();
