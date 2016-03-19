<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$module_root = "$module_root/profile";

if(!user_loged())
{
	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
	$template->enable('BLOCK_not_loged');
	return;
}

# del image
if($section == 'deleteimage')
{
	if(Logins::delete_image())
	{
		header("Location: $module_root/");
		return;
	} else {
		$template->enable('BLOCK_profile_error');
		$template->set_var('error_msg', 'Bildi neizdevās izdzēst!');
	}
}

// save
if(isset($_POST['data']))
{
	$login = new Logins;
	$login_data = $_POST['data'];

	if($data = $login->update_profile($login_data))
	{
		unset($data['l_sessiondata']);
		$_SESSION['login'] = $data;
		header("Location: $module_root/");
		return;
	} else {
		$template->enable('BLOCK_profile_error');
		$template->set_var('error_msg', $login->error_msg);
	}
	$login_data = array_merge($_SESSION['login'], $login_data);
} else {
	$login_data = $_SESSION['login'];
} // post

$set_vars = array(
	'user_pic_w'=>$user_pic_w,
	'user_pic_h'=>$user_pic_h,
	'user_pic_tw'=>$user_pic_tw,
	'user_pic_th'=>$user_pic_th
);
$template->set_array($set_vars);
$template->set_profile($login_data);

# Comment stats
$template->enable('BLOCK_truecomments');

$ids = array();
$data = array();
for($r=0; $r<2; $r++)
{
	if($r == 0){
		$v = 'r.res_votes_plus_count DESC';
	} elseif($r == 1){
		$v = 'r.res_votes_minus_count DESC';
	}

	$sql = "
	SELECT
		r.res_id
	FROM
		res r
	WHERE
		r.login_id = {$_SESSION['login']['l_id']}
	ORDER BY
		$v
	LIMIT 3
	";

	$ids[] = $db->Execute($sql);
}

$i=array();
foreach($ids as $id){
	foreach($id as $k=>$v){
		$i[] = $v['res_id'];
	}
}

$sql = "
SELECT
	c.*,
	rc.res_id AS parent_res_id,
	r.res_votes_plus_count AS plus_count,
	r.res_votes_minus_count AS minus_count
FROM
	comment c
JOIN res r ON r.res_id = c.res_id
JOIN res_comment rc ON rc.c_id = c.c_id
WHERE
	r.res_id IN (".join($i, ',').")
ORDER BY
	res_votes_plus_count DESC, res_votes_minus_count DESC
";

$data = $db->Execute($sql);
foreach($data as $item)
{
	$plus_count = $item['plus_count'];
	$minus_count = $item['minus_count'];
	$c_data = $item['c_data'];
	if(mb_strlen($c_data) > 70){
		$c_data = mb_substr($c_data, 0, 70).'...';
	}

	$c_href = "/resroute/{$item['parent_res_id']}/?c_id={$item['c_id']}";

	$template->set_var('minus_count', $minus_count, 'BLOCK_truecomment_item');
	$template->set_var('plus_count', $plus_count, 'BLOCK_truecomment_item');
	$template->set_var('c_data', $c_data, 'BLOCK_truecomment_item');
	$template->set_var('c_href', $c_href, 'BLOCK_truecomment_item');
	$template->parse_block('BLOCK_truecomment_item', TMPL_APPEND);
}

