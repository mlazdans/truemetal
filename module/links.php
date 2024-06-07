<?php declare(strict_types = 1);

$template = new MainTemplate();
$template->set_title('Linki');
$template->set_right_defaults();
$template->MiddleBlock = new LinksTemplate;
$template->print();
