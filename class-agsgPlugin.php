<?php
/*
Plugin Name: A GUI Shortcode Generator Plugin
Plugin URI:
Description: Generates shortcodes from WordPress admin page.  Make custom shortcodes in minutes without any coding knowledge.
Version: 0.0.1
Author: Bobby Ruby
Author URI:
*/
error_reporting(E_ERROR);
// if not accessed by a post from this form
if (!$_POST['form_info'] && !$_POST['type'] && !$_POST['kind'] && !$_POST['shortcode_rewrite']) {
    // make sure it wasn't accessed directly
    if (!defined('ABSPATH')) exit;
    register_activation_hook(__FILE__, array('agsgPlugin', 'install')); // install plugin on activation
    add_action('admin_init', array('agsgPlugin', 'load_plugin')); // run this code once after install
    add_action('plugins_loaded', array('agsgPlugin', 'getInstance'), 10);
} else if ($_POST['shortcode_rewrite']) {
    require_once($_SERVER['DOCUMENT_ROOT'] . 'robertrubyii/wp-load.php'); // Only way I could get it to work. :( - Don't like loading Wordpress at least its not getting loaded twice since we are just posting the data.
    // grab serialized data
    parse_str($_POST['shortcode_rewrite'], $inputs);
    $tag = $inputs['tag'];
    $new_code = $inputs['new_code'];
    $new_code = str_replace("\\'", "'", $new_code);
    agsgPlugin::deleteShortcode($tag);
    agsgPlugin::addShortcodeToFile($new_code);
    exit;
} else if ($_POST['form_info']) {
//    define( 'SHORTINIT', true ); --> tried SHORTINIT but got failure notices
    require_once($_SERVER['DOCUMENT_ROOT'] . 'robertrubyii/wp-load.php'); // Only way I could get it to work. :( - Don't like loading Wordpress at least its not getting loaded twice since we are just posting the data.
    include_once('class-agsgShortcodeGenerator.php');
    include_once('class-agsgShortcode.php');
    include_once('agsg-concrete-creator-classes.php');
    include_once('agsg-concrete-product-classes.php');
    include_once('class-agsgNotices.php');

    // grab serialized data
    parse_str($_POST['form_info'], $inputs);
    parse_str($_POST['matched_attributes'], $matched_atts);

    $args['type'] = $_POST['type'];
    $args['tag'] = $inputs['agsg_shortcode_tag_name'];
    $args['description'] = $inputs['agsg_description'];
    $args['allows_shortcodes'] = $inputs['agsg_process_shortcodes'];
    $args['html_tag'] = $inputs['agsg_html_tag_name'];
    $args['id'] = $inputs['agsg_id'];
    $args['class'] = $inputs['agsg_class'];
    $args['inline_styles'] = $inputs['agsg_inline_styles'];

    $args['html_atts']['names'] = $inputs['agsg_html_tag_att_name'];
    $args['html_atts']['values'] = $inputs['agsg_html_tag_default'];
    $args['atts']['names'] = $inputs['agsg_att_name'];
    $args['atts']['values'] = $inputs['agsg_default'];
    $args['mapped_atts']['match_html_att_names'] = $matched_atts['match_html_tag_att_name'];
    $args['mapped_atts']['match_shortcode_att_names'] = $matched_atts['match_att_name'];

    // grab all conditions on the screen and create an array for each one that contains only the data for it if conditions exist
    if ($inputs['agsg_has_conditions'] === 'Yes') {
        for ($i = 0; $i < count($inputs['agsg_shortcode_condition_type']); $i++) {
            $args['conditions'][$i]['type'] = $inputs['agsg_shortcode_condition_type'][$i];
            $args['conditions'][$i]['attribute'] = $inputs['agsg_shortcode_condition_attribute'][$i];
            $args['conditions'][$i]['operator'] = $inputs['agsg_shortcode_condition_operator'][$i];
            $args['conditions'][$i]['value'] = $inputs['agsg_shortcode_condition_value'][$i];
            $args['conditions'][$i]['tinyMCE'] = $inputs['agsg_shortcode_condition_tinyMCE'][$i];
        }
    }
    // get kind
    $kind = ($inputs['agsg_has_atts'] === 'Yes') ? 'ATT' : 'NonATT';

    if ($kind === 'ATT') {
        $shortcode = new agsgATTgenerator();
        $shortcode->generateShortcode($args);
    } else if ($kind === 'NonATT') {
        $shortcode = new agsgNonATTgenerator();
        $shortcode->generateShortcode($args);
    } else {
    }
    exit;
}
error_reporting(E_ALL);
include_once('agsg_shortcodes.php');
include_once('class-agsgSettings.php');


/**
 * Main Plugin Class
 * Installs, Updates, and calls all other classes for plugin besides Factory
 * @package WordPress
 */
class agsgPlugin
{
    private static $instance;

    private function __construct()
    {
        $settings = new agsgSettings(__FILE__);
        add_action('current_screen', array($this, 'addHelp'));
    }

    public static function addShortcodeToFile($shortcode_code)
    {
        $fileName = plugin_dir_path(__FILE__) . 'agsg_shortcodes.php';
        if ($fh = fopen($fileName, 'a')) { // open file agsg for appending if true so we can append our shortcode
            fwrite($fh, PHP_EOL . $shortcode_code . PHP_EOL);
        }
        fclose($fh);
        echo 'New Shortcode code Added';
    }

    public static function deleteShortcode($tag)
    {
        $fileName = plugin_dir_path(__FILE__) . 'agsg_shortcodes.php';
        $source_file = file_get_contents($fileName);
        $source = preg_replace('/(\/\/' . $tag . ')(.*)(\/\/' . $tag . ')/s', "", $source_file);
        file_put_contents($fileName, $source);
        echo 'Old Shortcode code Deleted <br>';
    }

    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function install()
    {
        add_option('agsgPlugin', 'agsg');
        /* activation code here */
        global $wpdb;
        // task_types Table - Used to hold all task types available in the system that can be assigned to jobs.  GREEN LIGHTED
        $table_name = $wpdb->prefix . "agsg_shortcodes";
        $sql = "CREATE TABLE $table_name (
          id INT NOT NULL AUTO_INCREMENT,
          type VARCHAR(11) NOT NULL,
          name VARCHAR(100) NOT NULL,
          kind VARCHAR(6) NOT NULL,
          tag VARCHAR(100) NOT NULL,
          htmlstg VARCHAR(300) NOT NULL,
          htmletg VARCHAR(20) NOT NULL,
          description VARCHAR(300) NOT NULL,
          example VARCHAR(300) NOT NULL,
          code TEXT NOT NULL,
          created_datetime DATETIME NOT NULL,
          CONSTRAINT $table_name" . "_pk PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function load_plugin()
    {
        if (is_admin() && get_option('agsgPlugin') == 'agsg') {
            delete_option('agsg');
            /* do some stuff once right after activation */
        }
    }

    public static function addHelp()
    {
        $screen = get_current_screen();
        $help_content = '<h3>Create a shortcode that surrounds content with an HTML element. (Enclosing)</h3>';
        $help_content .= '<ol>
        <li>Fill in the Shortcode Tag Name field - This should be on the short as possible, unique, but as descriptive as possible ( No need to have the "[]" as they will be stirpped out and do not worry about the underscores, when you click out of the field it will put them in for you. ).</li>
        <li>Fill in the HTML TAG Name field ( Read notes under fields as this can be overriden )</li>
        <li>Give your HTML element an id if you choose ( Read notes under fields as this can be overriden ).</li>
        <li>Give your HTML element some base classes if you choose.(read notes under fields as you can add to this set later using shortcode attribute "class")</li>
        <li>Give your HTML element some inline styles if you choose. (read notes under fields as you can add to this set later using shortcode attribute "style")</li>
        <li>Give your HTML element some additional attributes that you may need or require. (remember you do not need to create the HTML attributes "class", "style", or "id" - See notes under field.)</li>
        <li>Describe your shortcode, this description is inserted in the code in a comment block before the function, it may be handy when looking to change the specifications of a previously generated shortcode as you have chance to compare the code before actually committing to the code rewrite of the php file. ( I would not recommend those without coding experience to tamper with previously created shortcodes if they have used them already, unless the new one is EXACTLY the same besides for new attributes or conditions. )</li>
        <li>Give your shortcode some attributes if you need them, to map to your HTML attributes, display some conditional content above what is wrapped, or reference their values inside conditional content using the attribute reference syntax.( Make sure you read the notes under each fields that has them ).</li>
        <li>Give your shortcode some conditions if you need them, to check values of attributes and display additonal content above the wrapped element.( Make sure you read the notes under each fields that has them carefully ).</li>
        <li>Press "Generate Shortcode".</li>
        ';
        // Add help panel
        $screen->add_help_tab(array(
            'id' => 'cscs',
            'title' => 'Create a shortcode that surrounds content with an HTML element. (Enclosing)',
            'content' => $help_content,
        ));

        $help_content = '<h3>Create a shortcode that is replaced with content. (Self-Closing)</h3>';
        $help_content .= '<ol>
        <li>Fill in the Shortcode Tag Name field - This should be on the short as possible, unique, but as descriptive as possible ( No need to have the "[]" as they will be stirpped out and do not worry about the underscores, when you click out of the field it will put them in for you. ).</li>
        <li>Fill in the HTML TAG Name field ( This is just required even if it is not going to be used - You can just put "blah" or better yet "self-closed", how about "ziptydoda" )</li>
        <li>Skip down to the describe your shortcode area.</li>
        <li>Describe your shortcode, this description is inserted in the code in a comment block before the function, it may be handy when looking to change the specifications of a previously generated shortcode as you have chance to compare the code before actually committing to the code rewrite of the php file. ( I would not recommend those without coding experience to tamper with previously created shortcodes if they have used them already, unless the new one is EXACTLY the same besides for new attributes or conditions. )</li>
        <li>Give your shortcode some attributes if you need them, to display conditional content and reference their values inside conditional content using the attribute reference syntax.( Make sure you read the notes under each fields that has them ).</li>
        <li>Give your shortcode some conditions if you need them, to check values of attributes and display the content you want to display.( Make sure you read the notes under each fields that has them ).</li>
        <li>Press "Generate Shortcode".</li>
        ';
        // Add help panel
        $screen->add_help_tab(array(
            'id' => 'ces',
            'title' => 'Create a shortcode that is replaced with content. (Self-Closing)',
            'content' => $help_content,
        ));
    }
}

// Other functions for debugging - Uncomment when/if needed
function rfd_debugger($debugItem, $die = 0)
{
    echo '<pre>';
    print_r($debugItem);
    echo '</pre>';
    if ($die == 1) {
        die();
    }
}
//function showHooks()
//{
//    global $current_screen;
//    $screen = $current_screen;
//    global $hook_suffix;
//    $screen_id = $screen->id;
//    // List screen properties
//    $variables = '<ul style="width:50%;float:left;"> <strong>Screen variables </strong>'
//        . sprintf('<li> Screen id : %s</li>', $screen_id)
//        . sprintf('<li> Screen base : %s</li>', $screen->base)
//        . sprintf('<li>Parent base : %s</li>', $screen->parent_base)
//        . sprintf('<li> Parent file : %s</li>', $screen->parent_file)
//        . sprintf('<li> Hook suffix : %s</li>', $hook_suffix)
//        . '</ul>';
//
//    // Append global $hook_suffix to the hook stems
//    $hooks = array(
//        "load-$hook_suffix",
//        "admin_print_styles-$hook_suffix",
//        "admin_print_scripts-$hook_suffix",
//        "admin_head-$hook_suffix",
//        "admin_footer-$hook_suffix"
//    );
//
//    // If add_meta_boxes or add_meta_boxes_{screen_id} is used, list these too
//    if (did_action('add_meta_boxes_' . $screen_id))
//        $hooks[] = 'add_meta_boxes_' . $screen_id;
//
//    if (did_action('add_meta_boxes'))
//        $hooks[] = 'add_meta_boxes';
//
//    // Get List HTML for the hooks
//    $hooks = '<ul style="width:50%;float:left;"> <strong>Hooks </strong> <li>' . implode('</li><li>', $hooks) . '</li></ul>';
//
//    // Combine $variables list with $hooks list.
//    $help_content = $variables . $hooks;
//
//    // Add help panel
//    $screen->add_help_tab(array(
//        'id' => 'wptuts-screen-help',
//        'title' => 'Screen Information',
//        'content' => $help_content,
//    ));
//}