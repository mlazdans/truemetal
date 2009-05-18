<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// ----------------------------------------------------------------------------
require_once('../classes/class.MainModule.php');
require_once('../classes/class.Poll.php');

$action = array_shift($sys_parameters);

$poll = new Poll;

if($action == 'vote')
{
	$poll_id = isset($_POST['poll_id']) ? (integer)$_POST['poll_id'] : 0;
	$poll_pollid = isset($_POST['poll_pollid']) ? (integer)$_POST['poll_pollid'] : 0;
	if($poll_id && $poll_pollid && user_loged())
	{
		if($poll->vote($poll_id, $poll_pollid))
		{
			header("Location: $sys_http_root/");
			exit;
		} else {
			$template = new MainModule($sys_template_root, $sys_module_id);
			$template->set_title('Balsošanas kļūda!');
			$template->set_file('FILE_poll', 'tmpl.poll.php');
			$template->copy_block('BLOCK_middle', 'FILE_poll');
			//die($poll->error_msg);
			$template->disable('BLOCK_poll');
			$template->enable('BLOCK_poll_error');
			$template->set_var('error_msg', $poll->error_msg);
			$template->out();
			exit;
		}
	} else {
		header("Location: $sys_http_root/");
		exit;
	}
}

if($action == 'results')
{
	$id = (int)array_shift($sys_parameters);
	$path = array('map'=>array('module_id'=>'poll/results', 'module_name'=>'REZULTĀTI'));

	$template = new MainModule($sys_template_root, $sys_module_id);
	//$template->set_file('FILE_poll_results', 'tmpl.poll_results2.php');
	//$template->copy_block('BLOCK_middle', 'FILE_poll_results');
	$poll->show_results2($template, $id);

	$template->set_right();
	$template->set_reviews();
	$template->set_poll();
	$template->set_online();
	$template->set_calendar();

	$template->out();
}

?>