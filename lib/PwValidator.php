<?php declare(strict_types = 1);
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

class PwValidatorResult
{
	var bool $HAS_LEN        = false;
	var bool $HAS_NON_ALPHA  = false;
	var bool $HAS_ALPHA      = false;
	var bool $HAS_NO_REPEATS    = false;
}

class PwValidator
{
	const HAS_ALPHA      = 0;
	const HAS_NON_ALPHA  = 1;

	static function has_len(string $s): bool {
		return mb_strlen($s) > 8;
	}

	static function has_non_alpha(string $s): bool {
		return preg_match("/[^a-z]/i", $s) === 1;
	}

	static function has_alpha(string $s): bool {
		return preg_match("/[a-z]/i", $s) === 1;
	}

	static function has_repeats(string $s): bool {
		return preg_match("/(.)\\1{2}/", $s) === 1;
	}

	static function valid_pass(PwValidatorResult $r): bool {
		return
			$r->HAS_LEN &&
			$r->HAS_NON_ALPHA &&
			$r->HAS_ALPHA &&
			$r->HAS_NO_REPEATS
		;
	}

	static function validate(string $p): PwValidatorResult {
		$r = new PwValidatorResult($p);

		$r->HAS_LEN        = static::has_len($p);
		$r->HAS_NON_ALPHA  = static::has_non_alpha($p);
		$r->HAS_ALPHA      = static::has_alpha($p);
		$r->HAS_NO_REPEATS = !static::has_repeats($p);

		return $r;
	}
}
