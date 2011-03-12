<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$module = array_shift($sys_parameters);
$action = array_shift($sys_parameters);

if($module == 'votes' && $action == 'view')
{
	$template = new MainModule($sys_template_root, 'profile', 'admin/comment/votes/view.json.tpl');
	$res_id = (int)array_shift($sys_parameters);
	$sql = "
SELECT
	rv.*,
	l.*
FROM
	`res_vote` rv
JOIN `logins` l ON l.l_id = rv.login_id
WHERE
	rv.res_id = $res_id
";
	if($data = $db->Execute($sql))
		$template->enable('BLOCK_votes');

	foreach($data as $item)
	{
		$item['rv_color'] = ($item['rv_value'] > 0 ? "green" : "red");
		$template->set_array($item, 'BLOCK_votes');
		$template->parse_block('BLOCK_votes', TMPL_APPEND);
	}

	ob_start();
	$template->out();
	$html = ob_get_clean();

	$jsonData = new StdClass;
	$jsonData->title = "[ TRUEMETAL ".$template->get_title()." ]";
	$jsonData->html = $html;
	header('Content-Type: text/javascript; charset='.$sys_encoding);
	print json_encode($jsonData);
}
