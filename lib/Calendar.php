<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

class Calendar
{
	var $min_year;
	var $months=array(
		'',
		'Janvāris',
		'Februāris',
		'Marts',
		'Aprīlis',
		'Maijs',
		'Jūnijs',
		'Jūlijs',
		'Augusts',
		'Septembris',
		'Oktobris',
		'Novembris',
		'Decembris'
	);


	function Calendar()
	{
		$this->min_year = 2003;
	} // Calendar

	function parse_date(&$y, &$m, &$d)
	{
		$curr_y = date('Y');
		$curr_m = date('n');
		$curr_d = date('j');

		$y = (!$y || $y < $this->min_year || $y > $curr_y) ? $curr_y : $y;
		$m = (!$m || $m > 12 || $m < 1) ? $curr_m : $m;
			$days_in_month = date('t', mktime(0, 0, 0, $m, 1, $y));
		$d = (!$d || $d > $days_in_month || $d < 1) ? $curr_d : $d;

		if($m > $curr_m and $y >= $curr_y)
			$m = $curr_m;

		if($d > $curr_d and $m >= $curr_m and $y >= $curr_y) {
			$d = $curr_d;
			$m = $curr_m;
			$y = $curr_y;
		}
	} // parse_date

	function generate($y = 0, $m = 0, $d = 0)
	{
		global $sys_http_root;

		$this->parse_date($y, $m, $d);

		$curr_y = date('Y');
		$curr_m = date('n');
		$curr_d = date('j');

		$days_in_month = date('t', mktime(0, 0, 0, $m, 1, $y));
	$first_day = date('w', mktime(0, 0, 0, $m, 1, $y));
	$first_day = ($first_day) ? $first_day : 7;
	$weeks = ceil(($first_day + $days_in_month) / 7);
	$nextm_day = date('j', mktime(0, 0, 0, $m + 1, $d, $y));
		$prevm_day = date('j', mktime(0, 0, 0, $m - 1, $d, $y));

		$n_m = '<td><b>»</b></td>';
		$p_m = '<td><b>«</b></td>';
/*
		// naakamais meenesis
		if($m < 12)
			$n_m = '<a href="'.$sys_http_root."/archive/$y/".($m + 1).'/'.($d <= $nextm_day ? $d : $nextm_day).'/"><img src="'.$sys_http_root.'/img/cal_m_right.gif" width="8" height="10" alt="" border="0" onMouseOver="this.src=\''.$sys_http_root.'/img/cal_m_right_over.gif\'" onMouseOut="this.src=\''.$sys_http_root.'/img/cal_m_right.gif\'"></a>';
		else
			$n_m = '<a href="'.$sys_http_root.'/archive/'.($y + 1).'/1/'.($d <= $nextm_day ? $d : $nextm_day).'/"><img src="'.$sys_http_root.'/img/cal_m_right.gif" width="8" height="10" alt="" border="0" onMouseOver="this.src=\''.$sys_http_root.'/img/cal_m_right_over.gif\'" onMouseOut="this.src=\''.$sys_http_root.'/img/cal_m_right.gif\'"></a>';

		// iepr meenesis
		if($m > 1)
			$p_m = '<a href="'.$sys_http_root."/archive/$y/".($m - 1).'/'.($d <= $prevm_day ? $d : $d - $prevm_day).'/"><img src="'.$sys_http_root.'/img/cal_m_left.gif" width="8" height="10" alt="" border="0" onMouseOver="this.src=\''.$sys_http_root.'/img/cal_m_left_over.gif\'" onMouseOut="this.src=\''.$sys_http_root.'/img/cal_m_left.gif\'"></a>';
		else
			$p_m = '<a href="'.$sys_http_root.'/archive/'.($y - 1).'/12/'.($d <= $prevm_day ? $d : $d - $prevm_day).'/"><img src="'.$sys_http_root.'/img/cal_m_left.gif" width="8" height="10" alt="" border="0" onMouseOver="this.src=\''.$sys_http_root.'/img/cal_m_left_over.gif\'" onMouseOut="this.src=\''.$sys_http_root.'/img/cal_m_left.gif\'"></a>';
*/
		// naakamais meenesis
		if($m < 12)
			$n_m = '<td align="right"><a href="'.$sys_http_root."/archive/$y/".($m + 1).'/'.($d <= $nextm_day ? $d : $nextm_day).'/"><b>»</b></a></td>';
		else
			$n_m = '<td align="right"><a href="'.$sys_http_root.'/archive/'.($y + 1).'/1/'.($d <= $nextm_day ? $d : $nextm_day).'/"><b>»</b></a></td>';

		// iepr meenesis
		if($m > 1)
			$p_m = '<td><a href="'.$sys_http_root."/archive/$y/".($m - 1).'/'.($d <= $prevm_day ? $d : $d - $prevm_day).'/"><b>«</b></a></td>';
		else
			$p_m = '<td><a href="'.$sys_http_root.'/archive/'.($y - 1).'/12/'.($d <= $prevm_day ? $d : $d - $prevm_day).'/"><b>«</b></a></td>';

		$str = '<table border="0" cellpadding="2" cellspacing="1">';
		$str .= '<tr>'.$p_m.'<td valign="bottom" align="center" colspan="5" style="width: 100%"><b>'.$this->months[(integer)$m].'</b></td>'.$n_m.'</tr>';
		$str .= '<tr><td class="TD-cal-day">P</td><td class="TD-cal-day">O</td><td class="TD-cal-day">T</td><td class="TD-cal-day">C</td><td class="TD-cal-day">P</td><td class="TD-cal-day">S</td><td class="TD-cal-day">Sv</td></tr>';
		$str .= '</table>';
		$str .= '<table border="0" cellpadding="2" cellspacing="1" style="width: 100%">';

		// cik laucinji pa visam
		$total = $weeks * 7;
		for($r = 1; $r <= $total; ++$r) {
			if($r % 7 === 1)
				$str .= "<tr>";

			$t = $r - $first_day + 1;
			if($t <= $curr_d || ($y != $curr_y || $m != $curr_m))
				$a = '<a href="'.$sys_http_root."/archive/$y/$m/$t/".'">'.$t.'</a>';
			else
				$a = $t;

			$add = ' class="TD-cal"';
			// ja 6diena/7diena
			if($r % 7 == 0 || $r % 7 == 6)
				$add = ' class="TD-weekend"';

			// ja tekoshaa diena
			if($t == $d)
				$add = ' class="TD-current"';

			if($t > 0 && $t <= $days_in_month) {
				if($t > $curr_d && $add == ' class="TD-cal"')
					$str .= "<td class=\"TD-cal-furth\">$a</td>";
				else
					$str .= "<td$add>$a</td>";
			} else
				$str .= "<td$add>&nbsp;</td>";

			if($r % 7 === 0)
				$str .= "</tr>";
		}
		$str .= '</table>';
/*
		$n_y = '<td><img src="'.$sys_http_root.'/img/cal_m_right.gif" width="8" height="10" alt="" border="0" onMouseOver="this.src=\''.$sys_http_root.'/img/cal_m_right_over.gif\'" onMouseOut="this.src=\''.$sys_http_root.'/img/cal_m_right.gif\'"></td>';
		$p_y = '<td><img src="'.$sys_http_root.'/img/cal_m_left.gif" width="8" height="10" alt="" border="0" onMouseOver="this.src=\''.$sys_http_root.'/img/cal_m_left_over.gif\'" onMouseOut="this.src=\''.$sys_http_root.'/img/cal_m_left.gif\'"></td>';
		// naakamais gads
		if($y < $curr_y)
			$n_y = '<td><a href="'.$sys_http_root.'/archive/'.($y + 1)."/$m/".($d <= $nextm_day ? $d : $nextm_day).'/"><img src="'.$sys_http_root.'/img/cal_m_right.gif" width="8" height="10" alt="" border="0" onMouseOver="this.src=\''.$sys_http_root.'/img/cal_m_right_over.gif\'" onMouseOut="this.src=\''.$sys_http_root.'/img/cal_m_right.gif\'"></a></td>';

		// iepr gads
		if($y > $this->min_year)
			$p_y = '<td><a href="'.$sys_http_root.'/archive/'.($y - 1)."/$m/".($d <= $prevm_day ? $d : $d - $prevm_day).'/"><img src="'.$sys_http_root.'/img/cal_m_left.gif" width="8" height="10" alt="" border="0" onMouseOver="this.src=\''.$sys_http_root.'/img/cal_m_left_over.gif\'" onMouseOut="this.src=\''.$sys_http_root.'/img/cal_m_left.gif\'"></a></td>';
*/
		$n_y = '<td><b>»</b></td>';
		$p_y = '<td><b>«</b></td>';
		// naakamais gads
		if($y < $curr_y)
			$n_y = '<td><a href="'.$sys_http_root.'/archive/'.($y + 1)."/$m/".($d <= $nextm_day ? $d : $nextm_day).'/"><b>»</b></a></td>';

		// iepr gads
		if($y > $this->min_year)
			$p_y = '<td><a href="'.$sys_http_root.'/archive/'.($y - 1)."/$m/".($d <= $prevm_day ? $d : $d - $prevm_day).'/"><b>«</b></a></td>';

		$str .= '<table border="0" cellpadding="2" cellspacing="1"><tr><td colspan="3" style="height: 5px"></td></tr>';
		$str .= '<tr>'.$p_y.'<td align="center" style="width: 100%"><b>'.$y.'</b></td>'.$n_y.'</tr>';
		$str .= "</table>";

		return $str;
	} // generate

	function set_calendar(&$template, $y = 0, $m = 0, $d = 0)
	{
		$cal = $this->generate($y, $m, $d);
		$template->set_file('FILE_calendar', 'tmpl.calendar.php');
		$template->set_block_string('BLOCK_calendar_items', $cal);
		$template->parse_block('FILE_calendar');
		$template->set_var('right_item_data', $template->get_parsed_content('FILE_calendar'), 'BLOCK_right_item');
		$template->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // set_calendar

} // Calendar
