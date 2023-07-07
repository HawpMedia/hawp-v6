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
	add_editor_style(HMC_URL.'/assets/css/compiled-editor.css');
	add_editor_style(HMC_URL.'/style-editor.css');
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

