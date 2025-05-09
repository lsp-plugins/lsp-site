<?php

/**
 * Return unique values of the field in structures stored in array
 * @param array $array array of structures
 * @param scalar $field name of the field of structure that is used to fetch unique values
 * @return array of unique values of the specific field of data structures stored in original array
 */
function utl_unique_field($array, $field) {
	return array_values(
		array_unique(
			array_map(
				function ($record) use ($field) {
					return $record[$field];
				},
				$array
			)));
}

/**
 * Split the array of structures into multiple arrays based on the values
 * stored in a specific field of data structures stored in array.
 * @param array $array array of structures
 * @param scalar $field name of the field of structure that is used to fetch unique values
 * @return array of unique field value associated with a sub-array of records with the same field value.
 */
function utl_map_by_field($array, $field) {
	$result = [];
	foreach ($array as $record) {
		$value = $record[$field];
		if (!isset($result[$value])) {
			$result[$value] = [ $record ];
		} else {
			array_push($result[$value], $record);
		}
	}
	
	return $result;
};

/**
 * Perform unique mapping of array structures by field identifier
 * @param array $array array of structures
 * @param scalar $field name of the field of structure that is used to fetch unique values
 * @return array of unique field value associated with a record
 */
function utl_map_unique_by_field($array, $field) {
	$result = [];
	foreach ($array as $record) {
		$value = $record[$field];
		$result[$value] = $record;
	}
	
	return $result;
};

/**
 * Remove elements from array if they meet specific condition.
 */
function utl_remove_if(&$array, $condition) {
	$result = [];
	foreach ($array as $key => $value) {
		$value = $array[$key];
		if (!call_user_func($condition, $value)) {
			array_push($result, $value);
		}
	}
	$array = $result;
}

/**
 * Search among the records in array for match the specific value
 * @param array $array array of structures
 * @param scalar $field name of the field to search
 * @param mixed $value the value to search
 * @return array of structure elements that match
 */
function utl_find_equal($array, $field, $value) {
	$result = [];
	foreach ($array as $record) {
		if (!array_key_exists($field, $record)) {
			continue;
		}
		
		$field_value = $record[$field];
		if ($value === $field_value) {
			array_push($result, $record);
		}
	}
	
	return $result;
}

/**
 * Search among the records in array and return the first record that matches the specific value
 * @param array $array array of structures
 * @param scalar $field name of the field to search
 * @param mixed $value the value to search
 * @return array of structure elements that match
 */
function utl_find_first($array, $field, $value) {
	foreach ($array as $record) {
		if (!array_key_exists($field, $record)) {
			continue;
		}
		
		$field_value = $record[$field];
		if ($value === $field_value) {
			return $record;
		}
	}
	
	return null;
}

/**
 * Convert version representation as array to integral version
 * @param array $version the array representation of the version
 * @return scalar the integral version representation
 */
function version_to_raw($version) {
	if (!isset($version)) {
		return null;
	}
	
	return ($version[0] * 1000 + $version[1]) * 1000 + $version[2];
}

/**
 * Convert integral version representation to array representation
 * @param scalar $version_raw the intergal version
 * @return array the array representation of version
 */
function raw_to_version($version_raw) {
	if (!isset($version_raw)) {
		return null;
	}
	
	$micro = $version_raw % 1000;
	$minor = ($version_raw / 1000) % 1000;
	$major = ($version_raw / 1000000) % 1000;
	return [$major, $minor, $micro];
}

function raw_to_version_str($version_raw) {
	return implode('.', raw_to_version($version_raw));
}

function get_post_json() {
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		return null;
	}
	
	$json = file_get_contents('php://input');
	if (!isset($json)) {
		return null;
	}
	
	error_log("Script input: " . var_export($json, true));
	
	$json = json_decode($json, true);
	if (!isset($json)) {
		return null;
	}
	
	error_log("Decoded JSON: " . var_export($json, true));
	return $json;
}

function price_to_raw($price) {
	return intval($price * 100000);
}

function raw_to_price($price) {
	return $price * 0.00001;
}

function utl_get_value($array, $key, $dfl = null) {
	$path = explode('.', $key);
	
	foreach ($path as $key) {
		if (!is_array($array)) {
			return $dfl;
		}
		if (!array_key_exists($key, $array)) {
			return $dfl;
		}
		$array = $array[$key];
	}
	
	return $array;
}

function utl_set_value(&$array, $key, $value, $overwrite = true) {
	$path = explode('.', $key);
	
	foreach ($path as $key) {
		if (!is_array($array)) {
			if (!$overwrite) {
				return;
			}
			$array = [];
		}
		if (!array_key_exists($key, $array)) {
			$array[$key] = [];
		}
		$array = &$array[$key];
	}
	
	$array = $value;
}

?>