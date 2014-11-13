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
    public $dbid;
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
    public $html_id_OR; // bool
    public $htmlTagOR; // bool
    public $shortcodes_atts_str; // contains all attributes for this shortcode
    public $shortcode_atts; // contains all attributes for this shortcode
    public $error;

    public function logShortcodeToDatabase()
    {
        global $wpdb;
        $date = date('Y-m-d H:i:s');
        $table = $wpdb->prefix . 'agsg_shortcodes';
        $wpdb->insert($table,
            array( // columns
                'type' => $this->type,
                'name' => $this->name,
                'kind' => $this->kind,
                'tag' => $this->tag,
                'example' => $this->example,
                'code' => $this->shortcode_code,
                'created_datetime' => $date,
            ),
            array( // formats
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            )
        );
        // set the dbid
        $this->dbid = $wpdb->insert_id;
        if ($wpdb->last_error) {
            $this->error = $wpdb->last_error;
        }
    }

    /**
     * Query wpdb for row with that tag
     * if row exists return true
     * if it doesn't return false
     */
    public function tagExists($tag)
    {
        global $wpdb;
        $exists = $wpdb->get_row("SELECT * FROM $wpdb->prefix" . "agsg_shortcodes WHERE tag = '$tag'");
        $this->tag = $tag;
        if (!$exists == null) {
            $exists = true;
            $this->error = 'A shortcode with the tag "' . $this->tag . '" already exists!';
        }
        $this->exists = $exists;
        return $exists;
    }

    abstract public function generateExample();

    abstract public function generatePreview();
}