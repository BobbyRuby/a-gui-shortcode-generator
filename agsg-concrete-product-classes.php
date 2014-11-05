<?php
/**
 * Concrete product classes - They give us the type of shortcodes's we want
 * They extend agsgShortcode
 * @package WordPress
 * @subpackage AGSG
 */
//class agsgATTenclosed extends agsgShortcode
//{
//    public function __construct($stg, $etg, $id, $class, $atts, $defaults, $tag)
//    {
//
//    }
//}

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
        /** sample attributes
         * Classes you deam optional by the user ( any kind of tag )
         * The link, height, and width to a You Tube video
         * You tube video ex <iframe width="ATTRIBUTE VALUE" height="ATTRIBUTE VALUE" src="ATTRIBUTE VALUE" frameborder="0" allowfullscreen></iframe>
         */
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
        } else {
            $msg = new agsgNotices('Sorry the shortcode could not be created.', 'error', '');
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