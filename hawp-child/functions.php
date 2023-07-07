<?php
// ------------------------------------------
// Child theme functions.
// ------------------------------------------

/**
 * Loop through folders and auto include files.
 */
add_action('init', function() {
	// Auto include 'inc' files
	$inc_dir = HMC_PATH.'/inc/';
	if (is_dir($inc_dir)) {
		$includes = glob($inc_dir . '*.php');
		foreach ($includes as $include) {
			require_once $include;
		}
	}

	// Auto include 'blocks' files
	$block_dir = HMC_PATH.'/blocks/';
	if (is_dir($block_dir) && function_exists('register_block_type')) {
		$blocks = glob($block_dir . '*.php');
		foreach ($blocks as $block) {
			register_block_type(HMC_PATH . "/blocks/{$block}");
		}
	}
});

/**
 * Set up child theme stuff.
 */
add_action('after_setup_theme', function() {
	add_editor_style(HMC_URL.'/assets/css/compiled-editor.css');
	add_editor_style(HMC_URL.'/style-editor.css');
});

/**
 * Add block styles
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
// add_filter('block_categories_all' , function($categories) {
// 	$categories[] = array(
// 		'slug'  => 'ashenborne',
// 		'title' => 'AshenBorne'
// 	);
// 	return $categories;
// });


/**
 * Register block scripts.
 */
add_action('init', function() {
	//wp_register_script("hm-mixitup-pagination", HMC_URL."/assets/js/mixitup-pagination.min.js", [ 'jquery', 'hm-mixitup' ], null, true);
});

