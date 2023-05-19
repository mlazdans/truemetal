<?php declare(strict_types = 1);

class ViewAttendEntity extends Entity
{
	use ViewAttendEntityTrait;

	static function getByResId(int $res_id, ?ViewAttendFilter $F = new ViewAttendFilter): ViewAttendCollection
	{
		if(!$F->getOrderBy()){
			$F->orderBy('a_entered');
		}

		$F->res_id = $res_id;

		return (new static)->getAll($F);
	}
}
