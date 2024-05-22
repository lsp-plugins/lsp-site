<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once("./inc/site/auth.php");
require_once("./inc/site/notifications.php");
require_once("./inc/service/captcha.php");

function validate_auth_request() {
	$error = null;
	$error = verify_email($error, $_POST, 'email', 'Email');
	$error = verify_isset($error, $_POST, 'password', 'Password');
	$error = verify_csrf_token($error, 'auth', $_POST, 'token');
	$error = verify_captcha($error); // TODO

	return $error;
}

function validate_restore_request() {
	$error = null;
	$error = verify_email($error, $_POST, 'email', 'Email');
	$error = verify_csrf_token($error, 'auth', $_POST, 'token');
	$error = verify_captcha($error); // TODO

	return $error;
}

function process_auth_request(&$user_email, &$password_reset_token) {
	error_log('process_auth_request');

	$ip_addr = $_SERVER['REMOTE_ADDR'];

	$session = ensure_user_session_is_set();
	if (!isset($session)) {
		return 'HTTP session expired';
	}

	$user = get_session_user();
	if (isset($user)) {
		return "Already authenticated";
	}

	// Validate form data
	if (isset($_POST['email'])) {
		$user_email = $_POST['email'];
	}
	$error = validate_auth_request();
	if (isset($error)) {
		return $error;
	}

	// Authenticate user
	$user = auth_user($session, $ip_addr, $user_email, $_POST['password']);
	if (!isset($user)) {
		return 'Wrong email of password specified';
	}

	// Authorize user
	set_session_user($ip_addr, $user);

	return null;
}

function process_restore_request(&$user_email, &$password_reset_token) {
	error_log('process_restore_request');

	$ip_addr = $_SERVER['REMOTE_ADDR'];

	$session = ensure_user_session_is_set();
	if (!isset($session)) {
		return 'HTTP session expired';
	}

	// Validate form data
	if (isset($_POST['email'])) {
		$user_email = $_POST['email'];
	}
	$error = validate_restore_request();
	if (isset($error)) {
		return $error;
	}

	// Create password reset token
	$token = auth_create_password_reset_token($session, $ip_addr, $user_email);
	if (!isset($token)) {
		return 'Unknown error occurred when resetting password, please try again.';
	}

	// Send e-mail
	$result = notify_password_reset($user_email, $token['id']);
	return ($result) ?
		"Password reset mail has been sent" :
		"Error while requesting password reset";
}

function process_request(&$user_email, &$password_reset_token) {

	if (isset($_POST['auth'])) {
		return process_auth_request($user_email, $password_reset_token);
	} elseif (isset($_POST['restore'])) {
		return process_restore_request($user_email, $password_reset_token);
	}

	return 'Invalid sign-in mode';
}

$user_email = null;
$password_reset_token = "";
$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$message = process_request($user_email, $password_reset_token);
	if (!isset($message)) {
		header("Location: {$SITE_URL}/download");
		exit();
	}
}

?>

<!DOCTYPE html>

<?php require_once("./inc/header.php"); ?>
<?php require_once("./pages/auth/signin.php"); ?>
<?php require_once("./inc/footer.php"); ?>
