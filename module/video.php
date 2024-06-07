<?php declare(strict_types = 1);

$template = new MainTemplate();
$template->MiddleBlock = new VideoTemplate;
$template->set_title('Video');
$template->set_right_defaults();
$template->print();
