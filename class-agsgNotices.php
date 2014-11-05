<?php
/**
 * Class agsgNotices
 * @package WordPress
 * @subpackage AGSG
 */
class agsgNotices
{
    private $content;
    private $class;
    private $page;

    public function __construct($content, $class, $page = '')
    {
        $this->content = $content;
        $this->class = $class;
        $this->page = $page;
        add_action('agsg_admin_notices', array(&$this, 'showMsg'));
    }

    public function showMsg()
    {
        echo "<div class='" . $this->class . "'>
                " . _e($this->content) . "
            </div>";
    }

}