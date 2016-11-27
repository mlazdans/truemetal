<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

define('TMPL_APPEND', true);

class TemplateBlock
{
	public static $blocks_cache = array();

	var $ID;
	var $vars = array();
	var $blocks = array();

	var $parent_block = null;
	var $block_vars = null;

	var $content = '';
	var $parsed_content = '';
	var $last_parsed_content = '';
	var $parsed_count = 0;

	var $slash;
	var $undefined = 'remove';
	var $attributes = array(
		'disabled' => false
	);

	function __construct(TemplateBlock $parent, $ID, $content)
	{
		$this->ID = $ID;
		$this->slash = chr(92).chr(92);
		$this->parent_block = $parent;
		$this->content = $content;
		$this->__find_blocks();
		if(!isset(self::$blocks_cache[$ID]))
			self::$blocks_cache[$ID] = $this;

		return true;
	} // __construct

	private function __get_root()
	{
		if($this->parent_block == null){
			return $this;
		} else {
			return $this->parent_block->__get_root();
		}
	} // __get_root

	private function __find_blocks()
	{
		$patt = '/<!--\s+BEGIN\s+(.*)\s+(.*)-->(.*)<!--\s+END\s+\1\s+-->/smU';
		preg_match_all($patt, $this->content, $m);

		/* ja atrasts kaads bloks */
		if(isset($m[1]))
		{
			$int_count = count($m[1]);
			for($c = 0; $c < $int_count; $c++)
			{
				$id = $m[1][$c];
				$this->blocks[$id] = new TemplateBlock($this, $id, $m[3][$c]);

				$arr_attributes = explode(' ', strtolower($m[2][$c]));
				$this->blocks[$id]->attributes['disabled'] = in_array('disabled', $arr_attributes);
			}

			return $this->blocks;
		} else
			return false;
	} // __find_blocks

	private function __parse_vars()
	{
		$content = $this->content;
		$patt = array();
		$repl = array();
		$vars_cache = array();

		if($this->block_vars === null)
		{
			preg_match_all("/{(.*)}/U", $content, $m);
			$this->block_vars = $m[1];
		}

		foreach($this->block_vars as $k)
		{
			$patt[] = '/{'.$k.'}/';
			$p = array("/([$this->slash])+/", "/([\$])+/");
			$r = array("\\\\$1", "\\\\$1");
			if(!isset($vars_cache[$k]))
				$vars_cache[$k] = $this->find_var($k);

			$repl[] = preg_replace($p, $r, $vars_cache[$k]);
		}

		$content = preg_replace($patt, $repl, $content);

		/*
		switch ($this->undefined)
		{
			case 'remove':
				$content = preg_replace('/([^\\\])?{'.$variable_pattern.'}/U', '\1', $content);
				$content = preg_replace('/\\\{('.$variable_pattern.')}/', '{\1}', $content);
				return $content;
				//preg_replace('/\\\{'.$variable_pattern.'\}/', 'aaa{'.$variable_pattern.'}', $content);
				//return preg_replace('/(\n+|\r\n+)?{'.$variable_pattern.'}(\n+|\r\n+)?/', '', $content);
				break;
			case 'comment':
				return preg_replace('/{('.$variable_pattern.')}/', '<!-- UNDEFINED \1 -->', $content);
				//return preg_replace('/(\n+|\r\n+)?{('.$variable_pattern.')}(\n+|\r\n+)?/', '<!-- UNDEFINED \1 -->', $content);
				break;
			case 'warn':
				return preg_replace('/{('.$variable_pattern.')}/', 'UNDEFINED \1', $content);
				//return preg_replace('/(\n+|\r\n+)?{('.$variable_pattern.')}(\n+|\r\n+)?/', 'UNDEFINED \1', $content);
				break;
		}
		*/

		return $content;
	} // __parse_vars

	protected function error($msg, $e = E_USER_WARNING)
	{
		trigger_error($msg, $e);
	} // error

	function get_block($ID, $parent = null)
	{
		# fetch from cache
		if(isset(self::$blocks_cache[$ID]))
			return self::$blocks_cache[$ID];

		return false;
	} // get_block

	function get_block_under($ID)
	{
		if(isset($this->blocks[$ID])){
			return $this->blocks[$ID];
		}

		foreach($this->blocks as $block_id => $object){
			if($block = $this->blocks[$block_id]->get_block_under($ID)){
				return $block;
			}
		}

		return false;
	} // get_block

	function parse($append = false)
	{
		# ja bloks sleegts
		if($this->attributes['disabled'])
			return;

		# ja jau noparseets
		if($this->parsed_count && !$append) {
			return $this->get_parsed_content();
		}

		# ja jauna parseeshana
		$parsed_content = $this->__parse_vars();

		# ja blokaa veel ir bloki
		foreach($this->blocks as $block_id => $object)
		{
			$block_content = $object->parse();
			$patt = '/\s*<!--\s+BEGIN\s+' . $block_id . '\s+[^<]*-->.*<!--\s+END\s+' . $block_id . '\s+-->\s*/smi';
			preg_match_all($patt, $parsed_content, $m);
			foreach($m[0] as $mm) {
				$parsed_content = str_replace($mm, $block_content, $parsed_content);
			}
		}

		if($append) {
			$this->parsed_content .= $parsed_content;
		} else {
			$this->parsed_content = $parsed_content;
		}

		$this->parsed_count++;
		$this->last_parsed_content = $parsed_content;

		$cont = $this->get_parsed_content();

		# reset childs
		if($append) {
			foreach($this->blocks as $block_id => $object) {
				$object->reset();
			}
		}

		return $cont;
	} // parse

	function get_parsed_content($ID = '')
	{
		$block = $this;
		if($ID && !($block = $this->get_block($ID))){
			$this->error('get_parsed_content: block ['.$ID.'] not found!');
			return false;
		}

		return $block->parsed_content;
	} // get_parsed_content

	function find_var($k, $d = 0)
	{
		if(isset($this->vars[$k])) {
			return $this->vars[$k];
		} else if($this->parent_block) {
			return $this->parent_block->find_var($k, $d + 1);
		}

		return '';
	} // find_var

	function set_var($var_id, $value, $ID = '')
	{
		$block = $this;
		if($ID && !($block = $this->get_block($ID))){
			$this->error('set_var: block ['.$ID.'] not found!');
			return false;
		}

		$block->vars[$var_id] = $value;

		return false;
	} // set_var

	function set_array(Array $array, $ID = '')
	{
		$block = $this;
		if($ID && !($block = $this->get_block($ID))){
			$this->error('set_array: block ['.$ID.'] not found!');
			return false;
		}

		foreach($array as $key => $value) {
			$block->set_var($key, $value);
		}
	} // set_array

	function reset($ID = '')
	{
		$block = $this;
		if($ID && !($block = $this->get_block($ID))){
			$this->error('reset: block ['.$ID.'] not found!');
			return false;
		}

		$block->parsed_content = '';
		$block->last_parsed_content = '';
		$block->parsed_count = 0;
		if(!empty($block->blocks)){
			$block->parsed_content = '';
			$block->last_parsed_content = '';
			$block->parsed_count = 0;
			foreach($block->blocks as $block_id=>$object){
				$object->reset();
			}
		}

		return true;
	} // reset

	function enable($ID = '')
	{
		return $this->set_attribute('disabled', false, $ID);
	} // enable

	function disable($ID = '')
	{
		return $this->set_attribute('disabled', true, $ID);
	} // disable

	function set_attribute($attribute, $value, $ID = '')
	{
		$block = $this;
		if($ID && !($block = $this->get_block($ID))){
			$this->error('set_attribute: block ['.$ID.'] not found!');
			return false;
		}

		if(isset($block->attributes[$attribute]))
			return $block->attributes[$attribute] = $value;

		return false;
	} // set_attribute

	function copy_block($ID_to, $ID_from)
	{
		if(!($block_to = $this->get_block($ID_to))){
			$this->error('copy_block: block ['.$ID_to.'] not found!');
			return false;
		}

		if(!($block_from = $this->get_block($ID_from))){
			$this->error('copy_block: block ['.$ID_from.'] not found!');
			return false;
		}

		# tagat noskaidrosim, vai block_to nav zem block_from
		if(($block_under_test = $block_from->get_block_under($ID_to))){
			$this->error('copy_block: ['.$ID_from.'] is a child of ['.$ID_to.']');
			return false;
		}

		# paarkopeejam paareejos parametrus
		$block_to->vars = &$block_from->vars;
		$block_to->blocks = $block_from->blocks;
		$block_to->parsed_content = $block_from->parsed_content;
		$block_to->content = $block_from->content;

		# Uzstādam parentu
		$block_from->parent_block = $block_to;

		return true;
	} // copy_block

	function parse_block($ID, $append = false)
	{
		if($block = $this->get_block($ID)){
			return $block->parse($append);
		} else {
			$this->error('parse_block: block ['.$ID.'] not found!');
			return false;
		}
	} // parse_block

	function set_block_string($ID, $content)
	{
		if($block = $this->get_block($ID)){
			$this->error('set_block_string: block ['.$ID.'] not found!');
			return false;
		}

		return $block->content = $content;
	} // set_block_string

	function block_exists($ID)
	{
		if($block = $this->get_block($ID))
			return true;

		return false;
	} // block_exists

} // class::TemplateBlock

