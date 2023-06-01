<?php declare(strict_types = 1);

use dqdp\Template;

class AdminModule extends MainModule
{
	var $module_name;
	var $title;
	var Template $Index;

	function __construct(string $module_name, string $main_file = 'index.tpl')
	{
		parent::__construct($module_name, "admin/$main_file");
		$this->module_name = $module_name;

		$this->init();
	} // __construct

	function init()
	{
		global $admin_modules, $module_root;

		// $this->set_var('upload_root', $GLOBALS['sys_upload_http_root']);
		$this->Index->set_var('year', date('Y'));
		$this->Index->set_var('module_root', $module_root);

		$this->Index->set_var($this->module_name.'_class', 'TD-menu-active');

		foreach($admin_modules as $mod=>$val)
		{
			if($val)
			{
				$this->Index->set_var('adminmodule_id', $mod);
				$this->Index->set_var('adminmodule_name', $val);
				//if($mod == $this->module_name)
				if(preg_match("/^$mod\//", "$this->module_name/"))
					$this->Index->set_var('adminmodule_class', 'TD-menu-active');
				else
					$this->Index->set_var('adminmodule_class', 'TD-menu');
				$this->Index->parse_block('BLOCK_adminmodules', TMPL_APPEND);
			}
		}
	} // init

}
