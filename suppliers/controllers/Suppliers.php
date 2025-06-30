<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suppliers extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('suppliers_model');
    }

    /* List all suppliers */
    public function index()
    {
        if (!is_admin() && function_exists('has_permission') && !has_permission('suppliers', '', 'view')) {
            access_denied('suppliers');
        }

        $data['title'] = _l('suppliers');
        $this->load->view('suppliers/manage', $data);
    }

    /* Suppliers table - AJAX endpoint for DataTables */
    public function table()
    {
        // Debug: Log the table request
        error_log('Suppliers table method called - GET params: ' . print_r($_GET, true));

        // Set proper content type and clean output buffer
        header('Content-Type: application/json;charset=utf-8');
        ob_clean();

        if (!is_admin() && function_exists('has_permission') && !has_permission('suppliers', '', 'view')) {
            error_log('Access denied for suppliers table');
            echo json_encode([
                'draw' => intval($this->input->get('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
            exit();
        }

        try {
            // Handle directly since we're having issues with includes
            $this->handle_table_directly();
        } catch (Exception $e) {
            error_log('Table method exception: ' . $e->getMessage());
            echo json_encode([
                'draw' => intval($this->input->get('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
            exit();
        }
    }

    /* Handle table request directly */
    private function handle_table_directly()
    {
        try {
            // Get DataTables parameters
            $draw = intval($this->input->get('draw')) ?: 1;
            $start = intval($this->input->get('start')) ?: 0;
            $length = intval($this->input->get('length')) ?: 10;

            error_log("DataTables params: draw=$draw, start=$start, length=$length");

            // Get search value
            $search = $this->input->get('search');
            $search_value = isset($search['value']) ? trim($search['value']) : '';

            // Get order
            $order = $this->input->get('order');
            $order_column = isset($order[0]['column']) ? intval($order[0]['column']) : 0;
            $order_dir = isset($order[0]['dir']) && in_array($order[0]['dir'], ['asc', 'desc']) ? $order[0]['dir'] : 'desc';

            // Check if suppliers table exists
            if (!$this->db->table_exists(db_prefix() . 'suppliers')) {
                error_log('Suppliers table does not exist');
                echo json_encode([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Suppliers table does not exist'
                ]);
                exit();
            }

            // Count total records
            $this->db->select('COUNT(*) as total');
            $this->db->from(db_prefix() . 'suppliers');
            $query_result = $this->db->get();
            
            if (!$query_result) {
                error_log('Failed to count suppliers: ' . $this->db->error()['message']);
                throw new Exception('Database query failed');
            }

            $total_records = $query_result->row()->total;
            error_log("Total suppliers found: $total_records");

            // Build main query with proper contact joining
            $this->db->select('s.userid, s.company, s.datecreated, s.active');
            $this->db->from(db_prefix() . 'suppliers s');

            // Improved contact joining - get primary contact with proper CONCAT
            if ($this->db->table_exists(db_prefix() . 'supplier_contacts')) {
                $this->db->select('TRIM(CONCAT(COALESCE(sc.firstname, ""), " ", COALESCE(sc.lastname, ""))) as primary_contact', FALSE);
                $this->db->join(db_prefix() . 'supplier_contacts sc', 'sc.userid = s.userid AND sc.is_primary = 1', 'left');
            } else {
                $this->db->select('NULL as primary_contact', FALSE);
            }

            // Apply search filter
            if (!empty($search_value)) {
                $this->db->group_start();
                $this->db->like('s.company', $search_value);
                if ($this->db->table_exists(db_prefix() . 'supplier_contacts')) {
                    $this->db->or_like('sc.firstname', $search_value);
                    $this->db->or_like('sc.lastname', $search_value);
                    $this->db->or_like('CONCAT(sc.firstname, " ", sc.lastname)', $search_value);
                }
                $this->db->group_end();
            }

            // Get filtered count
            $total_filtered = $this->db->count_all_results('', false);
            error_log("Filtered suppliers: $total_filtered");

            // Apply ordering
            $order_columns = ['s.userid', 's.company', 'primary_contact', 's.datecreated'];
            if (isset($order_columns[$order_column])) {
                $this->db->order_by($order_columns[$order_column], $order_dir);
            } else {
                $this->db->order_by('s.userid', 'desc');
            }

            // Apply pagination
            if ($length != -1) {
                $this->db->limit($length, $start);
            }

            // Execute query
            $results = $this->db->get();
            
            if (!$results) {
                error_log('Failed to get suppliers data: ' . $this->db->error()['message']);
                throw new Exception('Database query failed');
            }

            $results = $results->result_array();
            error_log("Query executed, got " . count($results) . " results");

            // Format data for DataTables
            $data = [];
            foreach ($results as $row) {
                $company_name = htmlspecialchars($row['company'], ENT_QUOTES, 'UTF-8');

                // Add inactive label if needed
                if (isset($row['active']) && $row['active'] == 0) {
                    $company_name .= ' <span class="label label-default pull-right">לא פעיל</span>';
                }

                // Clean up primary contact - handle empty values properly
                $primary_contact = isset($row['primary_contact']) ? trim($row['primary_contact']) : '';
                
                // If primary contact is empty, null, or just spaces, show empty
                if (empty($primary_contact) || $primary_contact == ' ' || is_null($primary_contact)) {
                    $primary_contact = '';
                } else {
                    $primary_contact = htmlspecialchars($primary_contact, ENT_QUOTES, 'UTF-8');
                }

                // Build options buttons with proper FontAwesome icons
                $options = '';
                if (has_permission('suppliers', '', 'view') || is_admin()) {
                    $options .= '<a href="' . admin_url('suppliers/supplier/' . $row['userid']) . '" class="btn btn-default btn-icon" title="' . _l('view') . '"><i class="fa fa-eye"></i></a> ';
                }
                if (has_permission('suppliers', '', 'edit') || is_admin()) {
                    $options .= '<a href="' . admin_url('suppliers/supplier/' . $row['userid']) . '" class="btn btn-default btn-icon" title="' . _l('edit') . '"><i class="fa fa-pencil-square-o"></i></a> ';
                }
                if (has_permission('suppliers', '', 'delete') || is_admin()) {
                    $options .= '<a href="' . admin_url('suppliers/delete/' . $row['userid']) . '" class="btn btn-danger btn-icon _delete" title="' . _l('delete') . '"><i class="fa fa-remove"></i></a>';
                }

                $data[] = [
                    intval($row['userid']),
                    '<a href="' . admin_url('suppliers/supplier/' . $row['userid']) . '">' . $company_name . '</a>',
                    $primary_contact,
                    function_exists('_d') ? _d($row['datecreated']) : date('Y-m-d', strtotime($row['datecreated'])),
                    trim($options)
                ];
            }

            // Prepare response
            $response = [
                'draw' => $draw,
                'recordsTotal' => intval($total_records),
                'recordsFiltered' => intval($total_filtered),
                'data' => $data
            ];

            // Log response details
            error_log('Suppliers table response prepared: draw=' . $draw . ', total=' . $total_records . ', filtered=' . $total_filtered . ', rows=' . count($data));

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log('Handle table directly exception: ' . $e->getMessage());
            error_log('Exception trace: ' . $e->getTraceAsString());
            echo json_encode([
                'draw' => intval($this->input->get('draw')) ?: 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
        exit();
    }

    /* Test method to check table response */
    public function test_table_response()
    {
        ob_start();
        header('Content-Type: application/json;charset=utf-8');

        try {
            // Simulate DataTables request
            $response = [
                'draw' => 1,
                'recordsTotal' => 2,
                'recordsFiltered' => 2,
                'data' => [
                    [1, 'Test Company 1', 'John Doe', '2025-01-01', '<button>Edit</button>'],
                    [2, 'Test Company 2', 'Jane Smith', '2025-01-02', '<button>Edit</button>']
                ]
            ];

            ob_clean();
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }

    /* Add or edit supplier */
    public function supplier($id = '')
    {
        if (!is_admin() && function_exists('has_permission') && !has_permission('suppliers', '', 'view')) {
            access_denied('suppliers');
        }

        if ($this->input->post()) {
            if ($id == '') {
                if (!is_admin() && function_exists('has_permission') && !has_permission('suppliers', '', 'create')) {
                    access_denied('suppliers');
                }
                $id = $this->suppliers_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('supplier')));
                    redirect(admin_url('suppliers/supplier/' . $id));
                } else {
                    set_alert('danger', _l('something_went_wrong'));
                }
            } else {
                if (!is_admin() && function_exists('has_permission') && !has_permission('suppliers', '', 'edit')) {
                    access_denied('suppliers');
                }
                $success = $this->suppliers_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('supplier')));
                } else {
                    set_alert('danger', _l('something_went_wrong'));
                }
                redirect(admin_url('suppliers/supplier/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('supplier_lowercase'));
        } else {
            $supplier = $this->suppliers_model->get($id);
            if (!$supplier) {
                show_404();
            }
            $data['supplier'] = $supplier;
            $title = $supplier->company;
        }

        // Get customers for dropdown
        $data['customers'] = $this->suppliers_model->get_customers_dropdown();
        $data['title'] = $title;
        $this->load->view('suppliers/supplier', $data);
    }

    /* Add or edit contact */
    public function contact($supplier_id, $id = '')
    {
        if (!is_admin() && function_exists('has_permission') && !has_permission('suppliers', '', 'view')) {
            access_denied('suppliers');
        }

        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->suppliers_model->add_contact($this->input->post(), $supplier_id);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('supplier_contact')));
                }
            } else {
                $success = $this->suppliers_model->update_contact($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('supplier_contact')));
                }
            }
            redirect(admin_url('suppliers/supplier/' . $supplier_id));
        }

        if ($id == '') {
            $title = _l('add_new', _l('supplier_contact'));
        } else {
            $contact = $this->suppliers_model->get_contacts($supplier_id, $id);
            if (!$contact) {
                show_404();
            }
            $data['contact'] = $contact;
            $title = _l('edit', _l('supplier_contact'));
        }

        $data['supplier_id'] = $supplier_id;
        $data['title'] = $title;
        $this->load->view('suppliers/contact', $data);
    }

    /* Delete contact */
    public function delete_contact($supplier_id, $id)
    {
        if (!is_admin() && function_exists('has_permission') && !has_permission('suppliers', '', 'delete')) {
            access_denied('suppliers');
        }

        $this->suppliers_model->delete_contact($id);
        redirect(admin_url('suppliers/supplier/' . $supplier_id));
    }

    /* Add customer relation - AJAX */
    public function add_customer_relation($supplier_id)
    {
        if (!is_admin() && function_exists('has_permission') && !has_permission('suppliers', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                exit;
            }
            access_denied('suppliers');
        }

        $customer_id = $this->input->post('customer_id');
        if ($customer_id) {
            $success = $this->suppliers_model->add_supplier_customer_relation($supplier_id, $customer_id);
            if ($this->input->is_ajax_request()) {
                if ($success) {
                    // Get the new relation data
                    $customers = $this->suppliers_model->get_supplier_customers($supplier_id);
                    $new_customer = end($customers); // Get the last added customer
                    echo json_encode([
                        'success' => true,
                        'message' => _l('supplier_customer_relation_added'),
                        'customer' => $new_customer
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => _l('supplier_customer_relation_exists')
                    ]);
                }
                exit;
            } else {
                if ($success) {
                    set_alert('success', _l('supplier_customer_relation_added'));
                } else {
                    set_alert('warning', _l('supplier_customer_relation_exists'));
                }
            }
        }

        if (!$this->input->is_ajax_request()) {
            redirect(admin_url('suppliers/supplier/' . $supplier_id));
        }
    }

    /* Remove customer relation - AJAX */
    public function remove_customer_relation($supplier_id, $relation_id)
    {
        if (!is_admin() && function_exists('has_permission') && !has_permission('suppliers', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                exit;
            }
            access_denied('suppliers');
        }

        $success = $this->suppliers_model->remove_supplier_customer_relation($relation_id);

        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'success' => $success,
                'message' => $success ? _l('supplier_customer_relation_removed') : _l('something_went_wrong')
            ]);
            exit;
        } else {
            set_alert('success', _l('supplier_customer_relation_removed'));
            redirect(admin_url('suppliers/supplier/' . $supplier_id));
        }
    }

    /* Delete supplier */
    public function delete($id)
    {
        if (!is_admin() && function_exists('has_permission') && !has_permission('suppliers', '', 'delete')) {
            access_denied('suppliers');
        }

        if (!$id) {
            redirect(admin_url('suppliers'));
        }

        $response = $this->suppliers_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('supplier')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('supplier_lowercase')));
        }
        redirect(admin_url('suppliers'));
    }

    /* Debug method to test direct database query */
    public function debug_suppliers()
    {
        if (!is_admin()) {
            show_404();
        }

        echo "<h3>Debug Suppliers Data</h3>";

        // Test direct query
        $this->db->select('*');
        $this->db->from(db_prefix() . 'suppliers');
        $suppliers = $this->db->get()->result_array();

        echo "<h4>Direct Query Results (" . count($suppliers) . " suppliers found):</h4>";
        echo "<pre>";
        print_r($suppliers);
        echo "</pre>";

        // Test with join
        $this->db->select('s.*, TRIM(CONCAT(COALESCE(sc.firstname, ""), " ", COALESCE(sc.lastname, ""))) as primary_contact');
        $this->db->from(db_prefix() . 'suppliers s');
        $this->db->join(db_prefix() . 'supplier_contacts sc', 'sc.userid = s.userid AND sc.is_primary = 1', 'left');
        $suppliers_with_contacts = $this->db->get()->result_array();

        echo "<h4>Query with JOIN Results (" . count($suppliers_with_contacts) . " suppliers found):</h4>";
        echo "<pre>";
        print_r($suppliers_with_contacts);
        echo "</pre>";
    }
}
?>