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
     * Stores the shortcode defaults.
     *
     * @since    1.0.0
     * @access   public
     */
    protected $shortcode_defaults;


    /**
     * Stores each shortcode id with shortcode's generated css.
     *
     * @since    1.0.0
     * @access   public
     */
    protected $shortcode_styles;


    /**
     * Stores the combined css for all shorcodes.
     *
     * @since    1.0.0
     * @access   public
     */
    protected $styles;

	/**
	 * Initialize the class and set its properties.
   *
   * Intiates the container for the css styles.
   *
   * Initializes the file paths.
   *
   * Sets up the registered css properties.
	 *
	 * @since    1.0.0
   *
	 */
	public function __construct() {

        $this->styles = array();
        $this->set_file_paths();
        $this->get_registered_properties();

	}

	/**
	 * Setup the properties used for file paths.
	 *
	 * @since    1.0.0
	 */
	private function set_file_paths() {

        // The parent directory.
        $this->scssg_dir =  dirname( __FILE__ , 2 ). '/cssg';

        // Configurations for custom file paths.
        $file_path_configs = file_get_contents(  $this->scssg_dir . '/json/configs.json' );

        // Custom File path configs converted to array.
        $file_paths = json_decode( $file_path_configs, TRUE );

        // The location of the generated css.
        $this->css_dir = $this->scssg_dir . $file_paths['css_file_path'];

	}

	/**
	 * Get and store the registered css properties.
	 *
	 * @since    1.0.0
	 */
	private function get_registered_properties() {

        $registered_properties = file_get_contents(  $this->scssg_dir . '/json/css.json' );

        $this->registered_properties = (array) json_decode( $registered_properties );

	}

	/**
	 * Set up variables needed by the methods in the application.
	 *
	 * @since    1.0.0
   *
   * @param   string $shorcode   The name of the shortcode
   * @param   string $string    The shortcode's attributes.
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
   * Strips  and extracts the flag placed within a given string.
   *
   * Removes the curely braces used to wrap a value and returns the
   * clean version of that value.
   *
   * @since    1.0.0
   * @access   private
   *
   * @param   string $string    A string that contains the flag.
   * @return  string            Name of the extracted flag.
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
     * Call back that Searches for and replaces flags with the resulting
     * value of a shorcode's attribute.
     *
     * This function is a callback used to extract the name of a shortcode
     * that was used as a flog and  replaces attribute/option the flag
     * with the value of that attribute / option.
     *
     * @since    1.0.0
     * @access   private
     *
     * @param   string $css_value      The css string as the array value
     * @param   string $css_property   The css property name as the array key
     *
     */
    private function search_and_replace_flags( &$css_value, $css_property ) {

        // Extract out the flag from the option's output value.
        $flag = $this->extract_search_flag( $css_value );

        // Extract the property name from the flag.
        $property_name = str_replace( array( '{','}' ), '', $flag );

         // Replace the flag with value shortcode value from the user. // Todo: Add custom error message.
        if( false !== $flag && key_exists( $property_name, $this->shortcode_defaults ) ) {
            $css_value = str_replace( $flag, $this->shortcode_defaults[ $property_name ], $css_value );
        }
    }

     /**
     * Convert shortcode style settings into css properties.
     *
     * Uses the configurations from registered properties to generate a
     * a string of valid css for each shortcode attribute / aption
     *
     * @since    1.0.0
     * @access   private
     *
     * @param   array $shortcode_defaults        An array of shortcode attributes
     * @param   array $registered_properties     User registered css properties
     * @param   array $shortcode_css             Shortcode css => value pairs
     * @return  string                           Valid css declarations.
     */
    private function convert_properties_to_css( $shortcode_defaults, $registered_properties, $shortcode_css ) {

        // Current shortcode.
        $shortcode = $this->shortcode;

        // Shortcode css selector.
        $shortcode_id = $this->shortcode_id;

        // Array of property => selector pairs.
        $registered_properties = array_intersect_key( $registered_properties , $shortcode_css );

        // Property selectors.
        $css_selectors = [];

        // Contstruct and store css property : value declarations.
        foreach( $shortcode_css as $css_property => $css_value ) {

            // Are you a string? If not, go home.
            if( ! is_string ( $registered_properties[ $css_property ] ) ){
                continue;
            }

            // CSS selectors registered with each registered property.
            $registered_selectors = $registered_properties[ $css_property ];

            // Stitch together the propoerty selector.
            $selector = ! empty( $registered_selectors ) ? $shortcode_id . ' ' . $registered_selectors : $shortcode_id;

            if( strpos( $selector , '__' ) ){

                // Extract the element selector.
                $css_selector =  strstr( $selector, '__' , true );

                // Get the property assigned to the selector.
                $css_property = strstr( $selector, '__' , false );

                // Remove all the underscores.
                $css_property = str_replace( '__' , '', $css_property );

                $selector = $css_selector;
            }

            if( array_key_exists( $selector , $css_selectors ) ){
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
     * Generates and stores the final css output for all the attributes
     * for both custom css propoerties and objects for the current
     * shortcode.
     *
     * @since    1.0.0
     * @access   private
     *
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

        // Create an id used to identify each shortcode's set of styles.
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
        $custom_object_css = $this->convert_custom_object_to_css( $registered_properties, $shortcode_css );

        // Combine into one string of css from both custom property and object.
        $shortcode_css = $custom_property_css . $custom_object_css;

        // Store the css styles that was set for the shortcode.
        $this->shortcode_styles[ $shortcode_styles_id ] = $shortcode_css;

        if( array_key_exists( $shortcode_styles_id, $this->shortcode_styles ) ) {

            if( empty( $this->styles ) ) :

            $this->styles = $this->shortcode_styles[ $shortcode_styles_id ];

            else:

            $this->styles =  $this->styles . $this->shortcode_styles[ $shortcode_styles_id ] ;

            endif;

        }
    }


    /**
     * Converts css from configuration settings of custom css objects.
     *
     * Uses the configruation settings from registered css property objects
     * and generates valid css declarations for all the attributes of The
     * current shortcode.
     *
     * @since    1.0.0
     * @access   private
     *
     * @param   array $shortcode_defaults   An array of shortcode attributes
     * @param   array $shortcode_css        Shortcode css => value pairs
     * @return  Valid css declarations.
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

           // Name of the current shortcode.
           $shortcode = $this->shortcode;

           // The shorcode's id.
           $shortcode_id = $this->shortcode_id;

           // Check if elements property exists and if this shortcode is assigned to elements.
           $elements_set =  property_exists( $configuration, 'elements' ) && property_exists( $configuration->elements, $shortcode );

           // Get the target elements specific to the current shortcode if they exists.
           $local_elements = $elements_set && ! empty ( $configuration->elements->$shortcode ) ? $configuration->elements->$shortcode : FALSE;

           // Check if elements are Globally set.
           $global_elements_set = property_exists( $configuration, 'elements' ) && property_exists( $configuration->elements, 'all' );

            // Get the target elements used by all shortcodes if they exists.
           $global_elements = $global_elements_set ? $configuration->elements->all : FALSE;

           // Check if element assignments are active.
           $has_elements = ( ! empty( $local_elements ) ) || ! ( empty( $global_elements ) );

           // Check if the restrictions property exists and if this shortcode is assigned to restrictions.
           $restrict_set =  property_exists( $configuration, 'restrictions' ) && property_exists( $configuration->restrictions, $shortcode );

           // Make sure restriction are properly formated as an array and check if restrictions exists for this shortcode.
           $has_restrictions = $restrict_set && is_array( $configuration->restrictions->$shortcode ) && ( ! empty( $configuration->restrictions->$shortcode ) );

           // Check if current user option is allowed.
           $is_restricted = ( $has_restrictions && ( ! in_array( $user_option, $configuration->restrictions->$shortcode ) ) );

           // Get the css declaration selected for the current shortcode.
           $declaration = property_exists( $configuration->declarations, $user_option ) ? (array)$configuration->declarations->$user_option : FALSE;

           // Skip if there are no declarations or elements assigned.
           if( ( false == $declaration ) || ( ! $has_elements ) ){
               continue;
           }

           // Get any flags that were placed in the user selelcted declaration.
           $flag =  $this->extract_search_flag( implode( $declaration) );

           // Strip the braces from the given flag to get the name of our secondary_option.
           $secondary_option = ( false !== $flag ) ? str_replace( array( '{','}' ), '', $flag ) : false;

           // Check to see if the seciondary option is being used in the shortcode defaults.
           $secondary_option_active = ( false !== $secondary_option ) && array_key_exists( $secondary_option , $this->shortcode_defaults );

           // Move on if the secondary option isn't being used.
           if( ! $secondary_option_active ) { var_dump( 'no property active' );
               continue;
           }

           // Convert search flags into values from shortcode defaults.
           array_walk( $declaration, array( $this, 'search_and_replace_flags') );

           // Finally creaate an array  valid css delcarations.
           array_walk( $declaration, function( &$css_value ,$css_property ){
               $css_value = "$css_property:$css_value;";
           } );

           // Combine element selectors with their respective declatrations to complete a full css string.
           $elements = $elements_set ? $shortcode_id . $local_elements : $shortcode_id . $global_elements;

           // Replace all flags with the shortcode's id.
           $elements = sprintf( $elements, $shortcode_id );

           // store the css if there are no restrictions for this shortcode.
           $css[] = ! $is_restricted ? $elements  . '{' . implode( $declaration ) . '}' : false;

       }

        return implode( $css );
    }

     /**
      * Generates a style sheet and writes the compiled css.
      *
      * @since  1.0.0
      * @acces  public
      */
    public function shortcode_cssg( $shortcode, $defaults ) {

        // Setup shared variables.
        $this->setup_shortcode_vars( $shortcode, $defaults );

        // Generate the all styles for the current shortcode.
        $this->generate_shortcode_css();

        // Get all styles generated for the shortcode.
        $styles = $this->styles;

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
