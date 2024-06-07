<?php declare(strict_types = 1);

class ForumRootTemplate extends AbstractResTemplate
{
	public string $comment_class;

	function out(): void
	{ ?>
		<div class="forum-root">
			<div class="forum-root-name"><a href="<?=$this->res_route ?>"><?=$this->res_name ?></a></div>
			<div class="forum-root-theme-count Comment-count <?=$this->comment_class ?>"><?=$this->res_child_count ?></div>
		</div>
		<div class="forum-root-data"><?=$this->res_data_compiled ?></div>
		<div class="List-sep"></div><?
	}
}
