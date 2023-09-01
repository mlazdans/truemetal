<?php declare(strict_types = 1);

$template = new AdminModule($admin_module);
$template->set_title('Admin :: reporti');
$T = $template->add_file("admin/reports.tpl");

$report = postget('report');
$action = post('action');

if(in_array($action, array('comment_delete', 'comment_show', 'comment_hide')))
{
	if(include('module/admin/comment/action.inc.php')){
		# TODO: pazūd ievadītās IP
		redirect();
	}
	return;
}

if($report == 'ip')
{
	$data = postget('ips', array());
	if($ips = array_filter(preg_split("/[\s,]/", $data), 'is_not_empty'))
	{
		$C = new_template("admin/comment/list.tpl");
		$T->set_var('ips', $data);

		$CF = (new ResCommentFilter(
			ips:$ips,
			res_visible:false,
		))->rows(1000)->orderBy("res_entered DESC");

		admin_comment_list($C, (new ViewResCommentEntity)->getAll($CF));
		$T->set_block_string($C->parse(), 'BLOCK_report_comments');
	}
}

$template->out($T??null);
