#!/usr/bin/php
<?php

$full_path = getcwd() . '/' . $argv[0];
$cwd = dirname($full_path, 3);
chdir($cwd);

echo "CWD = $cwd\n";

require_once('./config/config.php');
require_once('./inc/site/artifacts.php');

$files = [];
$options = [];

for ($i=1; $i<count($argv); ++$i) {
	$arg = $argv[$i];
	$match = [];
	if (preg_match('/^--([a-zA-Z0-9\\-]+)(?:=(.*))?$/', $arg, $match)) {
		[$expr, $param, $value] = $match;
		if (array_key_exists($param, $options)) {
			echo "Duplicate option: {$arg}\n";
			exit(1);
		}
		$options[$param] = $value;
	} else {
		array_push($files, $arg);
	}
}

if (!isset($options['type'])) {
	echo "Build type should be specified\n";
	exit(2);
}

$errors = create_artifacts($files, $options['type']);

if (isset($errors)) {
	foreach ($errors as $error) {
		echo "$error\n";
	}
}


?>