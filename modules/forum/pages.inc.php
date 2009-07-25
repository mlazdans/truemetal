<?php

$total_pages = ceil($forum_count / $fpp);

// uzstaadam pages
if($total_pages > 1)
{
	$_pvs = $pages_visible_to_sides;
	$sep_count = 0;
	$visible_pages = $_pvs + (
		$page_id > $_pvs ? ($total_pages - $page_id > $_pvs ? 0 : $_pvs - ($total_pages - $page_id))
		: $_pvs - $page_id + 1
	);

	$side_sep = 1 + (
		$page_id > $_pvs ? ($total_pages - $page_id > $_pvs ? 0 : 1)
		: 1
	);

	$template->enable('BLOCK_is_pages');
	for($p = 1; $p <= $total_pages; $p++)
	{
		$p_id = ($total_pages > 10) && ($p < 10) ? "0$p" : $p;
		$template->set_var('page_id', $p_id, 'BLOCK_is_pages');

		# atziimee, tekoshu page
		if($p == $page_id)
		{
			$template->set_var('page_style', ' style="color: #00AC00;"', 'BLOCK_is_pages');
		} else {
			$template->set_var('page_style', '', 'BLOCK_is_pages');
		}

		# skippo pa nevajadziigaas pages
		if(abs($p - $page_id) > $visible_pages)
		{
			$sep_count++;
			$template->set_var('page_seperator', (abs($p - $page_id) > $visible_pages) && (abs($p - $page_id) - $visible_pages <= $side_sep) ? '[..]' : '', 'BLOCK_is_pages');
			$template->disable('BLOCK_page_switcher');
		} else {
			$template->enable('BLOCK_page_switcher');
			$template->set_var('page_seperator', '', 'BLOCK_is_pages');
		}

		$template->parse_block('BLOCK_page', TMPL_APPEND);

	}
}

# prev
$template->set_var('prev_page_id', ($page_id > 1) ? $page_id - 1 : $page_id, 'BLOCK_is_pages');
/*
if($page_id > 1)
{
	//$template->enable('BLOCK_page_prev');
} else {
	$template->set_var('prev_page_id', $page_id - 1, 'BLOCK_is_pages');
	//$template->enable('BLOCK_page_prev_disabled');
}
*/

# next
$template->set_var('next_page_id', ($page_id < $total_pages) ? $page_id + 1 : $page_id, 'BLOCK_is_pages');
/*
if($page_id < $total_pages)
{
	$template->set_var('next_page_id', $page_id + 1, 'BLOCK_is_pages');
	$template->enable('BLOCK_page_next');
} else {
	$template->enable('BLOCK_page_next_disabled');
}
*/
// --

//$template->parse_block('BLOCK_is_pages');
$template->set_var('pages_bottom', $template->get_parsed_content('BLOCK_is_pages'), 'BLOCK_middle');

