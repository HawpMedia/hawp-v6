<?php
/**
 * The child header file.
 *
 * @since 5.0.7
 */

get_template_part('parts/header/head');
?>

<nav class="menu-buttons">
	<a class="menu-button menu-home" href="<?php echo esc_url(home_url()); ?>"><span class="menu-button-icon fa fa-home"></span><span class="menu-button-label">Home</span></a>
	<a class="menu-button menu-contact" href="tel:<?php echo do_shortcode('[phone format="raw" dial_code="1"]'); ?>"><span class="menu-button-icon fa fa-phone"></span><span class="menu-button-label">Call</span></a>
	<a class="menu-button menu-open js-turnon" href="#menu" data-target="#menu"><span class="menu-button-icon fa fa-bars"></span><span class="menu-button-label">Menu</span></a>
	<a class="menu-button menu-close js-turnoff" href="#main" data-target="#menu"><span class="menu-button-icon fa fa-times"></span><span class="menu-button-label">Close Menu</span></a>
</nav>

<header id="header" class="header contain" role="banner">
	<?php echo do_shortcode('[logo class="logo"]'); ?>
</header>

<nav id="menu" data-target="#menu" class="menu-tab" role="navigation">
	<?php wp_nav_menu(array(
		'theme_location'=>'primary',
		'menu_id'=>'primary-menu',
		'menu_class'=>'menu',
	)); ?>
	<ul class="sub-menu menu-search-form">
		<li><?php get_search_form(); ?></li>
	</ul>
</nav>

<main id="main" role="main">

<span class="sentry"></span>
