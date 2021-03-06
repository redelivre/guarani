<?php
/**
 * The template for displaying Archive for Post Type Agenda
 *
 * @package Guarani
 * @since Guarani 1.0
 */
 
global $paged;
$showing_past = ($paged > 0 || (array_key_exists('eventos', $_GET) && $_GET['eventos'] == 'passados'));

get_header(); ?>

	<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
		
		<?php 
		/*
		if ($showingPast): ?>
				<h2 class="clearfix">
					Eventos Passados
					<a class="view-events" href="<?php echo add_query_arg('eventos', ''); ?>">Ver próximos eventos &raquo;</a>
				</h2>
			<?php else: ?>
				<h2 class="clearfix">
					Próximos eventos
					<a class="view-events" href="<?php echo add_query_arg('eventos', 'passados'); ?>">Ver eventos passados &raquo;</a>
				</h2>
			<?php endif; ?>
	    
            <?php if ( have_posts()) : ?>
            
                <?php get_template_part('loop', 'agenda'); ?>
			
            <?php else : ?>
                    <p class="post"><?php _e('No results found.', 'blog01'); ?></p>              
            <?php endif;
            */ ?>

            
            
		<?php if ( have_posts() ) : ?>

			<header class="archive-header archive-agenda-header">
				<?php  if ( $showing_past ) : ?>
					<h1 class="archive-title agenda-title"><?php _e( 'Past events', 'guarani' ); ?></h1>
					<a class="view-events" href="<?php echo add_query_arg( 'eventos', '' ); ?>"><?php _e( 'View next events &raquo;', 'guarani' ); ?></a>
				<?php else: ?>
					<h1 class="archive-title agenda-title"><?php _e( 'Next events', 'guarani' ); ?></h1>
					<a class="view-events" href="<?php echo add_query_arg( 'eventos', 'passados' ); ?>"><?php _e( 'View past events &raquo;', 'guarani' ); ?></a>
				<?php endif; ?>
			</header><!-- .archive-header -->
			
			<ul class="agenda-events-list">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					//get_template_part( 'content', get_post_format() );
				?>
				
				<?php if ( $date_start = get_post_meta( $post->ID, '_data_inicial', true ) ) : ?>
				<li>
					<span aria-hidden="true" class="icon-calendar"></span>
					<?php
					$date_end = get_post_meta( $post->ID, '_data_final', true );
					if ( $date_end && $date_end != $date_start ) :
						/* translators: Initial & final date for the event */
						printf(
							'%1$s to %2$s',
							date( get_option( 'date_format' ), strtotime( $date_start ) ),
							date( get_option( 'date_format' ), strtotime( $date_end ) )
						);
					else :
						echo date( get_option( 'date_format' ), strtotime( $date_start ) );
					endif;
					?>
				
				<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'guarani' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
				</li>
				<?php endif; ?>
				

			<?php endwhile; ?>
			
			</ul><!-- .agenda-events-list -->

			<?php guarani_content_nav( 'nav-below' ); ?>

		<?php else : ?>

			<?php get_template_part( 'no-results', 'archive' ); ?>

		<?php endif; ?>

		</div><!-- #content .site-content -->
	</section><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>