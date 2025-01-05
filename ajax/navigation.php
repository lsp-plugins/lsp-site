<?php
chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./config/config.php");
require_once('./inc/service/utils.php');
require_once('./inc/site/session.php');

function process_request() {
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		http_response_code(401);
		return;
	}
	
	$session_id = user_session_id();
	if (!isset($session_id)) {
		http_response_code(401);
		return;
	}
	
	$json = file_get_contents('php://input');
	if (!isset($json)) {
		http_response_code(400);
		return;
	}
	
	error_log("Script input: " . var_export($json, true));
	
	$json = json_decode($json, true);
	if (!isset($json)) {
		http_response_code(400);
		return;
	}
	
	error_log("Decoded JSON: " . var_export($json, true));
	
	// Make a snapshot
	$allowed_keys = [
		'download.section',
		'download.architectures.windows',
	];
	
	$error = null;
	$batch = [];
	foreach ($allowed_keys as $key) {
		if (array_key_exists($key, $json)) {
			$value = $json[$key];
			if (strlen($value) < 32) {
				$batch[$key] = $json[$key];
			}
		}
	}
	
	if (count($batch) <= 0) {
		error_log("Batch is empty");
		http_response_code(200);
		return;
	}
	
	// Now we are ready to update the data
	error_log("Updating session using batch: " . var_export($batch, true));
	
	[$error, $affected] = update_session_context(function($context) use ($batch) {
		error_log("Old context: " . var_export($context, true));
		
		foreach ($batch as $key => $value) {
			utl_set_value($context, "pages." . $key, $value);
		}
		error_log("New context: " . var_export($context, true));
		
		return $context;
	});
	
	if (isset($error)) {
		error_log("Error updating session context: {$error}");
	}
}

process_request();
