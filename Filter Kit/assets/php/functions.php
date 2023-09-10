<?php

function redirect($url) {
	header("Location: " . $url);
	exit(0);
}


function refresh($time = 0) {
	header("Refresh: " . $time);
	exit(0);
}


function url_exists($url) {
	$url_headers = @get_headers($url);
	
	if(!$url_headers || $url_headers[0] == "HTTP/1.1 404 Not Found")
	{
		return false;
	}
	else
	{
		return true;
	}
}


function removeInvalidCharacters($input, $allowedChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_0123456789')
{
	$str = '';
	
	for ($i = 0; $i < strlen($input); $i++)
	{
		if (!stristr($allowedChars, $input[$i]))
		{
			continue;
		}
		
		$str .= $input[$i];
	}
	
	return $str;
}


function error($type) {
	global $lang, $lang_code;
	
	if ($type === 403) {
		$type = 404;
	}
	
	$errors = array(
		404 => array(
			"code" => 404,
			"error" => "ERROR 404",
			"description" => "Page not found."
		),
		0x6572726f7231 => array(
			"code" => "0x6572726f7231",
			"error" => "Error: 0x6572726f7231",
			"description" => "A major problem has occured! We are trying to fix it as soon as possible."
		)
	);

	$current_error = $errors[$type];
	
	if ($type === 404)
	{
		header("HTTP/1.1 404 Not Found");
	}
	
	include_once("./assets/html/errors.html");
	
	exit(0);
}


function str_random($length, $chars = "01234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {

	return substr(
		str_shuffle(
			str_repeat($chars, $length)
		), 0, $length
	);
}


/*function str_random($length) {
	$chars = "01234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

	return str_random($length, $chars);
}*/



function mime_type($filename) {
	$mime_types = array(
		// Classic Files
		'txt' => 'text/plain',
		'htm' => 'text/html',
		'html' => 'text/html',
		'css' => 'text/css',
		'json' => array('application/json', 'text/json'),
		'xml' => 'application/xml',
		'swf' => 'application/x-shockwave-flash',
		'flv' => 'video/x-flv',
		
		'hqx' => 'application/mac-binhex40',
		'cpt' => 'application/mac-compactpro',
		'csv' => array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'),
		'bin' => 'application/macbinary',
		'dms' => 'application/octet-stream',
		'lha' => 'application/octet-stream',
		'lzh' => 'application/octet-stream',
		'exe' => array('application/octet-stream', 'application/x-msdownload'),
		'class' => 'application/octet-stream',
		'so' => 'application/octet-stream',
		'sea' => 'application/octet-stream',
		'dll' => 'application/octet-stream',
		'oda' => 'application/oda',
		'ps' => 'application/postscript',
		'smi' => 'application/smil',
		'smil' => 'application/smil',
		'mif' => 'application/vnd.mif',
		'wbxml' => 'application/wbxml',
		'wmlc' => 'application/wmlc',
		'dcr' => 'application/x-director',
		'dir' => 'application/x-director',
		'dxr' => 'application/x-director',
		'dvi' => 'application/x-dvi',
		'gtar' => 'application/x-gtar',
		'gz' => 'application/x-gzip',
		'php' => 'application/x-httpd-php',
		'php4' => 'application/x-httpd-php',
		'php3' => 'application/x-httpd-php',
		'phtml' => 'application/x-httpd-php',
		'phps' => 'application/x-httpd-php-source',
		'js' => array('application/javascript', 'application/x-javascript'),
		'sit' => 'application/x-stuffit',
		'tar' => 'application/x-tar',
		'tgz' => array('application/x-tar', 'application/x-gzip-compressed'),
		'xhtml' => 'application/xhtml+xml',
		'xht' => 'application/xhtml+xml',             
		'bmp' => array('image/bmp', 'image/x-windows-bmp'),
		'gif' => 'image/gif',
		'jpeg' => array('image/jpeg', 'image/pjpeg'),
		'jpg' => array('image/jpeg', 'image/pjpeg'),
		'jpe' => array('image/jpeg', 'image/pjpeg'),
		'png' => array('image/png', 'image/x-png'),
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'shtml' => 'text/html',
		'text' => 'text/plain',
		'log' => array('text/plain', 'text/x-log'),
		'rtx' => 'text/richtext',
		'rtf' => 'text/rtf',
		'xsl' => 'text/xml',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'word' => array('application/msword', 'application/octet-stream'),
		'xl' => 'application/excel',
		'eml' => 'message/rfc822',
		
		// Images
		'png' => 'image/png',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'bmp' => 'image/bmp',
		'ico' => 'image/vnd.microsoft.icon',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		
		// Archives
		'zip' => array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
		'rar' => 'application/x-rar-compressed',
		'msi' => 'application/x-msdownload',
		'cab' => 'application/vnd.ms-cab-compressed',
		
		// Audio / Video
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'mpga' => 'audio/mpeg',
		'mp2' => 'audio/mpeg',
		'mp3' => array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
		'aif' => 'audio/x-aiff',
		'aiff' => 'audio/x-aiff',
		'aifc' => 'audio/x-aiff',
		'ram' => 'audio/x-pn-realaudio',
		'rm' => 'audio/x-pn-realaudio',
		'rpm' => 'audio/x-pn-realaudio-plugin',
		'ra' => 'audio/x-realaudio',
		'rv' => 'video/vnd.rn-realvideo',
		'wav' => array('audio/x-wav', 'audio/wave', 'audio/wav'),
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpe' => 'video/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',
		'avi' => 'video/x-msvideo',
		'movie' => 'video/x-sgi-movie',
		
		// Adobe
		'pdf' => 'application/pdf',
		'psd' => array('image/vnd.adobe.photoshop', 'application/x-photoshop'),
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',
		
		// Microsoft Office
		'doc' => 'application/msword',
		'rtf' => 'application/rtf',
		'xls' => array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
		'ppt' => array('application/powerpoint', 'application/vnd.ms-powerpoint'),
		
		// Open Office
		'odt' => 'application/vnd.oasis.opendocument.text',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
	);
	
	$ext = explode('.', $filename);
	$ext = strtolower(end($ext));
	
	if (array_key_exists($ext, $mime_types)) {
		return (is_array($mime_types[$ext])) ? $mime_types[$ext][0] : $mime_types[$ext];
	} else if (function_exists('finfo_open')) {
		if (file_exists($filename)) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
	}
	
	return 'application/octet-stream';
}


?>