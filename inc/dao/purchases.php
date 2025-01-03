<?php

require_once('./inc/service/database.php');
require_once('./inc/service/utils.php');
require_once('./inc/service/uuid.php');

function dao_user_latest_orders($db, $customer_id)
{
	$stmt = null;

	try {
		$stmt = mysqli_prepare($db,
			"SELECT customer_id, product_id, version_raw " .
			"FROM v_latest_orders " .
			"WHERE (customer_id = ?)");
		
		mysqli_stmt_bind_param($stmt, 'i', $customer_id);
		if (!mysqli_stmt_execute($stmt)) {
			return ["Database error (execute)", null];
		}
			
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return ["Database error (get_result)", null];
		}

		$list = [];
		while ($row = mysqli_fetch_array($result)) {
			$raw_version = $row['version_raw'];
			
			array_push($list, [
				'customer_id' => $row['customer_id'],
				'product_id' => $row['product_id'],
				'raw_version' => $raw_version,
				'version' => raw_to_version($raw_version),
			]);
		}
		
		return [null, $list];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_user_cart($db, $customer_id) {
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db,
			"SELECT id, customer_id, product_id " .
			"FROM cart " .
			"WHERE (customer_id = ?)");
		
		mysqli_stmt_bind_param($stmt, 'i', $customer_id);
		if (!mysqli_stmt_execute($stmt)) {
			return ["Database error (execute)", null];
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return ["Database error (get_result)", null];
		}
			
		$list = [];
		while ($row = mysqli_fetch_array($result)) {
			array_push($list, [
				'id' => $row['id'],
				'customer_id' => $row['customer_id'],
				'product_id' => $row['product_id'],
			]);
		}
		
		return [null, $list];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_clear_user_cart($db, $customer_id) {
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db,
			"DELETE FROM cart " .
			"WHERE (customer_id = ?)");
		
		mysqli_stmt_bind_param($stmt, 'i', $customer_id);
		if (!mysqli_stmt_execute($stmt)) {
			return ["Database error (execute)", null];
		}
		
		$affected = mysqli_affected_rows($db);
		
		return [null, $affected];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_build_prices($db, $product_ids, $latest_orders)
{
	if (!is_array($product_ids)) {
		$product_ids = [ $product_ids ];
	}
	
	$order_mapping = utl_map_by_field($latest_orders, 'product_id');
	error_log("order_mapping = " . var_export($order_mapping, true));

	$stmt_upgrade = null;
	$stmt_purchase = null;
	
	try {
		$list = [];
		
		foreach ($product_ids as $product_id) {
			$data = null;
			
			if (isset($order_mapping[$product_id])) {
				$order_info = $order_mapping[$product_id][0];
				
				error_log("product_id = {$product_id}, order_info = " . var_export($order_info, true));
				
				$mapping_version = $order_info['raw_version'];
				
				// Upgrade logic. We need to fetch the latest version available for free download
				// and compute price for the upgrade
				if ($stmt_upgrade == null) {
					$stmt_upgrade = mysqli_prepare($db,
						"SELECT b.version_raw version_raw, bt.name build_type, p.price price, b.price upd_price " .
						"FROM build b " .
						"INNER JOIN product p " .
						"ON (p.id = b.product_id) " .
						"INNER JOIN build_type bt " .
						"ON (bt.id = b.type_id) " .
						"WHERE (b.product_id = ?) AND (b.version_raw >= ?) " .
						"ORDER BY version_raw");
				}
				
				mysqli_stmt_bind_param($stmt_upgrade, 'ii', $product_id, $mapping_version);
				if (!mysqli_stmt_execute($stmt_upgrade)) {
					return [ "Database error (execute)", null ];
				}
				
				$result = mysqli_stmt_get_result($stmt_upgrade);
				if (!isset($result)) {
					return [ "Database error (result)", null ];
				}
				
				$version_raw = $mapping_version;
				$download_raw = $version_raw;
				$purchase_raw = null;
				$major_update = false;
				$order_price = null;
				$price = 0;
				$is_free = false;
				
				// Process other rows
				while ($row = mysqli_fetch_array($result)) {
					$build_type = $row['build_type'];
					$update_price = $row['upd_price'];
					$version_raw = $row['version_raw'];
					
					// First row?
					if (!isset($order_price)) {
						$order_price = $row['price'];
						$is_free = ($order_price <= 0);							
						if ($version_raw !== $mapping_version) {
							$price = $order_price;
						}
					}
					
					// Check current state
					if ($build_type === 'major') {
						$major_update = true;
						$price = $order_price;
					} else if (isset($update_price)) {
						if (!$major_update) {
							// We don't take more than 80% price for upgrades
							$price = max($price + $update_price, intval($order_price * 0.8));
						}
					}
					
					// If price is set, then we turned into paid options
					if ($price > 0) {
						$purchase_raw = $version_raw;
					} else {
						$download_raw = $version_raw;
					}
				}
				
				$is_upgrade = isset($download_raw) && isset($purchase_raw);
				
				$data = [
					'download_raw' => $download_raw,
					'download' => raw_to_version($download_raw),
					'purchase_raw' => $purchase_raw,
					'purchase' => raw_to_version($purchase_raw),
					'is_upgrade' => $is_upgrade,
					'is_free' => $is_free,
					'price' => $price,
					'product_price' => (isset($order_price)) ? $order_price : 0
				];
			} else {
				// Purchase logic
				if ($stmt_purchase == null) {
					$stmt_purchase = mysqli_prepare($db,
						"SELECT max(b.version_raw) version_raw, max(p.price) price " .
						"FROM build b " .
						"INNER JOIN product p " .
						"ON (p.id = b.product_id) " .
						"WHERE (b.product_id = ?)");
				}
				
				mysqli_stmt_bind_param($stmt_purchase, 'i', $product_id);
				if (!mysqli_stmt_execute($stmt_purchase)) {
					return [ "Database error (execute)", null ];
				}
				
				$result = mysqli_stmt_get_result($stmt_purchase);
				if (!isset($result)) {
					return [ "Database error (result)", null ];
				}
				
				$row = mysqli_fetch_array($result);
				if (isset($row)) {
					$version_raw = $row['version_raw'];
					$price = $row['price'];
					
					$data = [
						'download_raw' => null,
						'download' => null,
						'purchase_raw' => $version_raw,
						'purchase' => raw_to_version($version_raw),
						'is_upgrade' => false,
						'is_free' => ($price <= 0),
						'price' => $price,
						'product_price' => $price
					];
				}
			}
			
			$list[$product_id] = $data;
		}
		
		return [null, $list];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_close($stmt_upgrade);
		db_safe_close($stmt_purchase);
	}
}

function dao_add_to_cart($db, $customer_id, $product_id)
{
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db,
			"INSERT INTO cart(customer_id, product_id) " .
			"VALUES (?, ?) ON DUPLICATE KEY UPDATE product_id = product_id");
		
		mysqli_stmt_bind_param($stmt, 'ii', $customer_id, $product_id);
		if (!mysqli_stmt_execute($stmt)) {
			return ["Database error (execute)", null];
		}
		
		$insert_id = mysqli_insert_id($db);
		return [null, $insert_id];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_remove_from_cart($db, $customer_id, $product_id)
{
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db,
			"DELETE FROM cart " .
			"WHERE (customer_id = ?) AND (product_id = ?)");
		
		mysqli_stmt_bind_param($stmt, 'ii', $customer_id, $product_id);
		if (!mysqli_stmt_execute($stmt)) {
			return ["Database error (execute)", null];
		}
		
		$affected = mysqli_affected_rows($db);
		
		return [null, $affected];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_create_order($db, $customer_id, $positions)
{
	$stmt = null;
	$order_price = 0;
	$status = 'draft';

	foreach ($positions as $item) {
		if (!isset($item['product_id'])) {
			return [ 'Not set product_id', null ];
		}
		if ((!isset($item['version'])) && (!isset($item['raw_version']))) {
			return [ 'Not set version nor raw_version', null ];
		}
		if (!isset($item['is_upgrade'])) {
			return [ 'Not set upgrade', null ];
		}
		if (!isset($item['price'])) {
			return [ 'Not set price', null ];
		}
		$order_price += $item['price'];
	}
	
	try {
		$order_id = null;
		
		// Create new order draft
		while (true) {
			$order_id = make_uuid();
			$created = db_current_timestamp();
			
			try {
				$stmt = mysqli_prepare($db,
					"INSERT INTO orders (id, customer_id, created_time, amount, status)" .
					"VALUES (?, ?, ?, ?, (SELECT id FROM order_status WHERE name=?))");
				
				mysqli_stmt_bind_param($stmt, 'sisis', $order_id, $customer_id, $created, $order_price, $status);
				if (!mysqli_stmt_execute($stmt)) {
					return ["Database error (create order)", null];
				}
				
				break;
			} catch (mysqli_sql_exception $e) {
				if (!unique_key_violation($e)) {
					throw $e;
				}
			}
		}
		
		// Fill order entries
		db_safe_close($stmt);
		$stmt = mysqli_prepare($db,
			"INSERT INTO order_item (order_id, product_id, version_raw, upgrade, amount)" .
			"VALUES (?, ?, ?, ?, ?)");
		
		foreach ($positions as $item) {
			$product_id = $item['product_id'];
			$version = (isset($item['raw_version'])) ? $item['raw_version'] : version_to_raw($item['version']);
			$upgrade = ($item['is_upgrade']) ? 1 : 0;
			$price = $item['price'];
			
			mysqli_stmt_bind_param($stmt, 'siiii', $order_id, $product_id, $version, $upgrade, $price);
			if (!mysqli_stmt_execute($stmt)) {
				return ["Database error (create order item)", null];
			}
			
			$id = mysqli_insert_id($db);
			if (!isset($id)) {
				return ["Database error (no order item id)", null];
			}
		}
		
		return [null, $order_id];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_find_order($db, $order_id)
{
	if (!isset($order_id)) {
		return ['Invalid parameters', null];
	}
	
	$order = null;
	$stmt = null;
	
	try {
		// Fetch order
		$stmt = mysqli_prepare($db,
			"SELECT " .
			"o.id order_id, o.method method, o.remote_id remote_id, o.payment_url payment_url, o.customer_id customer_id, " .
			"o.created_time created_time, o.submit_time submit_time, o.refund_time refund_time, " .
			"o.complete_time complete_time, o.verify_time verify_time, " .
			"o.status status_id, os.name status, o.amount amount " .
			"FROM orders o " .
			"INNER JOIN order_status os " .
			"ON (os.id = o.status)" .
			"WHERE (o.id = ?)");
		
		mysqli_stmt_bind_param($stmt, 's', $order_id);
		
		if (!mysqli_stmt_execute($stmt)) {
			return ["Database error (fetch order)", null];
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return ["Database error (get order)", null];
		}
		
		$row = mysqli_fetch_array($result);
		if (!isset($row)) {
			return ['Not found', null];
		}
		
		$order = [
			'order_id' => $row['order_id'],
			'method' => $row['method'],
			'remote_id' => $row['remote_id'],
			'payment_url' => $row['payment_url'],
			'customer_id' => $row['customer_id'],
			'created' => $row['created_time'],
			'submitted' => $row['submit_time'],
			'refunded' => $row['refund_time'],
			'completed' => $row['complete_time'],
			'verified' => $row['verify_time'],
			'status_id' => $row['status_id'],
			'status' => $row['status'],
			'amount' => $row['amount']
		];
		
		// Fetch order items
		db_safe_close($stmt);
		$items = [];
		
		$stmt = mysqli_prepare($db,
			"SELECT * from order_item " .
			"WHERE (order_id = ?)");
		
		mysqli_stmt_bind_param($stmt, 's', $order_id);
		
		if (!mysqli_stmt_execute($stmt)) {
			return ["Database error (fetch items)", null];
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return ["Database error (get items)", null];
		}
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($items, [
				'order_id' => $order_id,
				'order_item_id' => $row['id'],
				'product_id' => $row['product_id'],
				'raw_version' => $row['version_raw'],
				'is_upgrade' => $row['upgrade'] != 0,
				'price' => $row['amount']
			]);
		}
		
		$order['items'] = $items;
		
		return [null, $order];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_get_unfinished_orders($db)
{
	$stmt = null;
	
	try {
		// Fetch order
		$stmt = mysqli_prepare($db,
			"SELECT " .
			"o.id order_id, o.method method, o.remote_id remote_id, o.payment_url payment_url, o.customer_id customer_id, " .
			"o.created_time created_time, o.submit_time submit_time, o.refund_time refund_time, " .
			"o.complete_time complete_time, o.verify_time verify_time, " .
			"o.status status_id, os.name status, o.amount amount " .
			"FROM orders o " .
			"INNER JOIN order_status os " .
			"ON (os.id = o.status)" .
			"WHERE (os.name in ('created', 'draft'))");
		
		if (!mysqli_stmt_execute($stmt)) {
			return ["Database error (fetch order)", null];
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return ["Database error (get order)", null];
		}
		
		$order_list = [];
		while (true) {
			$row = mysqli_fetch_array($result);
			if (!isset($row)) {
				break;
			}
		
			array_push($order_list, [
				'order_id' => $row['order_id'],
				'method' => $row['method'],
				'remote_id' => $row['remote_id'],
				'payment_url' => $row['payment_url'],
				'customer_id' => $row['customer_id'],
				'created' => $row['created_time'],
				'submitted' => $row['submit_time'],
				'refunded' => $row['refund_time'],
				'completed' => $row['complete_time'],
				'verified' => $row['verify_time'],
				'status_id' => $row['status_id'],
				'status' => $row['status'],
				'amount' => $row['amount']
			]);
		}
		
		return [null, $order_list];
	} catch (mysqli_sql_exception $e) {
		return [db_log_exception($e), null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_remove_order($db, $order_id)
{
	if (!isset($order_id)) {
		return ['Invalid parameters', null];
	}
	
	$stmt = null;
	
	try {
		// Fetch order
		$query = "DELETE FROM orders " .
			"WHERE (id = ?) " .
			"  AND (status IN (" .
			"    SELECT os.id " .
			"    FROM order_status os " .
			"    WHERE (os.name = ?)" .
			"  ))";
		
		$stmt = mysqli_prepare($db, $query);
		$status = 'draft';
		mysqli_stmt_bind_param($stmt, 'ss', $order_id, $status);
		
		if (!mysqli_stmt_execute($stmt)) {
			return ["Database error (remove order)", null];
		}
		
		$affected = mysqli_affected_rows($db);
		
		return ($affected > 0) ? [null, $affected] : [ 'Order not found', null ];
	} catch (mysqli_sql_exception $e) {
		return [db_log_exception($e), null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_remove_item_from_order($db, $customer_id, $order_id, $product_id)
{
	if ((!isset($order_id)) || (!isset($product_id)) || (!isset($customer_id))) {
		return ['Invalid parameters', null];
	}
	
	$stmt = null;
	
	try {
		// Fetch order
		$query = "DELETE FROM order_item " .
			"WHERE (product_id = ?) " .
			"  AND (order_id IN (" .
			"    SELECT o.id FROM orders o " .
			"    INNER JOIN order_status os " .
			"    ON (os.id = o.status)" .
			"    WHERE (o.id = ?) AND (o.customer_id = ?) AND (os.name = ?)" .
			"  ))";
		
		$stmt = mysqli_prepare($db, $query);
		$status = 'draft';
		
		mysqli_stmt_bind_param($stmt, 'isis', $product_id, $order_id, $customer_id, $status);
		
		if (!mysqli_stmt_execute($stmt)) {
			return ["Database error (remove order item)", null];
		}
		
		$affected = mysqli_affected_rows($db);
		
		return [null, $affected];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_update_order($db, $order_id, $fields) {
	if ((!isset($order_id)) || (!isset($fields))) {
		return ['Invalid parameters', null];
	}
	
	$conditions = [ ];
	$arguments = [ ];
	$types = [ ];
	$stmt = null;
	
	try {
		// Update order
		if (isset($fields['status'])) {
			array_push($conditions, 'status = (SELECT id FROM order_status WHERE name=?)');
			array_push($arguments, $fields['status']);
			array_push($types, 's');
		}
		if (isset($fields['price'])) {
			array_push($conditions, 'amount = ?');
			array_push($arguments, $fields['price']);
			array_push($types, 'i');
		}
		if (isset($fields['remote_id'])) {
			array_push($conditions, 'remote_id = ?');
			array_push($arguments, $fields['remote_id']);
			array_push($types, 's');
		}
		if (isset($fields['payment_url'])) {
			array_push($conditions, 'payment_url = ?');
			array_push($arguments, $fields['payment_url']);
			array_push($types, 's');
		}
		if (isset($fields['method'])) {
			array_push($conditions, 'method = ?');
			array_push($arguments, $fields['method']);
			array_push($types, 's');
		}
		if (isset($fields['submitted'])) {
			array_push($conditions, 'submit_time = ?');
			array_push($arguments, $fields['submitted']);
			array_push($types, 's');
		}
		if (isset($fields['completed'])) {
			array_push($conditions, 'complete_time = ?');
			array_push($arguments, $fields['completed']);
			array_push($types, 's');
		}
		if (isset($fields['refunded'])) {
			array_push($conditions, 'refund_time = ?');
			array_push($arguments, $fields['refunded']);
			array_push($types, 's');
		}
		
		// Form the query
		$query = "UPDATE orders SET " .
			implode(', ', $conditions) .
			" WHERE (id = ?) ";
		array_push($arguments, $order_id);
		array_push($types, 's');
		
		error_log($query);
		
		$stmt = mysqli_prepare($db, $query);
		if (count($arguments) > 0) {
			mysqli_stmt_bind_param($stmt, implode('', $types), ...$arguments);
		}
		
		if (!mysqli_stmt_execute($stmt)) {
			return ["Database error (update order)", null];
		}
		
		$affected = mysqli_affected_rows($db);
		
		return [null, $affected];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_close($stmt);
	}
}

?>