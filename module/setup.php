<?php declare(strict_types = 1);

if(!$i_am_admin)
{
	return;
}

# TODO: mkdir sphinx/data, sphinx/log sphinx/binlog

// Fix mysql dump
// sed -i 's/ALTER DATABASE `truemetal` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;//g' truemetal.sql
// sed -i 's/ALTER DATABASE `truemetal` CHARACTER SET utf8 COLLATE utf8_unicode_ci ;//g' truemetal.sql
// sed -i 's/DEFINER=[^*]*\*/\*/g' truemetal.sql
// sed -i 's/ DEFINER=`root`@`localhost`//g' truemetal.sql

(function(){
	global $sys_root, $sys_user_root;

	$images_dir = join_paths($sys_user_root, 'pic', 'thumb');
	if(!file_exists($images_dir))
	{
		mkdir($images_dir, 0744, true);
	}

	$cache_path = join_paths($sys_root, 'public', 'cache');
	if(!file_exists($cache_path))
	{
		mkdir($cache_path, 0744, true);
	}

	$cache_path = join_paths($sys_root, 'tmp');
	if(!file_exists($cache_path))
	{
		mkdir($cache_path, 0744, true);
	}

})();

// apt install php8.2-intl
printr("extension_loaded(intl):".(int)extension_loaded('intl'));
