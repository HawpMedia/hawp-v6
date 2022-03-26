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

		// Filter Dynamic URL's
		if (get_theme_option('force_dynamic_urls') && is_admin()) {
			add_filter('content_save_pre', [$this, 'filter_dynamic_urls'], 999);
			add_filter('content_edit_pre', [$this, 'unfilter_dynamic_urls'], 1);
			add_filter('the_content', [$this, 'unfilter_dynamic_urls'], 1);
			add_filter('rest_request_after_callbacks', [$this, 'rest_unfilter_dynamic_urls'], 10);
		}

		// Force SSL
		if (get_theme_option('force_ssl')) {
			add_action('template_redirect', [$this, 'force_ssl']);
		}

		// Allow SVG Upload
		if (get_theme_option('allow_svg_upload')) {
			if (current_user_can('upload_files')) {
				add_filter('upload_mimes', [$this, 'allow_svg_upload']);
			}
		}

		// Remove WP admin items
		if (get_theme_option('wordpress_admin_item') == 0 && is_admin()) {
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
	}

	/**
	 * Filter dynamic urls.
	 */
	public function filter_dynamic_urls($content) {
		preg_match_all('/'.preg_quote(wp_get_upload_dir()['baseurl'],'/').'[^#?"\'\\\,\s()<>]+/', $content, $matches);
		foreach($matches[0] as $match) {
			$attachment = attachment_url_to_postid($match);
			if (!empty($attachment)) {
				$pos = strpos($content, $match);
				if ($pos!==false) {
					$content = substr_replace($content, '[uploads id='.$attachment.']', $pos, strlen($match));
				}
			}
		}
		preg_match_all('/'.preg_quote(home_url(),'/').'[^#?"\'\\\,\s()<>]+/', $content, $matches);
		foreach($matches[0] as $match) {
			$postid = url_to_postid($match);
			if (!empty($postid) && $match!=home_url('/')) {
				$pos = strpos($content, $match);
				if ($pos!==false) {
					$content = substr_replace($content, '[home_url id='.$postid.']', $pos, strlen($match));
				}
			}
		}
		$content = str_replace(wp_get_upload_dir()['baseurl'], '[uploads]', $content);
		$content = str_replace(home_url(), '[home_url]', $content);
		return $content;
	}

	/**
	 * Unfilter dynamic urls.
	 */
	public function unfilter_dynamic_urls($content) {
		$content = str_replace(['[home_url]', '[home]'], home_url(), $content);
		$content = str_replace('[uploads]', wp_get_upload_dir()['baseurl'], $content);
		preg_match_all('/\[uploads[^\]]*\]/i', $content, $matches);
		foreach($matches[0] as $match) {
			$pos = strpos($content, $match);
			if ($pos!==false) {
				$content = substr_replace($content, do_shortcode($match), $pos, strlen($match));
			}
		}
		preg_match_all('/\[home_url[^\]]*\]/i', $content, $matches);
		foreach($matches[0] as $match) {
			$pos = strpos($content, $match);
			if ($pos!==false) {
				$content = substr_replace($content, do_shortcode($match), $pos, strlen($match));
			}
		}
		return $content;
	}

	/**
	 * Unfilter dynamic urls in the REST API.
	 */
	public function rest_unfilter_dynamic_urls($result) {
		if (!empty($result->data['content']['raw'])) {
			$result->data['content']['raw'] = $this->unfilter_dynamic_urls($result->data['content']['raw']);
		}
		return $result;
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
