<?php
// ------------------------------------------
// Theme setup functions.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!class_exists('Hawp_Theme_Setup')):

class Hawp_Theme_Setup {

	/**
	 * Constructor.
	 */
	public function setup() {
		add_action('after_setup_theme', array($this, 'theme_setup'));
		add_action('after_setup_theme', array($this, 'child_theme_setup'));
		add_action('widgets_init', array($this, 'widgets_init'));
		add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
		add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_child_scripts'));
		add_action('wp_head', array($this, 'add_head_code'));
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		add_action('wp_body_open', array($this, 'add_body_code'));
		add_action('wp_footer', array($this, 'add_footer_code'));
		add_action('wp_default_scripts', array($this, 'enqueue_jquery_migrate'));
		remove_action('wp_print_styles', 'print_emoji_styles');
		add_action('wp_print_styles', array($this, 'enqueue_gutenberg_style'), 100);
		add_filter('excerpt_more', array($this, 'excerpt_more'));
		add_filter('get_the_archive_title', array($this, 'remove_archive_title'));
		add_action('pre_get_posts', array($this, 'num_posts_per_page'));
		add_filter('gform_ajax_spinner_url', array($this, 'gform_ajax_spinner_url'), 10, 2);
		add_filter('gform_submit_button', array($this, 'gform_submit_button'), 10, 5);
		add_filter('widget_text', 'do_shortcode');
		add_filter('gform_tabindex', '__return_false');
		add_filter('wp_nav_menu_items', 'do_shortcode');
		add_filter('wpseo_json_ld_output', '__return_empty_array');
	}

	/**
	 * Set up theme.
	 */
	public function theme_setup() {
		load_theme_textdomain('hawp');
		add_theme_support('title-tag');
		add_theme_support('automatic-feed-links');
		add_theme_support('post-thumbnails');
		add_theme_support('html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', ));
		add_post_type_support('page', 'excerpt');
		register_nav_menus(apply_filters('hawp_menus', array(
			'primary' => 'Primary Menu',
			'top' => 'Top Menu',
			'secondary' => 'Footer Menu',
			'social' => 'Social Menu',
			'copyright' => 'Copyright Menu',
		)));
	}

	/**
	 * Set up child theme presets.
	 */
	public function child_theme_setup() {
		// Child base editor styles
		add_editor_style(get_stylesheet_directory_uri().'/css/admin/style-editor.css');
		add_editor_style(get_stylesheet_directory_uri().'/assets/css/admin/style-editor.css');

		// Child custom editor styles
		if (file_exists(get_stylesheet_directory().'/css/admin/style-editor-custom.css')) {
			add_editor_style(get_stylesheet_directory_uri().'/css/admin/style-editor-custom.css');
		}
		if (file_exists(get_stylesheet_directory().'/assets/css/admin/style-editor-custom.css')) {
			add_editor_style(get_stylesheet_directory_uri().'/assets/css/admin/style-editor-custom.css');
		}
	}

	/**
	 * Set up theme widgets.
	 */
	public function widgets_init() {
		register_sidebar(array(
			'name' => 'Footer',
			'id' => 'footer',
			'description' => '',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<header><h3 class="widget-title">',
			'after_title' => '</h3></header>',
		));
		register_sidebar(array(
			'name' => 'Site Sidebar',
			'id' => 'site',
			'description' => '',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<header><h3 class="widget-title">',
			'after_title' => '</h3></header>',
		));
		register_sidebar(array(
			'name' => 'Blog Sidebar',
			'id' => 'blog',
			'description' => '',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<header><h3 class="widget-title">',
			'after_title' => '</h3></header>',
		));
	}

	/**
	 * Scripts and styles.
	 */
	public function wp_enqueue_scripts() {
		$js_deps = array('jquery');
		$css_deps = array();

		// Google fonts
		if (get_theme_option('google_fonts')) {
			wp_enqueue_style('google-fonts', esc_html(get_theme_option('google_fonts')), $css_deps);
			$css_deps[] = 'google-fonts';
		}

		// Lity styles & scripts
		if (get_theme_option('enqueue_lity_styles_scripts')) {
			wp_register_style('hm-lity-style', get_template_directory_uri().'/assets/lib/lity/2.4.0/lity.min.css', $css_deps);
			wp_enqueue_style('hm-lity-style');
			$css_deps[] = 'hm-lity-style';

			wp_register_script('hm-lity-script', get_template_directory_uri().'/assets/lib/lity/2.4.0/lity.min.js', $js_deps);
			wp_enqueue_script('hm-lity-script');
			$js_deps[] = 'hm-lity-script';
		}

		// Owl styles & scripts
		if (get_theme_option('enqueue_owl_styles_scripts')) {
			wp_register_style('hm-owl-style', get_template_directory_uri().'/assets/lib/owl/2.3.4/owl.carousel.min.css', $css_deps);
			wp_register_style('hm-owl-theme-style', get_template_directory_uri().'/assets/lib/owl/2.3.4/owl.theme.default.min.css', $css_deps);
			wp_enqueue_style('hm-owl-style');
			wp_enqueue_style('hm-owl-theme-style');
			$css_deps[] = 'hm-owl-style';

			wp_register_script('hm-owl-script', get_template_directory_uri().'/assets/lib/owl/2.3.4/owl.carousel.min.js', $js_deps);
			wp_enqueue_script('hm-owl-script');
			$js_deps[] = 'hm-owl-script';
		}

		// Litepicker styles & scripts
		if (get_theme_option('enqueue_litepicker_styles_scripts')) {
			wp_enqueue_script('hm-litepicker-script', 'https://cdnjs.cloudflare.com/ajax/lib/litepicker/2.0.11/litepicker.js');
		}

		// Mixitup styles & scripts
		if (get_theme_option('enqueue_mixitup_styles_scripts')) {
			wp_enqueue_script('hm-mixitup-script', get_template_directory_uri().'/assets/lib/mixitup/3.3.1/mixitup.min.js', [], false, true);
		}

		// Select2 styles & scripts
		if (get_theme_option('enqueue_select2_styles_scripts')) {
			wp_register_style('hm-select2-style', get_template_directory_uri().'/assets/lib/select2/4.0.13/select2.min.css', $css_deps);
			wp_enqueue_style('hm-select2-style');
			$css_deps[] = 'hm-select2-style';

			wp_register_script('hm-select2-script', get_template_directory_uri().'/assets/lib/select2/4.0.13/select2.min.js', $js_deps);
			wp_enqueue_script('hm-select2-script');
			$js_deps[] = 'hm-select2-script';
		}

		// Font Awesome 5 styles.
		if (get_theme_option('enqueue_fontawesome_5_style')) {
			wp_register_style('hm-fontawesome-5-style', get_template_directory_uri().'/assets/lib/fontawesome/5.15.3/css/all.min.css');
			wp_enqueue_style('hm-fontawesome-5-style');
			$css_deps[] = 'hm-fontawesome-5-style';
		}
	}

	/**
	 * Child scripts and styles.
	 */
	public function wp_enqueue_child_scripts() {
		$js_deps = array('jquery');
		$css_deps = array();

		// Google font styles.
		if (get_theme_option('google_fonts')) {
			wp_enqueue_style('google-fonts', esc_html(get_theme_option('google_fonts')), $css_deps);
		}

		// Compiled child styles.
		if (file_exists(get_stylesheet_directory().'/css/compiled.css')) {
			wp_enqueue_style('hm-child-compiled-style', get_stylesheet_directory_uri().'/css/compiled.css', $css_deps);
		}
		if (file_exists(get_stylesheet_directory().'/assets/css/compiled.css')) {
			wp_enqueue_style('hm-child-compiled-style', get_stylesheet_directory_uri().'/assets/css/compiled.css', $css_deps);
		}

		// Main child stylesheet.
		wp_enqueue_style('hm-child-style', get_stylesheet_directory_uri().'/style.css');

		// Child scripts.
		if (file_exists(get_stylesheet_directory().'/assets/js/script.js')) {
			wp_enqueue_script('hm-child-script', get_stylesheet_directory_uri().'/assets/js/script.js', $js_deps);
		}
		if (file_exists(get_stylesheet_directory().'/assets/js/script.js')) {
			wp_enqueue_script('hm-child-script', get_stylesheet_directory_uri().'/assets/js/script.js', $js_deps);
		}
	}

	public function add_head_code() {
		echo get_theme_option('head_code');
	}

	public function add_body_code() {
		echo get_theme_option('body_code');
	}

	public function add_footer_code() {
		echo get_theme_option('footer_code');
	}

	/**
	 * Gutenberg style option.
	 */
	public function enqueue_gutenberg_style() {
		if (get_theme_option('dequeue_gutenberg_style') == 0) {
			wp_dequeue_style('wp-block-library');
		}
	}

	/**
	 * jQuery Migrate script option.
	 */
	public function enqueue_jquery_migrate($scripts){
		if (get_theme_option('enqueue_jquery_migrate') == 0) {
			if (!is_admin() && isset( $scripts->registered['jquery'])) {
				$script = $scripts->registered['jquery'];
				if ($script->deps) { // Check whether the script has any dependencies
					$script->deps = array_diff($script->deps, array('jquery-migrate'));
				}
			}
		}
	}

	public function excerpt_more($more) {
		return '...';
	}

	/**
	 * Remove archive labels.
	 */
	public function remove_archive_title($title) {
		if (is_category()) {
			$title = single_cat_title('', false);
		} elseif (is_tag()) {
			$title = single_tag_title('', false);
		} elseif (is_author()) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif (is_post_type_archive()) {
			$title = post_type_archive_title('', false);
		} elseif (is_tax()) {
			$title = single_term_title('', false);
		} elseif (is_home()) {
			$title = single_post_title('', false);
		}

		return $title;
	}

	/**
	 * Number of posts per page.
	 */
	public function num_posts_per_page($query = false) {
		if (is_admin()) {
			return;
		}

		if (!is_a($query, 'WP_Query') || !$query->is_main_query()) {
			return;
		}

		if ($query->is_category) {
			$query->set( 'posts_per_page', (int) get_theme_option('catnum_posts') );
		} elseif ($query->is_tag) {
			$query->set( 'posts_per_page', (int) get_theme_option('tagnum_posts') );
		} elseif ($query->is_search) {
			$query->set( 'posts_per_page', (int) get_theme_option('searchnum_posts') );
		} elseif ($query->is_archive) {
			if (function_exists('is_woocommerce') && is_woocommerce()) {
				// Plugin Compatibility :: Skip query->set if "loop_shop_per_page" filter is being used by 3rd party plugins
				if (!has_filter('loop_shop_per_page')) {
					$query->set( 'posts_per_page', get_theme_option('woocommerce_archive_num_posts') );
				}
			} else {
				$query->set( 'posts_per_page', get_theme_option('archivenum_posts') );
			}
		}
	}

	/**
	 * Restore image title.
	 */
	public function restore_image_title($html, $id) {
		$attachment = get_post($id);
		if (strpos($html, 'title=')) {
			return $html;
		} else {
			$mytitle = esc_attr($attachment->post_title);
			return str_replace('<img', '<img title="'. $mytitle .'"', $html);
		}
	}

	/**
	 * Restore image title to gallery images.
	 */
	public function restore_title_to_gallery($content, $id) {
		$thumb_title = get_the_title($id);
		return str_replace('<a', '<a title="'. esc_attr($thumb_title) .'" ', $content);
	}

	/**
	 * Stop TinyMCE from modifying WYSIWYG code.
	 */
	public function override_mce_options($initArray) {
		$opts = '*[*]';
		$initArray['valid_elements'] = $opts;
		$initArray['extended_valid_elements'] = $opts;
		return $initArray;
	}

	/**
	 * Change gravity forms submit to button.
	 */
	public function gform_submit_button($button, $form) {
		$button = str_replace("input", "button", $button);
		$button = str_replace("/", "", $button);
		$button .= "{$form['button']['text']}</button>";
		return $button;
	}

	/**
	 * Replaces the default gravity form spinner image with a blank pixel, so you can style it with css
	 */
	public function gform_ajax_spinner_url($image_src, $form) {
		return 'data:images/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
	}

}

/**
 * Initialize Hawp_Theme_Setup class with a function.
 */
function hawp_theme_setup() {
	global $hawp_theme_setup;

	// Instantiate only once.
	if (!isset($hawp_theme_setup)) {
		$hawp_theme_setup = new Hawp_Theme_Setup();
		$hawp_theme_setup->setup();
	}
	return $hawp_theme_setup;
}
hawp_theme_setup();

endif; // class_exists check
