<?php

header($_SERVER["SERVER_PROTOCOL"]." 503 Temporary unavailable");
header("Retry-After: 86400"); // 1 day

$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_file('FILE_gallery', 'gallery.tpl');
$template->copy_block('BLOCK_middle', 'FILE_gallery');

$template->set_title("Galerijas");

$template->set_right();
$template->set_login();
$template->set_online();
$template->set_recent_comments();
$template->set_search();
$template->set_recent_reviews();
$template->out();

