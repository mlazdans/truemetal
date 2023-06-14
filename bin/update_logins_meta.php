<?php declare(strict_types = 1);

require_once(__DIR__.'/../include/boot.php');
require_once('include/dbconnect.php');

DB::Query("CALL logins_meta_update(NULL)");
