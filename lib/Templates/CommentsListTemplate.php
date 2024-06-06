<?php declare(strict_types = 1);

class CommentsListTemplate extends AbstractTemplate
{
	public ViewResCommentCollection $Comments;
	# TODO: tips
	public $disabled_users = [];
	public string $hl = "";

	function __construct()
	{
		if(User::logged())
		{
			$this->disabled_users = CommentDisabled::get(User::id());
		}
	}

	function out(): void
	{
		if($this->Comments->count()){
			// $comment_nr = 0;
			$C = new CommentTemplate;
			foreach($this->Comments as $item){
				set_res($C, $item, $this->hl);
				++$C->comment_nr;

				# TODO: kaut kas no šīs loģikas būtu jāpārness ārpus renderēšanas
				$C->can_edit_res = User::can_edit_res($item);
				$C->can_debug_res = User::can_debug_res($item);
				$C->vote_control_enabled = User::logged();
				$C->profile_link_enabled =  User::logged() && $item->l_hash;

				$C->print();

				// $this->comment($c, ++$comment_nr);
			}
		} else { ?>
			<!-- BEGIN BLOCK_no_comments disabled -->
			<div class="Info">Šim resursam nav neviena komentāra!</div>
			<!-- END BLOCK_no_comments --><?
		}
	}
}
