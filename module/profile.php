<?php declare(strict_types = 1);

$section = array_shift($sys_parameters);
if($section == 'user')
{
	$user = array_pop($sys_parameters);
	redirectp("/user/profile/$user/");
}

if($section == 'view')
{
	$template = new MainTemplate;
	$template->gone("Removed from public eyes");
	$template->set_right_defaults();
	$template->print();
}
