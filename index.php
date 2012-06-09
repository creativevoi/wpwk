<?php
/*
Plugin Name: Will Kit
Description: Plugin for thewillkit.com.au
Author: Son NH
Author URI: http://creativevoi.com
Version: 1.0
*/

class WillKit
{
    var $table_will = 'willdata';
    var $pluginPrefix = 'wpwk';
    var $version = '2.0';
    var $shortcode = 'WPWK';
    var $jsObject = '_wk';
    var $forms = array(
                        'personal_details',
                        'executors',
                    );

    var $pluginPath = '';
    var $pluginURL = '';
    var $incPath = '';
    var $adminClass = false;
    var $mainClass = false;
    var $ajaxClass = false;
    var $helpers = false;
    var $data = array();

    function __construct()
    {
        $this->pluginPath = plugin_dir_path(__FILE__);
        $this->pluginURL = plugin_dir_url(__FILE__);
        $this->incPath = $this->pluginPath . 'inc/';
    }

/********** INITIALLY FUNCTIONS **********/

    function init()
    {
        if (is_admin() AND file_exists($this->incPath . 'admin.php')) {
            // load admin class
            require_once($this->incPath . 'admin.php');
            $className = __CLASS__ . 'Admin';
            $this->adminClass = new $className();
        } elseif (file_exists($this->incPath . 'main.php')) {
            // load main class
            require_once($this->incPath . 'main.php');
            $className = __CLASS__ . 'Main';
            $this->mainClass = new $className();
        }

        if (file_exists($this->incPath . 'ajax.php')) {
            // load ajax class
            require_once($this->incPath . 'ajax.php');
            $className = __CLASS__ . 'Ajax';
            $this->ajaxClass = new $className();
        }

        register_activation_hook(__FILE__, array(&$this, 'on_activation'));
        register_deactivation_hook(__FILE__, array(&$this, 'on_deactivation'));
    }

    function on_activation()
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $table = $wpdb->prefix . $this->table_willdata;
        if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            $sql = "CREATE TABLE /*!32312 IF NOT EXISTS*/ `$table` (
                      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                      PRIMARY KEY (`id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
                    ";
            dbDelta($sql);
        }
    }
    
    function on_deactivation()
    {
    }

/********** UTILITY FUNCTIONS **********/

    function loadClass($name='')
    {
        if (file_exists($this->incPath . $name . '.php')) {
            require_once($this->incPath . $name . '.php');
            $className = __CLASS__ . ucfirst($name);
            $this->$name = new $className();
        }
    }

    function loadModel($name='', $shortname='')
    {
        $modelPath = $this->incPath . 'model_' . $name . '.php';
        if ( !file_exists($modelPath) ) {
            wp_die("File <strong>$modelPath</strong> does not exist.");
        } else {
            if ( property_exists($this, "table_$name") ) {
                $tableName = $this->{"table_$name"};
            } else {
                wp_die("Property <strong>table_$name</strong> does not exist in core class.");
            }
            require_once($modelPath);
            
            $className = __CLASS__ . "Model_$name";
            
            if ( !empty($shortname) ) {
                $this->{$shortname} = new $className($tableName);
            } else {
                $this->{$name} = new $className($tableName);
            }
        }
    }

    function loadTpl($name='', $return=false)
    {
        $tplPath = $this->pluginPath . 'tpl/' . $name . '.php';
        if ( !file_exists($tplPath) ) {
            wp_die("File <strong>$tplPath</strong> does not exist.");
        } else {
            foreach ($this->data as $var=>$value) {
                $$var = $value;
            }
            if ( !$return ) {
                require($tplPath);
            } else {
                ob_start();
                require($tplPath);
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
            }
        }
    }
    
    function strip_request()
    {
        $ar = array();
        foreach ($_GET as $key => $value)
        {
            if ( !in_array($key, array('p')) )
                $ar[$key] = $value;
        }
        return $ar;
    }
    
    function pagination($page = 1, $total = 0, $rows = 1, $is_ajax = FALSE)
    {
        $rows = intval($rows);
        $page = intval($page);
        $total = intval($total);
        if ($total == 0) {
            return array(
                'text' => "Showing 0/0",
                'goto_slb' => 'N/A',
                'ipp_slb' => 'N/A',
                'current_page' => 0,
                'total_page' => 0,
                'total_item' => 0,
                'links' => ""
            );
        }
        if ($rows <= 0)
            $rows = 1;
        $totalPages = ceil($total / $rows);
        if ($page <= 0)
            $page = 1;
        if ($page > $totalPages)
            $page = $totalPages;
    
        $sReturn = "";
        $offset = 2;
        $loop_num = $offset * 2 + 1;
    
        $params = '';
        $ar_params = $this->strip_request();
    
        foreach ($ar_params as $key => $value) {
            $params .= $key . '=' . urlencode($value) . '&amp;';
        }
        
        $base_url = site_url($_SERVER['PHP_SELF']) . '?' . $params . 'p=';
    
        if ($page != 1) {
            $href = $is_ajax ? "javascript:;" : $base_url . ($page - 1);
            $sReturn .= "<a href='$href' title='Go to page " . ($page - 1) . "' p='" . ($page - 1) . "' class='page_numbers prev'><span>&laquo; Previous</span></a>";
        }
    
        if ($totalPages < $loop_num + 4) {
            // NOT MUCH PAGE
            for ($i = 1; $i <= $totalPages; $i++) {
                $href = $is_ajax ? "javascript:;" : $base_url . $i;
                $sReturn .= ( $i == $page) ?
                        "<span class='page_numbers current_page'>$i</span>" :
                        "<a href='$href' title='Go to page $i' p='$i' class='page_numbers'><span>$i</span></a>";
            }
        } else {
            // MUCH PAGE
            if ($page < $loop_num) {
                for ($i = 1; $i <= $loop_num; $i++) {
                    $href = $is_ajax ? "javascript:;" : $base_url . $i;
                    $sReturn .= ( $i == $page) ?
                            "<span class='page_numbers current_page'>$i</span>" :
                            "<a href='$href' title='Go to page $i' p='$i' class='page_numbers'><span>$i</span></a>";
                }
                $href = $is_ajax ? "javascript:;" : $base_url . $totalPages;
                $sReturn .= "<span class='page_numbers dots'>...</span>"
                        . "<a href='$href' title='Go to page $totalPages' p='$totalPages' class='page_numbers'><span>$totalPages</span></a>";
            } else {
                $href = $is_ajax ? "javascript:;" : $base_url . "1";
                $sReturn .= "<a href='$href' title='Go to page 1' p='1' class='page_numbers'><span>1</span></a>"
                        . "<span class='page_numbers dots'>...</span>";
                if ($page > $totalPages - $loop_num + 1) {
                    for ($i = $totalPages - $loop_num + 1; $i <= $totalPages; $i++) {
                        $href = $is_ajax ? "javascript:;" : $base_url . $i;
                        $sReturn .= ( $i == $page) ?
                                "<span class='page_numbers current_page'>$i</span>" :
                                "<a href='$href' title='Go to page $i' p='$i' class='page_numbers'><span>$i</span></a>";
                    }
                } else {
                    for ($i = $page - $offset; $i <= $page + $offset; $i++) {
                        $href = $is_ajax ? "javascript:;" : $base_url . $i;
                        $sReturn .= ( $i == $page) ?
                                "<span class='page_numbers current_page'>$i</span>" :
                                "<a href='$href' title='Go to page $i' p='$i' class='page_numbers'><span>$i</span></a>";
                    }
                    $href = $is_ajax ? "javascript:;" : $base_url . $totalPages;
                    $sReturn .= "<span class='page_numbers dots'>...</span>"
                            . "<a href='$href' title='Go to page $totalPages' p='$totalPages' class='page_numbers'><span>$totalPages</span></a>";
                }
            }
        }
    
        if ($page < $totalPages) {
            $href = $is_ajax ? "javascript:;" : $base_url . ($page + 1);
            $sReturn .= "<a href='$href' title='Go to page " . ($page + 1) . "' p='" . ($page + 1) . "' class='page_numbers next'><span>Next &raquo;</span></a>";
        }
    
        $slb_goto = '<select name="p" class="gotoslb paginationslb">';
        for ($i = 1; $i <= $totalPages; $i++) {
            $slb_goto .= '<option value="' . $i . '"' . ($i == $page ? ' selected="selected"' : '') . '>' . $i . '</option>';
        }
        $slb_goto .= '</select>';
    
        $slb_itemperpage = '<select name="ipp" class="ippslb paginationslb">';
        for ($i = 10; $i <= 100; $i+=10) {
            $slb_itemperpage .= '<option value="' . $i . '"' . ($i == $rows ? ' selected="selected"' : '') . '>' . $i . '</option>';
        }
        $slb_itemperpage .= '</select>';
    
        return array(
            'text' => "Showing item " . (($page - 1) * $rows + 1) . "-" . ($page * $rows < $total ? $page * $rows : $total) . " of " . number_format($total),
            'goto_slb' => $slb_goto,
            'ipp_slb' => $slb_itemperpage,
            'current_page' => $page,
            'total_page' => $totalPages,
            'total_item' => $total,
            'links' => $sReturn
        );
    }
    
}

global $WillKit;
$WillKit = new WillKit();
$WillKit->init();

// require_once(WILLKIT_PLUGIN_PATH.'inc/core.php');
// require_once(WILLKIT_PLUGIN_PATH.'inc/helper.php');
// require_once(WILLKIT_PLUGIN_PATH.'ajax.php');
// require_once(WILLKIT_PLUGIN_PATH.'admin.php');

class WillKit1 
{
    var $loadJsCss = false;
    
    function __construct()
    {
        parent::__construct();

        wp_enqueue_script('jquery');
        $jsObject = array(
                        'ajaxurl' => admin_url('admin-ajax.php'),
                        'forms' => $this->forms
                    );
        wp_localize_script('jquery', '_wk', $jsObject);

        add_action('wp_footer', array(&$this, 'wp_footer'));
        add_shortcode('WILLKIT', array(&$this, 'shortcode_willkit'));
        
        register_activation_hook(__FILE__, array(&$this, 'on_activation'));
        register_deactivation_hook(__FILE__, array(&$this, 'on_deactivation'));
    }
    
    function wp_footer()
    {
        if ( $this->loadJsCss ) {
        	echo  "\n"
    	           . '<link rel="stylesheet" type="text/css" href="' . WILLKIT_PLUGIN_URL . 'css/fonts.css" />' . "\n"
        	       . '<link rel="stylesheet" type="text/css" href="' . WILLKIT_PLUGIN_URL . 'css/willkit.css" />' . "\n"
        	       . '<script type="text/javascript" src="' . WILLKIT_PLUGIN_URL . 'js/willkit.js"></script>' . "\n";
        }
    }
    
    function shortcode_willkit($atts)
    {
        $this->loadJsCss = true;
        extract(shortcode_atts(array(
        ), $atts));
        
        $this->loadview('form', $this->data);
    }
}