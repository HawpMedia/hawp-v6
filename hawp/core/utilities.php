<?php
// ------------------------------------------
// Theme utility functions.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!class_exists('Hawp_Theme_Updater')):

class Hawp_Theme_Utilities {

	/**
	 * Constructor.
	 */
	public function setup() {
		add_action('acf/init', [$this, 'acf_option_commands']);
	}

	/**
	 * Add the acf option commands.
	 */
	public function acf_option_commands() {

		// Force SSL
		if (get_theme_option('force_ssl')) {
			add_action('template_redirect', [$this, 'force_ssl']);
		}

		// Prefix post urls
		if (get_theme_option('prefix_post_urls')) {
			add_action('init', [$this, 'create_new_post_url_querystring'], 999);
			add_filter('post_link', [$this, 'append_post_query_string'], 10, 3);
			add_filter('template_redirect', [$this, 'redirect_old_post_urls']);
		}

		// Allow SVG Upload
		if (get_theme_option('allow_svg_upload')) {
			if (current_user_can('upload_files')) {
				add_filter('upload_mimes', [$this, 'allow_svg_upload']);
			}
		}

		// Remove WP admin items
		if (get_theme_option('wordpress_admin_item') == 0 && current_user_can('manage_options')) {
			add_action('admin_bar_menu', [$this, 'remove_wp_logo'], 999);
			add_action('wp_dashboard_setup', [$this, 'disable_default_dashboard_widgets'], 999); // Dashboard widgets
			add_filter('admin_head', [$this, 'disable_help_tabs']); // Help tab
			add_filter('admin_footer_text', '__return_false'); // Admin footer
		}

		// Remove comments menu item
		if (get_theme_option('comments_admin_menu_item') == 0) {
			add_action('admin_menu', [$this, 'remove_comments_menu_item']);
			add_action('init', [$this, 'remove_comment_support'], 100);
			add_action('wp_before_admin_bar_render', [$this, 'admin_bar_render']); // Removes from admin bar
		}

		// Custom login logo
		if (get_theme_option('login_logo')) {
			add_action('login_head', [$this, 'custom_login_logo']);
			add_filter('login_headerurl', [$this, 'custom_login_logo_url']);
		}
	}

	/**
	 * Force SSL for sites that are set to SSL.
	 */
	public function force_ssl() {
		if (!is_ssl() && stripos(home_url('/'), 'https://') === 0) {
			wp_redirect('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 301);
			exit();
		}
	}

	/**
	 * Add new post rewrite rule
	 */
	public function create_new_post_url_querystring() {
		add_rewrite_rule(
			'blog/([^/]*)$',
			'index.php?name=$matches[1]',
			'top'
		);

		add_rewrite_tag('%blog%','([^/]*)');
	}

	/**
	 * Modify post link
	 * This will print /blog/post-name instead of /post-name
	 */
	public function append_post_query_string( $url, $post, $leavename ) {
		if ($post->post_type != 'post') {
			return $url;
		}

		if (strpos($url, '%postname%') !== false) {
			$slug = '%postname%';
		} elseif ($post->post_name) {
			$slug = $post->post_name;
		} else {
			$slug = sanitize_title($post->post_title);
		}

		$url = home_url(user_trailingslashit('blog/'. $slug));

		return $url;
	}

	/**
	 * Redirect all posts to new url
	 * If you get error 'Too many redirects' or 'Redirect loop', then delete everything below
	 */
	public function redirect_old_post_urls() {
		if (is_singular('post')) {
			global $post;

			if (strpos($_SERVER['REQUEST_URI'], '/blog/') === false) {
			   wp_redirect(home_url(user_trailingslashit("blog/$post->post_name")), 301);
			   exit();
			}
		}
	}

	/**
	 * Allow SVG uploads in media library.
	 */
	public function allow_svg_upload($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	/**
	 * Remove WordPress admin logo.
	 */
	public function remove_wp_logo($wp_admin_bar) {
		$wp_admin_bar->remove_node('wp-logo');
	}

	/**
	 * Remove comment menu item and theme support.
	 */
	public function admin_bar_render() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('comments');
	}
	public function remove_comments_menu_item() {
		remove_menu_page('edit-comments.php');
	}
	public function remove_comment_support() {
		remove_post_type_support('post', 'comments');
		remove_post_type_support('page', 'comments');
	}

	/**
	 * Disable default dashboard widgets.
	 */
	public function disable_default_dashboard_widgets() {
		global $wp_meta_boxes;

		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']); // WP news & events
		remove_action('welcome_panel', 'wp_welcome_panel');
	}

	/**
	 * Disable help tabs.
	 */
	public function disable_help_tabs() {
		$screen = get_current_screen();
		$screen->remove_help_tabs();
	}

	/**
	 * Custom login logo.
	 */
	public function custom_login_logo() {
		$logo = wp_get_attachment_image_url(get_theme_option('login_logo'), 'full');

		$result = '<style>h1 a { background-image: url('.esc_url($logo).') !important; }</style>';

		echo $result;
	}

	/**
	 * Custom login logo url.
	 */
	function custom_login_logo_url() {
		return get_home_url();
	}

}

/**
 * Initialize Hawp_Theme_Utilities class with a function.
 */
function hawp_theme_utilities() {
	global $hawp_theme_utilities;

	// Instantiate only once.
	if (!isset($hawp_theme_utilities)) {
		$hawp_theme_utilities = new Hawp_Theme_Utilities();
		$hawp_theme_utilities->setup();
	}
	return $hawp_theme_utilities;
}
hawp_theme_utilities();

endif; // class_exists check
