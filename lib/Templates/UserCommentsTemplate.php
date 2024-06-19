<?php declare(strict_types = 1);

class UserCommentsTemplate extends AbstractTemplate
{
	public CommentsListTemplate $CommentListT;
	public string $l_nick;
	public bool $is_blocked;

	protected function out(): void
	{
		$this->CommentListT->title = sprintf("Komentāri: %s%s", specialchars($this->l_nick), $this->is_blocked ? ' (bloķēts)' : '');
		$this->CommentListT->print();
	}
}
