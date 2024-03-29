<?php declare(strict_types = 1);

class PwValidatorResult
{
	var bool $HAS_LEN        = false;
	var bool $HAS_NON_ALPHA  = false;
	var bool $HAS_ALPHA      = false;
	var bool $HAS_NO_REPEATS = false;
}

class PwValidator
{
	static function has_len(string $s): bool {
		return mb_strlen($s) > 9;
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
