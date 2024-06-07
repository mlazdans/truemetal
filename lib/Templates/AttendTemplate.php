<?php declare(strict_types = 1);

class AttendTemplate extends AbstractTemplate
{
	public int $l_id;
	public int $res_id;
	public string $event_startdate;
	public ?ViewAttendCollection $attendees = null;

	protected function out(): void
	{
		$me_attended = false;
		$ts = strtotime(date('d.m.Y', strtotime($this->event_startdate))) + 24 * 3600;
		$is_active = time() < $ts;

		?>
		<div class="Forum-cat" id="attendees<?=$this->res_id ?>">Solās ierasties:
			<? if($this->attendees) {
				$c = $this->attendees->count();
				$k = 0;
				foreach($this->attendees as $item)
				{
					if($item->a_attended && ($this->l_id == $item->l_id)){
						$me_attended = true;
					}

					$l_nick = specialchars($item->l_nick);
					$sep = (++$k < $c ? ', ' : '');
					if(!$item->a_attended){
						$l_nick = "<strike>$l_nick</strike>";
					} ?>
					<a href="/user/profile/<?=$item->l_hash ?>/" class="ProfilePopup" data-hash="<?=$item->l_hash ?>"><?=$l_nick ?></a><?=$sep ?>
				<? } ?>
			<? } ?>

			<? if($is_active) {?>
				<? if($me_attended) { ?>
					<a href="/attend/<?=$this->res_id ?>/off/" class="button" onclick="Truemetal.AttendNo('<?=$this->res_id ?>'); return false;">Es tomēr nenāks!</a>
				<? } else { ?>
					<a href="/attend/<?=$this->res_id ?>/" class="button" onclick="Truemetal.Attend('<?=$this->res_id ?>'); return false;">Es ar' nāks!!</a>
				<? } ?>
			<? } ?>
		</div><?
	}
}
