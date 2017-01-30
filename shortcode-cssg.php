<?php

require dirname( __FILE__ ) . '/class-shortcode-cssg.php';

/**
* Wrapper for the shortcode css generator function.
*
* In case there is another instance of the Shortcode_CSSG Class,
* this function passes its name to the class so it can be used
* to identify and set the correct path to the configuration file
* that belongs to this specific instance.
*
* @package    Shortcode_CSSG
*
* @param   string $shorcode   The name of the shortcode
* @param   string $defaults   The default attributes.
*/
function shortcode_cssg( $shortcode, $defaults ){

	$caller 	=__FUNCTION__;
	$parent_dir = trailingslashit( dirname( __FILE__, 2 ) );
	$scssg_dir  = trailingslashit( dirname( __FILE__ ) );

    $CSSG = Shortcode_CSSG::get_instance( compact( 'caller', 'parent_dir', 'scssg_dir' ) );

    return $CSSG->shortcode_cssg( $shortcode, $defaults );
}
