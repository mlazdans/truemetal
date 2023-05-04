<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$hl = rawurldecode(get("hl"));
$login = array_shift($sys_parameters);
$login_data = Logins::load_by_login($login);

$template = new MainModule($sys_module_id);
$template->set_file('FILE_user_comments', 'user/comments.tpl');
$template->copy_block('BLOCK_middle', 'FILE_user_comments');

$template->set_file('FILE_user_comments_list', 'comments.tpl');
$template->copy_block('BLOCK_user_comments_list', 'FILE_user_comments_list');

$template->set_array($login_data, 'FILE_user_comments');

if(user_loged() && $login_data)
{
	# Comments
	$params = array(
		'login_id'=>$login_data['l_id'],
		'limit'=>100,
		'order'=>"c_entered DESC",
		);

	$RC = new ResComment();
	$comments = $RC->Get($params);
	include('module/comment/list.inc.php');
} else {
	# TODO: unificēt
	$template->enable('BLOCK_not_loged');
}

$template->set_right();
$template->set_events();
$template->set_recent_forum();
$template->set_login();
$template->set_online();
$template->set_search();
$template->set_jubilars();

$template->out();

