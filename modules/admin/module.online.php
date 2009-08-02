<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: online');
$template->set_file('FILE_online', 'tmpl.online.php');

$active_sessions = $sess_handler->get_active();

$user_count = 0;
$template->enable('BLOCK_online_item');
foreach($active_sessions as $item) {
	$data = $sess_handler->get_vars($item['sess_data']);
	++$user_count;

	if(isset($data['user'])) {
		if(!$data['user']['username'])
			$data['user']['username'] = '-nezināms-';
	} else
		$data['user']['username'] = '-nezināms-';

	$template->set_var('online_name', $data['user']['username']);
	$template->set_var('online_ip', $item['sess_ip']);
	$template->parse_block('BLOCK_online_item', TMPL_APPEND);
}

if(!$user_count) {
	$template->disable('BLOCK_online_item');
	$template->enable('BLOCK_noonlines');
}

$template->parse_block('FILE_online');
$template->set_var('right_item_data', $template->get_parsed_content('FILE_online'));
$template->parse_block('BLOCK_right_item', TMPL_APPEND);

$template->out();

?>