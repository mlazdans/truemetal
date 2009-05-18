<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('../classes/class.Permission.php');

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: faili');

$perm = new Permission;
$permissions = $perm->user_permissions($_USER['user_login'], 'upload');

if(!in_array('admin', $permissions))
	$template->disable('BLOCK_browse');

if(
	!in_array('admin', $permissions) &&
	!in_array('write', $permissions) &&
	!in_array('read', $permissions)
) {
	$template->enable('BLOCK_msg');
	$template->set_var('msg', ACCESS_DENIED);
	$template->out();
	exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$hide = isset($_POST['hide']) ? $_POST['hide'] : '';

if($action == 'upload') {
	if(!in_array('admin', $permissions)) {
		$template->enable('BLOCK_msg');
		$template->set_var('msg', ACCESS_DENIED);
		$template->out();
		exit;
	}
	$template->enable('BLOCK_msg');
	$some_file = isset($_FILES['some_file']) ? $_FILES['some_file'] : array();
	if(!isset($some_file['name'])) {
		$template->set_var('msg', 'Kļūdains parametrs!');
	} elseif(!$some_file['name']) {
		$template->set_var('msg', 'Fails nav izvēlēts!');
	} elseif(move_uploaded_file($some_file['tmp_name'], $sys_upload_root.'/'.$some_file['name'])) {
		$path = $sys_upload_root.'/'.$some_file['name'];
		// resize
		$image_size = isset($_POST['image_size']) ? (integer)$_POST['image_size'] : 0;
		$data = getimagesize($path);
		if($data[0] > $image_size && $image_size) {
			$type = image_load($in_img, $path);
			$out_img = image_resample($in_img, $image_size);
			image_save($out_img, $path, $type);
		}
		// end resize
		$template->set_var('msg', 'Fails <b>'.$some_file['name'].'</b> uzkopēts!');
	} else {
		$template->set_var('msg', 'Neizdevās uzkopēt failu <b>'.$some_file['name'].'</b>!');
	}
	if($hide) {
		if(isset($_SERVER['HTTP_REFERER']))
			header('Location: '.$_SERVER['HTTP_REFERER']);
		else
			$template->enable('BLOCK_back');
	}
}

if($action == 'delete_multiple') {
	$template->enable('BLOCK_msg');
	$file_count = isset($_POST['file_count']) ? (integer)$_POST['file_count'] : 0;
	for($c = 1; $c <= $_POST['file_count']; ++$c) {
		$file = isset($_POST['file_'.$c]) ? $_POST['file_'.$c] : '';
		$file = preg_replace('/(..\/)/', '', $file);
		$path = $sys_upload_root.'/'.$file;
		if($file) {
			if(file_exists($path)) {
				if(unlink($path))
					$template->set_var('msg', 'Izdzēsts fails <b>'.$file.'</b>');
				else
					$template->set_var('msg', 'Nevar izdzēst failus <b>'.$file.'</b>');
			} else
				$template->set_var('msg', 'Fails <b>'.$file.'</b> neekistē');
			$template->parse_block('BLOCK_msg', TMPL_APPEND);
		} // file
	} // loop
}

// ielaadeejam failu sarakstu un uztaisa sarakstu sakaartotaa seciibaa 
$files = array();
if($dir = @opendir($sys_upload_root)) {
	while(false !== ($file = readdir($dir)))
		if ($file != "." && $file != "..")
			$files[] = $file;

	if(count($files))
		$template->enable('BLOCK_file_list');

	natcasesort($files);
	$c = 0;
	foreach($files as $file) {
		++$c;
		$template->set_var('nr', $c);
		$template->set_var('file_name', $file);
		$template->parse_block('BLOCK_file', TMPL_APPEND);
	}
} else
	print '<b>$sys_upload_root</b> definēto direktoriju nevar atvērt!'." [$sys_upload_root]";

$template->out();

?>