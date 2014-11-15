<?php
/**
 * AGSG Generated Shortcodes
 */
//preview_test
/**
 * gdsfgdsf
 **/
function preview_test_agsg($atts, $content = null)
{
    return "<div id='' class='' style='' >$content</div>";
}

add_shortcode('preview_test', 'preview_test_agsg');
//preview_test

//test_1
/**
 *
 **/
function test_1_agsg($atts, $content = null)
{
    return "<div id='' class='' style='' >$content</div>";
}

add_shortcode('test_1', 'test_1_agsg');
//test_1
//test_2
/**
 *
 **/
function test_2_agsg($atts, $content = null)
{
    $a = shortcode_atts(array('data_user_id' => '', 'display' => '', 'data_user_name' => ''), $atts);
    $var = '<div' . ' id="id"' . ' class="class ' . isset($a['class']) . '" style=" ' . isset($a['style']) . '"   data-user_id="' . isset($a['data_user_id']) . '" data-user_name="' . isset($a['data_user_name']) . '">' . $content . '</div>';
    if ($a['display'] == '') {
        $data_user_id = $a['data_user_id'];
        $data_user_name = $a['data_user_name'];
        $var .= "<table style='height: 39px;' width='267'>
<tbody>
<tr>
<td>User ID</td>
<td>User Name</td>
</tr>
<tr>
<td>$data_user_id</td>
<td>$data_user_name</td>
</tr>
</tbody>
</table>
<p>&nbsp;</p>";
    }
    return $var;
}

add_shortcode('test_2', 'test_2_agsg');
//test_2
