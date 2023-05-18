<?php declare(strict_types = 1);

class Res
{
	// protected ResFilter $F;

	// function __construct(ResFilter $F = new ResFilter)
	// {
	// 	$this->F = $F;
	// }

	// function get_filter(): ResFilter
	// {
	// 	return $this->F;
	// }

	// function load_single(): ?ViewResType
	// {
	// 	return parent::_load_single();
	// }

	// function load(): ViewResCollection
	// {
	// 	return (new ViewResEntity)->getAll($this->F);
	// }

	// function load_by_id(int $res_id): ?ViewResType
	// {
	// 	$this->F->res_id = $res_id;

	// 	return $this->load_single();
	// }

	// function load_by_res_id(int $res_id): ?ViewResType
	// {
	// 	return $this->load_by_id($res_id);
	// }

	static function hasNewComments(int $res_id, ?string $date = null, ?int $comment_count = null): bool
	{
		if(!User::logged()){
			return false;
		}

		# TODO: pārkonvertēt datumus jau uz timestamp!!!
		if(isset($_SESSION['res']['viewed_date'][$res_id]) && $date){
			return (strtotime($date) > strtotime($_SESSION['res']['viewed_date'][$res_id]));
		} elseif(isset($_SESSION['res']['viewed_before']) && $date){
			return ($_SESSION['res']['viewed_before'] < strtotime($date));
		}

		return $comment_count > 0;
	}

	# TODO: saglabāt tikai time stamp. Pirms tam jāpārkonvertē arī sessijās
	static function markAsSeen(int $res_id): void
	{
		if(User::logged()){
			$_SESSION['res']['viewed_date'][$res_id] = date('Y-m-d H:i:s');
		}
	}

	# Uzstāda res objektu, par pamatu ņemot logged in useri
	static function prepare_with_user(
		int $res_resid = null,
		int $table_id,
		string $res_data,
		?string $res_name = null,
		?string $res_intro = null,
		?string $res_data_compiled = null,
		): ResType
	{
		return ResType::initFrom(new ResDummy(
			res_resid: $res_resid,
			table_id: $table_id,
			login_id: User::id(),
			res_nickname: User::get_val('l_nick'),
			res_email: User::get_val('l_email'),
			res_ip: User::ip(),
			res_name: $res_name,
			res_intro: $res_intro,
			res_data: $res_data,
			res_data_compiled: $res_data_compiled ? $res_data_compiled : parse_text_data($res_data),
		));
	}

	# TODO: varbūt vajadzētu kaut kā apvienot daudzās vietas, kur šis tiek izsaukt, jo
	# patlaban datu validācija notiek katrā izsaukšanas vietā
	static function user_add_comment(int $res_id, string $c_data): ?int
	{
		$R = static::prepare_with_user(
			res_resid: $res_id,
			table_id: ResKind::COMMENT,
			res_data: $c_data,
		);

		return DB::withNewTrans(function() use ($R){
			if($res_id = $R->insert()){
				return CommentType::initFrom(new CommentDummy(
					res_id: $res_id
				))->insert();
			}
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
