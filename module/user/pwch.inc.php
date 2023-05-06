<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$template = new MainModule($sys_module_id);
$template->set_title('Paroles maiņa');
$template->set_file('FILE_module', 'user/pwch.tpl');
$template->copy_block('BLOCK_middle', 'FILE_module');

if(!user_loged())
{
	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
	$template->enable('BLOCK_not_loged');
	$template->set_right_defaults();
	$template->out();
	return;
}

$template->enable('BLOCK_pwch_form');

if(isset($_POST['data']))
{
	$data = $_POST['data'];
	$error_fields = $error_msgs = [];
	$logins = new Logins;

	if(empty($data['old_password'])){
		$error_msgs[] = 'Nav ievadīta vecā parole';
		$error_fields[] = 'old_password';
	} else {
		if(Logins::auth($_SESSION['login']['l_login'], $_POST['data']['old_password'])){
			if(!pw_validate($data['l_password'], $data['l_password2'], $error_msgs)){
				$error_fields[] = 'l_password';
			}
		} else {
			$error_msgs[] = 'Vecā parole nav pareiza';
			$error_fields[] = 'old_password';
		}
	}

	foreach($error_fields as $k){
		$template->set_var('error_'.$k, ' class="error-form"', 'BLOCK_pwch_form');
	}

	$template->set_array($data, 'BLOCK_pwch_form');

	if(!$error_msgs){
		if($logins->update_password($_SESSION['login']['l_login'], $data['l_password'])){
			$template->disable('BLOCK_pwch_form');
			$template->enable('BLOCK_pwch_ok');
		} else {
			$error_msgs[] = "Datubāzes kļūda";
		}
	}

	if($error_msgs){
		$template->enable('BLOCK_pwch_error');
		$template->set_var('error_msg', join('<br/>', $error_msgs), 'BLOCK_pwch_error');
	}
}

$template->set_right_defaults();
$template->out();
