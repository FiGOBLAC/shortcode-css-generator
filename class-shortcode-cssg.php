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
    protected $shortcode_excludes;
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
     * Stores the configurations used to create custom
     * css declaraation targeted at specific elements.
     *
     * Current allows sonly one css property per 
     * declaration.
     * 
     * @since    1.0.0
     * @access   public
     */
    protected $custom_css_properties; 

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
        
        $this->shortcode_styles = array();       
        $this->shortcode_excludes= array();

        $this->set_file_paths();
        $this->get_css_properties();                
        $this->get_custom_css_property_configs();                

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
	private function get_css_properties() {

        $registered_properties = file_get_contents(  $this->scssg_dir . '/json/css.json' );

        $this->registered_properties = ( json_decode( $registered_properties, true ) );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	private function get_custom_css_property_configs() {

        // Custom CSS property configurations file.
        $css_property_configs = file_get_contents(  $this->scssg_dir . '/json/css-config.json' );

        // Decode Custom CSS property configurations file.
        $this->custom_css_properties = (array) json_decode( $css_property_configs );

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
        
        $flag = $this->extract_search_flag( $css_value ); 
        
        // Extract the property name from the flag.
        $property_name = str_replace( array( '{','}' ), '', $flag );
        
        if( FALSE !== $flag ) { // Todo: Add custom error message.
                     
        // Replace the flag with value shortcode value from the user.
        $css_value = str_replace( $flag, $this->shortcode_defaults[ $property_name ], $css_value );
        
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
    private function generate_shortcode_css() {

        // Sir, Maam... I need to see some id.
        if( empty( $this->shortcode_defaults['id'] ) ) :
        return;
        endif;

        
        // Current shortcode.
        $shortcode = $this->shortcode;
        
        // Get the defaults;
        $shortcode_defaults = $this->shortcode_defaults;

        // Registered css properties.
        $css_properties = $this->registered_properties;
        
        // Extract all the css properties and values that matches registered properties.
        $shortcode_css  = array_intersect_key( $shortcode_defaults, $css_properties );

        // Remove css properties with no values.
        $shortcode_css  = array_filter(  $shortcode_css ); 
        
        // Shortcode css selector.
        $css_selector = '#' . $shortcode_defaults['id'];

        // Create an id used to identify each shortcode's style setting.
        $shortcode_styles_id = $shortcode . '_' . $shortcode_defaults['id'];

        $css_propoerties = array_intersect_key( $css_properties , $shortcode_css );

        if( ! empty( $shortcode_css ) ) {

            // Property selectors.
            $css_selectors = [];

            // Contstruct and store css property : value declarations.
            foreach( $shortcode_css as $css_property => $css_value ) : 

            // Convert to proper css format.
            $css_property = str_replace( '_', '-', $css_property );

            // Stitch together the propoerty selector.
            $selector = !empty( $css_propoerties[ $css_property ] ) ? $css_selector . ' ' . $css_propoerties[ $css_property ] : $css_selector;

            if( array_key_exists ( $selector , $css_selectors ) ){
                $css_selectors[ $selector ][] = $css_property . ':' . $css_value .';';
            }

            if( ! array_key_exists ( $selector , $css_selectors ) ){
                $css_selectors[ $selector ] = array ( $css_property . ':' . $css_value .';');
            }

            endforeach;
            
            array_walk( $css_selectors , function ( &$declaration , $selector  ) {
                $declaration =  $selector . '{'. implode( $declaration ). '}';
            });

            // Create our string of declarations.
            $shortcode_css = implode( $css_selectors );
            
            // Store the css styles that was set for the shortcode.                      
            $this->styles[ $shortcode_styles_id ] = $shortcode_css;

            if( array_key_exists( $shortcode_styles_id, $this->styles ) ) {

                if( empty( $this->shortcode_styles ) ) : 

                $this->shortcode_styles = $this->styles[ $shortcode_styles_id ];

                else: 

                $this->shortcode_styles =  $this->shortcode_styles . $this->styles[ $shortcode_styles_id ] ;
                          
                endif;

            };
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
    private function generate_css_from_custom() {
        
        // Defaults
        $shortcode_defaults = $this->shortcode_defaults;
 
        // Style mapping and configurations.               
        $custom_css_properties = $this->custom_css_properties;

        // Get the keys of the defaults only if they have configurations assigned in the custom property configs.
        $shortcode_css_defaults = array_filter( array_intersect_key( $shortcode_defaults, $custom_css_properties ) ); 

        // Get get the same keys as above but each with its configuration string assigned as the value.
        $custom_css_properties = array_intersect_key( $custom_css_properties, $shortcode_css_defaults ); 
        
        // No point in going further if there is nothing to work with.
        if ( empty( $custom_css_properties ) || ( empty( $shortcode_css_defaults ) ) ) :
        return false;
        endif;

        foreach( $custom_css_properties as $property_name => $property_config ) {
                    
            // Get the option value selected by the user.
            $user_option = $shortcode_defaults[ $property_name ]; 
            
            if( is_string( $property_config ) ) :
            $custom_styles[] = $this->generate_css_from_custom_property( $user_option, $property_config );
            endif;
            
            if( is_object( $property_config ) ) :
            $custom_styles[] = $this->generate_css_from_custom_property_object( $user_option, $property_name, $property_config );
            endif;
            
        }

        // One round of styles comming up..
        $custom_styles = implode ( $custom_styles );
        
        if( empty( $this->shortcode_styles ) ) : 
       
        $this->shortcode_styles = $custom_styles;
        
        else:
        
        $this->shortcode_styles = $this->shortcode_styles . $custom_styles ;
        
        endif; 

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
    private function generate_css_from_custom_property( $user_option, $configuration ) { 
        
        // Sir, Maam... I need to see some id.
        $shortcode_id = $this->shortcode_id;

        // CSS Selector.
        $selectors = strstr( $configuration, '__' , 'before' ); 

        // Add the shortcode's id to any format strings.
        $selectors = sprintf( $selectors, $shortcode_id ); 

        // Extract the css property.
        $css_property =  strstr( $configuration , '__' , '' );

        // Remove double underscores.
        $css_property =  str_replace( '__', '' , $css_property );

        // Replace underscores with hyphens.
        $css_property =  str_replace( '_', '-' , $css_property );

        // Extract the css value.
        $css_value = $user_option;

        // Store all selectors along with the css property and value.
        $css  = $shortcode_id . ' ' .  $selectors . '{'. $css_property . ':' . $css_value .';' . '}';

        return $css;

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
    private function generate_css_from_custom_property_object( $user_option, $property_name, $property_config ) {
        
        // name of the current shortcode.
        $shortcode = $this->shortcode;      
        
        // name of the current shortcode
        $shortcode_id = $this->shortcode_id;
                
        // Check if elements property exists and if this shortcode is assigned to elements.
        $elements_set =  property_exists( $property_config, 'elements' ) && property_exists( $property_config->elements, $shortcode );
        
        // Get the target elements specific to the current shortcode if they exists.
        $local_elements = $elements_set && ! empty ( $property_config->elements->$shortcode ) ? $property_config->elements->$shortcode : FALSE; 
        
        // Check if elements are Globally set.
        $global_elements_set = property_exists( $property_config, 'elements' ) && property_exists( $property_config->elements, 'all' );
                
        // get the target elements used by all shortcodes if they exists.
        $global_elements = $global_elements_set ? $property_config->elements->all : FALSE;
        
        // Check whether element are active
        $has_elements = ( ! empty( $local_elements ) ) || ! ( empty( $global_elements ) );
        
        // Check if restrictions property exists and if this shortcode is assigned to restrictions.
        $restrict_set =  property_exists( $property_config, 'restrictions' ) && property_exists( $property_config->restrictions, $shortcode );
        
        // Make sure restriction are properly formated as an array and check if restrictions exists for this shortcode.
        $has_restrictions = $restrict_set && is_array( $property_config->restrictions->$shortcode ) && ( ! empty( $property_config->restrictions->$shortcode ) );
        
        // Check if current user option is allowed.
        $is_restricted = ( $has_restrictions && ( ! in_array( $user_option, $property_config->restrictions->$shortcode ) ) );
        
        // Get the css declaration selected for the current shortcode.
        $declaration = property_exists( $property_config->declarations, $user_option ) ? (array)$property_config->declarations->$user_option : FALSE;
        
        if( ( FALSE == $declaration ) || ( ! $has_elements ) ){
            return;
        }
        
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
        
        return ! empty( $css_declaration ) ? sprintf( $css_declaration, $shortcode_id ) : FALSE;
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
        
        // Get css styles generated from custom properties.
        $this->generate_css_from_custom();
        
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
