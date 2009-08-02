<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

function poll_error($msg, &$template) {
	$template->enable('BLOCK_poll_error');
	$template->set_var('error_msg', $msg);
}

function set_poll(&$template, &$item) {
	if(!$item['poll_name'])
		$item['poll_name'] = '-nezināms-';
	$item['poll_name'] = strip_tags($item['poll_name']);

	$template->set_array($item);

	// ja aktiivs vai nee - kaukaa iekraaso to
	$template->disable('BLOCK_poll_active');
	$template->disable('BLOCK_poll_inactive');
	if($item['poll_active'] == POLL_ACTIVE) {
		$template->enable('BLOCK_poll_active');
		$template->set_var('poll_color_class', 'box-normal');
	} else {
		$template->enable('BLOCK_poll_inactive');
		$template->set_var('poll_color_class', 'box-invisible');
	}
}

require_once('lib/AdminModule.php');
require_once('lib/Poll.php');

$poll_id = (integer)array_shift($sys_parameters);
$action = isset($_POST['action']) ? $_POST['action'] : '';

$template = new AdminModule($sys_template_root.'/admin', 'poll');
$template->set_var('poll_class', 'TD-menu-active');
$template->set_title('Admin :: jautājumi');

$poll = new Poll;
if($poll->error_msg) {
	poll_error($poll->error_msg, $template);
	$template->out();
	exit;
}


if(in_array($action, array('delete_multiple', 'activate_multiple', 'deactivate_multiple'))) {
	if($poll->process_action($_POST, $action)) {
		if($poll_id)
			header("Location: $module_root/$poll_id/");
		else
			header("Location: $module_root/");
	} else {
		poll_error($poll->error_msg, $template);
		$template->out();
	}
	exit;
}

if($action == 'add_poll') {
	if(isset($_POST['data'])) {
		if(!$poll_id)	// ja jautaajums
			$_POST['data']['poll_active'] = 'N';
		if($newpoll_id = $poll->insert($poll_id, $_POST['data'])) {
			if($poll_id)
				header("Location: $module_root/$poll_id/");
			else
				header("Location: $module_root/$newpoll_id/");
		} else {
			$template->set_file('FILE_poll', 'tmpl.poll.php');
			$template->copy_block('BLOCK_middle', 'FILE_poll');
			poll_error($poll->error_msg, $template);
			$template->out();
		}
	}
	exit;
}

if($action == 'save_poll') {
	if(isset($_POST['data'])) {
		if($poll_id = $poll->update($poll_id, $_POST['data'])) {
			if($poll_id)
				header("Location: $module_root/$poll_id/");
			else
				header("Location: $module_root/");
		} else {
			$template->set_file('FILE_poll', 'tmpl.poll.php');
			$template->copy_block('BLOCK_middle', 'FILE_poll');
			poll_error($poll->error_msg, $template);
			$template->out();
		}
	}
	exit;
}

// ja redigeejam
if($poll_id) {

	$template->set_file('FILE_poll', 'tmpl.poll_edit.php');
	$template->copy_block('BLOCK_middle', 'FILE_poll');

	// jautaajums
	$poll_data = $poll->load($poll_id, 0, POLL_ALL);
	$poll1_name_stripped = strip_tags($poll_data['poll_name']);
	parse_form_data_array($poll_data);

	// jauna atbilde, ja redigee jautaajumu
	if(!$poll_data['poll_pollid']) {
		$template->set_file('FILE_pollnew', 'tmpl.poll_new.php');
		$template->copy_block('BLOCK_pollnew', 'FILE_pollnew');
		$template->set_var('poll_new_name', 'Jauna atbilde');
	}

	$template->set_var('poll1_id', $poll_data['poll_id']);
	$template->set_var('poll1_name', $poll_data['poll_name'] ? $poll_data['poll_name'] : '---');
	$template->set_var('poll1_name_stripped', $poll1_name_stripped ? $poll1_name_stripped : '---');
	$template->set_var('poll1_entered', $poll_data['poll_entered']);

	if($poll_data['poll_active'] == POLL_ACTIVE)
		$template->set_var('poll1_active', ' selected="selected"');
	else
		$template->set_var('poll1_inactive', ' selected="selected"');

	// atbildes
	$poll_data = $poll->load(0, $poll_id, POLL_ALL);

	if(count($poll_data)) {
		$template->set_file('FILE_polldets', 'tmpl.poll_det.php');
		$template->copy_block('BLOCK_polldets', 'FILE_polldets');
		$template->enable('BLOCK_poll');
	}

	$c = 0;
	foreach($poll_data as $item) {
		++$c;
		$template->set_var('poll_nr', $c);
		set_poll($template, $item);
		$template->parse_block('BLOCK_poll', TMPL_APPEND);
	}

	$template->set_var('item_count', $c);
 // ja pollu sarakstu
} else {
	$poll_data = $poll->load(0, 0, POLL_ALL);

	$template->set_file('FILE_poll', 'tmpl.poll.php');
	$template->copy_block('BLOCK_middle', 'FILE_poll');

	if(count($poll_data))
		$template->enable('BLOCK_poll');

	$c = 0;
	foreach($poll_data as $item) {
		++$c;
		$template->set_var('poll_nr', $c);
		set_poll($template, $item);
		$template->parse_block('BLOCK_poll', TMPL_APPEND);
	}
	$template->set_var('item_count', $c);

	// jauns jautaajums
	$template->set_file('FILE_pollnew', 'tmpl.poll_new.php');
	$template->copy_block('BLOCK_pollnew', 'FILE_pollnew');
	$template->set_var('poll_new_name', 'Jauns jautājums');
} // poll_id



$template->out();
