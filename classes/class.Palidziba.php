<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

define('P_VALIDATE', true);
define('P_DONTVALIDATE', false);

class Palidziba
{

	function Palidziba()
	{
	} // Palidziba

	function load($p_id = 0)
	{
		global $db;

		if($p_id)
			$sql = "SELECT * FROM palidziba WHERE p_id = $p_id";
		else
			$sql = "SELECT * FROM palidziba";

		if($p_id) {
			return $db->ExecuteSingle($sql);
		} else
			return $db->Execute($sql);
	} // load

	function search($data)
	{
		global $db;

		$sql_add = '';
		$data['lastname'] = parse_mysql_search_q($data['lastname']);
		$data['city'] = parse_mysql_search_q($data['city']);
		$data['spec'] = (int)$data['spec'];
		$data['reg'] = (int)$data['reg'];
		$data['area'] = (int)$data['area'];

		if($data['lastname'])
			$sql_add .= "p_lastname like '%$data[lastname]%' AND ";

		if($data['city'])
			$sql_add .= "p_city like '%$data[city]%' AND ";

		if($data['spec'])
			$sql_add .= "(p_spec1 = $data[spec] OR p_spec2 = $data[spec]) AND ";

		if($data['reg'])
			$sql_add .= "p_region = $data[reg] AND ";

		if($data['area'])
			$sql_add .= "p_area = $data[area] AND ";

		if($sql_add)
			$sql = 'SELECT * FROM palidziba WHERE '.substr($sql_add, 0, -5);
		else
			$sql = 'SELECT * FROM palidziba';

		return $db->Execute($sql);
	} // search

	function insert(&$data, $validate = P_DONTVALIDATE)
	{
		global $db;

		if($validate)
			$this->validate($data);

		$sql = "
		INSERT INTO palidziba (
			p_firstname, p_lastname, p_spec1, p_spec2,
			p_region, p_area, p_city, p_street,
			p_house, p_phone, p_mobile, p_email,
			p_notes
		) VALUES (
			'$data[p_firstname]', '$data[p_lastname]', $data[p_spec1], $data[p_spec2],
			$data[p_region], $data[p_area], '$data[p_city]', '$data[p_street]',
			'$data[p_house]', '$data[p_phone]', '$data[p_mobile]', '$data[p_email]',
			'$data[p_notes]'
		)";

		if($db->Execute($sql)) {
			return last_insert_id();
		} else
			return false;
	} // insert

	function update($p_id, &$data, $validate = P_VALIDATE) {
		global $db;

		$p_id = (integer)$p_id;
		if(!$p_id) {
			$this->error_msg = 'Nav norādīts vai nepareizs ārsta ID<br>';
			return false;
		}

		if($validate)
			$this->validate($data);

		$sql = 'UPDATE palidziba SET ';
		$sql .= "p_firstname = '$data[p_firstname]', ";
		$sql .= "p_lastname = '$data[p_lastname]', ";
		$sql .= "p_spec1 = $data[p_spec1], ";
		$sql .= "p_spec2 = $data[p_spec2], ";
		$sql .= "p_region = $data[p_region], ";
		$sql .= "p_area = $data[p_area], ";
		$sql .= "p_city = '$data[p_city]', ";
		$sql .= "p_street = '$data[p_street]', ";
		$sql .= "p_house = '$data[p_house]', ";
		$sql .= "p_phone = '$data[p_phone]', ";
		$sql .= "p_mobile = '$data[p_mobile]', ";
		$sql .= "p_email = '$data[p_email]', ";
		$sql .= "p_notes = '$data[p_notes]', ";
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE p_id = '.$p_id;

		$db->Execute($sql);
		return $p_id;
	}

	function save($p_id, &$data) {
		$this->validate($data);

		$p_id = (integer)$p_id;
		$error_msg = '';

		if(!$error_msg) {
			if($p_id)
				return $this->update($p_id, $data, P_DONTVALIDATE);
			else
				return $this->insert($data, P_DONTVALIDATE);
		} else { // $error_msg
			$this->error_msg = $error_msg;
			return false;
		}
	}

	// actionu preprocessors
	function process_action(&$data, $action) {

		$ret = true;
		$func = '';

		if($action == 'delete_multiple')
			$func = 'del';

		if(isset($data['palidziba_count']) && $func)
			for($r = 1; $r <= $data['palidziba_count']; ++$r)
				// ja iechekots, proceseejam
				if(isset($data['p_checked'.$r]) && isset($data['p_id'.$r]))
					$ret = $ret && $this->{$func}($data['p_id'.$r]);

		return $ret;
	}

	function del($p_id)
	{
		global $db;

		if(!$p_id)
			return true;

		$sql = "DELETE FROM palidziba WHERE p_id = $p_id";

		return $db->Execute($sql);
	} // del

	function validate(&$data)
	{
		if(isset($data['p_id']))
			$data['p_id'] = !ereg('[0-9]', $data['p_id']) ? 0 : $data['p_id'];
		else
			$data['p_id'] = 0;

		if(!isset($data['p_firstname']))
			$data['p_firstname'] = '';

		if(!isset($data['p_lastname']))
			$data['p_lastname'] = '';

		if(isset($data['p_spec1']))
			$data['p_spec1'] = !ereg('[0-9]', $data['p_spec1']) ? 0 : $data['p_spec1'];
		else
			$data['p_spec1'] = 0;

		if(isset($data['p_spec2']))
			$data['p_spec2'] = !ereg('[0-9]', $data['p_spec2']) ? 0 : $data['p_spec2'];
		else
			$data['p_spec2'] = 0;

		if(isset($data['p_region']))
			$data['p_region'] = !ereg('[0-9]', $data['p_region']) ? 0 : $data['p_region'];
		else
			$data['p_region'] = 0;

		if(isset($data['p_area']))
			$data['p_area'] = !ereg('[0-9]', $data['p_area']) ? 0 : $data['p_area'];
		else
			$data['p_area'] = 0;

		if(!isset($data['p_street']))
			$data['p_street'] = '';

		if(!isset($data['p_house']))
			$data['p_house'] = '';

		if(!isset($data['p_phone']))
			$data['p_phone'] = '';

		if(!isset($data['p_mobile']))
			$data['p_mobile'] = '';

		if(!isset($data['p_email']))
			$data['p_email'] = '';

		if(!isset($data['p_notes']))
			$data['p_notes'] = '';

		my_strip_tags($data['p_firstname']);
		my_strip_tags($data['p_lastname']);
		my_strip_tags($data['p_street']);
		my_strip_tags($data['p_house']);
		my_strip_tags($data['p_phone']);
		my_strip_tags($data['p_mobile']);
		my_strip_tags($data['p_email']);
		my_strip_tags($data['p_notes']);

	} // validate

} // Forum
