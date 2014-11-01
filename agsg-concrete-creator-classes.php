<?php
/**
 * Concrete creator classes - They give us the type of shortcodes's we want
 * They extend agsgShortcodeGenerator and the implement factory method createShortcode
 * @package WordPress
 * @subpackage sagsgPluginD
 */
/**
 * Class agsgATTgenerator
 * Decides which type (enclosed OR self-closed) of attributed shortcodes to create.
 */
class agsgATTgenerator extends agsgShortcodeGenerator
{
    public function createShortcode($type, $htmlContent, $atts, $tag)
    {
        if ($type == 'enclosed') {
            $shortcode = new agsgATTenclosed($htmlContent, $atts, $tag);
        } else if ($type == 'self-closed') {
            $shortcode = new agsgATTselfclosed($htmlContent, $atts, $tag);
        } else {
            $shortcode = null;
        }
        return $shortcode;
    }
}

/**
 * Class agsgNonATTgenerator
 * Decides which type (enclosed OR self-closed) of NON attributed shortcodes to create.
 */
class agsgNonATTgenerator extends agsgShortcodeGenerator
{
    public function createShortcode($type, $htmlContent, $atts, $tag)
    {
        if ($type == 'enclosed') {
            $shortcode = new agsgNonATTenclosed($htmlContent, $atts, $tag);
        } else if ($type == 'self-closed') {
            $shortcode = new agsgNonATTselfclosed($htmlContent, $atts, $tag);
        } else {
            $shortcode = null;
        }
        return $shortcode;
    }
}