<?php

class CVCore
{
    var $data = array();
    var $forms = array(
                        'personal_details',
                        'executors',
                    );
    var $db;
    
    function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }
    
    function loadview($tplPath, $data=array(), $return=false)
    {
        $data = (array) $data;
        foreach ( $data as $key=>$value )
        {
            $$key = $value;
        }
        $filePath = WILLKIT_PLUGIN_PATH . "view/$tplPath.php";
        if ( !file_exists($filePath) ) {
            $filePath = WILLKIT_PLUGIN_PATH . "view/misc/file_not_found.php";
        	if ( $return ) {
            	ob_start();
                require_once($filePath);
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
        	} else {
                require_once($filePath);
        	}
        } else {
            if ( $return ) {
            	ob_start();
                require_once($filePath);
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
            } else {
                require_once($filePath);
            }
        }
    }
    
    function debug($var, $exit=FALSE)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        if ($exit) exit();
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