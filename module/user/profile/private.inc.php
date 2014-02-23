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
	//$login = new Logins;
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
#if($i_am_admin){
	$template->enable('BLOCK_truecomments');

	$res = array();
	$ids = array();
	for($r=0; $r<2; $r++)
	{
		if($r == 0){
			$v = 1;
		} elseif($r == 1){
			$v = -1;
		} else {
			$v = ''; // To make query fail. Never should though.
		}

		$sql = "
		SELECT
			c.*,
			COUNT(*) sk
		FROM
			comment c
		JOIN comment_votes cv ON cv.cv_c_id = c.c_id
		WHERE
			c.c_userid = {$_SESSION['login']['l_id']} AND
			cv.cv_value = $v
		GROUP BY
			c.c_id
		ORDER BY
			sk DESC
		LIMIT 3
		";

		$res[$r] = $db->Execute($sql);

		foreach($res[$r] as $i){
			$ids[] = $i['c_id'];
		}
	}

	$sql = "
	SELECT
		c.*,
		cc_table,
		cc_table_id,
		(SELECT COUNT(*) FROM comment_votes WHERE cv_c_id = c_id AND cv_value = 1) AS plus_count,
		(SELECT COUNT(*) FROM comment_votes WHERE cv_c_id = c_id AND cv_value = -1) AS minus_count
	FROM
		comment c
	JOIN comment_connect ON cc_c_id = c_id
	WHERE
		c.c_id IN (".join(",", $ids).")
	";

	$data = array();

	$q = $db->Query($sql);
	while($item = $db->FetchAssoc($q)){
		$data[$item['c_id']] = $item;
	}

	# Lai būtu sasortēts kā vajag
	foreach($res as $r=>$section_items){
		foreach($section_items as $item){
			$item_data = $data[$item['c_id']];
			$plus_count = $item_data['plus_count'];
			$minus_count = $item_data['minus_count'];
			$c_data = $item_data['c_data'];
			if(mb_strlen($c_data) > 70){
				$c_data = mb_substr($c_data, 0, 70).'...';
			}

			$c_href = "/{$item_data['cc_table']}/{$item_data['cc_table_id']}/#comment{$item_data['c_id']}";

			$template->set_var('minus_count', $minus_count, 'BLOCK_truecomment_item');
			$template->set_var('plus_count', $plus_count, 'BLOCK_truecomment_item');
			$template->set_var('c_data', $c_data, 'BLOCK_truecomment_item');
			$template->set_var('c_href', $c_href, 'BLOCK_truecomment_item');
			$template->parse_block('BLOCK_truecomment_item', TMPL_APPEND);
		}
	}
#}

//$template->enable('BLOCK_picture_delete');
//$template->enable('BLOCK_private_profile');


