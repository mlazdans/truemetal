<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// Custom session handleris

require_once('lib/SessionHandler.php');
$cookie_time = 31536000; // 1 year

ini_set('session.save_handler', 'user');
ini_set('session.use_cookies', true);
ini_set('session.name', 'sid');
ini_set('session.cookie_lifetime', $cookie_time);
ini_set('session.serialize_handler', 'php');
ini_set('session.gc_probability', 1);

$sess_handler = new SessionHandler();

session_set_save_handler(
	array(&$sess_handler, "sess_open"),
	array(&$sess_handler, "sess_close"),
	array(&$sess_handler, "sess_read"),
	array(&$sess_handler, "sess_write"),
	array(&$sess_handler, "sess_destroy"),
	array(&$sess_handler, "sess_gc")
);

session_set_cookie_params($cookie_time, $sys_http_root.'/');
session_start();
