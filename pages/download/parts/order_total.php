<?php

function show_order_total($order) {
	$cost = sprintf("%.2f USD", $order['price'] / 100000.0);

	echo "<div id=\"order-total\">\n";
	echo "<div>TOTAL</div>\n";
	echo "<div>$cost</div>\n";
	echo "</div>\n";
}

?>