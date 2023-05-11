<?php declare(strict_types = 1);

$template = new MainModule($sys_module_id, 'index.tpl');
$template->set_title("Reģistrācija");
$template->set_right_defaults();

if(user_blacklisted() || Logins::banned24h($GLOBALS['ip']))
{
	$template->forbidden("Blacklisted IP: $ip");
	$template->out(null);
} else {
	$template->out(register($template, $sys_parameters));
}
