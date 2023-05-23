<?php declare(strict_types = 1);

/**
 * DeDup - foruma tēmas un pirmā komentāra sync.
 * Kaut kādā brīdī tika ieviesta atsevišķa komentāru tabula. Taisot tēmu, komentārs tika dublēts no forum_data.
 * Kaut kad vēlāk forum_data u.c. lauki tika pārnesti uz res tabulu.
 * Mēģinājums savest kārtībā.
 * */

# TODO: forum_display = 1

class DeDup
{
	private $for_p;
	private $com_p;
	private $upd_fres;
	private $upd_votes;
	private $del_res;
	private $redir_p;

	function __construct()
	{
		DB::setFetchFunction(DBFetchFunction::FetchObject);
		$this->for_p = DB::Prepare("SELECT * FROM view_res_forum WHERE res_id = ?");
		$this->com_p = DB::Prepare("SELECT * FROM view_res_comment WHERE res_resid = ? ORDER BY res_entered ASC LIMIT 1");

		$merge_fields = ['login_id', 'res_entered', 'res_nickname', 'res_email', 'res_ip', 'res_data', 'res_data_compiled'];

		$sql_fields = [];
		foreach($merge_fields as $f){
			$sql_fields[] = "fres.$f = cres.$f";
		}
		$fields = join(",\n", $sql_fields);

		$this->upd_fres = DB::Prepare("UPDATE res fres JOIN res cres ON cres.res_id = ? SET $fields WHERE fres.res_id = ?");
		$this->upd_votes = DB::Prepare("UPDATE res_vote SET res_id = ? WHERE res_id = ?");
		$this->del_res = DB::Prepare("DELETE FROM res WHERE res_id = ?");
		$this->redir_p  = DB::Prepare("INSERT INTO res_redirect (from_res_id, to_res_id) VALUES (?,?)");
	}

	function get_first_comment(int $res_id): ?ViewResCommentType
	{
		return ($r = DB::ExecutePrepared($this->com_p, $res_id)) ? ViewResCommentType::initFrom($r) : null;
	}

	function get_forum_res(int $res_id): ?ViewResForumType
	{
		return ($r = DB::ExecutePrepared($this->for_p, $res_id)) ? ViewResForumType::initFrom($r) : null;
	}

	function transform_comment_into_theme(int $forum_res_id, int $comment_res_id)
	{
		# TODO: saglabāt pirmā komenta redirektu uz tēmu? (/resroute/293859/?c_id=282374)
		return
			$this->res_redirect($comment_res_id, $forum_res_id) &&
			DB::ExecutePrepared($this->upd_fres, $comment_res_id, $forum_res_id) &&
			DB::ExecutePrepared($this->upd_votes, $forum_res_id, $comment_res_id) &&
			DB::ExecutePrepared($this->del_res, $comment_res_id)
		;
	}

	function transform_comment_into_event(int $forum_res_id, int $comment_res_id)
	{
		return
			$this->res_redirect($comment_res_id, $forum_res_id) &&
			DB::ExecutePrepared($this->upd_votes, $forum_res_id, $comment_res_id) &&
			DB::ExecutePrepared($this->del_res, $comment_res_id)
		;
	}

	function res_redirect(int $from_res_id, int $to_res_id)
	{
		return DB::ExecutePrepared($this->redir_p, $from_res_id, $to_res_id);
	}

}
