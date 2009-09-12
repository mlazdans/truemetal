<?php

$template = new AdminModule($sys_template_root.'/admin', "logins/edit");
$template->set_title('Admin :: logini :: rediģēt');

$Logins = new Logins();
$login = $Logins->load(array(
	'l_id'=>$l_id,
	'l_active'=>LOGIN_ALL,
	'l_accepted'=>LOGIN_ALL,
	));

//$template->enable('BLOCK_login_edit');
$template->set_array($login);

$YN = array(
	'l_active',
	'l_accepted',
	'l_emailvisible',
	'l_logedin',
	);

foreach($YN as $k)
{
	$v = sprintf("%s_%s_sel", $k, $login[$k]);
	$template->set_var($v, ' selected="selected"');
}

$template->out();

