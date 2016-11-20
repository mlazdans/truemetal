<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Article.php');

$now = time();

$_SESSION['forums']['viewed'] = array();
$_SESSION['comments']['viewed'] = array();

$_SESSION['forums']['viewed_before'] = $now;
$_SESSION['comments']['viewed_before'] = $now;

header("Location: $sys_http_root/");

