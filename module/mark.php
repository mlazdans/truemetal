<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$now = time();

$_SESSION['res']['viewed'] = array();
$_SESSION['res']['viewed_before'] = $now;

header("Location: /");

