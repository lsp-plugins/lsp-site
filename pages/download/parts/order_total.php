<?php

function show_order_total($order) {
	$cost = sprintf("%.2f USD", raw_to_price($order['price']));

	echo "<div id=\"order-total\">\n";
	echo "<div>TOTAL</div>\n";
	echo "<div>$cost</div>\n";
	echo "</div>\n";
}

?>