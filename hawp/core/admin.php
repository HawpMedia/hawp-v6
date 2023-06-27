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
		add_styles_and_scripts([
			[
				'styles' => [
					[ 'hm-admin-fontawesome-5', HM_URL.'/assets/lib/fontawesome/5.15.4/css/all.min.css' ],
					[ 'hm-admin-fontawesome-6', HM_URL.'/assets/lib/fontawesome/6.1.1/css/all.min.css' ],
					[ 'hm-admin', HM_URL.'/assets/css/admin.css' ],
				],
				'scripts' => [ [ 'hm-admin', HM_URL.'/assets/js/admin.js', ['jquery'] ] ]
			],
			[
				'enable' => strpos($_SERVER['REQUEST_URI'], 'theme-options') !== false ? true : false,
				'styles' => [ [ 'hm-admin-options', HM_URL.'/assets/css/admin-options.css' ] ]
			]
		]);
		wp_enqueue_code_editor(['type'=>'application/x-httpd-php']);
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
				'style' => 'background-color: #FF0000;'
			));
		}
	}

	/**
	 * Add menu item to admin menu bar
	 */
	public function add_admin_bar_menu($meta = true) {
		global $wp_admin_bar;

		$wp_admin_bar->add_menu( array(
			'id' => 'contact_hawp',
			'title' => __('Need help? Contact Hawp Media'),
			'href' => 'https://hawpmedia.com/',
			'meta' 	=> array('target' => '_blank'))
		);
	}

	/**
	 * Change admin footer text
	 */
	public function change_admin_footer() {
		echo '<span id="hm-footer-note">This website was developed by <a href="https://hawpmedia.com/" target="_blank">Hawp Media</a>.</span><svg style="max-width: 50px; position: absolute; right: 27px; top: -36px;" viewBox="0 0 50 43.5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><clipPath id="a"><path d="m0 0h50v43.5h-50z"/></clipPath><g clip-path="url(#a)" fill="#48bdee"><path d="m35.8 20.09c6.45.74 3.61 4.89-1.78 6.66 3.57-5.3.77-3.93-7.05-6.21 13.25-4.4 13.2-9.92 12.5-14.12-1.24-2.44-3.2-4.43-1.79-6.43.97.5 2.66 2.17 3.12 3.02 2.83.45 4.69 3.11 9.17 5.41.07.24-.1 1.4-1.25 1.86-1.48.59-3.21-.51-4.83 1.12-1.05 1.06-1.24 3.08-2.19 4.36-1.54 2.13-3.97 3.45-5.91 4.32z"/><path d="m36.84 9.38c0 .57-.91 5.01-7.13 7-2.65.72-5.41.69-8.63-.6 8.12 8.68-.13 11.51-2.81 17.5-.66 1.79.86 5.04.88 6.74-.06 1.81-1.44 3.3-3.24 3.49.07-.54.52-3.26.32-4.33-.23-1.21-1.38-2.99-1.61-4.62-.4-2.9 2.79-6.01 3.24-8.17.17-.79-.36-1.8-1.11-3.02-.79-1.27-1.92-2.32-2.04-2.28-.59.31-1.5 2.31-1.81 3.17-1.02 2.79-2.68 8.68-4.57 10.97-1.38 1.67-4.87 3.33-8.31 2.34 3.09 0 6.05-2.33 6.84-4.17 1.99-4.69.94-12.6 6.58-19.31 6.5-6.99 13.05-3.86 18.6-3.67 2.82.09 4.77-1 4.82-1.03z"/></g></svg>';
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
						'label' => '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zm-6 400H54c-3.3 0-6-2.7-6-6V86c0-3.3 2.7-6 6-6h340c3.3 0 6 2.7 6 6v340c0 3.3-2.7 6-6 6zM224 184v16c0 13.3-10.7 24-24 24h-24v148c0 6.6-5.4 12-12 12h-8c-6.6 0-12-5.4-12-12V224h-24c-13.3 0-24-10.7-24-24v-16c0-13.3 10.7-24 24-24h24v-20c0-6.6 5.4-12 12-12h8c6.6 0 12 5.4 12 12v20h24c13.3 0 24 10.7 24 24zm128 128v16c0 13.3-10.7 24-24 24h-24v20c0 6.6-5.4 12-12 12h-8c-6.6 0-12-5.4-12-12v-20h-24c-13.3 0-24-10.7-24-24v-16c0-13.3 10.7-24 24-24h24V140c0-6.6 5.4-12 12-12h8c6.6 0 12 5.4 12 12v148h24c13.3 0 24 10.7 24 24z"></path></svg> General',
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
						'key' => get_theme_option_prefix().'catnum_posts',
						'label' => 'Number of Posts displayed on Category pages',
						'name' => get_theme_option_prefix().'catnum_posts',
						'type' => 'text',
						'default_value' => 5,
					),
					array(
						'key' => get_theme_option_prefix().'archivenum_posts',
						'label' => 'Number of Posts displayed on Archive pages',
						'name' => get_theme_option_prefix().'archivenum_posts',
						'type' => 'text',
						'default_value' => 5,
					),
					array(
						'key' => get_theme_option_prefix().'searchnum_posts',
						'label' => 'Number of Posts displayed on Search pages',
						'name' => get_theme_option_prefix().'searchnum_posts',
						'type' => 'text',
						'default_value' => 5,
					),
					array(
						'key' => get_theme_option_prefix().'tagnum_posts',
						'label' => 'Number of Posts displayed on Tag pages',
						'name' => get_theme_option_prefix().'tagnum_posts',
						'type' => 'text',
						'default_value' => 5,
					),
					array(
						'key' => get_theme_option_prefix().'woocommerce_archive_num_posts',
						'label' => 'Number of Products displayed on WooCommerce archive pages',
						'name' => get_theme_option_prefix().'woocommerce_archive_num_posts',
						'type' => 'text',
						'default_value' => 9,
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
						'label' => '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M340.18 58.73C325.55 41.75 303.85 32 280.67 32c-35.78 0-66.49 22.94-74.62 55.78l-80.66 325.25C122.67 424.02 111.57 432 99 432c-8.3 0-16.31-3.53-21.41-9.44l-35.66-41.45c-5.84-6.79-16.27-7.71-23.29-2.06l-12.7 10.24c-7.01 5.65-7.96 15.74-2.13 22.53l35.67 41.47C54.12 470.27 75.82 480 99 480c35.78 0 66.49-22.94 74.62-55.78l80.66-325.25C257 87.98 268.11 80 280.67 80c8.3 0 16.31 3.53 21.41 9.44l39.99 46.53c5.84 6.79 16.27 7.72 23.29 2.07l12.69-10.22c7.02-5.65 7.97-15.74 2.14-22.53l-40.01-46.56z"></path></svg> Integration',
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
						'label' => '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M116.65 219.35a15.68 15.68 0 0 0 22.65 0l96.75-99.83c28.15-29 26.5-77.1-4.91-103.88C203.75-7.7 163-3.5 137.86 22.44L128 32.58l-9.85-10.14C93.05-3.5 52.25-7.7 24.86 15.64c-31.41 26.78-33 74.85-5 103.88zm143.92 100.49h-48l-7.08-14.24a27.39 27.39 0 0 0-25.66-17.78h-71.71a27.39 27.39 0 0 0-25.66 17.78l-7 14.24h-48A27.45 27.45 0 0 0 0 347.3v137.25A27.44 27.44 0 0 0 27.43 512h233.14A27.45 27.45 0 0 0 288 484.55V347.3a27.45 27.45 0 0 0-27.43-27.46zM144 468a52 52 0 1 1 52-52 52 52 0 0 1-52 52zm355.4-115.9h-60.58l22.36-50.75c2.1-6.65-3.93-13.21-12.18-13.21h-75.59c-6.3 0-11.66 3.9-12.5 9.1l-16.8 106.93c-1 6.3 4.88 11.89 12.5 11.89h62.31l-24.2 83c-1.89 6.65 4.2 12.9 12.23 12.9a13.26 13.26 0 0 0 10.92-5.25l92.4-138.91c4.88-6.91-1.16-15.7-10.87-15.7zM478.08.33L329.51 23.17C314.87 25.42 304 38.92 304 54.83V161.6a83.25 83.25 0 0 0-16-1.7c-35.35 0-64 21.48-64 48s28.65 48 64 48c35.2 0 63.73-21.32 64-47.66V99.66l112-17.22v47.18a83.25 83.25 0 0 0-16-1.7c-35.35 0-64 21.48-64 48s28.65 48 64 48c35.2 0 63.73-21.32 64-47.66V32c0-19.48-16-34.42-33.92-31.67z"></path></svg> SVG Library',
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
						'label' => '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M384 121.941V128H256V0h6.059c6.365 0 12.47 2.529 16.971 7.029l97.941 97.941A24.005 24.005 0 0 1 384 121.941zM248 160c-13.2 0-24-10.8-24-24V0H24C10.745 0 0 10.745 0 24v464c0 13.255 10.745 24 24 24h336c13.255 0 24-10.745 24-24V160H248zM123.206 400.505a5.4 5.4 0 0 1-7.633.246l-64.866-60.812a5.4 5.4 0 0 1 0-7.879l64.866-60.812a5.4 5.4 0 0 1 7.633.246l19.579 20.885a5.4 5.4 0 0 1-.372 7.747L101.65 336l40.763 35.874a5.4 5.4 0 0 1 .372 7.747l-19.579 20.884zm51.295 50.479l-27.453-7.97a5.402 5.402 0 0 1-3.681-6.692l61.44-211.626a5.402 5.402 0 0 1 6.692-3.681l27.452 7.97a5.4 5.4 0 0 1 3.68 6.692l-61.44 211.626a5.397 5.397 0 0 1-6.69 3.681zm160.792-111.045l-64.866 60.812a5.4 5.4 0 0 1-7.633-.246l-19.58-20.885a5.4 5.4 0 0 1 .372-7.747L284.35 336l-40.763-35.874a5.4 5.4 0 0 1-.372-7.747l19.58-20.885a5.4 5.4 0 0 1 7.633-.246l64.866 60.812a5.4 5.4 0 0 1-.001 7.879z"></path></svg> Scripts & Styles',
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
						'label' => '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M240.06,454.34A32,32,0,0,0,245.42,472l17.1,25.69c5.23,7.91,17.17,14.28,26.64,14.28h61.7c9.47,0,21.41-6.37,26.64-14.28L394.59,472A37.47,37.47,0,0,0,400,454.34L400,416H240ZM319.45,0C217.44.31,144,83,144,176a175,175,0,0,0,43.56,115.78c16.52,18.85,42.36,58.22,52.21,91.44,0,.28.07.53.11.78H400.12c0-.25.07-.5.11-.78,9.85-33.22,35.69-72.59,52.21-91.44A175,175,0,0,0,496,176C496,78.63,416.91-.31,319.45,0ZM320,96a80.09,80.09,0,0,0-80,80,16,16,0,0,1-32,0A112.12,112.12,0,0,1,320,64a16,16,0,0,1,0,32ZM112,192a24,24,0,0,0-24-24H24a24,24,0,0,0,0,48H88A24,24,0,0,0,112,192Zm504-24H552a24,24,0,0,0,0,48h64a24,24,0,0,0,0-48ZM131.08,55.22l-55.42-32a24,24,0,1,0-24,41.56l55.42,32a24,24,0,1,0,24-41.56Zm457.26,264-55.42-32a24,24,0,1,0-24,41.56l55.42,32a24,24,0,0,0,24-41.56Zm-481.26-32-55.42,32a24,24,0,1,0,24,41.56l55.42-32a24,24,0,0,0-24-41.56ZM520.94,100a23.8,23.8,0,0,0,12-3.22l55.42-32a24,24,0,0,0-24-41.56l-55.42,32a24,24,0,0,0,12,44.78Z"></path></svg> Utilities',
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
						'key' => get_theme_option_prefix().'admin_ui',
						'label' => '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M464 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h416c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zM48 92c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v24c0 6.6-5.4 12-12 12H60c-6.6 0-12-5.4-12-12V92zm416 334c0 3.3-2.7 6-6 6H54c-3.3 0-6-2.7-6-6V168h416v258zm0-310c0 6.6-5.4 12-12 12H172c-6.6 0-12-5.4-12-12V92c0-6.6 5.4-12 12-12h280c6.6 0 12 5.4 12 12v24z"></path></svg> Admin UI',
						'name' => get_theme_option_prefix().'admin_ui',
						'type' => 'tab',
						'placement' => 'left',
						'endpoint' => 0,
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
		  acf.add_filter("color_picker_args", function( $args, $field ){

			// this will create a settings variable with all settings
			const $settings = wp.data.select( "core/editor" ).getEditorSettings();
			// pull out the colors from that variable
			let $colors = $settings.colors.map(x => x.color);

			// assign those colors to palettes
			$args.palettes = $colors;
			return $args;
		  });
		})(jQuery);
		</script>';
	}

	/**
	 * ACF Local JSON sync.
	 */
	public function acf_json_save_point($path) {
		$path = get_stylesheet_directory() . '/acf-json';
		return $path;
	}

	public function acf_json_load_point($paths) {
		$paths[0] = get_stylesheet_directory() . '/acf-json';

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
