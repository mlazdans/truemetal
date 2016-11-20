<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

/* =========================================================== */
/* TemplateBlock
/* =========================================================== */
/* aprakstam templates block modeli
/*
/* =========================================================== */
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

	var $debug = true;
	var $die_on_error = false;

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	TemplateBlock (string block_id, string content [, string undefined])
	/* -----------------------------------------------------------
	/* konstruktors - uzstaada visko vajadziigu
	/* ----------------------------------------------------------- */
	function __construct($ID, $str_content, $str_undefined = 'remove')
	{
		$this->ID = $ID;
		$this->slash = chr(92).chr(92);
		$this->set_undefined($str_undefined);
		$this->content = $str_content;
		//$this->blocks = $this->__find_blocks();
		$this->__find_blocks();

		return true;
	} // __construct

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	__find_blocks()
	/* -----------------------------------------------------------
	/* samekleejam visus blokus esosajaa blokaa
	/*
	/* mega krutaaa regulaaraa izteiksme (regexp)
	/* $m[1] massiivs - vaidzeetu glabaaties bloku nosaukmiem
	/* $m[2] massiivs - dazaadi parametri <- veel jaatestee
	/* $m[3] massiivs - tipa kontents, tas kas pa vidu
	/* ----------------------------------------------------------- */
	function __find_blocks()
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
				$this->blocks[$id] = new TemplateBlock($id, $m[3][$c], $this->undefined);
				$this->blocks[$id]->block_parent = $this;

				$arr_attributes = explode(' ', strtolower($m[2][$c]));
				$this->blocks[$id]->attributes['disabled'] = in_array('disabled', $arr_attributes);
			}

			return $this->blocks;
		} else
			return false;
	} // __find_blocks

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	halt (string err_msg)
	/* -----------------------------------------------------------
	/* apstopee, ja vaig
	/* ----------------------------------------------------------- */
	function halt($str_msg)
	{
		if($this->debug) {
			if($this->die_on_error)
				die($str_msg);
			else
				print $str_msg;
		}
		return false;
	} // halt

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	parse ([boolean append])
	/* -----------------------------------------------------------
	/* apstraadaa bloku un taa apaksblokus, t.i., aizvieto blokus
	/* ar apaksbloku datiem. saliek mainiigo veertiibas ar
	/* __parse_vars()
	/* ----------------------------------------------------------- */
	function parse($bln_append = false)
	{
		/* ja bloks sleegts */
		if($this->attributes['disabled'])
			return;

		/* reset childs */
		/*
		if($bln_append) {
			foreach($this->blocks as $block_id => $object) {
				$object->reset();
			}
		}
		*/

		/* ja jau noparseets */
		if($this->parsed_count && !$bln_append) {
			return $this->__get_parsed_content();
		}

		/* ja jauna parseeshana */
		$parsed_content = $this->__parse_vars();

		/* ja blokaa veel ir bloki */
		foreach($this->blocks as $block_id => $object)
		{
			$block_content = $object->parse();
			$patt = '/\s*<!--\s+BEGIN\s+' . $block_id . '\s+[^<]*-->.*<!--\s+END\s+' . $block_id . '\s+-->\s*/smi';
			preg_match_all($patt, $parsed_content, $m);
			/*
			if($block_id == 'BLOCK_gallery_data'){
				printr($m);
			}*/
			foreach($m[0] as $mm) {
				$parsed_content = str_replace($mm, $block_content, $parsed_content);
			}
		}

		if($bln_append) {
			$this->parsed_content .= $parsed_content;
		} else {
			$this->parsed_content = $parsed_content;
		}

		$this->parsed_count++;
		$this->last_parsed_content = $parsed_content;

		$cont = $this->__get_parsed_content();

		/* reset childs */
		if($bln_append) {
			foreach($this->blocks as $block_id => $object) {
				$object->reset();
			}
		}

		return $cont;
	} // parse

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	__get_parsed_content()
	/* -----------------------------------------------------------
	/* dabuujam visus parseejumus kaa stringu
	/* ----------------------------------------------------------- */
	function __get_parsed_content()
	{
		return $this->parsed_content;
		//return join('', $this->parsed_content);
	} // __get_parsed_content

	function find_var($k, $d = 0)
	{
		if(isset($this->vars[$k])) {
			return $this->vars[$k];
		} else if($this->block_parent) {
			return $this->block_parent->find_var($k, $d + 1);
		}

		return '';
	} // find_var

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	__parse_vars()
	/* -----------------------------------------------------------
	/*  saliek mainiigo veertiibas
	/* ----------------------------------------------------------- */
	function __parse_vars()
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

		/*
		foreach($this->block_vars as $k)
		{
			if(!isset($vars_cache[$k]))
				$vars_cache[$k] = $this->find_var($k);

			$patt[] = '/{'.$k.'}/';
			$repl[] = $vars_cache[$k];
		}
		*/

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

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	set_var (string variable_id, mixed value [, boolean parent_only])
	/* -----------------------------------------------------------
	/* piesaista blokam mainiigo ar veertiibu
	/* defaultaa veertiiba nepieskiras apaksblokiem
	/* ja apaksblokiem vajag pieskirt, tad pieliekam
	/* $bln_parent_only = true
	/* ----------------------------------------------------------- */
	function set_var($str_var_id, $value, $bln_parent_only = false)
	{
		$this->vars[$str_var_id] = $value;
		return true;
	} // set_var

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	set_array(array values [, boolean parent_only])
	/* -----------------------------------------------------------
	/* uzstaadam masiivu ar datiem kaa veertiibas
	/* masiivam jaabuut $values['key1'], $values['some_other_key']
	/* formaa, t.i., indexi stringi.
	/* parent_only, ja veertiibas nevaig uzstaadiit apaksblokiem
	/* ----------------------------------------------------------- */
	function set_array($arr_array, $bln_parent_only = false, $prefix = '')
	{
		foreach($arr_array as $key => $value) {
			$this->set_var($key.$prefix, $value, $bln_parent_only);
		}
	} // set_array

	function set_array_prefix($arr_array, $prefix, $ID = '')
	{
		return $this->set_array($arr_array, false, $prefix);
	} // set_array_prefix

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	set_global (string variable_id, mixed value)
	/* -----------------------------------------------------------
	/* uzstaadam mainiigaa veertiibu globaali, t.i.,
	/* arii katram apaksblokam
	/* ----------------------------------------------------------- */
	function set_global($str_var_id, $value)
	{
		if($this->set_var($str_var_id, $value)) {
			foreach($this->blocks as $ID => $object)
				$this->blocks[$ID]->set_global($str_var_id, $value);

			return true;
		}

		return false;
	} // set_global

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	get_block (string block_id)
	/* -----------------------------------------------------------
	/* samekleejam bloku ar nosaukumu ID
	/* ----------------------------------------------------------- */
	function &get_block($ID)
	{
		# fetch from cache
		if(isset($this->blocks_cache[$ID]))
			return $this->blocks_cache[$ID];

		if(isset($this->blocks[$ID]))
		{
			$this->blocks_cache[$ID] = &$this->blocks[$ID];
			return $this->blocks[$ID];
		}

		foreach($this->blocks as $file_id => $object)
		{
			if($block =& $this->blocks[$file_id]->get_block($ID))
			{
				$this->blocks_cache[$ID] = &$block;
				return $block;
			}
		}

		$block = false;
		return $block;
	} // get_block
	/*
	function &get_block($ID)
	{
		//reset($this->blocks);

		if(isset($this->blocks[$ID]))
			return $this->blocks[$ID];

		foreach($this->blocks as $block_id => $object)
			if($block =& $this->blocks[$block_id]->get_block($ID))
				return $block;

		$block = false;
		return $block;
	} // get_block
	*/

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	reset (boolean parent_only)
	/* -----------------------------------------------------------
	/* uzstaadam visus mainiigos uz neko :)
	/* ----------------------------------------------------------- */
	function reset($bln_parent_only = false)
	{
		if(empty($this->blocks)) {
			//$this->parsed_content = array();
			$this->parsed_content = '';
			$this->last_parsed_content = '';
			$this->parsed_count = 0;
		} else {
			//$this->parsed_content = array();
			$this->parsed_content = '';
			$this->last_parsed_content = '';
			$this->parsed_count = 0;
			foreach($this->blocks as $block_id => $object)
			{
				$object->reset($bln_parent_only);
			}
		}

		return true;
	} // reset

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	block_isset (string block_id)
	/* -----------------------------------------------------------
	/* vai bloks ID existee?
	/* ----------------------------------------------------------- */
	function block_isset($ID)
	{
		if(isset($this->blocks[$ID]))
			return true;

		foreach($this->blocks as $block)
			if($block->block_isset($ID))
				return true;

		return false;
	} // block_isset

	function block_exists($ID)
	{
		return $this->block_isset($ID);
	} // block_exists

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	set_undefined (string undefined)
	/* -----------------------------------------------------------
	/* uzstaadam, ko dariit ar nedefineetiem mainiigiem
	/* ----------------------------------------------------------- */
	function set_undefined($str_undefined)
	{
		$this->undefined = $str_undefined;
		return true;
	} // set_undefined

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	invalid (string var_id)
	/* -----------------------------------------------------------
	/* paarbaudam, vai $string der par mainiigaa nosaukumu
	/* tipa, saakas ar (a-z vai A-Z vai _ vai ascii 127-255)
	/* un turpinas (a-z vai A-Z vai 0-9 _ vai ascii 127-255)
	/* ----------------------------------------------------------- */
	function invalid($str_var_id)
	{
		return false;
		//return !preg_match('/^[a-zA-Z_\x7F-\xFF][a-zA-Z0-9_\x7F-\xFF]*/', $str_var_id) or !$str_var_id;
	} // invalid

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	set_attribute (string attribute, mixed value)
	/* -----------------------------------------------------------
	/* uzstaadam atribuutu, iespeejamie atribuuti un to veertiibas
	/* nosaukums - veertiibas [defaultaa] - apraksts
	/* disabled - false/true [false] iespeeja izsleegt
	/* ----------------------------------------------------------- */
	//function set_attribute($str_attribute, $value)
	function set_attribute()
	{
		list($str_attribute, $value) = func_get_args();

		if(isset($this->attributes[$str_attribute]))
			return $this->attributes[$str_attribute] = $value;
		else
			$this->halt('set_attribute: no such attribute ['.$str_attribute.']');

		return false;
	} // set_attribute

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	set_block_string (string contents)
	/* -----------------------------------------------------------
	/* uzstaadam blokam parseejamos datus
	/* liidziigi, kaa set_file, tachu datus uzstaada nevis no faila,
	/* bet no stringa
	/* ----------------------------------------------------------- */
	//function set_block_string($content = '') {
	function set_block_string()
	{
		list($content) = func_get_args();
		return $this->content = $content;
	} // set_block_string

	/* ----------------------------------------------------------- */
	/* TemplateBlock
	/*	delete_block (string block_id)
	/* -----------------------------------------------------------
	/* izdzēš bloku
	/* ----------------------------------------------------------- */
	function delete_block($ID)
	{
		unset($this->blocks[$ID]);
	} // delete_block
}

