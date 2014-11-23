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
//ps_img_slider_fixed
/**
 * Shortcode Class ps_img_slider_fixed_agsg
 *  This is a simple image slider shortcode using the free CCS-PLUS Slider available here.<br />
 * -->https://www.google.com/url?sa=t&rct=j&q=&esrc=s&source=web&cd=5&cad=rja&uact=8&ved=0CD8QFjAE&url=https%3A%2F%2Fgithub.com%2FJamyGolden%2FPlusSlider&ei=RgVuVKGELZatyATmt4GgAg&usg=AFQjCNG2niCLOpb3DrIA1yawgBX2csvrcw&sig2=AmAwAvkqtvFeGEtT38lhew&bvm=bv.80120444,d.aWw<br />
 * <br />
 * Description:<br />
 * ps_image_slider_fixed - is a  self enclosing slider shortcode created to display 3 images.  You can set each image\\\'s link source, the link target to \"_blank\" if you need, and specify a title for each.<br />
 * The slider is controlled by some external javascript we are loading.  \"jQuery.plussslider.js\" and \"jquery.easing.1.3.js\".<br />
 * The slider is initialized through embedded javascript loaded in the footer so that we can use it on the fly without having to set up an external script each time. ( A function is generated to ensure this is loaded in the footer )<br />
 * We want to be able to set the animation easing we want in the settings via an attribute reference we define in our embedded javascript.<br />
 * We want to be able to set the slider type we want in the settings via an attribute reference we define in our embedded javascript.
 **/
class ps_img_slider_fixed_agsg
{
    protected static $a;
    protected static $current_use;
    protected static $total_count;

    // call back function for shortcode
    public function ps_img_slider_fixed_agsg_cb($atts, $content = null)
    {
        // get current use count
        static $first_call = true;
        if ($first_call) {
            self::$current_use = 1;
        } else {
            self::$current_use++;
        }
        $first_call = false;
        $current_use = self::$current_use;
        // initialize our attributes
        self::$a = shortcode_atts(array('class_ps_image_slider_fixed' => 'ps-image-slider-fixed', 'img3_h' => '250px', 'img2_h' => '250px', 'img1_h' => '250px', 'img3_w' => '630px', 'img2_w' => '630px', 'img1_w' => '630px', 'type' => 'slider', 'easing' => 'easeOutBounce', 'display_time' => '2000', 'auto_play' => 'true', 'ps_slider_id' => 'ps-slider-1', 'showv' => 'true', 'img3_alt' => 'clouds', 'img2_alt' => 'tree', 'img1_alt' => 'stop-sign', 'img3_title' => 'Clouds', 'img2_title' => 'Tree', 'img1_title' => 'Stop Sign', 'img3_link' => 'http://moezaick.com/agsg-demo/wp-content/plugins/a-gui-shortcode-generator/demo-docs/plusslider-master/images/image3.jpg', 'img3' => 'http://moezaick.com/agsg-demo/wp-content/plugins/a-gui-shortcode-generator/demo-docs/plusslider-master/images/image3.jpg', 'img2_link' => 'http://moezaick.com/agsg-demo/wp-content/plugins/a-gui-shortcode-generator/demo-docs/plusslider-master/images/image2.jpg', 'img2' => 'http://moezaick.com/agsg-demo/wp-content/plugins/a-gui-shortcode-generator/demo-docs/plusslider-master/images/image2.jpg', 'img1_link' => 'http://moezaick.com/agsg-demo/wp-content/plugins/a-gui-shortcode-generator/demo-docs/plusslider-master/images/image4.jpg', 'img1' => 'http://moezaick.com/agsg-demo/wp-content/plugins/a-gui-shortcode-generator/demo-docs/plusslider-master/images/image4.jpg'), $atts);

        $id = '';
        $classes = '';
        $styles = '';
        $html_tag = '';

        // get content and search for substring that matches our tag - get total count of how many time this shortcode has been used
        $post_content = get_the_content();
        self::$total_count = substr_count($post_content, '[' . 'image_slider_fixed');

        if (isset(self::$a['id'])) {
            $id = self::$a['id'];
        }
        if (isset(self::$a['class'])) {
            $classes = self::$a['class'];
        }
        if (isset(self::$a['style'])) {
            $styles = self::$a['style'];
        }
        if (isset(self::$a['html_tag'])) {
            $html_tag = self::$a['html_tag'];
        }
        if ($content) {
            $var = '<selfclosingshortcode' . ' id="' . '" class=" ' . $classes . '" style=" ' . $styles . '"  >' . $content . '</selfclosingshortcode>';
        } else {
            $var = '';
        }
        if (self::$a['showv'] == 'true') {
            $class_ps_image_slider_fixed = self::$a['class_ps_image_slider_fixed'];
            $img3_h = self::$a['img3_h'];
            $img2_h = self::$a['img2_h'];
            $img1_h = self::$a['img1_h'];
            $img3_w = self::$a['img3_w'];
            $img2_w = self::$a['img2_w'];
            $img1_w = self::$a['img1_w'];
            $type = self::$a['type'];
            $easing = self::$a['easing'];
            $display_time = self::$a['display_time'];
            $auto_play = self::$a['auto_play'];
            $ps_slider_id = self::$a['ps_slider_id'];
            $img3_alt = self::$a['img3_alt'];
            $img2_alt = self::$a['img2_alt'];
            $img1_alt = self::$a['img1_alt'];
            $img3_title = self::$a['img3_title'];
            $img2_title = self::$a['img2_title'];
            $img1_title = self::$a['img1_title'];
            $img3_link = self::$a['img3_link'];
            $img3 = self::$a['img3'];
            $img2_link = self::$a['img2_link'];
            $img2 = self::$a['img2'];
            $img1_link = self::$a['img1_link'];
            $img1 = self::$a['img1'];
            $var .= "<div id='$ps_slider_id' class='$class_ps_image_slider_fixed'><a href='$img1_link' target='&lt;&lt;img1_target&gt;&gt;' data-title='$img1_title'> <img src='$img1' border='0' alt='$img1_alt' title='$img1_title' width='$img1_w' height='$img1_h' /> </a> <a href='$img2_link' target='&lt;&lt;img2_target&gt;&gt;' data-title='$img2_title'> <img src='$img2' border='0' alt='$img2_alt' title='$img2_title' width='$img2_w' height='$img2_h' /> </a> <a href='$img3_link' target='&lt;&lt;img3_target&gt;&gt;' data-title='$img3_title'> <img src='$img3' border='0' alt='$img3_alt' title='$img3_title' width='$img3_w' height='$img3_h' /> </a></div>
";
            $cb = function () use ($type, $easing, $display_time, $auto_play, $ps_slider_id) {
                ?>
                <script>// <![CDATA[
                    jQuery(document).ready(function () {
                        jQuery('#<?php echo $ps_slider_id; ?>').plusSlider({
                            autoPlay: <?php echo $auto_play; ?>,
                            sliderEasing: '<?php echo $easing; ?>', // Anything other than 'linear' and 'swing' requires the easing plugin
                            displayTime: <?php echo $display_time; ?>, // The amount of time the slide waits before automatically moving on to the next one. This requires 'autoPlay: true'
                            sliderType: '<?php echo $type; ?>' // Choose whether the carousel is a 'slider' or a 'fader'
                        });
                    });
                    // ]]></script>
            <?php
            }; // end closure function
            add_action('wp_footer', $cb);

        } // end condition

        wp_enqueue_script("plus-image-slider", "http://moezaick.com/agsg-demo/wp-content/plugins/a-gui-shortcode-generator/demo-docs/plusslider-master/js/jquery.plusslider.js", array('jquery-easing'), "1.5.13", true);
        wp_enqueue_script("jquery-easing", "http://moezaick.com/agsg-demo/wp-content/plugins/a-gui-shortcode-generator/demo-docs/plusslider-master/js/jquery.easing.1.3.js", array('jquery'), "1.3", true);
        wp_enqueue_style("plusslider", "http://moezaick.com/agsg-demo/wp-content/plugins/a-gui-shortcode-generator/demo-docs/plusslider-master/css/plusslider.css", array(), "1.0.0", "screen");
        return $var;
    } // end shortcode cb function

} // end class
add_shortcode('ps_img_slider_fixed', array('ps_img_slider_fixed_agsg', 'ps_img_slider_fixed_agsg_cb'));
//ps_img_slider_fixed
