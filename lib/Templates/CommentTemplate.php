<?php declare(strict_types = 1);

class CommentTemplate extends AbstractResTemplate
{
	public int $comment_nr = 0;
	public int $c_id;
	public ?string $hl = "";

	public bool $can_edit_res = false;
	public bool $can_debug_res = false;
	public bool $vote_control_enabled = false;
	public bool $profile_link_enabled = false;

	function out(): void
	{
		$hl = $this->hl;
		// $BLOCK_comment->set_array($item);

		if($this->res_data_compiled && $hl){
			$this->res_data_compiled = hl($this->res_data_compiled, $hl);
		}

		$c_disabled_user_class = '';
		if(!empty($disabled_users[$this->login_id])){
			$c_disabled_user_class = ' disabled';
			$this->res_data_compiled = '-neredzams komentārs-';
		}
		?>
		<div class="Comment" id="comment<?=$this->c_id ?>">
			<div class="profile-header">
				<div class="user-info">
					<div class="nick"><?=$this->res_nickname ?>,&nbsp;</div>
					<div class="date"><?=$this->res_date ?></div>
				</div>

				<div class="controls">
					<? if($this->can_edit_res){ ?>
						<div class="unselectable">
							<a href="/comment/edit/<?=$this->c_id ?>/">[labot]</a>
						</div>
					<? } ?>

					<? if($this->can_debug_res){ ?>
						<div class="unselectable">
							<a href="/resdebug/<?=$this->res_id ?>/">[debug]</a>
						</div>
					<? } ?>

					<div class="vote unselectable <?=$this->comment_vote_class ?>" id="votes-<?=$this->res_id ?>" title="+<?=$this->res_votes_plus_count ?> - <?=$this->res_votes_minus_count ?>">
						<?=$this->res_votes ?>
					</div>

					<? if($this->vote_control_enabled) { ?>
						<div class="unselectable">
							<a href="/vote/up/<?=$this->res_id ?>/" class="SendVote" data-res_id="<?=$this->res_id ?>" data-vote="up">[&plus;]</a>
						</div>
						<div class="unselectable">
							<a href="/vote/down/<?=$this->res_id ?>/" class="SendVote" data-res_id="<?=$this->res_id ?>" data-vote="down">[&ndash;]</a>
						</div>
					<? } ?>

					<? if($this->profile_link_enabled) { ?>
						<div class="unselectable">
							<a href="/user/profile/<?=$this->l_hash ?>/" class="ProfilePopup" data-hash="<?=$this->l_hash ?>">[Profils]</a>
						</div>
					<? } ?>

					<div class="unselectable">
						<a href="<?=$this->res_route ?>">[#<?=$this->comment_nr ?>]</a>
					</div>
				</div>
			</div>
			<div class="res-data<?=$c_disabled_user_class ?>">
				<?=$this->res_data_compiled ?>
			</div>
		</div><?
	}
}
