<?php

add_shortcode( 'cssg_sample_shortcode', 'cssg_sample_shortcode');
add_shortcode( 'cssg_sample_shortcode2', 'cssg_sample_shortcode2');
add_shortcode( 'cssg_sample_shortcode3', 'cssg_sample_shortcode3');
add_shortcode( 'cssg_sample_shortcode4', 'cssg_sample_shortcode4');
add_shortcode( 'cssg_sample_shortcode5', 'cssg_sample_shortcode5');

function cssg_sample_shortcode( $atts, $content, $shortcode ){

    $defaults = array(

        'id'                		=> 'shortcode-demo-1',
        'class'             		=> 'shortcode-demo-1',
        'header'            		=> 'Add Any Native or Custom CSS Attribute!',
        'font-size'         		=> '16px',
        'text-align'        		=> 'center',
        'padding'           		=> '8px',
		'border'  					=> '1px solid lightgrey',
        'headerbox-padding-demo' 	=> '24px 0 45px 0',
        'headerbox-background-demo' => '#4a9f3b',
		'headerbox-font-size-demo' 	=> '28px',
		'headerbox-color-demo' 		=> 'white',
        'contentbox-padding-demo'   => '20px 0 0 0',
        'contentbox-height-demo'    => '200px',
		'contentbox-demo'			=> '',

    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode, 'demo' );

    extract( $defaults );

    shortcode_cssg( $shortcode, $defaults );

   	return "<div id='{$id}' class='promo-box{$class}'><div class='headerbox'><header>{$header}</header></div><div class='contentbox'>{$content}</div></div>";
}

function cssg_sample_shortcode2( $atts, $content, $shortcode ){

    $defaults = array(

        'id'                		=> 'shortcode-demo-2',
		'class'						=> 'shortcode-demo-2',
        'header'					=> 'Hover Me....<em> I\'m Going In!</em>',
        'min-height'				=> '300px',
        'line-height'				=> '34px',
        'text-align'        		=> 'center',
        'padding'           		=> '8px',
        'border'  					=> '1px solid lightgrey',
        'headerbox-padding-demo' 	=> '24px 0 45px 0',
        'headerbox-background-demo' => '#004b6b',
		'headerbox-font-size-demo' 	=> '28px',
		'headerbox-color-demo' 		=> 'white',
        'contentbox-padding-demo'   => '20px 0 0 0',
        'contentbox-height-demo'    => '200px',
		'contentbox-demo'			=> '',

    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode, 'demo' );

    extract( $defaults );

    shortcode_cssg( $shortcode, $defaults );

   	return "<div id='{$id}' class='promo-box{$class}'><div class='headerbox'><header>{$header}</header></div><div class='contentbox'>{$content}</div></div>";
}

function cssg_sample_shortcode3( $atts, $content, $shortcode ){

    $defaults = array(

        'id'						=> 'shortcode-demo-3',
        'class'						=> 'shortcode-demo-3',
		'header'					=> 'Got Gradients?',
		'subheader'					=> 'A shortcode created using a simple custom css property',
        'line-height'				=> '34px',
        'text-align'        		=> 'center',
        'padding'           		=> '8px',
        'border'  					=> '1px solid lightgrey',
        'headerbox-padding-demo' 	=> '24px 0 45px 0',
        'headerbox-background-demo' => '#7474BF',
		'headerbox-font-size-demo' 	=> '28px',
		'headerbox-color-demo' 		=> 'white',
        'contentbox-padding-demo'   => '20px 0 0 0',
        'contentbox-height-demo'    => '200px',
		'contentbox-demo'			=> '',
		'gradient-demo'				=> "steller",
        'gradient-left'				=> "#7474BF",
        'gradient-right'			=> "#348AC7",

    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode, 'demo' );

    extract( $defaults );

    shortcode_cssg( $shortcode, $defaults );

   	return "<div id='{$id}' class='promo-box{$class}'><div class='headerbox'><header>{$header}</header></div><div class='contentbox'>{$content}</div></div>";
}

function cssg_sample_shortcode4( $atts, $content, $shortcode ){

    $defaults = array(

        'id'						=> 'shortcode-demo-4',
        'class'						=> 'shortcode-demo-4',
		'header'					=> 'On The Edge? Round Things Off!',
		'subheader'					=> 'Shorcode CSS Generator makes it easy for you when your shortcode need options for shapping.',
        'line-height'				=> '34px',
        'text-align'        		=> 'center',
        'padding'           		=> '8px',
        'border'  					=> '1px solid lightgrey',
        'headerbox-padding-demo' 	=> '24px 0 45px 0',
        'headerbox-background-demo' => 'maroon',
		'headerbox-font-size-demo' 	=> '28px',
		'headerbox-color-demo' 		=> 'white',
        'contentbox-padding-demo'   => '20px 0 0 0',
        'contentbox-height-demo'    => '200px',
		'contentbox-demo'			=> '',
		'gradient-demo'				=> "steller",
        'gradient-left'				=> "#7474BF",
        'gradient-right'			=> "#348AC7",

    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode, 'demo' );

    extract( $defaults );

    shortcode_cssg( $shortcode, $defaults );

   	return "<div id='{$id}' class='promo-box{$class}'><div class='headerbox'><header>{$header}</header></div><div class='contentbox'>{$content}</div></div>";
}

function cssg_sample_shortcode5( $atts, $content, $shortcode ){

	$image_url =  get_template_directory_uri() .  '/libs/shortcode-cssg/demo/image.jpg';

    $defaults = array(

        'id'						=> 'shortcode-demo-5',
        'class'						=> 'shortcode-demo-5',
		'header'					=> '',
		'subheader'					=> 'Shorcode CSS Generator makes it easy for you when your shortcode need options for shapping.',
        'line-height'				=> '34px',
        'text-align'        		=> 'center',
        'padding'           		=> '8px',
        'border'  					=> '1px solid lightgrey',
        'headerbox-padding-demo' 	=> '100px 0 100px 0',
        'headerbox-background-demo' => "black url( $image_url ) center no-repeat",
		'headerbox-font-size-demo' 	=> '28px',
		'headerbox-color-demo' 		=> 'white',
        'contentbox-padding-demo'   => '20px 0 0 0',
        'contentbox-height-demo'    => '200px',
		'contentbox-demo'			=> '',

    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode, 'demo' );

    extract( $defaults );

    shortcode_cssg( $shortcode, $defaults );

   	return "<div id='{$id}' class='promo-box{$class}'><div class='headerbox'><header>{$header}</header></div><div class='contentbox'>{$content}</div></div>";
}
