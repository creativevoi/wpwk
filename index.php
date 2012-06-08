<?php
/*
Plugin Name: Will Kit
Description: Plugin for thewillkit.com.au
Author: Son NH
Author URI: http://creativevoi.com
Version: 1.0
*/

define('WILLKIT_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('WILLKIT_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('WILLKIT_TABLE', 'willdata');

require_once(WILLKIT_PLUGIN_PATH.'inc/core.php');
require_once(WILLKIT_PLUGIN_PATH.'inc/helper.php');
require_once(WILLKIT_PLUGIN_PATH.'ajax.php');
require_once(WILLKIT_PLUGIN_PATH.'admin.php');

class WillKit extends CVCore 
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
    
    function on_activation()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $table = $this->db->prefix . WILLKIT_TABLE;
        if($this->db->get_var("SHOW TABLES LIKE '$table'") != $table) {
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
}

$WillKit = new WillKit();