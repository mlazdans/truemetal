<?php declare(strict_types = 1);

# Blacklisted
if(user_blacklisted() || Logins::banned24h($GLOBALS['ip']))
{
	print "Blacklisted: $ip";
	return;
}

$template = new MainModule($sys_module_id, 'index.tpl');
$template->set_title("Reģistrācija");
$template->set_right_defaults();
$template->out(register($template, $sys_parameters));
