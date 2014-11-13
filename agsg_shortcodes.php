<?php
/**
 * AGSG Generated Shortcodes
 */
//test_2
/**
 * This is an awesome dynamic code generation tool.
 **/
function test_2_agsg($atts, $content = null)
{
    $a = shortcode_atts(array('condition' => '', 'link_two_text' => '', 'link_two_url' => '', 'link_two_title' => '', 'image_src' => '', 'image_desc' => '', 'link_url' => '', 'link_text' => '', 'link_title' => '', 'data-id' => ''), $atts);
    $var = '<div' . ' id="id"' . ' class="class ' . $a['class'] . '" style=" ' . $a['style'] . '"   data-id="' . $a['data-id'] . '">' . $content . '</div>';
    if ($a['condition'] == 'display') {
        $link_two_text = $a['link_two_text'];
        $link_two_url = $a['link_two_url'];
        $link_two_title = $a['link_two_title'];
        $image_src = $a['image_src'];
        $image_desc = $a['image_desc'];
        $link_url = $a['link_url'];
        $link_text = $a['link_text'];
        $link_title = $a['link_title'];
        $var .= "<p>&nbsp;gdfsgsdfgsd fg <a title='$link_title' href='$link_url' target='_blank'>$link_text</a> dsfasdfasdfsadf d&nbsp;<a title='title' href='$link_url'>this</a> is another <img src='$image_src' alt='$image_desc' /> <a title='$link_two_title' href='$link_two_url'>$link_two_text</a></p>";
    }
    return $var;
}

add_shortcode('test_2', 'test_2_agsg');
//test_2

//test_3
/**
 * This is an awesome dynamic code generation tool.
 **/
function test_3_agsg($atts, $content = null)
{
    $a = shortcode_atts(array('condition' => '', 'link_title' => '', 'link_text' => '', 'link_url' => ''), $atts);
    $var = '<div' . ' id="id"' . ' class="class ' . $a['class'] . '" style=" ' . $a['style'] . '"  >' . do_shortcode($content) . '</div>';
    if ($a['condition'] == 'display') {
        $link_title = $a['link_title'];
        $link_text = $a['link_text'];
        $link_url = $a['link_url'];
        $var .= "<p>Enter content you want displayed <a title='$link_title' href='$link_url' target='_blank'>$link_text</a></p>";
    }
    return $var;
}

add_shortcode('test_3', 'test_3_agsg');
//test_3
