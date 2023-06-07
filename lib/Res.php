<?php declare(strict_types = 1);

class Res
{
	static function is_marked_since(int $res_id, ?string $date = null): bool
	{
		if(!User::logged()){
			return false;
		}

		if(empty($date)){
			return false;
		}

		if(($ts = strtotime($date)) === false){
			return false;
		}

		if(isset($_SESSION['res_seen_ts'][$res_id])){
			return $ts > $_SESSION['res_seen_ts'][$res_id];
		}

		if(isset($_SESSION['res_marked_seen_ts'])){
			return $ts > $_SESSION['res_marked_seen_ts'];
		}

		return false;
	}

	static function mark_as_seen(int $res_id): void
	{
		if(User::logged()){
			$_SESSION['res_seen_ts'][$res_id] = time();
		}
	}

	# Uzstāda res objektu, par pamatu ņemot logged in useri
	static function prepare_with_user(
		int $res_resid = null,
		int $res_kind,
		string $res_data,
		?string $res_name = null,
		?string $res_intro = null,
		?string $res_data_compiled = null,
		): ResType
	{
		return new ResType(
			res_resid: $res_resid,
			res_kind: $res_kind,
			login_id: User::id(),
			res_nickname: User::get_val('l_nick'),
			res_email: User::get_val('l_email'),
			res_ip: User::ip(),
			res_name: $res_name,
			res_intro: $res_intro,
			res_data: $res_data,
			res_data_compiled: $res_data_compiled ? $res_data_compiled : parse_text_data($res_data),
		);
	}

	# TODO: varbūt vajadzētu kaut kā apvienot daudzās vietas, kur šis tiek izsaukt, jo
	# patlaban datu validācija notiek katrā izsaukšanas vietā
	static function user_add_comment(int $res_id, string $c_data): bool
	{
		$R = static::prepare_with_user(
			res_resid: $res_id,
			res_kind: ResKind::COMMENT,
			res_data: $c_data,
		);

		return DB::withNewTrans(function() use ($R){
			if($new_res_id = $R->insert()){
				if($c_id = (new CommentType(
					res_id: $new_res_id
				))->insert()) {
					if($new = ViewResCommentEntity::getById($c_id)){
						$U = new ResType(res_id:$new_res_id, res_route:$new->Route());
						if($U->update()){
							header("Location: $U->res_route");
							return true;
						}
					}
				};
			}

			return false;
		});
	}

	static function get_comments(int $res_id, ?ResFilter $F = new ResFilter()): ViewResCommentCollection
	{
		$F->res_resid = $res_id;

		if(empty($F->getOrderBy())){
			$F->OrderBy('res_entered');
		}

		return (new ViewResCommentEntity())->getAll($F);
	}

}
