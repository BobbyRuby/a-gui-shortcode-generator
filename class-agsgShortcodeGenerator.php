<?php
/**
 * Class agsgShortcodeGenerator
 * Abstract class to interface and is implemented by all generator concrete classes
 * Creator Class - Defines our abstract Factory Method that is used to create products
 * @package WordPress
 * @subpackage sagsgPluginD
 */

abstract class agsgShortcodeGenerator
{
    public $shortcode;

    public function __construct()
    {
    }

    /**
     * Calls the factory method
     * @param $type - enclosed of self-closed
     * @param $atts - attributes of shortcode
     * @param $atts - attributes of shortcode
     * @param $tag - the shortcode tag to use the shortcode ( well the shortcode )
     */
    public function generateShortcode($type, $htmlContent, $atts, $tag)
    {
        if (!$atts) $atts = array();
        $this->shortcode = $this->createShortcode($type, $htmlContent, $atts, $tag);
        $this->shortcode->logShortcodeToDatabase();
        $this->shortcode->addShortcodeToFile();
    }

    abstract function createShortcode($type, $htmlContent, $atts, $tag); // the factory method to be implemented by concrete creator classes
}