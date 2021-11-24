<?php
// ------------------------------------------
// Get the theme rolling!
// ------------------------------------------

define('HM_PATH', get_template_directory());
define('HM_URL', get_template_directory_uri());
define('HMC_PATH', get_stylesheet_directory());
define('HMC_URL', get_stylesheet_directory_uri());

if (!class_exists('Hawp_Theme')):

class Hawp_Theme {

	public static $theme = array(
		'name'                => 'Hawp Theme',
		'version'             => '5.7.1',
		'file'                => __FILE__,
		'textdomain'          => 'hawp',
		'option_prefix'       => 'hawp_theme_',
		'update_url'          => 'https://update.hawp.dev/theme/hawpv6/',
	);

	/**
	 * Constructor.
	 */
	public function setup() {
		add_action('after_setup_theme', array($this, 'includes'), 1);
	}

	/**
	 * Includes.
	 */
	public function includes() {

		// Return null if ACF Pro is not active.
		if (!class_exists('acf')) {
			add_action('admin_notices', function() {
				if (!class_exists('acf')) {
					printf('<div class="error"><p>'.__('<strong>Advanced Custom Fields Pro</strong> is deactivated or does not exist. Please install and activate it to use the active theme', 'hawp').'</p></div>', PHP_VERSION);
				}
			});
			return null;
		}

		// Loop through core folder and auto include files.
		$includes = [
			'functions',
			'setup',
			'helpers',
			'admin',
			'shortcodes',
			'utilities',
			'updater',
		];
		foreach ($includes as $include) {
			require_once "core/{$include}.php";
		}
	}

}

/**
 * Initialize Hawp_Theme class with a function.
 */
function hawp_theme() {
	global $hawp_theme;

	// Instantiate only once.
	if (!isset($hawp_theme)) {
		$hawp_theme = new Hawp_Theme();
		$hawp_theme->setup();
	}
	return $hawp_theme;
}
hawp_theme();

endif; // class_exists check
