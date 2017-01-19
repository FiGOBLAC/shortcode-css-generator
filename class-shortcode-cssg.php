<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.figoblac.com
 * @since      1.0.0
 *
 * @package    Shortcode_CG
 * @subpackage Shortcode_CG/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Shortcode_CG
 * @subpackage Shortcode_CG/public
 * @author     FiGO BLAC <figo@figoblac.com>
 */
class Church_Core_Shortcode_CSSG {


    /**
     * The shortcodes styles set for each shortcode.
     * 
     * @since    1.0.0
     * @access   public
     */
    protected $styles;

    /**
     * The shortcodes styles set for each shortcode.
     * 
     * @since    1.0.0
     * @access   public
     */
    protected $shortcode_styles;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
        
        $this->shortcode_styles = array();

        $this->set_file_paths();
        $this->get_registered_properties();

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	private function set_file_paths() {

        // The parent directory.
        $this->scssg_dir =  dirname( __FILE__ , 2 ). '/cssg';

        // Configurations for file paths.
        $file_path_configs = file_get_contents(  $this->scssg_dir . '/json/configs.json' );

        // File path configs converted to array.
        $file_paths = json_decode( $file_path_configs, TRUE );

        // The location of the generated css.
        $this->css_dir = $this->scssg_dir . $file_paths['css_file_path'];

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	private function get_registered_properties() {

        $registered_properties = file_get_contents(  $this->scssg_dir . '/json/css.json' );

        $this->registered_properties = (array) json_decode( $registered_properties );

	}
    
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	private function setup_shortcode_vars( $shortcode, $defaults ) {
          
        // Store shortcode name
        $this->shortcode = $shortcode;
        
        // Shortcode defaults.
        $this->shortcode_defaults = $defaults;

        // Sir, Maam... I need to see some id.
        $this->shortcode_id = '#' . $this->shortcode_defaults['id'];

	}
     /**
     * Convert shortcode style settings into css properties.
     * 
     * 
     * @since    1.0.0
     * @access   private
     * 
     * @param   array $shortcode     The name of the shortcode.
     * @param   array $defaults      The shortcode's defaults.
     */
    private function extract_search_flag( $string ) {
        
        if( ! strpos( $string, '{') ) {
            return false;
        }
        
        $pos1 = strpos( $string , '{');
        $pos2 = strpos( $string , '}');
        
        $startIndex = min( $pos1, $pos2 );
        $length = abs( $pos1 - $pos2 ) + 1;

        $flag = substr( $string, $startIndex, $length );
        
        return $flag;
    }   
    
     /**
     * Convert shortcode style settings into css properties.
     * 
     * 
     * @since    1.0.0
     * @access   private
     * 
     * @param   array $shortcode     The name of the shortcode.
     * @param   array $defaults      The shortcode's defaults.
     */
    private function search_and_replace_flags( &$css_value, $css_property ) {
        
        // Extract out the flag from the option's output value.
        $flag = $this->extract_search_flag( $css_value ); 
        
        // Extract the property name from the flag.
        $property_name = str_replace( array( '{','}' ), '', $flag );  var_dump($this->shortcode_defaults );
        
         // Replace the flag with value shortcode value from the user. // Todo: Add custom error message.
        if( false !== $flag && key_exists( $property_name, $this->shortcode_defaults ) ) {
            $css_value = str_replace( $flag, $this->shortcode_defaults[ $property_name ], $css_value );var_dump( $css_value );
        } 
    }

     /**
     * Convert shortcode style settings into css properties.
     * 
     * 
     * @since    1.0.0
     * @access   private
     * 
     * @param   array $shortcode     The name of the shortcode.
     * @param   array $defaults      The shortcode's defaults.
     */
    private function convert_properties_to_css( $shortcode_defaults, $registered_properties, $shortcode_css ) {

        // Current shortcode.
        $shortcode = $this->shortcode;
        
        // Shortcode css selector.
        $shortcode_id = '#' . $shortcode_defaults['id'];

        // List of property => selector pairs.
        $registered_properties = array_intersect_key( $registered_properties , $shortcode_css );

        // Property selectors.
        $css_selectors = [];

        // Contstruct and store css property : value declarations.
        foreach( $shortcode_css as $css_property => $css_value ) {

            // Are you a string? If not, go home.
            if( ! is_string ( $registered_properties[ $css_property ] ) ){
                continue;
            }

            // Stitch together the propoerty selector.
            $selector = !empty( $registered_properties[ $css_property ] ) ? $shortcode_id . ' ' . $registered_properties[ $css_property ] : $shortcode_id;

            if(  strpos( $selector , '__' ) ){

                $css_selector =  strstr( $selector, '__' , true );

                // Get the property assigned to the selector.
                $css_property = strstr( $selector, '__' , false );

                // Remove all the underscores.
                $css_property = str_replace( '__' , '', $css_property );

                $selector = $css_selector;
            }

            if( array_key_exists ( $selector , $css_selectors ) ){
                $css_selectors[ $selector ][] = $css_property . ':' . $css_value .';';
            }

            if( ! array_key_exists ( $selector , $css_selectors ) ){
                $css_selectors[ $selector ] = array ( $css_property . ':' . $css_value .';' );
            }
        }

        array_walk( $css_selectors , function ( &$declaration , $selector  ) {
            $declaration =  $selector . '{'. implode( $declaration ). '}';
        });

        $shortcode_css = implode( $css_selectors );

        return $shortcode_css ;

    }

    /**
     * Convert shortcode style settings into css properties.
     *
     * 
     * @since    1.0.0
     * @access   private
     * 
     * @param   array $shortcode     The name of the shortcode.
     * @param   array $defaults      The shortcode's defaults.
     */
    private function generate_shortcode_css() {

        // Sir, Maam... I need to see some id.
        if( empty( $this->shortcode_defaults['id'] ) ) {
            return;
        }

        // Get the defaults;
        $shortcode_defaults = $this->shortcode_defaults;

        // Registered css properties.
        $registered_properties = $this->registered_properties;

        // Create an id used to identify each shortcode's style setting.
        $shortcode_styles_id =  $this->shortcode . '_' . $shortcode_defaults['id'];
        
        // Extract all the css properties and values that matches registered properties.
        $shortcode_css  = array_intersect_key( $shortcode_defaults, $registered_properties );

        // Remove css properties with no values.
        $shortcode_css  = array_filter( $shortcode_css );
        
        if( empty( $shortcode_css ) ) {
            return;
        }
        
        // Generata the css from custom property configurations.
        $custom_property_css = $this->convert_properties_to_css( $shortcode_defaults, $registered_properties, $shortcode_css );
        
        // Generata the css from custom object configurations.
        $custom_object_css = $this->convert_custom_object_to_css( $registered_properties, $shortcode_css ); var_dump( $custom_object_css );

        $shortcode_css = $custom_property_css . $custom_object_css;

        // Store the css styles that was set for the shortcode.
        $this->styles[ $shortcode_styles_id ] = $shortcode_css;

        if( array_key_exists( $shortcode_styles_id, $this->styles ) ) {

            if( empty( $this->shortcode_styles ) ) :

            $this->shortcode_styles = $this->styles[ $shortcode_styles_id ];

            else:

            $this->shortcode_styles =  $this->shortcode_styles . $this->styles[ $shortcode_styles_id ] ;

            endif;

        }
    }

    
    /**
     * Converts custom style configurations into
     * proper css declarations.
     * 
     * @since    1.0.0
     * @access   private
     * 
     * @param  $shortcode    The name of the shortcode.
     * @param  $defaults     The shortcode defaults.
     * @return               Shortcode css styles.
     */
    private function convert_custom_object_to_css(  $registered_properties, $shortcode_css ) {
        
       foreach( $shortcode_css as $css_property => $css_value ) {

           // Get the objects css configuration settings.
           $configuration =  $registered_properties[ $css_property ];

           // Are you an object? If not, go home.
           if( ! is_object( $configuration ) ){
               continue;
           }

           // Get the option value selected by the user.
           $user_option = $css_value;

           // name of the current shortcode.
           $shortcode = $this->shortcode;

           // name of the current shortcode
           $shortcode_id = $this->shortcode_id;

           // Check if elements property exists and if this shortcode is assigned to elements.
           $elements_set =  property_exists( $configuration, 'elements' ) && property_exists( $configuration->elements, $shortcode );

           // Get the target elements specific to the current shortcode if they exists.
           $local_elements = $elements_set && ! empty ( $configuration->elements->$shortcode ) ? $configuration->elements->$shortcode : FALSE;

           // Check if elements are Globally set.
           $global_elements_set = property_exists( $configuration, 'elements' ) && property_exists( $configuration->elements, 'all' );
//
            // get the target elements used by all shortcodes if they exists.
           $global_elements = $global_elements_set ? $configuration->elements->all : FALSE;

           // Check whether element are active
           $has_elements = ( ! empty( $local_elements ) ) || ! ( empty( $global_elements ) );

           // Check if restrictions property exists and if this shortcode is assigned to restrictions.
           $restrict_set =  property_exists( $configuration, 'restrictions' ) && property_exists( $configuration->restrictions, $shortcode );

           // Make sure restriction are properly formated as an array and check if restrictions exists for this shortcode.
           $has_restrictions = $restrict_set && is_array( $configuration->restrictions->$shortcode ) && ( ! empty( $configuration->restrictions->$shortcode ) );

           // Check if current user option is allowed.
           $is_restricted = ( $has_restrictions && ( ! in_array( $user_option, $configuration->restrictions->$shortcode ) ) );

           // Get the css declaration selected for the current shortcode.
           $declaration = property_exists( $configuration->declarations, $user_option ) ? (array)$configuration->declarations->$user_option : FALSE;

           if( ( FALSE == $declaration ) || ( ! $has_elements ) ){
               return;
           } var_dump( $declaration );

           // Convert search flags into values from shortcode defaults
           array_walk( $declaration, array( $this, 'search_and_replace_flags') ) ;

           foreach( $declaration as $css_property => $css_value ) {
               $css_declaration[] = "$css_property:$css_value;";
           }

           // CSS for specific shortcodes.
           $local_css      = ! empty( $local_elements ) ? $shortcode_id . ' '. $local_elements . '{' . implode( $css_declaration ) . '}' : FALSE;

           // CSS used by all shortcodes.
           $global_css     = ! empty( $global_elements ) ? $shortcode_id . ' '. $global_elements . '{' . implode( $css_declaration ) . '}' : FALSE;

           // Combine css declatrations.
           $css_declaration = ( ! $is_restricted ) ? $local_css . $global_css : FALSE;

           $css[] = ! empty( $css_declaration ) ? sprintf( $css_declaration, $shortcode_id ) : FALSE;
       }

        return implode( $css );
    }
        
     /**
      * Generates the css for all shortcodes.
      *                         
      * @since  1.0.0
      * @acces  public
      */   
    public function shortcode_cssg( $shortcode, $defaults ) { 
        
        // Shared variables
        $this->setup_shortcode_vars( $shortcode, $defaults );
        
        // Get styles generated for the current shortcode.
        $this->generate_shortcode_css();

        // Get all styles generated for the shortcode.
        $styles = $this->shortcode_styles;
                
        // One rediculously long string of css declarations coming up...
        $styles = ! empty( $styles ) ? $styles : false; 
        
        // Get the theme options class instance using RF's beautiful proxy function.
        $proxy = ReduxFrameworkInstances::get_instance( 'TC_Options' );

        // Almost there... Just need to apply some filters.
        $styles = apply_filters( 'filter_generated_shortcode_css', $styles );        
        
        // Officially assign the content. 
        $shortcode_css = array( 'content' => $styles );
        
        // And Voila.. Add the contents to the css file.
        $proxy->filesystem->execute( 'put_contents', $this->css_dir, $shortcode_css );
    }
}
