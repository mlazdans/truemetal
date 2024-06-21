<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;

trait ResTypeTrait
{
	use PropertyInitTrait;

	var int $res_id;
	var string $res_hash;
	var ?int $res_resid;
	var int $res_kind;
	var ?int $login_id;
	var ?string $res_entered;
	var ?string $res_nickname;
	var ?string $res_email;
	var ?string $res_ip;
	var int $res_visible;
	var ?string $res_name;
	var ?string $res_intro;
	var ?string $res_data;
	var ?string $res_data_compiled;

	function __construct(?int $res_id = null, ?string $res_hash = null, ?int $res_resid = null, ?int $res_kind = null, ?int $login_id = null, ?string $res_entered = null, ?string $res_nickname = null, ?string $res_email = null, ?string $res_ip = null, ?int $res_visible = null, ?string $res_name = null, ?string $res_intro = null, ?string $res_data = null, ?string $res_data_compiled = null)
	{
		if(isset($res_id))$this->res_id = $res_id;
		if(isset($res_hash))$this->res_hash = $res_hash;
		if(isset($res_resid))$this->res_resid = $res_resid;
		if(isset($res_kind))$this->res_kind = $res_kind;
		if(isset($login_id))$this->login_id = $login_id;
		if(isset($res_entered))$this->res_entered = $res_entered;
		if(isset($res_nickname))$this->res_nickname = $res_nickname;
		if(isset($res_email))$this->res_email = $res_email;
		if(isset($res_ip))$this->res_ip = $res_ip;
		if(isset($res_visible))$this->res_visible = $res_visible;
		if(isset($res_name))$this->res_name = $res_name;
		if(isset($res_intro))$this->res_intro = $res_intro;
		if(isset($res_data))$this->res_data = $res_data;
		if(isset($res_data_compiled))$this->res_data_compiled = $res_data_compiled;
	}

	function save(): mixed {
		return (new ResEntity)->save($this);
	}

	function insert(): mixed {
		return (new ResEntity)->insert($this);
	}

	function delete(): bool {
		return (new ResEntity)->delete($this->res_id);
	}

	function update(): bool {
		return (new ResEntity)->update($this->res_id, $this);
	}
}
