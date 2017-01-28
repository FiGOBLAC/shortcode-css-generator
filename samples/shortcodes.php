<?php
function shortcode_1( $atts, $content, $shortcode ){

    $defaults = array(

        'id'                =>'my-id',
        'class'             =>'',
        'font-size'         =>'18px',
        'text-align'        =>'center',
        'padding'           =>'5px;',
        'color'             =>'white',
        'background-color'  =>'#0b6c89',
    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode );

    extract( $defaults ) ;

    shortcode_cssg( $shortcode, $defaults );

    return "<div id='$id' class='$class my-shortcode-sample'>$content</div>";
}

add_shortcode( 'my_shortcode', 'shortcode_1');

function shortcode2( $atts, $content, $shortcode ){

    $defaults = array(

        'id'                        =>'sc2',
        'class'                     => 'my-shortcode-sample',
        'font-size'                 =>'18px',
        'text-align'                =>'center',
        'gradient-sample'           =>'center',
        'gradient-sample-color'     =>'#0b6c89',
    );

    $defaults = shortcode_atts( $defaults, $atts , $shortcode );

    extract( $defaults ) ;

    shortcode_cssg( $shortcode, $defaults );

    return "<div id='$id' class='$class'>$content</div>";
}

add_shortcode( 'my_shortcode2', 'shortcode2');
