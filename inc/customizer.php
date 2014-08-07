<?php
/**
 * Guarani Customizer
 *
 * @since Guarani 1.0
*/

/**
 * Returns an array of color schemes registered for guarani.
 *
 * @since guarani 1.0
 */
function guarani_color_schemes() {
	$color_scheme_options = array(
		'default' => array(
			'value' => 'default',
			'label' => __( 'Default', 'guarani' ),
			'thumbnail' => '',
			'default_link_color' => '#222222',
		),
		'clean' => array(
			'value' => 'clean',
			'label' => __( 'Clean', 'guarani' ),
			'thumbnail' => '',
			'default_link_color' => '#333333',
		),
		'eco' => array(
			'value' => 'eco',
			'label' => __( 'Eco', 'guarani' ),
			'thumbnail' => '',
			'default_link_color' => '#589d8f',
		)
	);

	return apply_filters( 'guarani_color_schemes', $color_scheme_options );
}


/**
 * Returns the default options for guarani.
 *
 * @since guarani 1.0
 */
function guarani_get_default_theme_options() {
	$default_theme_options = array(
		'color_scheme' => 'default',
		'link_color'   => '#000'
	);

	return apply_filters( 'guarani_default_theme_options', $default_theme_options );
}


/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see guarani_theme_options_init()
 * @todo set up Reset Options action
 *
 * @since guarani 1.0
 */
function guarani_theme_options_validate( $input ) {
	$output = $defaults = guarani_get_default_theme_options();

	// Color scheme must be in our array of color scheme options
	if ( isset( $input['color_scheme'] ) && array_key_exists( $input['color_scheme'], guarani_color_schemes() ) )
		$output['color_scheme'] = $input['color_scheme'];

	// Our defaults for the link color may have changed, based on the color scheme.
	$output['link_color'] = $defaults['link_color'] = guarani_get_default_link_color( $output['color_scheme'] );

	// Link color must be 3 or 6 hexadecimal characters
	if ( isset( $input['link_color'] ) && preg_match( '/^#?([a-f0-9]{3}){1,2}$/i', $input['link_color'] ) )
		$output['link_color'] = '#' . strtolower( ltrim( $input['link_color'], '#' ) );


	return apply_filters( 'guarani_theme_options_validate', $output, $input, $defaults );
}

/**
 * Enqueue the styles for the current color scheme.
 *
 * @since guarani 1.0
 */
function guarani_enqueue_color_scheme() {
	$color_scheme = get_theme_mod( 'guarani_color_scheme' );

	if ( $color_scheme != '' && $color_scheme !== 'default' )
		wp_enqueue_style( $color_scheme, get_template_directory_uri() . '/css/schemes/' . $color_scheme . '.css', array(), null );

	do_action( 'guarani_enqueue_color_scheme', $color_scheme );
}
add_action( 'wp_enqueue_scripts', 'guarani_enqueue_color_scheme' );


/**
 * Implements guarani theme options into Theme Customizer
 *
 * @param $wp_customize Theme Customizer object
 * @return void
 *
 * @since guarani 1.3
 */
function guarani_customize_register( $wp_customize ) {

	/**
	 * Customize Image Reloaded Class
	 *
	 * Extende WP_Customize_Image_Control e permite o acesso aos uploads
	 * feitos dentro do mesmo contexto
	 * 
	 */
	class WP_Customize_Image_Reloaded_Control extends WP_Customize_Image_Control {
		/**
         * Constructor.
         *
         * @since 3.4.0
         * @uses WP_Customize_Image_Control::__construct()
         *
         * @param WP_Customize_Manager $manager
         */
        public function __construct( $manager, $id, $args = array() ) {
                
                parent::__construct( $manager, $id, $args );
                       
        }

        /**
         * Busca as imagens de acordo com o contexto definido
         * Não havendo contexto, são trazidas todas as imagens
         * 
         */
        public function tab_uploaded() {
            $custom_logos = get_posts( array(
                    'post_type'  => 'attachment',
                    'meta_key'   => '_wp_attachment_context',
                    'meta_value' => $this->context,
                    'orderby'    => 'post_date',
                    'nopaging'   => true,
            ) );

            ?>
            
            <div class="uploaded-target"></div>
            
            <?php
            if ( empty( $custom_logos ) )
                    return;

            foreach ( (array) $custom_logos as $custom_logo )
            	$this->print_tab_image( esc_url_raw( $custom_logo->guid ) );
        }

	}
	
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	$defaults = guarani_get_default_theme_options();
	
	/*
	 * Site title & tagline
	 */
	// Option to display or hide header text
	$wp_customize->add_setting( 'guarani_display_header_text', array(
		'capability' => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'guarani_display_header_text', array(
		'label'    => __( 'Display header text', 'guarani' ),
		'section'  => 'title_tagline',
		'type'     => 'checkbox',
		'settings' => 'guarani_display_header_text'
	) );
	
	// Centering header
	$wp_customize->add_setting( 'guarani_center_header', array(
		'capability' => 'edit_theme_options',
	) );

	$wp_customize->add_control( 'guarani_center_header', array(
		'label'    => __( 'Center header', 'guarani' ),
		'section'  => 'title_tagline',
		'type'     => 'checkbox',
		'settings' => 'guarani_center_header'
	) );

	/*
	 * Color scheme
	 * Placed inside the "Colors" section
	 */
	$wp_customize->add_setting( 'guarani_color_scheme', array(
		'default'    => $defaults['color_scheme'],
		'capability' => 'edit_theme_options',
	) );

	$schemes = guarani_color_schemes();
	
	$choices = array();
	
	foreach ( $schemes as $scheme ) {
		$choices[ $scheme['value'] ] = $scheme['label'];
	}

	$wp_customize->add_control( 'guarani_color_scheme', array(
		'label'    => __( 'Color Scheme', 'guarani' ),
		'section'  => 'colors',
		'type'     => 'radio',
		'choices'  => $choices,
		'priority' => 5,
		'settings' => 'guarani_color_scheme'
	) );
	
	
	$color = array( 'slug'=>'guarani_front_content_bg_color', 'default' => '#ffffff', 'label' => __( 'Cor de fundo do conteúdo', 'guarani' ) );
	$wp_customize->add_setting( $color['slug'], array( 'default' => $color['default'], 'type' => 'option', 'capability' => 'edit_theme_options', 'transport'=>'postMessage' ));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $color['slug'], array( 'label' => $color['label'], 'section' => 'colors', 'settings' => $color['slug'] )));
	
	
	/*
	 * Branding
	 * Logo, favicon, default image
	 */ 
	$wp_customize->add_section( 'guarani_branding', array(
		'title'    => __( 'Branding', 'guarani' ),
		'priority' => 30,
	) );
	
	// Branding: logo
	$wp_customize->add_setting( 'guarani_logo', array(
		'default'     => get_template_directory_uri() . '/images/schemes/logo-default.png',
		'capability'    => 'edit_theme_options',
	) );
	
    $wp_customize->add_control( new WP_Customize_Image_Reloaded_Control( $wp_customize, 'guarani_logo', array(
        'label'   	=> __( 'Cabeçalho', 'guarani' ),
        'section'	=> 'guarani_branding',
        'settings' 	=> 'guarani_logo',
        'context'	=> 'guarani-custom-logo'
    ) ) ); 
    
    // Branding: favicon
    $wp_customize->add_setting( 'guarani_favicon', array(
		//'default'     => ???,
		'capability'    => 'edit_theme_options',
	) );
    
    $wp_customize->add_control( new WP_Customize_Image_Reloaded_Control( $wp_customize, 'guarani_favicon', array(
        'label'   	=> __( 'Favicon (icone da barra de navegação/aba)', 'guarani' ),
        'section'	=> 'guarani_branding',
        'settings' 	=> 'guarani_favicon',
        'context'	=> 'guarani-favicon'
    ) ) );

	// Comments FB
	$wp_customize->add_section( 'guarani_fb_comments', array(
			'title'    => __( 'Comentários via Facebook', 'guarani' ),
			'priority' => 30,
	) );
	$wp_customize->add_setting( 'guarani_display_fb_comments', array(
			'capability' => 'edit_theme_options',
	) );
	
	$wp_customize->add_control( 'guarani_display_fb_comments', array(
			'label'    => __( 'Exibe a caixa de comentários do Facebook', 'guarani' ),
			'section'  => 'guarani_fb_comments',
			'type'     => 'checkbox',
			'settings' => 'guarani_display_fb_comments'
	) );
    
    /*
	 * Typography
	 */
	
	$wp_customize->add_section( 'guarani_typography', array(
		'title'    => __( 'Typography (beta)', 'guarani' ),
		'priority' => 30,
	) );
   
    $wp_customize->add_setting( 'guarani_font_main', array(
       'sanitize_callback' => 'guarani_font_values'
    ) );

    $wp_customize->add_control( 'guarani_font_main', array(
        'label'    => __( 'Main Font', 'guarani' ),
        'section'  => 'guarani_typography',
        'type'     => 'select',
        'choices'  => array(
        	'default' 				=> __( 'Default', 'guarani' ),
        	'Amaranth:400,700' 		=> __( 'Amaranth', 'guarani' ),
        	'Arvo:400,700,400' 		=> __( 'Arvo', 'guarani' ),
        	'Lato:400,700' 			=> __( 'Lato', 'guarani' ),
        	'Noticia+Text:400,700' 	=> __( 'Noticia Text', 'guarani' ),
        	'Special+Elite:400' 	=> __( 'Special Elite', 'guarani' )
 		),
        'priority' => 5,
        'setting' => 'guarani_font_main'
    ) );

	if(function_exists('mapasdevista_view'))
	{
	    $wp_customize->add_section( 'guarani_map', array(
	    		'title'    => __( 'Mapa', 'guarani' ),
	    		'priority' => 30,
	    ) );
	    $wp_customize->add_setting( 'guarani_display_home_map', array(
	    		'capability' => 'edit_theme_options',
	    ) );
	    
	    $wp_customize->add_control( 'guarani_display_home_map', array(
	    		'label'    => __( 'Display Map at Home Page', 'guarani' ),
	    		'section'  => 'guarani_map',
	    		'type'     => 'checkbox',
	    		'settings' => 'guarani_display_home_map'
	    ) );
	    $wp_customize->add_setting( 'guarani_display_home_map_filters', array(
	    		'capability' => 'edit_theme_options',
	    ) );
	     
	    $wp_customize->add_control( 'guarani_display_home_map_filters', array(
	    		'label'    => __( 'Display Map Filters at Home Page', 'guarani' ),
	    		'section'  => 'guarani_map',
	    		'type'     => 'checkbox',
	    		'settings' => 'guarani_display_home_map_filters'
	    ) );
	}
    
}
add_action( 'customize_register', 'guarani_customize_register' );


/**
 * Get dropdown value (font + its weight) and transform in an array
 * 
 * @param  string $value The select value
 * @return array $value An array with the font code, its weight and its name
 */
function guarani_font_values( $value ) {

    if ( $value == 'default' || is_array( $value ) )
    	return $value;

    // Font URL
    $new_value['url'] = $value;

	// Name
	$font_string = strstr( $value, ':', true );
    $new_value['name'] = str_replace( '+', ' ', $font_string );

    // Font weight
    $new_value['weight'] = strstr( $value, ':' );

    $value = $new_value;

    return $value;
}


/**
 * This will output the custom WordPress settings to the live theme's WP head.
 * 
 * Used for inline custom CSS
 *
 * @since Guarani 1.0
 */
function guarani_customize_css()
{
	?>
	<!-- Customize CSS -->
	<?php
	// Main font
	$font_css = get_theme_mod( 'guarani_font_main' );

	//print_r($font_css);

	if ( is_array( $font_css ) ) : ?>
		<link href='http://fonts.googleapis.com/css?family=<?php echo $font_css['url']; ?>' rel='stylesheet' type='text/css'>
		<style type="text/css">
			h1,
			h2,
			h3,
			h4,
			h5,
			h6,
			blockquote,
			.main-navigation a,
			.entry-meta,
			.entry-title,
			.site-content .site-navigation,
			.comment-author,
			.widget-title,
			.eletro_widgets_content h2 {
				font-family: <?php echo $font_css['name']; ?>, serif;
			}
		</style>
	<?php
	endif;
	?>
	<style type="text/css">

		<?php if ( get_theme_mod( 'guarani_display_header_text' ) == '' ) : ?>
		/* Header text */
		.site-title,
		.site-description {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
		<?php endif; ?>
		
		<?php if ( get_theme_mod( 'guarani_center_header' ) == 1 ) : ?>
		.site-header > * {
			text-align: center;
		}
		<?php endif; ?>
	</style> 
	<!-- /Customize CSS -->
	<?php
}
add_action( 'wp_head', 'guarani_customize_css' );


/**
 * Bind JS handlers to make Theme Customizer preview reload changes asynchronously.
 * Used with blogname and blogdescription.
 *
 * @since Guarani 1.0
 */
function guarani_customize_preview_js() {
	wp_enqueue_script( 'guarani-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20120523', true );
	wp_enqueue_script( 'guarani-customizer-map', get_template_directory_uri() . '/js/customizer-map.js', array( 'jquery' ), '20140410', true );
}
add_action( 'customize_preview_init', 'guarani_customize_preview_js' );


/**
 * Add Customizer link to both main menu & themes page
 * 
 */
function guarani_admin_customizer_menu_link() {

	global $menu, $submenu;

    $customizer_menu = false;
    $customizer_link = 'customize.php';
    
    // Check if there's already a submenu for customize.php
    foreach( $submenu as $k => $item ) {
	    foreach( $item as $sub ) {
	    	if( $customizer_link == $sub[2] ) {
	        	$customizer_menu = true;
	      	}
	    }
    }

    if ( ! $customizer_menu && current_user_can( 'edit_theme_options' ) ) {
	    add_theme_page( __( 'Customize', 'default' ), __( 'Customize', 'default' ), 'edit_theme_options', $customizer_link );
	}

}
add_action ( 'admin_menu', 'guarani_admin_customizer_menu_link', 99 );
?>