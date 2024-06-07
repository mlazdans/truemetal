<?php declare(strict_types = 1);

class OnlineTemplate extends RightItemAbstractTemplate
{
	public LoginsCollection $active_sessions;
	public bool $is_logged = false;

	protected function out(): void
	{
		foreach($this->active_sessions as $item)
		{
			if($this->is_logged) { ?>
				<div class="List-item">
					<a href="/user/profile/<?=$item->l_hash ?>/" class="ProfilePopup" data-hash="<?=$item->l_hash ?>"><?=specialchars($item->l_nick) ?></a>
				</div>
			<? } else { ?>
				<div class="List-item">
					<?=specialchars($item->l_nick) ?>
				</div><?
			}
		}
	}
}
