<?php

require dirname( __FILE__ ) . '/class-shortcode-cssg.php';

function shortcode_cssg( $shortcode, $defaults ){
    return Shortcode_CSSG::get_instance()->shortcode_cssg( $shortcode, $defaults );
}

