<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// sql functions

define('DB_MYSQL', 1);
define('DB_PGSQL', 2);
define('DB_MYSQLI', 3);

class SQLLayer
{
	var $int_db_type;
	var $res_db_conn;
	var $str_db_info_string;
	var $charset;

	/* Inicializeejam datubaazes objektu */
	function SQLLayer($int_db_type = DB_MYSQL, $charset = 'utf8')
	{
		$this->str_db_info_string = 'none';
		$this->int_db_type = $int_db_type;
		$this->charset = $charset;
	} // SQLLayer

	/* Sleedzamies klaat datubaazei */
	function connect($str_db_host = '', $str_db_user = '', $str_db_password = '',
		$str_db_name = '', $int_port = 3306)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQL:
				return $this->__connect_mysql($str_db_host, $str_db_user, $str_db_password, $str_db_name);
				break;
			case DB_PGSQL:
				return $this->__connect_pgsql($str_db_host, $str_db_user, $str_db_password, $str_db_name);
				break;
			case DB_MYSQLI:
				return $this->__connect_mysqli($str_db_host, $str_db_user, $str_db_password, $str_db_name, $int_port);
				break;
			default:
				return false;
				break;
		}
		return false;
	} // connect

	/* Sleedzamies klaat mysql */
	function __connect_mysql($str_db_host = '', $str_db_user = '',
		$str_db_password = '', $str_db_name = '')
	{
		if(!extension_loaded('mysql'))
			user_error('Šī PHP versija neatbalsta MySQL funkcijas!', E_USER_ERROR);

		if( !($this->res_db_conn = @mysql_connect($str_db_host, $str_db_user, $str_db_password)) )
			user_error(mysql_error(), E_USER_WARNING);

		if($str_db_name && $this->res_db_conn)
			if(@mysql_select_db($str_db_name))
				$this->str_db_info_string = 'MySQL::'.$str_db_name;
			else
				 user_error(mysql_error(), E_USER_WARNING);

		return $this->res_db_conn;
	} // __connect_mysql

	/* Sleedzamies klaat postgresql */
	function __connect_pgsql($str_db_host = '', $str_db_user = '',
		$str_db_password = '', $str_db_name = '')
	{
		if(!extension_loaded('pgsql'))
			user_error('Šī PHP versija neatbalsta PostgreSQL funkcijas!', E_USER_ERROR);

		$str_connect = '';

		if($str_db_host)
			$str_connect .= ' host='.$str_db_host;

		if($str_db_user)
			$str_connect .= ' user='.$str_db_user;

		if($str_db_password)
			$str_connect .= ' password='.$str_db_password;

		if($str_db_name)
			$str_connect .= ' dbname='.$str_db_name;

		if( !($this->res_db_conn = @pg_connect($str_connect)) )
			user_error(pg_last_error(), E_USER_WARNING);

		if($this->res_db_conn)
			$this->str_db_info_string = 'PgSQL::'.$str_db_name;

		return $this->res_db_conn;
	} // __connect_pgsql

	/* Sleedzamies klaat mysqli */
	function __connect_mysqli($str_db_host = '', $str_db_user = '',
		$str_db_password = '', $str_db_name = '', $int_port = 3306)
	{
		if(!extension_loaded('mysqli'))
			user_error('Šī PHP versija neatbalsta MySQLi funkcijas!', E_USER_ERROR);

		if( !($this->res_db_conn = mysqli_connect($str_db_host, $str_db_user, $str_db_password, $str_db_name, $int_port)) )
			user_error(mysqli_connect_error(), E_USER_WARNING);

		if($str_db_name && $this->res_db_conn)
		{
			if(@mysqli_select_db($this->res_db_conn, $str_db_name))
				$this->str_db_info_string = 'MySQLi::'.$str_db_name;
			else
				user_error(mysqli_error($this->res_db_conn), E_USER_WARNING);

			if($this->charset)
			{
				mysqli_set_charset($this->res_db_conn, $this->charset);
			}
		}

		return $this->res_db_conn;
	} // __connect_mysqli

	/* izpildam SQL pieprasiijumu */
	function Execute($str_sql)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQL:
				return $this->__execute_mysql($str_sql);
				break;
			case DB_PGSQL:
				return $this->__execute_pgsql($str_sql);
				break;
			case DB_MYSQLI:
				return $this->__execute_mysqli($str_sql);
				break;
			default:
				return false;
				break;
		}
	} // Execute

	/* izpildam SQL pieprasiijumu un atgriezham tikai vienu ierakstu */
	function ExecuteSingle($str_sql)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQL:
				$arr_data = $this->__execute_mysql($str_sql);
				break;
			case DB_PGSQL:
				$arr_data = $this->__execute_pgsql($str_sql);
				break;
			case DB_MYSQLI:
				$arr_data = $this->__execute_mysqli($str_sql);
				break;
			default:
				return false;
				break;
		}

		if(is_array($arr_data))
			if(isset($arr_data[0]))
				return $arr_data[0];

		return array();
	} // ExecuteSingle

	function __execute_mysql($str_sql)
	{
		if( !($res_q = @mysql_query($str_sql)) )
			user_error(mysql_error().($GLOBALS['sys_debug'] ? $str_sql : ''), E_USER_WARNING);

		$arr_data = array();

		if(is_resource($res_q)) {
			while($arr_row = @mysql_fetch_array($res_q, MYSQL_ASSOC))
				$arr_data[] = $arr_row;
		}

		/* ja selekteejam datus, tad atgriezam tos, savaadaak querija rezultaatu */
		return is_resource($res_q) ? $arr_data : $res_q;
	} // __execute_mysql

	function __execute_pgsql($str_sql)
	{
		if( !($res_q = @pg_query($str_sql)) )
			user_error(pg_last_error(), E_USER_WARNING);

		$arr_data = array();

		if($res_q) {
			while($arr_row = @pg_fetch_array($res_q))
				$arr_data[] = $arr_row;
		}

		/* ja selekteejam datus, tad atgriezam tos, savaadaak querija rezultaatu */
		return is_resource($res_q) ? $arr_data : $res_q;
	} // __execute_pgsql

	function __execute_mysqli($str_sql)
	{
		if( !($res_q = @mysqli_query($this->res_db_conn, $str_sql)) )
			user_error(mysqli_error($this->res_db_conn).($GLOBALS['sys_debug'] ? $str_sql : ''), E_USER_WARNING);

		$arr_data = array();
		if(is_object($res_q))
		{
			while($arr_row = @mysqli_fetch_assoc($res_q))
			{
				$arr_data[] = $arr_row;
			}
		}
/*
		if(is_resource($res_q)) {
			while($arr_row = @mysqli_fetch_assoc($res_q))
				$arr_row[] = $arr_row;
		}
*/
		/* ja selekteejam datus, tad atgriezam tos, savaadaak querija rezultaatu */
		return is_object($res_q) ? $arr_data : $res_q;
		//return is_resource($res_q) ? $arr_data : $res_q;
	} // __execute_mysqli

	function current_time()
	{
		switch( $this->int_db_type ) {
			case DB_MYSQL:
				return 'NOW()';
				break;
			case DB_PGSQL:
				return 'current_timestamp';
				break;
			case DB_MYSQLI:
				return 'NOW()';
				break;
			default:
				return 'NULL';
				break;
		}

		return 'NULL';
	} // current_time

	function now()
	{
		return $this->current_time();
	} // now

	function Query($str_sql)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				return $this->__query_mysqli($str_sql);
				break;
			default:
				return false;
				break;
		}
	} // Query

	function __query_mysqli($str_sql)
	{
		if( !($res_q = @mysqli_query($this->res_db_conn, $str_sql)) )
			user_error(mysqli_error($this->res_db_conn).($GLOBALS['sys_debug'] ? $str_sql : ''), E_USER_WARNING);

		return $res_q;
	} // __query_mysql

	function FetchAssoc($res_q)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				return mysqli_fetch_assoc($res_q);
				break;
			default:
				return false;
				break;
		}
	} // FetchAssoc

	function FetchObject($res_q)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				return mysqli_fetch_object($res_q);
				break;
			default:
				return false;
				break;
		}
	} // FetchObject

	function LastID()
	{
		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				return mysqli_insert_id($this->res_db_conn);
				break;
			default:
				return false;
				break;
		}
	} // LastID
/***
	function Prepare($str_sql)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				$ret = mysqli_prepare($this->res_db_conn, $str_sql);
				if(!$ret)
				{
					user_error(mysqli_error($this->res_db_conn).($GLOBALS['sys_debug'] ? $str_sql : ''), E_USER_WARNING);
				}
				return $ret;
				break;
			default:
				return false;
				break;
		}
	} // Prepare

	function BindResult()
	{
		$args = func_get_args();
		if(count($args) < 2)
		{
			user_error("BindResult(): too few arguments", E_USER_ERROR);
			return;
		}

		$stmt = array_shift($args);
		$bind_params = '';
		$typecodes = '';
		foreach($params as $k=>$v)
		{
			$bind_params .= ', $params['.$k.']';
		}

		$f = '$ret = mysqli_stmt_bind_result($stmt'.$bind_params.');';

		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				eval($f);
				return $ret;
				break;
			default:
				return false;
				break;
		}
	} // BindResult

	function BindParam()
	{
		$types = array(
			'integer'=>'i',
			'double'=>'d',
			'string'=>'s',
		);

		$args = func_get_args();
		if(count($args) < 2)
		{
			user_error("BindParam(): too few arguments", E_USER_ERROR);
			return;
		}

		$stmt = array_shift($args);
		$bind_params = '';
		$typecodes = '';
		foreach($params as $k=>$v)
		{
			$t = gettype($v);
			if(isset($types[$t]))
			{
				$typecodes .= $types[$t];
				$bind_params .= ', $params['.$k.']';
			}
		}

/
		foreach($args as $k=>$v)
		{
			$t = gettype($v);
			if(isset($types[$t]))
			{
				$typecodes .= $types[$t];
				$bind_params .= ', $args['.$k.']';
			}
		}
/
		$f = '$ret = mysqli_stmt_bind_param($stmt, $typecodes'.$bind_params.');';

		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				eval($f);
				return $ret;
				break;
			default:
				return false;
				break;
		}
	} // Bind
*/

} // SQLLayer

