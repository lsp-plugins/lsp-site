<!DOCTYPE html>

<?php
	require_once("./config/config.php");
	require_once("./config/plugins.php");

	require_once("./lib/recaptcha/autoload.php");

	// Determine current page
	$curr_page='';
	if (array_key_exists('page', $_REQUEST))
		$curr_page = $_REQUEST['page'];

	if ((!$curr_page) || (!array_key_exists($curr_page, $PAGES))) {
		reset($PAGES);
		$curr_page = key($PAGES);
	}
?>

<html lang="en-gb" dir="ltr" vocab="http://schema.org/">
	<head>
		<title>Linux Studio Plugins Project</title>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="<?=$SITEROOT?>/css/main-style.css" type="text/css">
		<link rel="stylesheet" href="<?=$SITEROOT?>/css/jquery-fancybox.css" type="text/css" media="screen">
		<link rel="shortcut icon" href="<?=$SITEROOT?>/img/LSP_favicon.png"/>
		<script type="text/javascript" src="<?=$SITEROOT?>/js/jquery-3.5.1.min.js" ></script>
		<script type="text/javascript" src="<?=$SITEROOT?>/js/jquery.fancybox-3.5.7.min.js"></script>
		<script type="text/javascript" src="<?=$SITEROOT?>/js/jquery.mob-menu.js"></script>
		<script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async defer></script>
	</head>
	<body>
	<div class="lsp-content smooth">
		<!-- Header -->
		<div id="header">
			<div class="header-box">
				<div style="display: none;" id="hidden-content">
					<div class="sup-container">
						<div class="donate-box">
							<div class="sup-project">
								<div class="sup-project-text">Linux Studio Plugins is a project
									powered by a very small team, but an ever growing community,
									and depends on people volunteering time, code, money, and
									energy to keep it going. This page will help you if you are
									interested in supporting the project in any way.</div>
								<div class="sup-project-text">The best way to support the
									project :</div>
								<br>
								<div class="sup-container">
									<div class="lpa">
										<a href="https://liberapay.com/sadko4u/donate" target="_blank"
											title="Donate using Liberapay" rel="noopener">Liberapay</a>
									</div>
									<div class="pay">
										<a href="https://paypal.me/sadko4u" target="_blank"
											title="Donate with PayPal" rel="noopener">PayPal</a>
									</div>
									<div class="ptr">
										<a
											href="https://www.patreon.com/sadko4u"
											target="_blank" title="Patreon"
											rel="noopener">Patreon</a>
									</div>
									<div class="eth">
										<a
											href="https://etherscan.io/address/0x079b24da78d78302cd3cfbb80c728cd554606cc6"
											target="_blank" title="Donate with Ethereum Wallet"
											rel="noopener">Ethereum</a>
									</div>
									<div class="bou">
										<a href="https://salt.bountysource.com/teams/lsp-plugins"
											target="_blank" title="Donate using Bountysource"
											rel="noopener">Bountysource</a>
									</div>
									<div class="qiwi">
										<a href="<?=$SITEROOT?>?page=qiwi"
											target="_blank" title="Donate using QIWI"
											rel="noopener">QIWI</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<a data-fancybox data-src="#hidden-content" href="javascript:;">
					<div class="donate-button"></div>
				</a>
			</div>
		</div>
		<!-- Menu -->
		<div id="menu" class="smooth">
			<?php require("./pages/menu.php") ?>
		</div>
		<!-- Main content -->
		<div id="main" class="main smooth">
			<?php require("./pages/{$PAGES[$curr_page]['page']}"); ?>
		</div>
		<!-- Footer -->
		<div id="footer">
			<a href="/index.php"><div class="footer-logo-wrapper">
				<div class="footer-logo"></div>
			</div></a>
			<div class="footer-text">
				<p>The project is developed and maintained by LSP Project Team with the main founder and developer at the head - Vladimir Sadovnikov.
					If you're ready to join, contribute to the project or contact to the developer - please use the <a href="<?=$SITEROOT?>/?page=feedback">feedback form</a> and do not forget to leave your e-mail address.
				</p>
			</div>
			<div class="footer-menu">
				MENU
				<ul class="menu-ul-footer">
					<?php
						foreach ($PAGES as $key => $value)
						{
							if ((array_key_exists('hidden', $value)) && ($value['hidden']))
								continue;

							$sclass = isset($value['style']) ? $value['style'] : "";
							if ($key == $curr_page)
								echo "<li class=\"sel menu-li-footer \">";
							else
								echo "<li class=\"menu-li-footer \">";

							if (($key != $curr_page) || ((isset($value['reentrant'])) && ($value['reentrant'])))
								echo "<a href=\"${SITEROOT}?page={$key}\">" . htmlspecialchars($value['name']) . "</a>";
							else
								echo htmlspecialchars($value['name']);

							echo "</li>\n";
						}
					?>
				</ul>
			</div>
			<div class="footer-links">
				<div>LINKS</div>
				<ul>
					<li><a href="https://github.com/sadko4u/" target="_blank">GitHub</a></li>
					<li><a href="https://linuxmusicians.com/" target="_blank">LinuxMusicians</a></li>
					<li><a href="https://lv2plug.in/" target="_blank">LV2</a></li>
					<li><a href="https://www.kvraudio.com/plugins/lsp" target="_blank">KVR Audio</a></li>
					<li><a href="https://ardour.org/" target="_blank">Ardour DAW</a></li>
					<li><a href="https://unfa.bandcamp.com/" target="_blank">unfa</a></li>
					<li><a href="https://steveerdeman.wixsite.com/home" target="_blank">STEVE ERDEMAN</a></li>
				</ul>
			</div>

			<div class="footer-copy">
				<a data-fancybox data-src="#hidden-content" href="javascript:;">
					<div class="donate-button-footer"></div>
				</a>
				<p>&copy; Linux Studio Plugins, 2015-2022</p>
				<p>All rights reserved</p>
				<?php require_once('./lib/google-gtag.php') ?>
				<a
					href="https://sourceforge.net/projects/lsp-plugins/files/latest/download"
					rel="nofollow"> <img alt="Download Linux Studio Plugins Project"
					src="https://img.shields.io/sourceforge/dm/lsp-plugins.svg">
				</a>
			</div>

		</div>
		<div class="img-load">
			<img src="/svg/lpa_gray.png">
			<img src="/svg/bou_gray.png">
			<img src="/svg/pay_gray.png">
			<img src="/svg/eth_gray.png">
			<img src="/svg/ptr_gray.png">
			<img src="/svg/donate_button_gray.svg">
			<img src="/img/LSP_logo_hover.png">
			<img src="/img/home_hover.png">
			<img src="/img/about_hover.png">
			<img src="/img/manuals_hover.png">
			<img src="/img/plugins_hover.png">
			<img src="/img/video_hover.png">
			<img src="/img/download_hover.png">
			<img src="/img/news_hover.png">
			<img src="/img/feedback_hover.png">
			<img src="/img/LSP_logo_footer_hover.png">
			<img src="/img/LSP_logo_mob_hover.png">
			<img src="/img/LSP_logo_2020_hover.png">
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".fancybox").fancybox();
		});
	</script>
	</body>
</html>
