<?php declare(strict_types = 1);

use dqdp\DBA\AbstractFilter;
use dqdp\DBA\Types\None;
use dqdp\SQL\Select;

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
		$this->apply_set_fields($sql, ['l_id', 'l_login', 'l_email', 'l_hash', 'l_nick', 'l_sess_id']);
		$this->apply_falsed_fields($sql, ['l_active', 'l_accepted', 'l_logedin']);

		if($this->l_lastaccess instanceof None)
		{
			$sql->Where("l_lastaccess IS NULL");
		}

		if($this->q)
		{
			$sql->Where(search_to_sql_cond($this->q, ['l_nick', 'l_login', 'l_email', 'l_userip']));
		}

		if($this->get_actitve_sessions){
			$sql->Where("l_logedin = 1 AND TIMESTAMPDIFF(second, l_lastaccess, CURRENT_TIMESTAMP) < 600");
		}

		return $sql;
	}

}
