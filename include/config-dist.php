<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// config fails
define('FORUM_MAXWORDSIZE', 26);
define('FORUM_MAXWORDS', 800);
define('FORUM_MAXLINES', 50);

$sys_admins = array(
	'127.0.0.1',
	'89.111.13.45',
);

$sys_mail_from = 'info@truemetal.lv';
$new_login_mail = 'info@truemetal.lv';

$sys_domain = 'truemetal.lv';

$user_pic_w = 500;
$user_pic_h = 375;
$user_pic_tw = 120;
$user_pic_th = 90;

$sys_database_type = 3;
$sys_db_host = 'localhost';
$sys_db_port = 53306;
$sys_db_name = 'truemetal';
$sys_db_user = 'root';
$sys_db_password = '';

/* modulis, kas atveras saakumaa (ja nav noraadiits neviens) */
$sys_default_module = 'article';

$i_am_admin = in_array($ip, $sys_admins);
$sys_debug = ($i_am_admin ? true : false);

