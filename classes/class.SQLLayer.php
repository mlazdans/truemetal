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
	var $conn;
	var $str_db_info_string;
	var $charset;

	/* Inicializeejam datubaazes objektu */
	function __construct($int_db_type = DB_MYSQLI, $charset = 'utf8')
	{
		$this->str_db_info_string = 'none';
		$this->int_db_type = $int_db_type;
		$this->charset = $charset;
	} // __construct

	/* Sleedzamies klaat datubaazei */
	function Connect($str_db_host = '', $str_db_user = '', $str_db_password = '', $str_db_name = '', $int_port = 0)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQL:
				return $this->__connect_mysql($str_db_host, $str_db_user, $str_db_password, $str_db_name);
				break;
			case DB_PGSQL:
				return $this->__connect_pgsql($str_db_host, $str_db_user, $str_db_password, $str_db_name);
				break;
			case DB_MYSQLI:
				if(empty($int_port))
					$int_port = 3306;

				return $this->__connect_mysqli($str_db_host, $str_db_user, $str_db_password, $str_db_name, $int_port);
				break;
			default:
				return false;
				break;
		}
		return false;
	} // Connect

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

		if(is_array($arr_data) && isset($arr_data[0]))
			return $arr_data[0];

		return array();
	} // ExecuteSingle

	function Now()
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
	} // Now

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
				return mysqli_insert_id($this->conn);
				break;
			default:
				return false;
				break;
		}
	} // LastID

	function Commit()
	{
		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				return mysqli_commit($this->conn);
				break;
			default:
				return false;
				break;
		}
	} // Commit

	function Rollback()
	{
		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				return mysqli_rollback($this->conn);
				break;
			default:
				return false;
				break;
		}
	} // Rollback

	function Quote($str)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				return $this->__mysqli_quote($str);
				break;
			default:
				return false;
				break;
		}
	} // Quote

	function QuoteArray($arr)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				return $this->__mysqli_quote_array($arr);
				break;
			default:
				return false;
				break;
		}
	} // Quote

	function AutoCommit($bool)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				return mysqli_autocommit($this->conn, $bool);
				break;
			default:
				return false;
				break;
		}
	} // AutoCommit




	/* Sleedzamies klaat mysql */
	protected function __connect_mysql($str_db_host = '', $str_db_user = '', $str_db_password = '', $str_db_name = '')
	{
		if(!extension_loaded('mysql'))
			user_error('Šī PHP versija neatbalsta MySQL funkcijas!', E_USER_ERROR);

		if( !($this->conn = @mysql_connect($str_db_host, $str_db_user, $str_db_password)) )
			user_error(mysql_error(), E_USER_WARNING);

		if($str_db_name && $this->conn)
			if(@mysql_select_db($str_db_name))
				$this->str_db_info_string = 'MySQL::'.$str_db_name;
			else
				user_error(mysql_error(), E_USER_WARNING);

		return $this->conn;
	} // __connect_mysql

	/* Sleedzamies klaat postgresql */
	protected function __connect_pgsql($str_db_host = '', $str_db_user = '', $str_db_password = '', $str_db_name = '')
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

		if( !($this->conn = @pg_connect($str_connect)) )
			user_error(pg_last_error(), E_USER_WARNING);

		if($this->conn)
			$this->str_db_info_string = 'PgSQL::'.$str_db_name;

		return $this->conn;
	} // __connect_pgsql

	/* Sleedzamies klaat mysqli */
	protected function __connect_mysqli($str_db_host = '', $str_db_user = '', $str_db_password = '', $str_db_name = '', $int_port = 3306)
	{
		if(!extension_loaded('mysqli'))
			user_error('Šī PHP versija neatbalsta MySQLi funkcijas!', E_USER_ERROR);

		if( !($this->conn = mysqli_connect($str_db_host, $str_db_user, $str_db_password, $str_db_name, $int_port)) )
			user_error(mysqli_connect_error(), E_USER_WARNING);

		if($str_db_name && $this->conn)
		{
			if(@mysqli_select_db($this->conn, $str_db_name))
				$this->str_db_info_string = 'MySQLi::'.$str_db_name;
			else
				user_error(mysqli_error($this->conn), E_USER_WARNING);

			if($this->charset)
			{
				mysqli_set_charset($this->conn, $this->charset);
			}
		}

		$this->conn = $this->conn;

		return $this->conn;
	} // __connect_mysqli

	protected function __execute_mysql($str_sql)
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

	protected function __execute_pgsql($str_sql)
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

	protected function __execute_mysqli($str_sql)
	{
		if( !($res_q = @mysqli_query($this->conn, $str_sql)) )
			user_error(mysqli_error($this->conn).($GLOBALS['sys_debug'] ? nl2br("\n$str_sql\n\n") : ''), E_USER_WARNING);

		$arr_data = array();
		if(is_object($res_q))
		{
			while($arr_row = @mysqli_fetch_assoc($res_q))
			{
				$arr_data[] = $arr_row;
			}
		}

		/* ja selekteejam datus, tad atgriezam tos, savaadaak querija rezultaatu */
		return is_object($res_q) ? $arr_data : $res_q;
	} // __execute_mysqli

	protected function __query_mysqli($str_sql)
	{
		if( !($res_q = @mysqli_query($this->conn, $str_sql)) )
			user_error(mysqli_error($this->conn).($GLOBALS['sys_debug'] ? $str_sql : ''), E_USER_WARNING);

		return $res_q;
	} // __query_mysql

	protected function __mysqli_quote($str)
	{
		return mysqli_real_escape_string($this->conn, $str);
	} // __mysqli_quote_array

	protected function __mysqli_quote_array($r)
	{
		foreach($r as $k=>$v)
			$r->{$k} = $this->__mysqli_quote($v);

		return $r;
	} // __mysqli_quote_array

	/***
	function Prepare($str_sql)
	{
		switch( $this->int_db_type ) {
			case DB_MYSQLI:
				$ret = mysqli_prepare($this->conn, $str_sql);
				if(!$ret)
				{
					user_error(mysqli_error($this->conn).($GLOBALS['sys_debug'] ? $str_sql : ''), E_USER_WARNING);
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

