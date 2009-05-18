<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

$lang = array_shift($sys_parameters);
$qs = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

$c = count(split('/', $sys_http_root));

if($qs) {
	$qs = substr($qs, strpos($qs, '//') + 2);
	$parts = split('/', $qs);
	array_splice($parts, 0, $c - 1);
	$parts[0] = $lang;
	$new_qs = join('/', $parts);
}

if(in_array($lang, $sys_languages))
	if(isset($new_qs))
		header("Location: $sys_http_root_base/$new_qs");
	else
		header("Location: $sys_http_root_base/$lang/admin/");
else
	header("Location: $admin_root/");
exit;
?>