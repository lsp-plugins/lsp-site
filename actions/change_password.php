<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once("./inc/site/auth.php");
require_once("./inc/site/notifications.php");
require_once("./inc/service/mail.php");
require_once("./lib/recaptcha/autoload.php");

function validate_restore_request() {
	$error = null;
	$error = verify_email($error, $_POST, 'email', 'Email');
	$error = verify_strong_password($error, $_POST, 'password', 'New password');
	$error = verify_isset($error, $_POST, 'password2', 'Password confirmation');
	if ((!isset($error)) && (strcmp($_POST['password'], $_POST['password2']) != 0)) {
		$error = "New password does not match it's confirmation";
	}
	$error = verify_csrf_token($error, 'restore', $_POST, 'token');
	$error = verify_token_id($error, $_POST, 'reset_token');
// 	$error = verify_captcha($error); // TODO
	
	return $error;
}

function process_change_password_request(&$user_email, &$password_reset_token) {
	$ip_addr = $_SERVER['REMOTE_ADDR'];
	
	$session_id = ensure_user_session_is_set();
	if (!isset($session_id)) {
		return 'HTTP session expired';
	}

	if (isset($_POST['email'])) {
		$user_email = $_POST['email'];
	}
	if (isset($_POST['reset_token'])) {
		$password_reset_token = $_POST['reset_token'];
	}
	error_log("email=$user_email, password_reset_token=$password_reset_token");
	$message = validate_restore_request();
	if (isset($message)) {
		return $message;
	}
	
	// Verify password reset token and it's match to entered e-mail
	$token = auth_get_password_reset_token($password_reset_token);
	if (!isset($token)) {
		return 'Invalid password reset token';
	}
	
	$user = auth_get_user($token['user_id']);
	if (!isset($user)) {
		return 'Malformed password reset token';
	}
	if (strcmp($_POST['email'], $user['email']) !== 0) {
		return 'Invalid email passed';
	}
	
	// Change password
	$user = auth_change_user_password($session_id, $ip_addr, $user['id'], $_POST['password']);
	if (!isset($user)) {
		return 'Failed password reset. Please try again';
	}
	
	// Bind user to session
	set_session_user($ip_addr, $user);

	return null;
}

$password_reset_token = "";
$user_email = "";
$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$message = process_change_password_request($user_email, $password_reset_token);
	if (!isset($message)) {
		// Redirect to download page
		header("Location: {$SITE_URL}/download");
		exit();
	}
}
?>

<!DOCTYPE html>

<?php require_once("./inc/header.php"); ?>
<?php require_once("./pages/auth/reset.php"); ?>
<?php require_once("./inc/footer.php"); ?>
