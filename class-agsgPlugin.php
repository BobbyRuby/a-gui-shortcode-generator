<?php
/*
Plugin Name: A GUI Shortcode Generator Plugin
Plugin URI:
Description: Generates shortcodes from WordPress admin page.  Make custom shortcodes in minutes without any coding knowledge.
Version: 0.0.1
Author: Bobby Ruby
Author URI:
*/
/**
 * Main Plugin Class
 * Calls all other classes for plugin.
 * @package WordPress
 */
include_once('class-agsgShortcodeGenerator.php');
include_once('class-agsgShortcode.php');
include_once('agsg-concrete-creator-classes.php');
include_once('agsg-concrete-product-classes.php');
class agsgPlugin
{
    private static $instance;

    private function __construct()
    {
        /**
         * Add filters or instantiate other objects here.
         */
        $shortcode = new agsgNonATTgenerator();
        $shortcode->generateShortcode('enclosed', "<p>I\'m a paragraph created by a shortcode</p>", array(), 'paragraph');
    }

    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}

agsgPlugin::get_instance();

include_once('agsg_shortcodes.php');