<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once("./inc/site/auth.php");
require_once("./inc/site/notifications.php");
require_once("./inc/service/mail.php");
require_once("./lib/recaptcha/autoload.php");

function process_change_password_request(&$user_email, &$password_reset_token) {
	global $GOOGLE;
	
	$ip_addr = $_SERVER['REMOTE_ADDR'];

	$session = ensure_user_session_is_set();
	if (!isset($session))
		return 'HTTP session expired';
	
	$user = get_session_user();
	if (isset($user))
		return "Already authenticated";
	
	$token = $_POST['token'];
	if (!apply_csrf_token('auth', $token)) {
		return 'Outdated request';
	}
	
	$email = $_POST['email'];
	if (!isset($email))
		return 'Email not specified';
	
	$user_email = $email;
	
	// Verify recaptcha
	/* TODO: uncomment this when ready
	$recaptcha = new \ReCaptcha\ReCaptcha($GOOGLE['recaptcha_sec']);
	$resp = $recaptcha->setExpectedHostname($GOOGLE['recaptcha_host'])
		->verify($_REQUEST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
	
	if (!$resp->isSuccess())
		return "Sorry, you have not passed captcha. Please try again. reCAPTCHA said: " . implode(',', $resp->getErrorCodes());
	*/
		
	$token = $_REQUEST['reset_token'];
	$password_reset_token = $token;
	
	$password = $_POST['password'];
	if (!isset($password)) {
		return 'Password not specified';
	}
	
	$password2 = $_POST['password2'];
	if (!isset($password2)) {
		return 'Password confirmation does not match to the original password';
	}
	
	if (strcmp($password, $password2) !== 0) {
		return 'Password and password confirmation do not match';
	}
	
	// Verify password reset token and it's match to entered e-mail
	if (!isset($token)) {
		return 'Invalid password reset token';
	}
	$token = auth_get_password_reset_token($token);
	if (!isset($token)) {
		return 'Invalid password reset token';
	}
	
	$user = auth_get_user($token['user_id']);
	if (!isset($user)) {
		return 'Invalid password reset token';
	}
	if (strcmp($email, $user['email']) !== 0) {
		return 'Invalid email';
	}
	
	// Change password
	$user = auth_change_user_password($session, $ip_addr, $user['id'], $password);
	if (!isset($user)) {
		return 'Failed password reset. Please try again';
	}
	
	// Bind user to session
	set_session_user($ip_addr, $user);

	return null;
}

$user_email = null;
$password_reset_token = "";
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
