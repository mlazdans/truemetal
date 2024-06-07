<?php declare(strict_types = 1);

class ForumRootListTemplate extends AbstractTemplate
{
	public ViewResForumCollection $forums;

	protected function out(): void
	{ ?>
		<div class="TD-cat">Forums</div>
		<div class="TD-content"><?
			if($this->forums) {
				foreach($this->forums as $item) {
					$T = new ForumRootTemplate;
					set_res($T, $item);
					$T->comment_class = Forum::has_new_comments($item) ? "Comment-count-new" : "Comment-count-old";
					$T->print();
				}
			} else {?>
				<div class="List-item">Pagaidām nav neviena foruma!</div>
			<?} ?>
		</div><?
	}
}
