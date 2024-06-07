<?php declare(strict_types = 1);

class CommentsRecentTemplate extends RightItemAbstractTemplate
{
	public bool $show_more = false;
	public ViewResForumCollection|ViewResArticleCollection $data;

	protected function out(): void
	{
		foreach($this->data as $item)
		{
			$comment_class = Res::not_seen($item->res_id, $item->res_comment_last_date??$item->res_entered) ? "Comment-count-new" : "Comment-count-old";
			$res_name = $item->res_name;
			if(mb_strlen($res_name) > 100){
				$res_name = mb_substr($item->res_name, 0, 97).'...';
			}
			?>
			<div class="List-item">
				<a href="<?=$item->res_route ?>" style="display: block;"><?=specialchars($res_name) ?>
					<span class="Comment-count <?=$comment_class ?>">(<?=$item->res_comment_count ?>)</span>
				</a>
			</div><?
		}

		if($this->show_more) { ?>
			<div class="List-item">
				<a style="font-style: italic;" href="/whatsnew/">-vairāk-</a>
			</div>
		<? }
	}
}
