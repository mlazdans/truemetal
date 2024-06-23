<?php declare(strict_types = 1);

function vote(MainTemplate $template, ViewResType $res, string $value): ?TrueResponseInterface
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
	$countCheck = DB::execute_single(
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

	$inserted = ResVoteEntity::vote_with_timeout(
		5,
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

function res_debug(MainTemplate $template, ViewResType $res): ?AbstractTemplate
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

function comment_edit(MainTemplate $template, ViewResCommentType $Comment): ?AbstractTemplate
{
	if(!User::logged()){
		$template->not_logged();
		return null;
	}

	if(!User::can_edit_res($Comment)){
		$template->forbidden("Nav tiesību labot");
		return null;
	}

	$action = post('action');
	$error_msg = [];

	$T = new CommentEditFormTemplate;
	$T->res_nickname = $Comment->res_nickname;

	if($action == 'update_comment')
	{
		if(update_comment($template, $Comment->res_id, post('res_data'), $error_msg)){
			return redirectn($Comment->res_route);
		}
	} else {
		$T->res_data = $Comment->res_data;
	}

	$T->res_route = $Comment->res_route;

	if($error_msg)
	{
		$T->error_msg = join("<br>", $error_msg);
	}

	return $T;
}

function res_route(MainTemplate $template, ViewResType $res): ?AbstractTemplate
{
	if(!User::is_admin()){
		$template->forbidden();
		return null;
	}

	if($res && $res->res_route)
	{
		return redirectn($res->res_route);
	} else {
		$template->not_found();
	}

	return null;
}

function forum_edit(MainTemplate $template, ViewResForumType $OLD): ?AbstractTemplate
{
	if(!User::logged()){
		$template->not_logged();
		return null;
	}

	if(!User::can_edit_res($OLD)){
		$template->forbidden("Nav tiesību labot");
		return null;
	}

	$action = post('action');
	$error_msg = [];

	$T = new ForumEditFormTemplate;
	$T->res_nickname = $OLD->res_nickname;

	if($action == 'update_forum')
	{
		$res_data = post('res_data');
		$res_name = post('res_name');
		if(empty($res_data))$error_msg[] = "Kaut kas jau jāieraksta";
		if(empty($res_name))$error_msg[] = "Kaut kas jau jāieraksta";

		if(!$error_msg){
			$result = DB::with_new_trans(function() use ($OLD, $res_data, $res_name){
				$Res = new ResType(
					res_id: $OLD->res_id,
					res_data: $res_data,
					res_name: $res_name,
					res_data_compiled: parse_text_data($res_data),
				);

				return $Res->update() && ($res_name !== $OLD->res_name ? Res::update_route($OLD->res_id) : true);
			});

			if($result && ($NEW = ViewResForumEntity::get($OLD->forum_id)))
			{
				return redirectn($NEW->res_route);
			} else {
				$error_msg[] = "Neizdevās saglabāt komentāru";
			}
		}

		$T->res_data = $res_data;
		$T->res_name = $res_name;
	} else {
		$T->res_data = $OLD->res_data;
		$T->res_name = $OLD->res_name;
	}

	$T->res_route = $OLD->res_route;

	if($error_msg)
	{
		$T->error_msg = join("<br>", $error_msg);
	}

	return $T;
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

	if(!($res = load_vres_by_hash($res_hash))){
		if(User::is_admin() && ($res_hash === (string)(int)$res_hash)){
			$res = load_vres_by_id((int)$res_hash);
		}
	}

	if(!$res){
		$template->not_found();
		return null;
	}

	if($section == 'debug'){
		return res_debug($template, $res);
	} elseif($section == 'edit') {
		if($res instanceof ViewResCommentType){
			return comment_edit($template, $res);
		}
		if($res instanceof ViewResForumType){
			if($res->type_id == Forum::TYPE_EVENT){
			} elseif($res->type_id == Forum::TYPE_STD){
				return forum_edit($template, $res);
			}
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
