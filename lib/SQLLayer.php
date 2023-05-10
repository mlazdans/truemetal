<?php declare(strict_types = 1);

class SQLLayer
{
	var $conn;
	var $str_db_info_string;
	var $charset;

	function __construct(string $charset = 'utf8')
	{
		$this->str_db_info_string = 'none';
		$this->charset = $charset;
		mysqli_report(MYSQLI_REPORT_ERROR);
	}

	function Connect($str_db_host = '', $str_db_user = '', $str_db_password = '', $str_db_name = '', $int_port = 0)
	{
		if(empty($int_port))
			$int_port = 3306;

		return $this->__connect_mysqli($str_db_host, $str_db_user, $str_db_password, $str_db_name, $int_port);
	} // Connect

	function Execute($str_sql)
	{
		return $this->__execute_mysqli($str_sql);
	} // Execute

	function ExecuteSingle($str_sql)
	{
		$arr_data = $this->__execute_mysqli($str_sql);

		if(is_array($arr_data) && isset($arr_data[0]))
			return $arr_data[0];

		return array();
	} // ExecuteSingle

	function Now()
	{
		return 'CURRENT_TIMESTAMP';
	}

	function Query($str_sql)
	{
		return $this->__query_mysqli($str_sql);
	} // Query

	function FetchAssoc($res_q)
	{
		return mysqli_fetch_assoc($res_q);
	} // FetchAssoc

	function FetchObject($res_q)
	{
		return mysqli_fetch_object($res_q);
	} // FetchObject

	function LastID()
	{
		return mysqli_insert_id($this->conn);
	} // LastID

	function Commit()
	{
		return mysqli_commit($this->conn);
	} // Commit

	function Rollback()
	{
		return mysqli_rollback($this->conn);
	} // Rollback

	function Quote($p)
	{
		if(is_array($p)){
			return $this->QuoteArray($p);
		} elseif(is_object($p)) {
			return $this->QuoteObject($p);
		} else {
			return $this->__mysqli_quote($p);
		}
	} // Quote

	function QuoteArray($arr)
	{
		return $this->__mysqli_quote_array($arr);
	} // QuoteArray

	function QuoteObject($obj)
	{
		return $this->__mysqli_quote_object($obj);
	} // QuoteObject

	function AutoCommit($bool)
	{
		return mysqli_autocommit($this->conn, $bool);
	} // AutoCommit

	function AffectedRows()
	{
		return mysqli_affected_rows($this->conn);
	}

	function numRows()
	{
		$d = $this->ExecuteSingle("SELECT FOUND_ROWS() AS fr");
		return (int)$d['fr'];
	} // numRows

	function Close()
	{
		return mysqli_close($this->conn);
	} // Close


	protected function __connect_mysqli($str_db_host = '', $str_db_user = '', $str_db_password = '', $str_db_name = '', $int_port = 3306)
	{
		if( !($this->conn = mysqli_connect($str_db_host, $str_db_user, $str_db_password, $str_db_name, $int_port)) )
			user_error(mysqli_connect_error(), E_USER_WARNING);

		if($str_db_name && $this->conn)
		{
			if(mysqli_select_db($this->conn, $str_db_name))
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

	protected function __execute_mysqli($str_sql)
	{
		if( !($res_q = mysqli_query($this->conn, $str_sql)) )
			user_error(mysqli_error($this->conn).($GLOBALS['sys_debug'] ? nl2br("\n$str_sql\n\n") : ''), E_USER_WARNING);

		$arr_data = array();
		if(is_object($res_q))
		{
			while($arr_row = mysqli_fetch_assoc($res_q))
			{
				$arr_data[] = $arr_row;
			}
		}

		# ja selekteejam datus, tad atgriezam tos, savaadaak querija rezultaatu
		return is_object($res_q) ? $arr_data : $res_q;
	} // __execute_mysqli

	protected function __query_mysqli($str_sql)
	{
		if( !($res_q = mysqli_query($this->conn, $str_sql)) )
			user_error(mysqli_error($this->conn).(true || $GLOBALS['sys_debug'] ? $str_sql : ''), E_USER_WARNING);

		return $res_q;
	} // __query_mysql

	protected function __mysqli_quote($str)
	{
		return mysqli_real_escape_string($this->conn, (string)$str);
	}

	protected function __mysqli_quote_object($obj)
	{
		foreach($obj as $k=>$v)
			$obj->{$k} = $this->__mysqli_quote($v);

		return $obj;
	}

	protected function __mysqli_quote_array($arr)
	{
		foreach($arr as $k=>$v)
			$arr[$k] = $this->__mysqli_quote($v);

		return $arr;
	}

}
