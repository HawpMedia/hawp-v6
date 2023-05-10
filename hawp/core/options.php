<?php
// ------------------------------------------
// Theme options functions.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!class_exists('Hawp_Theme_Options')):

class Hawp_Theme_Options {

	/**
	 * Constructor.
	 */
	public function setup() {

	}
}

/**
 * Initialize Hawp_Theme_Options class with a function.
 */
function hawp_theme_options() {
	global $hawp_theme_options;

	// Instantiate only once.
	if (!isset($hawp_theme_options)) {
		$hawp_theme_options = new Hawp_Theme_Options();
		$hawp_theme_options->setup();
	}
	return $hawp_theme_options;
}
hawp_theme_options();

endif; // class_exists check
