<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

define('SQLQ_TYPE_SELECT', 0);
define('SQLQ_TYPE_INSERT', 1);
define('SQLQ_TYPE_UPDATE', 2);
define('SQLQ_TYPE_DELETE', 3);

define('SQLC_AND', 'AND');
define('SQLC_OR', 'OR');
define('SQLC_NONE', '');

define('SQLQ_ORDER_ASC', 'ASC');
define('SQLQ_ORDER_DESC', 'DESC');

class SQLCondition
{
	var $field;
	var $op;

	function SQLCondition($field, $op = SQLC_NONE)
	{
		$this->field = $field;
		$this->op = $op;
	}
}

class SQLQuery
{
	var $type;
	var $tables = array();
	var $conditions = array();
	var $fields = array();
	var $groupby = array();
	var $orderby = array();

	var $limit;

	/* konstruktors */
	function SQLQuery($sqlq_type = 0)
	{
		$this->set_type($sqlq_type);
	}

	/* query tips (select, insert, etc...)*/
	function set_type($sqlq_type = 0)
	{
		$this->type = $sqlq_type;
	}

	/* tabulu pievienoshana */
	function table($sqlq_table)
	{
		$this->tables[] = $sqlq_table;
	}

	/* nosaciijumi */
	function cond($sqlq_condition, $sqlq_op = SQLC_AND)
	{
		$this->condition($sqlq_condition, $sqlq_op);
	}

	function condition($sqlq_condition, $sqlq_op = SQLC_AND)
	{
		$this->conditions[] = new SQLCondition($sqlq_condition, $sqlq_op);
	}

	/* lauki */
	function field($sqlq_field)
	{
		$this->fields[] = $sqlq_field;
	}

	/* grupeeshana peec laukiem */
	function groupby($sqlq_field)
	{
		$this->groupby[] = $sqlq_field;
	}

	/* kaartoshana peec laukiem */
	function orderby($sqlq_field, $sqlq_direction = '')
	{
		$this->orderby[] = $sqlq_field.($sqlq_direction ? ' '.$sqlq_direction : '');
	}

	/* limit */
	function limit($sqlq_limit)
	{
		$this->limit = $sqlq_limit;
	}

	/* izveidot sql query */
	function build()
	{
		switch($this->type)
		{
			case SQLQ_TYPE_SELECT:
				return $this->__build_select();
				break;
			default:
				$this->error('Not implemented!');
				break;
		}
	}

	/* select */
	function __build_select()
	{
		/* tabulas */
		$tables = join(',', $this->tables);
		if(!$tables) {
			$this->error('Tables not set!');
			return FALSE;
		}

		$sql = 'SELECT';

		/* lauki */
		$fields = join(',', $this->fields);
		$sql .= ' '.($fields ? $fields : ' *');
		$sql .= ' FROM '.$tables;

		/* nosaciijumi */
		if(count($this->conditions)) {
			$cond = '';
			foreach($this->conditions as $condition)
				$cond .= " $condition->field $condition->op";
			$cond_patt = array('/(AND|OR)$/iU', '/(AND|OR)\s+?\)/iU', '/\(\s+?(AND|OR)/iU');
			$cond_repl = array('', ')', '(');
			$cond = preg_replace($cond_patt, $cond_repl, $cond);
			if($cond)
				$sql .= " WHERE$cond";
		}

		/* grupeeshana */
		$groupby = join(',', $this->groupby);
		$sql .= ($groupby ? " GROUP BY $groupby" : '');

		/* kaartoshana */
		$orderby = join(',', $this->orderby);
		$sql .= ($orderby ? " ORDER BY $orderby" : '');

		/* ierobezhojums */
		$sql .= ($this->limit ? " LIMIT $this->limit" : '');
		return $sql;
	}

	/* kljuudu apstraade */
	function error($sqlq_msg)
	{
		print "$sqlq_msg\n";
	}
} // SQLQuery
