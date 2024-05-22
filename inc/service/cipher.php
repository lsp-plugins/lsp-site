<?php
function encrypt_text($text) {
	global $CIPHER;
	
	$key = $CIPHER['key'];
	$method = $CIPHER['method'];
	
	$iv_length = openssl_cipher_iv_length($CIPHER['method']);
	$iv = openssl_random_pseudo_bytes($iv_length);
	$encrypted = openssl_encrypt($text, $method, $key, OPENSSL_RAW_DATA, $iv);
	return base64_encode($iv . $encrypted);
}

function decrypt_text($text) {
	global $CIPHER;
	
	$key = $CIPHER['key'];
	$method = $CIPHER['method'];
	
	$iv_length = openssl_cipher_iv_length($CIPHER['method']);
	$encrypted = base64_decode($text);
	
	$iv = substr($encrypted, 0, $iv_length);
	$payload = substr($encrypted, $iv_length);
	
	return openssl_decrypt($payload, $method, $key, OPENSSL_RAW_DATA, $iv);
}

function encrypt_non_salted_text($text) {
	global $CIPHER;
	
	$iv = hex2bin($CIPHER['iv']);
	$key = $CIPHER['key'];
	$method = $CIPHER['method'];
	$encrypted = openssl_encrypt($text, $method, $key, OPENSSL_RAW_DATA, $iv);
	return base64_encode($encrypted);
}

function decrypt_non_salted_text($text) {
	global $CIPHER;
	
	$iv = hex2bin($CIPHER['iv']);
	$key = $CIPHER['key'];
	$method = $CIPHER['method'];
	$encrypted = base64_decode($text);
	return openssl_decrypt($encrypted, $method, $key, OPENSSL_RAW_DATA, $iv);
}
?>