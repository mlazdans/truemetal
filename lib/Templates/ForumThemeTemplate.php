<?php declare(strict_types = 1);

class ForumThemeTemplate extends AbstractResTemplate
{
	public string $comment_class;

	protected function out(): void
	{ ?>
		<div class="forum-theme">
			<div class="forum-theme-main">
				<div class="forum-theme-name"><a href="<?=$this->res_route ?>"><b><?=$this->res_name ?></b></a></div>
				<div class="forum-theme-comment-count <?=$this->comment_class ?>">(<?=$this->res_comment_count ?>)</div>
				<div class="forum-theme-last-comment-date"><?=$this->res_comment_last_date ?></div>
			</div>
			<div class="forum-theme-info">
				<? if($this->profile_link_enabled) { ?>
					<a href="/user/profile/<?=$this->l_hash ?>/" class="ProfilePopup" data-hash="<?=$this->l_hash ?>" style="color: white;"><?=$this->res_nickname ?></a>, <?=$this->res_date ?>
				<? } else { ?>
					<?=$this->res_nickname ?>, <?=$this->res_date ?>
				<? } ?>
			</div>
		</div><?
	}
}
