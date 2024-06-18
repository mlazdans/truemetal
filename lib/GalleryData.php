<?php declare(strict_types = 1);

class GalleryData
{
	static function get_next_data(int $res_resid, int $gd_id)
	{
		$sql = "SELECT gd_id FROM view_res_gd WHERE res_resid = $res_resid AND gd_id > $gd_id LIMIT 1";

		$data = DB::execute_single($sql);

		return isset($data['gd_id']) ? $data['gd_id'] : 0;
	}

	// function del($gd_id)
	// {
	// 	$sql = sprintf('DELETE FROM gallery_data WHERE gd_id = %d', $gd_id);

	// 	return DB::Execute($sql);
	// }

	// public static function Route($resource)
	// {
	// 	return "/gallery/view/$resource->gd_id/";
	// }

	public static function has_new_comments(ViewResGdType $item)
	{
		return Res::not_seen($item->res_id, $item->res_comment_last_date??$item->res_entered);
	}
}
