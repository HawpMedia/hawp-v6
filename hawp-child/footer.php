<?php
/**
 * The child footer template file.
 *
 * @since 5.1.0
 */
?>

</main>

<footer id="footer" class="footer" role="contentinfo">

	<aside class="contain widget-area">
		<?php if (is_active_sidebar('footer')) {
			dynamic_sidebar('footer');
		} ?>
	</aside>

	<div class="contain site-info">
		<div class="copyright">
			Copyright &copy; <?php echo date('Y'); ?> <?php echo shortcode_exists('company') ? do_shortcode('[company]') : get_bloginfo('name') ?>. All Rights Reserved.
		</div>
		<?php echo has_nav_menu('copyright') ? '<nav id="copyright-navigation" class="copyright-navigation" role="navigation">'.wp_nav_menu(array('theme_location' => 'copyright', 'echo' => false )).'</nav>' : ''; ?>
	</div>

</footer>

<?php get_template_part('foot'); ?>
