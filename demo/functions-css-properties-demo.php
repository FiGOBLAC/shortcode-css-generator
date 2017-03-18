<?php

/*
 * Native and custom css properties
*/
function shortcode_cssg_properties( $css_properties, $selectors ){
    
     $css_properties[] = array(
        'background'                => '',
        'background-color'          => '',
        'background-opacity'        => '',
        'background-image'          => '',
        'background-size'           => '',
        'background-repeat'         => '',
        'background-attachment'     => '',
        'background-position'       => '',
        'margin'                    => '',
        'margin-top'                => '',
        'margin-bottom'             => '',
        'margin-left'               => '',
        'margin-right'              => '',
        'padding'                   => '',
        'padding-top'               => '',
        'padding-bottom'            => '',
        'padding-left'              => '',
        'padding-right'             => '',
        'border'                    => '',
        'borde-left'                => '',
        'border-right'              => '',
        'border-top'                => '',
        'border-bottom'             => '',
        'border-color'              => '',
        'border-shadow'             => '',
        'border-radius'             => '',
        'border-image'              => '',
        'border-image_slice'        => '',
        '-webkit-border-image'      => '',
        '-moz-border-image'         => '',
        '-o-border-image'           => '',
        '-ms-border-image'          => '',
        'overflow'                  => '',
        'min-height'                => '',
        'line-height'               => '',
        'height'                    => '',
        'width'                     => '',
        'max-width'                 => '',
        'display'                   => '',
        'vertical-align'            => '',
        'color'                     => '',
        'font-style'                => '',
        'font-family'               => '',
        'font-size'                 => '',
        'line_height'               => '',
        'color'                     => '',
        'text-align'                => '',
        'opacity'                   => '',
        'cursor'                    => '',
        'headerbox-background:background'   => ' .headerbox',
        'headerbox-font-size:font-size'     => ' .headerbox',
        'headerbox-margin:margin'           => ' .headerbox',
        'headerbox-padding:padding'         => ' .headerbox',
        'headerbox-color:color'             => ' .headerbox',
        'headerbox-hover:color'             => ' .headerbox h2:hover',
     ),
    
     /* 
      * Custom propoerty built with multiple native applications. When a value 
      * is set for this property both margin-left and  margin-right properties 
      * will be set to the give value.
      */
   $css_properties['headerbox-margins'] => array(
        'type'          => 'custom:native',
        'properties'    => array ( 'margin-left', 'margin-up' ),
        'selector'      => ' .headerbox',
        'left'          => $selectors['headerbox'],
        'up'            => $selectors['gradient-up'],
        '[sample_shortcode]:left'  => $selectors['sample-shortcode'],
    );

    // Custom css property with multiple declaration libraries assigned
    $css_properties['gradient']  => array(
        'type'                              => 'custom',
        'gradient'                          => '', // Applies $selectors['gradient'] to gradient declaration.
        'transitions'                       => 'headerbox', // Applies $selectors['headerbox].
        'gradient:left'                     => $selectors['headerbox'], // Explicit selector assingment.
        'gradient:up'                       => 'valueover',
        '[sample_shortcode]:gradient'       => 'shortover',
        '[sample_shortcode]:gradient:left'  => ' .my-test-selector myheight, $.my-selector',
        'gradient:left:declaration'         => '-webkit-border-image:-webkit-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color});',
        '[sample_shortcode]:gradient:left:declaration'  => 'border: 1px solid transparent-trans',
        'gradient:filter'                               =>  array( 'left', 'right' )
    ),
           
    /* 
     * Demonstration on how to create a shortcode override.
     */
    $css_properties['sample-option::sample_shortcode']  => array(
        'type'                                          => 'custom',
        'gradient'                             => 'selectbox',
        'transitions'                          => 'selectbox',
        'gradient:left'                        => 'valueover',
        'gradient:up'                          => 'valueover',
        '[sample_shortcode]:gradient'          => 'shortover',
        '[sample_shortcode]:gradient:left'     => ' .my-test-selector',
        'gradient:left:declaration'                     =>  '-webkit-border-image:-webkit-linear-gradient(left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0), {gradient-color});',
        '[sample_shortcode]:gradient:left:declaration'  => 'border: 1px solid transparent-trans',
        'gradient:filter'                               =>  array( 'left', 'right' )
    ),
}

add_filter( 'register_css_properties','shortcode_css_properties' );