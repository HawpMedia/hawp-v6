<?php
/**
 * The child header file.
 */

get_template_part('parts/header/head');
?>

<header id="header" class="header contain" role="banner">

	<?php echo do_shortcode('[logo class="logo"]'); ?>

	<?php get_template_part('parts/header/header-menu-buttons'); ?>

</header>

<?php get_template_part('parts/header/header-menu'); ?>

<main id="main" role="main">

<span class="sentry"></span>
