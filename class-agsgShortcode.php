<?php
/**
 * Class agsgShortcode
 * Abstract class to interface and is implemented by all shortcode concrete classes*
 * You can extend this class to override the functions, please do not directly edit it, if you do make SURE you save a copy.
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
    public $conditions;
    public $scripts;
    public $styles;
    public $att_names;

    /**
     * Build all conditions     *
     */
    public function buildConditions()
    {
        if (count($this->conditions)) {
            foreach ($this->conditions as $condition) {
                $type = $condition["type"];
                $operator = $condition["operator"];
                $attribute = 'self::$a[\'' . $condition["attribute"] . '\']';
                $value = $condition["value"];
                $tinyMCE = $condition["tinyMCE"];
                // parse tiny mce content and find references to attributes
                foreach ($this->att_names as $att_name) {
                    // if attributes exist
                    if (strpos($tinyMCE, '&lt;&lt;' . $att_name . '&gt;&gt;') || strpos($tinyMCE, '<<' . $att_name . '>>')) {
                        // get an array of just attributes to work with
                        preg_match('/(\&lt\;\&lt\;[a-z_0-9]+\&gt\;\&gt\;)/', $tinyMCE, $matches);
                        // cycle through each, create var = value string, and add to array
                        if (is_array($matches)) {
                            foreach ($matches as $iv) {
                                $iv = preg_replace('/(\&lt\;\&lt\;[a-z_0-9]+\&gt\;\&gt\;)/', 'self::$a[\'' . $att_name . '\']', $iv);
                                $ref_atts[] = "$$att_name = $iv";
                            }
                        } else {
                            // get an array of just attributes to work with
                            preg_match('/(<<[a-z_0-9]+>>)/', $tinyMCE, $matches);
                            // cycle through each, create var = value string, and add to array
                            if (is_array($matches)) {
                                foreach ($matches as $iv) {
                                    $iv = preg_replace('/(<<[a-z_0-9]+>>)/', 'self::$a[\'' . $att_name . '\']', $iv);
                                    $ref_atts[] = "$$att_name = $iv";
                                }
                            }
                        }
                    }
                    // replace original with the att var generated
                    $tinyMCE = str_replace('&lt;&lt;' . $att_name . '&gt;&gt;', "$$att_name", $tinyMCE);
                }
                if (isset($ref_atts) && is_array($ref_atts)) {
                    $ref_atts = array_unique($ref_atts);
                } else {
                    $ref_atts = array();
                }
                if ($value === true || $value === false || $value === 0 || $value === 1) {
                } else {
                    $value = "'$value'";
                }
                $this->shortcode_code .= <<<STRING

        $type ( $attribute $operator $value ){
STRING;
                foreach ($ref_atts as $ref_att) {
                    $this->shortcode_code .= <<<STRING

            $ref_att;
STRING;
                }
                $this->shortcode_code .= <<<'VARSTR'

            $var .=
VARSTR;
                $tinyMCE = preg_replace("#<script>(.*?)</script>#is", "", $tinyMCE);
                $tinyMCE = str_replace('"', "'", $tinyMCE);
                $tinyMCE = preg_replace("(\\\\')", "'", $tinyMCE);
                $this->shortcode_code .= <<<STRING
 "$tinyMCE";

STRING;
                $this->_buildInternalScriptFunction($condition['tinyMCE']); // adds closure functions for scripts and ending bracket for condition
            }
        }
    }

    /**
     * Build all styles if there any
     */
    public function buildStyles()
    {
        // check for and add external css
        if (is_array($this->styles) && count($this->styles)) {
            foreach ($this->styles as $style) {
                $css_handle = '"' . $style['handle'] . '"';
                $css_src = '"' . $style["src"] . '"';
                if (isset($style["deps"]) && $style["deps"] !== '') {
                    $css_deps = explode(',', $style["deps"]);
                    $css_deps_str = 'array( ';
                    // build array string
                    foreach ($css_deps as $css_dep) {
                        $css_deps_str .= "'$css_dep'";
                    }
                    $css_deps_str .= ' )';
                }else{
                    $css_deps_str = 'array()';
                }
                $css_ver = '"' . $style["ver"] . '"';
                $css_media = '"' . $style["media"] . '"';
                $this->shortcode_code .= <<<STRING

wp_enqueue_style( $css_handle, $css_src, $css_deps_str, $css_ver, $css_media );
STRING;
            }
        }
    }

    /**
     * Build all scritps if there are any
     */
    public function buildScripts()
    {
        // check for and add external js
        if (is_array($this->scripts) && count($this->scripts)) {
            foreach ($this->scripts as $script) {
                $js_handle = '"' . $script['handle'] . '"';
                $js_src = '"' . $script["src"] . '"';
                if (isset($script["deps"]) && $script["deps"] !== '') {
                    $js_deps = explode(',', $script["deps"]);
                    $js_deps_str = 'array( ';
                    // build array string
                    foreach ($js_deps as $js_dep) {
                        $js_deps_str .= "'$js_dep'";
                    }
                    $js_deps_str .= ' )';
                } else {
                    $js_deps_str = 'array()';
                }
                $js_ver = '"' . $script["ver"] . '"';
                $this->shortcode_code .= <<<STRING

wp_enqueue_script( $js_handle, $js_src, $js_deps_str, $js_ver, true );
STRING;
            }
        }
    }

    /**
     * Build internal scripts if there are any
     * Should be processed at the end after addShortcode
     * @param $tinyMCE - Raw data from a tinyMCE to parse
     * @param $var_string - PHP variables in the use() part of closure
     */
    private function _buildInternalScriptFunction($tinyMCE)
    {
        // find all script tags
        $count = preg_match('#<script>(.*?)</script>#is', $tinyMCE, $intscripts);
        // get everything between any script tags storing them each in index in intscripts array and how many in count
                if ($count) {
                    for ($i = 0; $i < $count; $i++) {
                        $intscript = str_replace('\"', "'", $intscripts[$i]);
                        // parse $intscript and find references to attributes
                        foreach ($this->att_names as $att_name) {
                            // if attributes exist
                            if (strpos($intscript, '<<' . $att_name . '>>') || strpos($intscript, '&lt;&lt;' . $att_name . '&gt;&gt;')) {
                                // get an array of just attributes to work with
                                preg_match('/(<<[a-z_0-9]+>>)/', $intscript, $matches);
                                // cycle through each, create var = value string, and add to array
                                if (is_array($matches)) {
                                    foreach ($matches as $iv) {
                                        $iv = preg_replace('/(<<[a-z_0-9]+>>)/', '$' . $att_name, $iv);
                                        $script_ref_use_atts[] = "$iv";
                                    }
                                } else {
                                    // get an array of just attributes to work with
                                    preg_match('/(\&lt\;\&lt\;[a-z_0-9]+\&gt\;\&gt\;)/', $tinyMCE, $matches);
                                    // cycle through each, create var = value string, and add to array
                                    if (is_array($matches)) {
                                        foreach ($matches as $iv) {
                                            $iv = preg_replace('/(\&lt\;\&lt\;[a-z_0-9]+\&gt\;\&gt\;)/', '$' . $att_name, $iv);
                                            $script_ref_use_atts[] = "$iv";
                                        }
                                    }
                                }
                            }
                            // replace original with the att var generated
                            $intscript = str_replace('&lt;&lt;' . $att_name . '&gt;&gt;', "<?php echo $$att_name; ?>", $intscript);
                            $intscript = str_replace('<<' . $att_name . '>>', "<?php echo $$att_name; ?>", $intscript);
                        }
                        $var_string = '';
                        if (isset($script_ref_use_atts) && is_array($script_ref_use_atts)) {
                            $script_ref_use_atts = array_unique($script_ref_use_atts);
                            for ($j = 0; $j < count($script_ref_use_atts) * 2; $j = $j + 2) {
                                if ($j === 0) {
                                    $var_string = $script_ref_use_atts[$j];
                                } else if ($j % 2 == 0) {
                                    $var_string .= ', ' . $script_ref_use_atts[$j];
                                }
                            }
                        }
                        $this->shortcode_code .= <<<'VARSTR'
    $cb =
VARSTR;
                        $this->shortcode_code .= <<<STRING
 function () use ( $var_string ) {
STRING;
                        $this->shortcode_code .= <<<STRING

        ?>
        $intscript
        <?php
}; // end closure function
STRING;
                        $this->shortcode_code .= <<<STRING

add_action( 'wp_footer',
STRING;
                        $this->shortcode_code .= <<<'VARSTR'
$cb );

} // end condition

VARSTR;

                    }
            }
    }

    abstract public function generateExample();

    abstract public function generatePreview();
}