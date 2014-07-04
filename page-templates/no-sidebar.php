<?php
/**
 * Template name: Página sem sidebar
 *
 * @package Guarani
 * 
 */

get_header(); ?>

		<div id="primary" class="content-area no-sidebar">
			<div id="content" class="site-content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content .site-content -->
		</div><!-- #primary .content-area -->

<?php get_footer(); ?>