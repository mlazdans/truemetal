<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// niknaa templashu engine

require_once("lib/TemplateBlock.php");

define('TMPL_APPEND', true);

/* =========================================================== */
/* Template
/* =========================================================== */
/* aprakstam templates modeli, kursh tiek paplasinaats
/* no TemplateBlock
/* =========================================================== */
class Template extends TemplateBlock
{
	var $modtime;
	var $APC;
	var $root_dir = '.';
	//var $files = array();

	/* ----------------------------------------------------------- */
	/* Template
	/*	Template ([string root_dir [, string undefined]])
	/* -----------------------------------------------------------
	/* konstruktors - uzstaada visko vajadziigu
	/* root_dir - dir, kuraa atradiisies template
	/* undefined - ko dariit ar nedefineetiem mainiigajiem
	/* ----------------------------------------------------------- */
	function Template($str_root_dir = '.', $str_undefined = 'remove')
	{
		$this->APC = false && extension_loaded("APC");
		$this->set_root($str_root_dir);
		$this->set_undefined($str_undefined);
	} // Template

	/* ----------------------------------------------------------- */
	/* Template
	/*	set_root (string root_dir)
	/* -----------------------------------------------------------
	/* uzstaadam templates direktoriju
	/* ----------------------------------------------------------- */
	function set_root($str_root_dir)
	{
		$this->root_dir = $str_root_dir;
		$this->root_hash = md5($this->root_dir);

		return;
	} // set_root

	/* ----------------------------------------------------------- */
	/* Template
	/*	set_root (string file_name)
	/* -----------------------------------------------------------
	/* apstraadaajam failu nosaukumu - ja prieksaa nav
	/* / vai a-z: (tipa sisteemas root), tad pieliekam klaat
	/* templates dir root
	/* ----------------------------------------------------------- */
	function filename($str_file_name)
	{
		if(!$this->is_root($str_file_name))
			$str_file_name = $this->root_dir.'/'.$str_file_name;

		if(!file_exists($str_file_name))
			$this->halt('filename: file ['.$str_file_name.'] does not exists');

		return $str_file_name;
	} // filename

	/* ----------------------------------------------------------- */
	/* Template
	/*	block_isset (string ID)
	/* -----------------------------------------------------------
	/* vai bloks $ID eksistee?
	/* ----------------------------------------------------------- */
	function block_isset($ID)
	{
		if($b =& $this->get_block($ID))
			return true;
		else
			return false;
	} // block_isset

	/* ----------------------------------------------------------- */
	/* Template
	/*	get_parsed_content (string ID)
	/* -----------------------------------------------------------
	/* dabounam bloka ID noparseetos datus
	/* ----------------------------------------------------------- */
	function get_parsed_content($ID)
	{
		if($ID) {
			if($block =& $this->get_block($ID))
				return $block->__get_parsed_content();
		}
		return '';
	} // get_parsed_content

	/* ----------------------------------------------------------- */
	/* Template
	/*	set_file (string ID, string file_name)
	/* -----------------------------------------------------------
	/* ielaadeejam failu file_name un uzstaadam id ID
	/* ----------------------------------------------------------- */
	function set_file($ID, $str_file_name)
	{
		if($this->block_isset($ID)) {
			$this->halt('set_file: block ['.$ID.'] already exists');
			return false;
		} else {
			$key = $this->root_hash.$str_file_name;

			$file_path = $this->filename($str_file_name);
			$modtime = 0;
			if($file_exists = file_exists($file_path))
				$modtime = filemtime($file_path);

			/*
			if($this->blocks[$ID] =& $this->cache_fetch($key))
			{
				if($modtime && ($modtime == $this->blocks[$ID]->modtime))
				{
					print "OK<br>";
					return true;
				} else {
					print "Reload<br>";
				}
			}
			*/

			if($file_exists) {
				$str_content = file_get_contents($file_path);
			} else {
				$str_content = '';
			}

			$this->blocks[$ID] = new TemplateBlock($ID, $str_content, $this->undefined);
			$this->blocks[$ID]->block_parent = &$this;
			$this->blocks[$ID]->modtime = $modtime;

			$this->cache_store($key, $this->blocks[$ID]);

			return true;
		}
	} // set_file

	/* ----------------------------------------------------------- */
	/* Template
	/*	set_var (string var_id, mixed value, [string ID [, boolean parent_only]])
	/* -----------------------------------------------------------
	/* uzstaadam konkreetam blokam mainiigo
	/* ja nav noraadiits bloka id, tad uzstaadam globaali
	/* vairaak infas skat. TemplateBlock::set_var
	/* ----------------------------------------------------------- */
	function set_var($str_var_id, $value, $ID = '', $bln_parent_only = false)
	{
		if($ID)
		{
			if($block =& $this->get_block($ID))
				return $block->set_var($str_var_id, $value, $bln_parent_only);
		} else {
			parent::set_var($str_var_id, $value, $bln_parent_only);
			//return $this->set_global($str_var_id, $value);
		}

		return false;
	} // set_var

	/* ----------------------------------------------------------- */
	/* Template
	/*	set_array (array array, [string id, [boolean parent_only]])
	/* -----------------------------------------------------------
	/* uzstaadam masiivu ar datiem kaa veertiibas
	/* ja nav noraadiits bloka id, uzstaada globaali
	/* parent_only, ja veertiibas nevaig uzstaadiit apaksblokiem
	/* ----------------------------------------------------------- */
	function set_array($arr_array, $ID = '', $bln_parent_only = false,
		$prefix = '')
	{
		if($ID)
		{
			if($block =& $this->get_block($ID))
				return $block->set_array($arr_array, $bln_parent_only, $prefix);
		} else {
			return parent::set_array($arr_array, $bln_parent_only, $prefix);
			//foreach($this->blocks as $file_id => $object)
				//$this->blocks[$file_id]->set_array($arr_array, $bln_parent_only, $prefix);
		}
	} // set_array

	function set_array_prefix($arr_array, $prefix, $ID)
	{
		return $this->set_array($arr_array, $ID, false, $prefix);
	} // set_array_prefix

	/* ----------------------------------------------------------- */
	/* Template
	/*	is_root (string file_name)
	/* -----------------------------------------------------------
	/* noskaidro, vai faila nosaukums ir relatiivs vai nee
	/* ----------------------------------------------------------- */
	function is_root($str_file_name)
	{
		return preg_match('/^\/|^[a-zA-Z]:/i', $str_file_name);
	} // is_root

	/* ----------------------------------------------------------- */
	/* Template
	/*	parse_file (string ID)
	/* -----------------------------------------------------------
	/* apstraadaa failu ID
	/* ----------------------------------------------------------- */
	function parse_file($ID)
	{
		return $this->parse_block($ID);
	} // parse_file

	/* ----------------------------------------------------------- */
	/* Template
	/*	parse ()
	/* -----------------------------------------------------------
	/* neljaujam parseet failu kaa bloku
	/* ----------------------------------------------------------- */
	/*
	function parse()
	{
		$this->halt('parse: try parse_file() or parse_block()');
		return false;
	} // parse
	*/

	function parse_block($ID, $bln_append = false)
	{
		if($block =& $this->get_block($ID))
			return $block->parse($bln_append);

		return false;
	} // parse_block

	/* ----------------------------------------------------------- */
	/* Template
	/*	copy_block (string ID1, string ID2)
	/* -----------------------------------------------------------
	/* paarkopeejam bloku ID2 uz ID1
	/* ----------------------------------------------------------- */
	# XXX: nekad nav strādājis
	/*
	function _copy_vars_byobj(&$block1, &$block1) # XXX: <- LOL :D
	{
		$block1->vars = array_merge($block1->vars, $block2->vars);
	} // _copy_vars_byobj
	*/

	function copy_block($ID_from, $ID_to)
	{
		if( !($block1 =& $this->get_block($ID_from)) )
		{
			$this->halt('copy_block: block ['.$ID_from.'] not found!');
			return false;
		}

		if( ! ($block2 =& $this->get_block($ID_to)) )
		{
			$this->halt('copy_block: block ['.$ID_to.'] not found!');
			return false;
		}

		/* tagat noskaidrosim, vai block1 nav zem block2 */
		if( ($block3 =& $block2->get_block($ID_from)) )
		{
			$this->halt('copy_block: cannot copy ['.$ID_to.'] to ['.$ID_from.']. ['.$ID_from.'] is a child of ['.$ID_to.']');
			return false;
		}

		//$block1 = $block2;
		//return;

		/* uzstaadam visiem $block2 apaksblokiem mainiigos
			ljoti svariiga vieta - bugs bija ;)
		*/
		//foreach($block2->blocks as $key => $val)
			//$block2->blocks[$key]->set_array($block1->vars);

		/* paarkopeejam paareejos parametrus */
		$block1->vars = &$block2->vars;
		//$block1->vars = array_merge($block1->vars, $block2->vars);
		# XXX: nekad nav strādājis :D
		//$this->_copy_vars_byobj($block1, $block2);

		//$block1->blocks = array_merge($block1->blocks, $block2->blocks);
		$block1->blocks = $block2->blocks;
		//$block1->parsed_content = array_merge($block1->parsed_content, $block2->parsed_content);
		$block1->parsed_content = $block2->parsed_content;
		$block1->content = $block2->content;

		# Uzstādam parentu
		$block2->block_parent = &$block1;

		return true;
	} // copy_block

	/* ----------------------------------------------------------- */
	/* Template
	/*	reset_block (string ID [, boolean parent_only])
	/* -----------------------------------------------------------
	/* izdeesham visu bloka ID noparseeto saturu
	/* ----------------------------------------------------------- */
	function reset_block($ID, $bln_parent_only = false)
	{
		if($block =& $this->get_block($ID)) {
			return $block->reset($bln_parent_only);
		} else {
			$this->halt('reset_block: block ['.$ID.'] not found!');
			return false;
		}
	} // reset_block

	/* ----------------------------------------------------------- */
	/* Template
	/*	set_attribute (string ID, string attribute, mixed value)
	/* -----------------------------------------------------------
	/* uzstaadam blokam ID atribuutu
	/* vairaak skatiit TemplateBlock::set_attribute
	/* ----------------------------------------------------------- */
	function set_attribute($ID, $str_attribute, $value)
	{
		if($block =& $this->get_block($ID)) {
			return $block->set_attribute($str_attribute, $value);
		} else {
			$this->halt('set_attribute: block ['.$ID.'] not found!');
		}

		return false;
	} // set_attribute

	/* ----------------------------------------------------------- */
	/* Template
	/*	enable (string ID)
	/* -----------------------------------------------------------
	/* uzstaadam blokam ID atribuutu disabled=false
	/* vairaak skatiit TemplateBlock::set_attribute
	/* ----------------------------------------------------------- */
	function enable($ID)
	{
		return $this->set_attribute($ID, 'disabled', false);
	} // enable

	/* ----------------------------------------------------------- */
	/* Template
	/*	set_block_string (string ID, string content)
	/* -----------------------------------------------------------
	/* uzstaadam blokam ID parseejamos datus
	/* liidziigi, kaa set_file, tachu datus uzstaada nevis no faila,
	/* bet no stringa
	/* ----------------------------------------------------------- */
	function set_block_string($ID, $content = '')
	{
		if($block =& $this->get_block($ID)) {
			return $block->set_block_string($content);
		} else {
			$this->halt('set_block_string: block ['.$ID.'] not found!');
			return false;
		}
	} // set_block_string

	/* ----------------------------------------------------------- */
	/* Template
	/*	create_block (string ID, string content)
	/* -----------------------------------------------------------
	/* uztaisam bloku ar ID +parseejamos datus
	/* liidziigi, kaa set_file, tachu datus uzstaada nevis no faila,
	/* bet no stringa
	/* ----------------------------------------------------------- */
	function create_file($ID, $content = '')
	{
		if($this->block_isset($ID))
		{
			$this->halt('create_file: block ['.$ID.'] already exists');
			return false;
		} else {
			$this->blocks[$ID] = new TemplateBlock($ID, $content, $this->undefined);
			return true;
		}
	}

	/* ----------------------------------------------------------- */
	/* Template
	/*	disable (string ID)
	/* -----------------------------------------------------------
	/* uzstaadam blokam ID atribuutu disabled=true
	/* vairaak skatiit TemplateBlock::set_attribute
	/* ----------------------------------------------------------- */
	function disable($ID)
	{
		return $this->set_attribute($ID, 'disabled', true);
	} // disable

	function &cache_fetch($key)
	{
		if($this->APC)
		{
			print "Fetch: $key-";
			if($val = apc_fetch($key))
			{
				return $val;
			}
		}

		$val = null;
		return $val;
	} // cache_fetch

	function cache_store($key, &$val)
	{
		if($this->APC)
		{
			print "Store: $key<br>";
			return apc_store($key, $val);
		}

		return false;
	} // cache_store

} // class::Template


