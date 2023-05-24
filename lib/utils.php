<?php declare(strict_types = 1);

function invalid(string $value): bool
{
	return !$value || preg_match("/[^a-z^A-Z^0-9_]/", $value);
}

function valid(string $value): bool
{
	return !invalid($value);
}

// function my_strip_tags(string &$text): void
// {
// 	$text = htmlspecialchars($text, ENT_QUOTES);
// }

function get_month(int $i): ?string
{
	return [
		'janvārī', 'februārī', 'martā', 'aprīlī', 'maijā', 'jūnijā', 'jūlijā',
		'augustā', 'septembrī', 'oktobrī', 'novembrī', 'decembrī'
	][$i]??null;
}

function get_day(int $i): ?string {
	return ['svētdiena', 'pirmdiena', 'otrdiena', 'trešdiena', 'ceturtdiena', 'piektdiena', 'sestdiena'][$i]??null;
}

# TODO: rewrite!!!
function parse_text_data(string $data): string
{
	// proc url's - 1pass
	$patt = url_pattern();
	if(preg_match_all($patt, $data, $matches)) {
		$tokens = array();
		foreach($matches[0] as $k=>$v) {
			$tokens[$k] = ' '.substr(md5(uniqid((string)rand(),true)), 0, FORUM_MAXWORDSIZE - 1).' ';
			$data = str_replace($matches[0][$k], $tokens[$k], $data);
		}
	}

	// proc words
	preg_match_all('/(.\s)(\1{'.(integer)(FORUM_MAXWORDSIZE / 2).',})/Uu', $data, $tmp);
	/*
	$data = preg_replace(
		'/(.\s)(\1{'.(integer)(FORUM_MAXWORDSIZE / 2).',})/e',
		"mb_substr('$1', 0, FORUM_MAXWORDSIZE).\"...\"",
		$data
	);
	*/
	$data = preg_replace_callback(
		'/(.\s)(\1{'.(integer)(FORUM_MAXWORDSIZE / 2).',})/',
		function ($m){
			return mb_substr($m[1], 0, FORUM_MAXWORDSIZE)."...";
		},
		$data
	);

	// proc new lines
	$data = preg_replace('/(\n|\r\n){3,}/', '\1\1', $data);

	preg_match_all('/([^\s]*)(\s|$)/', $data, $tmp);
	#preg_match_all('/([^\s\p{P}]*)(\s|\p{P}|$)/u', $data, $tmp);

	$last_matched = false;
	if(isset($tmp[0])) {
		$c = count($tmp[0]);
		for($r = 0; $r < $c; ++$r) {
			if($tmp[1][$r] && preg_match('/^(!|\?){5,}/', $tmp[1][$r])) {
				if($last_matched)
					$tmp[2][$r] = $tmp[1][$r] = $tmp[0][$r] = '';

				$last_matched = true;
			} else
				$last_matched = false;

			if(mb_strlen($tmp[1][$r]) > FORUM_MAXWORDSIZE)
				$tmp[1][$r] = mb_substr($tmp[1][$r], 0, FORUM_MAXWORDSIZE)."...".$tmp[2][$r];
			else
				$tmp[1][$r] .= $tmp[2][$r];
		}
	}

	$w_count = count($tmp[1]);
	array_splice($tmp[1], FORUM_MAXWORDS * 2);

	$data = join("", $tmp[1]);
	// my_strip_tags($data);
	# TODO: glabāt vajadzētu bez parsēšans
	$data = htmlspecialchars($data, ENT_QUOTES);
	$data = preg_replace('/(\r\n|\n)/', '<br />\1', $data);

	// proc pre
	if(preg_match_all('/\[pre\](.*)\[\/pre\]/ims', $data, $m))
	{
		foreach($m[0] as $k=>$search)
		{
			$m[1][$k] = str_replace("<br />", "", $m[1][$k]);
			$data = str_replace($search, "<pre>{$m[1][$k]}</pre>", $data);
		}
	}

	// proc urls - 2pass
	foreach($matches[0] as $k=>$v) {
		$host = $m3 = $matches[3][$k];
		$url = $m4 = $matches[4][$k];

		if(strlen($m4) > 10)
			$m4 = substr($m4, 0, 20).'...';

		if(strlen($m3) > 40) {
			$m3 = substr($m3, 0, 40).'...';
			$m4 = '';
		}

		$url = htmlspecialchars($matches[1][$k].$matches[3][$k].$matches[4][$k], ENT_COMPAT, "UTF-8");
		$url_short = htmlspecialchars($m3.$m4);

		$TMs = array(
			'truemetal.lv',
			'www.truemetal.lv',
			'metal.id.lv',
			'www.metal.id.lv',
			);

		# youtube.com
		if(FALSE && (substr($host, -11) == 'youtube.com') && preg_match('/watch\?v=([^&]*)/i', $url, $url_parts))
		{
			$data = str_replace($tokens[$k], '<div><div class="youtube"><a href="'.$url.'">'.$url_short.'</a></div></div>', $data);
		# truemetal.lv
		} elseif(
			in_array($host, $TMs)
			/*
			(substr($host, -12) == 'truemetal.lv') ||
			(substr($host, -11) == 'metal.id.lv')
			*/
			)
		{
			$url = htmlspecialchars($matches[4][$k], ENT_COMPAT, "utf-8");
			$url_short = $url;
			$data = str_replace($tokens[$k], '<a href="'.$url.'">'.$url_short.'</a>', $data);
		# others
		} else {
			$data = str_replace($tokens[$k], '<a href="'.$url.'">'.$url_short.'</a>', $data);
		}

	}

	// ja pa daudz ievadiits
	if($w_count > FORUM_MAXWORDS)
		$data .= '...';

	return $data;
}

# TODO: dqdp imager
function image_resample(&$in_im, $w = 0, $h = 0, $percent = 0)
{
	$im_h = imagesy($in_im);
	$im_w = imagesx($in_im);
	$koef = $im_h / $im_w;

	// izkalkuleejam izmeerus atkariibaa no parametriem
	if(!$w && !$h)
	{
		if(!$percent)
			die('[FATAL] Missing configuration ($w,$h,$percent)!');
		else
		{
			$w = $im_w * ($percent / 100);
			$h = $im_h * ($percent / 100);
		}
	} elseif($w && !$h) { // !$w && 1$h
		$h = $w * $koef;
	} elseif(!$w) {
		$w = $h / $koef;
	} else {
		if($im_w > $w && $im_h > $h)
		{
			if($im_w / $w > $im_h / $h)
			{
				$h = $w * $koef;
				$w = $h / $koef;
			}	else {
				$w = $h / $koef;
				$h = $w * $koef;
			}
		} elseif($im_w > $w)
			$h = $w * $koef;
		elseif($im_h > $h)
			$w = $h / $koef;
	}

	$w_tmp = $w;
	$h_tmp = $h;
	if($koef > 1) // ja augstums liel_ks, attieciigi pret garumu izmainam augstumu
		$h_tmp = $w_tmp * $koef;
	elseif($koef < 1) // un otraadi
		$w_tmp = $h_tmp / $koef; // dala, jo koef tiek reekinaats attieciibaa pret augstumu

	$w = (int)$w;
	$h = (int)$h;
	$w_tmp = (int)$w_tmp;
	$h_tmp = (int)$h_tmp;

	// gatavaa bilde
	$out_im = imagecreatetruecolor($w, $h);

	// temporary bilde, lai tiktu saglabaatas proporcijas, lai nesanaak skiiba
	$tmp_im = imagecreatetruecolor($w_tmp, $h_tmp);

	// samazinam/palielinam
	imagecopyresampled($tmp_im, $in_im, 0,0, 0,0, $w_tmp,$h_tmp, $im_w,$im_h);

	// izgriezham peec defineetajiem izmeeriem iecentreetu
	$startx = (int)(($w_tmp - $w) / 2);
	$starty = (int)(($h_tmp - $h) / 2);
	imagecopyresized($out_im, $tmp_im, 0,0, $startx,$starty, $w,$h, $w,$h);

	return $out_im;
}

function image_save(&$image, $file, $type, $quality = 80)
{
	if($type == IMAGETYPE_GIF)
		imagegif($image, $file);
	elseif($type == IMAGETYPE_JPEG)
		imagejpeg($image, $file, $quality);
	else
		return FALSE;

	return TRUE;
}

function image_load(&$image, $file)
{
	global $image_load_error;

	$image_load_error = '';

	$data = getimagesize($file);
	$type = $data[2];

	if($type == IMAGETYPE_GIF)
	{
		if(function_exists('imagegif'))
			$image = imagecreatefromgif($file);
		else {
			$image_load_error = 'Nav GIF failu atbalsta!';
			return false;
		}
	} elseif($type == IMAGETYPE_JPEG) {
		if(function_exists('imagejpeg'))
			$image = imagecreatefromjpeg($file);
		else {
			$image_load_error = 'Nav JPEG failu atbalsta!';
			return false;
		}
	} elseif($type == IMAGETYPE_PNG) {
		if(function_exists('imagepng'))
			$image = imagecreatefrompng ($file);
		else {
			$image_load_error = 'Nav PNG failu atbalsta!';
			return false;
		}
	} else {
		$image_load_error = 'Nezināms fails!';
		return false;
	}

	return $type;
}

# TODO: vecais search - get rid off
function search_to_sql_legacy($q, $fields)
{
	$words = explode(' ', $q);
	if(!is_array($fields))
		$fields = array($fields);

	$match = '';
	foreach($words as $word)
	{
		$tmp = '';
		foreach($fields as $field)
			if($field)
				$tmp .= "$field REGEXP '".addslashes(preg_quote($word))."' OR ";
		$tmp = substr($tmp, 0, -4);
		if($tmp)
			$match .= "($tmp) AND ";
	}
	$match = substr($match, 0, -5);
	if($match)
		return "($match)";
	//$match = ",(module_name REGEXP '$q' OR module_data REGEXP '$q') score";
}

function save_upload(string $id, string $save_path): bool
{
	return empty($_FILES[$id]) ? false : move_uploaded_file($_FILES[$id]['tmp_name'], $save_path);
}

function _GET(?string $qs = null): array
{
	$qs = trim($qs??$_SERVER["QUERY_STRING"]??"");

	if(!$qs){
		return [];
	}

	foreach(explode('&', $qs) as $pair)
	{
		$parts = explode('=', $pair, 2);
		$k = $parts[0] ?? "";
		$v = $parts[1] ?? "";

		if(str_ends_with($k, '[]'))
		{
			$ret[substr($k, 0, -2)][] = $v;
		} else {
			$ret[$k] = $v;
		}
	}

	return $ret;
}

function is_not_empty($v)
{
	return !empty($v);
}

function innerHTML(&$dom, &$node, $html = false)
{
	## if html parameter not specified, return the current contents of $node
	if($html === false)
	{
		$doc = new DOMDocument();
		foreach ($node->childNodes as $child)
		$doc->appendChild($doc->importNode($child, true));

		return $doc->saveHTML();
	} else {
		## get rid of all current children
		foreach ($node->childNodes as $child)
			$node->removeChild($child);

		## if html is empty, we are done.
		if($html == '')
			return;

		## load up $html as DOM fragment, append it to our now-empty $node
		$f = $dom->createDocumentFragment();
		$f->appendXML($html);
		$node->appendChild( $f );
	}
}

function cache_save($h, $data)
{
	global $sys_root;

	$abs_path = join_paths($sys_root, 'public', 'cache', $h);
	$dir = dirname($abs_path);

	// $key = ftok("$sys_root/public/cache", "T");
	// //$key = crc32($h) % 101 + 0xBADBEEF;
	// $se = sem_get($key);
	// sem_acquire($se);

	if(!file_exists($dir))
		mkdir($dir, 0644, true);

	$status = true;
	if(!file_exists($abs_path))
		$status = file_put_contents($abs_path, $data, LOCK_EX);

	// sem_release($se);

	return $status;
} // cache_save

function cache_exists($h, $levels = 2)
{
	global $sys_root;

	return file_exists("$sys_root/public/cache/$h");
} // cache_exists

function cache_read($h)
{
	global $sys_root;

	return file_get_contents("$sys_root/public/cache/$h");
} // cache_read

function cache_http_path($h)
{
	return "/cache/$h";
} // cache_http_path

function cache_hash($id, $levels = 2)
{
	$hash = (string)crc32($id);
	$l = strlen($hash);

	$path = '';
	for($i = 1; $i <= $levels; $i++){
		$path .= substr($hash, $l - $i, 1).DIRECTORY_SEPARATOR;
	}
	$path .= "$hash-$id";

	return $path;
} // cache_hash

function ignored(?array $data, string $field): bool
{
	return falsed($data, $field);
}

function defaulted(?array $data, string $field): bool
{
	return !isset($data[$field]);
}

function falsed(?array $data, string $field): bool
{
	return isset($data[$field]) && ($data[$field] === false);
}
