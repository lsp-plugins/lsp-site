<?php

require_once('./inc/service/database.php');

function dao_create_product($db, $product) {
	$product_id = id_from_dict($db, 'product', $product);
	
	if (isset($product_id)) {
		return $product_id;
	}
	
	$stmt = mysqli_prepare($db, "INSERT INTO product(name) VALUES (?)");
	try {
		mysqli_stmt_bind_param($stmt, 's', $product);
		if (mysqli_stmt_execute($stmt)) {
			return mysqli_insert_id($db);
		}
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		mysqli_stmt_close($stmt);
	}
	
	return null;
}

function dao_get_build($db, $product_id, $build_type_id, $version) {
	
	$stmt = mysqli_prepare($db, "SELECT id FROM build WHERE " .
		"(product_id = ?) AND (type_id = ?) AND (major = ?) AND (minor = ?) AND (micro = ?)");
	try {
		mysqli_stmt_bind_param($stmt, 'iiiii', $product_id, $build_type_id, $version[0], $version[1], $version[2]);
		if (!mysqli_stmt_execute($stmt)) {
			return null;
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return null;
		}
			
		$row = mysqli_fetch_array($result);
		if ((!isset($row)) || (!isset($row['id']))) {
			return null;
		}
				
		return $row['id'];
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		mysqli_stmt_close($stmt);
	}
	
	return null;
}

function dao_create_build($db, $product_id, $build_type_id, $version) {
	
	$build_id = dao_get_build($db, $product_id, $build_type_id, $version);
	if (isset($build_id)) {
		return $build_id;
	}
	
	$stmt = mysqli_prepare($db, "INSERT INTO build(product_id, issue_date, type_id, major, minor, micro) " .
		"VALUES (?, current_date, ?, ?, ?, ?)");
	try {
		mysqli_stmt_bind_param($stmt, 'iiiii', $product_id, $build_type_id, $version[0], $version[1], $version[2]);
		if (mysqli_stmt_execute($stmt)) {
			return mysqli_insert_id($db);
		}
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		mysqli_stmt_close($stmt);
	}
	
	return null;
}

function dao_create_artifact($db, $product, $build_type, $format, $version, $platform, $architecture, $file_name) {
	$product_id = dao_create_product($db, $product);
	if (!isset($product_id)) {
		return ["Could not register product '{$product_id}'", null];
	}
	
	$build_type_id = id_from_dict($db, 'build_type', $build_type);
	if (!isset($build_type_id)) {
		return ["Unknown build type '{$build_type}'", null];
	}
	
	$build_id = dao_create_build($db, $product_id, $build_type_id, $version);
	if (!isset($build_id)) {
		return ["Could not create build type '{$build_type}' version '{$version[0]}.{$version[1]}.{$version[2]}' for product {$product}", null];
	}
	
	$platform_id = id_from_dict($db, 'platform', $platform);
	if (!isset($platform_id)) {
		return ["Unknown platform '{$platform}'", null];
	}
	
	$architecture_id = id_from_dict($db, 'architecture', $architecture);
	if (!isset($architecture_id)) {
		return ["Unknown architecture '{$architecture_id}'", null];
	}
	
	$format_id = id_from_dict($db, 'format', $format);
	if (!isset($format_id)) {
		return ["Unknown format '{$format_id}'", null];
	}
	

	$stmt = mysqli_prepare($db, "INSERT INTO artifact(build_id, platform_id, architecture_id, format_id, file_name) " .
		"VALUES (?, ?, ?, ?, ?)");
	try {
		mysqli_stmt_bind_param($stmt, 'iiiis', $build_id, $platform_id, $architecture_id, $format_id, $file_name);
		if (mysqli_stmt_execute($stmt)) {
			return [null, mysqli_insert_id($db)];
		}
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		mysqli_stmt_close($stmt);
	}
	
	return ['Unknown database error', null];
}

function dao_get_artifacts($db, $filter) {
	$conditions = [];
	$arguments = [];
	$types = [];
	if (isset($filter)) {
		if (isset($filter['product'])) {
			array_push($conditions, '(product = ?)');
			array_push($arguments, $filter['product']);
			array_push($types, 's');
		}
		if (isset($filter['arch'])) {
			array_push($conditions, '(architecture = ?)');
			array_push($arguments, $filter['arch']);
			array_push($types, 's');
		}
		elseif (isset($filter['architecture'])) {
			array_push($conditions, '(architecture = ?)');
			array_push($arguments, $filter['architecture']);
			array_push($types, 's');
		}
		if (isset($filter['platform'])) {
			array_push($conditions, '(platform = ?)');
			array_push($arguments, $filter['platform']);
			array_push($types, 's');
		}
		if (isset($filter['format'])) {
			array_push($conditions, '(format = ?)');
			array_push($arguments, $filter['format']);
			array_push($types, 'format');
		}
		if (isset($filter['version'])) {
			$version = $filter['version'];
			if (is_string($version)) {
				$version = explode('.', $version);
			} elseif (!is_array($version)) {
				return ["Invalid argument 'version'", null ];
			}
			if (count($version) != 3) {
				return ["Invalid value for argument 'version'", null ];
			}
			
			array_push($conditions, '((version_major = ?) AND (version_minor = ?) AND (version_micro = ?))');
			$arguments = array_merge($arguments, $version);
			array_push($types, 'i', 'i', 'i');
		}
	}
	
	$query = "SELECT * FROM v_artifacts";
	if (count($conditions) > 0) {
		$query .= " WHERE " . implode(" AND ", $conditions);
	}
	
	$stmt = mysqli_prepare($db, $query);
	try {
		if (count($arguments) > 0) {
			mysqli_stmt_bind_param($stmt, implode('', $types), ...$arguments);
		}
		if (!mysqli_stmt_execute($stmt)) {
			return ["Failed query", null];
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return ["Failed query", null];
		}
		
		$list = [];
		while ($row = mysqli_fetch_array($result)) {
			array_push($list, [
				'product' => $row['product'],
				'build_id' => $row['build_id'],
				'type' => $row['type'],
				'artifact_id' => $row['artifact_id'],
				'version' => [ $row['version_major'], $row['version_minor'], $row['version_micro'] ],
				'platform' => $row['platform'],
				'architecture' => $row['architecture'],
				'format' => $row['format'],
				'file' => $row['file_name']
			]);
		}
		
		return [null, $list];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		mysqli_stmt_close($stmt);
	}
	
	return ["Unknown error", null];
}



?>