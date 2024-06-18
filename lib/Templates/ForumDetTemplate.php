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

		<? if($this->AttendT) {
			$this->AttendT->print();
		} ?>

		<? $this->profile() ?>

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
			<div class="TD-content"><? $this->CommentFormT->print() ?></div>
		<? } ?>

		</div><?
	}
}
