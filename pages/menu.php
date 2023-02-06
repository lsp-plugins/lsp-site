<div class="container">
	<a href="/index.php" ><div class="logo smooth"></div></a>
	<div class="inner-container">
		<a href="/index.php" >
			<div class="logo-text"></div>
		</a>
		<div class="container-menu">
			<div class="m-cover"></div>
			<div class="menu">
				<ul class="menu-ul">
				<?php
					foreach ($PAGES as $key => $value)
					{
						if ((array_key_exists('hidden', $value)) && ($value['hidden']))
							continue;

						$sclass = isset($value['style']) ? $value['style'] : "";
						if ($key == $curr_page)
							echo "<li class=\"sel menu-li ${sclass}\">";
						else
							echo "<li class=\"menu-li ${sclass}\">";

						/*if (isset($value['icon']))
							echo "<img src=\"${SITEROOT}/img/${value['icon']}\">";*/

						if (($key != $curr_page) || ((isset($value['reentrant'])) && ($value['reentrant'])))
							echo "<a href=\"${SITEROOT}?page={$key}\">" . htmlspecialchars($value['name']) . "</a>";
						else
							echo htmlspecialchars($value['name']);

						echo "</li>\n";
					}
				?>
				</ul>
			</div>
			<div class="m-cover-r"></div>
			<div class="menu-mob-container">
				<a  class="menu-mob-triger menu-mobile-button" href="#">MENU</a>
				<div class="menu-mob-popup">
					<a class="menu-mob-close" href="#"></a>
					<ul class="menu-mob-ul">
					<?php
						foreach ($PAGES as $key => $value)
						{
							if ((array_key_exists('hidden', $value)) && ($value['hidden']))
								continue;

							$sclass = isset($value['style']) ? $value['style'] : "";
							if ($key == $curr_page)
								echo "<li class=\"sel menu-mob-li ${sclass}\">";
							else
								echo "<li class=\"menu-mob-li ${sclass}\">";

							/*if (isset($value['icon']))
								echo "<img src=\"${SITEROOT}/img/${value['icon']}\">";*/

							if (($key != $curr_page) || ((isset($value['reentrant'])) && ($value['reentrant'])))
								echo "<a href=\"${SITEROOT}?page={$key}\">" . htmlspecialchars($value['name']) . "</a>";
							else
								echo htmlspecialchars($value['name']);

							echo "</li>\n";
						}
					?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
