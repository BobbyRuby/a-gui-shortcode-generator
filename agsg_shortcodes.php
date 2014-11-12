<?php
/**
 * AGSG Generated Shortcodes
 */
//test
function test_agsg($atts, $content = null)
{
    $a = shortcode_atts(array('work' => '', 'c' => ''), $atts);
    $var = '<div' . ' id=""' . ' class=" ' . $a['class'] . '" style=" ' . $a['style'] . '"  >' . $content . '</div>';
    if ($a['work'] == '') {
        $c = $a['c'];
        echo "<p>Enter content you want displayed here. $c</p>";
    }
    if ($content) {
        return $var;
    }
}

add_shortcode('test', 'test_agsg');
//test
