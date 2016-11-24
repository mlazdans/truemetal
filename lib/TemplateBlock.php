<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

define('TMPL_APPEND', true);

class TemplateBlock
{
	var $ID;
	var $vars = array();
	var $blocks = array();
	var $blocks_cache = array();

	var $block_parent = null;
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

	function __construct($ID, $str_content)
	{
		$this->ID = $ID;
		$this->slash = chr(92).chr(92);
		$this->content = $str_content;
		$this->__find_blocks();

		return true;
	} // __construct

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
				$this->blocks[$id] = new TemplateBlock($id, $m[3][$c]);
				$this->blocks[$id]->block_parent = $this;

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

	protected function halt($msg, $e = E_USER_WARNING)
	{
		trigger_error($msg, $e);
	} // halt

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
			$this->halt('get_parsed_content: block ['.$ID.'] not found!');
			return false;
		}

		return $block->parsed_content;
	} // get_parsed_content

	function find_var($k, $d = 0)
	{
		if(isset($this->vars[$k])) {
			return $this->vars[$k];
		} else if($this->block_parent) {
			return $this->block_parent->find_var($k, $d + 1);
		}

		return '';
	} // find_var

	function set_var($var_id, $value, $ID = '')
	{
		$block = $this;
		if($ID && !($block = $this->get_block($ID))){
			$this->halt('set_var: block ['.$ID.'] not found!');
			return false;
		}

		$block->vars[$var_id] = $value;

		return false;
	} // set_var

	function set_array(Array $array, $ID = '')
	{
		$block = $this;
		if($ID && !($block = $this->get_block($ID))){
			$this->halt('set_array: block ['.$ID.'] not found!');
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
			$this->halt('reset: block ['.$ID.'] not found!');
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
			$this->halt('set_attribute: block ['.$ID.'] not found!');
			return false;
		}

		if(isset($block->attributes[$attribute]))
			return $block->attributes[$attribute] = $value;

		return false;
	} // set_attribute




	function get_block($ID)
	{
		# fetch from cache
		if(isset($this->blocks_cache[$ID]))
			return $this->blocks_cache[$ID];

		if(isset($this->blocks[$ID])){
			$this->blocks_cache[$ID] = $this->blocks[$ID];
			return $this->blocks[$ID];
		}

		foreach($this->blocks as $block_id => $object){
			if($block = $this->blocks[$block_id]->get_block($ID)){
				$this->blocks_cache[$ID] = $block;
				return $block;
			}
		}

		return false;
	} // get_block

	function copy_block($ID_from, $ID_to)
	{
		if(!($block1 = $this->get_block($ID_from))){
			$this->halt('copy_block: block ['.$ID_from.'] not found!');
			return false;
		}

		if(!($block2 = $this->get_block($ID_to))){
			$this->halt('copy_block: block ['.$ID_to.'] not found!');
			return false;
		}

		# tagat noskaidrosim, vai block1 nav zem block2
		if(($block3 = $block2->get_block($ID_from))){
			$this->halt('copy_block: cannot copy ['.$ID_to.'] to ['.$ID_from.']. ['.$ID_from.'] is a child of ['.$ID_to.']');
			return false;
		}

		# paarkopeejam paareejos parametrus
		$block1->vars = &$block2->vars;
		$block1->blocks = $block2->blocks;
		$block1->parsed_content = $block2->parsed_content;
		$block1->content = $block2->content;

		# Uzstādam parentu
		$block2->block_parent = $block1;

		return true;
	} // copy_block

	function parse_block($ID, $append = false)
	{
		if($block = $this->get_block($ID)){
			return $block->parse($append);
		} else {
			$this->halt('parse_block: block ['.$ID.'] not found!');
			return false;
		}
	} // parse_block

	function set_block_string($ID, $content)
	{
		if($block = $this->get_block($ID)){
			$this->halt('set_block_string: block ['.$ID.'] not found!');
			return false;
		}

		return $block->content = $content;
	} // set_block_string

	function block_isset($ID)
	{
		if($block = $this->get_block($ID))
			return true;

		return false;
	} // block_isset

	function block_exists($ID)
	{
		return $this->block_isset($ID);
	} // block_exists

} // class::TemplateBlock

