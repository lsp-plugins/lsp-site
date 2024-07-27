<?php

require_once("./inc/dao/artifacts.php");
require_once('./inc/dao/logging.php');
require_once("./inc/dao/purchases.php");
require_once("./inc/site/session.php");

function get_products($filter)
{
	$db = null;
	try {
		$db = connect_db('store');
		return dao_get_products($db, $filter);
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

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

function create_order() {
	$session = ensure_user_session_is_set();
	if (!isset($session)) {
		return ['HTTP session expired', null];
	}
	$session_user = get_session_user();
	$customer_id = $session_user['id'];
	error_log("customer_id: {$customer_id}");

	$customer_db = null;
	$store_db = null;
	try {
		$cart = null;
		$purchases = null;
		try {
			$customer_db = connect_db('customers');
			[$error, $cart] = dao_user_cart($customer_db, $customer_id);
			if (isset($error)) {
				return [$error, null];
			}
			
			error_log('user cart: ' . var_export($cart, true));
			if (count($cart) <= 0) {
				return [ null, null ];
			}
			
			[ $error, $purchases ] = dao_user_latest_orders($customer_db, $customer_id);
			if (isset($error)) {
				return [ $error, null ];
			}
			
			error_log('user purchases: ' . var_export($purchases, true));
			
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		}
		
		// Now we are ready to fetch prices
		$product_ids = utl_unique_field($cart, 'product_id');
		
		error_log('product_ids: ' . var_export($product_ids, true));
		
		$prices = null;
		try {
			$store_db = connect_db('store');
			
			[ $error, $prices ] = dao_build_prices($store_db, $product_ids, $purchases);
			if (isset($error)) {
				return [ $error, null ];
			}
			
			error_log('prices: ' . var_export($prices, true));
			
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		}
		
		foreach ($cart as &$item) {
			$product_id = $item['product_id'];
			$product = $prices[$product_id];
			$item['price'] = $product['price'];
			$item['raw_version'] = $product['purchase_raw'];
			$item['upgrade'] = isset($product['download_raw']);
		}

		error_log('cart: ' . var_export($cart, true));
		
		// Now we are ready to create order
		try {
			[$error, $order] = dao_create_order($customer_db, $customer_id, $cart);
			if (isset($error)) {
				return [$error, null ];
			}
			
			error_log('order: ' . var_export($order, true));
			
			mysqli_commit($customer_db);
			
			return [null, $order];
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		}
	} finally {
		db_safe_rollback($customer_db);
		db_safe_rollback($store_db);
	}
}

function enrich_order($order) {
	$store_db = null;
	try {
		// Fetch related products
		$items = &$order['items'];
		$product_ids = utl_unique_field($items, 'product_id');
		$products = null;
		try {
			$store_db = connect_db('store');
			[$error, $products] = dao_get_products($store_db, [ 'product_id' => $product_ids ]);
			if (isset($error)) {
				return [$error, null];
			}
			
			error_log("products: " . var_export($products, true));
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		}
		
		// Enrich order items with product information
		$product_map = utl_map_unique_by_field($products, 'id');
		error_log("product_map: " . var_export($product_map, true));
		
		foreach ($items as &$item) {
			$product_id = $item['product_id'];
			if (isset($product_map[$product_id])) {
				$product = $product_map[$product_id];
				$item['product_name'] = $product['name'];
				$item['product_desc'] = $product['description'];
			} else {
				$item['product_name'] = null;
				$item['product_desc'] = null;
			}
		}
		
		return [null, $order];
	} finally {
		db_safe_rollback($store_db);
	}
}

function find_order($order_id) {
	$session = ensure_user_session_is_set();
	if (!isset($session)) {
		return ['HTTP session expired', null];
	}
	$session_user = get_session_user();
	$customer_id = $session_user['id'];
	error_log("customer_id: {$customer_id}");
	
	$customer_db = null;
	
	try {
		// Fetch order
		$order = null;
		try {
			$customer_db = connect_db('customers');
			[$error, $order] = dao_find_order($customer_db, $customer_id, $order_id);
			if (isset($error)) {
				return [$error, null];
			}
			
			error_log("order: " . var_export($order, true));
		} catch (mysqli_sql_exception $e) {
			$error = db_log_exception($e);
			return [$error, null];
		}
		
		// Enrich order information
		return enrich_order($order);
	} finally {
		db_safe_rollback($customer_db);
	}
}

function remove_item_from_order($customer_id, $order_id, $product_id) {
	$db = null;
	try {
		$db = connect_db('customers');
		[$error, $affected] = dao_remove_item_from_order($db, $customer_id, $order_id, $product_id);
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

function submit_order($customer_id, $order_id, $remote_id, $price) {
	$session_id = user_session_id();
	$db = null;
	
	try {
		$db = connect_db('customers');
		[$error, $affected] = dao_update_order($db, $order_id, [
				'status' => 'created',
				'remote_id' => $remote_id,
				'price' => $price
			]);
		if (isset($error)) {
			return [$error, null];
		} else if ($affected <= 0) {
			return ['No order updated', null];
		}
		
		[$error, $order] = dao_find_order($db, $customer_id, $order_id);
		if (isset($error)) {
			return [$error, null];
		}
		
		dao_log_user_action($db, $customer_id, $session_id, 'submit_order', $order);
		
		// Enrich order information
		[$error, $order] = enrich_order($order);
		if (isset($error)) {
			return [ $error, null ];
		}
		
		// Commit information
		mysqli_commit($db);
		return [null, $order];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_rollback($db);
	}
}

?>