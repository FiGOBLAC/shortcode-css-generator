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

All you need to do is call the shortcode_cssg() function and pass in the shortcode name as the first parameter and then pass in the shortcode defaults as the second parameter.
Example:

<pre>

shortcode_cssg( $shortcode, $defaults );

</pre>

Thats it. Now your shortcode is ready to generate css.
 
##Create custom css Properties options.
Creating custom css properties for shortcode options are extremely simple and follows a very simple format. For example if you wanted to create an option that allows the user to select a link color, you would choose a name to use for the option and register the name into the json file as a key. you would then choose the selector you want the option to affect followed by two underscores and then the css color property and then register the formate as the value in the json file as follows:

{
  "link_color: "a__color",
}

Then you would just use this option in the shortcode like this:

[sample_shortcode link-color: "green" ] My sample shortcode [/sample_shortcode]

The generator will use the id of the current shortcode and output a css declaration that will look like this:

#shortcode_id a {
    color: "green";
}

Thats it! Pretty neat huh? You can use any css property as long as you prepend with either an attribute, id or class selector followed by two underscores.

## Targeting A class
To target a class just use the '.' selector with the class name and use '#' whene targeting and id.

{
    "link-color: .my-class__color
}

## Using pseudo classes
To use pseudo classes like :hover just add :psuedo-name after the selector.
Example:

{
  "link_color: "a:hover__color"
}

## Targeting Multiple Selectors
When targeting multiple selectors you will need to use the '%1$s' format string between each selector set which will be replaced by the shortcode's id.
Example:
{
  "text-height: ".my-class, %1$s p__line-height",
}

The generated css ouput for this configuration will be

#shortcode_id .my-class, #shortcode_id a:hover {
    color: green;
}

## Targeting Children

Example:
{
  "container_height: ".my-header-class > div_height",
}

## Targeting Other Elements

Example:
{
  "header_background: ".my-header-class > div_background_color",
}


## Targeting Element Children By Classname

Example:
{
  "list_font_size: "my-new-class > li.active > a__font-size",
}

There are many more compbinations you can you use depending on your needs so feel free to experiment.

##Important
* Do not use two underscores for selector names.
* Unless you create your won custom property object you can only use one css property per line for example you cant do this
{
  "link_color: "a:hover__color, a__background-color",
}

## Create Custom CSS Property Objects

Custom CSS Property Objects are made up of 3 components

###Object Name
###Elements
###Restrictions
###Declarations


if you need this kind of setup or need something more complex you can create your own css property object.


