<?php declare(strict_types = 1);

class GalleryData
{
	static function get_next_data(int $res_resid, int $gd_id)
	{
		$sql = "SELECT gd_id FROM view_res_gd WHERE res_resid = $res_resid AND gd_id > $gd_id LIMIT 1";

		$data = DB::ExecuteSingle($sql);

		return isset($data['gd_id']) ? $data['gd_id'] : 0;
	}

	// function del($gd_id)
	// {
	// 	$sql = sprintf('DELETE FROM gallery_data WHERE gd_id = %d', $gd_id);

	// 	return DB::Execute($sql);
	// }

	public static function Route($resource, $c_id = 0)
	{
		return "/gallery/view/$resource->gd_id/".($c_id ? "#comment$c_id" : "");
	}

	public static function hasNewComments(ViewResGDType $item)
	{
		return Res::hasNewComments($item->res_id, $item->res_comment_last_date, $item->res_child_count);
	}
}
