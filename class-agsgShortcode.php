<?php
/**
 * Class agsgShortcode
 * Abstract class to interface and is implemented by all shortcode concrete classes *
 * Product Class - Defines a base class for the type of product were going to be making
 * @package WordPress
 * @subpackage AGSG
 */
abstract class agsgShortcode
{
    public $shortcode_code;
    public $type;
    public $name;
    public $kind;
    public $tag;
    public $htmlstg;
    public $htmletg;
    public $example;
    public $description;
    public $exists;
    public $filename;
    public $html_id_OR;
    public $htmlTagOR;
    public $shortcodes_atts_str; // contains all attributes for this shortcode
    public $shortcode_atts; // contains all attributes for this shortcode
    public $error;
    public $error_msg;
    public $preview;
    public $regenerate;

    abstract public function generateExample();

    abstract public function generatePreview();
}