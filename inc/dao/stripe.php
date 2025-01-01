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
		
		$test_flag = ($test) ? 1 : 0;
		mysqli_stmt_bind_param($stmt, 'si', $name, $test_flag);
		if (!mysqli_stmt_execute($stmt)) {
			return [ "Error executing statement", null ];
		}
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return [ "Error fetching result from database", null ];
		}
		
		$row = mysqli_fetch_array($result);
		if (!isset($row)) {
			return [ null, null ];
		}
		
		return [
			null,
			[
				'name' => $row['name'],
				'test' => ($row['test']) ? true : false,
				'product_id' => $row['product_id']
			]
		];
	} finally {
		db_safe_close($stmt);
	}
	
	return [ null, null ];
}

function dao_create_stripe_product($db, $test, $name, $product_id) {
	$stmt = null;
	
	try {
		$stmt = mysqli_prepare($db, "INSERT INTO stripe_products(name, product_id, test) VALUES (?, ?, ?)");

		$test_flag = ($test) ? 1 : 0;
		mysqli_stmt_bind_param($stmt, 'ssi', $name, $product_id, $test_flag);
		
		if (!mysqli_stmt_execute($stmt)) {
			return [ "Error executing statement", null ];
		}
		
		return [
			null,
			[
				'name' => $name,
				'test' => ($test) ? true : false,
				'product_id' => $product_id
			]
		];
	}
	finally {
		mysqli_stmt_close($stmt);
	}
	
	return null;
}

function dao_get_stripe_price($db, $test, $product_id, $amount) {
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db,
			"SELECT " .
			"  price_id, product_id, test, amount " .
			"FROM stripe_prices " .
			"WHERE (product_id=?) AND (test=?) AND (amount = ?)");
		
		$test_flag = ($test) ? 1 : 0;
		mysqli_stmt_bind_param($stmt, 'sii', $product_id, $test_flag, $amount);
		if (!mysqli_stmt_execute($stmt)) {
			return [ "Error executing statement", null ];
		}
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return [ "Error fetching result from database", null ];
		}
		
		$row = mysqli_fetch_array($result);
		if (!isset($row)) {
			return [ null, null ];
		}
		
		return [
			null,
			[
				'product_id' => $row['product_id'],
				'price_id' => $row['price_id'],
				'amount' => $row['amount'],
				'test' => ($row['test']) ? true : false
			]
		];
	} finally {
		db_safe_close($stmt);
	}
	
	return [ null, null ];
}

function dao_create_stripe_price($db, $test, $product_id, $price_id, $amount) {
	$stmt = null;
	
	try {
		$stmt = mysqli_prepare($db, "INSERT INTO stripe_prices(product_id, price_id, amount, test) VALUES (?, ?, ?, ?)");
		
		$test_flag = ($test) ? 1 : 0;
		mysqli_stmt_bind_param($stmt, 'ssii', $product_id, $price_id, $amount, $test_flag);
		
		if (!mysqli_stmt_execute($stmt)) {
			return [ "Error executing statement", null ];
		}
		
		return [
			null,
			[
				'product_id' => $product_id,
				'price_id' => $price_id,
				'amount' => $amount,
				'test' => ($test) ? true : false
			]
		];
	}
	finally {
		mysqli_stmt_close($stmt);
	}
	
	return null;
}

?>