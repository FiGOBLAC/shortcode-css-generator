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
		protected $version;

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
		public static function get_instance( $id ) {

			static $instance = null;

			if( is_null( $instance ) ) {

				$instance = new self;
				$instance->init( $id );
				$instance->styles[ $id['caller'] ] = '';
			}

			$instance->run_caller_id( $id );

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
		private function init( $id ) {

			$this->version					= 1.0;
            $this->shortcodes_processed   	= 0;
			$this->shortcode_css         	= [];
			$this->caller                	= $id['caller'];

			$this->set_file_paths( $id );
			$this->load_filesystem_proxy();
			$this->load_core_configurations();
			$this->load_cssg_configuration();
			$this->load_demos();
			$this->load_css_properties();
			$this->load_css_declaration_library();
		}

		/**
		 * Gets and stores the name of the funtion.
		 *
		 * If another caller is detected other than the current
		 * caller the new caller will replaces the old
		 *
		 * @since    1.0.0
		 */
		private function run_caller_id( $id ) {

			if( $this->caller !== $id['caller'] ) {
				$this->caller = $id['caller'];

				$this->init( $id );
			}
		}

		/**
		 * Setup the properties used for file paths.
		 *
		 * @since    1.0.0
		 */
		private function set_file_paths( $id ) {

			// Parent directory outside the shortcode css generator.
			$this->parent_dir = $id['parent_dir'];

			// The parent directory of this file.
			$this->scssg_dir = $id['scssg_dir'];
		}


        /**
		 * Initialize filesystem proxy function.
		 *
		 * @since    1.0.0
		 */
		private function load_filesystem_proxy() {

			require_once $this->scssg_dir . 'class-shortcode-cssg-filesystem.php';

			$this->filesystem = Shortcode_CSSG_Filesystem::get_instance();
		}

		/**
		 * Loads the configuration file.
		 *
		 * @since    1.0.0
		 */
		private function load_core_configurations() {

			$configs = file_get_contents( $this->scssg_dir . 'json/configs.json' );

			$this->configs = json_decode( $configs, TRUE );

		}

        /**
		 * Initializes stylesheet generator based on configuration.
		 *
		 * @since    1.0.0
		 */
		private function load_cssg_configuration() {

            $config = $this->configs['generate_css_stylesheet'];

            $this->generate_stylesheet = ( ! empty( $config ) ) ? $config : false;

		}

		/**
		 * Loads demonstration files.
		 *
		 * @since    1.0.0
		 */
		private function load_demos() {

			if( ( 'on' === $this->configs['demo_mode'] ) ) {

				require_once  'demo/functions-css-declarations-demo.php';

				require_once $this->scssg_dir . 'demo/functions-css-properties-demo.php';

				$wpdir = get_template_directory_uri();

				$directory = strpos( $wpdir, 'themes' ) ? 'themes' : 'plugins';

				$wp_path = strstr( $wpdir, $directory, true );

				$cssg_path = strstr( dirname( __FILE__, 1 ), $directory );

				$shortcode_css_path = (string) $wp_path . $cssg_path;

				wp_enqueue_style( 'shortcode-cssg', $shortcode_css_path. '/css/shortcodes.css', array(),  $this->version, 'all' );

			}
		}

		/**
		 * Loads all registered css propereties.
		 *
		 * @since    1.0.0
		 */
		private function load_css_properties() {

			// CSS Properties used to filter active properties from the defaults.
			$this->active_property_checklist = array();

			// Initiate container for property overrides.
			$this->property_overrides = array();

			// Permanent containers that stays active through the life of property configurations.
			$this->configuration_presets = array( 'css_library' => array(), 'css_property_name' => '', 'user_value' => '' );

			// Properties registered by the user.
			$registered_properties = array( 'native' => array(),'custom' => array(), 'configured' => array() );

			// CSS Properties that have been translated to 'selector => declaration' pairs.
			$this->css_properties = array( 'shortcode_css' );

			// Filter that allows users to register css property configurations.
			$this->registered_properties = apply_filters( 'shortcode_css_properties', $registered_properties );

			// Collect the custom properties for the css actives check list.
			foreach( $this->registered_properties['custom'] as $property => $selector ){
				$css_property = strstr( $property, ':' );
				$custom_active_checklist [ strstr( $property, ':', true ) ] = "{$selector}::$css_property";
			}

			// Collect shortcode overrides.
			$this->property_overrides = array_filter( $this->registered_properties['configured'], function( $property ){
				return ( false !== strpos( $property, ':' ) );
			}, ARRAY_FILTER_USE_KEY );

			// Collect configured properties WITHOUT the shortcode overrides.
			$configured_properties = array_filter( $this->registered_properties['configured'], function( $property ){
				return ( false === strpos( $property, ':' ) );
			}, ARRAY_FILTER_USE_KEY );

			// Add custom properties to css actives check list.
			$this->active_property_checklist = array_merge( $this->registered_properties['native'], $custom_active_checklist, $configured_properties );

		}


		/**
		 * Loads all registered css propereties.
		 *
		 * @since    1.0.0
		 */
		private function load_css_declaration_library() {

			$css_declaration_library = array();

			$this->css_declaration_library = apply_filters( 'css_declaration_library', $css_declaration_library );

			$this->configuration_presets['css_library'] = $this->css_declaration_library;
		}

		/**
		 * Set up variables needed by methods in the application.
		 *
		 * @since    1.0.0
		 *
		 * @param   string $shorcode   The name of the shortcode
		 * @param   string $string    The shortcode's attributes.
		 */
		private function load_shortcode_properties( $shortcode, $defaults ) {

			// Shortcode
			$this->shortcode = $shortcode ;

			// Shortcode defaults.
			$this->defaults = array_filter( $defaults );

			// Sir, Maam... I need to see some id.
			$this->shortcode_id = isset( $defaults['id'] ) ? '#' . $defaults['id'] : '#' . mt_rand( 0, 9999 );
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

			if( ! strpos( $string, '{' ) ) {
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
		private function process_css_properties() {

			// Properties in use by the current shortcode.
			$active_css_properties = array_intersect_key( $this->active_property_checklist, $this->defaults );

			foreach( $active_css_properties as $property => $configuration ){

				// Add the name of the of the property to the its configuration list.
				$this->configuration_presets['css_property_name'] = $property;

				// Supply the configuration with he value entered by user.
				$this->configuration_presets['user_value'] = $this->defaults[ $property ];

				if( ! is_array( $configuration ) ){
					$this->translate_property_configuration( $property, $configuration );
				}

				if( is_array( $configuration ) && ( isset( $configuration['properties'] ) ) ) {

					// Supply the configuration with he value entered by user.
					$configuration  = array_merge( $configuration, $this->configuration_presets );

					// Checks for and applies option configuration overrides.
					$configuration = $this->run_configuraton_overrides( $configuration );

					// Checks for and applies option configuration  filters for css declarations
					$this->translate_single_configured_property( $this->defaults[ $property ], $configuration );
				}

				if( is_array( $configuration ) && !( isset( $configuration['properties'] ) ) ) {

					// Supply the configuration with he value entered by user.
					$configuration  = array_merge( $configuration, $this->configuration_presets );

					// Checks for and applies option configuration overrides.
					$configuration = $this->run_configuraton_overrides( $configuration );

					// Checks for and applies option configuration  filters for css declarations
					$configuration = $this->apply_declaration_filters( $configuration );

					// Checks for and applies option configuration  filters for css declarations
					$configuration = $this->translate_configured_properties( $this->defaults[ $property ], $configuration );

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
		private function run_configuraton_overrides( $configuration ) {

            // We get the name of the current shortcode.
            $shortcode = $this->shortcode;

			// Custom css property name.
			$configured_css_property = $configuration['css_property_name'];

			// Value set by the user for the current property/option.
			$property_value = $configuration['user_value'];

			// Get the configured properties that were registered.
            $property_overrides = $this->property_overrides;

			// Replace the WHOLE configuration with a shortcode specfic configuration.
            $configuration = key_exists( "{$configured_css_property}:{$shortcode}", $property_overrides )
                ? array_merge( $property_overrides[ "{$configured_css_property}:{$shortcode}" ], $this->configuration_presets )
                : $configuration;

			// Repalce only SPECIFIC configuration settings with a shortcode specfic settings.
            $configuration = key_exists( "{$configured_css_property}::{$shortcode}", $property_overrides )
                ? array_merge( $configuration, $property_overrides[ "{$configured_css_property}::{$shortcode}" ] )
                : $configuration;

			// Apply internal overrides.
			foreach( $configuration as $library_handle => $selector ){

				if ( ( false !== strpos( $library_handle, ':' ) ) && ( false === strpos( $library_handle, ':filter' ) ) ){
					continue;
				}

				 // Simple declaration override
				$declration_override = key_exists( "{$library_handle}:declaration", $configuration )
					? $configuration[ "{$library_handle}:declaration" ]
					: $configuration[ $library_handle ];

				// Property value doverride
				$value_override = key_exists( "{$library_handle}:{$property_value}", $configuration)
					? $configuration[ "{$library_handle}:{$property_value}" ]
					: $declration_override;

				// Property value declaration override
				$value_override = key_exists( "{$library_handle}:{$property_value}:declaration", $configuration )
					? $configuration[ "{$library_handle}:{$property_value}:declaration" ]
					: $value_override;

				// Shortcode override
				$shortcode_override = key_exists( "[{$shortcode}]:{$library_handle}", $configuration )
					? $configuration[ "[{$shortcode}]:{$library_handle}" ]
					: $value_override;

				// Shortcode declaration override
				$shortcode_override = key_exists( "[{$shortcode}]:{$library_handle}:declaration", $configuration )
					? $configuration[ "[{$shortcode}]:{$library_handle}:declaration" ]
					: $shortcode_override;

				// Shortcode property value override
				$shortcode_override = key_exists( "[{$shortcode}]:{$library_handle}:{$property_value}", $configuration )
					? $configuration[ "[{$shortcode}]:{$library_handle}:{$property_value}" ]
					: $shortcode_override;

				// // Shortcode property value declaration override
				$shortcode_override = key_exists( "[{$shortcode}]:{$library_handle}:{$property_value}:declaration", $configuration )
					? $configuration[ "[{$shortcode}]:{$library_handle}:{$property_value}:declaration" ]
					: $shortcode_override;

				$updated_configuration[ $library_handle ] = $shortcode_override;
			}

			return $updated_configuration;
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

            $filters = array_filter( $configuration, function( $configuration_key ) {
                return ( false !== strpos( $configuration_key, 'filter' ) );
            }, ARRAY_FILTER_USE_KEY );

            // Set css declaration library to the default libary.
            $css_declaration_library = $this->css_declaration_library;

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
                    $declaration_object_values = $css_declaration_library[ $declaration_object_name ];

                    // Include all except...
                    $declaration_object_values = $is_inclusion_filter
                        ? array_diff_key( $declaration_object_values, $declaration_filter_values )
                        : $declaration_object_values;

                    // Exclude all except...
                    $declaration_object_values = $is_exlusion_filter
                        ? array_intersect_key( $declaration_object_values, $declaration_filter_values )
                        : $declaration_object_values;

                    // Replaces the object values based on the filter.
                    $css_declaration_library[ $declaration_object_name ] = $declaration_object_values;

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
            $secondary_option_active = ( false !== $secondary_option ) && key_exists( $secondary_option, $this->defaults );

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
		private function translate_property_configuration( $property, $configuration ) {

			$selector_id = mt_rand( 0, 999 );

			// css property value set by the user.
			$css_value = $this->defaults[ $property ];

			$property = ( false !== strpos( $configuration, ':::') ) ? str_replace( ':::', '', strstr( $configuration, ':::') ) : $property;

			$selector = ( false !== strpos( $configuration, ':::') ) ?  strstr( $configuration, ':::', true )  : $configuration;

			// Set the css selector...
			$css_selector =  "$selector_id::{$this->shortcode_id}" . $selector;

			// Set the css selector...
			$css_selector =  str_replace( '$', $this->shortcode_id, $css_selector );

			// ..and the declaration.
			$css_declaration = "{$property}:{$css_value};";

			$this->css_properties['shortcode_css'][ $css_selector ] = $css_declaration ;

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
		private function translate_single_configured_property( $user_value, $configuration ) {

			$selector_id = mt_rand( 0, 9999 );

			$css_selector = ( isset( $configuration [ $user_value ] ) ) ? $configuration [ $user_value ] : $configuration['selector'];

			// Set the css selector...
			$css_selector =  "$selector_id::{$this->shortcode_id}" . $css_selector;

			// Set the css selector...
			$css_selector =  str_replace( '$', $this->shortcode_id, $css_selector );

			// CSS properties set by user
			$properties = $configuration['properties'];

			// Go get the declaration from the library.
			$declaration = is_string( $properties )
				? array_fill_keys( array( $properties ), $user_value )
				: '/**DECLARATION NOT FOUND**/';

			// Go get the declaration from the library.
			$declaration = is_array( $properties )
				? array_fill_keys( $properties, $user_value )
				: $declaration;

			if ( is_array( $declaration ) ) {
				array_walk( $declaration, function( &$css_value ,$css_property ){
					$css_value = "$css_property:$css_value;";
				});

				$declaration = implode( $declaration );
			}

			$this->css_properties['shortcode_css'][ $css_selector ] = $declaration;
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
		private function translate_configured_properties( $user_value, $configuration ) {

            // Set the css declaration library.
            $css_library =  $configuration['css_library'];

            // Remove dirty keys.
            $configuration = $this->sweep_configuration( $configuration, array( 'css_library', 'css_property_name' ) );

			foreach ( $configuration as $library_handle => $selector ){

				// Go and get the selector from the css  library.
				$selector = isset( $css_library[ 'selectors' ][ $selector ] )
					? $css_library[ 'selectors' ][ $selector ]
					: $selector;

				// Selector a configured custom property.
				$selector = ! empty( $sinlge_selector )
					? $sinlge_selector
					: $selector;

				// Set random prefix for selecors with the same name.
                $selector_id = mt_rand( 0, 999 );

				// Add the shortcode id as a main selector.
				$selector = "{$selector_id}::{$this->shortcode_id}" . str_replace( '$', $this->shortcode_id, $selector );

				// Go get the declaration from the library.
				$declaration = isset( $css_library[ $library_handle ][ $user_value ] )
					? $css_library[ $library_handle ][ $user_value ]
					: '/**DECLARATION NOT FOUND**/';

				// Go get the declaration from the library.
				$declaration = is_array( $declaration )
					? $declaration
					: array( $declaration );

				// Translate any secondary options stored in the declaration.
                $declaration = is_array( $declaration )
					? $this->translate_secondary_option( $declaration )
					: $declaration;

				if ( is_array( $declaration ) ) {
					array_walk( $declaration, function( &$css_value ,$css_property ){
						$css_value = "$css_property:$css_value;";
					});

					$declaration = implode( $declaration );
				}


				$this->css_properties['shortcode_css'][ $selector ] = $declaration;
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
		private function build_shortcode_css() {

            foreach( $this->css_properties['shortcode_css'] as $css_selector => $css_declaration ) {

                $css_selector       = str_replace( array('::#', '::.'), array('#', '.'), strstr( $css_selector, '::' ) );
                $css_declaration    = str_replace( '~', '', $css_declaration );

                if( key_exists( $css_selector , $this->shortcode_css ) ){
                    $this->shortcode_css[ $css_selector ][] = $css_declaration;
                }

                if( ! key_exists ( $css_selector , $this->shortcode_css ) ){
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

            // Total shortcodes.
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
            $this->process_css_properties();
//
            // Process shortcode configurations
            $this->build_shortcode_css();

			// Generate the all styles for the current shortcode.
			$this->generate_shortcode_css();

			// One rediculously long string of css declarations coming up...
			$styles = ! empty( $this->styles[ $this->caller ] ) ? $this->styles[ $this->caller ] : false;

			// Almost there... Just need to apply some filters.
			$styles = apply_filters( 'filter_shortcode_css', $styles, $shortcode );

            // Where should we generate the stylesheet?
            $css_dir = ( isset ( $this->configs['css_file_path'] ) && key_exists( 'css_file_path', $this->configs ) )
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
}
