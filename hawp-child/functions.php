<?php
// ------------------------------------------
// Child theme functions.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!isset($content_width)) {
	$content_width = 1400;
}

// Loop through inc folder and auto include files.
$includes = [
	//'add_item_here',
];
foreach ($includes as $include) {
	require_once "inc/{$include}.php";
}

/**
 * Set up child theme stuff.
 */
add_action('after_setup_theme', function() {
	add_theme_support('editor-styles');
	add_editor_style(HMC_URL.'/assets/css/compiled-editor.css');
	add_editor_style(HMC_URL.'/style-editor.css');
	add_theme_support('editor-color-palette', array(
		array(
			'name'  => 'White',
			'slug'  => 'white',
			'color' => '#fff',
		),
		array(
			'name'  => 'Black',
			'slug'  => 'black',
			'color' => '#000',
		),
	));
});

/**
 * Add block styles/
 */
/*
add_action('init', function() {
	register_block_style('core/heading', [
		'name' => 'bottom-border',
		'label' => 'Bottom border',
	]);
	register_block_style('core/list', [
		'name' => 'circle-check',
		'label' => 'Circle checklist',
	]);
	register_block_style('core/list', [
		'name' => 'no-list',
		'label' => 'Hidden checklist',
	]);
});
*/

/**
 * Add ACF block category.
 */
/*
add_filter('block_categories', function($categories, $post) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'newsitename',
				'title' => 'New Site Name',
			),
		)
	);
}, 10, 2);
*/

/*
/**
 * Add ACF blocks.
 */
add_action('acf/init', function() {
	if (function_exists('acf_register_block_type')) {
		// Container.
		acf_register_block_type(array(
			'name' => 'container',
			'title' => __('Container'),
			'description' => __('A container block.'),
			'render_template' => 'blocks/container/container.php',
			'category' => 'newsitename', // CHANGE
			'supports' => array(
				'align' => false,
				'anchor' => true,
				'mode' => false,
				'jsx' => true,
			),
			'mode' => 'preview',
		));
		// Hero.
		acf_register_block_type(array(
			'name' => 'hero',
			'title' => __('Hero'),
			'description' => __('Hero section with button.'),
			'render_template' => 'blocks/hero/hero.php',
			'category' => 'newsitename', // CHANGE
			'keywords' => array(
				'hero',
				'header',
			),
			'supports' => array(
				'align' => false,
				'mode' => false,
				'jsx' => true,
			),
			'mode' => 'preview',
		));
		// Testimonials.
		acf_register_block_type(array(
			'name' => 'testimonials',
			'title' => __('Testimonials'),
			'render_template' => 'blocks/testimonials/testimonials.php',
			'enqueue_assets' => function() {
				wp_enqueue_style('hm-owl-style', get_template_directory_uri().'/assets/lib/owl/2.3.4/owl.carousel.min.css');
				wp_enqueue_style('hm-owl-theme-style', get_template_directory_uri().'/assets/lib/owl/2.3.4/owl.theme.default.min.css');
				wp_enqueue_script('hm-owl-script', get_template_directory_uri().'/assets/lib/owl/2.3.4/owl.carousel.min.js', array('jquery'));
				wp_enqueue_script('block-testimonials', get_stylesheet_directory_uri().'/blocks/testimonials/js/script.js', array(), '', true);
			},
			'category' => 'newsitename', // CHANGE
			'supports' => array(
				'align' => false,
			),
			'mode' => 'preview',
		));
	}
});
