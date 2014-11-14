a-gui-shortcode-generator
=========================
With this generator you can generate shortcodes that are from complicated to the most basic.  It was built to allow those without programming knowledge to create shortcodes in which they could have never dreamed.
These shortcodes include those that embed videos or complete custom layouts and much much more.

#### It does this by providing the following features for generating code:
* Add HTML attriutes to wrapper elements for enclosing shortcodes.
* Add attriutes to shortcodes which you can use for lots of different implementations.
* Allow the mapping of shortcode attributes to HTML attributes.
* Allows the overriding of the id HTML attribute and html tag name in wrapper elements for enclosed shortcodes.
* A custom implementation of <a href="http://tinymce.com/" target="_blank" title="TinyMCE">TinyMCE</a> which allows the creation of tables, divs, images, video embeds and more to add customized content that is conditionally displayed while providing a custom "attribute reference" syntax to allow you to place attributed values within the content you want conditionally displayed.
* Add conditonal statements to shortcodes so you can display additional content on a per use basis when using enclosed shortcodes or when using self closed shortcodes, replace the shortcode with content conditionally per use. The content is displayed based on the value of an attribute you set up within a condition using the AGSG. The content also may contain references to other attibutes via the "attribute reference" syntax --> "&lt;&lt;i_am_an_attribute&gt;&gt;".
* Make enclosed shortcodes process other shortcodes.
   
## Installation:
Simple steps:

1. Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation.
2. Then activate the Plugin from Plugins page.
3. Done.

## Channelog
1.0.0 - Initial Release