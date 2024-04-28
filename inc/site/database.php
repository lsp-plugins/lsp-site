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

?>