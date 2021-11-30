<?php
/**
 * The child front page template file.
 */

get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class('main-content main-front'); ?>>

		<?php echo the_content(); ?>

	</article>

<?php endwhile; ?>

<?php get_footer(); ?>
