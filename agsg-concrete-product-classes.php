<?php
/**
 * Concrete product classes - They give us the type of shortcodes's we want
 * They extend agsgShortcode
 * @package WordPress
 * @subpackage AGSG
 */
/** sample attributes
 * Classes you deam optional by the user ( any kind of tag )
 * The link, height, and width to a You Tube video
 * You tube video ex <iframe width="ATTRIBUTE VALUE" height="ATTRIBUTE VALUE" src="ATTRIBUTE VALUE" frameborder="0" allowfullscreen></iframe>
 */
class agsgATTenclosed extends agsgShortcode
{
    /**
     * @param string $type - enclosed or self-closed-
     * @param string $tag - the shortcode string excluding the "[]" to invoke the shortcode function-
     * @param string $description - A short description of the shortcode-
     * @param string $allowsShortcodes - Whether or not this shortcode will permit other shortcodes to be wrapped in it. ('Yes' or 'No')-
     * @param string $htmlTag - the tag the enclosed shortcodes will wrap their content with.- no "<>"
     * @param string $id - the id of the html tag.-
     * @param string $class - class or classes of the html tag.-
     * @param string $inlineStyle - inline styles for the shortcode.-
     * @param array $html_atts - Multideminsional array containing html attribute names and static values - array( 'names' => array( name0, name1, name2 ) , 'values' => array( 'value0', value1', value2'  ) );
     * @param array $atts - Multideminsional array containing shortcode attribute names and default values - array( 'names' => array( name0, name1, name2 ) , 'values' => array( 'value0', value1', value2'  ) );
     * @param array $mapped_atts - Multideminsional array containing html tag and shortcode attribute names that have been matched up or 'mapped' - array( 'match_html_att_names' => array( name0, name1, name2 ) , 'match_shortcode_att_names' => array( name0, name1, name2 )  );
     * @return mixed
     */
    public function __construct($tag, $allowsShortcodes, $htmlTag, $id, $class, $inlineStyle, $html_atts, $atts, $mapped_atts)
    {
        if (!$this->tagExists($tag)) {
            // Set up some local variables
            $att_names = $atts['names'];
            $att_values = $atts['values'];
            $html_att_names = $html_atts['names'];
            $html_att_values = $html_atts['values'];
            $mapped_html_att_names = $mapped_atts['match_html_att_names'];
            $mapped_shortcode_att_names = $mapped_atts['match_shortcode_att_names'];
            $unmapped_html_att_names = array_diff($html_att_names, $mapped_html_att_names); // which weren't mapped?
            $unmapped_html_att_values = array_filter($html_att_values, array($this, 'filterHTMLattValues'));
            $filtered_shortcode_att_names = array_filter($mapped_shortcode_att_names, array($this, 'removeSelect'));
            $filtered_html_att_names = array_filter($mapped_shortcode_att_names, array($this, 'removeSelect'));
            $att_match_str = '';
            $shortcodes_atts_str = '';
            $unmapped_html_atts = '';
            $shortcode_atts = array();

            // set some global vars we want to store or use
            $this->kind = 'ATT';
            $this->type = 'enclosed';
            $this->id = $id;
            $this->class = $class;

            for ($i = 0; $i < count($att_names); $i++) {
                // check to see if htmlTag has been overridden by checking for the 'html_tag' att name.
                // if so then set the tag to that default value and set global htmlTagOR to true
                if ($att_names[$i] === 'html_tag') {
                    $this->htmlTagOR = true;
                } else {
                    if ($i === 0) $shortcodes_atts_str = "array("; // only on 1st iteration
                    // build the shortcodes_atts_str array string to pass to shortcode_atts
                    $shortcode_atts[$att_names[$i]] = $att_values[$i];
                    if ($i !== count($att_names) - 1) { // not last iteration
                        $shortcodes_atts_str .= "'$att_names[$i]' => '$att_values[$i]',";
                    } else { // last iteration
                        $shortcodes_atts_str .= "'$att_names[$i]' => '$att_values[$i]'";
                        $shortcodes_atts_str .= ')';
                    }
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
$a['
VARSTR;
                $match_str .= <<<STRING
$filtered_shortcode_att_names[$i]'].'
STRING;
                $att_match_str .= $match_str;
            }
            // generate a typical use example
            $this->example = $this->generateExample(); // $this->tag is set and ready to use so no need to use $tag $this->mapped_atts is set as well
            // log the shortcode to the db and set its name
            $this->logShortcodeToDatabase();

            $this->shortcode = <<<STRING
function $this->name (
STRING;
            $this->shortcode .= <<<'VARSTR'
 $atts, $content = null ) {
VARSTR;
            $this->shortcode .= <<<'VARSTR'
    $a = shortcode_atts(
VARSTR;
            $this->shortcode .= <<<STRING
 $shortcodes_atts_str,
STRING;
            $this->shortcode .= <<<'VARSTR'
  $atts );
VARSTR;
            // is their an override for the html tag
            if ($this->htmlTagOR) {
                $this->shortcode .= <<<'VARSTR'
  return '<'.$a['html_tag'].
VARSTR;
            } else { // there isn't
                $this->shortcode .= <<<STRING
    return '<$htmlTag'.
STRING;
            }
            // add id and static classes
            $this->shortcode .= <<<STRING
' id="$id" class="$class '
STRING;
            // add attributed classes
            $this->shortcode .= <<<'VARSTR'
 .$a['class'].'"
VARSTR;
            // add static and attributed inline styles
            $this->shortcode .= <<<STRING
 style="$inlineStyle
STRING;
            $this->shortcode .= <<<'VARSTR'
 '.$a['inline_style'].'"
VARSTR;
            // add in the html attributes where values have NOT been mapped to shortcode attributes
            $this->shortcode .= <<<STRING
 $unmapped_html_atts
STRING;
            // add in the html attributes where values have been mapped to shortcode attributes
            $this->shortcode .= <<<STRING
 $att_match_str
STRING;
            // check if shortcodes are allowed to be embedded in this shortcode
            if ($allowsShortcodes === 'No') {
                $this->shortcode .= <<<'EOD'
>'.$content
EOD;
            } else { // allowed
                $this->shortcode .= <<<'EOD'
do_shortcode($content)
EOD;

            }
            $this->shortcode .= <<<EOD
.'</$htmlTag>';
}
add_shortcode( '$tag', '$this->name' );
EOD;
        }
    }

    public function generateExample()
    {
        return "[$this->tag]Place some other content here[/$this->tag]";
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

//class agsgATTselfclosed extends agsgShortcode
//{
//    public function __construct($htmlTag, $atts, $tag)
//    {
//    }
//}

class agsgNonATTenclosed extends agsgShortcode
{
    public function __construct($htmlstg, $htmletg, $tag, $allowsShortcodes, $description, $id, $class)
    {
        if (!$this->tagExists($tag)) {
            // set some info we want to store or use
            $this->kind = 'NonATT';
            $this->type = 'enclosed';
            $this->htmlstg = $htmlstg;
            $this->htmletg = $htmletg;
            $this->id = $id;
            $this->class = $class;
            $this->example = $this->generateExample(); // $this->tag is set and ready to use so no need to use $tag
            // log the shortcode to the db
            $this->logShortcodeToDatabase();
            $this->shortcode = <<<EOD
function $this->name
EOD;
            $this->shortcode .= <<<'EOD'
( $atts, $content = null )
EOD;
            $this->shortcode .= <<<EOD
{
    return "$htmlstg
EOD;
            // check if shortcodes are allowed to be embedded in this shortcode
            if ($allowsShortcodes === 'No') {
                $this->shortcode .= <<<'EOD'
$content
EOD;
            } else { // allowed
                $this->shortcode .= <<<'EOD'
do_shortcode($content)
EOD;

            }
            $this->shortcode .= <<<EOD
$htmletg";
}
add_shortcode( '$tag', '$this->name' );
EOD;
        }
    }

    public function generateExample()
    {
        return "[$this->tag]Place some other content here[/$this->tag]";
    }

    public function generatePreview()
    {

    }
}
//
//class agsgNonATTselfclosed extends agsgShortcode
//{
//    public function __construct($htmlTag, $atts, $tag)
//    {
//
//    }
//}