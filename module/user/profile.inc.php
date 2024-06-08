<?php declare(strict_types = 1);

$l_hash = array_shift($sys_parameters);

$template = new MainTemplate;
$template->set_title('Profils');

if($l_hash)
{
	$T = public_profile($template, $l_hash);
} else {
	$T = private_profile($template);
}

# TODO: abstract out
if(isset($_GET['json'])){
	header('Content-Type: text/javascript; charset='.$sys_encoding);

	$jsonData = new StdClass;
	$jsonData->title = "[ TRUEMETAL ".specialchars($template->get_title())." ]";
	$jsonData->html = $T->parse();
	print json_encode($jsonData);
} else {
	$template->set_right_defaults();
	$template->MiddleBlock = $T;
	$template->print();
}
