<?php declare(strict_types = 1);

use dqdp\DBA\AbstractFilter;
use dqdp\DBA\Types\None;
use dqdp\SQL\Select;
use dqdp\TODO;

class LoginsFilter extends AbstractFilter
{
	function __construct(
		public ?int $l_id                      = null,
		public ?string $l_login                = null,
		public ?string $l_email                = null,
		public ?string $l_hash                 = null,
		public ?string $l_nick                 = null,
		public ?string $l_sess_id              = null,
		public null|int|false $l_logedin       = false,
		public null|int|false $l_active        = 1,
		public null|int|false $l_accepted      = 1,
		public ?None $l_lastaccess             = null,
		public ?string $q                      = null,
		public ?bool $jubilars                 = false,
		public ?bool $get_all_ips              = false,
	) {}

	static function ignore_disabled(LoginsFilter $F, bool $ignore_disabled): LoginsFilter
	{
		if($ignore_disabled)
		{
			$F->l_active = false;
			$F->l_accepted = false;
		}

		return $F;
	}

	protected function apply_filter(Select $sql): Select
	{
		$prefix = "";

		$this->apply_set_fields($sql, ['l_id', 'l_login', 'l_email', 'l_hash', 'l_nick', 'l_sess_id'], $prefix);
		$this->apply_falsed_fields($sql, ['l_active', 'l_accepted', 'l_logedin'], $prefix);

		if($this->l_lastaccess instanceof None)
		{
			$sql->Where("l_lastaccess IS NULL");
		}

		if($this->jubilars)
		{
			new TODO("move to proc/view");
			// $d0 = date('Y-m-d H:i:s', strtotime("-6 month"));
			// $d1 = date('Y-m-d H:i:s', strtotime("-2 day"));
			// $d2 = date('Y-m-d H:i:s', strtotime("+2 day"));
			// $sql->Where("(DATE_FORMAT(l_entered, '%m%d') >= DATE_FORMAT('$d1', '%m%d') AND DATE_FORMAT(l_entered, '%m%d') <= DATE_FORMAT('$d2', '%m%d'))");
			// $sql->Where("l_lastaccess >= '$d0'");

			// $sql->Select("DATE_FORMAT(l_entered, '%m%d')", "entered_stamp");
			// $sql->Select("DATEDIFF(CURRENT_TIMESTAMP, l_entered)", "age");
			// 	if(!empty($params['jubilars'])){
			// 		$sql->OrderBy("entered_stamp ASC");
		}

		if($this->q)
		{
			$sql->Where(search_to_sql_cond($this->q, [$prefix.'l_nick', $prefix.'l_login', $prefix.'l_email', $prefix.'l_userip']));
		}

		if($this->get_all_ips)
		{
			new TODO("get_all_ips: move to View/Proc");
			// $d = date('Y-m-d H:i:s', strtotime('-1 year'));
			// $sql->Select("(SELECT GROUP_CONCAT(DISTINCT c_userip) FROM comment WHERE login_id = logins.l_id AND c_entered > '$d')", "all_ips");
		}

		return $sql;
	}

}
