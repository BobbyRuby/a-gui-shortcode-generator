<?php
/**
 * Based off of http://www.hughlashbrooke.com/2014/02/complete-versatile-options-page-class-wordpress-plugin/
 * Some simple changes to fit this implementation.
 */
if (!defined('ABSPATH')) exit;

class agsgSettings
{
    private $dir;
    private $file;
    private $assets_dir;
    private $assets_url;
    private $settings_base;
    private $settings;
    private $pageHooks;

    public function __construct($file)
    {
        $this->file = $file;
        $this->dir = dirname($this->file);
        $this->assets_dir = trailingslashit($this->dir) . 'assets';
        $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));
        $this->settings_base = 'agsg_';

        // Initialise settings
        add_action('admin_init', array($this, 'init'));

        // Register plugin settings
        add_action('admin_init', array($this, 'register_settings'));

        // Add settings page to menu
        add_action('admin_menu', array($this, 'add_menu_item'));

        // Add settings link to plugins page
        add_filter('plugin_action_links_' . plugin_basename($this->file), array($this, 'add_settings_link'));
    }

    /**
     * Initialise settings
     * @return void
     */
    public function init()
    {
        $this->settings = $this->settings_fields();
    }

    /**
     * Add settings page to admin menu
     * @return void
     */
    public function add_menu_item()
    {
        (!$this->settings) ? $settings = $this->settings_fields() : $settings = $this->settings;
        foreach ($settings as $page) {
            $pageHook = add_menu_page(__($page['title'], 'plugin_textdomain'), __($page['title'], 'plugin_textdomain'), 'manage_options', $page['slug'], array($this, $page['slug'] . '_settings_page'));
            add_action('admin_print_styles-' . $pageHook, array($this, 'settings_CSS_assets'));
            add_action('admin_print_scripts-' . $pageHook, array($this, 'settings_JS_assets'));
            $this->pageHooks[] = $pageHook;
        }
    }

    /**
     * Load settings CSS
     * @return void
     */
    public function settings_CSS_assets()
    {
        wp_enqueue_style('agsg-admin-css', $this->assets_url . 'css/settings.css');
    }

    /**
     * Load settings JS
     * @return void
     */
    public function settings_JS_assets()
    {
        // We're including the farbtastic script & styles here because they're needed for the colour picker
        // If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
//        wp_enqueue_style('farbtastic');
//        wp_enqueue_script('farbtastic');

        // We're including the WP media scripts here because they're needed for the image upload field
        // If you're not including an image upload then you can leave this function call out
        wp_enqueue_media();

        wp_register_script('agsg-admin-js', $this->assets_url . 'js/settings.js', array('farbtastic', 'jquery'), '1.0.0');
        wp_enqueue_script('agsg-admin-js');
    }

    /**
     * Add settings link to plugin list table
     * @param  array $links Existing links
     * @return array        Modified links
     */
    public function add_settings_link($links)
    {
        (!$this->settings) ? $settings = $this->settings_fields() : $settings = $this->settings;
        foreach ($settings as $page) {
            $settings_link = '<a href="options-general.php?page=' . $page['slug'] . '">' . __($page['title'], 'plugin_textdomain') . '</a>';
            array_push($links, $settings_link);
        }
        return $links;
    }

    /**
     * Build settings fields
     * @return array Fields to be displayed on settings page arranged by page[section][fields]
     */
    private function settings_fields()
    {
        // Begin Page
        $pageSlug = 'eagsg';
        $settings[$pageSlug] = array(
            'title' => __('A GUI Shortcode Generator', 'plugin_textdomain'),
            'slug' => $pageSlug,
            'sections' => array(
                'html-enclose' => array(
                    'title' => __('', 'plugin_textdomain'),
                    'description' => __('', 'plugin_textdomain'),
                    'page_slug' => $pageSlug,
                    'fields' => array(
                        array(
                            'id' => 'shortcode_tag_name',
                            'label' => __('Shortcode Tag Name', 'plugin_textdomain'),
                            'description' => __('The shortcode tag name used to invoke the shortcode function without the "[" or "]".<br/>
                            Example Input:  my_shortcode<br/>
                             <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> No need to worry about the underscores. All spaces are automatically replaced with them.', 'plugin_textdomain'),
                            'type' => 'text',
                            'default' => '',
                            'placeholder' => __('Ex: my_shortcode', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'html_tag_name',
                            'label' => __('HTML TAG Name', 'plugin_textdomain'),
                            'description' => __('The HTML tag name that content will be placed in between without the "<" and ">" symbols.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> This can be overriden by creating a shotcode attribute below named "html_tag" to allow for more flexibility.  If you override this value by creating the "html_tag" attribute, make sure that you give it a default value as the generator will not use the value entered here.<br/>
                            <span class="error dashicons dashicons-welcome-write-blog"></span><span class="important error">Very Important Note:</span> This is required even if not being creating an enclosing shortcode.  If creating a shortcode that will self-close i.e -> [short_code condition="display 2" /], then just put anything as it will be discarded.', 'plugin_textdomain'),
                            'type' => 'text',
                            'default' => '',
                            'placeholder' => __('Ex: div', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'id',
                            'label' => __('ID', 'plugin_textdomain'),
                            'description' => __('The HTML tag\'s id for hooking to with CSS or JS.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> This can be overriden by creating a shotcode attribute below named "id".  If you override this value by creating the "id" attribute, make sure that you give it a default value as the generator will not use the value entered here.', 'plugin_textdomain'),
                            'type' => 'text',
                            'default' => '',
                            'placeholder' => __('Ex: my-shortcode-content', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'class',
                            'label' => __('Base Classes', 'plugin_textdomain'),
                            'description' => __('The HTML tag\'s base class for hooking to with CSS or JS.<br/>
                             <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Unlike the "html_tag" override, if you create a shortcode attribute named "class" then the values entered for the shortcode attribute when used will add to the HTML "class" attribute.', 'plugin_textdomain'),
                            'type' => 'text',
                            'default' => '',
                            'placeholder' => __('Ex: some-shortcode-content', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'inline_styles',
                            'label' => __('Inline Styles', 'plugin_textdomain'),
                            'description' => __('You can put any valid styles you would normally put in a \'style=""\' attribute for an HTML tag.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Unlike the "html_tag" override, if you create a shortcode attribute named "style" then the values entered for the shortcode attribute when used will add to the HTML "style" attribute.', 'plugin_textdomain'),
                            'type' => 'text',
                            'default' => '',
                            'placeholder' => __('Ex: font-size: 30px; color: #eaeaea; background: #00000;', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'has_html_tag_atts',
                            'label' => __('HTML Tag Attributes
                                            <div id="add_html_tag_att"><span class="add_att_description">Add HTML TAG Attribute</span><span class="dashicons dashicons-plus-alt"></span></div>', 'plugin_textdomain'),
                            'description' => __('You can give your HTML tag attributes.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> HTML attribute values can be set here or you can map them to shortcode attributes so that they can be set at the time of use.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> No need for an HTML "id", "class", or "style" attribute here, the HTML attributes are automatically mapped to shortcode attributes "id", "class", and "style" respectivley if they exist in <a id="has_atts_Yes-link" href="#has_atts_Yes">shortcode attributes area</a>.<br/>
                            <span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span> You must create the shortcode attributes "id", "class", "style", or "html_tag" in the <a id="has_atts_Yes-link" href="#has_atts_Yes">shortcode attributes area</a> to use them in the shortcode.', 'plugin_textdomain'),
                            'type' => 'radio',
                            'options' => array('Yes' => __('Yes', 'plugin_textdomain'), 'No' => __('No', 'plugin_textdomain')),
                            'default' => __('No', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'process_shortcodes',
                            'label' => __('Process Shortcodes Recursively.', 'plugin_textdomain'),
                            'description' => __('If this shortcode might wrap another shortcode, this should be "Yes".</br>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Useful if you want to create certain HTML tags that are meant to contain other tags.  Could be used to quickly create a series of custom layout shortcodes by wrapping some other enclosing shortcodes in a parent container.', 'plugin_textdomain'),
                            'type' => 'radio',
                            'options' => array('Yes' => 'Yes', 'No' => 'No'),
                            'default' => 'No'
                        ),
                        array(
                            'id' => 'description',
                            'label' => __('Describe the shortcode.', 'plugin_textdomain'),
                            'description' => __('If you want, you can give your shortcode a description.', 'plugin_textdomain'),
                            'type' => 'textarea',
                            'default' => '',
                            'placeholder' => __('Describe the shortcode here.', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'has_scripts',
                            'label' => __('Shortcode External JS
                                            <div id="add_shortcode_script"><span class="add_att_description">Add External JS</span><span class="dashicons dashicons-plus-alt"></span></div>', 'plugin_textdomain'),
                            'description' => __('You can give your shortcode JS here.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Your scripts are only loaded when the shortcode is use.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Your scripts are only loaded once no matter how many times you use the shortcode on the same page.', 'plugin_textdomain'),
                            'type' => 'radio',
                            'options' => array('Yes' => __('Yes', 'plugin_textdomain'), 'No' => __('No', 'plugin_textdomain')),
                            'default' => __('No', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'has_styles',
                            'label' => __('Shortcode External CSS
                                            <div id="add_shortcode_style"><span class="add_att_description">Add External CSS</span><span class="dashicons dashicons-plus-alt"></span></div>', 'plugin_textdomain'),
                            'description' => __('You can give your shortcode CSS here.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Your styles are only loaded when the shortcode is use.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> Your styles are only loaded once no matter how many times you use the shortcode on the same page.', 'plugin_textdomain'),
                            'type' => 'radio',
                            'options' => array('Yes' => __('Yes', 'plugin_textdomain'), 'No' => __('No', 'plugin_textdomain')),
                            'default' => __('No', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'has_atts',
                            'label' => __('Shortcode Attributes
                                            <div id="add_shortcode_att"><span class="add_att_description">Add Shortcode Attribute</span><span class="dashicons dashicons-plus-alt"></span></div>', 'plugin_textdomain'),
                            'description' => __('You can give your shortcode attributes that can be used to set attributes in the HTML.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> The "id" shortcode attribute will override the "ID" field above so you can change it up at the time of use, but do note that is will need a default value set, as when the "id" shortcode attribute exists the data in the "ID" field is ignored.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> The "html_tag" shortcode attribute will override the "HTML TAG Name" field above so you can change it up at the time of use, but do note this will need a default value set, as when the "html_tag" shortcode attribute exists the data in the "HTML TAG" Name field is ignored.<br/>
                            <span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span> You must create the shortcode attributes "id", "class", "style", or "html_tag" here to use them in the shortcode.', 'plugin_textdomain'),
                            'type' => 'radio',
                            'options' => array('Yes' => __('Yes', 'plugin_textdomain'), 'No' => __('No', 'plugin_textdomain')),
                            'default' => __('No', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'has_conditions',
                            'label' => __('Shortcode Conditions
                                            <div id="add_shortcode_condition"><span class="add_att_description">Add Shortcode Condition</span><span class="dashicons dashicons-plus-alt"></span></div>', 'plugin_textdomain'),
                            'description' => __('You can give your shortcode if statements that perform actions depending on attribute values.<br/>
                            <span class="dashicons dashicons-welcome-write-blog"></span><span class="important">Important Note:</span> You create the "Actions" using a TinyMCE module.<br/>
                            <span class="dashicons dashicons-welcome-write-blog error"></span><span class="important error">Very Important Note:</span> You can reference the attributes for this shortcode in the TinyMCE editor like this "&lt;&lt;i_am_a_shortcode_att&gt;&gt;"', 'plugin_textdomain'),
                            'type' => 'radio',
                            'options' => array('Yes' => __('Yes', 'plugin_textdomain'), 'No' => __('No', 'plugin_textdomain')),
                            'default' => __('No', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'preview',
                            'label' => __('Preview Generated Code', 'plugin_textdomain'),
                            'description' => __('"Yes" should be selected if you only want to generate a preview of the code generated by this setup.', 'plugin_textdomain'),
                            'type' => 'radio',
                            'options' => array('Yes' => __('Yes', 'plugin_textdomain'), 'No' => __('No', 'plugin_textdomain')),
                            'default' => __('No', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'regenerate',
                            'label' => __('Regenerate Code', 'plugin_textdomain'),
                            'description' => __('"Yes" should be selected if you are looking to regenerate a shortcode that you already generated before.  Don\'t worry though, if you forget nothing bad is going to happen, you will just get an error message saying it exists..', 'plugin_textdomain'),
                            'type' => 'radio',
                            'options' => array('Yes' => __('Yes', 'plugin_textdomain'), 'No' => __('No', 'plugin_textdomain')),
                            'default' => __('No', 'plugin_textdomain')
                        ),
                        array(
                            'id' => 'install_url',
                            'type' => 'hidden',
                            'default' => plugin_dir_url($this->file)
                        )
                    ) // end fields
                ) // end section
            )
        ); // end page

        return $settings;
    }

    /**
     * Register plugin settings
     * @return void
     */
    public function register_settings()
    {
        if (is_array($this->settings)) {
            foreach ($this->settings as $page) {
                foreach ($page['sections'] as $section => $data) {
                    // Add section to page
                    add_settings_section($section, $data['title'], array($this, 'settings_section'), $page['slug']);

                    foreach ($data['fields'] as $field) {

                        if ($field['type'] === 'hidden') {
                        } else {
                            // Validation callback for field
                            $validation = '';
                            if (isset($field['callback'])) {
                                $validation = $field['callback'];
                            }

                            // Register field
                            $option_name = $this->settings_base . $field['id'];
                            register_setting($page['slug'], $option_name, $validation);

                            // Add field to page
                            add_settings_field($field['id'], $field['label'], array($this, 'display_field'), $page['slug'], $section, array('field' => $field));
                        }
                    }
                }
            }
        }
    }

    public function settings_section($section)
    {
        foreach ($this->settings as $page) {
            foreach ($page as $key => $data) {
                if ($key === 'sections' && is_array($data)) {
                    $html = '<p> ' . $data[$section['id']]['description'] . '</p>' . "\n";
                }
            }
        }
        echo $html;
    }

    /**
     * Changes only need to be made if creating custom submit buttons for a page. (Like to hook into with id or class)
     * @return string - the submit button
     */
    private function get_submit_button()
    {
        $submit_button = '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr(__('Save Settings', 'plugin_textdomain')) . '" />' . "\n";
        foreach ($this->settings as $page) {
            foreach ($page as $key => $data) {
                if ($key === 'slug') {
                    $submit_button = '<input name="submit_' . $data . '" type="submit" class="button-primary" value="' . esc_attr(__('Generate Shorcode', 'plugin_textdomain')) . '" />' . "\n";
                }
            }
        }
        return $submit_button;
    }

    private function display_hidden_fields()
    {
        foreach ($this->settings as $page) {
            foreach ($page['sections'] as $section => $data) {
                foreach ($data['fields'] as $field) {
                    if ($field['type'] === 'hidden') {
                        $option_name = $this->settings_base . $field['id'];
                        $html = '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" value="' . $field['default'] . '"/>' . "\n";
                    }
                }
            }
        }
        return $html;
    }

    /**
     * Generate HTML for displaying fields
     * @param  array $args Field data
     * @return void
     */
    public function display_field($args)
    {

        $field = $args['field'];

        $html = '';

        $option_name = $this->settings_base . $field['id'];
        $option = get_option($option_name);

        $data = '';
        if (isset($field['default'])) {
            $data = $field['default'];
            if ($option) {
                $data = $option;
            }
        }

        switch ($field['type']) {

            case 'text':
            case 'password':
            case 'number':
                if (isset($field['grab_array'])) {
                    $html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '[]" placeholder="' . esc_attr($field['placeholder']) . '" value="' . $data . '"/>' . "\n";
                } else {
                    $html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . $data . '"/>' . "\n";
                }
                break;

            case 'text_secret':
                $html .= '<input id="' . esc_attr($field['id']) . '" type="text" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value=""/>' . "\n";
                break;

            case 'textarea':
                $html .= '<textarea id="' . esc_attr($field['id']) . '" rows="5" cols="50" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '">' . $data . '</textarea><br/>' . "\n";
                break;

            case 'checkbox':
                $checked = '';
                if ($option && 'on' == $option) {
                    $checked = 'checked="checked"';
                }
                $html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" ' . $checked . '/>' . "\n";
                break;

            case 'checkbox_multi':
                foreach ($field['options'] as $k => $v) {
                    $checked = false;
                    if (in_array($k, $data)) {
                        $checked = true;
                    }
                    $html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="checkbox" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '[]" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label> ';
                }
                break;

            case 'radio':
                foreach ($field['options'] as $k => $v) {
                    $checked = false;
                    if ($k == $data) {
                        $checked = true;
                    }
                    $html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="radio" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label> ';
                }
                break;

            case 'select':
                $html .= '<select name="' . esc_attr($option_name) . '" id="' . esc_attr($field['id']) . '">';
                foreach ($field['options'] as $k => $v) {
                    $selected = false;
                    if ($k == $data) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
                }
                $html .= '</select> ';
                break;

            case 'select_multi':
                $html .= '<select name="' . esc_attr($option_name) . '[]" id="' . esc_attr($field['id']) . '" multiple="multiple">';
                foreach ($field['options'] as $k => $v) {
                    $selected = false;
                    if (in_array($k, $data)) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '" />' . $v . '</label> ';
                }
                $html .= '</select> ';
                break;

            case 'image':
                $image_thumb = '';
                if ($data) {
                    $image_thumb = wp_get_attachment_thumb_url($data);
                }
                $html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
                $html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __('Upload an image', 'plugin_textdomain') . '" data-uploader_button_text="' . __('Use image', 'plugin_textdomain') . '" class="image_upload_button button" value="' . __('Upload new image', 'plugin_textdomain') . '" />' . "\n";
                $html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="' . __('Remove image', 'plugin_textdomain') . '" />' . "\n";
                $html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
                break;

            case 'color':
                ?>
                <div class="color-picker" style="position:relative;">
                    <input type="text" name="<?php esc_attr_e($option_name); ?>" class="color"
                           value="<?php esc_attr_e($data); ?>"/>

                    <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;"
                         class="colorpicker"></div>
                </div>
                <?php
                break;

        }

        switch ($field['type']) {

            case 'checkbox_multi':
            case 'radio':
            case 'select_multi':
                $html .= '<br/><span class="description">' . $field['description'] . '</span>';
                break;

            default:
                $html .= '<label for="' . esc_attr($field['id']) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
                break;
        }

        echo $html;
    }

    /**
     * Validate individual settings field
     * @param  string $data Inputted value
     * @return string       Validated value
     */
    public function validate_field($data)
    {
        if ($data && strlen($data) > 0 && $data != '') {
            $data = urlencode(strtolower(str_replace(' ', '-', $data)));
        }
        return $data;
    }

    /**
     * Load settings page content
     * @return void
     */
    public function eagsg_settings_page()
    {
        (!$this->settings) ? $settings = $this->settings_fields() : $settings = $this->settings;
        foreach ($settings as $page) {
            if ($page['slug'] == 'eagsg') {
                add_thickbox();
                // Build page HTML
                $html = '<div class="wrap" id="plugin_settings">' . "\n";
                $html .= '
                <div style="display:none;" id="attribute_matcher">
                    <form id="attribute_matching_form" method="post">
                        <br/>
                        <input name="submit_attribute_matching_form" type="submit" class="button-primary" value="' . esc_attr(__('Set Mapping', 'plugin_textdomain')) . '" />
                    </form>
                </p></div>' . "\n";
                $html .= '<div title="" id="map_attributes"><span id="map_att_description"><a title="Mapping your HTML TAG attributes to your shortcode attributes." href="#TB_inline?width=600&height=550&inlineId=attribute_matcher" class="thickbox">Map HTML TAG attributes to your shortcode attributes.</span><span class="dashicons dashicons-location-alt"></span></a></div>' . "\n";
                $html .= '<div id="agsg_' . $page['slug'] . '_settings">' . "\n";
                $html .= '<h2>' . __($page['title'], 'plugin_textdomain') . '</h2>' . "\n";
//                $html .= '<div>' . __($page['sections']['html-enclose']['description'], 'plugin_textdomain') . '</div>' . "\n";
                $html .= '<form id="agsg_' . $page['slug'] . '_form" method="post" action="options.php" enctype="multipart/form-data">' . "\n";
                $html .= $this->display_hidden_fields();

                // Setup navigation
                //            $html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
                //            $html .= '<li><a class="tab all current" href="#all">' . __('All', 'plugin_textdomain') . '</a></li>' . "\n";
                //            foreach ($page['sections'] as $section => $data) {
                //                $html .= '<li>| <a class="tab" id="' . $section . '_tab" href="#' . $section . '">' . $data['title'] . '</a></li>' . "\n";
                //            }

                $html .= '</ul>' . "\n";

                $html .= '<div class="clear"></div>' . "\n";
                // Get settings fields
                ob_start();
                ?>
                <div id="<?php echo $page['slug'] . '_container' ?>" class="settings_section_container">
                    <?php
                    settings_fields($page['slug']);
                    do_settings_sections($page['slug']);
                    //                    wp_editor('', 'test', $settings = array());
                    ?>
                </div>
                <?php

                $html .= ob_get_clean();

                $html .= '<p class="submit">' . "\n";
                $html .= $this->get_submit_button();
                $html .= '</p>' . "\n";
                $html .= '</form>' . "\n";
                $html .= '<div id="agsg_shortcode_preview"></div>';
                $html .= '</div>' . "\n";
                $html .= '</div>' . "\n";

                echo $html;
            }
        }
    }
}