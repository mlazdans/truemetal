<?php declare(strict_types = 1);

class ForumThemeListTemplate extends AbstractResTemplate
{
	public ViewResForumCollection $themes;
	public ?ThemeEditFormTemplate $form = null;
	public bool $is_sorted_C = false;
	public bool $is_sorted_T = false;
	public bool $is_logged = false;
	public bool $is_bazaar = false;

	public int $pages_visible_to_sides;
	public int $items_per_page;
	public int $page_id;

	private function pages(): void
	{
		$forum_count = $this->res_child_count;
		$total_pages = ceil($forum_count / $this->items_per_page);

		if($total_pages < 1){
			return;
		}

		$page_id = $this->page_id;
		$prev_page_id = $page_id > 1 ? $page_id - 1 : $page_id;
		$next_page_id = $page_id < $total_pages ? $page_id + 1 : $page_id;

		?>
		<div class="Forum-cat" style="margin-bottom: 0; display: flex;">
			<div>
				<a href="<?=$this->res_route ?>/page/<?=$prev_page_id ?>/"><img src="/img/left.png" alt="Vienu lapu atpakaļ"></a>
			</div><?
			$_pvs = $this->pages_visible_to_sides;
			$sep_count = 0;
			$visible_pages = $_pvs + (
				$page_id > $_pvs ? ($total_pages - $page_id > $_pvs ? 0 : $_pvs - ($total_pages - $page_id))
				: $_pvs - $page_id + 1
			);

			$side_sep = 1 + (
				$page_id > $_pvs ? ($total_pages - $page_id > $_pvs ? 0 : 1)
				: 1
			);

			for($p = 1; $p <= $total_pages; $p++)
			{
				$p_id = ($total_pages > 10) && ($p < 10) ? "0$p" : $p;
				$page_style = $p == $page_id ? ' style="color: #00AC00;"' : '';
				?>
				<div class="Forum-Pager"><?
				if(abs($p - $page_id) > $visible_pages)
				{
					$sep_count++;
					$page_seperator = (abs($p - $page_id) > $visible_pages) && (abs($p - $page_id) - $visible_pages <= $side_sep) ? '[..]' : '';
					print $page_seperator;
				} else { ?>
					<a href="<?=$this->res_route ?>/page/<?=$p ?>/"<?=$page_style ?>>[<?=$p_id ?>]</a><?
				}
				?>
				</div><?
			} ?>
			<div>
				<a href="<?=$this->res_route ?>/page/<?=$next_page_id ?>/"><img src="/img/right.png" alt="Vienu lapu uz priekšu"></a>
			</div>
		</div>
	 <?
	}

	protected function out(): void
	{ ?>
		<div class="TD-cat">Forums / <?=$this->res_name ?></div>
		<div class="TD-content">

		<? $this->pages() ?>

		<? if($this->is_sorted_T) { ?>
			<div class="List-item">
				Tēmas sakārtotas pēc to ievadīšanas datuma
			</div>
		<? } ?>

		<? if($this->is_sorted_C) { ?>
		<div class="List-item">
			Tēmas sakārtotas pēc to pēdējā komentāra datuma
		</div>
		<? } ?>

		<? if($this->themes) {
			foreach($this->themes as $theme){
				$T = new ForumThemeTemplate;
				set_res($T, $theme);
				$T->comment_class = Forum::has_new_comments($theme) ? "Comment-count-new" : "Comment-count-old";
				$T->res_date = proc_date($theme->res_entered, true);
				$T->res_comment_last_date = $theme->res_comment_last_date ? proc_date($theme->res_comment_last_date, true) : "-";
				$T->print();
			}
		} else { ?>
			<div class="List-item">
				Pagaidām forumam nav nevienas tēmas!
			</div>
		<? } ?>

		<div class="List-sep"></div>

		<? if($this->is_logged) { ?>
			<div class="TD-cat">
				Pievienot jaunu tēmu
			</div>
			<div class="List-item">
				Ņem vērā - stulbs tēmas nosaukums garantē tēmas izdzēšanu un daudz mīnusus!
			</div>
			<? if($this->is_bazaar) { ?>
				<div class="List-item">
					Tirgus sadaļā tēmas veidot var sākt, ja reģistrējies vismaz 10 dienas VAI (plusi-mīnusi) >= 10.
				</div>
			<? } ?>

			<? if($this->form) {
				$this->form->print();
			} ?>
		<? } else { ?>
			<div class="Info">
				Pievienot jaunu tēmu var tikai reģistrēti lietotāji, tapēc, ielogojies vai <a href="/register/">reģistrējies</a>!
			</div>
		<? } ?>
		</div><?
	}
}
