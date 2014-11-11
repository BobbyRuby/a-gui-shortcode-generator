<?php
/**
 * AGSG Generated Shortcodes
 */
function my_shortcode_agsg($atts, $content = null)
{
    $a = shortcode_atts(array('w' => ''), $atts);
    return '<article' . ' id="my-article"' . ' class="articles ' . $a['class'] . '" style=" ' . $a['style'] . '"   width="' . $a['w'] . '">' . $content . '</article>';
}

add_shortcode('my_shortcode', 'my_shortcode_agsg');