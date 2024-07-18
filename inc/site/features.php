<?php

function check_site_feature($name) {
	global $SITE_FEATURES;
	
	return isset($SITE_FEATURES[$name]) && ($SITE_FEATURES[$name]);
}

?>