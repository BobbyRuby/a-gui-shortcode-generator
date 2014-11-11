<?php
/**
 * AGSG Generated Shortcodes
 */
//shortcode 2
function shortcode_2_agsg($atts, $content = null)
{
    return "<div id='theid' class='class class class class' style='color: #333333;' title='milkshakes' >$content</div>";
}

add_shortcode('shortcode_2', 'shortcode_2_agsg');
//shortcode 2
//shortcode 3
function shortcode_3_agsg($atts, $content = null)
{
    $a = shortcode_atts(array('t' => ''), $atts);

    $var = '<div' . ' id="theid"' . ' class="class class class class ' . $a['class'] . '" style="color: #333333; ' . $a['style'] . '"   title="' . $a['t'] . '">' . do_shortcode($content) . '</div>';
    if ($content) {
        return $var;
    }
}

add_shortcode('shortcode_3', 'shortcode_3_agsg');
//shortcode 3