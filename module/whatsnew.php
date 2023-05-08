<?php declare(strict_types = 1);

$template = new MainModule('whatsnew');
$template->set_title('Kas jauns');
$template->set_right_defaults();
$template->out(whatsnew($template));
