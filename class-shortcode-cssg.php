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
		 * Stores the combined css for each shorcode.
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

			$this->set_file_paths( $identity );
			$this->load_configurations();
			$this->load_registered_properties();
			$this->load_css_declaration_library();
			$this->init_stylesheet_generator();
			$this->init_filesystem_proxy();

			$this->caller = $identity['caller'];
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

            $config = $this->configs['generate-css-stylesheet'];

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

            // Load only the options that have values set for them.
            $active_properties = array_filter( array_intersect_key( $this->registered_properties, $this->defaults ) );

            // Shortcode code declaration library filename.
            $shortcode_declaration_file = $this->scssg_dir . $this->configs['css_lib_path'] . "{$shortcode}.lib.json" ;

            // Load shortcode specific css declaration library.
			if( file_exists( $shortcode_declaration_file ) ){

                $shortcode_css_declaration_library  = file_get_contents( $shortcode_declaration_file );

                $this->css_declaration_library      = json_decode( $shortcode_css_declaration_library, true );

            }

            // Pre registered css properties for processing.
			$this->process_css_property_configurations( $this->registered_properties, $active_properties );

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
		private function process_css_property_configurations( $registered_properties, $active_properties ) {

            // Array of native css properties ( i.e. margin );
            $native_css_properties = array_filter( $active_properties, function( &$configuration, $property ){
                return $this->is_valid_property_type( $property, $configuration, 'native-property' );
            }, ARRAY_FILTER_USE_BOTH );

            // Array of custom css properties ( i.e. custom-name:margin );
            $custom_css_properties = array_filter( $active_properties, function( &$configuration, $property ){
                return $this->is_valid_property_type( $property, $configuration, 'custom-property' );
            }, ARRAY_FILTER_USE_BOTH );

           // Array of custom css properties ( i.e. custom-name{} );
            $custom_css_propertiey_objects = array_filter( $active_properties, function( &$configuration, $property ){
                return $this->is_valid_property_type( $property, $configuration, 'custom-property-object' );
            }, ARRAY_FILTER_USE_BOTH );

           // Array of custom css properties ( i.e. custom-name{} );
            $custom_css_propertiey_group = array_filter( $active_properties, function( &$configuration, $property ){
                return $this->is_valid_property_type( $property, $configuration, 'custom-property-group' );
            }, ARRAY_FILTER_USE_BOTH );

            if( ! empty( $custom_css_propertiey_objects ) ) {

                // Checks for and applies option configuration overrides.
                $custom_css_propertiey_objects = $this->apply_configuraton_overrides( $custom_css_propertiey_objects );

                // Checks for and applies option configuration  filters for css declarations
                $custom_css_propertiey_objects = $this->apply_declaration_filters( $custom_css_propertiey_objects );

                // Apply all css declaration filters set within the configuration.
                $this->build_shortcode_css( $custom_css_propertiey_objects );

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
		private function is_valid_property_type( $property = '', $configuration, $exptected_type ) {

            switch( $exptected_type ){

                case 'native-property':
                    $is_valid = ( is_string( $configuration ) && ( false === strpos( $property, ':' ) ) );
                    break;

                case 'custom-property':
                    $is_valid = ( is_string( $configuration ) && ( false !== strpos( $property, ':' ) ) );
                    break;

                case 'custom-property-object':
                    $is_valid = isset( $configuration['type'] ) && ( false !== strpos( $configuration['type'], 'custom' ) );
                    break;

                default: $is_valid = false;
            }

            return $is_valid;
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
		private function import_foreign_configurations( $configuration ) {
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
		private function apply_configuraton_overrides( $custom_css_propertiey_objects ) {

            // Apply an any configuration overrides for this shortcode.
            array_walk( $custom_css_propertiey_objects, function( &$config, $property_name ){

                // Merge in user entered property value.
                $config['user_value'] = $this->defaults[ $property_name ];

                // Get the value set by the user for this property/option.
                $property_value = $config['user_value'];

                // Removes the shortcode marker from  the overrides lists.
                $shortcode_overrides =  str_replace( "[shortcode]:", '', array_keys( $config ) );

                // A list of the overrides with out the shortode markers.
                $shortcode_overrides = array_combine ( $shortcode_overrides , $config );

                // Removes the shortcode marker from  the overrides lists.
                $selector_overrides =  str_replace( "{$property_value}:selector", 'selector', array_keys( $shortcode_overrides ) );

                // Removes the shortcode marker from  the overrides lists.
                $selector_overrides =  str_replace( "{$property_value}:declaration", 'declaration', $selector_overrides );

                // Removes the shortcode marker from  the overrides lists.
                $selector_overrides = ( 'custom:native' === $config['type'] )
                    ? str_replace( ":selector", '::', $selector_overrides )
                    : $selector_overrides;

                // A list of the overrides with out the shortode markers.
                $overrides = array_combine ( $selector_overrides , $shortcode_overrides );

                $config = array_filter( $overrides, function( $key, $value ){
                    return ( substr_count( $value, ':' )  !== 2 ) || ( false !== strpos( $value, 'filter' ) );
                }, ARRAY_FILTER_USE_BOTH );

            });

          return $custom_css_propertiey_objects;
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
		private function apply_declaration_filters( $custom_css_propertiey_objects ) {

            foreach( $custom_css_propertiey_objects as $property_name => $property_configuration ){

                $css_declaration_library  = $this->css_declaration_library;

                $filters = array_filter( $property_configuration, function( $configuration_key ) {
                    return ( false !== strpos( $configuration_key, 'filter' ) );
                }, ARRAY_FILTER_USE_KEY );

                if( ! empty( $filters ) ){

                    foreach( $filters as $declaration_object_name => $declaration_filter_values ){

                        // Remove the identifier.
                        $is_inclusion_filter = false !== strpos( $declaration_object_name, '::filter' );

                        // Remove the identifier.
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

                        }
                    }
                }

                if( ( 'custom:native' === $property_configuration['type'] ) && is_string ( $property_configuration['properties'] ) ){
                    $properties = array( trim( $property_configuration['properties'] ) );
                    $css_declaration_library = array_fill_keys( $properties, $property_configuration['user_value'] );
                }

                if( ( 'custom:native' === $property_configuration['type'] ) && is_array ( $property_configuration['properties'] ) ){
                    $css_declaration_library = array_fill_keys( $property_configuration['properties'], $property_configuration['user_value'] );
                }

                // Add the declartion library to the configuration's array.
                $property_configuration['css_library'] = $css_declaration_library;

                // Merge/replace the main configuration.
                $custom_css_propertiey_objects[ $property_name ] = $property_configuration;
            }

            return $custom_css_propertiey_objects;
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
		private function build_shortcode_css( $custom_css_propertiey_objects ) {

            $shortcode_css = [];

            foreach( $custom_css_propertiey_objects as $property_name => $configuration ){

                // Round up the selectors.
                $selectors = array_filter( $configuration, function( $handle ) {
                    return ( false !== strpos( $handle, 'selector') );
                }, ARRAY_FILTER_USE_KEY );

                // Round up private declartions;.
                $private_declarations = array_filter( $configuration, function( $handle ) {
                    return ( false !== strpos( $handle, ':declaration') );
                }, ARRAY_FILTER_USE_KEY );

                if( ! empty( $selectors ) ){

                    foreach( $selectors as $declaration_object_name => $css_selector ){

                       if( strpos ( '->' , $css_selector ) ):

                        $parts              = explode( '->' , trim( $css_selector) );
                        $selector_object    = $parts[0];
                        $selector_value     = $parts[1];

                        $css_selector = isset( $configuration['css_library'][ $selector_object ][ $selector_value ] )
                            ? $configuration['css_library'] [ $selector_object ][ $selector_value ]
                            : $css_selector;

                       endif;

                        // Create a private declaration name so we can check for its existence.
                        $private_declaration_name = str_replace( ':selector', ":declaration", $declaration_object_name );

                        // Name of the declaration object used by this property.
                        $declaration_object_name = str_replace( array( ':selector' ), '', $declaration_object_name );

                        // Pass in the configuration 's property name.
                        $configuration['property_name'] = $property_name;

                        // Assing the correct css declaration type.
                        if( ! empty ( $private_declarations ) && ! isset( $configuration['properties'] ) ){
                            $configuration['css_library'] = $private_declarations[ $private_declaration_name ];
                        }

                        // Assing the correct css declaration type.
//                        if( ! empty ( $private_declarations ) && ! isset( $configuration['properties'] ) ){
                            $configuration['declaration_object'] = $declaration_object_name;
//                        }

                        // replace flags with shortcode id.
                        $css_selector =  $this->shortcode_id . str_replace( '$', $this->shortcode_id, $css_selector );

                        // Build the css declartion for the current selector.
                        $css_declaration = $this->build_css_declaration( $configuration );

                        var_dump( $css_declaration );

                        if( ! ( $css_selector &&  $css_declaration ) ){
                            continue;
                        }

                        $css_declaration = is_array( $css_declaration ) ? implode( $css_declaration ) : $css_declaration;

                        if( array_key_exists( $css_selector , $shortcode_css ) ){
                            $shortcode_css[ $css_selector ][] = $css_declaration;
                        }

                        if( ! array_key_exists ( $css_selector , $shortcode_css ) ){
                            $shortcode_css[ $css_selector ] = array( $css_declaration );
                        }
                    }
                }
            }

            if( ! empty( $shortcode_css ) ){

                array_walk( $shortcode_css , function ( &$declaration , $selector  ) {
                    $declaration =  $selector . '{'. implode( $declaration ). '}';
                });

                $this->styles[ $this->caller ] = implode( $shortcode_css );
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
		 * @param   array $registered_properties     User registered css properties
		 * @param   array $shortcode_css             Shortcode css => value pairs
		 * @return  string                           Valid css declarations.
		 */
		private function build_css_declaration( $property_configs ) {

            $css_declaration = $property_configs['css_library'];

            if( ('custom' === $property_configs['type' ] ) && ! is_string( $property_configs['css_library'] ) ) {

                // Value set by the user.
                $shortcode_option_value = $property_configs['user_value'];

                // Get the name of the declaration object.
                $declaration_object_name = $property_configs['declaration_object'];

                // Go and get the css declaration from the library.
                $css_declaration = isset( $property_configs['css_library'][ $declaration_object_name ][ $shortcode_option_value ] )
                    ? $property_configs['css_library'][ $declaration_object_name ][ $shortcode_option_value ]
                    : array();

                 // Get any flags that were placed in the user selelcted declaration.
                $flag = ! empty( $css_declaration )
                    ? $this->extract_search_flag( implode( $css_declaration ) )
                    : false;

                // Strip the braces from the given flag to get the name of our secondary_option.
                $secondary_option = ( false !== $flag ) ? str_replace( array( '{','}' ), '', $flag ) : false;

                // Check to see if the secondary option is being used in the shortcode defaults.
                $secondary_option_active = ( false !== $secondary_option ) && array_key_exists( $secondary_option, $this->defaults );

                // Convert search flags into values from shortcode defaults.
                if( $secondary_option_active ) {
                    array_walk( $css_declaration, array( $this, 'search_and_replace_flags' ) );
                }

                // Finally creaate an array valid css delcarations.
                if( $secondary_option && ! $secondary_option_active ) {
                    return;
                }

            }

            if( ! empty( $css_declaration ) && is_array( $css_declaration  ) ){

                array_walk( $css_declaration, function( &$css_value ,$css_property ){
                    $css_value = "$css_property:$css_value;";
                });

              $css_declaration = implode( $css_declaration );

            }

            return ( ! empty( $css_declaration ) ) ? $css_declaration : false;
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
			if( false !== $flag && key_exists( $property_name, $this->defaults ) ) {
				$css_value = str_replace( $flag, $this->defaults[ $property_name ], $css_value );
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
		 * @param   array $registered_properties     User registered css properties
		 * @param   array $shortcode_css             Shortcode css => value pairs
		 * @return  string                           Valid css declarations.
		 */
		private function convert_properties_to_css( $registered_properties, $shortcode_css ) {

			// Current shortcode.
			$shortcode = $this->shortcode;

			// Shortcode css selector.
			$shortcode_id = $this->shortcode_id;

			// Array of property => selector pairs.
			$registered_properties = array_intersect_key( $registered_properties, $shortcode_css );

			// Property selectors.
			$shortcode_css = [];

			// Contstruct and store css property : value declarations.
			foreach( $shortcode_css as $css_property => $css_value ) {

				// Are you a string? If not, go home.
				if( ! is_string ( $registered_properties[ $css_property ] ) ){
					continue;
				}

				// CSS selectors registered with each registered property.
				$registered_selectors = $registered_properties[ $css_property ];

				// Stitch together the propoerty selector.
				$selector = ! empty( $registered_selectors ) ? $shortcode_id . $registered_selectors : $shortcode_id;

				if( strpos( $selector , '__' ) ){

					// Extract the element selector.
					$css_selector =  strstr( $selector, '__' , true );

					// Get the property assigned to the selector.
					$css_property = strstr( $selector, '__' , false );

					// Remove all the underscores.
					$css_property = str_replace( '__' , '', $css_property );

					$selector = $css_selector;
				}

				if( array_key_exists( $selector , $shortcode_css ) ){
					$shortcode_css[ $selector ][] = $css_property . ':' . $css_value .';';
				}

				if( ! array_key_exists ( $selector , $shortcode_css ) ){
					$shortcode_css[ $selector ] = array ( $css_property . ':' . $css_value .';' );
				}
			}

			array_walk( $shortcode_css , function ( &$declaration , $selector  ) {
				$declaration =  $selector . '{'. implode( $declaration ). '}';
			});

			$shortcode_css = implode( $shortcode_css );

			return $shortcode_css;
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
			if( empty( $this->defaults['id'] ) ) {
				return;
			}

			// Get the defaults;
			$defaults = $this->defaults;

			// Registered css properties.
			$registered_properties = $this->registered_properties;

			// Get the current owner ( function that called this class ).
			$stylesheet_owner =  $this->caller;

			// Extract all the css properties and values that matches registered properties.
			$shortcode_css  = array_intersect_key( $defaults, $registered_properties );

			// Remove css properties with no values.
			$shortcode_css  = array_filter( $shortcode_css );

			if( empty( $shortcode_css ) ) {
				return;
			}

			// Generata the css from custom property configurations.
			$custom_property_css = $this->convert_properties_to_css( $registered_properties, $shortcode_css );

			// Generata the css from custom object configurations.
			$custom_object_css = $this->convert_custom_object_to_css( $registered_properties, $shortcode_css );

			// Combine into one string of css from both custom property and object.
			$shortcode_css = $custom_property_css . $custom_object_css;

			if( array_key_exists( $stylesheet_owner, $this->styles ) ){
				$shortcode_css = $this->styles[ $stylesheet_owner ] . $shortcode_css;
			}

			$this->styles[ $stylesheet_owner ] = $shortcode_css;

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
		 * @param   array $defaults   An array of shortcode attributes
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
			   if( ( false == $declaration ) ){
				   continue;
			   }

			   // Get any flags that were placed in the user selelcted declaration.
			   $flag =  $this->extract_search_flag( implode( $declaration) );

			   // Strip the braces from the given flag to get the name of our secondary_option.
			   $secondary_option = ( false !== $flag ) ? str_replace( array( '{','}' ), '', $flag ) : false;

			   // Check to see if the seciondary option is being used in the shortcode defaults.
			   $secondary_option_active = ( false !== $secondary_option ) && array_key_exists( $secondary_option , $this->defaults );

			   // Convert search flags into values from shortcode defaults.
			   if( $secondary_option_active ) {
					array_walk( $declaration, array( $this, 'search_and_replace_flags') );
				}

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

			return ! empty( $css ) ? implode( $css ) : false;
		}

		/**
		 * Generates a style sheet and writes the compiled css.
		 *
		 * @since  1.0.0
		 * @acces  public
		 */
		public function shortcode_cssg( $shortcode, $defaults ) {

//            global $shortcode_tags;
//
//            var_dump( $shortcode_tags );

			// Setup shared variables.
			$this->load_shortcode_properties( $shortcode, $defaults );

//			// Generate the all styles for the current shortcode.
//			$this->generate_shortcode_css();
//
//			// Get the styles from the current owner.
//			$styles = $this->styles[ $this->caller ];
//
//			// One rediculously long string of css declarations coming up...
//			$styles = ! empty( $styles ) ? $styles : false;
//
//			// Almost there... Just need to apply some filters.
//			$styles = apply_filters( 'filter_shortcode_css', $styles, $shortcode );
//
//            // Where should we generate the stylesheet?
//            $css_dir = ( isset ( $this->configs['css_file_path'] ) && array_key_exists( 'css_file_path', $this->configs ) )
//                ?  $this->parent_dir . $this->configs['css_file_path'].$this->configs['css_file_name'];
//                :  $this->parent_dir . "/shortcode-cssg/css/shortcodes.css";
//
//			// If the generator goes out... party over. Otherwise do ya thing.
//			$css = ( $this->generate_stylesheet === true ) ? $styles : false;
//
//			// And Voila.. Add the contents to the css file.
//			$this->filesystem->execute( 'put_contents', $css_dir, array( 'content' => $css ) );
//
//			return $this->styles;

		}
	}
}
