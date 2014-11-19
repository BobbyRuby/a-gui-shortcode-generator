<?php
/**
 * Concrete product classes - They produce our shortcodes when called from a generator
 * They extend agsgShortcode
 * @package WordPress
 * @subpackage AGSG
 */
/**
 * Class agsgATT
 * Responsible for creating the attributed shortcodes
 */
class agsgATT extends agsgShortcode
{
    /**
     * See createShortcode function in agsgShortcodeGenerator for param explanations
     * @param $tag
     * @param $allowsShortcodes
     * @param $htmlTag
     * @param $id
     * @param $class
     * @param $inlineStyle
     * @param $html_atts
     * @param $atts
     * @param $mapped_atts
     * @param $conditions
     * @param $description
     * @param $scripts
     * @param $styles
     */
    public function __construct($tag, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts, $conditions, $description, $scripts, $styles)
    {

        $this->name = $tag . '_agsg';
        $this->description = $description;
        $this->conditions = $conditions;
        $this->scripts = $scripts;
        $this->att_names = $atts['names'];
        $this->styles = $styles;
        $att_names = $atts['names'];
            $att_values = $atts['values'];
            $html_att_names = $html_atts['names'];
            $html_att_values = $html_atts['values'];
            $mapped_html_att_names = $mapped_atts['match_html_att_names'];
            $mapped_shortcode_att_names = $mapped_atts['match_shortcode_att_names'];
        error_reporting(0);
        $unmapped_html_att_names = array_diff($html_att_names, $mapped_html_att_names); // which weren't mapped?
        $unmapped_html_att_values = array_filter($html_att_values, array($this, 'filterHTMLattValues'));
            $filtered_shortcode_att_names = array_filter($mapped_shortcode_att_names, array($this, 'removeSelect'));
            $filtered_html_att_names = array_filter($mapped_html_att_names, array($this, 'removeSelect'));
        error_reporting(E_ALL);
        $att_match_str = '';
        $this->shortcodes_atts_str = '';
            $unmapped_html_atts = '';

            $this->kind = 'ATT';
        $this->tag = $tag;
        $this->id = $id;
            $this->class = $class;
            $this->htmlstg = "<$htmlTag>"; // default
            $this->htmletg = "</$htmlTag>"; // default

            for ($i = 0; $i < count($att_names); $i++) {
                $this->shortcode_atts[$att_names[$i]] = $att_values[$i];
                // check to see if htmlTag has been overridden by checking for the 'html_tag' att name.
                if ($att_names[$i] === 'html_tag') {
                    $this->htmlTagOR = true; // used below in HEREDOCS
                    // set the stg and etg
                    $this->htmlstg = 'Determine per use.';
                    $this->htmletg = 'Determine per use.';
                } // check to see if id has been overridden by checking for the 'id' att name.
                else if ($att_names[$i] === 'id') {
                    $this->html_id_OR = true; // used below in HEREDOCS
                }
                // build the shortcodes_atts_str array string to pass to shortcode_atts
                if ($i === 0) $this->shortcodes_atts_str = "array("; // only on 1st iteration
                if ($i !== count($att_names) - 1) { // not last iteration
                    $this->shortcodes_atts_str .= "'$att_names[$i]' => '$att_values[$i]',";
                } else { // last iteration
                    $this->shortcodes_atts_str .= "'$att_names[$i]' => '$att_values[$i]'";
                    $this->shortcodes_atts_str .= ')';
                }

            }
            // build a string that contains all unmapped HTML attributes and their static values
            for ($i = 0; $i < count($unmapped_html_att_values); $i++) {
                if ($unmapped_html_att_values[$i] && $unmapped_html_att_names[$i])
                    $unmapped_html_atts .= ' ' . $unmapped_html_att_names[$i] . '="' . $unmapped_html_att_values[$i] . '"';
            }

            // build the html attributes where values have been mapped to shortcode attributes string
            for ($i = 0; $i < count($filtered_html_att_names); $i++) {
                // create single variable for each attribute name match
                $match_str = <<<STRING
 $filtered_html_att_names[$i]="'.
STRING;
                $match_str .= <<<'VARSTR'
isset($a['
VARSTR;
                $match_str .= <<<STRING
$filtered_shortcode_att_names[$i]']).'"
STRING;
                $att_match_str .= $match_str;
            }
            // generate a typical use example
            $this->example = $this->generateExample($this->shortcode_atts); // $this->tag is set and ready to use so no need to use $tag $this->mapped_atts is set as well

            // create function
            $this->shortcode_code = <<<STRING
//$tag
/**
* $this->description
**/
function $this->name (
STRING;
            $this->shortcode_code .= <<<'VARSTR'
 $atts, $content = null ) {

VARSTR;
            $this->shortcode_code .= <<<'VARSTR'
    $a = shortcode_atts(
VARSTR;
            $this->shortcode_code .= <<<STRING
 $this->shortcodes_atts_str,
STRING;
            $this->shortcode_code .= <<<'VARSTR'
 $atts );
    $id = '';
    $classes = '';
    $styles = '';
    $html_tag = '';
    if (isset($a['id'])) {
        $id = $a['id'];
    }
    if (isset($a['class'])) {
        $classes = $a['class'];
    }
    if (isset($a['style'])) {
        $styles = $a['style'];
    }
    if (isset($a['html_tag'])) {
        $html_tag = $a['html_tag'];
    }
 if($content){
    $var =
VARSTR;
            // is their an override for the html tag
            if ($this->htmlTagOR) {
                $this->shortcode_code .= <<<'VARSTR'
'<'.$html_tag.
VARSTR;
            } else { // there isn't
                $this->shortcode_code .= <<<STRING
'<$htmlTag'.
STRING;
            }
            // is their an override for the id
            if ($this->html_id_OR) {
                $this->shortcode_code .= <<<'VARSTR'
' id="'.$id.
VARSTR;
            } else { // there isn't
                $this->shortcode_code .= <<<STRING
' id="$id'.
STRING;
            }
            // add id and static classes
            $this->shortcode_code .= <<<STRING
'" class="$class '
STRING;
            // add attributed classes
            $this->shortcode_code .= <<<'VARSTR'
 .$classes.'"
VARSTR;
            // add static and attributed inline_style
            $this->shortcode_code .= <<<STRING
 style="$inlineStyle
STRING;
            $this->shortcode_code .= <<<'VARSTR'
 '.$styles.'"
VARSTR;
            // add in the html attributes where values have NOT been mapped to shortcode attributes
            $this->shortcode_code .= <<<STRING
 $unmapped_html_atts
STRING;
            // add in the html attributes where values have been mapped to shortcode attributes
            $this->shortcode_code .= <<<STRING
 $att_match_str
STRING;
            // check if shortcodes are allowed to be embedded in this shortcode
            if ($allowsShortcodes === 'No') {
                $this->shortcode_code .= <<<'VARSTR'
>'.$content
VARSTR;
            } else { // allowed
                $this->shortcode_code .= <<<'VARSTR'
>'.do_shortcode($content)
VARSTR;
            }
            $this->shortcode_code .= <<<EOD
.'</$htmlTag>';
}else{
EOD;
        $this->shortcode_code .= <<<'VARSTR'

  $var = '';
}
VARSTR;

        // build conditions
        $this->buildConditions(); // adds all conditions to this->shortcode_code - uses this->conditions set above
        // build scripts
        $this->buildScripts(); // adds all scripts to this->shortcode_code - uses this->scripts set above
        // build styles
        $this->buildStyles(); // adds all styles to this->shortcode_code - uses this->styles set above

        $this->shortcode_code .= <<<'VARSTR'

    return $var;
VARSTR;
            $this->shortcode_code .= <<<STRING

}
add_shortcode( '$tag', '$this->name' );
//$tag
STRING;
    }

    public function generateExample()
    {
        $example = "Enclosing Example -- [$this->tag ";
        foreach ($this->shortcode_atts as $a => $dv) {
            $example .= "$a=\"$dv\" ";
        }
        $example .= "]Some shortcode content.[/$this->tag]<br/>";
        $example .= "Self Closing Example -- [$this->tag ";
        foreach ($this->shortcode_atts as $a => $dv) {
            $example .= "$a=\"$dv\" ";
        }
        $example .= " /]";
        return $example;
    }

    public function generatePreview()
    {

    }

    public function filterHTMLattValues($v)
    {
        if ($v != false) {
            return true;
        }
        return false;
    }

    public function removeSelect($v)
    {
        if ($v != 'select') {
            return true;
        }
        return false;
    }
}

class agsgNonATT extends agsgShortcode
{
    /**
     * See createShortcode function in agsgShortcodeGenerator for param explanations
     * @param $htmlstg
     * @param $htmletg
     * @param $tag
     * @param $allowsShortcodes
     * @param $description
     * @param $id
     * @param $class
     * @param $scripts
     * @param $styles
     */
    public function __construct($htmlstg, $htmletg, $tag, $allowsShortcodes, $description, $id, $class, $scripts, $styles)
    {
            // set some info we want to store or use
            $this->name = $tag . '_agsg';
        $this->scripts = $scripts;
        $this->styles = $styles;
        $this->description = $description;
        $this->tag = $tag;
        $this->kind = 'NonATT';
        $this->htmlstg = $htmlstg;
            $this->htmletg = $htmletg;
            $this->id = $id;
            $this->class = $class;
            $this->example = $this->generateExample(); // $this->tag is set and ready to use so no need to use $tag

            $this->shortcode_code = <<<EOD
//$tag
/**
* $this->description
**/
function $this->name
EOD;
            $this->shortcode_code .= <<<'EOD'
( $atts, $content = null )
EOD;
        // build scripts
        $this->buildScripts(); // adds all scripts to this->shortcode_code - uses this->scripts set above
        // build styles
        $this->buildStyles(); // adds all styles to this->shortcode_code - uses this->styles set above
        $this->shortcode_code .= <<<EOD
{
    return "$htmlstg
EOD;
            // check if shortcodes are allowed to be embedded in this shortcode
            if ($allowsShortcodes === 'No') {
                $this->shortcode_code .= <<<'EOD'
$content
EOD;
            } else { // allowed
                $this->shortcode_code .= <<<'EOD'
do_shortcode($content)
EOD;

            }
            $this->shortcode_code .= <<<EOD
$htmletg";
}
add_shortcode( '$tag', '$this->name' );
//$tag
EOD;
    }

    public function generateExample()
    {
        return "[$this->tag]Place some other content here[/$this->tag]";
    }

    public function generatePreview()
    {

    }
}