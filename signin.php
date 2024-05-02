<?php

chdir($_SERVER['DOCUMENT_ROOT']);

require_once("./inc/top.php");
require_once("./inc/site/auth.php");
require_once("./inc/service/mail.php");
require_once("./lib/recaptcha/autoload.php");

function process_auth_request(&$user_email, &$password_reset_token) {
	global $GOOGLE;
	global $SITE_URL, $MAIL_ADDR;
	
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
		
	if (isset($_POST['auth'])) {
		$password = $_POST['password'];
		if (!isset($password))
			return 'Password not specified';
		
		$user = auth_user($session, $ip_addr, $email, $password);
		if (!isset($user))
			return 'Wrong email of password specified';
			
		// Redirect to download page
		set_session_user($ip_addr, $user);
		header("Location: {$SITE_URL}/?page=download");
		exit();
	
	} elseif (isset($_POST['register'])) {
		$password = $_POST['password'];
		if (!isset($password))
			return 'Password not specified';
		
		$password2 = $_POST['password2'];
		if (!isset($password2))
			return 'Password confirmation does not match to the original password';
		
		if (strcmp($password, $password2) !== 0)
			return 'Password and password confirmation do not match';
			
		$user = create_user($session, $ip_addr, $email, $password, 'regular');
		if (!isset($user))
			return "Trying to register already registered user";

		// Bind user to session
		set_session_user($ip_addr, $user);
		
		// Create password reset token
		$token = auth_create_email_verification_token($session, $ip_addr, $user['id']);
		if (isset($token)) {
			send_mail(
				[ $MAIL_ADDR['noreply'] => 'Email verification service' ],
				[ $email => 'LSP Customer' ],
				'LSP Plugins: email verification',
				'email_verification', 
				[
					'site_url' => "{$SITE_URL}/",
					'recovery_url' => "{$SITE_URL}/actions/verify_email?token={$token['id']}"
				]);
		}
			
		// Redirect to download page
		header("Location: {$SITE_URL}/?page=download");
		exit();
	} elseif (isset($_POST['restore'])) {
		// Create password reset token
		$token = auth_create_password_reset_token($session, $ip_addr, $email);
		if (!isset($token)) {
			return 'Unknown error occurred when resetting password, please try again.';
		}
		
		// Send e-mail
		$result = send_mail(
			[ $MAIL_ADDR['noreply'] => 'Password reset service' ],
			[ $email => 'LSP Customer' ],
			'LSP Plugins: password reset',
			'password_reset', 
			[
				'site_url' => "{$SITE_URL}/",
				'recovery_url' => "{$SITE_URL}/signin?token={$token['id']}"
			]);

		return ($result) ?
			"Password reset mail has been sent" :
			"Error while requesting password reset";
	} elseif (isset($_POST['update'])) {
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
		
		// Redirect to download page
		header("Location: {$SITE_URL}/?page=download");
		exit();
	}
	
	return 'Invalid sign-in mode';
}

function process_reset_request(&$display_page, &$password_reset_token) {
	if (!isset($_REQUEST['token']))
		return null;
	$token_id = $_REQUEST['token'];
	
	if (!ensure_user_session_is_set())
		return 'HTTP session expired';
	
	$token = auth_get_password_reset_token($token_id);
	if (!isset($token)) {
		return "Invalid password reset token";
	}
	
	$password_reset_token = $token_id;
	$display_page = "./pages/reset.php";
	return null;
}

$display_page = "./pages/signin.php";
$user_email = null;
$password_reset_token = "";
$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$message = process_auth_request($user_email, $password_reset_token);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$message = process_reset_request($display_page, $password_reset_token);
}
?>

<!DOCTYPE html>

<?php require_once("./inc/header.php"); ?>
<?php require_once($display_page); ?>
<?php require_once("./inc/footer.php"); ?>
