<?php

require_once("./inc/dao/purchases.php");

function user_purchase_prices($customer_id, $product_ids)
{
	if (!is_array($product_ids)) {
		$product_ids = [ $product_ids ];
	}
	
	// Fetch user purchases from the customer database
	$customer_db = null;
	$purchases = null;
	try {
		$customer_db = connect_db('customers');
		[ $result, $purchases ] = dao_user_latest_orders($customer_db, $customer_id);
		if (isset($result)) {
			return [ $result, null ];
		}
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($customer_db);
	}
	
	// Now we are ready to fetch prices
	$store_db = null;
	$prices = null;
	try {
		$store_db = connect_db('store');
		[ $result, $prices ] = dao_build_prices($store_db, $product_ids, $purchases);
		if (isset($result)) {
			return [ $result, null ];
		}
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($store_db);
	}
	
	return [ null, $prices ];
}

?>