<?php declare(strict_types = 1);

$cookie_time = 3600 * 24 * 365;

ini_set('session.use_cookies', true);
ini_set('session.name', 'sid');
ini_set('session.cookie_lifetime', $cookie_time);
ini_set('session.serialize_handler', 'php');
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', $cookie_time);

session_set_save_handler(new SessHandler);

session_set_cookie_params($cookie_time, '/');

session_start();
