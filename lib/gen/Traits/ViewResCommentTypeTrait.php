<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;
use dqdp\DBA\Types\Varchar;

trait ViewResCommentTypeTrait {
	use PropertyInitTrait;

	readonly int $c_id;
	readonly int $res_id;
	readonly ?int $res_resid;
	readonly int $table_id;
	readonly ?int $login_id;
	readonly ?string $res_entered;
	readonly ?string $res_nickname;
	readonly ?string $res_email;
	readonly ?string $res_ip;
	readonly int $res_visible;
	readonly ?string $res_name;
	readonly ?string $res_intro;
	readonly ?string $res_data;
	readonly ?string $res_data_compiled;
	readonly ?int $res_votes;
	readonly ?int $res_votes_plus_count;
	readonly ?int $res_votes_minus_count;
	readonly ?int $res_child_count;
	readonly ?string $res_child_last_date;
	readonly ?int $res_comment_count;
	readonly ?string $res_comment_last_date;
	readonly ?string $l_hash;

	function __construct(array|object|null $data = null, array|object|null $defaults = null) {
		parent::__construct($data, $defaults);
		if(!prop_initialized($this, 'res_visible'))$this->res_visible = (int)1;
	}

	static function initres_nickname(mixed $v): string {
		return (string)(new VarChar($v, 32));
	}

	static function initres_email(mixed $v): string {
		return (string)(new VarChar($v, 128));
	}

	static function initres_ip(mixed $v): string {
		return (string)(new VarChar($v, 32));
	}

	static function initl_hash(mixed $v): string {
		return (string)(new VarChar($v, 8));
	}

	function save(): mixed {
		return (new ViewResCommentEntity)->save($this);
	}

	function insert(): mixed {
		return (new ViewResCommentEntity)->insert($this);
	}
}
