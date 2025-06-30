<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suppliers_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get supplier/s
     * @param mixed $id supplier id
     * @param array $where perform where
     * @return mixed
     */
    public function get($id = '', $where = [])
    {
        $this->db->select('*,' . db_prefix() . 'suppliers.userid as userid,' . db_prefix() . 'countries.short_name as country_short_name,' . db_prefix() . 'countries.iso2 as country_code');
        $this->db->from(db_prefix() . 'suppliers');
        $this->db->join(db_prefix() . 'countries', db_prefix() . 'countries.country_id=' . db_prefix() . 'suppliers.country', 'left');
        $this->db->where($where);
        
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'suppliers.userid', $id);
            $supplier = $this->db->get()->row();
            
            if ($supplier) {
                $supplier->contacts = $this->get_contacts($id);
                $supplier->customers = $this->get_supplier_customers($id);
                
                if ($supplier->country != 0) {
                    $supplier->country_name = get_country_short_name($supplier->country);
                } else {
                    $supplier->country_name = $supplier->country_short_name;
                }
                
                $supplier->billing_country_name = '';
                $supplier->shipping_country_name = '';
                
                if ($supplier->billing_country != 0) {
                    $supplier->billing_country_name = get_country_short_name($supplier->billing_country);
                }
                if ($supplier->shipping_country != 0) {
                    $supplier->shipping_country_name = get_country_short_name($supplier->shipping_country);
                }
            }
            return $supplier;
        }
        
        $this->db->order_by('company', 'asc');
        return $this->db->get()->result_array();
    }

    /**
     * Add new supplier
     * @param array $data supplier data
     * @return mixed
     */
    public function add($data)
    {
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert(db_prefix() . 'suppliers', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Supplier Added [ID: ' . $insert_id . ', ' . $data['company'] . ']');
            hooks()->do_action('supplier_created', $insert_id);
            return $insert_id;
        }

        return false;
    }

    /**
     * Update supplier
     * @param array $data supplier data
     * @param mixed $id supplier id
     * @return boolean
     */
    public function update($data, $id)
    {
        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'suppliers', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Supplier Updated [ID: ' . $id . ']');
            hooks()->do_action('supplier_updated', $id);
            return true;
        }

        return false;
    }

    /**
     * Delete supplier
     * @param mixed $id supplier id
     * @return boolean
     */
    public function delete($id)
    {
        hooks()->do_action('before_supplier_deleted', $id);
        
        // Delete customer-supplier relations
        $this->db->where('supplier_id', $id);
        $this->db->delete(db_prefix() . 'supplier_customer_relations');
        
        // Delete contacts first
        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'supplier_contacts');
        
        // Delete supplier
        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'suppliers');

        if ($this->db->affected_rows() > 0) {
            log_activity('Supplier Deleted [' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get supplier contacts
     * @param mixed $supplier_id
     * @param mixed $id contact id
     * @return array
     */
    public function get_contacts($supplier_id = '', $id = '')
    {
        $this->db->where('userid', $supplier_id);
        
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'supplier_contacts')->row();
        }
        
        $this->db->order_by('is_primary', 'desc');
        return $this->db->get(db_prefix() . 'supplier_contacts')->result_array();
    }

    /**
     * Add new contact
     * @param array $data contact data
     * @param mixed $supplier_id
     * @return mixed
     */
    public function add_contact($data, $supplier_id)
    {
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['userid'] = $supplier_id;

        if (isset($data['is_primary']) && $data['is_primary'] == 1) {
            $this->db->where('userid', $supplier_id);
            $this->db->update(db_prefix() . 'supplier_contacts', ['is_primary' => 0]);
        }

        $this->db->insert(db_prefix() . 'supplier_contacts', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Supplier Contact Added [SupplierID: ' . $supplier_id . ', ContactID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update contact
     * @param array $data contact data
     * @param mixed $id contact id
     * @return boolean
     */
    public function update_contact($data, $id)
    {
        $contact = $this->get_contacts('', $id);

        if (isset($data['is_primary']) && $data['is_primary'] == 1) {
            $this->db->where('userid', $contact->userid);
            $this->db->update(db_prefix() . 'supplier_contacts', ['is_primary' => 0]);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'supplier_contacts', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Supplier Contact Updated [ContactID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete contact
     * @param mixed $id contact id
     * @return boolean
     */
    public function delete_contact($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'supplier_contacts');

        if ($this->db->affected_rows() > 0) {
            log_activity('Supplier Contact Deleted [ContactID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get customers related to supplier
     * @param mixed $supplier_id
     * @return array
     */
    public function get_supplier_customers($supplier_id)
    {
        $this->db->select('c.userid, c.company, scr.id as relation_id, scr.created_at as relation_date');
        $this->db->from(db_prefix() . 'supplier_customer_relations scr');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = scr.customer_id');
        $this->db->where('scr.supplier_id', $supplier_id);
        $this->db->order_by('c.company', 'asc');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get suppliers related to customer
     * @param mixed $customer_id
     * @return array
     */
    public function get_customer_suppliers($customer_id)
    {
        $this->db->select('s.userid, s.company, scr.id as relation_id, scr.created_at as relation_date');
        $this->db->from(db_prefix() . 'supplier_customer_relations scr');
        $this->db->join(db_prefix() . 'suppliers s', 's.userid = scr.supplier_id');
        $this->db->where('scr.customer_id', $customer_id);
        $this->db->order_by('s.company', 'asc');
        
        return $this->db->get()->result_array();
    }

    /**
     * Add supplier-customer relation
     * @param mixed $supplier_id
     * @param mixed $customer_id
     * @return boolean
     */
    public function add_supplier_customer_relation($supplier_id, $customer_id)
    {
        // Check if relation already exists
        $this->db->where('supplier_id', $supplier_id);
        $this->db->where('customer_id', $customer_id);
        $existing = $this->db->get(db_prefix() . 'supplier_customer_relations')->row();
        
        if ($existing) {
            return false; // Relation already exists
        }

        $data = [
            'supplier_id' => $supplier_id,
            'customer_id' => $customer_id,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => get_staff_user_id()
        ];

        $this->db->insert(db_prefix() . 'supplier_customer_relations', $data);
        
        if ($this->db->insert_id()) {
            log_activity('Supplier-Customer Relation Added [SupplierID: ' . $supplier_id . ', CustomerID: ' . $customer_id . ']');
            return true;
        }

        return false;
    }

    /**
     * Remove supplier-customer relation
     * @param mixed $relation_id
     * @return boolean
     */
    public function remove_supplier_customer_relation($relation_id)
    {
        $this->db->where('id', $relation_id);
        $this->db->delete(db_prefix() . 'supplier_customer_relations');

        if ($this->db->affected_rows() > 0) {
            log_activity('Supplier-Customer Relation Removed [RelationID: ' . $relation_id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get all customers for dropdown
     * @return array
     */
    public function get_customers_dropdown()
    {
        $this->db->select('userid, company');
        $this->db->from(db_prefix() . 'clients');
        $this->db->where('active', 1);
        $this->db->order_by('company', 'asc');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get all suppliers for dropdown
     * @return array
     */
    public function get_suppliers_dropdown()
    {
        $this->db->select('userid, company');
        $this->db->from(db_prefix() . 'suppliers');
        $this->db->where('active', 1);
        $this->db->order_by('company', 'asc');
        
        return $this->db->get()->result_array();
    }
}
?>