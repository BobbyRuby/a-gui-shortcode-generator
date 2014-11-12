<?php
/**
 * AGSG Generated Shortcodes
 */

//test
/**
 *
 */
function test_agsg($atts, $content = null)
{
    $a = shortcode_atts(array('condition' => '', 'link_url' => '', 'link_text' => '', 'link' => '', 'data-id' => ''), $atts);
    $var = '<div' . ' id="id"' . ' class="class ' . $a['class'] . '" style=" ' . $a['style'] . '"   data-id="' . $a['data-id'] . '">' . $content . '</div>';
    if ($a['condition'] == display) {
        $link_text = target = "_blank" > $a['link_text'];
        $link = href = "$a['link']";
        echo "<p><a title=" & lt;&lt;link_title & gt;&gt;" href="$link" target="_blank">$link_text</a></p>";
    }
    if ($content) {
        return $var;
    }
}

add_shortcode('test', 'test_agsg');
//test
