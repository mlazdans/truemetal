<?php declare(strict_types = 1);

class CommentsListTemplate extends AbstractTemplate
{
	public ViewResCommentCollection $Comments;
	public ?string $hl = null;
	public string $title = "Komentāri";

	protected function out(): void
	{
		?><div class="TD-cat" id="comments-list"><?=$this->title ?></div><?

		if($this->Comments->count()){
			$C = new CommentTemplate;
			foreach($this->Comments as $item)
			{
				set_res($C, $item, $this->hl);
				$C->c_id = $item->c_id;
				$C->res_nr += 1;
				$C->print();
			}
		} else { ?>
			<div class="Info">Šim resursam nav neviena komentāra!</div><?
		}
	}
}
