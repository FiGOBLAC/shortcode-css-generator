<?php
add_shortcode( 'my_shortcode', 'shortcode_1');
add_shortcode( 'my_shortcode2', 'shortcode2');
add_shortcode( 'my_shortcode3', 'shortcode3');

function shortcode_1( $atts, $content, $shortcode ){

    $defaults = array(

        'id'                =>'shortcode-1',
        'class'             =>'',
        'font-size'         =>'18px',
        'text-align'        =>'center',
        'padding'           =>'5px;',
        'color'             =>'white',
        'background-color'  =>'rgba(105, 87, 183, 0.97)',
    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode );

    extract( $defaults ) ;

    shortcode_cssg( $shortcode, $defaults );

    return "<div id='$id' class='$class my-shortcode-sample'>$content</div>";
}

function shortcode2( $atts, $content, $shortcode ){

    $defaults = array(

        'id'                        =>'shortcode-2',
        'class'             =>'',
        'font-size'         =>'18px',
        'text-align'        =>'center',
        'padding'           =>'5px;',
        'color'             =>'white',
        'background-color'  =>'rgba(188, 54, 142, 0.97)',

    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode );

    extract( $defaults ) ;

    shortcode_cssg( $shortcode, $defaults );

    return "<div id='$id' class='$class'>$content</div>";
}

function shortcode3( $atts, $content, $shortcode ){

    $defaults = array(

        'id'                        => 'shortcode-3',
        'class'             =>'',
        'font-size'         =>'18px',
        'text-align'        =>'center',
        'padding'           =>'5px;',
        'color'             =>'white',
        'background-color'  =>'rgba(188, 54, 142, 0.97)',
    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode );

    extract( $defaults ) ;

    shortcode_cssg( $shortcode, $defaults );

    return "<div id='$id' class='$class'>$content</div>";
}
