<?php
/**
 * Concrete product classes - They give us the type of shortcodes's we want
 * They extend agsgShortcode
 * @package WordPress
 * @subpackage sagsgPluginD
 */
class agsgATTenclosed extends agsgShortcode
{
    public function __construct($htmlContent, $atts, $tag)
    {

    }
}

class agsgATTselfclosed extends agsgShortcode
{
    public function __construct($htmlContent, $atts, $tag)
    {
    }
}

class agsgNonATTenclosed extends agsgShortcode
{
    public function __construct($htmlContent, $atts, $tag)
    {
        $tag_shortcode = $tag . '_shortcode_' . mt_rand(1, 1000000); // unique name for function using tag sent and '_shortcode_' and random number
        $this->shortcode = <<<EOD
function $tag_shortcode
EOD;
        $this->shortcode .= <<<'EOD'
( $atts, $content = null )
EOD;
        $this->shortcode .= <<<EOD
{
    return '$htmlContent';
}
add_shortcode( '$tag', '$tag_shortcode' );
EOD;
    }
}

class agsgNonATTselfclosed extends agsgShortcode
{
    public function __construct($htmlContent, $atts, $tag)
    {

    }
}