<?php

function show_order_total($order) {
	$char_usd = ' $';
	$cost = sprintf("%.2f", raw_to_price($order['price'])) . $char_usd;

	echo "<div id=\"order-total\" class=\"order-total\">\n";
	echo "<div class=\"ot-total\">TOTAL</div>\n";
	echo "<div class=\"ot-cost\">$cost</div>\n";
	echo "</div>\n";
}

?>
