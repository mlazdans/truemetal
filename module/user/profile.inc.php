<?php declare(strict_types = 1);

$l_hash = array_shift($sys_parameters);

$template = new MainModule($sys_module_id);
$template->set_title('Profils');

if($l_hash)
{
	$T = public_profile($template, $l_hash);
} else {
	$T = private_profile($template);
}

$template->set_right_defaults();
$template->out($T);
