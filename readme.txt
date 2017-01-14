#shortcode CSS Generator

== Description ==

Features

== Instructions ==
## Setting up your shortcode

## Setup your custom css object configuration

Using the shortcode css generator is extremely simple. if you are developing a new shortcode typically your code will probably look like this:
<pre>
$defaults = extract( array(
  'id' => '',
  'class => '',
  'option => '',
));

$defaults = shortcode_atts( $defaults, $atts, $shortcode );

Call the shortcode_cssg function and pass in the shortcode name as the first parameter and then pass in the shortcode defaults as the second parameter. Thats it. Now your shortcode is ready to generate css.
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
