<?php

class WillKitModel_will
{
    var $db = false;
    var $table = '';
    var $results = array();
    var $row = array();
    
    function __construct($tableName)
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix . $tableName;
    }
    
    function get_list($page=1, $itemsPerPage=20, $total=false, $filters=array())
    {
        $sWhere = "WHERE deleted = 0";
        $sOrder = "ORDER BY `id` DESC";
        
        if ( isset($filters['completed']) AND $filters['completed'] != '-1' ) {
            $sWhere .= " AND completed = " . intval($filters['completed']);
        }
        if ( isset($filters['is_paid']) AND $filters['is_paid'] != '-1' ) {
            $sWhere .= " AND is_paid = " . intval($filters['is_paid']);
        }
        if ( isset($filters['s']) AND !empty($filters['s']) ) {
            $s = $this->db->escape($filters['s']);
            $sWhere .= " AND (
                           firstname LIKE '%$s%'
                           OR lastname LIKE '%$s%'
                           OR occupation LIKE '%$s%'
                           OR email LIKE '%$s%'
                       )";
        }
        
        if ( $total ) {
            $sql = "SELECT COUNT(*) AS `total`
                    FROM $this->table
                    $sWhere";
            return $this->db->get_var($sql);
        } else {
            if ( $itemsPerPage > 0 ) {
                $startIndex = ($page-1) * $itemsPerPage;
                $sLimit = "LIMIT $startIndex, $itemsPerPage";
            } else {
                $sLimit = "";
            }
            
            $sql = "SELECT $this->table.*
                    FROM $this->table
                    $sWhere
                    $sOrder
                    $sLimit";

            $this->results = $this->db->get_results($sql);
            
            return $this->results;
        }
    }
    
    function delete( $id )
    {
        $sql = "DELETE FROM $this->table
                WHERE id = '$id'";
        $this->db->query($sql);
        return true;
    }
    
    function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id;
    }
    
    function update($id, $data=array())
    {
        $this->db->update($this->table, $data, array('id'=>$id));
        return 1;
    }
    
    function get_detail( $id )
    {
        $sql = "SELECT $this->table.*
                FROM $this->table
                WHERE `id` = $id";
        $this->row = $this->db->get_row($sql);
        return $this->row;
    }
    

}