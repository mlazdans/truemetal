<?php declare(strict_types = 1);

abstract class AbstractResTemplate extends AbstractTemplate
{
	public ?int $res_id = null;
	public ?int $login_id = null;
	public ?int $res_votes_plus_count = null;
	public ?int $res_votes_minus_count = null;
	public ?int $res_child_count = null;
	public ?int $res_comment_count = null;
	public int $res_nr = 0;
	public ?string $res_hash = null;
	public ?string $res_date = null;
	public ?string $res_entered = null;
	public ?string $res_date_short = null;
	public ?string $res_votes = null;
	public ?string $comment_vote_class = null;
	public ?string $res_route = null;
	public ?string $res_name = null;
	public ?string $res_nickname = null;
	public ?string $res_intro = null;
	public ?string $res_data = null;
	public ?string $res_data_compiled = null;
	public ?string $l_hash = null;
	public ?string $res_comment_last_date = null;
	public ?string $hl = "";
	public bool $vote_control_enabled = false;
	public bool $profile_link_enabled = false;
	public bool $can_edit_res = false;
	public bool $can_debug_res = false;
	public bool $is_disabled = false;
	public ?CommentsListTemplate $CommentListT = null;
	public ?CommentAddFormTemplate $CommentFormT = null;

	protected function profile(): void
	{ ?>
		<div class="profile-header">
		<div class="user-info">
			<div class="nick"><?=$this->res_nickname ?>,&nbsp;</div>
			<div class="date"><?=$this->res_date ?></div>
		</div>

		<div class="controls">
			<? if($this->can_edit_res) { ?>
				<div class="unselectable">
					<a href="/res/edit/<?=$this->res_hash ?>/">[labot]</a>
				</div>
			<? } ?>

			<? if($this->can_debug_res) { ?>
				<div class="unselectable">
					<a href="/res/debug/<?=$this->res_hash ?>/">[debug]</a>
				</div>
			<? } ?>

			<div class="vote unselectable <?=$this->comment_vote_class ?>" id="votes-<?=$this->res_hash ?>" title="+<?=$this->res_votes_plus_count ?> - <?=$this->res_votes_minus_count ?>">
				<?=$this->res_votes ?>
			</div>

			<? if($this->vote_control_enabled) { ?>
				<div class="unselectable">
					<a href="/vote/up/<?=$this->res_hash ?>/" class="SendVote" data-res_hash="<?=$this->res_hash ?>" data-vote="up">[&plus;]</a>
				</div>
				<div class="unselectable">
					<a href="/vote/down/<?=$this->res_hash ?>/" class="SendVote" data-res_hash="<?=$this->res_hash ?>" data-vote="down">[&ndash;]</a>
				</div>
			<? } ?>

			<? if($this->profile_link_enabled) { ?>
				<div class="unselectable">
					<a href="/user/profile/<?=$this->l_hash ?>/" class="ProfilePopup" data-hash="<?=$this->l_hash ?>">[Profils]</a>
				</div>
			<? } ?>

			<div class="unselectable">
				<a href="<?=$this->res_route ?>">[#<?=$this->res_nr ?>]</a>
			</div>
		</div>
	</div><?
	}
}
