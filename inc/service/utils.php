<?php

function utl_unique_field($array, $field) {
	return array_unique(
		array_map(
			function ($record) use($field) {
				return $record[$field];
			},
			$array
		));
}

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

?>