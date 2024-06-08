<?php declare(strict_types = 1);

class ForumDetTemplate extends AbstractResTemplate
{
	public ?AttendTemplate $AttendT = null;
	public bool $is_closed = false;
	public bool $is_sorted_A = false;
	public bool $is_sorted_D = false;
	public string $error_msg = "";

	protected function out(): void
	{ ?>
		<div class="TD-cat"><?=$this->res_name ?></div>
		<div class="TD-content">

		<?
		if($this->AttendT) {
			$this->AttendT->print();
		}
		?>

		<div class="profile-header">
			<div class="user-info">
				<div class="nick"><?=$this->res_nickname ?>,</div>
				<div class="date"><?=$this->res_date ?></div>
			</div>

			<div class="controls">
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
					<a href="<?=$this->res_route ?>">[##]</a>
				</div>
			</div>
		</div>
		<div class="res-data">
			<?=$this->res_data_compiled ?>
		</div>

		<? if($this->error_msg) { ?>
			<div class="error"><?=$this->error_msg ?></div>
		<? } ?>

		<? if($this->is_sorted_A || $this->is_sorted_D) { ?>
		<div class="List-item">
			<? if($this->is_sorted_A) { ?>
			Komentāri sakārtoti pēc to ievadīšanas datuma
			<? } ?>

			<? if($this->is_sorted_D) { ?>
			Komentāri sakārtoti pēc to ievadīšanas datuma dilstoši
			<? } ?>
		</div>
		<? } ?>

		<div class="List-sep"></div>

		<? if($this->CommentListT) { ?>
			<div class="TD-content"><? $this->CommentListT->print() ?></div>
		<? } ?>

		<? if($this->is_closed) { ?>
			<div class="Info">Tēma slēgta</div>
		<? } ?>

		<? if($this->CommentFormT) { ?>
			<div class="TD-cat">Pievienot komentāru</div>
			<div class="TD-content"><? $this->CommentFormT->print() ?></div>
		<? } ?>

		</div><?
	}
}
