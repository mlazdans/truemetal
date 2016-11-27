<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$login = array_shift($sys_parameters);

if($login)
{
	include('module/user/profile/user.inc.php');
	return;
}

$template = new MainModule($sys_module_id);
$template->set_title('Profils');
$template->set_file('FILE_module', 'user/profile/private.tpl');
$template->copy_block('BLOCK_middle', 'FILE_module');

# View and edit private profile
include('module/user/profile/private.inc.php');

$template->set_right_defaults();
$template->out();

