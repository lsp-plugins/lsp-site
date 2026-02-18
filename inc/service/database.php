<?php
$DB_CONNECTIONS = array();

function db_log_query($query, $types, $values) {
	error_log("query = {$query}, types = {$types}, values = " . var_export($values, true));
}

function db_log_exception($e) {
	$message = "SQL exception: {$e->getMessage()}\n {$e->getTraceAsString()}";
	error_log($message);
	return $message;
}

function db_safe_disconnect($db) {
	if (!isset($db)) {
		return;
	}
	
	try {
		mysqli_close($db);
	} catch (Exception $e) {
		/* nothing */
	} finally {
		$db = null;
	}
}

function db_safe_close(&$stmt) {
	if (!isset($stmt)) {
		return;
	}
	
	try {
		mysqli_stmt_close($stmt);
	} catch (Exception $e) {
		/* nothing */
	} finally {
		$stmt = null;
	}
}

function db_safe_rollback($db) {
	if (!isset($db)) {
		return;
	}
	
	try {
		mysqli_rollback($db);
	} catch (Exception $e) {
		/* nothing */
	}
}

function db_current_timestamp($delta = null) {
	$curr_date = date("Y-m-d H:i:s");
	if (!isset($delta)) {
		return $curr_date;
	}
	
	return date(
		"Y-m-d H:i:s",
		strtotime($delta, strtotime($curr_date)));
}

function db_unix_timestamp($timestamp, $delta = null) {
	$curr_date = date("Y-m-d H:i:s", $timestamp);
	if (!isset($delta)) {
		return $curr_date;
	}
		
	return date(
		"Y-m-d H:i:s",
		strtotime($delta, strtotime($curr_date)));
}

function db_add_time_interval($timestamp, $delta) {
	return date(
		"Y-m-d H:i:s",
		strtotime($delta, strtotime($timestamp))
	);
}

function db_strtotime($timestamp) {
	return strtotime($timestamp);
}

function connect_db($database)
{
	global $DATABASES, $DB_CONNECTIONS;
	
	// Check if we already have cached connection
	// TODO: maybe check the persistent connection option?
	if (isset($DB_CONNECTIONS[$database])) {
		$db = $DB_CONNECTIONS[$database];
		$server_info = mysqli_get_server_info($db);
		if (isset($server_info)) {
			return $DB_CONNECTIONS[$database];
		}
	}
	
	// Check that we have connection settings
	if ((!isset($DATABASES)) || (!isset($DATABASES[$database]))) {
		return null;
	}
	
	// Connect to the database
	try {
		$link = $DATABASES[$database];
		$db = mysqli_connect("{$link['host']}:{$link['port']}", $link['user'], $link['password']);
		if (!isset($db)) {
			return null;
		}
		
		// Setup connection parameters
		mysqli_autocommit($db, false);
		mysqli_select_db($db, $link['database']);
		mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
		return null;
	}
	
	// Store connection
	$DB_CONNECTIONS[$database] = $db;
	return $db;
}

function unique_key_violation($obj, $key = null) {
	$is_exception = $obj instanceof mysqli_sql_exception;
	$error_num = ($is_exception) ? $obj->getCode() : mysqli_errno($obj);
	if (!isset($key)) {
		$key = '.*';
	}
	
	if (($error_num === 1062) || ($error_num === 1586)) {
		$error_str = ($is_exception) ? $obj->getMessage() : $obj->getmysqli_error($obj);
		if (preg_match("/Duplicate entry '.*' for key '{$key}'/", $error_str)) {
			return true;
		}
	} 
	return false;
}

function foreign_key_violation($obj) {
	$is_exception = $obj instanceof mysqli_sql_exception;
	$error_num = ($is_exception) ? $obj->getCode() : mysqli_errno($obj);
	if ($error_num === 1452) {
		$error_str = ($is_exception) ? $obj->getMessage() : $obj->getmysqli_error($obj);
		if (preg_match("/a foreign key constraint fails/", $error_str)) {
			return true;
		}
	}
	return false;
}

function id_from_dict($db, $table, $key) {
	$stmt = mysqli_prepare($db, "SELECT id FROM {$table} WHERE name=?");
	try {
		mysqli_stmt_bind_param($stmt, 's', $key);
		if (!mysqli_stmt_execute($stmt))
			return null;
		
		$result = mysqli_stmt_get_result($stmt);
		if (!isset($result))
			return null;
		
		$row = mysqli_fetch_array($result);
		if ((!isset($row)) || (!isset($row['id'])))
			return null;
		
		return $row['id'];
	} catch (mysqli_sql_exception $e) {
		db_log_exception($e);
	} finally {
		mysqli_stmt_close($stmt);
	}
	
	return null;
}

?>