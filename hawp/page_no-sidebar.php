<?php
/**
 * Template Name: No Sidebar Page
 * Template Post Type: post, page
 */

get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

	<article id="page-<?php the_ID(); ?>" <?php post_class('main-content main-no-sidebar main-'.get_post_type()); ?>>

		<header class="contain main-header">
			<?php get_template_part('parts/title/title', get_post_type()); ?>
			<?php if(is_singular()){ get_template_part('parts/meta/meta', get_post_type()); } ?>
		</header>

		<div class="contain entry-wrapper entry-no-sidebar">

			<div class="clear entry-content entry-<?php echo get_post_type(); ?>">

				<?php get_template_part('parts/content/content', get_post_type()); ?>

			</div>

		</div>

	</article>

<?php endwhile; ?>

<?php get_footer(); ?>
