<?php declare(strict_types = 1);

$f = (require '../vendor/autoload.php');
$f->addPsr4("", "/www/dev.truemetal.lv/lib");
$f->addPsr4("dqdp\\", "/www/dqdp8/dqdp");

$sys_script_version = 1;

define('FORUM_MAXWORDSIZE', 36);
define('FORUM_MAXWORDS', 1600);
define('FORUM_MAXLINES', 50);

$sys_admins = [ '127.0.0.1' ];
$sys_include_paths = [
	".",
	$sys_root,
	$sys_root.DIRECTORY_SEPARATOR.'lib',
];
$sys_mail = 'info@truemetal.lv';
$sys_domain = 'truemetal.lv';
$user_pic_w = 500;
$user_pic_h = 375;
$user_pic_tw = 120;
$user_pic_th = 90;
$sys_database_type = 3;
$sys_db_host = 'localhost';
$sys_db_port = 3306;
$sys_db_name = 'truemetal';
$sys_db_user = 'root';
$sys_db_password = '';
$sys_mail_params = [
	'driver'=>'smtp',
	'host'=>'mail.dqdp.net',
	'port'=>587,
	'username'=>'info@truemetal.lv',
	'password'=>'',
	'auth'=>true,
];
$sys_default_module = 'article';

$i_am_admin = (php_sapi_name() == 'cli') || in_array($ip, $sys_admins);
$sys_debug = ($i_am_admin ? true : false);

$top_banners = [
	/*
	array(
		'img'=>'banner_lemess2017.gif',
		'alt'=>'ZOBENS UN LEMESS 2017',
		'href'=>'/forum/124488-zobens-un-lemess-2017',
		'width'=>187,
		'height'=>121,
	),
	*/
];

$sys_css = [ 'styles-dynamic', 'article', 'jquery-ui' ];
$sys_js = [ 'jquery', 'jquery-ui', 'truemetal' ];
