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

//add_action('after_setup_theme', function() {
	add_theme_support('editor-styles');
	add_editor_style(get_stylesheet_directory_uri().'/css/admin/style-editor.css');
	add_editor_style(get_stylesheet_directory_uri().'/css/admin/style-editor-custom.css');
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
//});
