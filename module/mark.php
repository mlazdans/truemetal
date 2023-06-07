<?php declare(strict_types = 1);

$_SESSION['res_seen_ts'] = array();
$_SESSION['res_marked_seen_ts'] = time();

header("Location: /");
