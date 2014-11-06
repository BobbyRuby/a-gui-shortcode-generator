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

    /** @todo - Fix this doc block
     * Create Shortcode Needed arguments - Create Shortcode is called by generateShortcode
     * @param string $type - enclosed or self-closed-
     * @param string $tag - the shortcode string excluding the "[]" to invoke the shortcode function-
     * @param string $description - A short description of the shortcode-
     * @param string $allowsShortcodes - Whether or not this shortcode will permit other shortcodes to be wrapped in it. ('Yes' or 'No')-
     * @param string $htmlTag - the tag the enclosed shortcodes will wrap their content with.-
     * @param string $id - the id of the html tag.-
     * @param string $class - class or classes of the html tag.-
     * @param string $inlineStyle - inline styles for the shortcode.-
     * @param array $html_atts - Multideminsional array containing html attribute names and static values - array( 'names' => array( name0, name1, name2 ) , 'values' => array( 'value0', value1', value2'  ) );
     * @param array $atts - Multideminsional array containing shortcode attribute names and default values - array( 'names' => array( name0, name1, name2 ) , 'values' => array( 'value0', value1', value2'  ) );
     * @param array $mapped_atts - Multideminsional array containing html tag and shortcode attribute names that have been matched up or 'mapped' - array( 'match_html_att_names' => array( name0, name1, name2 ) , 'match_shortcode_att_names' => array( 'value0', value1', value2'  ) );
     * @return mixed
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
        $html_atts = $args['html_atts'];
        $atts = $args['atts'];
        $mapped_atts = $args['mapped_atts'];

        if (!$atts) $atts = array();
        $this->shortcode = $this->createShortcode($type, $tag, $description, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts);
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
     * Takes an HTML tag inner text and returns an indexed array containing the end tag and start tag.
     * @param string $htmlTag - the tag the NonATT enclosed shortcodes will wrap their content with.
     * @return array - indexed array containing the end tag and start tag with classes and/or id
     */
    protected function getHtmlStartEndTag($htmlTag, $id, $class, $inlineStyle, $html_atts)
    {
        $stg = "<$htmlTag id='$id' class='$class' style='$inlineStyle' ";
        for ($i = 0; $i < count($html_atts['names']); $i++) {
            $stg .= $html_atts['names'][$i] . '="' . $html_atts['values'][$i] . '" ';
        }
        $stg .= '>';
        $etg = "</$htmlTag>";
        return array($stg, $etg);
    }

    /**
     * @param string $type - enclosed or self-closed
     * @param string $tag - the shortcode string excluding the "[]" to invoke the shortcode function
     * @param string $description - A short description of the shortcode
     * @param string $allowsShortcodes - Whether or not this shortcode will permit other shortcodes to be wrapped in it. ('Yes' or 'No')
     * @param string $htmlTag - the tag the enclosed shortcodes will wrap their content with.
     * @param string $id - the id of the html tag.
     * @param string $class - class or classes of the html tag.
     * @param string $inlineStyle - inline styles for the shortcode.
     * @param array $html_atts - Multideminsional array containing html attribute names and static values - array( 'names' => array( name0, name1, name2 ) , 'values' => array( 'value0', value1', value2'  ) );
     * @param array $atts - Multideminsional array containing shortcode attribute names and default values - array( 'names' => array( name0, name1, name2 ) , 'values' => array( 'value0', value1', value2'  ) );
     * @param array $mapped_atts - Multideminsional array containing html tag and shortcode attribute names that have been matched up or 'mapped' - array( 'match_html_att_names' => array( name0, name1, name2 ) , 'match_shortcode_att_names' => array( 'value0', value1', value2'  ) );
     * @return mixed
     */
    abstract function createShortcode($type, $tag, $description, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts); // the factory method to be implemented by concrete creator classes
}