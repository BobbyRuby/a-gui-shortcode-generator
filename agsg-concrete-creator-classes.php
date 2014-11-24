<?php
/**
 * Concrete creator classes - They give us the type of shortcodes's we want
 * They extend agsgShortcodeGenerator and the implement factory method createShortcode
 * @package WordPress
 * @subpackage AGSG
 */
/**
 * Class agsgATTgenerator
 * Creates shortcodes with attributes
 */
class agsgATTgenerator extends agsgShortcodeGenerator
{
    public function createShortcode($type, $tag, $description, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions, $scripts, $styles)
    {
        /**
         * To extend this plugin, just make another generator class that implements createShortcode in the same manner regardless of what the products use.  Make sure you include the main plugin file in your php file
         */
        $shortcode = new agsgATT($tag, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions, $description, $scripts, $styles);
        $shortcode->type = $type;
        return $shortcode;
    }
}

/**
 * Class agsgNonATTgenerator
 * Creates shortcodes without attributes
 */
class agsgNonATTgenerator extends agsgShortcodeGenerator
{
    public function createShortcode($type, $tag, $description, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions, $scripts, $styles)
    {
        /**
         * To extend this plugin, just make another generator class that implements createShortcode in the same manner regardless of what the products use.  Make sure you include the main plugin file in your php file
         */
        // get our open and close tags since were are going to wrap something rather than replace the shortcode and add in our classes/ids/inlineStyles while were at it
        $htmlTag = $this->getHtmlStartEndTag($htmlTag, $id, $class, $inlineStyle, $html_atts);
        $htmlstg = $htmlTag[0];
        $htmletg = $htmlTag[1];
        $shortcode = new agsgNonATT($htmlstg, $htmletg, $tag, $allowsShortcodes, $description, $id, $class, $scripts, $styles);
        $shortcode->type = $type;
        return $shortcode;
    }
}

/**
 * Allow condition statements to be controlled by global and post variables
 * Add in ability to use if else statements - May require rework of conditions to thickbox - examine the way conditions are added.
 *
 * The ways that scripts and styles are conditionally enqueue is:
 * 1 - Via adding them in HTML 5 syntax to the WYSIWYG editor that is controlled by the condition in question.
 * 2 - By setting a string value that calls enqueue script / style for that script when the string value is found in the post's content
 *
 *
 ******
 * All types may have multiple embedded styles / scripts and or external ones.
 *
 * Shortcode Types
 ** Enclosed
 *** 1 - Enclosed shortcodes that display conditional content above or below the enclosed content or both.
 *** 2 - Enclosed shortcodes that have html attributes populated by shortcode attributes through mapping.
 *** 3 - Enclosed shortcodes that have html attributes populated by shortcode attributes through mapping, that display conditional content above or below, have static external scripts / styles, conditional external script and styles, and / or embedded scripts and styles.
 *** -- - Enclosed shortcodes that have nothing but the base classes, an id, or styles.
 ** Enclosed
 *** 1 - Enclosed shortcodes that display conditional content above or below the enclosed content or both.
 *** 2 - Enclosed shortcode that have html attributes populated by shortcode attributes through mapping.
 *
 */