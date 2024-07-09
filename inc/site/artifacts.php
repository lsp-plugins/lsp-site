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
		'i686' => 'x86',
		'riscv32' => 'riscv32',
		'riscv64' => 'riscv64',
		'x86_64' => 'x86_64'
	];
	
	$warnings = [];
	
	$archive_pattern='(?:tar\\.(?:gz|bz)|zip|7z|exe)';
	$product_pattern='([a-z0-9\\-]+)';
	$version_pattern='(\\d+)\\.(\d+)\\.(\\d+)';
	$fmt_pattern='(clap|gst|jack|ladspa|lv2|vst2|vst3)';
	$platform_pattern='([a-zA-Z0-9]+)';
	$architecture_pattern='([a-z0-9_]+)';
	
	$db = null;
	try {
		$db = connect_db('store_admin');
		if (!isset($db)) {
			return null;
		}
	
		foreach ($file_names as $file) {
			$basename = basename($file);
			$matches = [];
			$result = [];
			
			if (preg_match("/^{$product_pattern}-src-{$version_pattern}\\.{$archive_pattern}$/", $basename, $matches)) {
				// Source code
				[$text, $product, $major, $minor, $micro] = $matches;
				
				$result = dao_create_artifact($db,
					$product, $build_type, 'src',
					[$major, $minor, $micro],
					'any', 'noarch', $basename);
				
			} elseif (preg_match("/^{$product_pattern}-doc-{$version_pattern}\\.{$archive_pattern}$/", $basename, $matches)) {
				// Documentation
				[$text, $product, $major, $minor, $micro] = $matches;
				
				$result = dao_create_artifact($db,
					$product, $build_type, 'doc',
					[$major, $minor, $micro],
					'any', 'noarch', $basename);
				
			} elseif (preg_match("/^{$product_pattern}-{$fmt_pattern}-{$version_pattern}-{$platform_pattern}-{$architecture_pattern}\\.{$archive_pattern}$/", $basename, $matches)) {
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
					$platform, $architecture, $basename);
				
			} elseif (preg_match("/^{$product_pattern}-{$version_pattern}-{$platform_pattern}-{$architecture_pattern}\\.{$archive_pattern}$/", $basename, $matches)) {
				// Binary build
				[$text, $product, $major, $minor, $micro, $platform, $archtecture] = $matches;
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
					$product, $build_type, 'multi',
					[$major, $minor, $micro],
					$platform, $architecture, $basename);
				
			} else {
				$result = [ "Could not parse artifact {$file}", null ];
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

function get_filtered_artifacts($view, $filter) {
	$db = null;
	$result = null;
	
	try {
		$db = connect_db('store');
		if (!isset($db)) {
			return ["Connection error", null];
		}
		
		$result = dao_get_artifacts($db, (isset($view)) ? $view : 'v_artifacts', $filter);
				
	} catch (mysqli_sql_exception $e) {
		$error = db_log_exception($e);
		return [$error, ''];
	} finally {
		db_safe_rollback($db);
	}
	
	return $result;
}

function get_latest_releases() {
	return get_filtered_artifacts('v_latest_artifacts', []);
}

function get_product_releases($product_id) {
	return get_filtered_artifacts(
		'v_artifacts',
		[
			'product_id' => $product_id
		]);
}

function get_lastest_documentation_build() {
	return get_filtered_artifacts(
		'v_latest_artifacts',
		[
			'format' => 'doc'
		]);
}

?>