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
    public $shortcode;
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
    public $htmlTagOR; // bool
    public $mapped_atts; // contains all attributes for this shortcode
    public $error;

    public function logShortcodeToDatabase()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'agsg_shortcodes';
        $wpdb->insert($table,
            array( // columns
                'type' => $this->type,
                'kind' => $this->kind,
                'tag' => $this->tag,
                'htmlstg' => $this->htmlstg,
                'htmletg' => $this->htmletg,
                'example' => $this->example,
                'description' => $this->description,
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
        if (!$wpdb->last_error) {
            // Set name of shortcode and its function
            $this->name = $this->tag . '_agsgShortcode_' . $wpdb->insert_id; // use the tag name for the function
            $wpdb->update($table,
                array( // columns
                    'name' => $this->name
                ),
                array( // wheres
                    'id' => $wpdb->insert_id
                ),
                array( // formats
                    '%s'
                ),
                array( // where formats
                    '%d'
                )
            );
        } else {
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