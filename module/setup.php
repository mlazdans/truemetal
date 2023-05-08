<?php declare(strict_types = 1);

if(!$i_am_admin)
{
	return;
}

$thum_dir = $sys_user_root.DIRECTORY_SEPARATOR.'pic'.DIRECTORY_SEPARATOR.'thumb';

if(!file_exists($thum_dir))
{
	mkdir($thum_dir, 0644, true);
}
