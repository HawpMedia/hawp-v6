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
		if (is_admin()) {
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 999);
			add_action('admin_notices', array($this, 'admin_notices'));
			add_action('acf/init', array($this, 'add_acf_options_page'));
			add_action('acf/init', array($this, 'add_acf_options_fields'));
			add_action('acf/input/admin_footer', array($this, 'add_acf_color_palette'));
			add_filter('acf/settings/save_json', array($this, 'acf_json_save_point'));
			add_filter('acf/settings/load_json', array($this, 'acf_json_load_point'));
		}
	}

	/**
	 * Add admin styles and scripts.
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_script('admin-js', get_template_directory_uri().'/assets/js/admin.js', array('jquery'));
		wp_enqueue_style('admin-css', get_template_directory_uri().'/assets/css/admin.css');
		wp_enqueue_style('admin-fontawesome-4', get_template_directory_uri().'/lib/fontawesome-4.7/css/font-awesome.min.css');
		wp_enqueue_style('admin-fontawesome-5', get_template_directory_uri().'/lib/fontawesome-5.15.3/css/all.min.css');

		if (strpos($_SERVER['REQUEST_URI'], 'theme-options') !== false) {
			wp_enqueue_style('admin-options-css', get_template_directory_uri().'/assets/css/admin-options.css');
		}

		wp_enqueue_code_editor(array('type'=>'application/x-httpd-php'));
	}

	/**
	 * Add admin notices.
	 */
	public function admin_notices() {
		if (current_user_can('administrator') && !get_option('blogname')) {
			echo '<div class="error notice is-dismissible"><p>Site title is NOT set. Please go to the <a href="'.home_url().'/wp-admin/options-general.php">Settings page</a> and add the site title.</p></div>';
		}
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
						'key' => get_theme_option_prefix().'enqueue_fontawesome_4_style',
						'label' => 'FontAwesome 4',
						'instructions' => '',
						'name' => get_theme_option_prefix().'enqueue_fontawesome_4_style',
						'type' => 'true_false',
						'default_value' => 0,
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
						'key' => get_theme_option_prefix().'force_dynamic_urls',
						'label' => 'Dynamic URL\'s',
						'name' => get_theme_option_prefix().'force_dynamic_urls',
						'type' => 'true_false',
						'instructions' => 'Filter [home], [home_url] and [uploads] shortcodes base URL with Site Address URL (recommended).',
						'default_value' => 1,
						'ui' => 1,
						'ui_on_text' => 'On',
						'ui_off_text' => 'Off',
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
		$color_palette = current( (array) get_theme_support( 'editor-color-palette' ) );

		// bail if there aren't any colors found
		if ( !$color_palette )
			return;

		// output begins
		ob_start();

		// output the names in a string
		echo '[';
			foreach ( $color_palette as $color ) {
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
					acf.add_filter( "color_picker_args", function( args, $field ){

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
