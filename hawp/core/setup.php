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
		add_action('after_setup_theme', [$this, 'theme_setup']);
		add_action('after_setup_theme', [$this, 'child_theme_setup']);
		add_action('widgets_init', [$this, 'widgets_init']);
		add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
		add_action('wp_head', [$this, 'add_head_code']);
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		add_action('wp_body_open', [$this, 'add_body_code']);
		add_action('wp_footer', [$this, 'add_footer_code']);
		add_action('wp_default_scripts', [$this, 'enqueue_jquery_migrate']);
		remove_action('wp_print_styles', 'print_emoji_styles');
		add_action('wp_print_styles', [$this, 'enqueue_gutenberg_style'], 100);
		add_filter('excerpt_more', [$this, 'excerpt_more']);
		add_filter('get_the_archive_title', [$this, 'remove_archive_title']);
		add_action('pre_get_posts', [$this, 'num_posts_per_page']);
		add_filter('gform_ajax_spinner_url', [$this, 'gform_ajax_spinner_url'], 10, 2);
		add_filter('gform_submit_button', [$this, 'gform_submit_button'], 10, 5);
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
		add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
		add_post_type_support('page', 'excerpt');
		register_nav_menus(apply_filters('hawp_menus', [
			'primary' => 'Primary Menu',
			'top' => 'Top Menu',
			'secondary' => 'Footer Menu',
			'social' => 'Social Menu',
			'copyright' => 'Copyright Menu',
		]));
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
		register_sidebar([
			'name' => 'Footer',
			'id' => 'footer',
			'description' => '',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<header><h3 class="widget-title">',
			'after_title' => '</h3></header>',
		]);
		register_sidebar([
			'name' => 'Site Sidebar',
			'id' => 'site',
			'description' => '',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<header><h3 class="widget-title">',
			'after_title' => '</h3></header>',
		]);
		register_sidebar([
			'name' => 'Blog Sidebar',
			'id' => 'blog',
			'description' => '',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<header><h3 class="widget-title">',
			'after_title' => '</h3></header>',
		]);
	}

	/**
	 * Scripts and styles.
	 */
	public function wp_enqueue_scripts() {
		$scripts = [
			[
				'is_on' => get_theme_option('google_fonts') ? true : false,
				'style' => [ ['hm-google-fonts', esc_html(get_theme_option('google_fonts')), [], false, false] ],
			],
			[
				'is_on' => get_theme_option('enqueue_lity_styles_scripts') ? true : false,
				'style' => [ ['hm-lity', HM_URL.'/assets/lib/lity/2.4.0/lity.min.css', [], false, false] ],
				'script' => [ ['hm-lity', HM_URL.'/assets/lib/lity/2.4.0/lity.min.js', ['jquery'], false, true] ],
			],
			[
				'is_on' => get_theme_option('enqueue_owl_styles_scripts') ? true : false,
				'style' => [
					['hm-owl', HM_URL.'/assets/lib/owl/2.3.4/owl.carousel.min.css', [], false, true],
					['hm-owl-theme', HM_URL.'/assets/lib/owl/2.3.4/owl.theme.default.min.css', [], false, true]
				],
				'script' => [ ['hm-owl', HM_URL.'/assets/lib/owl/2.3.4/owl.carousel.min.js', ['jquery'], false, true] ],
			],
			[
				'is_on' => get_theme_option('enqueue_select2_styles_scripts') ? true : false,
				'style' => [ ['hm-select2', HM_URL.'/assets/lib/select2/4.0.13/select2.min.css', [], false, false] ],
				'script' => [ ['hm-select2', HM_URL.'/assets/lib/select2/4.0.13/select2.min.js', ['jquery'], false, true] ],
			],
			[
				'is_on' => get_theme_option('enqueue_litepicker_styles_scripts') ? true : false,
				'script' => [ ['hm-litepicker', 'https://cdnjs.cloudflare.com/ajax/lib/litepicker/2.0.11/litepicker.js', [], false, true] ]
			],
			[
				'is_on' => get_theme_option('enqueue_mixitup_styles_scripts') ? true : false,
				'script' => [ ['hm-mixitup', HM_URL.'/assets/lib/mixitup/3.3.1/mixitup.min.js', [], false, true] ]
			],
			[
				'is_on' => get_theme_option('enqueue_fontawesome_5_style') ? true : false,
				'style' => [ ['hm-fontawesome-5', HM_URL.'/assets/lib/fontawesome/5.15.4/css/all.min.css', [], false, false] ],
			],
			[
				'is_on' => get_theme_option('enqueue_fontawesome_6_style') ? true : false,
				'style' => [ ['hm-fontawesome-6', HM_URL.'/assets/lib/fontawesome/6.1.1/css/all.min.css', [], false, false] ],
			],
			[
				'is_on' => file_exists(HMC_PATH.'/css/compiled.css') ? true : false,
				'style' => [ ['hm-child-compiled-old', HMC_URL.'/css/compiled.css', [], false, false] ],
			],
			[
				'is_on' => file_exists(HMC_PATH.'/assets/css/compiled.css') ? true : false,
				'style' => [ ['hm-child-compiled', HMC_URL.'/assets/css/compiled.css', [], false, false] ],
			],
			[
				'is_on' => true,
				'style' => [ ['hm-child', HMC_URL.'/style.css', [], false, false] ],
			],
			[
				'is_on' => file_exists(HMC_PATH.'/js/script.js') ? true : false,
				'script' => [ ['hm-child-old', HMC_URL.'/js/script.js', ['jquery'], false, true] ],
			],
			[
				'is_on' => file_exists(HMC_PATH.'/assets/js/script.js') ? true : false,
				'script' => [ ['hm-child', HMC_URL.'/assets/js/script.js', ['jquery'], false, true] ],
			],
		];

		foreach($scripts as $data) {
			if ($data['is_on'] == true) {
				if (isset($data['style'])) {
					foreach ($data['style'] as $style) {
						wp_register_style($style[0], $style[1], $style[2], $style[3], $style[4]);
						wp_enqueue_style($style[0]);
					}
				}
				if (isset($data['script'])) {
					foreach ($data['script'] as $script) {
						wp_register_script($script[0], $script[1], $script[2], $script[3], $script[4]);
						wp_enqueue_script($script[0]);
					}
				}
			}
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
					$script->deps = array_diff($script->deps, ['jquery-migrate']);
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
