<?php

	ob_start(); 
	ob_implicit_flush(0); 

	function check_can_gzip()
	{
		$str_accept_encoding = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';

		if(headers_sent() || connection_aborted())
			return false;

		if(strpos($str_accept_encoding, 'x-gzip') !== false)
			return "x-gzip";

		if(strpos($str_accept_encoding, 'gzip') !== false)
			return "gzip";

		return 0; 
	} // check_can_gzip


	/* $level = compression level 0-9, 0=none, 9=max */
	function gz_doc_out($int_level = 4)
	{
		global $sys_encoding;

		$str_encoding = check_can_gzip();

		if($str_encoding) {
			header('Content-Type: text/html; charset='.$sys_encoding);

			$str_contents = ob_get_contents();
			ob_end_clean(); 

			header("Content-Encoding: $str_encoding");
			print "\x1f\x8b\x08\x00\x00\x00\x00\x00";

			$int_size = strlen($str_contents);

			$str_crc = crc32($str_contents);

			$str_contents = gzcompress($str_contents, $int_level);
			$str_contents = substr($str_contents, 0, strlen($str_contents) - 4);

			print $str_contents;

			print pack('V', $str_crc);
			print pack('V', $int_size);

			return true;
		} else {
			ob_end_flush();
			return false;
		}
	} // gz_doc_out

?>
