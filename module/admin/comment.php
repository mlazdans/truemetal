<?php declare(strict_types = 1);

$module = array_shift($sys_parameters);
$action = array_shift($sys_parameters);
$res_id = (int)array_shift($sys_parameters);

$template = new AdminModule('comment');

if($module == 'votes' && $action == 'view')
{
	$T = $template->add_file('admin/comment/votes/view.json.tpl');

	if($data = DB::Execute("SELECT rv.*, l.* FROM `res_vote` rv LEFT JOIN `logins` l ON l.l_id = rv.login_id WHERE rv.res_id = $res_id"))
	{
		$BLOCK_votes = $T->enable('BLOCK_votes');
		foreach($data as $item)
		{
			$item['rv_color'] = ($item['rv_value'] > 0 ? "green" : "red");
			$BLOCK_votes->set_array($item);
			$BLOCK_votes->parse(TMPL_APPEND);
		}
	}
}

if($module == 'original' && $action == 'view')
{
	$T = $template->add_file('admin/comment/original/view.json.tpl');

	$data = ViewResCommentEntity::getByResId($res_id, true);

	if($data){
		$T->set_array($data);
	}
}

$template->out($T??null);
