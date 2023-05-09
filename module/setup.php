<?php declare(strict_types = 1);

if(!$i_am_admin)
{
	return;
}

(function(){
	global $sys_root, $sys_user_root;

	$images_dir = join_paths($sys_user_root, 'pic', 'thumb');
	if(!file_exists($images_dir))
	{
		mkdir($images_dir, 0644, true);
	}

	$cache_path = join_paths($sys_root, 'public', 'cache');
	if(!file_exists($cache_path))
	{
		mkdir($cache_path, 0644, true);
	}

})();
