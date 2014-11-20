<?php
/**
 * AGSG Generated Shortcodes
 */
//article_video_table
/**
 * In this video I show how to create a shortcode that is an enclosing shortcode and displays how you can use several features of this plugin to create custom shortcodes with custom functionality<br />
 * by building a shortcode that is for a client that owns a movie review site.<br />
 * <br />
 * Shortcode Specifications Received from Client:<br />
 * - The shortcode needs to output  wrap an article about the movie being reviewed.<br />
 * - It should have an option of displaying a striped table with cells that contain data to help support the article above.<br />
 * - The statistics can vary.<br />
 * - It should have an option that allows a video to be embedded from either YouTube or Vimeo or both.<br />
 * <br />
 * What this means to us:<br />
 * - We need an enclosing type shortcode that wraps the content in an article element.<br />
 * - We need a couple of attributes that our client can use to control what is displayed. v (Vimeo) / yt (YouTube) /  dt (data table).<br />
 * - We need a couple of attributes that  our client can use for the src for each video frame v_src (Vimeo) / yt_src (YouTube)<br />
 * - We need a set of arbitrary attributes that are for populating the table.<br />
 * - The statistics can vary, so the JS we input needs to analyze the data output and strip out any DOM elements that are blank.  ( This isn\\\'t the best solution. So I want to point out the class you need to override to do begin extending the functionality of this plugin for your own needs, without having to worry about losing them in updates.  You would extend the agsgATT concrete product class and override the construct. ->  Copy / Paste / Rename class then customize. )
 **/
function article_video_table_agsg($atts, $content = null)
{
    $a = shortcode_atts(array('dt_class' => '', 'dt_caption' => '', 'td_10_r3' => '', 'td_10_r2' => '', 'td_10_r1' => '', 'td_10_r4' => '', 'td_9_r4' => '', 'td_8_r4' => '', 'td_7_r4' => '', 'td_6_r4' => '', 'td_5_r4' => '', 'td_4_r4' => '', 'td_3_r4' => '', 'td_2_r4' => '', 'td_1_r4' => '', 'td_9_r3' => '', 'td_8_r3' => '', 'td_7_r3' => '', 'td_6_r3' => '', 'td_5_r3' => '', 'td_4_r3' => '', 'td_3_r3' => '', 'td_2_r3' => '', 'td_1_r3' => '', 'td_9_r2' => '', 'td_8_r2' => '', 'td_7_r2' => '', 'td_6_r2' => '', 'td_5_r2' => '', 'td_4_r2' => '', 'td_3_r2' => '', 'td_2_r2' => '', 'td_1_r2' => '', 'td_9_r1' => '', 'td_8_r1' => '', 'td_7_r1' => '', 'td_6_r1' => '', 'td_5_r1' => '', 'td_4_r1' => '', 'td_3_r1' => '', 'td_2_r1' => '', 'td_1_r1' => '', 'th10' => '', 'th9' => '', 'th8' => '', 'th7' => '', 'th6' => '', 'th5' => '', 'th4' => '', 'th3' => '', 'th2' => '', 'th1' => '', 'v_src' => '//player.vimeo.com/video/105475850', 'yt_src' => '//www.youtube.com/embed/t3vksVYHjJU?showinfo=0', 'showdt' => '', 'showyt' => '', 'showv' => '', 'class' => '', 'id' => ''), $atts);
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
    if ($content) {
        $var = '<article' . ' id="' . $id . '" class="vidarticle ' . $classes . '" style=" ' . $styles . '"  >' . do_shortcode($content) . '</article>';
    } else {
        $var = '';
    }
    if ($a['showv'] == 'yes') {
        $v_src = $a['v_src'];
        $var .= "<p><iframe src='$v_src' width='500' height='281' frameborder='0'></iframe></p>";
    }
    if ($a['showyt'] == 'yes') {
        $v_src = $a['v_src'];
        $yt_src = $a['yt_src'];
        $var .= "<p><iframe src='$yt_src' width='560' height='315' frameborder='0'></iframe></p>";
    }
    if ($a['showdt'] == 'yes') {
        $v_src = $a['v_src'];
        $yt_src = $a['yt_src'];
        $dt_class = $a['dt_class'];
        $dt_caption = $a['dt_caption'];
        $td_10_r3 = $a['td_10_r3'];
        $td_10_r2 = $a['td_10_r2'];
        $td_10_r1 = $a['td_10_r1'];
        $td_10_r4 = $a['td_10_r4'];
        $td_9_r4 = $a['td_9_r4'];
        $td_8_r4 = $a['td_8_r4'];
        $td_7_r4 = $a['td_7_r4'];
        $td_6_r4 = $a['td_6_r4'];
        $td_5_r4 = $a['td_5_r4'];
        $td_4_r4 = $a['td_4_r4'];
        $td_3_r4 = $a['td_3_r4'];
        $td_2_r4 = $a['td_2_r4'];
        $td_1_r4 = $a['td_1_r4'];
        $td_9_r3 = $a['td_9_r3'];
        $td_8_r3 = $a['td_8_r3'];
        $td_7_r3 = $a['td_7_r3'];
        $td_6_r3 = $a['td_6_r3'];
        $td_5_r3 = $a['td_5_r3'];
        $td_4_r3 = $a['td_4_r3'];
        $td_3_r3 = $a['td_3_r3'];
        $td_2_r3 = $a['td_2_r3'];
        $td_1_r3 = $a['td_1_r3'];
        $td_9_r2 = $a['td_9_r2'];
        $td_8_r2 = $a['td_8_r2'];
        $td_7_r2 = $a['td_7_r2'];
        $td_6_r2 = $a['td_6_r2'];
        $td_5_r2 = $a['td_5_r2'];
        $td_4_r2 = $a['td_4_r2'];
        $td_3_r2 = $a['td_3_r2'];
        $td_2_r2 = $a['td_2_r2'];
        $td_1_r2 = $a['td_1_r2'];
        $td_9_r1 = $a['td_9_r1'];
        $td_8_r1 = $a['td_8_r1'];
        $td_7_r1 = $a['td_7_r1'];
        $td_6_r1 = $a['td_6_r1'];
        $td_5_r1 = $a['td_5_r1'];
        $td_4_r1 = $a['td_4_r1'];
        $td_3_r1 = $a['td_3_r1'];
        $td_2_r1 = $a['td_2_r1'];
        $td_1_r1 = $a['td_1_r1'];
        $th10 = $a['th10'];
        $th9 = $a['th9'];
        $th8 = $a['th8'];
        $th7 = $a['th7'];
        $th6 = $a['th6'];
        $th5 = $a['th5'];
        $th4 = $a['th4'];
        $th3 = $a['th3'];
        $th2 = $a['th2'];
        $th1 = $a['th1'];
        $var .= "<table class='$dt_class' style='margin-left: auto; margin-right: auto;'><caption>$dt_caption</caption>
<tbody>
<tr><th>$th1</th><th>$th2</th><th>$th3</th><th>$th4</th><th>$th5</th><th>$th6</th><th>$th7</th><th>$th8</th><th>$th9</th><th>$th10</th></tr>
<tr>
<td>$td_1_r1</td>
<td>$td_2_r1</td>
<td>$td_3_r1</td>
<td>$td_4_r1</td>
<td>$td_5_r1</td>
<td>$td_6_r1</td>
<td>$td_7_r1</td>
<td>$td_8_r1</td>
<td>$td_9_r1</td>
<td>$td_10_r1</td>
</tr>
<tr>
<td>$td_1_r2</td>
<td>$td_2_r2</td>
<td>$td_3_r2</td>
<td>$td_4_r2</td>
<td>$td_5_r2</td>
<td>$td_6_r2</td>
<td>$td_7_r2</td>
<td>$td_8_r2</td>
<td>$td_9_r2</td>
<td>$td_10_r2</td>
</tr>
<tr>
<td>$td_1_r3</td>
<td>$td_2_r3</td>
<td>$td_3_r3</td>
<td>$td_4_r3</td>
<td>$td_5_r3</td>
<td>$td_6_r3</td>
<td>$td_7_r3</td>
<td>$td_8_r3</td>
<td>$td_9_r3</td>
<td>$td_10_r3</td>
</tr>
<tr>
<td>$td_1_r4</td>
<td>$td_2_r4</td>
<td>$td_3_r4</td>
<td>$td_4_r4</td>
<td>$td_5_r4</td>
<td>$td_6_r4</td>
<td>$td_7_r4</td>
<td>$td_8_r4</td>
<td>$td_9_r4</td>
<td>$td_10_r4</td>
</tr>
</tbody>
</table>
<p> </p>";
    }
    wp_enqueue_script("article-video-table-manager", "http://moezaick.com/agsg-demo/stripey.js", array('jquery'), "1.0", true);
    wp_enqueue_style("article-video-table-styler", "http://moezaick.com/agsg-demo/stripey.css", array(), "1.0", "screen");
    return $var;
}

add_shortcode('article_video_table', 'article_video_table_agsg');
//article_video_table