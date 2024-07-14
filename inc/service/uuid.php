<?php
function make_uuid()
{
	$data = PHP_MAJOR_VERSION < 7 ? openssl_random_pseudo_bytes(16) : random_bytes(16);
	$data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // Set version to 0100
	$data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // Set bits 6-7 to 10
	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function encode_uuid($uuid, $cipher)
{
	$data = hex2bin(str_replace('-', '', $cipher));
	$data[6] = chr(ord($data[6]) & 0x0f);    	// Reset version to 0000
	$data[8] = chr(ord($data[8]) & 0x3f);    	// Reset bits 6-7 to 0
	
	$uuid_bin = hex2bin(str_replace('-', '', $uuid));
	for ($i=0; $i<16; ++$i) {
		$data[$i] = chr(ord($data[$i]) ^ ord($uuid_bin[$i]));
	}
	
	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function decode_uuid($code, $cipher)
{
	return encode_uuid($code, $cipher);
}

?>