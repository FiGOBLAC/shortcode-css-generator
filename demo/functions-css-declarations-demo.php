<?php

function shortcode_cssg_declaration_libraries( $library, $shortcode ){
    
    $library['selectors'] = array(
        "gradient"          => " .library-selectbox $.library-selectbox",
        "transitions"       => " .library-selectbox $.library-selectbox",
        "selectbox2"        => " .library-selectbox2",
        "selectbox3"        => " .library-selectbox3"
    );
    
    $library['gradient'] = array(
        
        'left'=> array(
            'border'    => '1px solid',
            'border~'   => '1px solid',
            'border~~'  => '1px solid',
            'border~~~' => '1px solid',
            '-webkit-border-image'  => '-webkit-linear-gradient(left, {gradient-color},rgba(0, 0, 0, 0),  rgba(0, 0, 0, 0))',
            '-o-border-image'       => '-o-linear-gradient(left, {gradient-color}, rgba(0, 0, 0, 0),  rgba(0, 0, 0, 0))',
            '-moz-border-image'     => '-moz-linear-gradient(left, {gradient-color}, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0))',
            '-ms-border-image'      => '-ms-linear-gradient(left, rgba(0, 0, 0, 0), {gradient-color}, rgba(0, 0, 0, 0))',
            'border-image-slice'    => 1
        ),

        'right'=> array(
            '-webkit-border-image'  => '-webkit-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color})',
            '-o-border-image'       => '-o-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color})',
            '-moz-border-image'     => '-moz-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color}))',
            '-ms-border-image'      => '-ms-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color}))',
            'border-image-slice'    => 1
        ),

        'up'=> array(
            'border'=> '1px solid transparent',
            '-webkit-border-image'=> '-webkit-linear-gradient(left, {gradient-color},rgba(0, 0, 0, 0),  rgba(0, 0, 0, 0))',
            '-o-border-image'=> '-o-linear-gradient(left, {gradient-color}, rgba(0, 0, 0, 0),  rgba(0, 0, 0, 0))',
            '-moz-border-image'=> '-moz-linear-gradient(left, {gradient-color}, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0))',
            '-ms-border-image'=> '-ms-linear-gradient(left, rgba(0, 0, 0, 0), {gradient-color}, rgba(0, 0, 0, 0))',
            'border-image-slice'=> 1
        ),

        'down'=> array(
            'border'=> '1px solid transparent',
            '-webkit-border-image'=> '-webkit-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color})',
            '-o-border-image'=> '-o-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color})',
            '-moz-border-image'=> '-moz-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color}))',
            '-ms-border-image'=> '-ms-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color}))',
            'border-image-slice'=> 1
        ),
    );
        
    $library['transitions'] = array(
        
        'left'=> array(
            'border'    => '1px solid',
            'border~'   => '1px solid',
            'border~~'  => '1px solid',
            'border~~~' => '1px solid',
            '-webkit-border-image'  => '-webkit-linear-gradient(left, {gradient-color},rgba(0, 0, 0, 0),  rgba(0, 0, 0, 0))',
            '-o-border-image'       => '-o-linear-gradient(left, {gradient-color}, rgba(0, 0, 0, 0),  rgba(0, 0, 0, 0))',
            '-moz-border-image'     => '-moz-linear-gradient(left, {gradient-color}, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0))',
            '-ms-border-image'      => '-ms-linear-gradient(left, rgba(0, 0, 0, 0), {gradient-color}, rgba(0, 0, 0, 0))',
            'border-image-slice'    => 1
        ),

        'right'=> array(
            '-webkit-border-image'=> '-webkit-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color})',
            '-o-border-image'=> '-o-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color})',
            '-moz-border-image'=> '-moz-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color}))',
            '-ms-border-image'=> '-ms-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color}))',
            'border-image-slice'=> 1
        ),

        'up'=> array(
            'border'=> '1px solid transparent',
            '-webkit-border-image'=> '-webkit-linear-gradient(left, {gradient-color},rgba(0, 0, 0, 0),  rgba(0, 0, 0, 0))',
            '-o-border-image'=> '-o-linear-gradient(left, {gradient-color}, rgba(0, 0, 0, 0),  rgba(0, 0, 0, 0))',
            '-moz-border-image'=> '-moz-linear-gradient(left, {gradient-color}, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0))',
            '-ms-border-image'=> '-ms-linear-gradient(left, rgba(0, 0, 0, 0), {gradient-color}, rgba(0, 0, 0, 0))',
            'border-image-slice'=> 1
        ),

        'down'=> array(
            'border'=> '1px solid transparent',
            '-webkit-border-image'=> '-webkit-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color})',
            '-o-border-image'=> '-o-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color})',
            '-moz-border-image'=> '-moz-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color}))',
            '-ms-border-image'=> '-ms-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color}))',
            'border-image-slice'=> 1
        ),
    );
    
    return $library;
    
}

add_filter( 'css_declaration_library', 'shortcode_cssg_declaration_libraries' );

