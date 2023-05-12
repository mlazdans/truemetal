<?php declare(strict_types = 1);

# Comment actions
if(in_array($action, array('comment_delete', 'comment_show', 'comment_hide')))
{
	if(include('module/admin/comment/action.inc.php')){
		header("Location: ".($l_id ? "$module_root/$l_id/" : "$module_root"));
	}
	return;
}

$template = new AdminModule("logins");
$template->set_title('Admin :: logini :: rediģēt');

$T = $template->add_file("admin/logins/edit.tpl");

$Logins = new Logins();
$login = $Logins->load(array(
	'l_id'=>$l_id,
	'l_active'=>Res::STATE_ALL,
	'l_accepted'=>Res::STATE_ALL,
	'get_all_ips'=>true,
));

$all_ips = $login['all_ips']??"";

$T->set_array($login, 'BLOCK_login_edit');

$YN = array(
	'l_active',
	'l_accepted',
	'l_emailvisible',
	'l_logedin',
);

foreach($YN as $k)
{
	$v = sprintf("%s_%s_sel", $k, $login[$k]);
	$T->set_var($v, ' selected="selected"', 'BLOCK_login_edit');
}
$T->set_var('all_ips_view', str_replace(",", ", ", $all_ips), 'BLOCK_login_edit');

# User comments
$C = new_template("admin/comment/list.tpl");

$comments = (new ResComment)->get([
	'login_id'=>$l_id,
	'c_visible'=>Res::STATE_ALL,
	'order'=>'c_entered DESC',
	'limit'=>500,
]);

# Šmurguļi, kas nāk no vairākā IP un reklamē :E (piemēram, HeavenGrey)
$alsoUsers = Logins::collectUsersByIP(explode(",", $all_ips), $l_id);

if($alsoUsers)
{
	$T->enable('BLOCK_logins_also');
	foreach($alsoUsers as $item)
	{
		$T->set_array($item, 'BLOCK_logins_also_list');
		$T->set_var('l_color_class', 'box-normal', 'BLOCK_logins_also_list');
		if($item['l_active'] != Res::STATE_ACTIVE)
			$T->set_var('l_color_class', 'box-inactive', 'BLOCK_logins_also_list');
		$T->parse_block('BLOCK_logins_also_list', TMPL_APPEND);
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
	$T->enable('BLOCK_logins_pics');
	foreach($pic_suffixes as $suffix){
		$T->set_var('l_pic_suffix', $suffix, 'BLOCK_logins_pics_item');
		$T->parse_block('BLOCK_logins_pics_item', TMPL_APPEND);
	}
}

admin_comment_list($C, $comments);
$T->set_block_string($C->parse(), 'BLOCK_login_comments');

$template->out($T);

