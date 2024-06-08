<?php declare(strict_types = 1);

class ArticleTemplate extends AbstractResTemplate
{
	public ?int $art_id = null;
	public ?string $hl = null;

	protected function out(): void
	{ ?>
		<div class="TD-cat">
			<div class="res-name"><a class="caption" href="<?=$this->res_route ?>"><?=$this->res_name ?></a></div>
		</div>

		<div class="TD-content">

		<div class="profile-header">
			<div class="user-info">
				<div class="nick"><?=$this->res_nickname ?>,</div>
				<div class="date"><?=$this->res_date_short ?></div>
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
					<a href="<?=$this->res_route ?>">[#]</a>
				</div>
			</div>
		</div>

		<div class="Article-item">
			<div class="data">
				<div class="intro">
				<?=$this->res_intro ?>
				</div>
				<?=$this->res_data ?>
			</div>
		</div>
		</div>

		<div class="TD-cat" id="art-comments-<?=$this->art_id ?>">Komentāri</div>
		<div class="TD-content"><? $this->CommentListT->print() ?></div>

		<div class="TD-cat">Pievienot komentāru</div>
		<div class="TD-content"><? $this->CommentFormT->print() ?></div><?
	}
}
