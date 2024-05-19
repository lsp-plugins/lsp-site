<?php

require_once("./inc/dao/artifacts.php");

function create_artifacts($file_names, $build_type) {
	$platform_mapping = [
		'BSD' => 'freebsd',
		'FreeBSD' => 'freebsd',
		'Linux' => 'linux',
		'MacOS' => 'macos',
		'Windows' => 'windows'
	];
	
	$architecture_mapping = [
		'aarch64' => 'aarch64',
		'arm32' => 'armv7a',
		'i586' => 'x86',
		'riscv64' => 'riscv64',
		'x86_64' => 'x86_64'
	];
	
	$warnings = [];
	
	$archive_pattern='(?:tar\\.(?:gz|bz)|zip|7z)';
	$product_pattern='([a-z0-9\\-]+)';
	$version_pattern='(\\d+)\\.(\d+)\\.(\\d+)';
	$fmt_pattern='([a-z0-9]+)';
	$platform_pattern='([a-zA-Z0-9]+)';
	$architecture_pattern='([a-z0-9_]+)';
	
	$db = null;
	try {
		$db = connect_db('store_admin');
		if (!isset($db)) {
			return null;
		}
	
		foreach ($file_names as $file) {
			$matches = [];
			$result = [];
			
			if (preg_match("/^{$product_pattern}-src-{$version_pattern}\\.{$archive_pattern}$/", $file, $matches)) {
				// Source code
				[$text, $product, $major, $minor, $micro] = $matches;
				
				$result = dao_create_artifact($db,
					$product, $build_type, 'src',
					[$major, $minor, $micro],
					'any', 'noarch', $file);
				
			} elseif (preg_match("/^{$product_pattern}-doc-{$version_pattern}\\.{$archive_pattern}$/", $file, $matches)) {
				// Documentation
				[$text, $product, $major, $minor, $micro] = $matches;
				
				$result = dao_create_artifact($db,
					$product, $build_type, 'doc',
					[$major, $minor, $micro],
					'any', 'noarch', $file);
				
			} elseif (preg_match("/^{$product_pattern}-{$fmt_pattern}-{$version_pattern}-{$platform_pattern}-{$architecture_pattern}\\.{$archive_pattern}$/", $file, $matches)) {
				// Binary build
				[$text, $product, $format, $major, $minor, $micro, $platform, $archtecture] = $matches;
				if (!array_key_exists($platform, $platform_mapping)) {
					array_push($warnings, "Unknown platform '{$platform}' for artifact '{$file}'");
					continue;
				}
				$platform = $platform_mapping[$platform];
				
				if (!array_key_exists($archtecture, $architecture_mapping)) {
					array_push($warnings, "Unknown architecture '{$archtecture}' for artifact '{$file}'");
					continue;
				}
				$architecture = $architecture_mapping[$archtecture];
				
				$result = dao_create_artifact($db,
					$product, $build_type, $format,
					[$major, $minor, $micro],
					$platform, $architecture, $file);
				
			} else {
				$result = [ "Could not parse artifact", null ];
			}
			
			[$error] = $result;
			if (isset($error)) {
				$error = "Error for '{$file}': {$error}";
				array_push($warnings, $error);
			}
		}
		
		if (count($warnings) <= 0) {
			mysqli_commit($db);
		}
	
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		array_push($warnings, $error);
	} finally {
		db_safe_rollback($db);
	}
	
	return (count($warnings) > 0) ? $warnings : null;
}

function get_artifacts($filter) {
	$db = null;
	$result = null;
	
	try {
		$db = connect_db('store');
		if (!isset($db)) {
			return ["Connection error", null];
		}
		
		$result = dao_get_artifacts($db, $filter);
				
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, ''];
	} finally {
		db_safe_rollback($db);
	}
	
	return $result;
}

function get_latest_free_releases() {
	global $PACKAGE;
	
	return get_artifacts([
		'product' => 'lsp-plugins',
		'version' => $PACKAGE['version']
	]);
}


?>