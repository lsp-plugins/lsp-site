<?php

require_once('./inc/service/database.php');

function dao_get_stripe_product($db, $test, $name) {
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db,
			"SELECT " .
			"  name, test, product_id " .
			"FROM stripe_products " .
			"WHERE (name=?) AND (test=?)");
		
		mysqli_stmt_bind_param($stmt, 'si', $name, ($test) ? 1 : 0);
		if (!mysqli_stmt_execute($stmt)) {
			return null;
		}
		$result = mysqli_stmt_get_result($stmt);
		if (!$result) {
			return null;
		}
		
		$row = mysqli_fetch_array($result);
		if (!isset($row)) {
			return null;
		}
		
		return [
			'name' => $row['name'],
			'test' => ($row['test']) ? true : false,
			'product_id' => $row['product_id']
		];
	} finally {
		db_safe_close($stmt);
	}
	
	return null;
}

function dao_create_stripe_product($db, $test, $name, $product_id) {
	$stmt = null;
	
	try {
		$stmt = mysqli_prepare($db, "INSERT INTO stripe_products(name, product_id, test) VALUES (?, ?, ?)");

		try {
			mysqli_stmt_bind_param($stmt, 'ssi', $name, $product_id, ($test) ? 1 : 0);
			
			if (!mysqli_stmt_execute($stmt)) {
				return null;
			}
			
			return [
				'name' => $name,
				'test' => ($test) ? true : false,
				'product_id' => $product_id
			];
				
		} catch (mysqli_sql_exception $e) {
			if (!unique_key_violation($e)) {
				db_log_exception($e);
				break;
			}
		}
	}
	finally {
		mysqli_stmt_close($stmt);
	}
	
	return null;
}

?>