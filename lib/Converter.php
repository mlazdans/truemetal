<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// Converter: repleiso datus datubaazee

class Converter
{
	var $tables;
	var $find;
	var $repl;

	function Converter()
	{
		$this->tables = $this->find = $this->repl = array();
	} // Converter

	function add_table($table, $primary_key, $fields)
	{
		if(!$table || !$primary_key || !$fields)
			return false;

		if(!is_array($fields) && $fields)
			$fields = array($fields);

		$this->tables[$table]['name'] = $table;
		$this->tables[$table]['primary_key'] = $primary_key;
		$this->tables[$table]['fields'] = $fields;

		return true;
	} // add_table

	function add_replacement($find, $repl)
	{
		if(!$find || !$repl)
			return false;

		if(!is_array($find))
			$find = array($find);

		if(!is_array($repl))
			$repl = array($repl);

		if(count($find) == count($repl)) {
			$this->find = array_merge($this->find, $find);
			$this->repl = array_merge($this->repl, $repl);
			return true;
		}

		return false;
	} // add_replacement

	function convert($charset_from = '', $charset_to = '')
	{
		global $db;

		$patt = $this->get_pattern();
		foreach($this->tables as $table)
		{
			$field_sql = '';
			foreach($table['fields'] as $field)
				$field_sql .= $field.', ';
			$field_sql = substr($field_sql, 0, -2);

			if($field_sql)
				$sql = 'SELECT '.$table['primary_key'].', '.$field_sql.' FROM '.$table['name'];
			else
				continue;

			$data = $db->Execute($sql);
			foreach($data as $item)
			{
				$field_sql = '';
				foreach($table['fields'] as $field)
				{
					$field_sql .=
					"$field = '".
					addslashes(
						preg_replace(
							$patt,
							$this->repl,
							(
								($charset_from && $charset_to) ?
								iconv($charset_from, $charset_to, $item[$field]) :
								$item[$field]
							)
						)
					).
					"', ";
				}

				$field_sql = substr($field_sql, 0, -2);
				if($field_sql)
				{
					$sql = 'UPDATE '.$table['name'].' SET '.$field_sql.' WHERE '.$table['primary_key'].
						' = '.$item[$table['primary_key']];
					$sql2 = 'UPDATE '.$table['name'].' WHERE '.$table['primary_key'].
						' = '.$item[$table['primary_key']];
				} else
					continue;

				print "$sql2";
				if(!$db->Execute($sql))
					print ' - [ERROR] ('.mysql_error().')';
				else
					print ' - [OK]';
				print "\n";
			} // data
		}
	} // convert

	function get_pattern()
	{
		$patt = array();
		foreach($this->find as $find)
			$patt[] = '/'.preg_quote($find, '/').'/imsU';

		return $patt;
	} //

} // class Converter
