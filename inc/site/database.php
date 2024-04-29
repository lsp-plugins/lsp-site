<?php
	$DB_CONNECTIONS = array();

	function connect_db($database)
	{
		global $DATABASES, $DB_CONNECTIONS;
		
		// Check if we already have cached connection
		// TODO: maybe check the persistent connection option?
		if (isset($DB_CONNECTIONS[$database])) {
			$mysql = $DB_CONNECTIONS[$database];
			if (is_resource($mysql)) {
				return $DB_CONNECTIONS[$database];
			}
		}
		
		// Check that we have connection settings
		if ((!isset($DATABASES)) || (!isset($DATABASES[$database]))) {
			return null;
		}
		
		// Connect to the database
		$link = $DATABASES[$database];
		$mysql = mysqli_connect("{$link['host']}:{$link['port']}", $link['user'], $link['password']);
		if (!isset($mysql)) {
			return null;
		}
		
		// Setup connection parameters
		mysqli_autocommit($mysql, false);
		mysqli_select_db($mysql, $link['database']);
		
		// Store connection
		$DB_CONNECTIONS[$database] = $mysql;
		return $mysql;
	}
	
	function unique_key_violation($db) {
		$error_num = mysqli_errno($db);
		if (($error_num === 1062) || ($error_num === 1586)) {
			$error_str = mysqli_error($db);
			if (preg_match("/Duplicate entry '.*' for key '.*'/", $error_str)) {
				return true;
			}
		} 
		return false;
	}
	
	function foreign_key_violation($db) {
		$error_num = mysqli_errno($db);
		if ($error_num === 1452) {
			$error_str = mysqli_error($db);
			if (preg_match("/a foreign key constraint fails/", $error_str)) {
				return true;
			}
		}
		return false;
	}

?>