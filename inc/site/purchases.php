<?php

require_once("./inc/dao/purchases.php");

function user_purchase_prices($customer_id, $product_ids)
{
	$store_db = null;
	$customer_db = null;
	
	if (!isset($product_ids)) {
		try {
			$store_db = connect_db('store');
			[ $error, $product_ids ]= dao_all_product_ids($store_db);
			if (isset($error)) {
				return [ $error, null ];
			}
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		} finally {
			db_safe_rollback($store_db);
		}
	}
	elseif (!is_array($product_ids)) {
		$product_ids = [ $product_ids ];
	}
	
	error_log("customer_id = $customer_id, product_ids = " . var_export($product_ids, true));
	
	// Fetch user purchases from the customer database
	$purchases = null;
	try {
		$customer_db = connect_db('customers');
		[ $error, $purchases ] = dao_user_latest_orders($customer_db, $customer_id);
		if (isset($error)) {
			return [ $error, null ];
		}
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($customer_db);
	}
	
	error_log("purchases = " . var_export($purchases, true));
	
	// Now we are ready to fetch prices
	$prices = null;
	try {
		if (!isset($store_db)) {
			$store_db = connect_db('store');
		}

		[ $error, $prices ] = dao_build_prices($store_db, $product_ids, $purchases);
		if (isset($error)) {
			return [ $error, null ];
		}
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($store_db);
	}
	
	error_log("prices = " . var_export($prices, true));
	
	return [ null, $prices ];
}

function user_cart($customer_id) {
	$db = null;
	try {
		$db = connect_db('customers');
		return dao_user_cart($db, $customer_id);
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function add_to_cart($customer_id, $product_id) {
	$db = null;
	try {
		$db = connect_db('customers');
		[$error, $insert_id] = dao_add_to_cart($db, $customer_id, $product_id);
		if ((!isset($error)) && (isset($insert_id))) {
			mysqli_commit($db);
		}
		return [$error, $insert_id];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function remove_from_cart($customer_id, $product_id) {
	$db = null;
	try {
		$db = connect_db('customers');
		[$error, $affected] = dao_remove_from_cart($db, $customer_id, $product_id);
		if ((!isset($error)) && ($affected > 0)) {
			mysqli_commit($db);
		}
		
		return [$error, $affected];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

function cleanup_cart($customer_id) {
	$db = null;
	try {
		$db = connect_db('customers');
		[$error, $affected] = dao_clear_user_cart($db, $customer_id);
		if ((!isset($error)) && ($affected > 0)) {
			mysqli_commit($db);
		}
		
		return [$error, $affected];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

?>