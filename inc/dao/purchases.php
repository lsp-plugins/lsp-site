<?php

require_once('./inc/service/database.php');
require_once('./inc/service/utils.php');

function dao_user_latest_orders($db, $customer_id)
{
	$stmt = mysqli_prepare($db,
		"SELECT customer_id, product_id, version_raw " .
		"FROM v_latest_orders " .
		"WHERE (customer_id = ?)");
	try {
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
		mysqli_stmt_close($stmt);
	}
}

function dao_user_cart($db, $customer_id) {
	$stmt = mysqli_prepare($db,
		"SELECT id, customer_id, product_id " .
		"FROM cart " .
		"WHERE (customer_id = ?)");
	try {
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
		mysqli_stmt_close($stmt);
	}
}

function dao_clear_user_cart($db, $customer_id) {
	$stmt = mysqli_prepare($db,
		"DELETE FROM cart " .
		"WHERE (customer_id = ?)");
	try {
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
		mysqli_stmt_close($stmt);
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
			
			if (array_key_exists($product_id, $order_mapping) && isset($order_mapping[$product_id])) {
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
				
				// Process other rows
				while ($row = mysqli_fetch_array($result)) {
					$build_type = $row['build_type'];
					$update_price = $row['upd_price'];
					$version_raw = $row['version_raw'];
					
					// First row?
					if (!isset($order_price)) {
						$order_price = $row['price'];
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
				
				$data = [
					'download_raw' => $download_raw,
					'download' => raw_to_version($download_raw),
					'purchase_raw' => $purchase_raw,
					'purchase' => raw_to_version($purchase_raw),
					'price' => $price
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
					
					$data = [
						'download_raw' => null,
						'download' => null,
						'purchase_raw' => $version_raw,
						'purchase' => raw_to_version($version_raw),
						'price' => $row['price']
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
	$stmt = mysqli_prepare($db,
		"INSERT INTO cart(customer_id, product_id) " .
		"VALUES (?, ?) ON DUPLICATE KEY UPDATE product_id = product_id");
	try {
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
		mysqli_stmt_close($stmt);
	}
}

function dao_remove_from_cart($db, $customer_id, $product_id)
{
	$stmt = mysqli_prepare($db,
		"DELETE FROM cart " .
		"WHERE (customer_id = ?) AND (product_id = ?)");
	try {
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
		mysqli_stmt_close($stmt);
	}
}

?>