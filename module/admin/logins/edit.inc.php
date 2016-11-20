<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/ResComment.php');

# Comment actions
if(in_array($action, array('comment_delete', 'comment_show', 'comment_hide')))
{
	if(include('module/admin/comment/action.inc.php')){
		header("Location: ".($l_id ? "$module_root/$l_id/" : "$module_root"));
	}
	return;
}

$template = new AdminModule($sys_template_root.'/admin', "logins/edit");
$template->set_title('Admin :: logini :: rediģēt');

$Logins = new Logins();
$login = $Logins->load(array(
	'l_id'=>$l_id,
	'l_active'=>LOGIN_ALL,
	'l_accepted'=>LOGIN_ALL,
	'get_all_ips'=>true,
	));

$template->set_array($login, 'BLOCK_login_edit');

$YN = array(
	'l_active',
	'l_accepted',
	'l_emailvisible',
	'l_logedin',
	);

foreach($YN as $k)
{
	$v = sprintf("%s_%s_sel", $k, $login[$k]);
	$template->set_var($v, ' selected="selected"', 'BLOCK_login_edit');
}
$template->set_var('all_ips_view', str_replace(",", ", ", $login['all_ips']), 'BLOCK_login_edit');

# User comments
$template->set_file('FILE_comment_list', 'comment/list.tpl');
$template->copy_block('BLOCK_login_comments', 'FILE_comment_list');

$RC = new ResComment();
$RC->setDb($db);
$comments = $RC->get(array(
	'login_id'=>$l_id,
	'c_visible'=>Comment::ALL,
	'order'=>'c_entered DESC',
	'limit'=>500,
	));

# Šmurguļi, kas nāk no vairākā IP un reklamē :E (piemēram, HeavenGrey)
$alsoUsers = Logins::collectUsersByIP(explode(",", $login['all_ips']), $l_id);

if($alsoUsers)
{
	$template->enable('BLOCK_logins_also');
	foreach($alsoUsers as $item)
	{
		$template->set_array($item, 'BLOCK_logins_also_list');
		$template->set_var('l_color_class', 'box-normal', 'BLOCK_logins_also_list');
		if($item['l_active'] != LOGIN_ACTIVE)
			$template->set_var('l_color_class', 'box-inactive', 'BLOCK_logins_also_list');
		$template->parse_block('BLOCK_logins_also_list', TMPL_APPEND);
	}
}

# Bildes
$pic_suffixes = array();

# Main pic
if(file_exists("$sys_user_root/pic/thumb/$l_id.jpg")){
	$pic_suffixes = array('');
}

# Pic history
$files = scandir("$sys_user_root/pic/thumb", 1);
foreach($files as $k=>$v){
	if(preg_match("/^$l_id(-\d+).jpg\$/", $v, $m)){
		$pic_suffixes[] = $m[1];
	}
}

if($pic_suffixes)
{
	$template->enable('BLOCK_logins_pics');
	foreach($pic_suffixes as $suffix){
		$template->set_var('l_pic_suffix', $suffix, 'BLOCK_logins_pics_item');
		$template->parse_block('BLOCK_logins_pics_item', TMPL_APPEND);
	}
}


include('module/admin/comment/list.inc.php');

$template->out();

