<?php declare(strict_types = 1);

$json = isset($_GET['json']);
$l_hash = array_shift($sys_parameters);

$template = new MainModule($sys_module_id);
$template->set_title('Profils');

if($l_hash)
{
	$T = public_profile($template, $l_hash);

	if($json)
	{
		$template->set_middle($T);
		$html = $template->Index->get_block('BLOCK_container')->parse();

		$jsonData = new StdClass;
		$jsonData->title = "[ TRUEMETAL ".$template->get_title()." ]";
		$jsonData->html = $html;
		header('Content-Type: text/javascript; charset='.$sys_encoding);
		print json_encode($jsonData);
		return;
	}

	$T?->enable('BLOCK_profile_title');
} else {
	$T = private_profile($template);
}

$template->set_right_defaults();
$template->out($T);
