<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// config fails
define('FORUM_MAXWORDSIZE', 26);
define('FORUM_MAXWORDS', 400);
define('FORUM_MAXLINES', 50);
define('TMP_DIR', ini_get('upload_tmp_dir'));
define('ACCESS_DENIED', 'Nav pieejas!');
define('ARTICLE_TO_SHOW', 15);

$sys_admins = array(
	'89.111.13.45',
);

$bobijs = array(
	517
);

/* some predefines */
$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'undefined';
$i_am_admin = isset($i_am_admin) ? $i_am_admin : in_array($ip, $sys_admins);
//$i_am_admin = true;
$now = date("d.m.Y, H:i", time());
$today = date("d.m.Y");

$sys_languages = array(
	'lv'
);

$mail_from = 'info@truemetal.lv';
$new_login_mail = 'info@truemetal.lv';

$sys_default_lang = 'lv';
$sys_encoding = 'utf-8';

$sys_error_reporting = E_ALL;
if($i_am_admin)
{
	ini_set('display_errors', 1);
	$sys_debug = 1;
} else {
	ini_set('display_errors', 0);
	$sys_debug = 0;
}
error_reporting($sys_error_reporting);

$sys_domain = 'truemetal.lv';

$sys_root = '/www/truemetal.lv/public';

$sys_http_root = '';
$sys_template_root = '/www/truemetal.lv/templates';

$sys_upload_root = '/www/truemetal.lv/public/data';
$sys_upload_http_root = '/data';

$sys_user_root = '/www/truemetal.lv/public/users';
$sys_user_http_root = '/users';

$user_pic_w = 500;
$user_pic_h = 375;
$user_pic_tw = 120;
$user_pic_th = 90;

$sys_database_type = 3;
$sys_db_host = 'localhost';
$sys_db_name = 'truemetal';
$sys_db_user = 'truemetal';
$sys_db_password = 'Truu2Nigga5WoopBitch16';

$sys_use_compression = 0;
$sys_default_module = 'article';

/* modulis, kas atveras saakumaa (ja nav noraadiits neviens) */
$sys_first_module = 'news';

/* memcache */
$sys_use_chache = false;
$memcache_host = 'localhost';
$memcache_port = 11211;
