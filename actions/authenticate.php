<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once("./inc/site/auth.php");
require_once("./inc/site/notifications.php");
require_once("./lib/recaptcha/autoload.php");

function process_auth_request(&$user_email, &$password_reset_token) {
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
	
	if (!$resp->isSuccess()) {
		return "Sorry, you have not passed captcha. Please try again. reCAPTCHA said: " . implode(',', $resp->getErrorCodes());
	}
	*/
		
	if (isset($_POST['auth'])) {
		$password = $_POST['password'];
		if (!isset($password))
			return 'Password not specified';
		
		$user = auth_user($session, $ip_addr, $email, $password);
		if (!isset($user))
			return 'Wrong email of password specified';
			
		set_session_user($ip_addr, $user);
			
		return null;	
	} elseif (isset($_POST['restore'])) {
		// Create password reset token
		$token = auth_create_password_reset_token($session, $ip_addr, $email);
		if (!isset($token)) {
			return 'Unknown error occurred when resetting password, please try again.';
		}
		
		// Send e-mail
		$result = notify_password_reset($email, $token['id']);

		return ($result) ?
			"Password reset mail has been sent" :
			"Error while requesting password reset";
	}
	
	return 'Invalid sign-in mode';
}

$user_email = null;
$password_reset_token = "";
$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$message = process_auth_request($user_email, $password_reset_token);
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
