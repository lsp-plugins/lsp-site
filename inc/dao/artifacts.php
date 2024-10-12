<?php

require_once('./inc/service/database.php');
require_once('./inc/service/uuid.php');

function dao_create_product($db, $product) {
	$product_id = id_from_dict($db, 'product', $product);
	
	if (isset($product_id)) {
		return $product_id;
	}
	
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db, "INSERT INTO product(name) VALUES (?)");
		
		mysqli_stmt_bind_param($stmt, 's', $product);
		if (mysqli_stmt_execute($stmt)) {
			return mysqli_insert_id($db);
		}
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		db_safe_close($stmt);
	}
	
	return null;
}

function dao_all_product_ids($db) {
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db, "SELECT DISTINCT id FROM product");
		
		if (!mysqli_stmt_execute($stmt)) {
			return null;
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return null;
		}
		
		$product_ids = [];
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($result, $row['id']);
		}
		
		return [ null, $product_ids ];
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		db_safe_close($stmt);
	}
	
	return null;
}

function dao_get_products($db, $filter = null) {
	$stmt = null;
	$conditions = [];
	$arguments = [];
	$types = [];
	
	if (isset($filter)) {
		if (isset($filter['product_id'])) {
			$product_id = $filter['product_id'];
			if (is_array($product_id)) {
				if (count($product_id) > 0) {
					$condition = '(id in (' . implode(', ', array_fill(0, count($product_id), '?')) . '))';
					array_push($conditions, $condition);
					$arguments = array_merge($arguments, $product_id);
					array_push($types, str_repeat('i', count($product_id)));
				}
			} else {
				array_push($conditions, '(id = ?)');
				array_push($arguments, $product_id);
				array_push($types, 'i');
			}
		}
		if (isset($filter['id'])) {
			$product_id = $filter['id'];
			if (is_array($product_id)) {
				if (count($product_id) > 0) {
					$condition = '(id in (' . implode(', ', array_fill(0, count($product_id), '?')) . '))';
					array_push($conditions, $condition);
					$arguments = array_merge($arguments, $product_id);
					array_push($types, str_repeat('i', count($product_id)));
				}
			} else {
				array_push($conditions, '(id = ?)');
				array_push($arguments, $product_id);
				array_push($types, 'i');
			}
		}
		if (isset($filter['name'])) {
			$product_name = $filter['name'];
			if (is_array($product_name)) {
				if ((count($product_name)) > 0) {
					$condition = '(name in (' . implode(', ', array_fill(0, count($product_name), '?')) . '))';
					array_push($conditions, $condition);
					$arguments = array_merge($arguments, $product_name);
					array_push($types, str_repeat('s', count($product_name)));
				}
			} else {
				array_push($conditions, '(name = ?)');
				array_push($arguments, $product_name);
				array_push($types, 's');
			}
		}
	}
	
	$query = "SELECT id, name, description, price FROM product";
	if (count($conditions) > 0) {
		$query .= " WHERE " . implode(" AND ", $conditions);
	}

	try {
		error_log($query);
		
		$stmt = mysqli_prepare($db, $query);
		if (count($arguments) > 0) {
			mysqli_stmt_bind_param($stmt, implode('', $types), ...$arguments);
		}
		if (!mysqli_stmt_execute($stmt)) {
			return ["Failed query", null];
		}
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result)) {
			return null;
		}
		
		$list = [];
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($list,
				[
					'id' => $row['id'],
					'name' => $row['name'],
					'description' => $row['description'],
					'price' => $row['price']
				]);
		}
		
		return [ null, $list ];
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		db_safe_close($stmt);
	}
}

function dao_get_build($db, $product_id, $build_type_id, $version) {
	
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db, "SELECT id FROM build WHERE " .
			"(product_id = ?) AND (type_id = ?) AND (major = ?) AND (minor = ?) AND (micro = ?)");
		
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
		db_safe_close($stmt);
	}
	
	return null;
}

function dao_create_build($db, $product_id, $build_type_id, $version) {
	
	$build_id = dao_get_build($db, $product_id, $build_type_id, $version);
	if (isset($build_id)) {
		return $build_id;
	}
	
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db, "INSERT INTO build(product_id, issue_date, type_id, major, minor, micro, version_raw) " .
			"VALUES (?, current_date, ?, ?, ?, ?, ?)");
		
		$raw_version = ($version[0] * 1000 + $version[1]) * 1000 + $version[2];
		mysqli_stmt_bind_param($stmt, 'iiiiii', $product_id, $build_type_id, $version[0], $version[1], $version[2], $raw_version);
		if (mysqli_stmt_execute($stmt)) {
			return mysqli_insert_id($db);
		}
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		db_safe_close($stmt);
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

	$stmt = null;
	try {
		while (true) {
			// Try to fetch already existing record
			$stmt = mysqli_prepare(
				"SELECT id, file_name from artifact " .
				"WHERE (buld_id = ?) AND (platform_id = ?) AND (architecture_id = ?) AND (format_id = ?)");
			mysqli_stmt_bind_param($stmt, 'iiii', $build_id, $platform_id, $architecture_id, $format_id);
			
			$row = mysqli_fetch_array($result);
			if ((isset($row)) && (isset($row['id'])) && (isset($row['file_name']))) {
				$artifact_id = $row['id'];
				$old_file_name = $row['file_name'];
				if (strcmp($old_file_name, $file_name) != 0) {
					return ["Conflicting file name requested: '{$file_name}', existing: '{$old_file_name}'", null];
				}
				
				return [null, $artifact_id];
			}
			
			// Try to create new record
			$stmt = mysqli_prepare($db,
				"INSERT INTO artifact(id, build_id, platform_id, architecture_id, format_id, file_name) " .
				"VALUES (?, ?, ?, ?, ?, ?)");
			$artifact_id = make_uuid();
	
			try {
				mysqli_stmt_bind_param($stmt, 'siiiis', $artifact_id, $build_id, $platform_id, $architecture_id, $format_id, $file_name);
				if (mysqli_stmt_execute($stmt)) {
					return [null, $artifact_id];
				}
			} catch (mysqli_sql_exception $e) {
				if (!unique_key_violation($e)) {
					throw $e;
				}
			}
		}
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, null];
	} finally {
		mysqli_stmt_close($stmt);
	}
	
	return ['Unknown database error', null];
}

function dao_get_artifacts($db, $view, $filter) {
	$conditions = [];
	$arguments = [];
	$types = [];
	if (isset($filter)) {
		if (isset($filter['artifact_id'])) {
			array_push($conditions, '(artifact_id = ?)');
			array_push($arguments, $filter['artifact_id']);
			array_push($types, 's');
		}
		if (isset($filter['product'])) {
			array_push($conditions, '(product = ?)');
			array_push($arguments, $filter['product']);
			array_push($types, 's');
		}
		if (isset($filter['product_id'])) {
			$product_id = $filter['product_id'];
			if (is_array($product_id)) {
				if (count($product_id) > 0) {
					$condition = '(product_id in (' . implode(', ', array_fill(0, count($product_id), '?')) . '))';
					array_push($conditions, $condition);
					$arguments = array_merge($arguments, $product_id);
					array_push($types, str_repeat('i', count($product_id)));
				}
			} else {
				array_push($conditions, '(product_id = ?)');
				array_push($arguments, $product_id);
				array_push($types, 'i');
			}
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
			array_push($types, 's');
		}
		if (isset($filter['version'])) {
			$version = $filter['version'];
			if (is_string($version)) {
				$version = explode('.', $version);
			} elseif (is_numeric($version)) {
				$version = raw_to_version($version);
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
		if (isset($filter['raw_version'])) {
			$raw_version = $filter['raw_version'];
			if (is_string($raw_version)) {
				$version = explode('.', $version);
				$version = version_to_raw($version);
			} elseif (is_numeric($version)) {
				// nothing
			} elseif (is_array($version)) {
				$version = version_to_raw($version);
			} else {
				return ["Invalid argument 'raw_version'", null ];
			}
			
			array_push($conditions, '(version_raw = ?)');
			$arguments = array_push($arguments, $version);
			array_push($types, 'i');
		}
	}
	
	$query = "SELECT * FROM {$view}";
	if (count($conditions) > 0) {
		$query .= " WHERE " . implode(" AND ", $conditions);
	}
	
	$stmt = null;
	try {
		$stmt = mysqli_prepare($db, $query);
		
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
			$product = $row['product'];
			$description = $row['description'] ?: $product;
			array_push($list, [
				'product' => $product,
				'product_id' => $row['product_id'],
				'description' => $description,
				'build_id' => $row['build_id'],
				'type' => $row['type'],
				'artifact_id' => $row['artifact_id'],
				'version' => [ $row['version_major'], $row['version_minor'], $row['version_micro'] ],
				'raw_version' => $row['version_raw'],
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
		db_safe_close($stmt);
	}
	
	return ["Unknown error", null];
}



?>