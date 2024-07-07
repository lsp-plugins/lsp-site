<?php

function browser_info() {
	
	$windows_platforms = [
		"Win10",
		"Win16",
		"Win2000",
		"Win31",
		"Win32",
		"Win64",
		"Win7",
		"Win8",
		"Win8.1",
		"Win95",
		"Win98",
		"WinCE",
		"WinME",
		"WinMobile",
		"WinNT",
		"WinPhone",
		"WinPhone10",
		"WinPhone6",
		"WinPhone7",
		"WinPhone7.10",
		"WinPhone7.5",
		"WinPhone7.8",
		"WinPhone8",
		"WinPhone8.1",
		"WinRT8",
		"WinRT8.1",
		"WinVista",
		"WinXP"
	];
	
	$linux_platforms = [
		"CentOS",
		"Debian",
		"Fedora",
		"FirefoxOS",
		"KaiOS",
		"Linux",
		"Mobilinux",
		"OpenBSD",
		"Red Hat",
		"Ubuntu",
		"Ubuntu Touch",
		"Xubuntu"
	];
	
	$bsd_platforms = [
		"BSD",
		"DragonFly BSD",
		"FreeBSD",
		"NetBSD",
		"OpenBSD"
	];
	
	$apple_platforms = [
		"ATV OS X",
		"Brew",
		"Brew MP",
		"Darwin",
		"iOS",
		"ipadOS",
		"macOS",
		"MacOSX",
		"MacPPC",
	];
	
	$platform_family = null;
	$browser = get_browser(null, true);
	if (!isset($browser)) {
		return null;
	}
	
	$platform = $browser['platform'];
	if (isset($platform)) {
		if (in_array($platform, $windows_platforms)) {
			$platform_family = 'windows';
		} elseif (in_array($platform, $linux_platforms)) {
			$platform_family = 'linux';
		} elseif (in_array($platform, $bsd_platforms)) {
			$platform_family = 'freebsd';
		} elseif (in_array($platform, $apple_platforms)) {
			$platform_family = 'macos';
		}
		
		if (!isset($platform_family)) {
			if (preg_match("/Win/", $platform)) {
				$platform_family = 'windows';
			} elseif (preg_match("/BSD/i", $platform)) {
				$platform_family = 'freebsd';
			} elseif (preg_match("/MacOS/i", $platform)) {
				$platform_family = 'macos';
			}
		}
	}
	
	if (!isset($platform_family)) {
		if ((isset($platform['win16']) && $platform['win16']) ||
			(isset($platform['win32']) && $platform['win32']) ||
			(isset($platform['win64']) && $platform['win64'])) {
			$platform_family = 'windows';
		}
	}
	
	$browser['platform_family'] = $platform_family;
	
	return $browser;
}

?>