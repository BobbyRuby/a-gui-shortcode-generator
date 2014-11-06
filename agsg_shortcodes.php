<?php
/**
 * AGSG Generated Shortcodes
 */
function framed_thumb_agsgShortcode_52($atts, $content = null)
{
    $a = shortcode_atts(array('class' => ''), $atts);
    return '<div' . ' id="img-thumb-frame" class="img-thumb-frame-base-class ' . $a['class'] . '" style=" ' . $a['inline_style'] . '"  height="100px" data="imagedata" width="100px" >' . $content . '</div>';
}

add_shortcode('framed_thumb', 'framed_thumb_agsgShortcode_52');
