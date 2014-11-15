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
    public function createShortcode($type, $tag, $description, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions)
    {
        /**
         * To extend this plugin you can check the "type" here and call different product classes - you could always just make another generator class also but they must implement createShortcode in the same manner regardless of what the products use.
         * I choose just a non attributed and attributed but it could be different
         */
        $shortcode = new agsgATT($tag, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions, $description);
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
    public function createShortcode($type, $tag, $description, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions)
    {
        /**
         * To extend this plugin you can check the "type" here and call different product classes - you could always just make another generator class also but they must implement createShortcode in the same manner regardless of what the products use.
         * I choose just a non attributed and attributed but it could be different
         */
        // get our open and close tags since were are going to wrap something rather than replace the shortcode and add in our classes/ids/inlineStyles while were at it
        $htmlTag = $this->getHtmlStartEndTag($htmlTag, $id, $class, $inlineStyle, $html_atts);
        $htmlstg = $htmlTag[0];
        $htmletg = $htmlTag[1];
        $shortcode = new agsgNonATTenclosed($htmlstg, $htmletg, $tag, $allowsShortcodes, $description, $id, $class);
        $shortcode->type = $type;
        return $shortcode;
    }
}