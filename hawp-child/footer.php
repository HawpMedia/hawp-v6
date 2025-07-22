<?php
/**
 * The child footer template file.
 */
?>

</main>

<footer id="footer" class="footer" role="contentinfo">

	<?php get_template_part('parts/layout/footer-widgets'); ?>

	<div class="contain site-info">
		<div class="copyright">
			Copyright &copy; <?php echo date('Y'); ?> <?php echo shortcode_exists('company') ? do_shortcode('[company]') : get_bloginfo('name') ?>. All Rights Reserved.
		</div>
		<?php echo has_nav_menu('copyright') ? '<nav id="copyright-navigation" class="copyright-navigation" role="navigation">'.wp_nav_menu(array('theme_location' => 'copyright', 'echo' => false )).'</nav>' : ''; ?>
	</div>

</footer>
<?php get_template_part('parts/layout/foot'); ?>

