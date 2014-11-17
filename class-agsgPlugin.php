<?php
/**
 * Plugin Name: A GUI Shortcode Generator Plugin
 * Plugin URI:
 * Description: Generates shortcodes from WordPress admin page.  Make REAL custom shortcodes in minutes without any coding knowledge.
 * Version: 1.0.0
 * Author: Robert Ruby II
 * Author URI:
 * Text Domain: Not Yet Implemented as of v1.0.0
 * Domain Path: N/A
 * License: See Envato for details
 */

if ($_POST['form_info']) { //@todo Refactor to WP Ajax in 1.1.0
    $wp_load = agsgPlugin::get_wp_load_path();
    require_once($wp_load);
    include_once('class-agsgShortcodeGenerator.php');
    include_once('class-agsgShortcode.php');
    include_once('agsg-concrete-creator-classes.php');
    include_once('agsg-concrete-product-classes.php');
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

    $args['preview'] = ($inputs['agsg_preview'] === 'Yes') ? true : false;
    $args['regenerate'] = ($inputs['agsg_regenerate'] === 'Yes') ? true : false;
//    agsgPlugin::rfd_debugger($args,1);
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
//} elseif($_POST['shortcode_button_list']){  @todo Set up button when refactoring to WP Ajax - 1.1.0
//    $wp_load = agsgPlugin::get_wp_load_path();
//    require_once($wp_load);
//    // get array of tag names and examples
//    global $wpdb;
//    $table = $wpdb->prefix.'agsg_shortcodes';
//    $rows = $wpdb->get_results("SELECT * FROM $wpdb->prefix" . "agsg_shortcodes", ARRAY_A);
//    echo json_encode($rows);
//    exit;
} else // if not accessed by a post from this form
    if (!$_POST['form_info'] && !$_POST['type'] && !$_POST['kind']) {
        // make sure it wasn't accessed directly
        if (!defined('ABSPATH')) exit;
        register_activation_hook(__FILE__, array('agsgPlugin', 'install')); // install plugin on activation
        add_action('admin_init', array('agsgPlugin', 'load_plugin')); // run this code once after install
        add_action('plugins_loaded', array('agsgPlugin', 'getInstance'), 10);
    }
include_once('agsg_shortcodes.php');
include_once('class-agsgSettings.php');
include_once('agsgListPage.php');


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
        $settings = new agsgSettings(__FILE__); // adds own menu item
        // actions and filters
        add_action('current_screen', array(&$this, 'addHelp'));
        add_action('admin_print_styles', array(&$this, 'iconCss'));
        add_action('admin_head', array(&$this, 'addButton'));
        // in agsgListPage.php
        add_action('admin_menu', 'agsg_shortcode_add_menu_items');
        add_filter('set-screen-option', 'agsg_shortcode_per_page_set_screen_option', 10, 3);
        /**
         * Use this for debugging
         */
//        add_action('current_screen', array($this, 'showHooks'));
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
        add_option('agsgPlugin', 'agsg'); // for one time stuff after activation
        add_option('agsgPluginVersion', '1.0.0'); // for version number and updates
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
          description VARCHAR(600) NOT NULL,
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

    public static function iconCss()
    {
        $assets_url = esc_url(trailingslashit(plugins_url('/assets/', __FILE__)));
        wp_enqueue_style('agsg-icon-css', $assets_url . 'css/icon-css.css');
    }

    public static function addHelp()
    {
        $screen = get_current_screen();
        if ($screen->id === 'toplevel_page_eagsg') {
            $help_content = '<h3>Create a shortcode that surrounds content with an HTML element. (Enclosing)</h3>';
            $help_content .= '<ol>
            <li>Fill in the Shortcode Tag Name field. This should be as short as possible, unique, but as descriptive as possible. ( No need to have the "[]" as they will be stirpped out and do not worry about the underscores, when you click out of the field it will put them in for you. ).</li>
            <li>Fill in the HTML TAG Name field. ( Read notes under fields as this can be overriden )</li>
            <li>Give your HTML element an id if you are making an enclosed shortcode. ( Read notes under fields as this can be overriden ).</li>
            <li>Give your HTML element some base classes if you are making an enclosed shortcode. ( Read notes under fields as you can add to this set later using shortcode attribute "class" )</li>
            <li>Give your HTML element some inline styles if you are making an enclosed shortcode. ( Read notes under fields as you can add to this set later using shortcode attribute "style")</li>
            <li>Give your HTML element some additional attributes that you may need or require. ( Remember you do not need to create the HTML attributes "class", "style", or "id" but any other attribute you wish your element to have should be noted here. )</li>
            <li>Describe your shortcode, this description is inserted in the code in a comment block before the function begins, it may be handy when looking to change the specifications of a previously generated shortcode as you have chance to compare the code before actually committing to the code regeneration rewrite of the php file. ( I would not recommend those without coding experience to tamper with previously created shortcodes if they have used them already, unless the new one only adds NEW attributes or conditions while leaving everything else exactly the same. )</li>
            <li>Give your shortcode some attributes if you need them, to map to your HTML attributes, display some conditional content below what is wrapped, or reference their values inside conditional content using the attribute reference syntax..</li>
            <li>Give your shortcode some conditions if you need them, to check values of attributes and display additonal content above the wrapped element.</li>
            <li>Press "Generate Shortcode".</li>
            </ol>
            ';
            // Add help panel
            $screen->add_help_tab(array(
                'id' => 'cscs',
                'title' => 'Create a shortcode that surrounds content with an HTML element. (Enclosing)',
                'content' => $help_content,
            ));

            $help_content = '<h3>Create a shortcode that is replaced with content. (Self-Closing)</h3>';
            $help_content .= '<ol>
            <li>Fill in the Shortcode Tag Name field. This should be as short as possible, unique, but as descriptive as possible. ( No need to have the "[]" as they will be stirpped out and do not worry about the underscores, when you click out of the field it will put them in for you. ).</li>
            <li>Fill in the HTML TAG Name field ( This is just required even if it is not going to be used - You can just put "blah" or better yet "self-closed", how about "ziptydoda" )</li>
            <li>Skip down to the describe your shortcode area.</li>
            <li>Describe your shortcode, this description is inserted in the code in a comment block before the function, it may be handy when looking to change the specifications of a previously generated shortcode as you have chance to compare the code before actually committing to the code rewrite of the php file. ( I would not recommend those without coding experience to tamper with previously created shortcodes if they have used them already, unless the new one only adds NEW attributes or conditions while leaving everything else exactly the same. )</li>
            <li>Give your shortcode some attributes if you need them, to map to your HTML attributes, display some conditional content below what is wrapped, or reference their values inside conditional content using the attribute reference syntax..</li>
            <li>Give your shortcode some conditions if you need them, to check values of attributes and display additonal content above the wrapped element.</li>
            <li>Press "Generate Shortcode".</li>
            </ol>
            ';
            // Add help panel
            $screen->add_help_tab(array(
                'id' => 'ces',
                'title' => 'Create a shortcode that is replaced with content. (Self-Closing)',
                'content' => $help_content,
            ));

            $help_content = '<h3>Shortcode attribute syntax explained</h3>
            <p>The shortcode attribute syntax is based off of the same idea as the shortcodes themselves but instead of activating what is called a callback function, it just lets you embed variables within the generated shortcode functions.  What that means is when anywhere inside a TinyMCE that an attribute name is encountered in between dual less than and dual greater than signs, it replaces it with the value fed from the shortcode when used.</p>
            <p>Here is an example assuming you have a shortcode attribute named "link_text":
            <code>I just love all the cool design features of the &lt;&lt;link_text&gt;&gt;</code></p>
            <p>Here is an example assuming you have a shortcode attribute named "image":
            <code>This is the best picture I\'ve seen of the mountains &lt;&lt;image&gt;&gt;</code></p>
            <p>In both of these examples the attribute names and the "<" ">" signs will be replaced with what ever value the attributes are equal to when using the shortcode they were created with.</p>
            ';
            // Add help panel
            $screen->add_help_tab(array(
                'id' => 'sase',
                'title' => 'Shortcode attribute syntax explained',
                'content' => $help_content,
            ));

            $help_content = '<h3>Regenerate an existing shortcode</h3>
            <p>Simply ensure the "Regenerate Code" option is set to "Yes".  I only advise those who are comfortable looking at PHP code to perform regenerations as if a shortcode has already been used and you regenerate it, then you change the way it works, your site might break.  The only way to prevent that is to make sure you ONLY extend the functionality of the shortcode and everything else remains the same.  Please be cautious using this.</p>
            ';
            // Add help panel
            $screen->add_help_tab(array(
                'id' => 'pgcbwtf',
                'title' => 'Preview generated code before writing to file',
                'content' => $help_content,
            ));

            $help_content = '<h3>Preview generated code before writing to file</h3>
            <p>Simply ensure the "Preview Generated Code" option is set to "Yes"</p>
            ';
            // Add help panel
            $screen->add_help_tab(array(
                'id' => 'pgcbwtf',
                'title' => 'Preview generated code before writing to file',
                'content' => $help_content,
            ));

            $help_content = '<h3>Compare already existing shortcode function before writing regenerated code to file</h3>
            <p>To compare an already existing shortcodes function to one you might want to replace it with is as simple as trying to generate a shortcode with the same tag without having "Regenerate Code" or "Preview Generated Code" options set to "Yes".  When these options are set to "No" the generator first trys to find a shortcode with that tag.  If it does then it will display the tag, old code, and proposed code so you can compare.  As long as you do not leave the page, you can do this as many times as needed so you can tweak your shortcode many times to get them working how you like.</p>
            ';
            // Add help panel
            $screen->add_help_tab(array(
                'id' => 'caesfbwrctf',
                'title' => 'Compare already existing shortcode function before writing regenerated code to file',
                'content' => $help_content,
            ));
        }
    }

    /**
     * Used for debugging - Adds help tab to show hooks for page
     */
    public static function showHooks()
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

    /**
     * Another function for debugging
     * @param $debugItem
     * @param int $die
     */
    public static function rfd_debugger($debugItem, $die = 0)
    {
        echo '<pre>';
        print_r($debugItem);
        echo '</pre>';
        if ($die == 1) {
            die();
        }
    }

    /**
     * http://stackoverflow.com/questions/2354633/wordpress-root-directory-path
     * @return bool|mixed|string
     */
    public static function get_wp_load_path()
    {
        $base = dirname(__FILE__);
        $path = false;

        if (@file_exists(dirname(dirname($base)) . "/wp-load.php")) {
            $path = dirname(dirname($base)) . "/wp-load.php";
        } else
            if (@file_exists(dirname(dirname(dirname($base))) . "/wp-load.php")) {
                $path = dirname(dirname(dirname($base))) . "/wp-load.php";
            } else
                $path = false;

        if ($path != false) {
            $path = str_replace("\\", "/", $path);
        }
        return $path;
    }

    public function addButton()
    {
        global $typenow;
        // check user permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }
        // check if WYSIWYG is enabled
        if (get_user_option('rich_editing') == 'true') {
            add_filter("mce_external_plugins", array(&$this, 'addwptinyButton'));
            add_filter('mce_buttons', array(&$this, 'registerwptinyButton'));
        }
    }

//    public function addwptinyButton($plugin_array){ @todo Set up button when refactoring to WP Ajax - 1.1.0
//        $plugin_array['agsg_shortcode_button'] = plugins_url( '/assets/js/button.js', __FILE__ ); // CHANGE THE BUTTON SCRIPT HERE
//   	    return $plugin_array;
//    }
//
//    public function registerwptinyButton($buttons){
//       array_push($buttons, "agsg_shortcode_button");
//       return $buttons;
//    }
}