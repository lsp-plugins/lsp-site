<script>
	var pages = {
		"bsd": {
			"div": ".dwnld-bsd",
			"block": ".dwnld-block-bsd"
		},
		"lin": {
			"div": ".dwnld-lin",
			"block": ".dwnld-block-lin"
		},
		<?php /*
		"mac": {
			"div": ".dwnld-mac",
			"block": ".dwnld-block-mac"
		}, */ ?>
		"src": {
			"div": ".dwnld-src",
			"block": ".dwnld-block-src"
		},
		<?php /*
		"win": {
			"div": ".dwnld-win",
			"block": ".dwnld-block-win"
		}, */ ?>
	};

	$(document).ready(function() {
		for (var key in pages) {
			const id = key;

			$(pages[id]["div"]).on({
				click: function() {
					for (var key in pages) {
						var page = pages[key]; 
						var div = page["div"];
						var block = page["block"];
						if (key === id) {
							$(div).addClass("dwnld-active");
							$(div).removeClass("dwnld-hover");
							$(block).slideDown(300);
						} else {
							$(div).removeClass("dwnld-active");
							$(block).slideUp(300);
						}
					}
				},
				mouseenter: function() {
					var div = pages[id]["div"];
					var obj = $(div);
					if (!obj.hasClass("dwnld-active")) {
						obj.addClass("dwnld-hover");
					}
				},
				mouseleave: function() {
					var div = pages[id]["div"];
					var obj = $(div);
					obj.removeClass("dwnld-hover");
				},
			});
		}
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
		<?php /*
		<div class="tile-flex-inner dwnld-win">
			<div class="dwnld-content">
				<div class="dwnld-os">Windows</div>
				<div class="dwnld-desc">Is a group of several proprietary graphical operating system</div>
			</div>
		</div>
		*/ ?>
		<?php /*
		<div class="tile-flex-inner dwnld-mac">
			<div class="dwnld-content">
				<div class="dwnld-os">MAC OS</div>
				<div class="dwnld-desc">UNIX-based operating system by Apple Inc.</div>
			</div>
		</div> */ ?>
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
	<?php /*
	<div class="dwnld-block-win" style="display: none;">
		<?php require_once("./pages/download/windows.php"); ?>
	</div> */ ?>
	<?php /*
	<div class="dwnld-block-mac" style="display: none;">
		<?php require_once("./pages/download/mac.php"); ?>
	</div> */ ?>	
	<div class="dwnld-block-bsd" style="display: none;">
		<?php require_once("./pages/download/bsd.php"); ?>
	</div>
	<div class="dwnld-block-src" style="display: none;">
		<?php require_once("./pages/download/source.php"); ?>
	</div>
</div>
