<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// pats pats...

require_once('lib/Template.php');

class AdminModule extends Template
{

	var $module_name;
	var $title;

	function __construct($template_root, $module_name = '',
		$str_main_file = 'index.tpl', $str_undefined = 'remove')
	{

		$this->set_module_name($module_name ? $module_name : get_class($this));

		$this->set_root($template_root);
		$this->set_undefined($str_undefined);

		/* ielaadeejam failus */
		$this->set_file("FILE_index", $str_main_file);
		$this->set_file("FILE_middle", "".$this->module_name.".tpl");
		$this->copy_block("BLOCK_middle", "FILE_middle");

		$this->init();

		return true;
	} // __construct

	function init()
	{
		global $admin_modules, $_USER, $sys_encoding;

		$this->set_global('encoding', $GLOBALS['sys_encoding']);
		$this->set_global('upload_root', $GLOBALS['sys_upload_http_root']);
		$this->set_global('http_root', $GLOBALS['sys_http_root']);
		$this->set_global('admin_root', $GLOBALS['admin_root']);
		$this->set_global('module_root', $GLOBALS['module_root']);
		$this->set_global('year', date('Y'));
		$this->set_global('script_version', $GLOBALS['sys_script_version']);

		$this->set_global('USER_name', $_USER['user_name']);

		foreach($admin_modules as $mod=>$val)
		{
			// ja uzstaadiits modulis
			if($val)
			{
				$this->set_var('adminmodule_id', $mod);
				$this->set_var('adminmodule_name', $val);
				//if($mod == $this->module_name)
				if(preg_match("/^$mod\//", "$this->module_name/"))
					$this->set_var('adminmodule_class', 'TD-menu-active');
				else
					$this->set_var('adminmodule_class', 'TD-menu');
				$this->parse_block('BLOCK_adminmodules', TMPL_APPEND);
			}
		}
	} // init

	function set_title($str_title)
	{
		$this->title = $str_title;
		$this->set_global('title', $this->title);
	} // set_title

	function set_module_name($module_name)
	{
		$this->module_name = $module_name;
	} // set_module_name

	function out()
	{
		print $this->parse_file("FILE_index");
	} // out

} // AdminModule

