#!/usr/local/php7/bin/php
<?php

$KERNEL_LEAVE_AFTER_INIT = true;
require_once('../public/kernel.php');
require_once('include/console.php');
require_once('include/dbconnect.php');
require_once('lib/utils.php');

$email = 'kkas@banda.lv';

email($email, 'Test', "Test");
