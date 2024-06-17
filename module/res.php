<?php declare(strict_types = 1);

function vote(MainTemplate $template, ?ResourceTypeInterface $res, string $value): ?TrueResponseInterface
{
	global $ip;

	$json = isset($_GET['json']);

	if(!User::logged())
	{
		$template->not_logged();
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

function res_debug(MainTemplate $template, ?ResourceTypeInterface $res): ?AbstractTemplate
{
	if(!User::logged()){
		$template->not_logged();
		return null;
	}

	if(!User::can_debug_res($res)){
		$template->forbidden("Nav tiesību skatīt");
		return null;
	}

	$T = new ResDebugTemplate;
	$T->res = $res;

	return $T;
}

function comment_edit(MainTemplate $template, ?ViewResCommentType $Comment): ?AbstractTemplate
{
	if(!User::logged()){
		$template->not_logged();
		return null;
	}

	if(!$Comment){
		$template->not_found();
		return null;
	}

	if(!User::can_edit_res($Comment)){
		$template->forbidden("Nav tiesību labot");
		return null;
	}

	$action = post('action');
	$error_msg = [];

	$T = new CommentEditFormTemplate;
	$T->l_nick = User::nick();

	if($action == 'update_comment')
	{
		if(update_comment($template, $Comment->res_id, post('res_data'), $error_msg)){
			redirect($Comment->res_route);
			return null;
		}
	} else {
		$T->res_data = specialchars($Comment->res_data);
	}

	$T->res_route = $Comment->res_route;

	if($error_msg)
	{
		$T->error_msg = join("<br>", $error_msg);
	}

	return $T;
}

function res_route(MainTemplate $template, ?ResourceTypeInterface $res): ?AbstractTemplate
{
	if(!User::is_admin()){
		$template->forbidden();
		return null;
	}

	$moved = false;
	if($redirect_res = ResRedirectEntity::get_by_from_res_id($res->res_id)){
		$moved = true;
		$res = load_res($redirect_res->to_res_id);
	}

	if($res && ($location = $res->Route()))
	{
		if($moved){
			redirectp($location);
		} else {
			redirect($location);
		}
	} else {
		$template->not_found();
	}

	return null;
}

function process_request(MainTemplate $template): null|AbstractTemplate|TrueResponseInterface
{
	global $sys_parameters;

	$section = array_shift($sys_parameters);
	$res_hash = array_shift($sys_parameters);

	if(!$section || !$res_hash){
		$template->bad_request();
		return null;
	}

	if(!($res = load_res_by_hash($res_hash))){
		$template->not_found();
		return null;
	}

	if($section == 'debug'){
		return res_debug($template, $res);
	} elseif($section == 'edit') {
		if($res instanceof ViewResCommentType){
			return comment_edit($template, $res);
		}
	} elseif($section == 'route'){
		return res_route($template, $res);
	} elseif($section == 'vote'){
		$value = array_shift($sys_parameters);
		return vote($template, $res, $value);
	} elseif($section == 'attend'){
		if($res instanceof ViewResForumType){
			$yesno = array_shift($sys_parameters);
			if(($yesno == 'yes') || ($yesno == 'no')){
				return attend($template, $res, $yesno);
			} elseif(!$yesno) {
				return attendees_view($res);
			}
		}
	}

	$template->bad_request();

	return null;
}

$template = new MainTemplate();
$T = process_request($template);

# TODO: abstract out
if($T instanceof TrueResponseInterface){
	$T->print();
} elseif(isset($_GET['json'])){
	header('Content-Type: text/javascript; charset=utf-8');

	$template->MiddleBlock = $T;
	$template->set_out('container');

	$jsonData = new StdClass;
	$jsonData->title = "[ TRUEMETAL ".specialchars($template->get_title())." ]";
	$jsonData->html = $template->parse();
	print json_encode($jsonData);
} else {
	$template->set_right_defaults();
	$template->MiddleBlock = $T;
	$template->print();
}
