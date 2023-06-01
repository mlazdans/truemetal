<?php declare(strict_types = 1);

$template = new MainModule($sys_module_id);
$template->set_title("Arhīvs: visi notikumi līdz šim");
$template->set_descr("Metāliskais arhīvs");
$template->set_right_defaults();
$template->out(archive($template));

