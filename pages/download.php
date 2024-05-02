<?php
require_once('./inc/files.php');
require_once('./inc/site/auth.php');
require_once('./inc/site/database.php');
require_once('./inc/site/session.php');
?>

<h1>DOWNLOAD</h1>

<?

$user = get_session_user();
if ((isset($user)) && (!isset($user['verified']))) {
	$user_id = $user['id'];
	$token = auth_find_email_verification_token($user_id);
	$resend_period = 0;
	if (isset($token)) {
		$token_created = $token['created'];
		$threshold_time = db_current_timestamp('-20 minutes');
		$resend_period = db_strtotime($token_created) - db_strtotime($threshold_time);
	}
	
	if ($resend_period < 0)
		$resend_period = 0;
?>

<div>
<form id="email_form" action="<?=$SITEROOT?>/actions/verify_email.php" method="POST">
<p>For full functionality we've sent you verification link on your e-mail. Please follow this link. If you didn't receive the e-mail,
you can request to send it again.</p>

<input id="email_button" type="submit" value="Send e-mail" name="verification" <?= ($resend_period > 0) ? 'disabled' : '' ?>>

<div id="email_countdown_message" style="display: none;">
	Re-send available after: <span id="email_countdown"></p>
</div>

<script type="text/javascript">
	var countdown = '<?= $resend_period ?>';
	
	if (countdown > 0) {
		var email_timer = setInterval(
			function() {
				var button = $("#email_button");
				var counter_message = $("#email_countdown_message");
				var counter = $('#email_countdown');
				if (countdown <= 0) {
					clearInterval(email_timer);
					button.removeAttr('disabled');
					counter_message.hide();
					return;
				}
	
				button.attr('disabled', 'true');
				minutes = Math.floor(countdown / 60) + "";
				seconds = (countdown % 60) + "";
				if (minutes.length < 2) {
					minutes = '0' + minutes;
				}
				if (seconds.length < 2) {
					seconds = '0' + seconds;
				}

				counter_message.show();
				counter.text(minutes + ":" + seconds);
				--countdown;
			},
			1000);
	}
</script>

</form>
</div>

<?
}

?>

<p>The LSP project is an open-source project and cares about quality of developed software.</p>
<p>Still there is no absolute warranty about stability of the software on different platforms, so you're using this software on your own risk.</p>
<p>Unless many commercial or proprietary projects, LSP project does not sell license keys or offer technical support for enterprise solutions.</p>

<h2>Binary distribution</h2>

<p>You may download the latest release from <a href="<?php echo "${FILE_SHARE}${PACKAGE['version']}"; ?>/">SourceForge.net</a></p>

<p><a href="https://sourceforge.net/projects/lsp-plugins/files/lsp-plugins/<?php echo "${PACKAGE['version']}" ?>/" rel="nofollow"><img alt="Download LSP Plugins" src="https://a.fsdn.com/con/app/sf-download-button"></a></p>

<p>You also may view all previous releases <a href="<?php echo $FILE_SHARE; ?>">here</a>.</p>

<h2>Source code</h2>

<p>Source code is accessible from <a href="<?php echo $CODE_REPO; ?>">GIT repository at GitHub.com</a>.</p>

<p>
    You may stimulate development of plugins by subscribing or donating the project.
</p>
<p>
    Because project needs regular support, small bounty subscription is much more preferred rather than huge but one-time donation.
</p>

<h2>Building</h2>
<?php file_content('README.md', 'building'); ?>

<h2>System requirements</h2>

<?php require('./pages/manuals/requirements.php'); ?>

<?php
 // <iframe src="https://streamtip.com/embed/youtube/< ?php echo $STREAMTIP['username']; ? >?theme=dark" width="400" height="200" style="border:none;"></iframe>
?>
