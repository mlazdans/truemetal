<?php declare(strict_types = 1);

class AttendEntity extends Entity
{
	use AttendEntityTrait;

	static function attend(int $login_id, int $res_id, int $attend)
	{
		$D = new AttendDummy(
			l_id:$login_id,
			res_id:$res_id,
			a_attended:$attend
		);

		return AttendType::initFrom($D)->save();
	}
}
