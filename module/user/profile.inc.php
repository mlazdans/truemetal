<?php declare(strict_types = 1);

// $json = isset($_GET['json']);
$l_hash = array_shift($sys_parameters);

$template = new MainModule($sys_module_id);
$template->set_title('Profils');

if($l_hash)
{
	$T = public_profile($template, $l_hash);
	$T->enable('BLOCK_profile_title');
} else {
	$T = private_profile($template);
}

$template->set_right_defaults();
$template->out($T);
