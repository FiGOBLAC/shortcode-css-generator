<?php

add_filter( 'shortcode_css_properties','register_css_properties' );
add_filter( 'shortcode_css_properties','register_configured_properties' );

/*
 * Native and custom css properties
*/
function register_css_properties( $css_properties){
    
	$css_properties['native'] = array(

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

	 );

	$css_properties['custom'] = array(

		'headerbox-background-demo:background'	=> ' .headerbox',
		'headerbox-height-demo:height'   		=> ' .headerbox header',
		'headerbox-font-size-demo:font-size'    => ' .headerbox header',
        'headerbox-margin-demo:margin'          => ' .headerbox',
        'headerbox-padding-demo:padding'        => ' .headerbox',
        'headerbox-color-demo:color'            => ' .headerbox',
        'headerbox-hover-demo:color'            => ' .headerbox h2:hover',
        'contentbox-padding-demo:padding'		=> ' .contentbox',
        'contentbox-height-demo:height'			=> ' .contentbox',

     );

	return $css_properties;
}


/*
 * Native and custom css properties
*/
function register_configured_properties( $css_properties ){

	$css_properties['configured'] = array(

//		'gradient-demo:sample_shortcode' 	=> array(
//			'gradient-demo'						=> 'gradient-shortcode-GLOBAL',
//		),

//		'cssg-contentbox-demo' 		=> array(
//			'properties'    			=> array ( 'margin-left', 'margin-top' ),
//			'selector'      			=> ' .contenbox-selector',
//			'left'          			=> 'headerbox' ,
//			'[sample_shortcode]:left'	=> ' .sample-shortcode-SELECO',
//			'[sample_shortcode]:up'		=> 'sample-shortcode-SELECO',
//		),
////
//	   	'gradient-demo'							=> array(
//			'gradient-dl-demo'                          => 'gradient-selector', // Applies $selectors['gradient'] to gradient declaration.
//			'gradient-dl-demo:left'                     => 'gradient-left-selector', // Explicit selector assingment.
//			'gradient-dl-demo:up'                       => 'gradient-up-selector',
//			'gradient-demo:filter'					 =>  array( 'left', 'up' ),
//			'gradient-demo:left:declaration'         => ' .gradient-left-DECO',
//			'transitions'                       	 => 'transitions-selector', // Applies $selectors['headerbox].
//			'transitions:left'						 => 'transitions-left-selector', // Applies $selectors['headerbox].
//			'[sample_shortcode2]:gradient-demo'      => 'sample-shortcode-gradient-SELECO',
//			'[sample_shortcode2]:gradient-demo:left' => ' .sample-shortcode-gradient-left-css-SELECO',
//			'[sample_shortcode]:gradient-demo:left:declaration'  => 'border: 1px solid transparent-trans',
//			'[sample_shortcode]:transitions-demo:left:declaration' => 'border: 1px solid transitions-trans',
//    	),

	);

	return $css_properties;
}


//	// Custom css property with multiple declaration libraries assigned
//    $css_properties['gradient-demo']  = array(
//        'type'                              => 'custom',
//        'gradient-demo'                          => 'gradient-selector', // Applies $selectors['gradient'] to gradient declaration.
//        'gradient-demo:left'                     => 'gradient-left-selector', // Explicit selector assingment.
//        'gradient-demo:up'                       => 'gradient-up-selector',
//		'gradient-demo:filter'					=>  array( 'left', 'up' ),
//		'gradient-demo:left:declaration'        => ' .gradient-left-DECO',
//		'transitions'                       	=> 'transitions-selector', // Applies $selectors['headerbox].
//		'transitions:left'						=> 'transitions-left-selector', // Applies $selectors['headerbox].
//		'[sample_shortcode2]:gradient-demo'     => 'sample-shortcode-gradient-SELECO',
//        '[sample_shortcode2]:gradient-demo:left' => ' .sample-shortcode-gradient-left-css-SELECO ',
//        '[sample_shortcode2]:gradient-demo:left:declaration'  => 'border: 1px solid transparent-trans',
//        '[sample_shortcode]:transitions-demo:left:declaration'  => 'border: 1px solid transitions-trans',
//    );

	// Custom css property with multiple declaration libraries assigned
//    $css_properties['gradient-demo:sample_shortcode']  = array(
//        'type'                              => 'custom',
//        'gradient'                          => 'gradient-selector', // Applies $selectors['gradient'] to gradient declaration.
//        'gradient:left'                     => 'gradient-left-selector', // Explicit selector assingment.
//        'gradient:up'                       => 'gradient-up-selector',
//		'gradient:filter'					=>  array( 'left', 'right' ),
//		'gradient:left:declaration'         => ' .gradient-left-DECO',
//		'transitions'                       => 'transitions-selector', // Applies $selectors['headerbox].
//        '[sample_shortcode2]:gradient'      => 'sample-shortcode-gradient-SELECO',
//        '[sample_shortcode]:gradient:left'  => ' .sample-shortcode-gradient-left-css-SELECO ',
//        '[sample_shortcode]:gradient:left:declaration'  => 'border: 1px solid transparent-trans',
//    );
//
//    /*
//     * Demonstration on how to create a shortcode override.
//     */
//    $css_properties['sample-option'] = array(
//        'type'											=> 'custom',
//        'gradient'                             			=> 'gradient-selector',
//        'transitions'                          			=> 'transitions-selector',
//        'gradient:left'                        			=> 'gradient-left-selector',
//        'gradient:up'                          			=> 'gradient-up-selector',
//        '[sample_shortcode]:gradient'          			=> 'sample-shortcode-gradient-selector',
//        '[sample_shortcode]:gradient:left'     			=> ' .sample-shortcode-gradient-left-selector',
//        'gradient:left:declaration'						=>  '-webkit-border-image: gradient-left-DECO',
//        '[sample_shortcode]:gradient:left:declaration'  => 'border: sample-shortcode-gradient-left-DECO',
//        'gradient:filter'                               =>  array( 'left', 'right' )
//    );
//
    /* 
     * Demonstration on how to create a shortcode override.
     */
//    $css_properties['gradient-demo:sample_shortcode'] = array(
//        'type'									=> 'custom',
//        'gradient'                             	=> 'gradient-shortcode-override',
//    );
	//
    /*
     * Demonstration on how to create a shortcode override.
     */
//    $css_properties['gradient-demo:sample_shortcode2'] = array(
//        'type'									=> 'custom',
//        'gradient'                             	=> 'gradient-shortcode-override',
//    );

