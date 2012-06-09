<?php

class WillKitHelpers extends WillKit
{
	function __construct()
	{
		parent::__construct();
	}

	function selectbox_days($name='', $selected='', $extra='')
	{
	    $options = array(''=>'Day');
	    for ($i=1; $i<=31; $i++)
	    {
	        $options["$i"] = (strlen($i)==1) ? "0$i" : $i;
	    }
	    return $this->_selectbox($name, $options, $selected, $extra);
	}

	function selectbox_months($name='', $selected='', $extra='')
	{
	    $options = array(''=>'Month');
	    for ($i=1; $i<=12; $i++)
	    {
	        $options["$i"] = (strlen($i)==1) ? "0$i" : $i;
	    }
	    return $this->_selectbox($name, $options, $selected, $extra);
	}

	function selectbox_years($name='', $selected='', $extra='')
	{
	    $thisYear = date('Y');
	    $options = array(''=>'Year');
	    for ($i=($thisYear-18); $i>=1900; $i--)
	    {
	        $options["$i"] = $i;
	    }
	    return $this->_selectbox($name, $options, $selected, $extra);
	}

	function selectbox_states($name='', $selected='', $extra='')
	{
	    $options = array(
	                    '' => 'Choose state',
	                    'Queensland' => 'Queensland',
	                    'New South Wales' => 'New South Wales',
	                    'Victoria' => 'Victoria',
	                    'Australian Capital Territory' => 'Australian Capital Territory',
	                    'Northern Territory' => 'Northern Territory',
	                    'South Australia' => 'South Australia',
	                    'Western Australia' => 'Western Australia',
	                    'Tasmania' => 'Tasmania',
	                );
	    return $this->_selectbox($name, $options, $selected, $extra);
	}

	function infoimg($text='')
	{
	    return '<img src="' . $this->pluginURL . 'images/icon-info.png" alt="info" title="'.$text.'" class="infoimg" />';
	}

	function _selectbox($name='', $options=array(), $selected=array(), $extra='')
	{
	    $html = '<select name="'.$name.'"'.$extra.'>';
	    foreach ( $options as $value=>$label )
	    {
	        if ( is_array($selected) ) {
	            $isSelected = in_array($value, $selected);
	        } else {
	            $isSelected = ($value==$selected);
	        }
	        $html .= '<option value="'.$value.'"'.($isSelected ? ' selected="selected"' : '').'>'.$label.'</option>';
	    }
	    $html .= '</select>';
	    return $html;
	}
}