<?php
require_once("./inc/top.php");
require_once("./inc/site/auth.php");
require_once("./lib/recaptcha/autoload.php");
require("./vendor/autoload.php");

function process_auth_request() {
	global $GOOGLE;
	global $SITEROOT;

	if (!ensure_user_session_is_set())
		return 'HTTP session expired';
	
	$user = get_session_user();
	if (isset($user))
		return "Already authenticated";
	
	$token = $_POST['token'];
	if (!apply_csrf_token('auth', $token))
		return 'Outdated request';
	
	$email = $_POST['email'];
	if (!isset($email))
		return 'Email not specified';
	
	// Verify recaptcha
	/* TODO: return this when ready
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
		
		$user = auth_user($email, $password);
		if (!isset($user))
			return 'Wrong email of password specified';
			
		// Redirect to download page
		set_session_user($user);
		header("Location: {$SITEROOT}/?page=download");
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
			
		$user = create_user($email, $password, 'regular');
		if (!isset($user))
			return "Trying to register already registered user";
				
		// Redirect to registered page
		set_session_user($user);
		header("Location: {$SITEROOT}/?page=download");
		exit();
	} elseif (isset($_POST['restore'])) {
		// Redirect to restore page
		header("Location: {$SITEROOT}/restore");
		exit();
	}
	
	return 'Invalid authentication mode';
}

$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$message = process_auth_request();
}
?>

<!DOCTYPE html>

<?php require_once("./inc/header.php"); ?>
<?php require_once("./pages/signin.php"); ?>
<?php require_once("./inc/footer.php"); ?>
