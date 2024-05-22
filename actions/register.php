<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once("./inc/site/auth.php");
require_once("./inc/site/notifications.php");
require_once("./lib/recaptcha/autoload.php");

function validate_register_request() {
	$error = null;
	$error = verify_email($error, $_POST, 'email', 'Email');
	$error = verify_strong_password($error, $_POST, 'password', 'New password');
	$error = verify_isset($error, $_POST, 'password2', 'Password confirmation');
	if ((!isset($error)) && (strcmp($_POST['password'], $_POST['password2']) != 0)) {
		$error = "New password does not match it's confirmation";
	}
	$error = verify_csrf_token($error, 'register', $_POST, 'token');
	// 	$error = verify_captcha($error); // TODO
	
	return $error;
}

function process_register_request(&$user_email) {
	$ip_addr = $_SERVER['REMOTE_ADDR'];
	
	// Verify session state
	$session_id = ensure_user_session_is_set();
	if (!isset($session_id)) {
		return 'HTTP session expired';
	}
		
	$user = get_session_user();
	if (isset($user)) {
		return "Already authenticated";
	}
	
	// Verify request
	if (isset($_POST['email'])) {
		$user_email = $_POST['email'];
	}
	$message = validate_register_request();
	if (isset($message)) {
		return $message;
	}
	
	// Create user
 	$user = create_user($session_id, $ip_addr, $_POST['email'], $_POST['password'], 'regular');
 	if (!isset($user)) {
 		return "User with such e-mail has already been registered";
 	}
 					
 	// Authorize user to session
 	set_session_user($ip_addr, $user);
 					
 	// Create password reset token
 	$token = auth_create_email_verification_token($session_id, $ip_addr, $user['id']);
 	if (isset($token)) {
 		notify_email_verification($user_email, $token['id']);
 	}
 					
 	return null;
}

$user_email = "";
$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$message = process_register_request($user_email);
	if (!isset($message)) {
		// Redirect to download page
		header("Location: {$SITE_URL}/download");
		exit();
	}
}

?>

<!DOCTYPE html>

<?php require_once("./inc/header.php"); ?>
<?php require_once("./pages/auth/signin.php"); ?>
<?php require_once("./inc/footer.php"); ?>
