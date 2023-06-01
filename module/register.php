<?php declare(strict_types = 1);

$template = new MainModule($sys_module_id, 'index.tpl');
$template->set_title("Reģistrācija");
$template->set_right_defaults();

if(User::blacklisted())
{
	$template->forbidden("Blacklisted IP: $ip");
} elseif(Logins::banned24h($ip)){
	$template->forbidden("Jāatpūšas 10 min.");
} else {
	$T = register($template, $sys_parameters);
}

$template->out($T??null);
