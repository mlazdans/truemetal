<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$now = time();

$_SESSION['res']['viewed_before'] = $now;
$_SESSION['res']['viewed_date'] = array();
$_SESSION['forums']['viewed_date'] = array();

# Remove historic entries
unset($_SESSION['res']['viewed']);
unset($_SESSION['forums']['viewed']);
unset($_SESSION['forums']['viewed_before']);

header("Location: /");

