<script>
	// Uncomment all for mac
	$(document).ready(function() {
		$('.dwnld-lin').on({
			click: function() {
				$(this).addClass("dwnld-active");
				$('.dwnld-win').removeClass("dwnld-active");
				// $('.dwnld-mac').removeClass("dwnld-active");
				$('.dwnld-bsd').removeClass("dwnld-active");
				$('.dwnld-src').removeClass("dwnld-active");
				$(this).removeClass("dwnld-hover");
				$('.dwnld-block-lin').slideDown(300);
				$('.dwnld-block-win').slideUp(300);
				// $('.dwnld-block-mac').slideUp(300);
				$('.dwnld-block-bsd').slideUp(300);
				$('.dwnld-block-src').slideUp(300);
			},
			mouseenter: function() {
				if (!$(this).hasClass("dwnld-active")) {
					$(this).addClass("dwnld-hover");
				}
			},
			mouseleave: function() {
				$(this).removeClass("dwnld-hover");
			}
		});

		$('.dwnld-win').on({
			click: function() {
				$(this).addClass("dwnld-active");
				$('.dwnld-lin').removeClass("dwnld-active");
				// $('.dwnld-mac').removeClass("dwnld-active");
				$('.dwnld-bsd').removeClass("dwnld-active");
				$('.dwnld-src').removeClass("dwnld-active");
				$(this).removeClass("dwnld-hover");
				$('.dwnld-block-win').slideDown(300);
				$('.dwnld-block-lin').slideUp(300);
				// $('.dwnld-block-mac').slideUp(300);
				$('.dwnld-block-bsd').slideUp(300);
				$('.dwnld-block-src').slideUp(300);
			},
			mouseenter: function() {
				if (!$(this).hasClass("dwnld-active")) {
					$(this).addClass("dwnld-hover");
				}
			},
			mouseleave: function() {
				$(this).removeClass("dwnld-hover");
			}
		});

		// $('.dwnld-mac').on({
		// 	click: function() {
		// 		$(this).addClass("dwnld-active");
		// 		$('.dwnld-lin').removeClass("dwnld-active");
		// 		$('.dwnld-win').removeClass("dwnld-active");
		// 		$('.dwnld-bsd').removeClass("dwnld-active");
		// 		$('.dwnld-src').removeClass("dwnld-active");
		// 		$(this).removeClass("dwnld-hover");
		// 		$('.dwnld-block-mac').slideDown(300);
		// 		$('.dwnld-block-lin').slideUp(300);
		// 		$('.dwnld-block-win').slideUp(300);
		// 		$('.dwnld-block-bsd').slideUp(300);
		// 		$('.dwnld-block-src').slideUp(300);
		// 	},
		// 	mouseenter: function() {
		// 		if (!$(this).hasClass("dwnld-active")) {
		// 			$(this).addClass("dwnld-hover");
		// 		}
		// 	},
		// 	mouseleave: function() {
		// 		$(this).removeClass("dwnld-hover");
		// 	}
		// });

		$('.dwnld-bsd').on({
			click: function() {
				$(this).addClass("dwnld-active");
				$('.dwnld-lin').removeClass("dwnld-active");
				// $('.dwnld-mac').removeClass("dwnld-active");
				$('.dwnld-win').removeClass("dwnld-active");
				$('.dwnld-src').removeClass("dwnld-active");
				$(this).removeClass("dwnld-hover");
				$('.dwnld-block-bsd').slideDown(300);
				$('.dwnld-block-lin').slideUp(300);
				// $('.dwnld-block-mac').slideUp(300);
				$('.dwnld-block-win').slideUp(300);
				$('.dwnld-block-src').slideUp(300);
			},
			mouseenter: function() {
				if (!$(this).hasClass("dwnld-active")) {
					$(this).addClass("dwnld-hover");
				}
			},
			mouseleave: function() {
				$(this).removeClass("dwnld-hover");
			}
		});

		$('.dwnld-src').on({
			click: function() {
				$(this).addClass("dwnld-active");
				$('.dwnld-win').removeClass("dwnld-active");
				// $('.dwnld-mac').removeClass("dwnld-active");
				$('.dwnld-bsd').removeClass("dwnld-active");
				$('.dwnld-lin').removeClass("dwnld-active");
				$(this).removeClass("dwnld-hover");
				$('.dwnld-block-src').slideDown(300);
				$('.dwnld-block-win').slideUp(300);
				// $('.dwnld-block-mac').slideUp(300);
				$('.dwnld-block-bsd').slideUp(300);
				$('.dwnld-block-lin').slideUp(300);
			},
			mouseenter: function() {
				if (!$(this).hasClass("dwnld-active")) {
					$(this).addClass("dwnld-hover");
				}
			},
			mouseleave: function() {
				$(this).removeClass("dwnld-hover");
			}
		});
	});
</script>


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

<div class="tile-flex-container">

	<div class="tile-flex" style="margin-bottom:10px;">

		<div class="tile-flex-inner dwnld-lin dwnld-active">
			<div class="dwnld-content">
				<div class="dwnld-os">Linux</div>
				<div class="dwnld-desc">Open-source, community-developed operating system</div>
			</div>
		</div>
		<div class="tile-flex-inner dwnld-win">
			<div class="dwnld-content">
				<div class="dwnld-os">Windows</div>
				<div class="dwnld-desc">Is a group of several proprietary graphical operating system</div>
			</div>
		</div>
		<!-- <div class="tile-flex-inner dwnld-mac">
			<div class="dwnld-content">
				<div class="dwnld-os">MAC OS</div>
				<div class="dwnld-desc">UNIX-based operating system by Apple Inc.</div>
			</div>
		</div> -->
		<div class="tile-flex-inner dwnld-bsd">
			<div class="dwnld-content">
				<div class="dwnld-os">Free BSD</div>
				<div class="dwnld-desc">Is a free and open-source Unix-like operating system</div>
			</div>
		</div>

		<div class="tile-flex-inner dwnld-src">
			<div class="dwnld-content">
				<div class="dwnld-os">Source</div>
				<div class="dwnld-desc">Is a programmer’s instructions - written in a computer programming language</div>
			</div>
		</div>

	</div>


	<div class="dwnld-block-lin" style="display: block;">
		<?php require_once("./pages/download/linux.php"); ?>
	</div>
	<div class="dwnld-block-win" style="display: none;">
		<?php require_once("./pages/download/windows.php"); ?>
	</div>
	<!-- <div class="dwnld-block-mac" style="display: none;">
		<?php require_once("./pages/download/mac.php"); ?>
	</div> -->
	<div class="dwnld-block-bsd" style="display: none;">
		<?php require_once("./pages/download/bsd.php"); ?>
	</div>
	<div class="dwnld-block-src" style="display: none;">
		<?php require_once("./pages/download/source.php"); ?>
	</div>
</div>
