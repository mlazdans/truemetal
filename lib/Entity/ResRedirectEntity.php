<?php declare(strict_types = 1);

class ResRedirectEntity extends Entity
{
	use ResRedirectEntityTrait;

	static function get_by_from_res_id(int $from_res_id, ?ResRedirectFilter $F = new ResRedirectFilter): ?ResRedirectType
	{
		$F->from_res_id = $from_res_id;

		return (new static)->get_single($F);
	}

}
