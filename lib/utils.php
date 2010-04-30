<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// dazaadas paliigfunkcijas

define('REMOVE_TABLE', 1);
define('REMOVE_FONT', 2);

function invalid($value)
{
	return ereg("[^a-z^A-Z^0-9_]", $value) or !$value;
} // invalid

function valid($value)
{
	return !invalid($value);
} // valid

function parse_params($data)
{
	$ret = array();
	foreach($data as $key=>$value)
	{
		if(trim($value))
			$ret[] = rawurldecode($value);
	}

	return $ret;
} // parse_params


function my_strip_tags(&$text)
{
	$text = htmlspecialchars(stripslashes($text), ENT_QUOTES);
} // my_strip_tags

function remove_shit(&$data, $filter = 0)
{
	$filter = (integer)$filter;
	$patt = array();
	$repl = array();

	if($filter & REMOVE_TABLE) {
		$patt = array_merge($patt,
			array(
				'/<table([^<]*)>/imsU',
				'/<\/table>/imsU',
				'/<tbody([^<])*>/imsU',
				'/<\/tbody>/imsU',
				'/<tr([^<])*>/imsU',
				'/<\/tr>/imsU',
				'/<td([^<])*>/imsU',
				'/<\/td>/imsU'
			)
		);
		$repl = array_merge($repl, array_fill(0, 8, ''));
	}

	$patt[] = '/<p([^<]*)>/imsU';
	$repl[] = '<p>';

	if($filter & REMOVE_FONT) {
		$patt = array_merge($patt,
			array(
				'/<font([^<]*)>/imsU',
				'/<\/font>/imsU'
			)
		);
		$repl = array_merge($repl, array_fill(0, 2, ''));
	}

	$patt = array_merge($patt,
		array(
			'/<span([^<]*)>/imsU',
			'/<\/span>/imsU',
			'/<strong([^<]*)>/imsU',
			'/<\/strong>/imsU',
			'/<o:p>/imsU',
			'/<\/o:p>/imsU',
			'/<p>(&nbsp;)*<\/P>/imsU',
			'/<\?xml([^<]*)\/>/imsU',
			'/<.:([^<]*)>/imsU',
			'/<\/.:([^<]*)>/imsU',
			'/(\t)+/',
			'/(\t\n)/',
			'/(\n)+/',
			'/(\r\n)+/'
		)
	);
	$repl = array_merge($repl,
		array(
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			"\r\n",
			"\r\n"
		)
	);

	$data = preg_replace($patt, $repl, $data);
} // remove_shit

function proc_date($date)
{
	# $date - strtotime acceptable date
	# XXX: NOT TRUE $date format Y:M:D:H:M - 2002:07:27:23:12
	$M = array(
		'janvārī',
		'februārī',
		'martā',
		'aprīlī',
		'maijā',
		'jūnijā',
		'jūlijā',
		'augustā',
		'septembrī',
		'oktobrī',
		'novembrī',
		'decembrī'
	);
	$D = array(
		'šodien',
		'vakar',
		'aizvakar'
	);

	$date_now = date("Y:m:j:H:i");
	@list($y0, $m0, $d0, $h0, $min0) = split(":", date("Y:m:j:H:i", strtotime($date)));
	@list($y1, $m1, $d1, $h1, $min1) = split(":", $date_now);
	// mktime ( [int hour [, int minute [, int second [, int month [, int day [, int year [, int is_dst]]]]]]])
	$dlong0 = mktime($h0, $min0, 0, $m0, $d0, $y0);
	$dlong1 = mktime($h1, $min1, 0, $m1, $d1, $y1);
	$diff = date('z', $dlong1) - date('z', $dlong0);

	$retdate = '';

	if( ($diff < 3) && ($y1 == $y0) )
	//if( ($diff < 3) /*&& ($y1 == $y0)*/ )
	{
		$retdate .= $D[$diff];
	} else {
		if($y1 != $y0)
			$retdate .= "$y0. gada ";
		$retdate.= "$d0. ".$M[$m0 - 1];
	}

	if((integer)$h0 || (integer)$min0)
		$retdate .= ", plkst. $h0:$min0";

	return $retdate;
} // proc_date

function url_pattern()
{
	$url_patt = $path_patt = '';
	return "/(http(s?):\/\/|ftp:\/\/|telnet:\/\/|dchub:\/\/|ed2k:\/\/|mailto:|callto:)([^\/\s\t\n\r\!\'\<>(\)]".$url_patt."*)([^\s\t\n\r\!\'\<>(\)]".$path_patt."*)/is";
} // url_pattern

function parse_text_data(&$data)
{
	// proc url's - 1pass
	$patt = url_pattern();
	if(preg_match_all($patt, $data, $matches)) {
		$tokens = array();
		foreach($matches[0] as $k=>$v) {
			$tokens[$k] = ' '.substr(md5(uniqid(rand(),1)), 0, FORUM_MAXWORDSIZE - 1).' ';
			$data = str_replace($matches[0][$k], $tokens[$k], $data);
		}
	}

	// proc words
	preg_match_all('/(.\s)(\1{'.(integer)(FORUM_MAXWORDSIZE / 2).',})/U', $data, $tmp);
	$data = preg_replace(
		'/(.\s)(\1{'.(integer)(FORUM_MAXWORDSIZE / 2).',})/e',
		"mb_substr('$1', 0, FORUM_MAXWORDSIZE).\"...\"",
		$data
	);

	// proc new lines
	$data = preg_replace('/(\n|\r\n){3,}/', '\1\1', $data);

	preg_match_all('/([^\s]*)(\s|$)/', $data, $tmp);

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

			if(strlen($tmp[1][$r]) > FORUM_MAXWORDSIZE)
				$tmp[1][$r] = substr($tmp[1][$r], 0, FORUM_MAXWORDSIZE)."...".$tmp[2][$r];
			else
				$tmp[1][$r] .= $tmp[2][$r];
		}
	}

	$w_count = count($tmp[1]);
	array_splice($tmp[1], FORUM_MAXWORDS * 2);

	$data = join("", $tmp[1]);
	my_strip_tags($data);
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

		$url = htmlspecialchars($matches[1][$k].$matches[3][$k].$matches[4][$k], ENT_COMPAT, "utf-8");
		$url_short = htmlspecialchars($m3.$m4);

		# youtube.com
		if(FALSE && (substr($host, -11) == 'youtube.com') && preg_match('/watch\?v=([^&]*)/i', $url, $url_parts))
		{
			$data = str_replace($tokens[$k], '<div><div class="youtube"><a href="'.$url.'">'.$url_short.'</a></div></div>', $data);
		# truemetal.lv
		} elseif(
			(substr($host, -12) == 'truemetal.lv') ||
			(substr($host, -11) == 'metal.id.lv')
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

	$data = addslashes($data);
} // parse_text_data

function set_forum(&$template, $forum_id)
{
	global $forum;

	$tree = $forum->get_tree($forum_id);
	if(!$tree)
		return;

	$template->enable('BLOCK_forum_path');
	$forum_path = '';

	foreach($tree as $key=>$item)
	{
		if(isset($tree[$key + 0]))
		{
			$forum_path = $item['forum_id'].'/';
			$template->set_var('forum1_id', $item['forum_id'], 'BLOCK_middle');
			$template->set_var('forum1_name', addslashes($item['forum_name']), 'BLOCK_middle');
			$template->set_var('forum1_path', $forum_path, 'BLOCK_middle');
			$template->parse_block('BLOCK_forum_path', TMPL_APPEND);
		}
	}

} // set_forum

function valid_date($date)
{
	list($d, $m, $y) = split('\.', $date);
	return checkdate($m, $d, $y);
} // valid_date

function substitute_change($str)
{
	$patt = array(
		"'Ā'", "'Č'", "'Ē'", "'Ģ'", "'Ī'", "'Ķ'", "'Ļ'", "'Ņ'", "'Ō'", "'Ŗ'", "'Š'", "'Ū'", "'Ž'",
		"'ā'", "'č'", "'ē'", "'ģ'", "'ī'", "'ķ'", "'ļ'", "'ņ'", "'ō'", "'ŗ'", "'š'", "'ū'", "'ž'",
	);
	$repl = array(
		"A", "C", "E", "G", "I", "K", "L", "N", "O", "R", "S", "U", "Z",
		"a", "c", "e", "g", "i", "k", "l", "n", "o", "r", "s", "u", "z",
	);

	return preg_replace($patt, $repl, $str);
} // substitute_change

function substitute($str)
{
	$patt = array(
		"/([ĀČĒĢĪĶĻŅŌŖŠŪŽ])/iue"
	);
	$repl = array(
		"'[$1|'.substitute_change('$1').']'"
	);
	return preg_replace($patt, $repl, $str);
} // substitute

function valid_host($host)
{
	$testip = gethostbyname($host);
	$test1 = ip2long($testip);
	$test2 = long2ip($test1);

	return ($testip == $test2);
} // valid_host

function valid_email($email)
{
	if(!$email)
		return false;

	$parts = split('@', $email);

	if(count($parts) != 2)
		return false;

	list($username, $domain) = $parts;

	return ($username and $domain and (valid_host($domain) || checkdnsrr($domain)));
	//return $username and $domain and valid_host($domain);
} // valid_email

function get_modules($admin = false)
{
	global $sys_root;

	if($admin)
		$path = $sys_root.'/modules/admin';
	else
		$path = $sys_root.'/modules';

	$modules = array();
	if($dir = opendir($path)) {
		while($file = readdir($dir))
			if(filetype($path.'/'.$file) != 'dir') {
				if(preg_match('/^module\.(.*).php/i', $file, $m))
					$modules[] = $m[1];
			}
	} // open dir

	sort($modules);
	return $modules;
} // get_modules

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

	// gatavaa bilde
	$out_im = imagecreatetruecolor($w, $h);

	// temporary bilde, lai tiktu saglabaatas proporcijas, lai nesanaak skiiba
	$tmp_im = imagecreatetruecolor($w_tmp, $h_tmp);

	// samazinam/palielinam
	imagecopyresampled($tmp_im, $in_im, 0,0, 0,0, $w_tmp,$h_tmp, $im_w,$im_h);

	// izgriezham peec defineetajiem izmeeriem iecentreetu
	$startx = ($w_tmp - $w) / 2;
	$starty = ($h_tmp - $h) / 2;
	imagecopyresized($out_im, $tmp_im, 0,0, $startx,$starty, $w,$h, $w,$h);

	return $out_im;
} // image_resample

function image_save(&$image, $file, $type, $quality = 80)
{
	if($type == IMAGETYPE_GIF)
		imagegif($image, $file);
	elseif($type == IMAGETYPE_JPEG)
		imagejpeg($image, $file, $quality);
	else
		return FALSE;

	return TRUE;
} // image_save

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
} // image_load

function strip_script(&$data, &$keys, &$scripts)
{
	$patts = array(
		'/<script[^>]*>([^<]*)<\/script>/imsU',
		'/<title[^>]*>([^<]*)<\/title>/imsU',
		'/<head[^>]*>([^<]*)<\/head>/imsU',
		'/<style[^>]*>([^<]*)<\/style>/imsU',
		'/<object[^>]*>([^<]*)<\/object>/imsU',
		'/&[^;]*;/sU'
		//&nbsp;
	);
	foreach($patts as $patt) {
		preg_match_all($patt, $data, $m);
		for($r = 0; $r < count($m[0]); ++$r) {
			$token = '<'.md5(uniqid(rand(),1)).'>';
			$keys[] = $token;
			$scripts[] = $m[0][$r];
			$data = str_replace($m[0][$r], $token, $data);
		}
		//print_r($m);
	}
} // strip_script

function unstrip_script(&$data, &$keys, &$scripts)
{
	for($r = 0; $r < count($keys); ++$r)
		$data = str_replace($keys[$r], $scripts[$r], $data);
} // unstrip_script

function parse_search_q($q)
{
	$q = preg_quote($q);
	//$q = preg_replace('/[\h\v]/ims', ' ', $q);
	//$q = preg_replace('/[\pPSZ]/uims', ' ', $q);
	$q = preg_replace('/[\pP\pZ\pS\pC}]/uims', ' ', $q);
	//$q = preg_replace('/(\n\r|\n)+/ims', ' ', $q);
	$q = preg_replace('/(\s)+/uims', ' ', $q);

	return trim($q);
} // parse_search_q

function parse_mysql_search_q($q)
{
	return preg_replace('/[%\'_]/', '\$1', parse_search_q($q));
} // parse_mysql_search_q

/*
function hl(&$data, $kw)
{
	strip_script($data, $keys, $scripts);
	$colors = array('white', 'white', 'black', 'white');
	$bg = array('red', 'blue', 'yellow', 'magenta');
	$cc = count($colors);
	$bc = count($bg);

	$words = split(' ', $kw);
	// duplikaati nafig
	$words = array_unique($words);

	foreach($words as $index=>$word) {
		$color = $colors[$index % $cc];
		$bgcolor = $bg[$index % $bc];
		$data = ">$data<";
		$patt = "/(>[^<]*)(".$word.")([^>]*)<?/imsU";
		$data = preg_replace($patt, "$1<font style=\"background-color: $bgcolor\" color=\"$color\"><b>$2</b></font>$3", substr($data, 1, strlen($data)-2));
	}
	unstrip_script($data, $keys, $scripts);
} // hl
*/
function hl(&$data, $kw)
{
	strip_script($data, $keys, $scripts);
	$colors = array('white', 'white', 'black', 'white');
	$bg = array('red', 'blue', 'yellow', 'magenta');
	$cc = count($colors);
	$bc = count($bg);

	$kw = trim(preg_replace("/[\*\(\)\-\+]/", " ", $kw));

	$words = split(' ', $kw);
	// duplikaati nafig
	$words = array_unique($words);

	//$tokens = array();
	foreach($words as $index=>$word)
	{
		$word = preg_replace('/[<>\/]/', '', $word);
		$word = substitute(preg_quote($word));
		if(empty($word))
			continue;

		$color = $colors[$index % $cc];
		$bgcolor = $bg[$index % $bc];
		$data = ">$data<";
		$patt = "/(>[^<]*)(".substitute(preg_quote($word)).")([^>]*)<?/imsUu";
		$data = preg_replace($patt, "$1<span style=\"background-color: $bgcolor; color: $color; font-weight: bold;\">$2</span>$3", $data);
		$data = mb_substr($data, 1, mb_strlen($data)-2);
	}

	unstrip_script($data, $keys, $scripts);
} // hl

function search_to_sql($q, $fields)
{
	$words = split(' ', $q);
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
} // search_to_sql

function search_to_spider($q, $fields)
{
	$ret = array();

	$words = split(' ', $q);
	if(!is_array($fields))
		$fields = array($fields);

	$match = '';
//	$words = array_unique($words);
	foreach($words as $word)
	{
		$tmp = '';
		foreach($fields as $field)
			if($field)
				$tmp .= "$field = '".addslashes(preg_quote($word))."' OR ";
		$tmp = substr($tmp, 0, -4);
		if($tmp)
			$match .= "($tmp) OR ";
	}
	$match = substr($match, 0, -4);

	if($match)
	{
		$ret['match'] = "($match)";
		$ret['words'] = $words;

		return $ret;
	}
} // search_to_spider

function set_area(&$template, $block = 'BLOCK_area', $area_id = 0)
{
	global $area;

	foreach($area as $k=>$v) {
		$template->set_var('area_id', $k + 1);
		$template->set_var('area_value', $v);
		if($area_id && ($area_id == $k + 1))
			$template->set_var('area_selected', ' selected');
		else
			$template->set_var('area_selected', '');
		$template->parse_block($block, TMPL_APPEND);
	}

} // set_area

function email($to, $subj, $msg, $attachments = array())
{
	global $sys_mail_from, $mail_params;

	require_once('Mail.php');
	require_once('Mail/mime.php');

	$headers = array(
		'From'=>$sys_mail_from,
		'Subject'=>$subj,
	);

	$mime = new Mail_Mime("\n");
	$mime->setTxtBody($msg);
	foreach($attachments as $file)
	{
		$filename = $file['tmp_name'];
		$filename_show = $file['name'];
		$type = $file['type'];
		$mime->addAttachment($filename, $type, $filename_show);
	}

	$body = $mime->get();
	$hdrs = $mime->headers($headers);

	if(empty($mail_params))
	{
		$mail = Mail::factory('mail');
	} else {
		$mail = Mail::factory('mail', $mail_params);
	}
	$e = $mail->send($to, $hdrs, $body);

	if($e !== TRUE)
	{
		$ret = false;
	} else {
		$ret = true;
	}

	return $ret;
} // email

/*
function email($to, $subj, $msg)
{
	global $sys_mail_from;

	$headers = "FROM: $sys_mail_from\n";
	$ret = @mail($to, $subj, $msg, $headers);
	if(!$ret)
	{
		$GLOBALS['php_errormsg'] = $php_errormsg;
	}

	return $ret;
} // email
*/

function user_loged()
{
	return !empty($_SESSION['login']['l_id']);
} // user_loged

function parse_form_data_array(&$data)
{
	foreach($data as $k=>$v)
	{
		if(is_array($v))
		{
			parse_form_data_array($v);
			$data[$k] = $v;
		} else
			$data[$k] = parse_form_data($v);
	}
} // parse_form_data_array

function parse_form_data($data)
{
	global $config;

	if(get_magic_quotes_gpc())
		$data = stripslashes($data);

	return htmlspecialchars($data, ENT_COMPAT, $GLOBALS['sys_encoding']);
} // parse_form_data

function ent($data)
{
	if(is_array($data))
	{
		foreach($data as $k=>$v)
			$data[$k] = ent($v);
		return $data;
	} else {
		return htmlentities($data, ENT_COMPAT, 'UTF-8');
	}
} // ent

function save_file($id, $save_path)
{
	$some_file = isset($_FILES[$id]) ? $_FILES[$id] : array();
	if(!$some_file)
		return false;

	$ct = $some_file['type'];
	if(move_uploaded_file($some_file['tmp_name'], $save_path))
	{
		return $ct;
	}

	return false;
} // save_file

function to_int($val)
{
	if(ereg('[^0-9]', trim($val)))
	{
		$val = 0;
	} else {
		settype($val, 'integer');
	}

	return $val;
} // to_int

function to_float($val)
{
	if(ereg('[^0-9\.]', trim($val)))
	{
		$val = 0;
	} else {
		$parts = split('\.', $val);
		if(count($parts) > 2)
		{
			$val = 0;
		} else {
			settype($val, 'float');
		}
	}

	return $val;
} // to_float

function to_range($val, $range, $default = '')
{
	$range_a = preg_split('//', $range);
	if(!$val || !in_array($val, $range_a))
	{
		$val = $default;
	}

	return $val;
} // to_range

function printr(&$data)
{
	print "<pre>";
	print_r($data);
	print "</pre>";
} // printr

function _GET()
{
	$ret = array();
	$qs = isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : '';
	if($qs)
	{
		$pairs = split('&', $qs);
		foreach($pairs as $kv)
		{
			$parts = split('=', $kv);
			$k = isset($parts[0]) ? $parts[0] : false;
			$v = isset($parts[1]) ? $parts[1] : false;

			# Arrays
			if(substr($k, -2) == '[]')
			{
				$ka = substr($k, 0, -2);
				if(empty($ret[$ka]))
					$ret[$ka] = array();
				$ret[$ka][] = $v;
			} else {
				$ret[$k] = $v;
			}
		}
	}

	return $ret;
}

function get($key, $default = '')
{
	return isset($_GET[$key]) ? $_GET[$key] : $default;
} // get

function post($key, $default = '')
{
	return isset($_POST[$key]) ? $_POST[$key] : $default;
} // post

function postget($key, $default = '')
{
	return isset($_POST[$key]) ? $_POST[$key] : get($key, $default);
} // postget

function sess($key, $default = '')
{
	return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
} // sess

function cookie($key, $default = '')
{
	return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
} // cookie

function server($key, $default = '')
{
	return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
} // server

function upload($key, $default = '')
{
	return isset($_FILES[$key]) ? $_FILES[$key] : $default;
} // upload

function fix_path($path)
{
	return str_replace('\\', '/', $path);
} // fix_path

function redirect($url = '')
{
	$url = $url ? $url : php_self();

	return header("Location: $url");
} // redirect

function php_self()
{
	return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
} // php_self

function is_not_empty($v)
{
	return !empty($v);
} // is_not_empty

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
} // innerHTML

/*
if (!function_exists('http_build_url'))
{
	define('HTTP_URL_REPLACE', 1);				// Replace every part of the first URL when there's one of the second URL
	define('HTTP_URL_JOIN_PATH', 2);			// Join relative paths
	define('HTTP_URL_JOIN_QUERY', 4);			// Join query strings
	define('HTTP_URL_STRIP_USER', 8);			// Strip any user authentication information
	define('HTTP_URL_STRIP_PASS', 16);			// Strip any password authentication information
	define('HTTP_URL_STRIP_AUTH', 32);			// Strip any authentication information
	define('HTTP_URL_STRIP_PORT', 64);			// Strip explicit port numbers
	define('HTTP_URL_STRIP_PATH', 128);			// Strip complete path
	define('HTTP_URL_STRIP_QUERY', 256);		// Strip query string
	define('HTTP_URL_STRIP_FRAGMENT', 512);		// Strip any fragments (#identifier)
	define('HTTP_URL_STRIP_ALL', 1024);			// Strip anything but scheme and host

	// Build an URL
	// The parts of the second URL will be merged into the first according to the flags argument.
	//
	// @param	mixed			(Part(s) of) an URL in form of a string or associative array like parse_url() returns
	// @param	mixed			Same as the first argument
	// @param	int				A bitmask of binary or'ed HTTP_URL constants (Optional)HTTP_URL_REPLACE is the default
	// @param	array			If set, it will be filled with the parts of the composed url like parse_url() would return
	function http_build_url($url, $parts=array(), $flags=HTTP_URL_REPLACE, &$new_url=false)
	{
		$keys = array('user','pass','port','path','query','fragment');

		// HTTP_URL_STRIP_ALL becomes all the HTTP_URL_STRIP_Xs
		if ($flags & HTTP_URL_STRIP_ALL)
		{
			$flags |= HTTP_URL_STRIP_USER;
			$flags |= HTTP_URL_STRIP_PASS;
			$flags |= HTTP_URL_STRIP_PORT;
			$flags |= HTTP_URL_STRIP_PATH;
			$flags |= HTTP_URL_STRIP_QUERY;
			$flags |= HTTP_URL_STRIP_FRAGMENT;
		}
		// HTTP_URL_STRIP_AUTH becomes HTTP_URL_STRIP_USER and HTTP_URL_STRIP_PASS
		else if ($flags & HTTP_URL_STRIP_AUTH)
		{
			$flags |= HTTP_URL_STRIP_USER;
			$flags |= HTTP_URL_STRIP_PASS;
		}

		// Parse the original URL
		$parse_url = parse_url($url);

		// Scheme and Host are always replaced
		if (isset($parts['scheme']))
			$parse_url['scheme'] = $parts['scheme'];
		if (isset($parts['host']))
			$parse_url['host'] = $parts['host'];

		// (If applicable) Replace the original URL with it's new parts
		if ($flags & HTTP_URL_REPLACE)
		{
			foreach ($keys as $key)
			{
				if (isset($parts[$key]))
					$parse_url[$key] = $parts[$key];
			}
		}
		else
		{
			// Join the original URL path with the new path
			if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH))
			{
				if (isset($parse_url['path']))
					$parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '', $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
				else
					$parse_url['path'] = $parts['path'];
			}

			// Join the original query string with the new query string
			if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY))
			{
				if (isset($parse_url['query']))
					$parse_url['query'] .= '&' . $parts['query'];
				else
					$parse_url['query'] = $parts['query'];
			}
		}

		// Strips all the applicable sections of the URL
		// Note: Scheme and Host are never stripped
		foreach ($keys as $key)
		{
			if ($flags & (int)constant('HTTP_URL_STRIP_' . strtoupper($key)))
				unset($parse_url[$key]);
		}


		$new_url = $parse_url;

		return
			 ((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
			.((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' . $parse_url['pass'] : '') .'@' : '')
			.((isset($parse_url['host'])) ? $parse_url['host'] : '')
			.((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
			.((isset($parse_url['path'])) ? $parse_url['path'] : '')
			.((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '')
			.((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '')
		;
	}
} // http_build_url
*/

function urlize($name)
{
	$name = mb_strtolower($name);
	$name = strip_tags($name);
	$name = preg_replace("/\s+/", "-", $name);

	return $name;
} // urlize

