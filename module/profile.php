<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Logins.php');
require_once('lib/MainModule.php');

// user profile
$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_title('Profils');
$template->set_file('FILE_module', 'tmpl.profile.php');
$template->copy_block('BLOCK_middle', 'FILE_module');

$section = array_shift($sys_parameters);

# pop view
if($section == 'view')
{
	include("module/profile/view.inc.php");
	return;
}

# another user profile
if($section == 'user')
{
	include("module/profile/user.inc.php");
	return;
}

if(!$section)
{
	include("module/profile/default.inc.php");
}

$template->set_right();
$template->set_login();
$template->set_reviews();
$template->set_poll();
$template->set_search();
$template->set_online();

$template->out();

