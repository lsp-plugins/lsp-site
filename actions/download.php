<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once("./inc/service/validation.php");
require_once("./inc/site/artifacts.php");
require_once("./inc/site/download.php");

function validate_download_request() {
	$error = null;
	$error = verify_token_id($error, $_GET, 'id');
	
	return $error;
}

function process_download_request()
{
	global $FILE_STORAGE;
	
	// Verify request
	$error = validate_download_request();
	if (isset($error)) {
		http_response_code(401);
		return;
	}
	
	// Get artifact identifier
	$download_id = $_GET['id'];
	$artifact_id = parse_download_id($download_id);
	if (!isset($artifact_id)) {
		error_log("Could not parse download_id={$download_id}");
		http_response_code(401);
		return;
	}
	
	error_log("Requested artifact id={$artifact_id}");
	
	// Fetch artifact
	[$error, $artifact] = find_artifact($artifact_id);
	if (isset($error)) {
		error_log("Could not find artifact id={$artifact_id}");
		http_response_code(404);
		return;
	}
	
	$product = $artifact['product'];
	$version = implode('.', $artifact['version']);
	$file = $artifact['file'];
	$file_location = "$FILE_STORAGE/$product/$version/$file";
	
	if (!file_exists($file_location)) {
		error_log("Could not find matching file '{$file_location}' for artifact id={$artifact_id}");
		http_response_code(404);
		return;
	}
	
	$content_type = get_download_content_type($file);
	
	http_response_code(200);
// 	header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
	header("Cache-Control: public");
	header("Content-Type: $content_type");
	header("Content-Transfer-Encoding: Binary");
	header("Content-Length: " . filesize($file_location));
	header("Content-Disposition: attachment; filename=" . rawurlencode($file));
	
	readfile($file_location);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	process_download_request();
} else {
	http_response_code(401);
}


?>