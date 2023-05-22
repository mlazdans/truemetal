<?php declare(strict_types = 1);

use Jfcherng\Diff\DiffHelper;

if(!$i_am_admin){
	return;
}

$action = get('action');
$forum_res_id = (int)get('forum_res_id');
$comment_res_id = (int)get('comment_res_id');
$ignored = (int)get('ignored');

if($action == 'merge')
{
	$sql = "INSERT INTO res_merge (forum_res_id, comment_res_id, ignored) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE ignored=VALUES(ignored)";
	DB::Execute($sql, $forum_res_id, $comment_res_id, $ignored);
	return;
}

DB::setFetchFunction(DBFetchFunction::FetchObject);

$for_p = DB::Prepare("SELECT * FROM view_res_forum WHERE res_id = ?");
$com_p = DB::Prepare("SELECT * FROM view_res_comment WHERE res_resid = ? ORDER BY res_entered ASC LIMIT 1");

class FCache {
	function __construct(
		public ViewResForumType $forum,
		public ViewResForumType $fres,
		public ViewResCommentType $comm,
	) {}
}

function get_first_comment(int $res_id): ?ViewResCommentType
{
	global $com_p;

	return ($r = DB::ExecutePrepared($com_p, $res_id)) ? ViewResCommentType::initFrom($r) : null;
}

function get_forum_res(int $res_id): ?ViewResForumType
{
	global $for_p;

	return ($r = DB::ExecutePrepared($for_p, $res_id)) ? ViewResForumType::initFrom($r) : null;
}

/**
 * @return FCache[]
 * */
function load_cache(): ?array
{
	global $sys_root;

	$f = join_paths($sys_root, '..', "comment_check_cache.txt");
	if(file_exists($f)){
		return unserialize(file_get_contents($f));
	}

	return null;
}

/**
 * @return FCache[]
 * */
function save_cache(): array
{
	global $sys_root;

	$ret = [];
	$q = DB::Query("SELECT * FROM forum ORDER BY forum_id");
	while($r = DB::Fetch($q))
	{
		$forum = ViewResForumType::initFrom($r);
		$fres = get_forum_res($forum->res_id);
		$comm = get_first_comment($forum->res_id);
		if(is_null($fres)){
			printr("[ERROR: Forum not in res:$forum->forum_id]");
			continue;
		}

		if(is_null($fres->res_data)){
			printr("[ERROR: No res_data]", $fres);
			continue;
		}

		if(!$comm){
			continue;
		}

		if(
			($fres->forum_display !== 1) &&
			(
				($fres->res_data !== $comm->res_data) ||
				($fres->res_data_compiled !== $comm->res_data_compiled)
			)
		) {
			$ret[] = new FCache($forum, $fres, $comm);
		}
	}

	file_put_contents(join_paths($sys_root, '..', "comment_check_cache.txt"), serialize($ret));

	return $ret;
}

/**
 * @param FCache[] $data
 * */
function process_data(MainModule $template, array $data)
{
	$T = $template->Index;
	foreach($data as $item){
		$forum = $item->forum;
		$fres = $item->fres;
		$comm = $item->comm;
		$sql = "SELECT * FROM res_merge WHERE forum_res_id = ? AND comment_res_id = ?";

		$T->set_array($forum);
		$T->set_array($fres);
		$T->set_var('forum_res_id', $forum->res_id);
		$T->set_var('comment_res_id', $comm->res_id);
		$T->set_var('res_data_diff', DiffHelper::calculate($fres->res_data, $comm->res_data, 'SideBySide'));
		$T->set_var('res_data_compiled_diff', DiffHelper::calculate($fres->res_data_compiled, $comm->res_data_compiled, 'SideBySide'));

		$table_class = $merge_status = '';
		if($status = DB::ExecuteSingle($sql, $forum->res_id, $comm->res_id))
		{
			if($status->ignored){
				$merge_status = "ignored, ";
				$table_class = "ignored";
			} else {
				$merge_status = "merged, ";
				$table_class = "merged";
			}
		}
		$T->set_var('merge_status', $merge_status);
		$T->set_var('table_class', $table_class);

		$T->parse_block('cmp', TMPL_APPEND);
	}
}

$template = new MainModule("cmp", 'cmp.tpl');

if(!($data = load_cache()))
{
	$data = save_cache();
}

process_data($template, $data);

$template->out(null);
