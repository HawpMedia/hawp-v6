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

	// Auto register blocks by their dir name in 'blocks'
	// auto_register_theme_blocks(HMC_PATH.'/blocks/');

	// Register block scripts - they get enqueued in block.json
	// wp_register_script('block-YOUR_BLOCK', HMC_URL.'/blocks/YOUR_BLOCK_FOLDER/script.js', [ 'jquery' ], null, true);
});

/**
 * Set up child theme stuff.
 */
add_action('after_setup_theme', function() {
	// add_theme_support('disable-layout-styles'); // Disable layout styles
	// add_theme_support('editor-styles'); // Add theme support for editor styles
	// add_theme_support('wp-block-styles'); // Add theme support for block styles
	// remove_theme_support('block-templates'); // Remove theme support for block templates

	add_editor_style(HMC_URL.'/assets/css/compiled-editor.css');
	add_editor_style(HMC_URL.'/style-editor.css');
});

/**
 * Add block styles.
 */
// add_action('init', function() {
// 	register_block_style('core/heading', [
// 		'name' => 'bottom-border',
// 		'label' => 'Bottom border',
// 	]);
// 	register_block_style('core/list', [
// 		'name' => 'circle-check',
// 		'label' => 'Circle checklist',
// 	]);
// 	register_block_style('core/list', [
// 		'name' => 'no-list',
// 		'label' => 'Hidden checklist',
// 	]);
// });

/**
 * Add custom block category.
 */
// add_filter('block_categories_all' , function($categories) {
// 	$categories[] = array(
// 		'slug'  => 'your-cat-slug',
// 		'title' => 'Your Cat Name'
// 	);
// 	return $categories;
// });
