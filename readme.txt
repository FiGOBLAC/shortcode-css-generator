#shortcode CSS Generator

== Description ==

Features

== Instructions ==
## Setting up your shortcode
Using the shortcode css generator is extremely simple. if you are developing a new shortcode typically your code will probably look like this:
<pre>
$defaults = extract( array(
  'id' => '',
  'class => '',
  'option => '',
));

$defaults = shortcode_atts( $defaults, $atts, $shortcode );
</pre>
All you need to do is call the shortcode_cssg() function and pass in the shortcode name as the first parameter and then pass in the shortcode defaults as the second parameter. Thats it. Now your shortcode is ready to generate css.
Example:

<pre>

shortcode_cssg( $shortcode, $defaults );

</pre>

## Edit and customize your list of allowed css properties
Navigate to the css.json folder. For each css property you wish to use, add a line with the name of the css property wrapped in double quotes followed by a semi-colon. Be careful to follow the correct formatting.
Examples:
}
"border" : "",
"margin" : "",
"margin-left" ""
}

If you want to add a placedhoder you may do so in between the quotes after the semi-colon. This will be overriden when the option is used via shortcode defaults.
Example: 
"border: "1px solid lightgrey",

Once you have added all the css properties you want to use make sure to remove the comma from the very last entry.


## Creating custom css properties
Sometimes, depending on the nature of the shortcode you are creating, you may need your css actions to be a little more specfic or more descriptive of what it does. For example lets say you need to give the shortcode the option to set a color when 'hovering over a link'  Edit the css.config.json file and add the name of the shortcode option followed by the selector, two underscores and the csss property that will represent the value type:
Example:
{
"link-color: "a:hover__color"
}

 The "link-color" is the name of the shortcode option which will be used to set the link color on the shortcode like this:
 [sample_shortcode link-color='green'] My Sample Shortcode [/sample_shortcode]
 
 The css output will by the generator will  use the shortcode id to procude a  css declaration that look like this:
 #shortcode_id a:hover { 
    color: green;
 }
 
 What if you wanted to add color to a font icon that has a class name of .ficon or change the fontize? Edit the css.config.json file and add..
 {
 "link-color: "a__font=size"
 "link-color: ".ficon__color"
 "link-color: "a:hover__color"

}
 
 Pretty neat huh?


## Createing custom css propoerty objects
