<?php declare(strict_types = 1);

class UserCommentsTemplate extends AbstractTemplate
{
	public CommentsListTemplate $CommentListT;
	public string $l_nick;
	public bool $is_blocked;

	protected function out(): void
	{ ?>
		<div class="TD-cat">Komentāri: <?=specialchars($this->l_nick) ?><?=($this->is_blocked ? ' (bloķēts)' : '') ?></div>
		<?
		$this->CommentListT->print();
	}
}
