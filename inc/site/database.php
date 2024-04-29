<?php
$DB_CONNECTIONS = array();

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
	$link = $DATABASES[$database];
	$db = mysqli_connect("{$link['host']}:{$link['port']}", $link['user'], $link['password']);
	if (!isset($db)) {
		return null;
	}
	
	// Setup connection parameters
	mysqli_autocommit($db, false);
	mysqli_select_db($db, $link['database']);
	mysqli_report(MYSQLI_REPORT_ALL);
	
	// Store connection
	$DB_CONNECTIONS[$database] = $db;
	return $db;
}

function unique_key_violation($obj) {
	$is_exception = $obj instanceof mysqli_sql_exception;
	$error_num = ($is_exception) ? $obj->getCode() : mysqli_errno($obj);
	if (($error_num === 1062) || ($error_num === 1586)) {
		$error_str = ($is_exception) ? $obj->getMessage() : $obj->getmysqli_error($obj);
		if (preg_match("/Duplicate entry '.*' for key '.*'/", $error_str)) {
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
	} finally {
		mysqli_stmt_close($stmt);
	}
}

?>