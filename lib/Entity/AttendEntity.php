<?php declare(strict_types = 1);

class AttendEntity extends Entity
{
	use AttendEntityTrait;

	static function attend(int $login_id, int $res_id, int $attend)
	{
		return (new AttendType(
			l_id:$login_id,
			res_id:$res_id,
			a_attended:$attend
		))->save();
	}
}
