<?php
class NPSettings {
	
	const REST_URL = 'api_url';
	const EMANDATE_WEB_URL = 'emandate_url';
	const PLUGIN_VER = 'NUAPAY_PLUGIN_VERSION';
	
	public static function updatePluginVersion($value) {
		update_option(self::PLUGIN_VER, $value);
	}
	
	public static function getPluginVersion() {
		return get_option(self::PLUGIN_VER, '0');
	}
	
	public static function updateRestUrl($value) {
		$options = get_option('np_form_options');
		$options[self::REST_URL] = $value; 
		update_option('np_form_options', $options);
	}
	
	public static function getRestUrl() {
		$options = get_option('np_form_options');
		return $options[self::REST_URL];
	}
	
	public static function updateEmandateWebUrl($value) {
		$options = get_option('np_form_options');
		$options[self::EMANDATE_WEB_URL] = $value;
		update_option('np_form_options', $options);
	}
	
	public static function getEmandateWebUrl() {
		$options = get_option('np_form_options');
		return $options[self::EMANDATE_WEB_URL];
	}
}