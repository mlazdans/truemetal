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

	protected function out(): void
	{
		if($this->Comments->count()){
			$C = new CommentTemplate;
			foreach($this->Comments as $item){
				set_res($C, $item, $this->hl);
				$C->c_id = $item->c_id;
				$C->comment_nr += 1;
				$C->print();
			}
		} else { ?>
			<div class="Info">Šim resursam nav neviena komentāra!</div><?
		}
	}
}
