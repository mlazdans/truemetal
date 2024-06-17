<?php declare(strict_types = 1);

function vote(MainTemplate $template, string $value, string $res_hash): ?TrueResponseInterface
{
	global $ip;

	$json = isset($_GET['json']);

	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	if(!($res = ViewResEntity::get_by_hash($res_hash))){
		$template->not_found();
		return null;
	}

	if(User::id() == $res->login_id)
	{
		$template->msg(specialchars($value == 'up' ? ":)" : ">:)"));
		return null;
	}

	# Check count 24h
	$countCheck = DB::ExecuteSingle(
		"SELECT COUNT(*) cc FROM res_vote WHERE login_id = ? AND rv_entered > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 24 HOUR)",
		User::id()
	);

	if(!$countCheck)
	{
		$template->error("Datubāzes kļūda");
		return null;
	}

	if($countCheck['cc'] > 24)
	{
		$template->msg("Pārsniegtiņš divdesmitčetriņu stundiņu limitiņš balsošaniņai.");
		return null;
	}

	$timeout = 5;
	$sql = "INSERT INTO res_vote (
		res_id, login_id, rv_value, rv_userip
	) VALUES (
		?, ?, ?, ?
	) ON DUPLICATE KEY UPDATE
		rv_value = CASE WHEN TIMESTAMPDIFF(MINUTE, rv_entered, CURRENT_TIMESTAMP) < $timeout THEN
			CASE WHEN VALUES(rv_value) <=> rv_value THEN 0 ELSE VALUES(rv_value) END
		ELSE rv_value END,
		rv_userip = CASE WHEN TIMESTAMPDIFF(MINUTE, rv_entered, CURRENT_TIMESTAMP) < $timeout THEN VALUES(rv_userip) ELSE rv_userip END
	";

	$inserted = DB::Execute(
		$sql,
		$res->res_id,
		User::id(),
		$value == 'up' ? 1 : -1,
		$ip
	);

	if(!$inserted || !($new_data = ViewResEntity::get_by_id($res->res_id)))
	{
		$template->error("Datubāzes kļūda");
		return null;
	}

	if($json){
		$retJson = new StdClass;
		$retJson->Votes = $new_data->res_votes;
		return new JsonResponse($retJson);
	} else {
		redirect_referer();
		return null;
	}
}


$template = new MainTemplate();

$value = array_shift($sys_parameters);
$res_hash = array_shift($sys_parameters);

if($T = vote($template, $value, $res_hash)){
	$T->print();
	return;
} else {
	# TODO: abstract out
	if(isset($_GET['json'])){
		header('Content-Type: text/javascript; charset=utf-8');

		$template->set_out('container');

		$jsonData = new StdClass;
		$jsonData->title = "[ TRUEMETAL ".specialchars($template->get_title())." ]";
		$jsonData->html = $template->parse();
		print json_encode($jsonData);
	} else {
		$template->set_right_defaults();
		$template->print();
	}
}
