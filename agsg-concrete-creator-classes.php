<?php
/**
 * Concrete creator classes - They give us the type of shortcodes's we want
 * They extend agsgShortcodeGenerator and the implement factory method createShortcode
 * @package WordPress
 * @subpackage AGSG
 */
/**
 * Class agsgATTgenerator
 * Decides which type (enclosed OR self-closed) of attributed (ATT) shortcodes to create.
 */
class agsgATTgenerator extends agsgShortcodeGenerator
{
    public function createShortcode($type, $tag, $description, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions)
    {
        if ($type == 'enclosed') {
            $shortcode = new agsgATTenclosed($tag, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions, $description);
        } else if ($type == 'self-closed') {
//            $shortcode = new agsgATTselfclosed($htmlTag, $atts, $tag);
        } else {
            $shortcode = null;
        }
        return $shortcode;
    }
}

/**
 * Class agsgNonATTgenerator
 * Decides which type (enclosed OR self-closed) of non-attributed (NonATT) shortcodes to create.
 * @complete
 */
class agsgNonATTgenerator extends agsgShortcodeGenerator
{
    public function createShortcode($type, $tag, $description, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions)
    {
        // check the type
        if ($type == 'enclosed') {
            // get our open and close tags since were are going to wrap something rather than replace the shortcode and add in our classes/ids/inlineStyles while were at it
            $htmlTag = $this->getHtmlStartEndTag($htmlTag, $id, $class, $inlineStyle, $html_atts);
            $htmlstg = $htmlTag[0];
            $htmletg = $htmlTag[1];
            $shortcode = new agsgNonATTenclosed($htmlstg, $htmletg, $tag, $allowsShortcodes, $description, $id, $class);
        } else if ($type == 'self-closed') {
//            $shortcode = new agsgNonATTselfclosed($htmlTag, $atts, $tag);
        } else {
            $shortcode = null;
        }
        return $shortcode;
    }
}