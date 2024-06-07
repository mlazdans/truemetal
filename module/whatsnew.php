<?php declare(strict_types = 1);

$template = new MainTemplate;
$template->MiddleBlock = get_whatsnew_template($template);
$template->set_title('Kas jauns');
$template->set_right_defaults();
$template->print();
