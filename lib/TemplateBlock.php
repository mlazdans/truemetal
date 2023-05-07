<?php declare(strict_types = 1);

define('TMPL_APPEND', true);

class TemplateBlock
{
	private string $ID = '';
	private int $parsed_count = 0;
	private ?int $offset_start = null;    // where block starts
	private ?int $offset_end = null;      // where block ends
	private ?int $len = null;             // block length
	private bool $attr_disabled = false;
	/** @var TemplateBlock[] */
	protected array $blocks = [];
	private array $vars = [];
	private array $block_vars = [];
	private string $content = '';
	private $parsed_content = '';         // XXX: ja uzliek type, tad baigi lēns!!!
	private ?TemplateBlock $parent = null;

	function __construct(TemplateBlock $parent = NULL, string $ID, string $content){
		$this->ID = $ID;
		$this->parent = $parent;
		$this->content = $content;
		$this->__find_blocks();
	}

	function block_exists(string $ID): bool {
		return (bool)$this->_get_block($ID);
	}

	function get_block(string $ID): TemplateBlock {
		if($block = $this->_get_block($ID)){
			return $block;
		}

		throw new InvalidArgumentException("block not found ($ID)");
	}

	function parse_block(string $ID, bool $append = false): string {
		return $this->get_block($ID)->parse($append);
	}

	function parse(bool $append = false): string {
		if($this->attr_disabled){
			return '';
		}

		if($this->parsed_count && !$append) {
			return $this->parsed_content;
		}

		$this->parsed_count++;
		$parsed_content = $this->content;
		foreach($this->blocks as $id=>$block){
			// No white-space check
			// $patt = "/<!-- BEGIN $id .*-->.*<!-- END $id -->/smU";
			$patt = "/\R?<!-- BEGIN $id .*-->.*<!-- END $id -->\R?/s";
			if(preg_match($patt, $parsed_content, $m, PREG_OFFSET_CAPTURE)){
				$offset = (int)$m[0][1];
				$len = strlen($m[0][0]);
				$parsed_content = substr_replace($parsed_content, $block->parse($append), $offset, $len);
			}
		}

		$parsed_content = $this->_apply_vars($parsed_content);

		if($append){
			$this->parsed_content .= $parsed_content;
			// foreach($this->blocks as $object){
			// 	$object->reset();
			// }
		} else {
			$this->parsed_content = $parsed_content;
		}

		return $parsed_content;
	}

	function get_vars(string $ID = NULL): ?array {
		return ($block = $this->_get_block_or_self($ID)) ? $block->vars : NULL;
	}

	function get_parsed_content(string $ID = NULL): ?string {
		return ($block = $this->_get_block_or_self($ID)) ? $block->parsed_content : NULL;
	}

	function get_var(string $var_id, string $ID = NULL){
		if($block = $this->_get_block_or_self($ID)){
			if(isset($block->vars[$var_id])) {
				return $block->vars[$var_id];
			} elseif($block->parent) {
				return $block->parent->get_var($var_id);
			}
		}

		return NULL;
	}

	function set_var(string $var_id, $value, string $ID = NULL): TemplateBlock {
		if($block = $this->_get_block_or_self($ID)){
			$block->vars[$var_id] = $value;
		}

		return $this;
	}

	function set_array(iterable $array, string $ID = NULL): TemplateBlock {
		if($block = $this->_get_block_or_self($ID)){
			foreach($array as $k=>$v){
				$block->vars[$k] = $v;
			}
		}

		return $this;
	}

	function set_except(array $exclude, array $data, string $ID = NULL): TemplateBlock {
		if($block = $this->_get_block_or_self($ID)){
			$diff = array_diff(array_keys($data), $exclude);
			foreach($diff as $k){
				$block->vars[$k] = $data[$k];
			}
		}

		return $this;
	}

	function reset(string $ID = NULL): TemplateBlock {
		if($block = $this->_get_block_or_self($ID)){
			$block->parsed_content = '';
			$block->parsed_count = 0;
			foreach($block->blocks as $o){
				$o->reset();
			}
		}

		return $this;
	}

	function enable_if(bool $cond, string $ID = NULL): TemplateBlock {
		return $this->set_attribute('disabled', !$cond, $ID);
	}

	function enable(string $ID = NULL): TemplateBlock {
		return $this->set_attribute('disabled', false, $ID);
	}

	function disable(string $ID = NULL): TemplateBlock {
		return $this->set_attribute('disabled', true, $ID);
	}

	function set_attribute(string $attribute, $value, string $ID = NULL): TemplateBlock {
		if($block = $this->_get_block_or_self($ID)){
			if($attribute == 'disabled'){
				$block->attr_disabled = $value;
			}
		}
		return $this;
	}

	# TODO: test vai remove?
	// function copy_block($ID_to, $ID_from){
	// 	if(!($block_to = $this->get_block($ID_to))){
	// 		return false;
	// 	}

	// 	if(!($block_from = $this->get_block($ID_from))){
	// 		return false;
	// 	}

	// 	# tagat noskaidrosim, vai block_to nav zem block_from
	// 	// if($block_from->get_block($ID_to)){
	// 	// 	$this->error("block is a child of parent ($ID_from:$ID_to)");
	// 	// 	return false;
	// 	// }

	// 	# paarkopeejam paareejos parametrus
	// 	$block_to->vars = &$block_from->vars;
	// 	$block_to->blocks = &$block_from->blocks;
	// 	$block_to->block_vars = &$block_from->block_vars;
	// 	$block_to->content = &$block_from->content;
	// 	$block_to->parsed_content = &$block_from->parsed_content;
	// 	$block_to->attr_disabled = &$block_from->attr_disabled;
	// 	$block_from->parent = $block_to;

	// 	//unset($block_from->parent->blocks[$ID_from]);

	// 	return true;
	// }

	function set_block_string(string $ID, string $content){
		if($block = $this->get_block($ID)){
			$block->parsed_content = $content;
			$block->parsed_count = 1;
		}

		return $this;
	}

	function dump_blocks($pre = ''){
		foreach($this->blocks as $block_id=>$object){
			$a = ($object->blocks ? '+' : '-');
			print "$pre$a$block_id($object->parsed_count)<br>\n";
			$object->dump_blocks("| $pre");
		}
	}

	function dump(){
		$vars = [
			'ID', 'attr_disabled', 'parsed_count', 'offset_start', 'offset_end', 'len',
			'blocks', 'vars', 'block_vars', 'content', 'parsed_content'
		];

		foreach($vars as $k){
			if($k == 'blocks'){
				$ret[$k] = array_keys($this->blocks);
			} else {
				$ret[$k] = $this->{$k};
			}
		}

		return $ret;
	}

	private function _apply_vars($data): string {
		if(empty($this->block_vars)){
			return $data;
		}

		foreach($this->block_vars as $k){
			$patt[] = '{'.$k.'}';
			$repl[] = $this->get_var($k);
		}

		return str_replace($patt, $repl, $data);
	}

	private function __find_blocks(){
		// $m_WHOLE = 0;
		// $m_BEGIN = 1;
		// $m_ID = 2;
		// $m_ATTRS = 3;
		// $m_CONTENTS = 4;
		// $m_END = 5;
		$m_WHOLE = 0;
		$m_ID = 1;
		$m_ATTRS = 2;
		$m_CONTENTS = 3;

		// $patt = '/(<!-- BEGIN ([\S]+) (.*)-->)(.*)(<!-- END \2 -->)/smUS';
		$patt = '/<!-- BEGIN ([\S]+) (.*)-->(.*)<!-- END \1 -->/smU';

		if(preg_match_all($patt, $this->content, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER) === false){
			$err = array_flip(array_filter(get_defined_constants(true)['pcre'], function ($value) {
				return substr($value, -6) === '_ERROR';
			  }, ARRAY_FILTER_USE_KEY))[preg_last_error()];

			throw new ParseError(sprintf("template compilation failure $this->ID (%s)", $err));
		}

		$striped_offset = 0;
		$striped_content = '';
		foreach($matches as $item){
			$id = $item[$m_ID][0];

			if(isset($this->blocks[$id])){
				$content_offset = (int)$item[$m_CONTENTS][1];
				throw new InvalidArgumentException(
					sprintf("block already exists ($id), at %d near: '%s'",
					$item[$m_WHOLE][1],
					substr($this->content, $content_offset - 20, 40))
				);
			}

			$Block = new TemplateBlock($this, $id, $item[$m_CONTENTS][0]);
			$Block->len = strlen($item[$m_WHOLE][0]);
			$Block->offset_start = (int)$item[$m_WHOLE][1];
			$Block->offset_end = $Block->offset_start + $Block->len;

			$Block->attr_disabled = (strpos($item[$m_ATTRS][0], 'disabled') !== false);

			$this->blocks[$id] = $Block;

			$part = substr($this->content, $striped_offset, $Block->offset_start - $striped_offset);
			$striped_content .= $part;
			$striped_offset = $Block->offset_end;
		}
		$part = substr($this->content, $striped_offset);
		$striped_content .= $part;

		# Vars
		if(preg_match_all("/{([^\s}]+)}/", $striped_content, $m)){
			$this->block_vars = array_unique($m[1]);
		}
	}

	protected function error($msg, $e = E_USER_WARNING){
		$tmsg = '';
		$t = debug_backtrace();
		for($i=1;$i<count($t);$i++){
			$bn = basename($t[$i]['file']);
			if($bn == 'TemplateBlock.php' || $bn == 'Template.php'){
				continue;
			}
			$tmsg = sprintf("(called %s line %d)", $t[$i]['file'], $t[$i]['line']);
			break;
		}

		if($tmsg){
			$msg .= " $tmsg";
		}

		trigger_error($msg, $e);
	}

	private function _get_block_or_self(string $ID = null): ?TemplateBlock {
		if($ID){
			return $this->_get_block($ID);
		} else {
			return $this;
		}
	}

	private function _get_block(string $ID): ?TemplateBlock {
		if($this->ID == $ID){
			return $this;
		}

		if(isset($this->blocks[$ID])){
			return $this->blocks[$ID];
		}

		foreach($this->blocks as $o){
			if($block = $o->_get_block($ID)){
				return $block;
			}
		}

		return null;
	}
}
