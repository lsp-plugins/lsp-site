<?php
require_once('./inc/files.php');
require_once('./inc/service/database.php');
require_once('./inc/site/auth.php');
require_once('./inc/site/session.php');
?>

<h1>DOWNLOAD</h1>

<?php
require_once('./pages/parts/user_verification_status.php');
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
