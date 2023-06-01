<?php declare(strict_types = 1);

$l_hash = array_shift($sys_parameters)??"";
$l_suff = array_shift($sys_parameters)??"";

user_image($l_hash, true, $l_suff);
