<?php declare(strict_types = 1);

use dqdp\DBA\Types\None;

spl_autoload_register();

# TODO: mainot galerijām/foruma/commention utt login_id, trigerī nomainās arī res tabulā
# TODO: mainot paroli chrome piedāvā ieseivot arī pie fail

# DEFAULTS - var overraidot configā
$sys_start_time        = microtime(true);
$sys_root              = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..');
$sys_public_root       = $sys_root.DIRECTORY_SEPARATOR.'public';
$sys_template_root     = $sys_root.DIRECTORY_SEPARATOR.'templates';
$sys_user_root         = $sys_root.DIRECTORY_SEPARATOR.'users';
$sys_upload_root       = $sys_public_root.DIRECTORY_SEPARATOR.'data';
$sys_upload_http_root  = '/data';

$sys_error_reporting   = E_ALL;
$sys_default_lang      = 'lv';
$sys_domain            = (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
$sys_script_version    = 1;
$sys_banned            = [];
$sys_devs              = [];
$sys_module_map        = [];
$sys_include_paths     = [];
$sys_no_sess_modules   = ['css', 'jsload'];
$sys_no_db_modules     = ['css', 'jsload'];
$sys_mail              = $_SERVER['SERVER_ADMIN']??ini_get('sendmail_from') or ($sys_mail = 'nobody@localhost');
$ip                    = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');

# Config
require_once($sys_root.'/include/config.php');

$sys_debug = (php_sapi_name() == 'cli') || in_array($ip, $sys_devs);
$DQDP_DEBUG = $sys_debug;

ini_set('display_errors', (bool)$sys_debug);
ini_set('expose_php', (bool)$sys_debug);
error_reporting($sys_error_reporting);

# Include paths
$include_path = array_unique(array_merge($sys_include_paths, explode(PATH_SEPARATOR, ini_get('include_path'))));
ini_set('include_path', join(PATH_SEPARATOR, $include_path));

mb_regex_encoding('utf-8');
mb_internal_encoding('utf-8');

const nil = new None;

require_once('stdlib.php');
require_once('lib/truelib.php');
require_once('lib/utils.php');
