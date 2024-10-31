<?php

function np_plugin_update($fromVersion, $toVersion) {

	$versions = array(
		'0' => 0,
		'1' => 1,  
		'1.0.1' => 2,
		'1.0.2' => 3,
		'1.0.3' => 4,
		'1.0.4' => 5,
		'1.0.5' => 6,
		'1.0.6' => 7
	);

	write_log('Upgrading version ' . $fromVersion . ' to version ' . $toVersion);

	if (!array_key_exists($fromVersion, $versions)) {
		write_log('Cannot find fromVersion ' . $fromVersion . ' in listed versions');
		return;
	}
	
	if (!array_key_exists($toVersion, $versions)) {
		write_log('Cannot find toVersion ' . $toVersion . ' in listed versions');
		return;
	}

	$fromIndex = $versions[$fromVersion];
	$toIndex = $versions[$toVersion];

	if ($fromIndex >= $toIndex) {
		write_log('From version already up to date with to-version, skipping further checks');
		return;
	}
	
	if ($toIndex >= 4) {
		write_log('Upgrading API URL for REST endpoints');
		// from version 1.0.3 onwards REST endpoint is used instead of old style url form encoded endpoint
		NPSettings::updateRestUrl('https://api.nuapay.com/v1/emandates');
	}
	
	if ($toIndex >= 5) {
		
		write_log('Upgrading REST API URL');
		NPSettings::updateRestUrl('https://api.nuapay.com/emandate-rest');
		
		write_log('Upgrading UI URL');
		NPSettings::updateEmandateWebUrl('https://api.nuapay.com/emandate/');
	}

	NPSettings::updatePluginVersion($toVersion);
}
