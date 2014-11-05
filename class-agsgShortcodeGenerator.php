<?php
/**
 * Class agsgShortcodeGenerator
 * Abstract class to interface and is implemented by all generator concrete classes
 * Creator Class - Defines our abstract Factory Method that is used to create products
 * @package WordPress
 * @subpackage AGSG
 */
abstract class agsgShortcodeGenerator
{
    public $shortcode;

    /**
     * Calls the factory method
     * @param string $type - enclosed of self-closed
     * @param string $htmlTag - Wrapper tag for content should pass only text in b/w "<>" tags.
     * @param string $atts - attributes of shortcode
     * @param string $tag - the shortcode tag to use the shortcode ( the [shortcode] )
     */
    public function generateShortcode($args)
    {
        // grab all args from the array sent
        $type = $args['type'];
        $tag = $args['tag'];
        $description = $args['description'];
        $allowsShortcodes = $args['allows_shortcodes'];
        $htmlTag = $args['html_tag'];
        $id = $args['id'];
        $class = $args['class'];
        $inlineStyle = $args['inline_styles'];
        $atts = $args['atts'];
        $defaults = $args['defaults'];
        if (!$atts) $atts = array();
        $this->shortcode = $this->createShortcode($type, trim($tag), trim($description), $allowsShortcodes, trim($htmlTag), trim($id), trim($class), trim($inlineStyle), $atts, $defaults);
        // access shortcode global 'exists' to see if we can create the file.
        if (!$this->shortcode->exists) {
            $this->addShortcodeToFile();
            $this->print_shortcode_data();
        } else {
            $this->print_error_data();
        }

    }

    private function print_shortcode_data()
    {
        $html = '<h3>Some details about the shortcode "' . $this->shortcode->name . '", you just created with AGSG</h3>';
        $html .= '<strong>File the code for this shortcode was added to:</strong><br/>
        <i>"' . $this->shortcode->filename . '"</i><br/>';
        $html .= '<h4>The code generated and added to the file above by AGSG...</h4>
        <textarea readonly="readonly">' . $this->shortcode->shortcode . '</textarea>';
        $html .= '<h4>An Example of how to use the shortcode you created...</h4>
        <i>"' . $this->shortcode->example . '"</i><br/>';
        echo $html;
    }

    /**
     * Adds a shortcode to the file 'agsg_shortcodes.php'
     */
    private function addShortcodeToFile()
    {
        $fileName = plugin_dir_path(__FILE__) . 'agsg_shortcodes.php';
        $this->shortcode->filename = $fileName;
        if ($fh = fopen($fileName, 'a')) { // open file agsg for appending if true so we can append our shortcode
            fwrite($fh, PHP_EOL . $this->shortcode->shortcode . PHP_EOL);
        }
        fclose($fh);
    }

    private function print_error_data()
    {
        $html = '<h3>Something went horribly wrong!</h3>';
        $html .= $this->shortcode->error;
        echo $html;
    }

    /**
     * Takes an HTML tag inner text and returns an indexed array containing the end tag and start tag
     * @param $htmlTag - should only be the text inside the "<>" tags.  Example:  For a <section> you just pass 'section'
     * @return array - indexed array containing the end tag and start tag with classes and/or id
     */
    protected function getHtmlStartEndTag($htmlTag, $id, $class, $inlineStyle)
    {
        $stg = "<$htmlTag id='$id' class='$class' style='$inlineStyle'>";
        $etg = "</$htmlTag>";
        return array($stg, $etg);
    }

    abstract function createShortcode($type, $tag, $description, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $atts, $defaults); // the factory method to be implemented by concrete creator classes
}