<?php
/**
 * Class agsgShortcode
 * Abstract class to interface and is implemented by all shortcode concrete classes *
 * Product Class - Defines a base class for the type of product were going to be making
 * @package WordPress
 * @subpackage sagsgPluginD
 */
abstract class agsgShortcode
{
    public $shortcode;

    public function logShortcodeToDatabase()
    {
        echo 'Logging to database...<br/>';
    }

    public function addShortcodeToFile()
    {
        $fileName = 'agsg_shortcodes.php';
        if ($fh = fopen($fileName, 'a')) { // open file agsg for appending if true so we can append our shortcode
            echo "Adding new shortcode function to $fileName..." . '<br/>';
            fwrite($fh, PHP_EOL . $this->shortcode . PHP_EOL);
        }
        fclose($fh);
    }
}