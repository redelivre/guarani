<?php
/**
 * Guarani functions and definitions
 *
 * @package Guarani
 * @since Guarani 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Guarani 1.0
 */
if ( ! isset( $content_width ) )
	$content_width = 648;

if ( ! function_exists( 'guarani_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since Guarani 1.0
 */
function guarani_setup() {

	// Custom template tags for this theme.
	require( get_template_directory() . '/inc/template-tags.php' );
	
	// Custom functions & tweaks for Guarani
	require( get_template_directory() . '/inc/extras.php' );
	
	// Customizer
	require( get_template_directory() . '/inc/customizer.php' );
	
	// Featured Video function
	require( get_template_directory() . '/inc/featured-video.php' );
	
	//Implement the Custom Header feature
	//require( get_template_directory() . '/inc/custom-header.php' );
	
	// Customizer
	require( get_template_directory() . '/inc/hacklab_post2home/hacklab_post2home.php' );

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on Guarani, use a find and replace
	 * to change 'guarani' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'guarani', get_template_directory() . '/languages' );
	
	// Register menus
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'guarani' ),
	) );
	
	// Editor style
	add_editor_style();
	
	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Post Thumbnails
	add_theme_support( 'post-thumbnails' );
	
	// Custom Background
	add_theme_support( 'custom-background', array(
		'default-image' => get_stylesheet_directory_uri() . '/images/background.png'
	) );
	
	// Image sizes
	add_image_size( 'highlight', 500, 320, true );
	add_image_size( 'small-feature', 330, 9999 );
	add_image_size( 'highlight-single', 686, 400, true );
	
	// Default link type is file
	update_option( 'image_default_link_type','file' );	

	// Add suport for Post Formats
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'image', 'link', 'video' ) );
	
	// Call Theme Custom Widgets
	require( get_template_directory() . '/inc/widgets.php' );
	
	// Custom meta boxes
	require( get_template_directory() . '/inc/meta-box-team.php' );
	
	/**
	 * Debugging & testing
	 * This file is only meant for testing functionalities (e.g. Agenda post type)
	 */
	//require( get_template_directory() . '/inc/debugging.php' );
}
endif; // guarani_setup
add_action( 'after_setup_theme', 'guarani_setup' );

/**
 * Register widgetized area and update sidebar with default widgets
 *
 * @since Guarani 1.0
 */
function guarani_widgets_init() {
	register_sidebar( array(
		'name' 			=> __( 'Sidebar', 'guarani' ),
		'id' 			=> 'sidebar-main',
		'description'   => __( 'The main sidebar', 'guarani' ),
		'before_widget' => '<aside id="%1$s" class="widget-container %2$s">',
		'after_widget' 	=> '</aside>',
		'before_title' 	=> '<h3 class="widget-title">',
		'after_title' 	=> '</h3>',
	) );
	
	register_sidebar( array(
		'name' 			=> __( 'Footer Widget Area', 'guarani' ),
		'id' 			=> 'sidebar-footer',
		'description'   => __( 'Appears in the footer section of the site', 'guarani' ),
		'before_widget' => '<aside id="%1$s" class="widget-container %2$s">',
		'after_widget' 	=> '</aside>',
		'before_title' 	=> '<h3 class="widget-title">',
		'after_title' 	=> '</h3>',
	) );
}
add_action( 'widgets_init', 'guarani_widgets_init' );


/**
 * Enqueue scripts and styles
 *
 * @since Guarani 1.0
 */
function guarani_scripts() {

	// Normalize
	wp_register_style( 'normalize', get_template_directory_uri() . '/css/normalize.css', '', '1.0.2', 'all' );
	wp_enqueue_style( 'normalize' );

	// Default style
	wp_enqueue_style( 'style', get_stylesheet_uri() );

	// Small menu
	wp_enqueue_script( 'small-menu', get_template_directory_uri() . '/js/small-menu.js', array( 'jquery' ), '20120206', true );

	// Comment reply
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	
	// Keyboard navigation
	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
	
	// Swiper slider (http://www.idangero.us/sliders/swiper/)
	if ( is_front_page() ) {
		wp_enqueue_style( 'swiper', get_template_directory_uri() . '/css/flexslider.css' );
		wp_enqueue_script( 'swiper', get_template_directory_uri() . '/js/jquery.flexslider-min.js', array( 'jquery' ), '1.8', true );
	}
	
	// fancyBox (http://fancyapps.com/fancybox/)
	wp_enqueue_style( 'fancybox', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.css' );
	wp_enqueue_script( 'fancybox', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.pack.js', array( 'jquery' ), '2.1.4', true );
	wp_enqueue_script( 'responsive', get_template_directory_uri() . '/js/responsive.js');
	
	
}
add_action( 'wp_enqueue_scripts', 'guarani_scripts' );


/**
 * Call footer scripts
 * 
 * @since Guarani 1.0
 */
function guarani_footer_scripts() {
	
	if ( is_home() || is_front_page() ) :
	?>
	<!-- Swiper -->
	<script type="text/javascript">
		jQuery(function($){
			var slider = $('.flexslider').flexslider({
				animation: "slide",
				direction: "horizontal",
				smoothHeight: true,
				start: function(slider) {
					$('.flex-direction-nav li:first a').addClass('flexslider-guarani-prev');
					$('.flex-direction-nav li:last a').addClass('flexslider-guarani-next');
				}
			});
		});
	</script>
	<?php
	endif; 
	?>
	
	<!-- fancyBox -->
	<script type="text/javascript">
		jQuery(document).ready(function() {
		    jQuery('.hentry').find('a:has(img)').addClass('fancybox');
	        jQuery('.hentry').find('a:has(img)').attr('rel','gallery');
	        jQuery('a.fancybox').fancybox();
		});
	</script>
	<?php
	
}
add_action( 'wp_footer', 'guarani_footer_scripts' );


/**
 * Auto activate plugins
 *
 * @link http://wpengineer.com/2300/activate-wordpress-plugins-automatically-via-a-function/
 *
 * @since Guarani 1.0
 */
function guarani_activate_plugins() {
    
    /*if ( ! current_user_can( 'activate_plugins' ) )
        return;*/
        
    $plugins = array();
    $plugins = get_option( 'active_plugins' );
    
    if ( $plugins ) {
    
    	// The plugin list
        $pugins_to_active = array(
            'eletro-widgets/eletro-widgets.php', 
        );
        
        foreach ( $pugins_to_active as $plugin ) {
            if ( ! in_array( $plugin, $plugins ) ) {
                array_push( $plugins, $plugin );
                update_option( 'active_plugins', $plugins );
                do_action('activate_' . basename($plugin)); // TODO Não vai funcionar se o nome do plugin for diferente do nome do arquivo
            }
        }
        
        $plugins_ativados = get_option( 'active_plugins' );
        foreach ($pugins_to_active as $pugin_to_active)
        {
	        if(!in_array($pugin_to_active, $plugins_ativados))
	        {
	        	wp_die(__("Erro ao ativar plugins para funcionamento do tema", "guarani").print_r($plugins_ativados, true));
	        }
        }
        
        
        $role = get_role('administrator');
        $role->add_cap('manage_eletro_widgets');
    }
}
add_action( 'admin_init', 'guarani_activate_plugins' );


/**
 * Deactivate Eletro Widgets plugin removal
 *
 * @link http://wpmu.org/remove-plugin-deactivation-wordpress/
 * @since Guarani 1.0
 */
function lock_plugins( $actions, $plugin_file, $plugin_data, $context ) {
 
  if ( array_key_exists( 'deactivate', $actions ) && in_array( $plugin_file, array( 'eletro-widgets/eletro-widgets.php' ) ) )
    unset( $actions['deactivate'] );
    
  return $actions;
  
}
add_filter( 'plugin_action_links', 'lock_plugins', 10, 4 );

function guarani_fb_comments_box() {

	$fb_appid = get_theme_mod( 'guarani_facebook_appid' );
	?>

	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&appId=<?php echo $fb_appid; ?>&version=v2.0";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	
	<script>
		var siteLeadUrl = "<?php echo get_template_directory_uri() . "/js/site-lead.js" ?>";
		Modernizr.load({
	        test: Modernizr.mq("only screen and (min-width:64.063em)"),
	        yep: siteLeadUrl
		});
	</script>

	<?php
}
add_action( 'wp_footer', 'guarani_fb_comments_box' );

/**
 * Add Facebook OpenGraph meta properties
 */
function guarani_fb_opengraph() {
	$fb_appid = get_theme_mod( 'guarani_facebook_appid' );

	if ( ! empty ( $fb_appid ) ) {
		echo '<meta property="fb:app_id" content="' . $fb_appid . '"/>';
	}	
}
add_action( 'wp_head', 'guarani_fb_opengraph' );


?>