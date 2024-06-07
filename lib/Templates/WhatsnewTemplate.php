<?php declare(strict_types = 1);

class WhatsnewTemplate extends AbstractTemplate
{
	public CommentsRecentTemplate $ForumRecent;
	public CommentsRecentTemplate $CommentsRecent;

	protected function out(): void
	{
		?>
		<div class="TD-cat">Kas jauns?</div>

		<div style="display: flex;">
			<div class="TD-content" style="width: 50%;">
				<? $this->ForumRecent->print() ?>
			</div>
			<div class="TD-content" style="width: 50%;">
				<? $this->CommentsRecent->print() ?>
			</div>
		</div><?
	}
}
