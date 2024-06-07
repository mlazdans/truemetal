<?php declare(strict_types = 1);

class EventsTemplate extends RightItemAbstractTemplate
{
	public ViewResForumCollection $items;
	public string $search_q = '';

	function __construct()
	{
		$this->name = "Aktuāli";
	}

	protected function out(): void
	{
		$c = 0;
		$tc = $this->items->count();
		$more_events = false;
		foreach($this->items as $item)
		{
			$ts = strtotime($item->event_startdate);
			$D = date('j', $ts);
			$Dw = date('w', $ts);
			$M = date('m', $ts);

			$diff = floor(($ts - time()) / (3600 * 24));

			$event_class = "";
			$event_title = specialchars($D.". ".get_month($M - 1).", ".get_day($Dw - 0));
			$event_name = specialchars($item->res_name);
			$event_url = $item->res_route;

			if($diff<2){
				$event_class = " actual0";
			} elseif($diff<4){
				$event_class = " actual1";
			} elseif($diff<7){
				$event_class = " actual2";
			}

			if($tc > 5)
			{
				if($c==5) {
					$more_events = true;
					?>
					<div class="List-item">
						<a
							style="font-style: italic;"
							href="#"
							onclick="$('#MoreEvents').slideToggle();this.text=(this.text == '-vairāk-' ? '-mazāk-' : '-vairāk-');return false;"
						>-vairāk-</a>
					</div>
					<div style="display: none;" id="MoreEvents"><?
				}
			}
			$c++;
			?>
			<div class="List-item<?=$event_class ?>">
				<a href="<?=$event_url ?>" title="<?=$event_title ?>" style="display: block;"><?=$event_name ?></a>
			</div><?
		}

		if($more_events){ ?>
			</div><?
		}
	}
}
