<?php
function encrypt_text($text) {
	global $CIPHER;
	
	$iv = hex2bin($CIPHER['iv']);
	$key = $CIPHER['key'];
	$method = $CIPHER['method'];
	$encrypted = openssl_encrypt($text, $method, $key, OPENSSL_RAW_DATA, $iv);
	return base64_encode($encrypted);
}

function decrypt_text($text) {
	global $CIPHER;
	
	$iv = hex2bin($CIPHER['iv']);
	$key = $CIPHER['key'];
	$method = $CIPHER['method'];
	$encrypted = base64_decode($text);
	return openssl_decrypt($encrypted, $method, $key, OPENSSL_RAW_DATA, $iv);
}
?>