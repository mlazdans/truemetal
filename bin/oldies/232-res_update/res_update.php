<?php declare(strict_types = 1);

require_once('../include/boot.php');
require_once('include/dbconnect.php');

ini_set('memory_limit', '512M');

DB::with_new_trans(function(){
	$sql = "UPDATE res SET
	res_nickname = ?,
	res_email = ?,
	res_ip = ?,
	res_entered = ?,
	res_visible = ?,
	res_name = ?,
	res_intro = ?,
	res_data = ?,
	res_data_compiled = ?,
	res_resid = ?
	WHERE res_id = ?";

	$p = DB::Prepare($sql);

	# Forums
	print "Forums:";
	$q = DB::Query("SELECT * FROM forum ORDER BY forum_id");
	while($r = DB::fetch_object($q))
	{
		unset($PARENT);
		if($r->forum_forumid){
			$PARENT = DB::execute_single("SELECT * FROM forum WHERE forum_id = ?", $r->forum_forumid);
		}

		$data = [
			$r->forum_username,
			$r->forum_useremail,
			$r->forum_userip,
			$r->forum_entered,
			$r->forum_active == 'Y' ? 1 : 0,
			$r->forum_name,
			NULL,
			$r->forum_data,
			$r->forum_datacompiled,
			isset($PARENT) ? $PARENT['res_id'] : NULL,
			$r->res_id
		];

		if(!$p->execute($data)){
			sqlr($sql);
			printr($data);
			return false;
		}

		$p->closeCursor();
	}
	unset($q);
	print "done\n";

	# Articles
	print "Articles:";
	$q = DB::Query("SELECT * FROM article ORDER BY art_id");
	while($r = DB::fetch_object($q)){
		$data = [
			'BigUgga',
			'marrtins@hackers.lv',
			'80.232.240.76',
			$r->art_entered,
			$r->art_active == 'Y' ? 1 : 0,
			$r->art_name,
			$r->art_intro,
			$r->art_data,
			NULL,
			NULL,
			$r->res_id
		];

		if(!$p->Execute($data)){
			sqlr($sql);
			printr($data);
			return false;
		}

		$p->closeCursor();
	}
	unset($q);
	print "done\n";

	# Gallery
	print "Gallery:";
	$q = DB::Query("SELECT * FROM gallery ORDER BY gal_id");
	while($r = DB::fetch_object($q))
	{
		$data = [
			'BigUgga',
			'marrtins@hackers.lv',
			'80.232.240.76',
			$r->gal_entered,
			$r->gal_visible == 'Y' ? 1 : 0,
			$r->gal_name,
			NULL,
			$r->gal_data,
			NULL,
			NULL,
			$r->res_id
		];

		if(!$p->Execute($data)){
			sqlr($sql);
			printr($data);
			return false;
		}

		$p->closeCursor();
	}
	unset($q);
	print "done\n";

	# Gallery data
	print "Gallery data:";
	//`gd_id`, `res_id`, `gal_id`, `gd_filename`, `gd_visible`, `gd_mime`, `gd_data`, `gd_thumb`, `gd_descr`, `gd_entered`
	$q = DB::Query("SELECT `res_id`, `gal_id`, `gd_filename`, `gd_visible`, `gd_descr`, `gd_entered` FROM gallery_data ORDER BY gd_id");
	while($r = DB::fetch_object($q))
	{
		unset($PARENT);
		if($r->gal_id){
			$PARENT = DB::execute_single("SELECT * FROM gallery WHERE gal_id = ?", $r->gal_id);
		}

		$data = [
			'BigUgga',
			'marrtins@hackers.lv',
			'80.232.240.76',
			$r->gd_entered,
			$r->gd_visible == 'Y' ? 1 : 0,
			$r->gd_filename,
			NULL,
			$r->gd_descr,
			NULL,
			isset($PARENT) ? $PARENT['res_id'] : NULL,
			$r->res_id
		];

		if(!$p->Execute($data)){
			sqlr($sql);
			printr($data);
			return false;
		}

		$p->closeCursor();
	}
	unset($q);
	print "done\n";

	# Komenti
	print "Comments:";
	// UPDATE res
	// JOIN (SELECT
	// 	c.*,
	// 	r2.res_id AS res_resid
	// FROM res_comment rc
	// JOIN comment c ON c.c_id = rc.c_id
	// JOIN res r ON r.res_id = c.res_id
	// JOIN res r2 ON r2.res_id = rc.res_id
	// ) AS comm ON comm.res_id = res.res_id
	// SET
	// 	res.res_nickname = comm.c_username,
	// 	res.res_email = comm.c_useremail,
	// 	res.res_ip = comm.c_userip,
	// 	res.res_entered = comm.c_entered,
	// 	res.res_visible = CASE WHEN comm.c_visible = 'Y' THEN 1 ELSE 0 END,
	// 	res.res_name = NULL,
	// 	res.res_intro = NULL,
	// 	res.res_data = comm.c_data,
	// 	res.res_data_compiled = comm.c_datacompiled,
	// 	res.res_resid = comm.res_resid

	$q = DB::Query("SELECT
		c.*,
		r2.res_id AS res_resid
	FROM res_comment rc
	JOIN comment c ON c.c_id = rc.c_id
	JOIN res r ON r.res_id = c.res_id
	JOIN res r2 ON r2.res_id = rc.res_id
	ORDER BY c.c_id
	");

	while($r = DB::fetch_object($q))
	{
		$data = [
			$r->c_username,
			$r->c_useremail,
			$r->c_userip,
			$r->c_entered,
			$r->c_visible == 'Y' ? 1 : 0,
			NULL,
			NULL,
			$r->c_data,
			$r->c_datacompiled,
			$r->res_resid,
			$r->res_id
		];

		if(!$p->Execute($data)){
			sqlr($sql);
			printr($data);
			return false;
		}

		$p->closeCursor();
	}
	unset($q);
	print "done\n";

	print "Commit!\n";
	return true;
});
