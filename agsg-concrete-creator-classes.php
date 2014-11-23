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
 * Allow conditions inside the content that can be controlled by shortcode attributes, items in the globals array,
 *
 *
 *
 *
 *
 * Shortcode Types
 * 1 - Enclosed shortcode with no shortcode attributes but has conditions for the content through using the globals array.
 * 2 - Emclosed shortcode
 */