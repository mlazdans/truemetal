<?php declare(strict_types = 1);

class ArchiveTemplate extends AbstractTemplate
{
	# Eksperiments ar vnk fetch() bez papildus buferiem
	public ViewMainpageEntity $MP;

	protected function out(): void
	{ ?>
		<div class="TD-cat">Arhīvs</div>
		<?

		$old_date = '';
		// $formatter = new IntlDateFormatter("lv", IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
		$menesi = menesi();
		$count = 0;
		while($item = $this->MP->fetch())
		{
			$ts = strtotime($item->res_entered);
			$date = date('Ym', $ts);
			$res_date = date("Y", $ts).". gada ".mb_strtolower($menesi[date("m", $ts)]);
			$count++;

			if(!$old_date || ($old_date != $date)) {
				if($old_date) { ?>
					<div class="List-sep"></div>
				<? } ?>
				<div class="TD-cat"><?=$res_date ?></div><?
			}
			$old_date = $date;
			?>
			<div class="TD-content">
				<div class="List-item">
					<a href="<?=$item->res_route ?>"><?=specialchars($item->res_name) ?></a>
				</div>
			</div><?
		}

		if(!$count) { ?>
			<div class="Info">Nav ierakstu</div><?
		}
	}
}
