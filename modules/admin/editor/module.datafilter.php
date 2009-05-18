<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

$template = new Template($sys_template_root.'/admin/editor');
$template->set_file("FILE_index", 'tmpl.datafilter.php');

$template->set_var('encoding', $sys_encoding);
$template->set_var('http_root', $sys_http_root);
$template->set_var('REMOVE_TABLE', REMOVE_TABLE);
$template->set_var('REMOVE_FONT', REMOVE_FONT);

print $template->parse_file('FILE_index');
?>