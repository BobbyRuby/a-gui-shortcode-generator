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
        $conditions = $args['conditions'];
        $preview = $args['preview'];
        $regenerate = $args['regenerate'];

        if (!$atts) $atts = array();

        $this->shortcode = $this->createShortcode($type, $tag, $description, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions, $preview, $regenerate);
        if ($preview)
            $this->shortcode->preview = $preview;
        if ($regenerate)
            $this->shortcode->regenerate = $regenerate;

        $this->shortcode->filename = plugin_dir_path(__FILE__) . 'agsg_shortcodes.php';

        if ($this->shortcode->preview) { // if this is a preview
            $this->print_shortcode_msg('preview');
        } else if ($this->shortcode->regenerate) { // are we regenerating?
            $this->regenerate_shortcode_code();
            $this->update_shortcode();
            $this->add_shortcode_to_file();
            $this->print_shortcode_msg();
        } else if (!$this->shortcode->exists) { // if one exists and there is a regen is set still add to file
            $this->add_shortcode_to_file();
            $this->print_shortcode_msg();
        } else { // one exists but not regen
            $this->print_error_data('exists');
        }

    }

    private function regenerate_shortcode_code()
    {
        $source_file = file_get_contents($this->shortcode->fileName);
        $source = preg_replace('/(\/\/' . $this->shortcode->tag . ')(.*)(\/\/' . $this->shortcode->tag . ')/s', "", $source_file);
        file_put_contents($this->shortcode->fileName, $source);
        echo 'Old Shortcode code deleted from file....<br>';
        agsgPlugin::addShortcodeToFile($this->shortcode->tag, $this->shortcode->shortcode_code);
    }

    private function update_shortcode()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'agsg_shortcodes';
        $wpdb->update(
            $table,
            array(
                'code' => $this->shortcode->shortcode_code,
                'example' => $this->shortcode->example,
                'description' => $this->shortcode->description,
                'kind' => $this->shortcode->kind,
            ),
            array('tag' => $this->shortcode->tag),
            array(
                '%s',
                '%s',
                '%s',
                '%s'
            ),
            array('%s')
        );
        echo 'Shorcode database row updated for ' . $this->shortcode->tag . '...';
    }

    private function add_shortcode_to_file()
    {
        if ($fh = fopen($this->shortcode->filename, 'a')) { // open file agsg for appending if true so we can append our shortcode
            fwrite($fh, PHP_EOL . $this->shortcode->shortcode_code . PHP_EOL);
        }
        fclose($fh);
    }

    private function print_shortcode_msg($type = '')
    {
        if ($type === 'preview') {
            $html = '<h3>Some details about the shortcode "' . $this->shortcode->name . '", you may want to create with AGSG</h3>';
            $html .= '<h4>The code for this shortcode will be added to file located at:</h4>
            <i>"' . $this->shortcode->filename . '"</i><br/>';
            $html .= '<h4>The code generated and that will be added to the file above by AGSG...</h4>
            <textarea readonly="readonly">' . $this->shortcode->shortcode_code . '</textarea>';
            $html .= '<h4>An Example of how to use the shortcode you may create...</h4>
            <i>"' . $this->shortcode->example . '"</i><br/>';
        } else {
            $html = '<h3>Some details about the shortcode "' . $this->shortcode->name . '", you just created with AGSG</h3>';
            $html .= '<h4>The code for this shortcode was added to file located at:</h4>
            <i>"' . $this->shortcode->filename . '"</i><br/>';
            $html .= '<h4>The code generated and added to the file above by AGSG...</h4>
            <textarea readonly="readonly">' . $this->shortcode->shortcode_code . '</textarea>';
            $html .= '<h4>An Example of how to use the shortcode you created...</h4>
            <i>"' . $this->shortcode->example . '"</i><br/>';
        }
        echo $html;
    }

    private function print_error_data($err_type)
    {
        global $wpdb;
        $html = '<h3>' . $this->shortcode->error . '</h3>';
        if ($err_type === 'exists') {
            // get old shortcode for quick comparison
            $table = $wpdb->prefix . 'agsg_shortcodes';
            $sql = $wpdb->prepare(
                'SELECT code FROM ' . $table . ' WHERE tag = %s',
                $this->shortcode->tag
            );
            $oldshortcode_code = $wpdb->get_var($sql);
            $html .= '<form id="regen" method="post">';
            $html .= '<h4><label for="tag">Shortcode Tag</label></h4><input id="tag" name="tag" type="text" value="' . $this->shortcode->tag . '" readonly />';
            $html .= '<h4><label for="old_shortcode">Old Shortcode Code</label></h4><textarea id="old_shortcode" readonly="readonly">' . $oldshortcode_code . '</textarea>';
            $html .= '<h4><label for="new_shortcode">Preview of Replacement Code for Shortcode if Regenerated</label></h4><textarea id="new_shortcode" name="new_code" readonly="readonly">' . $this->shortcode->shortcode_code . '</textarea>';
            $html .= $this->getOverwriteShortcodeButton();
            $html .= '</form>';
            /**
             * Begin eagsg page regen form
             */
            $script = <<<SCRIPT
            <script type="text/javascript">
            var dir = jQuery('[name="agsg_install_url"]').val(); // dir url of install from hidden meta
            var page = dir + 'class-agsgPlugin.php';
            var regenRequest = '';
            jQuery("#regen").submit(function (event) {
                // prevent default posting of form
                event.preventDefault();
                // abort any pending regenRequest
                if (regenRequest) {
                    regenRequest.abort();
                }
                // setup some local variables
                var form = jQuery(this);
                // let's select and cache all the fields
                var inputs = jQuery(form).find("input, select, button, textarea");
                // serialize the data in the form
                var serializedData = jQuery(form).serialize();

                // let's disable the inputs for the duration of the ajax regenRequest
                jQuery(inputs).prop("disabled", true);
                // fire off the regenRequest
                regenRequest = jQuery.ajax({
                    url: page,
                    type: "post",
                    data: { shortcode_rewrite: serializedData }
                });
                // callback handler that will be called on success
                regenRequest.done(function (html, response, textStatus, jqXHR) {
                    // output shortcode information
                    jQuery('#agsg_shortcode_preview').append(html);
                });
                // callback handler that will be called on failure
                regenRequest.fail(function (jqXHR, textStatus, errorThrown) {
                    // log the error to the console
                    console.error(
                        "The following error occured: " +
                        textStatus, errorThrown
                    );
                });
                // if the regenRequest failed or succeeded
                regenRequest.always(function () {
                    // reenable the inputs
                    jQuery(inputs).prop("disabled", false);
                });

    });
    </script>
SCRIPT;
            /**
             * End eagsg page regen form
             */

        }
        echo $html;
        echo $script;
    }

    private function getOverwriteShortcodeButton()
    {
        return '<input type="submit" value="Delete old code and Regenerate Shortcode to above Specifications" class="button-primary" name="submit_agsg_regen">';
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
            $stg .= $html_atts['names'][$i] . "='" . $html_atts['values'][$i] . "' ";
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
     * $param array $conditions - Multimdeminsional array containing conditions data
     * $param bool  $preview - If flagged will only generate a preview
     * $param bool  $regenerate - If flagged, this will update the shortcode tag fed.
     * @return mixed
     */
    abstract function createShortcode($type, $tag, $description, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions, $preview, $regenerate); // the factory method to be implemented by concrete creator classes
}