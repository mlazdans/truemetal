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
		public ?bool $get_all_ips              = false,
		public ?bool $get_actitve_sessions     = false,
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

		if($this->get_actitve_sessions){
			$sql->Where("l_logedin = 1 AND TIMESTAMPDIFF(second, l_lastaccess, CURRENT_TIMESTAMP) < 600");
		}

		return $sql;
	}

}
