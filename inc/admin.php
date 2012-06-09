<?php

class WillKitAdmin extends WillKit
{
	function __construct()
	{
		parent::__construct();

		$this->loadModel('will', 'willmd');

		add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
        add_action('admin_head', array(&$this, 'admin_head'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
	}

	function admin_enqueue_scripts()
	{
		wp_enqueue_script('jquery');
		$jsObject = array(
							'ajaxurl' => admin_url('admin-ajax.php'),
							'prefix' => $this->pluginPrefix . '_'
						);
		wp_localize_script('jquery', $this->jsObject, $jsObject);
	}

	function admin_head()
	{
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->pluginURL?>css/admin/wpwk.css" />
<?php
	}

	function admin_menu()
	{
        $hookSuffix = add_menu_page('Will Kit', 'Will Kit', 'manage_options', $this->pluginPrefix . '_manage_will', array(&$this, 'index'), $this->pluginURL . 'images/will.png');
        add_action('admin_footer-'.$hookSuffix, array(&$this, 'index_footer'));
	}

	function index()
	{
		$filters = array();
        if ( isset($_GET['completed']) AND $_GET['completed'] != '-1' ) {
        	$filters['completed'] = intval($_GET['completed']);
        }
        if ( isset($_GET['is_paid']) AND $_GET['is_paid'] != '-1' ) {
        	$filters['is_paid'] = intval($_GET['is_paid']);
        }
        if ( isset($_GET['s']) AND !empty($_GET['s']) ) {
        	$filters['s'] = trim($_GET['s']);
        }

        $pageno = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $itemsPerPage = 20;
        $totalItems = $this->willmd->get_list(0, 0, true, $filters);
        $totalPages = ceil($totalItems/$itemsPerPage);
        if ( $pageno > $totalPages ) $pageno = $totalPages;
        if ( $pageno < 1 ) $pageno = 1;

        $this->data['items'] = $this->willmd->get_list($pageno, $itemsPerPage, false, $filters);
        $this->data['pagination'] = $this->pagination($pageno, $totalItems, $itemsPerPage);
        
        $this->loadTpl('admin/list_will');
	}

	function index_footer()
	{
?>
    <script type="text/javascript" src="<?php echo $this->pluginURL?>js/admin/list-will.js"></script>
<?php
	}
}