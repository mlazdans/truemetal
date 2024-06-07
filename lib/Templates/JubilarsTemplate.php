<?php declare(strict_types = 1);

class JubilarsTemplate extends RightItemAbstractTemplate
{
	public ViewJubilarsCollection $data;
	public bool $is_logged = false;

	private function when_logged(ViewJubilarsType $j, string $jub_info): void
	{ ?>
		<div class="List-item">
			<a href="/user/profile/<?=$j->l_hash ?>/" class="ProfilePopup" data-hash="<?=$j->l_hash ?>"><?=specialchars($j->l_nick) ?></a><?=$jub_info ?>
		</div><?
	}

	private function when_not_logged(ViewJubilarsType $j, string $jub_info): void
	{ ?>
		<div class="List-item">
			<?=specialchars($j->l_nick).$jub_info ?>
		</div><?
	}

	protected function out(): void
	{
		foreach($this->data as $j)
		{
			$jub_year = '';
			if($j->age == 0){
				$jub_year = 'jauniņais';
			} elseif($j->age == 1){
				$jub_year = ' gadiņš';
			} else {
				if((substr((string)$j->age, -2) != 11) && ($j->age % 10 == 1)){
					$jub_year = ' gads';
				} else {
					$jub_year = ' gadi';
				}
			}

			// $TJub->set_var('l_nick', $j->l_nick);
			// $TJub->set_var('l_hash', $j->l_hash);

			if($j->age){
				$jub_info = " ($j->age $jub_year)";
			} else {
				$jub_info = " ($jub_year)";
			}

			if($this->is_logged){
				$this->when_logged($j, $jub_info);
			} else {
				$this->when_not_logged($j, $jub_info);
			}
		}
	}
}

