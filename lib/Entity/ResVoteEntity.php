<?php declare(strict_types = 1);

class ResVoteEntity extends Entity
{
	use ResVoteEntityTrait;

	static function vote_with_timeout(int $timeout, int $res_id, int $login_id, int $rv_value, string $rv_userip)
	{
		$sql = "INSERT INTO res_vote (
			res_id, login_id, rv_value, rv_userip
		) VALUES (
			?, ?, ?, ?
		) ON DUPLICATE KEY UPDATE
			rv_value = CASE WHEN TIMESTAMPDIFF(MINUTE, rv_entered, CURRENT_TIMESTAMP) < $timeout THEN
				CASE WHEN VALUES(rv_value) <=> rv_value THEN 0 ELSE VALUES(rv_value) END
			ELSE rv_value END,
			rv_userip = CASE WHEN TIMESTAMPDIFF(MINUTE, rv_entered, CURRENT_TIMESTAMP) < $timeout THEN VALUES(rv_userip) ELSE rv_userip END
		";

		return (new static)->get_trans()->query($sql, $res_id, $login_id, $rv_value, $rv_userip);
	}
}
