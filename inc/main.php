<?php

class WillKitMain extends WillKit
{
	function __construct()
	{
		parent::__construct();

		add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));
		add_action('wp_head', array(&$this, 'wp_head'));
		add_action('wp_footer', array(&$this, 'wp_footer'));
		add_shortcode($this->shortcode, array(&$this, 'will_form'));
	}

	function wp_enqueue_scripts()
	{
		wp_enqueue_script('jquery');
		$jsObject = array(
						'ajaxurl' => admin_url('admin-ajax.php'),
						'forms' => $this->forms,
						'prefix' => $this->pluginPrefix . '_'
					);
		wp_localize_script('jquery', $this->jsObject, $jsObject);
	}

	function wp_head()
	{
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->pluginURL;?>css/fonts.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->pluginURL;?>css/willkit.css" />
<?php
	}

	function wp_footer()
	{
?>
<script type="text/javascript" src="<?php echo $this->pluginURL;?>js/main.js"></script>
<?php
	}

	function will_form($atts)
	{
		extract(shortcode_atts(array(), $atts));

		return $this->loadTpl('will_form', true);
	}
}