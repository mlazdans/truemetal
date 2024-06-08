<?php declare(strict_types = 1);

$hl = get("hl");
$l_hash = array_shift($sys_parameters)??"";

$template = new MainTemplate();
$template->set_right_defaults();
$template->MiddleBlock = user_comments($template, $l_hash, $hl);
$template->print();
