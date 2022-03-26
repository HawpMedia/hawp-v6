<?php
// ------------------------------------------
// Theme updater functions.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!class_exists('Hawp_Theme_Updater')):

class Hawp_Theme_Updater {

	/**
	 * Constructor.
	 */
	public function setup() {
		if (is_admin()) {
			if (get_theme_update_url() != '') {
				add_filter('pre_set_site_transient_update_themes', [$this, 'update_theme_check']);
			}
		}
	}

	/**
	 * Theme update checker.
	 */
	public function update_theme_check($transient) {
		if (empty($transient->checked['hawp'])) {
			return $transient;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, get_theme_update_url());
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);

		if (empty($result)) {
			return $transient;
		}

		if ($data = json_decode($result)) {
			if (version_compare($transient->checked['hawp'], $data->new_version, '<')) {
				$transient->response['hawp'] = (array) $data;
			}
		}

		return $transient;
	}
}

/**
 * Initialize Hawp_Theme_Updater class with a function.
 */
function hawp_theme_updater() {
	global $hawp_theme_updater;

	// Instantiate only once.
	if (!isset($hawp_theme_updater)) {
		$hawp_theme_updater = new Hawp_Theme_Updater();
		$hawp_theme_updater->setup();
	}
	return $hawp_theme_updater;
}
hawp_theme_updater();

endif; // class_exists check
