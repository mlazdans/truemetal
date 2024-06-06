<?php declare(strict_types = 1);

abstract class AbstractResTemplate extends AbstractTemplate
{
	public ?int $res_id = null;
	public ?int $login_id = null;
	public ?int $res_votes_plus_count = null;
	public ?int $res_votes_minus_count = null;
	public ?string $res_date = null;
	public ?string $res_entered = null;
	public ?string $res_date_short = null;
	public ?string $res_votes = null;
	public ?string $comment_vote_class = null;
	public ?string $res_route = null;
	public ?string $res_name = null;
	public ?string $res_nickname = null;
	public ?string $res_intro = null;
	public ?string $res_data = null;
	public ?string $res_data_compiled = null;
	public ?string $l_hash = null;
	public bool $vote_control_enabled = false;
	public bool $profile_link_enabled = false;
}
