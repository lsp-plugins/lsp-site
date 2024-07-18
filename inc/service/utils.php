<?php

/**
 * Return unique values of the field in structures stored in array
 * @param array $array array of structures
 * @param scalar $field name of the field of structure that is used to fetch unique values
 * @return array of unique values of the specific field of data structures stored in original array
 */
function utl_unique_field($array, $field) {
	return array_unique(
		array_map(
			function ($record) use($field) {
				return $record[$field];
			},
			$array
		));
}

/**
 * Split the array of structures into multiple arrays based on the values
 * stored in a specific field of data structures stored in array.
 * @param array $array array of structures
 * @param scalar $field name of the field of structure that is used to fetch unique values
 * @return array of unique field value associated with a sub-array of recods with the same field value.
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

?>