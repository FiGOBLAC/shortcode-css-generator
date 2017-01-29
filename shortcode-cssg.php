<?php

require dirname( __FILE__ ) . '/class-shortcode-cssg.php';

/**
* Initiates the generator class and runs css genearator funcion.
*
* In case there is another instance of the Shortcode_CSSG Class
* this function sets a path to the correct configuration
* directory regardless of where the location of the shortcode
* when it iscalled.
*
* @package    Shortcode_CSSG
*
* @author     FiGO BLAC <figoblacmedia@yahoo.com>
*/
function shortcode_cssg( $shortcode, $defaults ){
	$cssg_dir = dirname( __FILE__ );
    return Shortcode_CSSG::get_instance( $cssg_dir )->shortcode_cssg( $shortcode, $defaults );

}

