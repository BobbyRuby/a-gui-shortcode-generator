<?php
/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
require_once(ABSPATH . 'wp-content/plugins/a-gui-shortcode-generator/assets/php/class-wp-list-table.php');

/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 */
class agsg_shortcode_table extends agsg_WP_List_Table
{

    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct()
    {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'shortcode', //singular name of the listed records
            'plural' => 'shortcodes', //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ));
    }

    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_name($item)
    {
        //Return the title contents
        return sprintf('<span id="list_name_%2$s">%1$s</span> <span id="list_id_%2$s" style="color:silver">(id:%2$s)</span>',
            /*$1%s*/
            $item['name'],
            /*$2%s*/
            $item['id']
//            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_code($item)
    {
        $data = preg_replace('/[<]/', '&lt;', $item['code']);
        return '<textarea readonly>' . $data . '</textarea>';
    }

    function column_created_datetime($item)
    {
        $old_date = $item['created_datetime']; // returns Saturday, January 30 10 02:06:34
        $old_date_timestamp = strtotime($old_date);
        $new_date = date('g:ia \o\n l jS F Y', $old_date_timestamp);
        return $new_date;
    }

    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/
            $this->_args['singular'], //Let's simply repurpose the table's singular label
            /*$2%s*/
            $item['id'] //The value of the checkbox should be the record's id
        );
    }

    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
//            'type' => 'Type',
//            'name' => 'Name',
            'tag' => 'Shortcode Name',
            'kind' => 'Kind',
            'description' => 'Description',
            'example' => 'Example',
            'code' => 'Code',
            'created_datetime' => 'Created Datetime'
        );
        return $columns;
    }

    /**
     * used to grab cols to output search by checkboxes
     * @return array
     */
    function get_search_cols()
    {
        $columns = array(
            'tag' => 'Shortcode Name',
            'kind' => 'Kind',
            'description' => 'Description',
            'example' => 'Example',
            'code' => 'Code',
            'created_datetime' => 'Created Datetime'
        );
        return $columns;
    }

    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns()
    {
        $sortable_columns = array(
//            'id'     => array('id',false),  //true means it's already sorted
//            'type' => array('type', false),
//            'name' => array('name', false),
            'tag' => array('tag', false),
            'kind' => array('kind', false),
        );
        return $sortable_columns;
    }

    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions()
    {
        $actions = array(
            'delete_selected' => 'Delete Selected',
        );
        return $actions;
    }

    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action()
    {
        //Detect when a bulk action is being triggered...
        if ('delete_selected' === $this->current_action() && $_GET['shortcode']) {
            foreach ($_GET['shortcode'] as $id) {
                $this->delete_shortcode($id);
            }
        }
    }

    function delete_shortcode($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'agsg_shortcodes';
        $row = $wpdb->get_row("SELECT * FROM $wpdb->prefix" . "agsg_shortcodes WHERE id = '$id'");
        $tag = $row->tag;
        $wpdb->delete($table, array('id' => $id));
        $filename = plugin_dir_path(__FILE__) . 'agsg_shortcodes.php';
        $source_file = file_get_contents($filename);
        $source = preg_replace('/(\/\/' . $tag . ')(.*)(\/\/' . $tag . ')/s', "", $source_file);
        file_put_contents($filename, $source);
    }

    function get_shortcodes($order_params = false, $search_params = false)
    {
        global $wpdb;
        if ($order_params) {
            $orderby = $order_params['orderby'];
            $order = $order_params['order'];
            $shortcodes = $wpdb->get_results("SELECT * FROM $wpdb->prefix" . "agsg_shortcodes ORDER BY $orderby $order", ARRAY_A);
        } else
            if ($search_params) {
                $s = $search_params['s'];
                $by = $search_params['by'];
                for ($i = 0; $i < count($by); $i++) {
                    if ($i === 0) {
                        $search = "WHERE $by[$i] LIKE '%$s%'";
                    } else {
                        $search .= " OR $by[$i] LIKE '%$s%'";
                    }

                }
                $shortcodes = $wpdb->get_results("SELECT * FROM $wpdb->prefix" . "agsg_shortcodes $search", ARRAY_A);
            } else {
                $shortcodes = $wpdb->get_results("SELECT * FROM $wpdb->prefix" . "agsg_shortcodes", ARRAY_A);
        }
        return $shortcodes;
    }

    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items()
    {
        global $wpdb; //This is used only if making any database queries
        // get the current user ID
        $user = get_current_user_id();
        // get the current admin screen
        $screen = get_current_screen();
        // retrieve the "per_page" option
        $screen_option = $screen->get_option('per_page', 'option');
        // retrieve the value of the option stored for the current user
        $per_page = get_user_meta($user, $screen_option, true);
        if (empty ($per_page) || $per_page < 1) {
            // get the default value if none is set
            $per_page = $screen->get_option('per_page', 'default');
        }

        $this->_column_headers = $this->get_column_info(); // works with screen options function add_options

        $this->process_bulk_action();
        // is sorted
        if (isset($_GET['orderby']) && isset($_GET['order'])) {
            $order_params['orderby'] = $_GET['orderby'];
            $order_params['order'] = $_GET['order'];
            $data = $this->get_shortcodes($order_params);
        } else
            // is search
            if (isset($_POST['s'])) {
                $search_params = array('s' => $_POST['s'], 'by' => $_POST['searchBy']);
                $data = $this->get_shortcodes(false, $search_params);
            } else {
            $data = $this->get_shortcodes();
        }
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        if (is_array($data))
            $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page, //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page) //WE have to calculate the total number of pages
        ));
    }
}

/** ************************ REGISTER THE PAGE ****************************
 *******************************************************************************
 * Now we just need to define an admin page. For this example, we'll add a top-level
 * menu item to the bottom of the admin menus.
 */
function agsg_shortcode_add_menu_items()
{
    global $shortcode_page;
    $shortcode_page = add_menu_page('AGSG Shortcode List', 'AGSG Shortcode List', 'manage_options', 'shortcode_list', 'agsg_shortcode_render_list_page');
    add_action("load-$shortcode_page", "agsg_shortcode_page_screen_options");
    add_action('admin_print_styles-' . $shortcode_page, 'agsg_shortcode_list_css_enqueue');
    add_action('admin_print_scripts-' . $shortcode_page, 'agsg_shortcode_list_js_enqueue');
}

function agsg_shortcode_list_css_enqueue()
{
    $assets_url = esc_url(trailingslashit(plugins_url('/assets/', __FILE__)));
    wp_enqueue_style('agsg-list-page-css', $assets_url . 'css/list-page.css');
}

function agsg_shortcode_list_js_enqueue()
{
    $assets_url = esc_url(trailingslashit(plugins_url('/assets/', __FILE__)));
    wp_enqueue_script('agsg-list-page-js', $assets_url . 'js/list-page.js');
}

/***************************** SCREEN OPTIONS ********************************
 ********************************************************************************/
function agsg_shortcode_page_screen_options()
{
    global $shortcode_page;
    global $shortcode_list_table;
    $screen = get_current_screen();
    // get out of here if we are not on our settings page
    if (!is_object($screen) || $screen->id != $shortcode_page)
        return;

    $args = array(
        'label' => __('Shortcodes per page', 'shortcode'),
        'default' => 10,
        'option' => 'shortcode_per_page'
    );
    add_screen_option('per_page', $args);

    $shortcode_list_table = new agsg_shortcode_table();
}

function agsg_shortcode_per_page_set_screen_option($status, $option, $value)
{
    if ('shortcode_per_page' == $option) return $value;
}

/***************************** RENDER THE PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function agsg_shortcode_render_list_page()
{
    global $shortcode_page;
    global $shortcode_list_table;
    //Create an instance of our package class...

    //Fetch, prepare, sort, and filter our data...
    $shortcode_list_table->prepare_items();
    ?>
    <!-- serach form -->
    <div class="search-container">
        <form method="post">
            <input type="hidden" name="page" value="<?php echo $shortcode_page ?>"/>
            <?php $shortcode_list_table->search_box('Search by Name', $shortcode_page); ?>
            <div class="check-box-container">
                <?php
                $cols = $shortcode_list_table->get_search_cols();
                $col_ks = array_keys($cols);
                echo "<span class='search-by'><strong>Search By:</strong></span>";
                for ($i = 0; $i < count($cols); $i++) {
                    $col_k = $col_ks[$i];
                    echo "<label for='searchBy-" . $col_ks[$i] . "'> $cols[$col_k] </label>
                <input id='searchBy-" . $col_ks[$i] . "' type='checkbox' name='searchBy[]' value='" . $col_ks[$i] . "' />";
                }
                echo "<span class='search-by'><strong>For:</strong></span>";
                ?>
            </div>
        </form>
    </div>
    <div class="search-response"><?php
        if (isset($_POST['s']) && !isset($_POST['searchBy'])) {
            echo '<p class="error form-invalid">No columns selected, please select a column.</p>';
        } else if (isset($_POST['s']) && $_POST['s'] === '') {
            echo '<p class="error form-invalid">Please enter a search phrase to look for.</p>';
        }
        ?></div>
    <!--    <div class="search-response">-->
    <!--        --><?php // @todo - Cannot figure out why this will not show columns when bottom labels are clicked
//        if(isset($_POST['s']) ){
//            if(isset($_POST['searchBy'])){
//                $s = $_POST['s'];
//                $s_msg = "Search results for query $s in columns:";
//                $by = $_POST['searchBy'];
//                for($i = 0; $i < count($by); $i++){
//                    if($by[$i] == 'tag'){
//                        $col = 'Shortcode Name';
//                    }
//                    if($i === 0){
//                        $s_msg .= ' '.ucfirst($col);
//                    }
//                    else{
//                        $s_msg .= ' / '.ucfirst($col);
//                    }
//                }
//            }else{
//                $s_msg = 'No columns selected for search.  All shortcodes returned.';
//            }
//            echo $s_msg;
//        }
//
    ?>
    <!--    </div>-->
    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="shortcodes_filter" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php
        $shortcode_list_table->display();
        ?>
    </form>
    <div class="check-box-container-bottom">
        <?php
        echo "<span class='search-by'><strong>Search By:</strong></span>";
        for ($i = 0; $i < count($cols); $i++) {
            $col_k = $col_ks[$i];
            echo "<label for='searchBy-" . $col_ks[$i] . "'> $cols[$col_k] </label>
                <input id='searchBy" . $col_ks[$i] . "' type='checkbox' name='searchBy[]' value='" . $col_ks[$i] . "' />";
        }
        echo "<span class='search-by'><strong>For:</strong></span>";
        ?>
    </div>
<?php
}