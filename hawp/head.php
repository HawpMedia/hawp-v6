<?php
/**
 * The header opening template.
 *
 * @since 4.1.2
 */
?>
<!doctype html>
<html <?php language_attributes(); ?> class="hawp5 nojs">
<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-to-content" href="#main">Skip to content</a>

<div id="wrapper">
