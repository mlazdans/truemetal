<?php declare(strict_types = 1);

use dqdp\DBA\AbstractFilter;
use dqdp\SQL\Select;

class ResFilter extends AbstractFilter
{
	function __construct(
		# ja te kaut ko liek klāt, tad labāk sync ar pārējām klasēm, kas extendo šo
		public ?int $res_id                = null,
		public null|int|false $res_resid   = null, // false: WHERE res_resid IS NULL
		public ?int $table_id              = null,
		public ?int $login_id              = null,
		public null|int|false $res_visible = 1,
		public ?array $ips                 = null,
		public ?array $res_ids             = null,
	) {}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['res_id', 'table_id', 'login_id']);
		$this->apply_falsed_fields($sql, ['res_visible']);

		if(isset($this->res_resid))
		{
			if($this->res_resid === false)
			{
				$sql->Where("res_resid IS NULL");
			} else {
				$sql->Where(["res_resid = ?", $this->res_resid]);
			}
		}

		if(!is_null($this->ips)){
			$sql->WhereIn('res_ip', $this->ips);
		}

		if(!is_null($this->res_ids)){
			$sql->WhereIn("res_id", $this->res_ids);
		}

		return $sql;
	}

}
