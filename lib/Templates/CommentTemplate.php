<?php declare(strict_types = 1);

class CommentTemplate extends AbstractResTemplate
{
	public int $c_id;

	protected function out(): void
	{
		if($this->res_data_compiled && $this->hl){
			$this->res_data_compiled = hl($this->res_data_compiled, $this->hl);
		}

		$c_disabled_user_class = '';
		if($this->is_disabled){
			$c_disabled_user_class = ' disabled';
			$this->res_data_compiled = '-neredzams komentārs-';
		}
		?>
		<div class="Comment" id="comment<?=$this->c_id ?>">
			<? $this->profile() ?>
			<div class="res-data<?=$c_disabled_user_class ?>">
				<?=$this->res_data_compiled ?>
			</div>
		</div><?
	}
}
