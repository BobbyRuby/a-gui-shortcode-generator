<?php
/*
Plugin Name: A GUI Shortcode Generator Plugin
Plugin URI:
Description: Generates shortcodes from WordPress admin page.  Make custom shortcodes in minutes without any coding knowledge.
Version: 0.0.1
Author: Bobby Ruby
Author URI:
*/

function rfd_debugger($debugItem, $die = 0)
{
    echo '<pre>';
    print_r($debugItem);
    echo '</pre>';
    if ($die == 1) {
        die();
    }
}

// if not accessed by a post from this form
if (!$_POST['form_info'] && !$_POST['type'] && !$_POST['kind']) {
    // make sure it wasn't accessed directly
    if (!defined('ABSPATH')) exit;
    register_activation_hook(__FILE__, array('agsgPlugin', 'install')); // install plugin on activation
    add_action('admin_init', array('agsgPlugin', 'load_plugin')); // run this code once after install
    add_action('plugins_loaded', array('agsgPlugin', 'getInstance'), 10);

} else { // data in $_POST
//    define( 'SHORTINIT', true ); // tried SHORTINIT but got failure notices
    require_once($_SERVER['DOCUMENT_ROOT'] . 'robertrubyii/wp-load.php'); // Only way I could get it to work. :(
    include_once('class-agsgShortcodeGenerator.php');
    include_once('class-agsgShortcode.php');
    include_once('agsg-concrete-creator-classes.php');
    include_once('agsg-concrete-product-classes.php');
    include_once('class-agsgNotices.php');
    /**
     * @param string $kind - used to determine what kind of shortcode were going to created. NonATT or ATT
     * @param string $type - used by createShortcode factory method in concrete creator classes to decide what type of shortcode is going to be created. enclosed or self-closed
     * @param string $htmlTag - used by concrete product classes to wrap content
     * @param array $att - used by concrete product classes for attributes
     * @param string $tag - used by concrete product classes to set the tag for the generated shortcode
     */
    // grab serialized data
    parse_str($_POST['form_info'], $inputs);
    parse_str($_POST['matched_attributes'], $matched_atts);
    rfd_debugger($matched_atts, 1);
    $args['type'] = $_POST['type'];
    $args['tag'] = $inputs['agsg_shortcode_tag_name'];
    $args['description'] = $inputs['agsg_description'];
    $args['allows_shortcodes'] = $inputs['agsg_process_shortcodes'];
    $args['html_tag'] = $inputs['agsg_html_tag_name'];
    $args['id'] = $inputs['agsg_id'];
    $args['class'] = $inputs['agsg_class'];
    $args['inline_styles'] = $inputs['agsg_inline_styles'];

    // grab arrays to call our function
    $args['agsg_html_tag_att_name'] = $inputs['agsg_atts'];
    $args['atts'] = $inputs['agsg_atts'];
    $args['html_tag_defaults'] = $inputs['agsg_defaults'];

    if ($_POST['kind'] === 'ATT') {
//        $shortcode = new agsgATTgenerator();
//        $shortcode->generateShortcode($args);
    } else if ($_POST['kind'] === 'NonATT') {
        $inputs['agsg_atts'] = array();
        $inputs['agsg_defaults'] = array();

        $shortcode = new agsgNonATTgenerator();
        $shortcode->generateShortcode($args);
//            $msg = new agsgNotices('Test.', 'updated');
    } else {
    }
    exit;
}

include_once('agsg_shortcodes.php');
// Added 11/2/14
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
        add_action('current_screen', array(&$this, 'showHooks'), 10); // show hooks
        $settings = new agsgSettings(__FILE__);
    }

    public function showHooks()
    {
        global $current_screen;
        $screen = $current_screen;
        global $hook_suffix;
        $screen_id = $screen->id;
        // List screen properties
        $variables = '<ul style="width:50%;float:left;"> <strong>Screen variables </strong>'
            . sprintf('<li> Screen id : %s</li>', $screen_id)
            . sprintf('<li> Screen base : %s</li>', $screen->base)
            . sprintf('<li>Parent base : %s</li>', $screen->parent_base)
            . sprintf('<li> Parent file : %s</li>', $screen->parent_file)
            . sprintf('<li> Hook suffix : %s</li>', $hook_suffix)
            . '</ul>';

        // Append global $hook_suffix to the hook stems
        $hooks = array(
            "load-$hook_suffix",
            "admin_print_styles-$hook_suffix",
            "admin_print_scripts-$hook_suffix",
            "admin_head-$hook_suffix",
            "admin_footer-$hook_suffix"
        );

        // If add_meta_boxes or add_meta_boxes_{screen_id} is used, list these too
        if (did_action('add_meta_boxes_' . $screen_id))
            $hooks[] = 'add_meta_boxes_' . $screen_id;

        if (did_action('add_meta_boxes'))
            $hooks[] = 'add_meta_boxes';

        // Get List HTML for the hooks
        $hooks = '<ul style="width:50%;float:left;"> <strong>Hooks </strong> <li>' . implode('</li><li>', $hooks) . '</li></ul>';

        // Combine $variables list with $hooks list.
        $help_content = $variables . $hooks;

        // Add help panel
        $screen->add_help_tab(array(
            'id' => 'wptuts-screen-help',
            'title' => 'Screen Information',
            'content' => $help_content,
        ));
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


            // maybe redirect to another page? display custom notices?
        }
    }
}