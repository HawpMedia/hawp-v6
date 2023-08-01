<?php
// ------------------------------------------
// This file loads the theme.
// ------------------------------------------

if (!class_exists('Hawp_Theme')):

class Hawp_Theme {

	public static $theme = [
		'name'                => 'Hawp Theme',
		'version'             => HM_THEME_VERSION,
		'file'                => __FILE__,
		'textdomain'          => 'hawp',
		'option_prefix'       => 'hawp_theme_',
		'update_url'          => 'https://update.hawp.dev/themes/hawp-v6/',
	];

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action('after_setup_theme', [$this, 'includes'], 1);
	}

	/**
	 * Include the required files if ACF Pro is active.
	 */
	public function includes() {
		if (class_exists('acf_pro')) {
			$includes = [
				'functions',
				'setup',
				'admin',
				'shortcodes',
				'utilities',
				'updater',
			];
			foreach ($includes as $include) {
				require_once HM_PATH."/core/{$include}.php";
			}
		} else {
			add_action('admin_notices', function () {
				printf(
					'<div class="error"><p>%s</p></div>',
					esc_html__('The <strong>Advanced Custom Fields Pro</strong> plugin is either deactivated or does not exist. Please install and activate it to use the active theme.', 'hawp')
				);
			});
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
	}
	return $hawp_theme;
}
hawp_theme();

endif; // class_exists check
