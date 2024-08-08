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
			];
			foreach ($includes as $include) {
				require_once HM_PATH."/core/{$include}.php";
			}
		} else {
			add_action('admin_notices', [$this, 'acf_pro_missing_notice']);
			error_log('ACF Pro is either deactivated or does not exist. The Hawp Theme requires ACF Pro to function correctly.');
		}
	}

	/**
	 * Admin notice for missing ACF Pro.
	 */
	public function acf_pro_missing_notice() {
		printf(
			'<div class="error"><p><strong>Advanced Custom Fields Pro</strong> %s <strong>%s</strong></p></div>',
			esc_attr__('plugin is deactivated or does not exist. Please install and activate it to use', 'hawp'),
			self::$theme['name'],
		);
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
