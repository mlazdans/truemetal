<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;

trait CommentDisabledTypeTrait {
	use PropertyInitTrait;

	var int $login_id;
	var int $disable_login_id;

	function __construct(?int $login_id = null, ?int $disable_login_id = null) {
		if(isset($login_id))$this->login_id = $login_id;
		if(isset($disable_login_id))$this->disable_login_id = $disable_login_id;
	}

	function save(): mixed {
		return (new CommentDisabledEntity)->save($this);
	}

	function insert(): mixed {
		return (new CommentDisabledEntity)->insert($this);
	}

	function delete(): bool {
		return (new CommentDisabledEntity)->delete([$this->login_id, $this->disable_login_id]);
	}

	function update(): bool {
		return (new CommentDisabledEntity)->update([$this->login_id, $this->disable_login_id], $this);
	}
}
