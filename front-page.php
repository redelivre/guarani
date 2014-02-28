<?php
/**
 * Template Name: Front Page Template
 *
 * @since Guarani 1.0
 */
__( 'Front Page Template', 'guarani' );

get_header(); ?>
    
    
	<?php
	if( function_exists('mapasdevista_view') && get_theme_mod('guarani_display_home_map') == 1 )
	{?>
		<div class="map clear"><?php guarani_the_map(); ?></div>
		<?php /* <div class="map-filters clear"><?php guarani_the_map_filters(); ?></div><?php */
	} 
	else
	{
		$feature = new WP_Query( array( 'posts_per_page' => -1, 'ignore_sticky_posts' => 1, 'meta_key' => '_home', 'meta_value' => 1 ) );
		
	    if ( $feature->have_posts() ) : ?>
	    
	    <div class="flexslider highlights">
	        <ul class="slides">
		        <?php while ( $feature->have_posts() ) : $feature->the_post(); ?>
			        <li>
				        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				        	<div class="media slide cf">
				    			<?php if ( has_post_thumbnail() ) : ?>
				    			<figure class="img entry-image">
				    			<?php the_post_thumbnail( 'highlight' ); ?>
				    			</figure>
				    			<?php endif; ?>
				        		
				        		<div class="bd">
				        			<div class="entry-meta">
					        			<?php $category = get_the_category(); ?>
										<a href="<?php echo get_category_link( $category[0]->term_id ); ?>"><?php echo $category[0]->cat_name; ?></a>
									</div>
				        			<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				        			<div class="entry-summary">
					        			<?php the_excerpt(); ?>
				        			</div>
				        		</div>
				        	</div><!-- /slide -->
				        </article><!-- /article -->
			        </li>
	        	<?php endwhile; ?>
	        	</ul><!-- .swiper-wrapper -->
	        	
	    </div><!-- .swiper-container -->
		
		<?php
		wp_reset_postdata();
		
		else : ?>
			<?php if ( current_user_can( 'edit_theme_options' ) ): ?>
				<div class="empty-feature">
	                <p><?php printf( __( 'To display your featured posts here go to the <a href="%s">Post Edit Page</a> and check the "Feature" box. You can select how many posts you want, but use it wisely.', 'guarani' ), admin_url('edit.php') ); ?></p>
				</div>
			<?php
			endif;
		endif; // have_posts()
	}
	?>
					
    <div class="container cf">
        <?php if ( class_exists( 'EletroWidgets' ) ) new EletroWidgets( 3, 'main' ); ?>
    </div><!-- /container -->

<?php get_footer(); ?>