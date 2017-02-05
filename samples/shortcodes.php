<?php

add_shortcode( 'sample_shortcode', 'sample_shortcode');
add_shortcode( 'sample_shortcode2', 'sample_shortcode2');
add_shortcode( 'sample_shortcode3', 'sample_shortcode3');
add_shortcode( 'sample_shortcode4', 'sample_shortcode4');
add_shortcode( 'sample_shortcode5', 'sample_shortcode5');

function sample_shortcode( $atts, $content, $shortcode ){

    $defaults = array(

        'id'                =>'shortcode-1',
        'class'             =>'shortcode-1',
        'shortcode-option'  =>'testing',
        'shortcode-option2'  =>'testing',
        'shortcode-option3'  =>'testing',
        'shortcode-option4'  =>'testing',
        'shortcode-option5'  =>'testing',
        'shortcode-option6'  =>'testing',
        'shortcode-option7'  =>'testing',
        'font-size'         =>'',
        'text-align'        =>'center',
        'padding'           =>'8px',
        'color'             =>'white',
        'background-color'  =>'#004b6b',
    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode );

    extract( $defaults ) ;

    shortcode_cssg( $shortcode, $defaults );

    return "<div id='$id' class='$class'>$content</div>";
}

function sample_shortcode2( $atts, $content, $shortcode ){

    $defaults = array(

        'id'				=>'shortcode-2',
        'class'				=>'shortcode-2',
        'header'			=>'Hover Me.... I\'m Going In!',
        'min-height'		=>'300px',
        'line-height'		=>'34px',
        'text-align'        =>'center',
        'padding'           =>'8px',
        'border'  			=>'1px solid lightgrey',
		// custom css properties.
        'headerbox-header-size'	=> '34px',
        'headerbox-font-size'	=> '18px',
        'headerbox-margin'		=> '0 0 20px 0',
        'headerbox-padding'		=> '20px 0',
        'headerbox-color'  		=> 'white',
        'headerbox-hover'  		=> 'lightgreen',
        'headerbox-background'  => '#004b6b',
    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode );

    extract( $defaults ) ;

    shortcode_cssg( $shortcode, $defaults );

   return "<div id='$id' class='$class'><div class='headerbox gradient'><header>$header</header></div>$content</div>";
}

function sample_shortcode3( $atts, $content, $shortcode ){

    $defaults = array(

        'id'					=>'shortcode-3',
        'class'					=>'shortcode-3',
		'header'				=>'Got Gradients?',
		'subheader'				=>'A shortcode created using a simple custom css property',
        'line-height'			=>'34px',
        'text-align'        	=>'center',
        'padding'           	=>'8px',
        'border'  				=>'1px solid lightgrey',
		// custom css properties.
        'headerbox-header-size'	=> '50px',
        'headerbox-font-size'	=> '18px',
        'headerbox-margin'		=> '0 0 20px 0',
		'headerbox-padding'		=>'60px 0',
        'headerbox-color'  		=> 'white',
        'gradient'				=> "steller",
        'gradient-left'			=> "#7474BF",
        'gradient-right'		=> "#348AC7",
    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode );

    extract( $defaults ) ;

    shortcode_cssg( $shortcode, $defaults );

    return "<div id='$id' class='$class'><div class='headerbox gradient'><header>$header</header><p>$subheader</p></div>$content</div>";
}

function sample_shortcode4( $atts, $content, $shortcode ){

    $defaults = array(

        'id'				=>'shortcode-4',
        'class'				=>'shortcode-4',
		'header'			=>'On The Edge? Round Things Off!',
		'subheader'			=>'Shorcode CSS Generator makes it easy for you when your shortcode need options for shapping.',
        'line-height'		=>'34px',
        'text-align'        =>'center',
        'padding'           =>'8px',
        'border'  			=>'1px solid lightgrey',

		// custom css properties.
        'headerbox-header-size'	=> '40px',
        'headerbox-font-size'	=> '18px',
        'headerbox-margin'		=> '0 0 20px 0',
		'headerbox-padding'	 	=> '60px 0',
        'headerbox-color'  		=> 'white',
		'gradient'				=> "maroon",
    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode );

    extract( $defaults ) ;

    shortcode_cssg( $shortcode, $defaults );

    return "<div id='$id' class='$class'><div class='headerbox gradient'><header>$header</header><p>$subheader</p></div>$content</div>";
}

function sample_shortcode5( $atts, $content, $shortcode ){

	$image_url = get_template_directory_uri() . '/shortcode-cssg/samples/image.jpg';

    $defaults = array(

        'id'				=> 'shortcode-5',
        'class'				=> 'shortcode-5',
		'header'			=> 'On The Edge? Round Things Off!',
		'subheader'			=> 'Shorcode CSS Generator makes it easy for you when your shortcode need options for shapping.',
        'line-height'		=> '34px',
        'text-align'        => 'center',
        'padding'           => '8px',
        'border'  			=> '1px solid lightgrey',

		// custom css properties.
        'headerbox-header-size'	=> '40px',
        'headerbox-font-size'	=> '18px',
        'headerbox-margin'		=> '0 0 20px 0',
		'headerbox-padding'	 	=>'60px 0',
        'headerbox-color'  		=> 'white',
		'headerbox-background'  => "black url( $image_url ) center no-repeat"
    );

    $defaults = shortcode_atts( $defaults, $atts );

    extract( $defaults ) ;

    shortcode_cssg( $shortcode, $defaults );

    return "<div id='$id' class='$class'><div class=><div class='headerbox gradient'></div>$content</div>";
//	<header>$header</header><p>$subheader</p>

}

