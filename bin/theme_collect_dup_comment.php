<?php declare(strict_types = 1);

require_once('../include/boot.php');
require_once('include/dbconnect.php');
require_once('include/console.php');

class DeDupFirstComment extends DeDup
{
	function dedup()
	{
		$reset_p = DB::Prepare("UPDATE res SET res_data = NULL, res_data_compiled = NULL WHERE res_id = ?");

		$q = DB::Query("SELECT * FROM forum WHERE forum_allow_childs = 0 ORDER BY forum_id");
		while($r = DB::Fetch($q))
		{
			$forum = ViewResForumType::initFrom($r);
			$fres = $this->get_forum_res($forum->res_id);
			$comm = $this->get_first_comment($forum->res_id);
			if(is_null($fres)){
				printr("[ERROR: Forum not in res:$forum->forum_id]");
				continue;
			}

			if(is_null($fres->res_data)){
				printr("[ERROR: No res_data]", $fres);
				continue;
			}

			// Skip bez komentiem
			if(!$comm){
				continue;
			}

			// Show forum_data (manuāls formatējums). Šiem vienmēr pirmais koments ir kā duplikāts no tēmas datiem un netika rādīts.
			if($fres->forum_display == 1)
			{
				if($this->transform_comment_into_event($fres->res_id, $comm->res_id, 0)){
					print "Merged forum_display=1: $fres->res_id, $comm->res_id\n";
				} else {
					return false;
				}
				continue;
			}

			if(
				($fres->res_data == $comm->res_data) &&
				($fres->res_data_compiled == $comm->res_data_compiled)
			)
			{
				if(
					($fres->res_nickname !== $comm->res_nickname) &&
					($fres->res_ip !== $comm->res_ip)
				) {
					// Vēsturiskās foruma tēmas, bez ievadīta komenta!
					if(DB::ExecutePrepared($reset_p, $fres->res_id)){
						print "Emptied: $fres->res_id\n";
					} else {
						return false;
					}
				} else {
					// Ar tēmu vienādais koments
					if($this->transform_comment_into_theme($fres->res_id, $comm->res_id, 0)){
						print "Merged 1st comment: $fres->res_id, $comm->res_id\n";
					} else {
						return false;
					}
				}
			} else {
				// Te nāk tie komenti, kas manuāli tika syncoti: bin/updates_sql/r232-4-res_merge.sql
			}
		}

		return true;
	}

	function dedup_manual()
	{
		$q = DB::Query("SELECT * FROM res_merge WHERE ignored = 0");
		while($r = DB::FetchObject($q))
		{
			if($this->transform_comment_into_theme($r->forum_res_id, $r->comment_res_id)){
				print "Merged manual: $r->forum_res_id, $r->comment_res_id\n";
			} else {
				return false;
			}
		}

		return true;
	}
}

$res =
	DB::Query("DROP TRIGGER IF EXISTS res_trigger_AD") &&
	DB::Query("DROP TRIGGER IF EXISTS res_trigger_AI") &&
	DB::Query("DROP TRIGGER IF EXISTS res_trigger_AU") &&
	DB::Query("DROP TRIGGER IF EXISTS res_vote_trigger_AD") &&
	DB::Query("DROP TRIGGER IF EXISTS res_vote_trigger_AI") &&
	DB::Query("DROP TRIGGER IF EXISTS res_vote_trigger_AU")
;

if(!$res){
	print "Drop triggers failed\n";
	exit(1);
}

$res = DB::withNewTrans(function(){
	return DB::Query("CALL res_meta_update(NULL)") && DB::Query("CALL logins_meta_update(NULL)");
});

if(!$res){
	print "Call update metas failed\n";
	exit(1);
}

$res = DB::withNewTrans(function(){
	$DD = new DeDupFirstComment();

	return $DD->dedup_manual() && $DD->dedup();
});

if($res){
	print "Merged\n";
} else {
	print "FAIL\n";
}

print "\nTODO:\n";
print "Re-create triggers!\n";

$res = DB::withNewTrans(function(){
	return DB::Query("CALL res_meta_update(NULL)") && DB::Query("CALL logins_meta_update(NULL)");
});

if(!$res){
	print "Final Call update metas failed\n";
	exit(1);
}
