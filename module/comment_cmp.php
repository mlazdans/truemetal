<?php declare(strict_types = 1);

use Jfcherng\Diff\DiffHelper;

if(!$i_am_admin){
	return;
}

ini_set("memory_limit", "512M");

$action = get('action');
$forum_res_id = (int)get('forum_res_id');
$comment_res_id = (int)get('comment_res_id');
$ignored = (int)get('ignored');

if($action == 'merge')
{
	insert_comment_theme_merge($forum_res_id, $comment_res_id, $ignored);
	return;
}

function process_data(MainModule $template)
{
	$T = $template->Index;
	$c = 0;
	$erase_data = [];

	/**
	 * Salīdzina tēmas ar pirmo komentu. $data tika ievākts no faila, kas tika ģenerēts ar skriptu, kurš tagad ir izdzēsts
	 * Ideja - ielasīt visu forumu, tad dabūt pirmo komentāru un tad salīdzināt res_data un res_data_compiled
	 * Pēc merge vairs īsti nav aktuāls, bet var noderēt par pamatu kādam citam compare.
	 * */
	foreach($data as $item)
	{
		$fres = $item->fres;
		$comm = $item->comm;

		$erase_data[] = $fres->res_id;

		$ddiff = datediff($fres->res_entered, $comm->res_entered, 1);
		// if(abs($ddiff) < 60){
		// 	continue;
		// }

		$sql = "SELECT * FROM res_merge WHERE forum_res_id = ? AND comment_res_id = ?";

		// $T->set_with_prefix($forum);
		$T->set_with_prefix("forum_", $fres);
		$T->set_with_prefix("comment_", $comm);
		// $T->set_var('forum_res_id', $forum->res_id);
		// $T->set_var('comment_res_id', $comm->res_id);
		// $T->set_var('forum_login_id', $forum->login_id??null);
		// $T->set_var('comment_login_id', $comm->login_id??null);
		$T->set_var("entered_diff", $ddiff);
		$T->set_var('res_data_diff', DiffHelper::calculate($fres->res_data, $comm->res_data, 'SideBySide'));
		$T->set_var('res_data_compiled_diff', DiffHelper::calculate($fres->res_data_compiled, $comm->res_data_compiled, 'SideBySide'));
		$T->set_var("item_count", ++$c);

		$table_class = $merge_status = '';
		if($status = DB::ExecuteSingle($sql, $fres->res_id, $comm->res_id))
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
	// print join("")
}

$template = new MainModule("cmp", 'cmp.tpl');

process_data($template);

$template->out(null);
