<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Logins.php');
require_once('lib/MainModule.php');

$action = array_shift($sys_parameters);

$template = new MainModule($sys_template_root, $sys_module_id, 'tmpl.index.php');
$template->set_title("Aizmirsu paroli");
$template->set_file('FILE_forgot', 'tmpl.forgot.php');
$template->copy_block('BLOCK_middle', 'FILE_forgot');

$template->set_right();
$template->set_login();
$template->set_reviews();
$template->set_poll();
$template->set_search();
$template->set_online();

$logins = new Logins;

$empty_data = array(
	'l_login'=>'',
	'l_email'=>'',
);

$data = $empty_data;
$error_msg = array();

if($action == 'request')
{
	include("module/forgot/request.inc.php");
} elseif($action == 'accept') {
	include("module/forgot/accept.inc.php");
} else {
	$template->enable('BLOCK_forgot_form');
}

if($error_msg)
{
	$template->enable('BLOCK_forgot_error');
	$template->set_var('error_msg', join("<br/>", $error_msg), 'BLOCK_forgot_error');
}

parse_form_data_array($data);
$template->set_array($data, 'BLOCK_forgot_form');

$template->out();

