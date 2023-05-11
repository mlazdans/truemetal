<?php declare(strict_types = 1);

$hl = rawurldecode(get("hl"));
$l_hash = array_shift($sys_parameters)??"";

$template = new MainModule($sys_module_id);
$template->set_right_defaults();
$template->out(user_comments($template, $l_hash, $hl));

