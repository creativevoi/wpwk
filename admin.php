<?php

class WillKitAdmin extends CVCore 
{
    function __construct()
    {
        parent::__construct();
        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_action('admin_head', array(&$this, 'admin_head'));
    }
    
    function admin_head()
    {
?>
<link rel="stylesheet" type="text/css" href="<?php echo WILLKIT_PLUGIN_URL?>css/admin/core.css" />
<?php
    }
    
    function admin_menu()
    {
        $hookSuffix = add_menu_page('Will Kit', 'Will Kit', 10, 'willkit', array(&$this, 'index'), WILLKIT_PLUGIN_URL . 'images/will.png');
        add_action('admin_footer-'.$hookSuffix, array(&$this, 'index_footer'));
    }
    
    function index()
    {
        $tblWill = $this->db->prefix . WILLKIT_TABLE;
        
        $sWhere = "WHERE deleted = 0";
        $sOrder = "ORDER BY `id` DESC";
        
        if ( isset($_GET['completed']) AND $_GET['completed'] != '-1' ) {
        	$sWhere .= " AND completed = " . intval($_GET['completed']);
        }
        if ( isset($_GET['is_paid']) AND $_GET['is_paid'] != '-1' ) {
        	$sWhere .= " AND is_paid = " . intval($_GET['is_paid']);
        }
        if ( isset($_GET['s']) AND !empty($_GET['s']) ) {
            $s = $this->db->escape($_GET['s']);
        	$sWhere .= " AND (
        	               firstname LIKE '%$s%'
        	               OR lastname LIKE '%$s%'
        	               OR occupation LIKE '%$s%'
        	               OR email LIKE '%$s%'
    	               )";
        }
        
        $pageno = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $itemsPerPage = 20;
        
        $sql = "SELECT COUNT(*) AS `total`
                FROM $tblWill
                $sWhere";
        $totalItems = $this->db->get_var($sql);
        $totalPages = ceil($totalItems/$itemsPerPage);
        
        if ( $pageno > $totalPages ) $pageno = $totalPages;
        if ( $pageno < 1 ) $pageno = 1;
        $startIndex = ($pageno-1) * $itemsPerPage;
        
        $sql = "SELECT *
                FROM $tblWill
                $sWhere
                $sOrder
                LIMIT $startIndex, $itemsPerPage";
        $this->data['items'] = $this->db->get_results($sql);
        $this->data['pagination'] = $this->pagination($pageno, $totalItems, $itemsPerPage);
        
        $this->loadview('admin/list_will', $this->data);
    }
    
    function index_footer()
    {
?>
    <script type="text/javascript" src="<?php echo WILLKIT_PLUGIN_URL?>js/admin/list-will.js"></script>
<?php
    }
}

$WillKitAdmin = new WillKitAdmin();