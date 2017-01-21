<?php

require dirname( __FILE__ , 2 ) . '/class-shortcode-cssg.php';

function shortcode_cssg( $shortcode, $defaults ){

    $cssg = new Shortcode_CSSG();

    $cssg->shortcode_cssg( $shortcode, $defaults );

}
