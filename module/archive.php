<?php declare(strict_types = 1);

$template = new MainTemplate();
$template->set_title("Arhīvs: visi notikumi līdz šim");
$template->set_descr("Metāliskais arhīvs");
$template->set_right_defaults();
$template->MiddleBlock = archive($template);
$template->print();

