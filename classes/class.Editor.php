<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// WYSIWYG editor

require_once('../includes/inc.utils.php');

class Editor
{
	var $ID;

	function Editor()
	{
		srand(getmicrotime());
		$randval = rand();
		$this->ID = md5($randval);
	} // Editor

} // Editor
