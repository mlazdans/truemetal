<?
error_reporting(E_ALL);
/*
case 'i':ONIG_OPTION_IGNORECASE;
case 'x':ONIG_OPTION_EXTEND;
case 'm':ONIG_OPTION_MULTILINE;
case 's':ONIG_OPTION_SINGLELINE;
case 'p':ONIG_OPTION_MULTILINE | ONIG_OPTION_SINGLELINE;
case 'l':ONIG_OPTION_FIND_LONGEST;
case 'n':ONIG_OPTION_FIND_NOT_EMPTY;
case 'j':ONIG_SYNTAX_JAVA;
case 'u':ONIG_SYNTAX_GNU_REGEX;
case 'g':ONIG_SYNTAX_GREP;
case 'c':ONIG_SYNTAX_EMACS;
case 'r':ONIG_SYNTAX_RUBY;
case 'z':ONIG_SYNTAX_PERL;
case 'b':ONIG_SYNTAX_POSIX_BASIC;
case 'd':ONIG_SYNTAX_POSIX_EXTENDED;
case 'e':eval;
*/

function mb_hl(&$data, $kw)
{
	strip_script($data, $keys, $scripts);
	$colors = array('white', 'white', 'black', 'white');
	$bg = array('red', 'blue', 'yellow', 'magenta');
	$cc = count($colors);
	$bc = count($bg);

	$words = split(' ', $kw);
	// duplikaati nafig
	$words = array_unique($words);

	//$tokens = array();
	foreach($words as $index=>$word)
	{
		$word = preg_replace('/[<>]/', '', $word);
		$color = $colors[$index % $cc];
		$bgcolor = $bg[$index % $bc];
		$data = ">$data<";
		$patt = "/(>[^<]*)(".substitute(preg_quote($word)).")([^>]*)<?/imsUu";
		printr($data);
		printr($patt);
		//$patt = "/(>[^<]*)(".preg_quote($word).")([^>]*)<?";
		$data = preg_replace($patt, "$1<font style=\"background-color: $bgcolor\" color=\"$color\"><b>$2</b></font>$3", $data);
		$data = mb_substr($data, 1, mb_strlen($data)-2);
		//$data = mb_eregi_replace($patt, "\\0<font style=\"background-color: $bgcolor\" color=\"$color\"><b>\\1</b></font>\\2", mb_substr($data, 1, mb_strlen($data)-1), 'imb');
	}
	unstrip_script($data, $keys, $scripts);
} // mb_hl

//$data = file_get_contents('data.txt');

$data = 'Nu bāc Tabestic Enteron, es būšu pilnīgs gailis, ja neaiziešu uz to pastīties, kā Fauns vada savu apvienību, par kuru man pēdējā laikā radās pastiprināta interese. Es beidzot uzzināšu, kas ir tas piektais (Runcis Renārs). Tas nu tā. Faunam ir liela nozīme 21. gadsimta grindā, tādēļ derētu izrādīt vismaz minimālu cieņu un atvilkties uz turieni. Vot i mēs ar Tolx nospriedām, ka ir jāiet!!!!';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?
//call_user_func_array

//$stmt = $db->Prepare("SELECT art_data, art_entered FROM article_lv WHERE art_id=? AND art_visible=?");
//$params = array(455, 'Y');
//$db->BindParam($stmt, $params);
//$db->BindResult($stmt, $params);
//$db->BindParam($stmt, 455, 'Y');
//$db->BindResult($stmt, $params);

//preg_match_all('/Mārtiņš/', $data, $m);
//printr($m);
//mb_hl($data, 'Mārtiņš');
mb_hl($data, 'runcis renārs');
printr($data);
//print mb_hl($data, 'martins');
?>
</body>
</html>
