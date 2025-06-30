<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Create suppliers table
if (!$CI->db->table_exists(db_prefix() . 'suppliers')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'suppliers` (
        `userid` int(11) NOT NULL AUTO_INCREMENT,
        `company` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `vat` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `phonenumber` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `country` int(11) NOT NULL DEFAULT 0,
        `city` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `zip` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `state` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `website` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `datecreated` datetime NOT NULL,
        `active` int(11) NOT NULL DEFAULT 1,
        `billing_street` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `billing_city` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `billing_state` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `billing_zip` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `billing_country` int(11) DEFAULT 0,
        `shipping_street` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `shipping_city` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `shipping_state` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `shipping_zip` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `shipping_country` int(11) DEFAULT 0,
        `default_language` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `default_currency` int(11) NOT NULL DEFAULT 0,
        `addedfrom` int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY (`userid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
    
    // Insert sample data for testing
    $CI->db->query("INSERT INTO `" . db_prefix() . "suppliers` 
        (`company`, `vat`, `phonenumber`, `city`, `state`, `country`, `address`, `website`, `datecreated`, `active`, `addedfrom`) 
        VALUES 
        ('Tech Solutions Ltd', '123456789', '+1-555-0123', 'New York', 'NY', 1, '123 Tech Street', 'www.techsolutions.com', NOW(), 1, 1),
        ('Global Supplies Inc', '987654321', '+1-555-0124', 'Los Angeles', 'CA', 1, '456 Supply Ave', 'www.globalsupplies.com', NOW(), 1, 1),
        ('Premium Parts Co', '456789123', '+1-555-0125', 'Chicago', 'IL', 1, '789 Parts Blvd', 'www.premiumparts.com', NOW(), 0, 1)");
}

// Create supplier contacts table
if (!$CI->db->table_exists(db_prefix() . 'supplier_contacts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'supplier_contacts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `userid` int(11) NOT NULL,
        `is_primary` int(11) NOT NULL DEFAULT 0,
        `firstname` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `lastname` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `email` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `phonenumber` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `datecreated` datetime NOT NULL,
        `active` tinyint(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`),
        KEY `userid` (`userid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
    
    // Insert sample contacts
    $CI->db->query("INSERT INTO `" . db_prefix() . "supplier_contacts` 
        (`userid`, `firstname`, `lastname`, `email`, `phonenumber`, `title`, `is_primary`, `datecreated`) 
        VALUES 
        (1, 'John', 'Smith', 'john@techsolutions.com', '+1-555-0124', 'Sales Manager', 1, NOW()),
        (2, 'Jane', 'Doe', 'jane@globalsupplies.com', '+1-555-0125', 'Account Manager', 1, NOW()),
        (3, 'Bob', 'Johnson', 'bob@premiumparts.com', '+1-555-0126', 'Operations Manager', 1, NOW())");
}

// Create supplier-customer relations table
if (!$CI->db->table_exists(db_prefix() . 'supplier_customer_relations')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'supplier_customer_relations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `supplier_id` int(11) NOT NULL,
        `customer_id` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `created_by` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `supplier_id` (`supplier_id`),
        KEY `customer_id` (`customer_id`),
        UNIQUE KEY `unique_relation` (`supplier_id`, `customer_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
}

// Only add permissions if the permissions table exists
if ($CI->db->table_exists(db_prefix() . 'permissions')) {
    // Check if permission already exists
    $existing_permission = $CI->db->get_where(db_prefix() . 'permissions', array('shortname' => 'suppliers'))->row();

    if (!$existing_permission) {
        // Add permission
        $CI->db->insert(db_prefix() . 'permissions', array(
            'name' => 'Suppliers',
            'shortname' => 'suppliers'
        ));
        $permission_id = $CI->db->insert_id();

        // Add permission to admin role if role_permissions table exists
        if ($CI->db->table_exists(db_prefix() . 'role_permissions') && $permission_id) {
            $CI->db->insert(db_prefix() . 'role_permissions', array(
                'roleid' => 1,
                'permissionid' => $permission_id
            ));
        }
    }
}

echo 'Suppliers module installed successfully with sample data!';
?>