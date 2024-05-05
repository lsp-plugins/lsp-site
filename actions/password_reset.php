<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once("./inc/site/auth.php");

function validate_reset_request() {
	$error = null;
	$error = verify_token_id($error, $_REQUEST, 'token');
	
	return $error;
}

function process_reset_request(&$display_page, &$password_reset_token) {
	$error = validate_reset_request();
	if (isset($error)) {
		return $error;
	}
	
	if (!ensure_user_session_is_set()) {
		return 'HTTP session expired';
	}
	
	$token_id = $_REQUEST['token'];
	$token = auth_get_password_reset_token($token_id);
	if (!isset($token)) {
		return "Invalid password reset token";
	}
	
	$password_reset_token = $token_id;
	$display_page = "./pages/auth/reset.php";
	return null;
}

$user_email = "";
$display_page = "./pages/auth/signin.php";
$password_reset_token = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$message = process_reset_request($display_page, $password_reset_token);
}
?>

<!DOCTYPE html>

<?php require_once("./inc/header.php"); ?>
<?php require_once($display_page); ?>
<?php require_once("./inc/footer.php"); ?>
