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
									<div class="yoomoney">
										<a href="<?=$SITEROOT?>?page=yoomoney"
											title="Donate using Yoomoney">Yoomoney</a>
									</div>
<?php /*
									<div class="bou">
										<a href="https://salt.bountysource.com/teams/lsp-plugins"
											target="_blank" title="Donate using Bountysource"
											rel="noopener">Bountysource</a>
									</div>
*/ ?>
<?php /*
									<div class="qiwi">
										<a href="<?=$SITEROOT?>?page=qiwi"
											title="Donate using QIWI">QIWI</a>
									</div>
*/ ?>
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
