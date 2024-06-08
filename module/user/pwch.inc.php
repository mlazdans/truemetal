<?php declare(strict_types = 1);

$template = new MainTemplate();
$template->set_title('Paroles maiņa');

$template->set_right_defaults();
$template->MiddleBlock = change_pw($template);
$template->print();
