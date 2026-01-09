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
					<li class="menu-li-footer "><a href="?page=privacy">Privacy Policy</a></li>
				</ul>
			</div>
			<div class="footer-links">
				<div>LINKS</div>
				<ul>
					<li><a href="https://github.com/lsp-plugins/lsp-plugins/" target="_blank">GitHub</a></li>
					<li><a href="https://linuxmusicians.com/" target="_blank">LinuxMusicians</a></li>
					<li><a href="https://linuxaudio.dev/" target="_blank">Linux Audio Plugin Development</a></li>
					<li><a href="https://lv2plug.in/" target="_blank">LV2</a></li>
					<li><a href="https://www.kvraudio.com/plugins/lsp" target="_blank">KVR Audio</a></li>
					<li><a href="https://ardour.org/" target="_blank">Ardour DAW</a></li>
					<li><a href="https://unfa.bandcamp.com/" target="_blank">unfa</a></li>
					<li><a href="https://steveerdeman.wixsite.com/home" target="_blank">STEVE ERDEMAN</a></li>
					<li><a href="https://www.youtube.com/@carlirwinmusic" target="_blank">Carl Irwin Music</a></li>
				</ul>
			</div>

			<div class="footer-copy">
				<a data-fancybox data-src="#hidden-content" href="javascript:;">
					<div class="donate-button-footer"></div>
				</a>
				<p>&copy; Linux Studio Plugins, 2015-2026</p>
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
