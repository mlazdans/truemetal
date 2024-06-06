<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;

trait LoginsTypeTrait
{
	use PropertyInitTrait;

	var int $l_id;
	var ?string $l_hash;
	var ?string $l_login;
	var string $l_nick;
	var ?string $l_password;
	var ?string $l_email;
	var int $l_active;
	var int $l_accepted;
	var string $l_entered;
	var ?string $l_userip;
	var int $l_emailvisible;
	var ?string $l_sess_id;
	var ?string $l_sess_ip;
	var ?string $l_sessiondata;
	var ?string $l_lastaccess;
	var int $l_logedin;
	var int $l_forumsort_themes;
	var int $l_forumsort_msg;
	var int $l_disable_youtube;
	var ?int $votes_plus;
	var ?int $votes_minus;
	var ?int $comment_count;
	var ?string $rating;
	var ?int $votes;

	function __construct(?int $l_id = null, ?string $l_hash = null, ?string $l_login = null, ?string $l_nick = null, ?string $l_password = null, ?string $l_email = null, ?int $l_active = null, ?int $l_accepted = null, ?string $l_entered = null, ?string $l_userip = null, ?int $l_emailvisible = null, ?string $l_sess_id = null, ?string $l_sess_ip = null, ?string $l_sessiondata = null, ?string $l_lastaccess = null, ?int $l_logedin = null, ?int $l_forumsort_themes = null, ?int $l_forumsort_msg = null, ?int $l_disable_youtube = null, ?int $votes_plus = null, ?int $votes_minus = null, ?int $comment_count = null, ?string $rating = null, ?int $votes = null)
	{
		if(isset($l_id))$this->l_id = $l_id;
		if(isset($l_hash))$this->l_hash = $l_hash;
		if(isset($l_login))$this->l_login = $l_login;
		if(isset($l_nick))$this->l_nick = $l_nick;
		if(isset($l_password))$this->l_password = $l_password;
		if(isset($l_email))$this->l_email = $l_email;
		if(isset($l_active))$this->l_active = $l_active;
		if(isset($l_accepted))$this->l_accepted = $l_accepted;
		if(isset($l_entered))$this->l_entered = $l_entered;
		if(isset($l_userip))$this->l_userip = $l_userip;
		if(isset($l_emailvisible))$this->l_emailvisible = $l_emailvisible;
		if(isset($l_sess_id))$this->l_sess_id = $l_sess_id;
		if(isset($l_sess_ip))$this->l_sess_ip = $l_sess_ip;
		if(isset($l_sessiondata))$this->l_sessiondata = $l_sessiondata;
		if(isset($l_lastaccess))$this->l_lastaccess = $l_lastaccess;
		if(isset($l_logedin))$this->l_logedin = $l_logedin;
		if(isset($l_forumsort_themes))$this->l_forumsort_themes = $l_forumsort_themes;
		if(isset($l_forumsort_msg))$this->l_forumsort_msg = $l_forumsort_msg;
		if(isset($l_disable_youtube))$this->l_disable_youtube = $l_disable_youtube;
		if(isset($votes_plus))$this->votes_plus = $votes_plus;
		if(isset($votes_minus))$this->votes_minus = $votes_minus;
		if(isset($comment_count))$this->comment_count = $comment_count;
		if(isset($rating))$this->rating = $rating;
		if(isset($votes))$this->votes = $votes;
	}

	function save(): mixed {
		return (new LoginsEntity)->save($this);
	}

	function insert(): mixed {
		return (new LoginsEntity)->insert($this);
	}

	function delete(): bool {
		return (new LoginsEntity)->delete($this->l_id);
	}

	function update(): bool {
		return (new LoginsEntity)->update($this->l_id, $this);
	}
}
