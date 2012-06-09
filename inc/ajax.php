<?php

class WillKitAjax extends WillKit
{
    // 0 = nopriv, 1 = priv, other = priv & nopriv
    var $actions = array(
                        'export_customer' => 1,
                        'load_form' => -1,
                    );

    function __construct()
    {
        parent::__construct();

        $this->loadClass('helpers');

        foreach ( $this->actions as $action=>$actionOption )
        {
            if ($actionOption == 0) {
                add_action("wp_ajax_nopriv_{$this->pluginPrefix}_$action", array(&$this, $action));
            } elseif ($actionOption == 1) {
                add_action("wp_ajax_{$this->pluginPrefix}_$action", array(&$this, $action));
            } else {
                add_action("wp_ajax_{$this->pluginPrefix}_$action", array(&$this, $action));
                add_action("wp_ajax_nopriv_{$this->pluginPrefix}_$action", array(&$this, $action));
            }
        }
    }
    
    function export_customer()
    {
        $tblWill = $this->db->prefix . WILLKIT_TABLE;
        
        $sWhere = "WHERE deleted = 0";
        
        if ( isset($_POST['completed']) AND $_POST['completed'] != '-1' ) {
        	$sWhere .= " AND completed = " . intval($_POST['completed']);
        }
        if ( isset($_POST['is_paid']) AND $_POST['is_paid'] != '-1' ) {
        	$sWhere .= " AND is_paid = " . intval($_POST['is_paid']);
        }
        if ( isset($_POST['s']) AND !empty($_POST['s']) ) {
            $s = $this->db->escape($_POST['s']);
        	$sWhere .= " AND (
        	               firstname LIKE '%$s%'
        	               OR lastname LIKE '%$s%'
        	               OR occupation LIKE '%$s%'
        	               OR email LIKE '%$s%'
    	               )";
        }
        
        $sql = "SELECT *
                FROM $tblWill
                $sWhere";
        $items = $this->db->get_results($sql);
        
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=willkit-customers-".date('HisdmY').".csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo "Name,Email\n";
        foreach ( $items as $item )
        {
            echo stripslashes(utf8_decode(trim("$item->firstname $item->lastname"))) . "," . stripslashes($item->email) . "\n";
        }
        exit();
    }
    
    function load_form()
    {
        $form = isset($_POST['form']) ? trim($_POST['form']) : '';
        if ( !in_array($form, $this->forms) ) {
        	$form = $this->forms[0];
        }
        $this->loadTpl("forms/$form");
        exit;
    }
}