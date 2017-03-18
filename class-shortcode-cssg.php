<?php

if( ! class_exists( 'Shortcode_CSSG' ) ) {

	/**
	 * The shortcode generator class.
	 *
	 * Defines and generates the css for shortcode
	 * attributes.
	 *
	 * @package    Shortcode_CSSG
	 *
	 * @author     FiGO BLAC <figoblacmedia@yahoo.com>
	 */
	class Shortcode_CSSG {

		/**
		 * Stores the name of the current shortcode being processed.
		 *
		 * @since    1.0.0
		 * @access   public
		 */
		protected $shortcode;
		/**
		 * Stores the shortcode defaults.
		 *
		 * @since    1.0.0
		 * @access   public
		 */
		protected $defaults;

		/**
		 * Stores shortcode css selector => declaration builds.
         *
		 *
		 * @since    1.0.0
		 * @access   public
		 */
		protected $shortcode_css;

		/**
		 * Stores the combined css for each shorcode instance.
		 *
		 * @since    1.0.0
		 * @access   public
		 */
		protected $styles;

		/**
		 * The filesystem proxy class.
		 *
		 * @since    1.0.0
		 * @access   public
		 */
		protected $filesystem;

		/**
		 * The path to the shortcode css generatory folder.
		 *
		 * @since    1.0.0
		 * @access   public
		 */
		protected $scssg_dir;

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
		 * @access   public
		 *
		 */
		public static function get_instance( $identity ) {

			static $instance = null;

			if( is_null( $instance ) ) {

				$instance = new self;
				$instance->init( $identity );
				$instance->styles[ $identity['caller'] ] = '';
			}

			$instance->run_caller_id( $identity );

			return $instance;

		}

		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 * @access   public
		 *
		 */
		public function __construct() {}

		/**
		 * Setup the properties used for file paths.
		 *
		 * @since    1.0.0
		 */
		private function init( $identity ) {

            $this->shortcodes_processed   = 0;
			$this->shortcode_css         = [];
			$this->caller                = $identity['caller'];

			$this->set_file_paths( $identity );
			$this->load_configurations();
			$this->load_registered_properties();
			$this->load_css_declaration_library();
			$this->init_stylesheet_generator();
			$this->init_filesystem_proxy();

		}

		/**
		 * Gets and stores the name of the funtion.
		 *
		 * If another caller is detected other than the current
		 * caller the new caller will replaces the old
		 *
		 * @since    1.0.0
		 */
		private function run_caller_id( $identity ) {

			if( $this->caller !== $identity['caller'] ) {
				$this->caller = $identity['caller'];

				$this->init( $identity );
			}
		}

		/**
		 * Setup the properties used for file paths.
		 *
		 * @since    1.0.0
		 */
		private function set_file_paths( $identity ) {

			// Parent directory outside the shortcode css generator.
			$this->parent_dir = $identity['parent_dir'];

			// The parent directory of this file.
			$this->scssg_dir = $identity['scssg_dir'];
		}


        /**
		 * Initialize filesystem proxy function.
		 *
		 * @since    1.0.0
		 */
		private function init_filesystem_proxy() {

			require_once $this->scssg_dir . 'class-shortcode-cssg-filesystem.php';

			$this->filesystem = Shortcode_CSSG_Filesystem::get_instance();
		}


		/**
		 * Loads the configuration file.
		 *
		 * @since    1.0.0
		 */
		private function load_configurations() {

			$configs = file_get_contents( $this->scssg_dir . 'json/configs.json' );

			$this->configs = json_decode( $configs, TRUE );

		}


        /**
		 * Initializes stylesheet generator based on configuration.
		 *
		 * @since    1.0.0
		 */
		private function init_stylesheet_generator() {

            $config = $this->configs['generate_css_stylesheet'];

            $this->generate_stylesheet = ( ! empty( $config ) ) ? $config : false;

		}

		/**
		 * Loads all registered css propereties.
		 *
		 * @since    1.0.0
		 */
		private function load_registered_properties() {

			$registered_properties = file_get_contents( $this->scssg_dir . 'json/css.json' );

			$this->registered_properties = json_decode( $registered_properties, true );

		}

		/**
		 * Loads all registered css propereties.
		 *
		 * @since    1.0.0
		 */
		private function load_css_declaration_library() {

            // Main css declaration library filename.
            $css_declaration_file = $this->scssg_dir . $this->configs['css_lib_path'] . "css.lib.json";

            // Load the main css declaration library file
			$css_declaration_library = file_get_contents( $css_declaration_file );

            // Working with them as arrays.
			$this->css_declaration_library = json_decode( $css_declaration_library, true );

		}

		/**
		 * Set up variables needed by the methods in the application.
		 *
		 * @since    1.0.0
		 *
		 * @param   string $shorcode   The name of the shortcode
		 * @param   string $string    The shortcode's attributes.
		 */
		private function load_shortcode_properties( $shortcode, $defaults ) {

			// Store shortcode name
			$this->shortcode = $shortcode;

			// Shortcode defaults.
			$this->defaults = array_filter( $defaults );

			// Sir, Maam... I need to see some id.
			$this->shortcode_id = '#' . $this->defaults['id'];

            // Shortcode code declaration library filename.
            $shortcode_declaration_file = $this->scssg_dir . $this->configs['css_lib_path'] . "{$shortcode}.lib.json" ;

            // Load shortcode specific css declaration library.
			if( file_exists( $shortcode_declaration_file ) ){

                $shortcode_css_declaration_library  = file_get_contents( $shortcode_declaration_file );

                $this->css_declaration_library      = json_decode( $shortcode_css_declaration_library, true );
            }

		}

        /**
		 * Initialize and prep configurations for each of the shortcode"s
         * options and prepare for processing.
		 *
		 * @since    1.0.0
         *
         * @param   array $declaration_lib   Shortcode css declaration library
		 */
		private function validate_property_type( $property = '', $configuration, $exptected_type ) {

            switch( $exptected_type ){

                case 'native-property':
                    return ( is_string( $configuration ) && ( false === strpos( $property, ':' ) ) );
                    break;
                case 'custom-property':
                    return ( is_string( $configuration ) && ( false !== strpos( $property, ':' ) ) );
                    break;
                case 'custom-property-object':
                    return isset( $configuration['type'] ) && ( false !== strpos( $configuration['type'], 'custom' ) );
                    break;
            }
		}

        /**
		* Strips and extracts the flag placed within a given string.
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
		 * Callback that Searches for and replaces flags with the resulting
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
		private function search_and_replace_flags( &$css_value ) {

			// Extract out the flag from the option's output value.
			$flag = $this->extract_search_flag( $css_value );

			// Extract the property name from the flag.
			$property_name = str_replace( array( '{','}' ), '', $flag );

			 // Replace the flag with value shortcode value from the user. // Todo: Add custom error message.
			if( false !== $flag && key_exists( $property_name, $this->defaults ) ) {
				$css_value = str_replace( $flag, $this->defaults[ $property_name ], $css_value );
			}

            return $css_value;
		}

        /**
		 * Initialize and prep configurations for each of the shortcode"s
         * options and prepare for processing.
		 *
		 * @since    1.0.0
         *
         * @param   array $declaration_lib   Shortcode css declaration library
		 */
		private function sweep_configuration( $configuration, $properties ) {

            foreach( $properties as $property ) {
                unset( $configuration[ $property ] );
            }

            return $configuration;
		}

        /**
		 * Processes the configuration of the current property/option.
         *
         * Applies overrides, converts selectors and their assigned
         * declarations into a format ready for css generation.
		 *
		 * @since    1.0.0
         *
         * @param   array $declaration_lib   Shortcode css declaration library
		 */
		private function process_css_property_configurations() {

            $native_css_properties          = [];
            $custom_css_properties          = [];
            $custom_css_property_objects    = [];
            $custom_property_group          = [];

            foreach( $this->registered_properties as $property => $configuration ){

                // Array of custom css properties ( i.e. custom-name{} );
                $is_native_property = $this->validate_property_type( $property, $configuration, 'native-property' );

                // Array of custom css properties ( i.e. custom-name{} );
                $is_custom_property = $this->validate_property_type( $property, $configuration, 'custom-property' );

                // Array of custom css properties ( i.e. custom-name{} );
                $is_custom_object   = $this->validate_property_type( $property, $configuration, 'custom-property-object' );

                // Array of custom css properties ( i.e. custom-name{} );
                $is_custom_group    = $this->validate_property_type( $property, $configuration, 'custom-property-object' );

                $is_psuedo_property = str_replace( ':', '', strstr( $property, ':', true ) );
                $is_active_property = array_key_exists( $property, $this->defaults ) ;
                $is_active_psuedo   = array_key_exists( str_replace( ':', '', strstr( $property, ':', true ) ), $this->defaults ) ;

                if( $is_active_property || $is_active_psuedo ){

                    if( $is_native_property ) {
                        $native_css_configuration = $this->translate_native_configuration( $property, $configuration );
                        $native_css_configuration = $this->build_shortcode_css( $native_css_configuration );
                    }

                    if( $is_custom_property ) {
                        $custom_css_configuration = $this->translate_native_configuration( $property, $configuration ,'custom' );
                        $custom_css_configuration = $this->build_shortcode_css( $custom_css_configuration );
                    }

                    if( $is_custom_object ) {

                        if( false !== strpos( $property, ":{$this->shortcode}")) {
                            continue;
                        }

                        if( ( $is_custom_object && ! isset( $configuration['properties'] ) ) ) {
                            $configuration['css_library'] = $this->css_declaration_library;
                        }

                        // Add the name of the of the property to the its configuration list.
                        $configuration['property_name'] = $property;

                        // Supply the configuration with he value entered by user.
                        $configuration['user_value'] = $this->defaults[ $property ];

                        // Checks for and applies option configuration overrides.
                        $configuration =  $this->apply_configuraton_overrides( $configuration );

                        // Checks for and applies option configuration  filters for css declarations
                        $configuration = $this->apply_declaration_filters( $configuration );

                        // Checks for and applies option configuration  filters for css declarations
                        $configuration = $this->translate_configuration( $configuration );

                        // Checks for and applies option configuration  filters for css declarations
                        $configuration = $this->build_shortcode_css( $configuration, 'object' );

                        // Re entered the configuration to the object list.
                        $custom_css_property_objects[ $property ] = $configuration;

                    }
                }
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
		 * @param   array $defaults        An array of shortcode attributes
		 * @return  string                           Valid css declarations.
		 */
		private function apply_configuraton_overrides( $configuration ) {

            // We get the name of the current shortcode.
            $shortcode = $this->shortcode;

            // Get this configuration's property name.
            $property_name = $configuration['property_name'];

            // Get the value set by the user for this property/option.
            $property_value = $configuration['user_value'];

            // Does this configuration has a css library assigned?
            $css_lib = isset( $configuration['css_library'] ) ? $configuration['css_library'] : false ;

            // Get the value set by the user for this property/option.
            $properties = $this->registered_properties;

            // Repalce ONLY specific configuration settings with shortcode specfic settings.
            $inclusive_override = array_key_exists( "{$property_name}::{$shortcode}", $properties );

            // Replace the configuration with a shortcode specfic configuration.
            $exclusive_override = array_key_exists( "{$property_name}:{$shortcode}", $properties );

            // Replace the configuration with a shortcode specfic configuration.
            $configuration = $inclusive_override
                ? array_merge( $configuration, $properties[ "{$property_name}::{$shortcode}" ] )
                : $configuration;

            // Replace the configuration with a shortcode specfic configuration.
            $configuration = $exclusive_override
                ? array_merge( $properties[ "{$property_name}:{$shortcode}" ], array( $property_name, $property_value, $css_lib ) )
                : $configuration;

            // Removes the shortcode marker from  the overrides lists.
            $shortcode_overrides =  str_replace( "[ {$shortcode} ]:", '', array_keys( $configuration ) );

            // A list of the overrides with out the shortode markers.
            $shortcode_overrides = array_combine ( $shortcode_overrides , $configuration );

            // Removes the shortcode marker from  the overrides lists.
            $selector_overrides =  str_replace( "{$property_value}:selector", 'selector', array_keys( $shortcode_overrides ) );

            // Removes the shortcode marker from  the overrides lists.
            $selector_overrides =  str_replace( "{$property_value}:declaration", 'declaration', $selector_overrides );

            // Removes the shortcode marker from  the overrides lists.
            $selector_overrides = ( 'custom:native' === $configuration['type'] )
                ? str_replace( ":selector", '::', $selector_overrides )
                : $selector_overrides;

            // A list of the overrides with out the shortode markers.
            $overrides = array_combine ( $selector_overrides , $shortcode_overrides );

            $configuration = array_filter( $overrides, function( $key, $value ){
                return ( substr_count( $value, ':' )  !== 2 ) || ( false !== strpos( $value, 'filter' ) );
            }, ARRAY_FILTER_USE_BOTH );

          return $configuration;

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
		 * @param   array $defaults        An array of shortcode attributes
		 * @return  string                           Valid css declarations.
		 */
		private function apply_declaration_filters( $configuration ) {

            if( 'custom' !== $configuration['type'] ) {
                return $configuration;
            }

            $filters = array_filter( $configuration, function( $configuration_key ) {
                return ( false !== strpos( $configuration_key, 'filter' ) );
            }, ARRAY_FILTER_USE_KEY );

            // Set css declaration library to the default libary.
            $css_declaration_library  =  $configuration['css_library'];

            foreach( $filters as $declaration_object_name => $declaration_filter_values ){

                // Include all except..
                $is_inclusion_filter = false !== strpos( $declaration_object_name, '::filter' );

                // Exclude all except..
                $is_exlusion_filter = false !== strpos( $declaration_object_name, ':filter' ) && ! $is_inclusion_filter;

                // Remove the filter markers.
                $declaration_object_name = str_replace( array( '::filter', ':filter',  ), '', $declaration_object_name );

                // Names matching the keys of declaration object values from the library.
                $declaration_filter_values = array_flip( $declaration_filter_values );

                if( isset ( $css_declaration_library[ $declaration_object_name ] ) ) {

                    // Declaration object values pulled from library based on keys in the filter.
                    $declaratiion_object_values = $css_declaration_library[ $declaration_object_name ];

                    // Include all except...
                    $declaratiion_object_values = $is_inclusion_filter
                        ? array_diff_key( $declaratiion_object_values, $declaration_filter_values )
                        : $declaratiion_object_values;

                    // Exclude all except...
                    $declaratiion_object_values = $is_exlusion_filter
                        ? array_intersect_key( $declaratiion_object_values, $declaration_filter_values )
                        : $declaratiion_object_values;

                    // Replaces the object values based on the filter.
                    $css_declaration_library[ $declaration_object_name ] = $declaratiion_object_values;

                    // Add the declartion library to the configuration's array.
                    $configuration['css_library'] = $css_declaration_library;

                }

                // We dont need the filter marker any more so clean up the configuration.
                $dirty_keys = array( "{$declaration_object_name}:filter", "{$declaration_object_name}::filter" );

                // Remove dirty keys.
                $configuration = $this->sweep_configuration( $configuration, $dirty_keys );

            }

            return $configuration;
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
		 * @param   array $defaults        An array of shortcode attributes
		 * @return  string                           Valid css declarations.
		 */
		private function translate_secondary_option( $css_declaration ) {

            $declaration = is_array( $css_declaration ) ? implode( $css_declaration ) : $css_declaration;

            // Get any flags that were placed in the user selelcted declaration.
            $flag = ! empty( $declaration )
                ? $this->extract_search_flag( $declaration )
                : false;

            // Strip the braces from the given flag to get the name of our secondary_option.
            $secondary_option = ( false !== $flag ) ? str_replace( array( '{','}' ), '', $flag ) : false;

            // Check to see if the secondary option is being used in the shortcode defaults.
            $secondary_option_active = ( false !== $secondary_option ) && array_key_exists( $secondary_option, $this->defaults );

            // Convert search flags into values from shortcode defaults.
            if( $secondary_option_active && is_array( $css_declaration ) ) :
                array_walk( $css_declaration, array( $this, 'search_and_replace_flags' ) );

            elseif( $secondary_option_active ):
                $css_declaration = $this->search_and_replace_flags( $css_declaration );

            endif;


            return ( $secondary_option && ! $secondary_option_active ) ? false : $css_declaration;
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
		 * @param   array $defaults        An array of shortcode attributes
		 * @return  string                           Valid css declarations.
		 */
		private function translate_native_configuration( $property, $configuration, $type='' ) {

            $shortcode_css = array();

            $selector_id = mt_rand( 0, 999 );

            // custom css property name.
            $property_name = ( 'custom' === $type ) ? strstr( $property, ':', true ) : $property;

            // native Css property
            $css_property = ( 'custom' === $type ) ?  str_replace( ':', '', strstr( $property, ':' ) ) : $property;

            // css property value set by the user.
            $css_value = $this->defaults[ $property_name ];

            // Set the css selector...
            $css_selector =  "$selector_id::{$this->shortcode_id}" . $configuration;

            // Set the css selector...
            $css_selector =  str_replace( '$', $this->shortcode_id, $css_selector );

            // ..and the declaration.
            $css_declaration = "{$css_property}:{$css_value};";

            $shortcode_css[ $css_selector ] = $css_declaration ;

            return $shortcode_css;

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
		 * @param   array $defaults        An array of shortcode attributes
		 * @return  string                           Valid css declarations.
		 */
		private function translate_configuration( $configuration ) {

            // Get the users' value.
            $user_value = $configuration['user_value'];

            // What type is the current configuraiton?
            $type =  $configuration['type'];

            // Are there any properties to process.
            $has_properties = isset( $configuration['properties'] );

            // Set the css declaration library.
            $css_library = ! $has_properties ? $configuration['css_library'] : false;

            // Get properties if exists
            $properties = $has_properties ?  $configuration['properties'] : false;

            // Initiate container for shortcode css.
            $configuraton['shortcode_css'] = array();

            // List of configuratoin properites ot remoave.
            $dirty_keys = array( 'css_library', 'user_value','type', 'property_name', 'properties' );

            // Remove dirty keys.
            $configuration = $this->sweep_configuration( $configuration, $dirty_keys );

            // Round up the selectors.
            $private_declarations = array_filter( $configuration, function( $handle ) {
                return ( false !== strpos( $handle, ':declaration') );
            }, ARRAY_FILTER_USE_KEY );

            foreach( $configuration as $handle => $value ){

                if( array_key_exists( $handle, $private_declarations ) ){
                    continue;
                }

                // Is the selector stored in the css declaration library?
                $selector           = strpos( $value, '->' ) ? explode('->', $value ) : $value;
                $selector_object    = is_array( $selector ) ? $selector[0] : false;
                $selector_value     = is_array( $selector ) ? $selector[1] : false;

                // Go and get the selector from the css declaration library.
                $selector = ( is_array( $selector ) && isset( $this->css_declaration_library[ $selector_object ][ $selector_value] ) )
                    ? $this->css_declaration_library[ $selector_object ][ $selector_value ]
                    : $selector;

                // Set random prefix for selecors with the same name.
                $selector_id = mt_rand( 0, 999 );

                // Add the shortcode id as a main selector.
                $selector = "{$selector_id}::" . $this->shortcode_id . str_replace( '$', $this->shortcode_id, $selector );

                // Go get the declaration from the library.
                $declaration = $has_properties && is_string( $properties )
                    ? array_fill_keys( array( $properties ), $user_value )
                    : false;

                // Go get the declaration from the library.
                $declaration = $has_properties && is_array( $properties )
                    ? array_fill_keys( $properties, $user_value )
                    : $declaration;

                // Extract the name of the css declaration block.
                $declaration_handle = str_replace( ':', '', strstr( $handle, ":", true ) );

                // Go get the declaration from the library.
                $declaration = ! $has_properties && isset( $css_library[ $declaration_handle ][ $user_value ] )
                    ? $css_library[ $declaration_handle ][ $user_value ]
                    : $declaration;

                // If is a private declaration get the declaraton from the private declaratoin pool.
                if( array_key_exists( "{$declaration_handle}:declaration", $private_declarations ) ) {
                    $declaration = $private_declarations[ "{$declaration_handle}:declaration" ];
                }

                // Translate any secondary options.
                $declaration =  $this->translate_secondary_option( $declaration );

                if( is_array( $declaration ) ){

                    array_walk( $declaration, function( &$css_value ,$css_property ){
                        $css_value = "$css_property:$css_value;";
                    });

                    $declaration = implode( $declaration );
                }

                $configuration['shortcode_css'][ $selector ] = $declaration;
            }

            return  $configuration ;
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
		 * @param   array $defaults        An array of shortcode attributes
		 * @return  string                           Valid css declarations.
		 */
		private function build_shortcode_css( $configuration, $type ='' ) {

            $shortcode_css = ( 'object' === $type ) ? $configuration['shortcode_css'] : $configuration;

            foreach( $shortcode_css as $css_selector => $css_declaration ) {

                $css_selector       = str_replace( array('::#', '::.'), array('#', '.'), strstr( $css_selector, '::' ) );
                $css_declaration    = str_replace( '~', '', $css_declaration );

                if( array_key_exists( $css_selector , $this->shortcode_css ) ){
                    $this->shortcode_css[ $css_selector ][] = $css_declaration;
                }

                if( ! array_key_exists ( $css_selector , $this->shortcode_css ) ){
                   $this->shortcode_css[ $css_selector ] = array( $css_declaration );
                }
            }
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

            // Updates the call count.
            ++$this->shortcodes_processed;

            // Total shortcodes
            $total_shortcodes = $this->configs['total_shortcodes'];

            if( $total_shortcodes === $this->shortcodes_processed ){

                $shortcode_css = $this->shortcode_css;

                array_walk( $shortcode_css , function ( &$declaration , $selector ) {
                    $declaration = $selector . '{'. implode( $declaration ). '}';
                });

                $this->styles[ $this->caller ] = implode( $shortcode_css );
            }
		}

		/**
		 * Generates a style sheet and writes the compiled css.
		 *
		 * @since  1.0.0
		 * @acces  public
		 */
		public function shortcode_cssg( $shortcode, $defaults ) {

			// Load shortcode properties.
			$this->load_shortcode_properties( $shortcode, $defaults );

            // Process shortcode configurations
            $this->process_css_property_configurations();

			// Generate the all styles for the current shortcode.
			$this->generate_shortcode_css();

			// One rediculously long string of css declarations coming up...
			$styles = ! empty( $this->styles[ $this->caller ] ) ? $this->styles[ $this->caller ] : false;

			// Almost there... Just need to apply some filters.
			$styles = apply_filters( 'filter_shortcode_css', $styles, $shortcode );

            // Where should we generate the stylesheet?
            $css_dir = ( isset ( $this->configs['css_file_path'] ) && array_key_exists( 'css_file_path', $this->configs ) )
                ?  $this->parent_dir . $this->configs['css_file_path'].$this->configs['css_file_name']
                :  $this->parent_dir . "/shortcode-cssg/css/shortcodes.css";

			// If the generator goes out... party over. Otherwise do ya thing.
			$css = ( $this->generate_stylesheet === true ) ? $styles : false;

            // And Voila.. Add the contents to the css file.
            if( ! empty( $styles ) ) {
                $this->filesystem->execute( 'put_contents', $css_dir, array( 'content' => $css ) );
            }
		}
	}
