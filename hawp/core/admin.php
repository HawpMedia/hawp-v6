<?php
// ------------------------------------------
// Theme admin functions.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!class_exists('Hawp_Theme_Admin')):

class Hawp_Theme_Admin {

	/**
	 * Constructor.
	 */
	public function setup() {
		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'], 999);
		add_action('admin_notices', [$this, 'admin_notices']);
		add_action('admin_bar_menu', [$this, 'add_admin_bar_menu'], 100);
		add_action('admin_bar_menu', [$this, 'add_admin_bar_notice'], 0);
		add_filter('admin_footer_text', [$this, 'change_admin_footer']);
		add_action('acf/init', [$this, 'add_acf_options_page']);
		add_action('acf/init', [$this, 'add_acf_options_fields']);
		//add_action('acf/input/admin_footer', [$this, 'add_acf_color_palette']);
		add_action('acf/input/admin_footer', [$this, 'add_theme_colors_to_acf_color_picker']);
		add_filter('acf/settings/save_json', [$this, 'acf_json_save_point']);
		add_filter('acf/settings/load_json', [$this, 'acf_json_load_point']);
	}

	/**
	 * Add admin styles and scripts.
	 */
	public function admin_enqueue_scripts() {
		wp_register_style('hm_admin_fontawesome_5', HM_URL.'/assets/lib/fontawesome/5.15.4/css/all.min.css');
		wp_enqueue_style('hm_admin_fontawesome_5');
		wp_register_style('hm_admin_fontawesome_6', HM_URL.'/assets/lib/fontawesome/6.5.1/css/all.min.css');
		wp_enqueue_style('hm_admin_fontawesome_6');
		wp_register_style('hm_admin_style', HM_URL.'/assets/css/admin.css');
		wp_enqueue_style('hm_admin_style');

		$google_fonts = get_theme_option('google_fonts');
		if ($google_fonts) {
			wp_enqueue_style('hm_admin_google_fonts', $google_fonts);
		}

		wp_register_style('hm_admin_options_style', HM_URL.'/assets/css/admin-options.css');
		if (strpos($_SERVER['REQUEST_URI'], 'theme-options')) {
			wp_enqueue_style('hm_admin_options_style');
		}

		// Load admin options style only on theme options page
		if (strpos($_SERVER['REQUEST_URI'], 'theme-options')) {
			wp_register_style('hm_admin_options_style', HM_URL.'/assets/css/admin-options.css', [], filemtime(HM_PATH.'/assets/css/admin-options.css'));
			wp_enqueue_style('hm_admin_options_style');
		}
	}

	/**
	 * Add admin notices.
	 */
	public function admin_notices() {
		$url = $_SERVER['HTTP_HOST'];

		if (current_user_can('administrator') && !get_option('blogname')) {
			echo '<div class="notice notice-error"><p>Warning: Site Title is NOT set. Please go to the <a href="' . admin_url('options-general.php') . '">Settings page</a> and add the site title.</p></div>';
		}

		if (current_user_can('administrator') && get_option('blog_public') == 1 && (strpos($url, '.local') !== false || strpos($url, '.dev') !== false)) {
			echo '<div class="notice notice-error"><p>Warning: <a href="' . admin_url('options-reading.php') . '">Discourage search engines from indexing this site</a> is NOT checked in Settings->Reading->Search Engine Visibility. Make sure this option IS checked if the site is not live.</p></div>';
		}
	}

	/**
	 * Add menu node to admin bar
	 *
	 * This gets added if the url contains
	 * .local or .dev, so its only for dev sites.
	 */
	public function add_admin_bar_notice($meta = true) {
		global $wp_admin_bar;
		$url = $_SERVER['HTTP_HOST'];

		if (current_user_can('administrator') && (strpos($url, '.local') !== false || strpos($url, '.dev') !== false)) {
			// Add the notice to the admin bar
			$wp_admin_bar->add_node(array(
				'id'    => 'dev-site-notice',
				'title' => '&lt;/&gt;',
				'meta'  => array('class' => 'dev-site-notice'),
				'position' => -99999,
			));
			$wp_admin_bar->add_node(array(
				'parent'=> 'dev-site-notice',
				'id'    => 'dev-site-notice-text',
				'title' => sprintf('This website is in development by %s', Hawp_Theme::$whitelabel['brand_name']),
				'meta'  => array('class' => 'dev-site-notice-item'),
				'position' => -99999,
			));
		}
	}

	/**
	 * Add menu item to admin menu bar
	 */
	public function add_admin_bar_menu($meta = true) {
		if (current_user_can('manage_options')) {
			global $wp_admin_bar;

			$wp_admin_bar->add_menu(array(
				'id' => 'contact_hawp',
				'title' => sprintf(__('Need help? Contact %s'), Hawp_Theme::$whitelabel['brand_name']),
				'href' => Hawp_Theme::$whitelabel['brand_url'],
				'meta' 	=> array('target' => '_blank')
			));
		}
	}

	/**
	 * Change admin footer text
	 */
	public function change_admin_footer() {
		printf(
			'<span id="hm-footer-note">%s <a href="%s" target="_blank">%s</a>.<span style="width: 50px; height: 50px; position: absolute; right: 27px; top: -36px;">%s</span>',
			Hawp_Theme::$whitelabel['admin_footer_text'],
			Hawp_Theme::$whitelabel['brand_url'],
			Hawp_Theme::$whitelabel['brand_name'],
			Hawp_Theme::$whitelabel['admin_footer_logo']
		);
	}

	/**
	 * Add ACF options page.
	 */
	public function add_acf_options_page() {
		if (function_exists('acf_add_options_page')) {
			acf_add_options_page(array(
				'page_title' => __('Theme Options'),
				'menu_title' => __('Theme Options'),
				'parent_slug' => 'themes.php',
				'menu_slug' => 'theme-options',
				'capability' => 'manage_options',
				'updated_message' => __('Theme options updated.'),
				'redirect' => false,
			));
		}
	}

	/**
	 * Add ACF options fields.
	 */
	public function add_acf_options_fields() {
		if (function_exists('acf_add_local_field_group')) {
			acf_add_local_field_group(array(
				'key' => get_theme_option_prefix().'general',
				'title' => 'General',
				'fields' => array(
					array(
						'key' => get_theme_option_prefix().'tab_general',
						'label' => 'General',
						'name' => get_theme_option_prefix().'tab_general',
						'type' => 'tab',
						'placement' => 'left',
						'endpoint' => 0,
					),
					array(
						'key' => get_theme_option_prefix().'logo',
						'label' => 'Logo',
						'name' => get_theme_option_prefix().'logo',
						'type' => 'image',
						'instructions' => 'Used with <code>[logo]</code> shortcode.',
						'return_format' => 'id',
						'preview_size' => 'full',
						'library' => 'all',
						'mime_types' => 'jpg, jpeg, png, svg',
					),
					array(
						'key' => get_theme_option_prefix().'header_bg_image',
						'label' => 'Default header background image',
						'name' => get_theme_option_prefix().'header_bg_image',
						'type' => 'image',
						'return_format' => 'id',
						'preview_size' => 'full',
						'library' => 'all',
						'mime_types' => 'jpg, jpeg, png',
					),
					array(
						'key' => get_theme_option_prefix().'catnum_posts',
						'label' => 'Number of Posts displayed on Category pages',
						'name' => get_theme_option_prefix().'catnum_posts',
						'type' => 'number',
						'default_value' => 5,
					),
					array(
						'key' => get_theme_option_prefix().'archivenum_posts',
						'label' => 'Number of Posts displayed on Archive pages',
						'name' => get_theme_option_prefix().'archivenum_posts',
						'type' => 'number',
						'default_value' => 5,
					),
					array(
						'key' => get_theme_option_prefix().'searchnum_posts',
						'label' => 'Number of Posts displayed on Search pages',
						'name' => get_theme_option_prefix().'searchnum_posts',
						'type' => 'number',
						'default_value' => 5,
					),
					array(
						'key' => get_theme_option_prefix().'tagnum_posts',
						'label' => 'Number of Posts displayed on Tag pages',
						'name' => get_theme_option_prefix().'tagnum_posts',
						'type' => 'number',
						'default_value' => 5,
					),
					array(
						'key' => get_theme_option_prefix().'woocommerce_archive_num_posts',
						'label' => 'Number of Products displayed on WooCommerce archive pages',
						'name' => get_theme_option_prefix().'woocommerce_archive_num_posts',
						'type' => 'number',
						'default_value' => 9,
					),
					array(
						'key' => get_theme_option_prefix().'excerpt_length',
						'label' => 'Change the default excerpt length',
						'name' => get_theme_option_prefix().'excerpt_length',
						'type' => 'number',
						'placeholder' => 'defaults to 55',
					),
					array(
						'key' => get_theme_option_prefix().'content_cta',
						'label' => 'Content CTA',
						'name' => get_theme_option_prefix().'content_cta',
						'type' => 'textarea',
						'instructions' => 'This code will be used to generate the content that is placed in the <code>[content_cta]</code> shortcode.',
						'wrapper' => array(
							'class' => 'hm-html-editor',
						),
						'rows' => 4,
					),
					array(
						'key' => get_theme_option_prefix().'tab_integration',
						'label' => 'Integration',
						'name' => get_theme_option_prefix().'tab_integration',
						'type' => 'tab',
						'placement' => 'left',
						'endpoint' => 0,
					),
					array(
						'key' => get_theme_option_prefix().'google_fonts',
						'label' => 'Google Fonts embed URL <a href="https://fonts.google.com/" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-info"></span></a>',
						'name' => get_theme_option_prefix().'google_fonts',
						'type' => 'url',
						'instructions' => '',
					),
					array(
						'key' => get_theme_option_prefix().'head_code',
						'label' => 'Add code to the &lt; head &gt;',
						'name' => get_theme_option_prefix().'head_code',
						'type' => 'textarea',
						'wrapper' => array(
							'class' => '',
						),
						'rows' => 16,
					),
					array(
						'key' => get_theme_option_prefix().'body_code',
						'label' => 'Add code to the &lt; body &gt;',
						'name' => get_theme_option_prefix().'body_code',
						'type' => 'textarea',
						'instructions' => 'Good for tracking codes such as google analytics.',
						'wrapper' => array(
							'class' => '',
						),
						'rows' => 14,
					),
					array(
						'key' => get_theme_option_prefix().'footer_code',
						'label' => 'Add code above the &lt; /body &gt;',
						'name' => get_theme_option_prefix().'footer_code',
						'type' => 'textarea',
						'instructions' => 'Adds code to bottom of the site, before the closing body tag.',
						'wrapper' => array(
							'class' => '',
						),
						'rows' => 14,
					),
					array(
						'key' => get_theme_option_prefix().'tab_svgs',
						'label' => 'SVG Library',
						'name' => get_theme_option_prefix().'tab_svgs',
						'type' => 'tab',
						'placement' => 'left',
					),
					array(
						'key' => get_theme_option_prefix().'svgs',
						'label' => 'SVG Library',
						'name' => get_theme_option_prefix().'svgs',
						'instructions' => 'The number on the left of each row is the ID of your SVG. Example: <code>[svg id="1"]</code> will grab the SVG in row "1".',
						'type' => 'repeater',
						'layout' => 'table',
						'button_label' => 'Add SVG',
						'sub_fields' => array(
							array(
								'key' => get_theme_option_prefix().'label',
								'label' => 'Label',
								'name' => get_theme_option_prefix().'label',
								'instructions' => 'Label your svg.',
								'type' => 'text',
								'wrapper' => array(
									'width' => '25',
								),
							),
							array(
								'key' => get_theme_option_prefix().'svg',
								'label' => 'Code',
								'name' => get_theme_option_prefix().'svg',
								'instructions' => 'Paste your code in the texarea.',
								'type' => 'textarea',
								'wrapper' => array(
									'width' => '75',
								),
							),
						),
					),
					array(
						'key' => get_theme_option_prefix().'tab_scripts_styles',
						'label' => 'Scripts & Styles',
						'name' => get_theme_option_prefix().'tab_scripts_styles',
						'type' => 'tab',
						'placement' => 'left',
						'endpoint' => 0,
					),
					array(
						'key' => get_theme_option_prefix().'dequeue_gutenberg_style',
						'label' => 'Gutenberg Stylesheet',
						'name' => get_theme_option_prefix().'dequeue_gutenberg_style',
						'type' => 'true_false',
						'instructions' => 'The default block builder stylesheet.',
						'default_value' => 1,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'enqueue_jquery_migrate',
						'label' => 'jQuery Migrate',
						'name' => get_theme_option_prefix().'enqueue_jquery_migrate',
						'type' => 'true_false',
						'instructions' => 'Preserves compatibility of jQuery for versions of jQuery older than 1.9.',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'enqueue_lity_styles_scripts',
						'label' => 'Lity Lightbox',
						'instructions' => 'A jQuery library for developers to create lightweight, accessible and responsive lightboxes.',
						'name' => get_theme_option_prefix().'enqueue_lity_styles_scripts',
						'type' => 'true_false',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'enqueue_swiper_styles_scripts',
						'label' => 'Swiper',
						'name' => get_theme_option_prefix().'enqueue_swiper_styles_scripts',
						'type' => 'true_false',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'enqueue_owl_styles_scripts',
						'label' => 'Owl Carousel',
						'instructions' => 'A jQuery library for developers to create responsive and touch enabled carousel sliders.',
						'name' => get_theme_option_prefix().'enqueue_owl_styles_scripts',
						'type' => 'true_false',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'enqueue_litepicker_styles_scripts',
						'label' => 'Litepicker',
						'instructions' => 'A JavaScript library with no dependencies for developers to create lightweight date range pickers.',
						'name' => get_theme_option_prefix().'enqueue_litepicker_styles_scripts',
						'type' => 'true_false',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'enqueue_mixitup_styles_scripts',
						'label' => 'Mixitup',
						'instructions' => 'A JavaScript library with no dependencies for developers to create animated filtering and sorting elements.',
						'name' => get_theme_option_prefix().'enqueue_mixitup_styles_scripts',
						'type' => 'true_false',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'enqueue_select2_styles_scripts',
						'label' => 'Select2',
						'instructions' => 'The jQuery replacement for select boxes.',
						'name' => get_theme_option_prefix().'enqueue_select2_styles_scripts',
						'type' => 'true_false',
						'default_value' => 1,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'enqueue_fontawesome_5_style',
						'label' => 'FontAwesome 5',
						'instructions' => '',
						'name' => get_theme_option_prefix().'enqueue_fontawesome_5_style',
						'type' => 'true_false',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'enqueue_fontawesome_6_style',
						'label' => 'FontAwesome 6',
						'instructions' => '',
						'name' => get_theme_option_prefix().'enqueue_fontawesome_6_style',
						'type' => 'true_false',
						'default_value' => 1,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'utilities',
						'label' => 'Utilities',
						'name' => get_theme_option_prefix().'utilities',
						'type' => 'tab',
						'placement' => 'left',
						'endpoint' => 0,
					),
					array(
						'key' => get_theme_option_prefix().'force_ssl',
						'label' => 'Force SSL URL\'s',
						'name' => get_theme_option_prefix().'force_ssl',
						'type' => 'true_false',
						'instructions' => 'Force SSL redirect on internal URL\'s if the Site Address URL includes HTTPS.',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'prefix_post_urls',
						'label' => 'Prefix Post URL\'s',
						'name' => get_theme_option_prefix().'prefix_post_urls',
						'type' => 'true_false',
						'instructions' => 'Rewrite post urls to include /blog/ before the post slug.',
						'default_value' => 1,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'allow_svg_upload',
						'label' => 'Allow SVG Uploads',
						'name' => get_theme_option_prefix().'allow_svg_upload',
						'type' => 'true_false',
						'instructions' => 'Allow SVG files to be uploaded in the media library. Make sure you add the code below to the top of your svg file: <pre><code>&#x3C;?xml version="1.0" encoding="utf-8"?&#x3E;</code></pre>',
						'default_value' => 1,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'separate_multisite_acf_json',
						'label' => 'Separate ACF Fields on Multisite Subsites',
						'name' => get_theme_option_prefix().'separate_multisite_acf_json',
						'type' => 'true_false',
						'instructions' => 'Allows multisite sites to have their own set of ACF fields. If this is disabled, they are shared accross the network.',
						'default_value' => 1,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'admin_ui',
						'label' => 'Admin UI',
						'name' => get_theme_option_prefix().'admin_ui',
						'type' => 'tab',
						'placement' => 'left',
						'endpoint' => 0,
					),
					array(
						'key' => get_theme_option_prefix().'login_logo',
						'label' => 'Login Logo',
						'name' => get_theme_option_prefix().'login_logo',
						'type' => 'image',
						'instructions' => 'Appears on the login page. Shoud be a square to display properly. (160x160 recommended)',
						'return_format' => 'id',
						'preview_size' => 'full',
						'library' => 'all',
						'max_width' => 80,
						'min_height' => 80,
						'max_width' => 160,
						'max_height' => 160,
						'mime_types' => 'jpg, jpeg, png, svg',
					),
					array(
						'key' => get_theme_option_prefix().'comments_admin_menu_item',
						'label' => 'Comments Menu Item',
						'name' => get_theme_option_prefix().'comments_admin_menu_item',
						'type' => 'true_false',
						'instructions' => 'Show or hide the Comments menu item.',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
					array(
						'key' => get_theme_option_prefix().'wordpress_admin_item',
						'label' => 'WordPress Admin Items',
						'name' => get_theme_option_prefix().'wordpress_admin_item',
						'type' => 'true_false',
						'instructions' => 'Show or hide the wordpress.org related widgets & menus.',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'options_page',
							'operator' => '==',
							'value' => 'theme-options',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'acf_after_title',
				'style' => 'seamless',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'active' => true,
			));
		}
	}

	/**
	 * Get the editor color palette.
	 */
	public function get_editor_color_palette() {
		// get the colors
		$color_palette = current((array) get_theme_support('editor-color-palette'));

		// bail if there aren't any colors found
		if (!$color_palette)
			return;

		// output begins
		ob_start();

		// output the names in a string
		echo '[';
			foreach ($color_palette as $color) {
				echo "'" . $color['color'] . "', ";
			}
		echo ']';

		return ob_get_clean();
	}

	/**
	 * Add the editor color palette to acf color field.
	 */
	public function add_acf_color_palette() {
		$color_palette = $this->get_editor_color_palette();

		if (!$color_palette) {
			return;
		} else {
			echo '<script type="text/javascript">
				(function($) {
					acf.add_filter("color_picker_args", function(args, $field) {

						// add the hex codes for the colors appearing as swatches
						args.palettes = '. $color_palette .'

						// return colors
						return args;
					});
				})(jQuery);
			</script>';
		}
	}

	/**
	 * Add theme.json colors to acf color picker.
	 */
	public function add_theme_colors_to_acf_color_picker() {
		echo '<script type="text/javascript">
			(function($) {
			if (typeof wp !== "undefined" && typeof wp.data !== "undefined") {
				acf.add_filter("color_picker_args", function( $args, $field ){

					// this will create a settings variable with all settings
					const $settings = wp.data.select( "core/editor" ).getEditorSettings();
					// pull out the colors from that variable
					let $colors = $settings.colors.map(x => x.color);

					// assign those colors to palettes
					$args.palettes = $colors;
					return $args;
				});
			}
		})(jQuery);
		</script>';
	}

	/**
	 * Save ACF field groups to JSON based on site URL.
	 */
	public function acf_json_save_point($path) {
		$subsite_url = get_site_url();

		// Extract the desired portion from the site URL
		$subsite_slug = get_subsite_slug_from_url($subsite_url);

		$path = get_stylesheet_directory() . '/acf-json/' . $subsite_slug; // Customize the save path as needed

		// Create the directory if it doesn't exist
		if (!is_dir($path)) {
			wp_mkdir_p($path);
		}

		return $path;
	}

	/**
	 * Load ACF field groups from JSON based on site URL.
	 */
	public function acf_json_load_point($paths) {
		$subsite_url = get_site_url();

		// Extract the desired portion from the site URL
		$subsite_slug = get_subsite_slug_from_url($subsite_url);

		$load_path = get_stylesheet_directory() . '/acf-json/' . $subsite_slug; // Customize the load path as needed

		// Create the directory if it doesn't exist
		if (!is_dir($load_path)) {
			wp_mkdir_p($load_path);
		}

		// Remove the path to ACF post types JSON file
		unset($paths[0]);

		// Add JSON path for ACF fields based on site URL
		$paths[] = $load_path;

		return $paths;
	}
}

/**
 * Initialize Hawp_Theme_Admin class with a function.
 */
function hawp_theme_admin() {
	global $hawp_theme_admin;

	// Instantiate only once.
	if (!isset($hawp_theme_admin)) {
		$hawp_theme_admin = new Hawp_Theme_Admin();
		$hawp_theme_admin->setup();
	}
	return $hawp_theme_admin;
}
hawp_theme_admin();

endif; // class_exists check
