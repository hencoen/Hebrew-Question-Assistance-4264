<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Set proper headers and clean output
header('Content-Type: application/json');
ob_clean();

// Check permissions first
if (!is_admin() && function_exists('has_permission') && !has_permission('suppliers', '', 'view')) {
    echo json_encode([
        'draw' => intval($CI->input->get('draw')),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => []
    ]);
    exit();
}

try {
    // Always use custom implementation since data_tables_init seems to have issues
    $draw = intval($CI->input->get('draw')) ?: 1;
    $start = intval($CI->input->get('start')) ?: 0;
    $length = intval($CI->input->get('length')) ?: 10;
    
    // Get search value
    $search = $CI->input->get('search');
    $search_value = isset($search['value']) ? $search['value'] : '';
    
    // Get order
    $order = $CI->input->get('order');
    $order_column = isset($order[0]['column']) ? intval($order[0]['column']) : 0;
    $order_dir = isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';
    
    // Base query for counting total records
    $CI->db->select('COUNT(*) as total');
    $CI->db->from(db_prefix() . 'suppliers');
    $total_records = $CI->db->get()->row()->total;
    
    // Base query for data
    $CI->db->select('s.userid, s.company, s.datecreated, s.active, 
                     CONCAT(COALESCE(sc.firstname, ""), " ", COALESCE(sc.lastname, "")) as primary_contact');
    $CI->db->from(db_prefix() . 'suppliers s');
    $CI->db->join(db_prefix() . 'supplier_contacts sc', 'sc.userid = s.userid AND sc.is_primary = 1', 'left');
    
    // Apply search if provided
    if (!empty($search_value)) {
        $CI->db->group_start();
        $CI->db->like('s.company', $search_value);
        $CI->db->or_like('sc.firstname', $search_value);
        $CI->db->or_like('sc.lastname', $search_value);
        $CI->db->group_end();
    }
    
    // Get filtered count
    $total_filtered = $CI->db->count_all_results('', false);
    
    // Apply ordering
    switch($order_column) {
        case 0:
            $CI->db->order_by('s.userid', $order_dir);
            break;
        case 1:
            $CI->db->order_by('s.company', $order_dir);
            break;
        case 2:
            $CI->db->order_by('primary_contact', $order_dir);
            break;
        case 3:
            $CI->db->order_by('s.datecreated', $order_dir);
            break;
        default:
            $CI->db->order_by('s.userid', 'desc');
    }
    
    // Apply limit
    if ($length != -1) {
        $CI->db->limit($length, $start);
    }
    
    $results = $CI->db->get()->result_array();
    
    // Format data for DataTables
    $data = [];
    foreach ($results as $row) {
        $company_name = $row['company'];
        
        // Add inactive label if needed
        if (isset($row['active']) && $row['active'] == 0) {
            $company_name .= ' <span class="label label-default pull-right">' . _l('inactive') . '</span>';
        }
        
        // Clean up primary contact
        $primary_contact = trim($row['primary_contact']);
        if (empty($primary_contact) || $primary_contact == ' ') {
            $primary_contact = '';
        }
        
        // Build options buttons
        $options = '';
        if (has_permission('suppliers', '', 'edit') || is_admin()) {
            $options .= '<a href="' . admin_url('suppliers/supplier/' . $row['userid']) . '" class="btn btn-default btn-icon" title="עריכה"><i class="fa fa-pencil-square-o"></i></a>';
        }
        if (has_permission('suppliers', '', 'delete') || is_admin()) {
            $options .= ' <a href="' . admin_url('suppliers/delete/' . $row['userid']) . '" class="btn btn-danger btn-icon _delete" title="מחיקה"><i class="fa fa-remove"></i></a>';
        }
        
        $data[] = [
            $row['userid'],
            '<a href="' . admin_url('suppliers/supplier/' . $row['userid']) . '">' . $company_name . '</a>',
            $primary_contact,
            _d($row['datecreated']),
            $options
        ];
    }
    
    // Prepare final output
    $output = [
        'draw' => $draw,
        'recordsTotal' => intval($total_records),
        'recordsFiltered' => intval($total_filtered),
        'data' => $data
    ];
    
    // Debug logging
    error_log('Suppliers table output: ' . json_encode([
        'draw' => $draw,
        'total_records' => $total_records,
        'total_filtered' => $total_filtered,
        'data_count' => count($data)
    ]));
    
    echo json_encode($output);
    
} catch (Exception $e) {
    error_log('Suppliers table exception: ' . $e->getMessage());
    echo json_encode([
        'draw' => intval($CI->input->get('draw')) ?: 1,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage()
    ]);
}

exit();
?>