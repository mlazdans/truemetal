<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/TemplateBlock.php');

class Template extends TemplateBlock
{
	var $modtime;
	var $root_dir = '.';

	function __construct($root_dir = '.')
	{
		$this->set_root($root_dir);
	} // Template

	function set_root($root_dir)
	{
		$this->root_dir = $root_dir;
		return;
	} // set_root

	private function __filename($file_name)
	{
		$file_name = "$this->root_dir/$file_name";
		if(!file_exists($file_name))
			$this->error('filename: file ['.$file_name.'] does not exists', E_USER_ERROR);

		return $file_name;
	} // __filename

	function set_file($ID, $file_name)
	{
		if($this->block_exists($ID)) {
			$this->error('set_file: block ['.$ID.'] already exists', E_USER_ERROR);
			return false;
		}

		$modtime = 0;
		$content = '';

		$file_path = $this->__filename($file_name);
		if($file_exists = file_exists($file_path)){
			$modtime = filemtime($file_path);
			$content = file_get_contents($file_path);
		}

		$this->blocks[$ID] = new TemplateBlock($this, $ID, $content);
		//$this->blocks[$ID]->block_parent = $this;
		$this->blocks[$ID]->modtime = $modtime;

		return true;
	} // set_file

} // class::Template


