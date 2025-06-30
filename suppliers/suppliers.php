<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Suppliers
Description: Suppliers management module for Perfex CRM - identical to customers functionality  
Version: 1.0.0
Author: Custom Module
*/

define('SUPPLIERS_MODULE_NAME', 'suppliers');
define('SUPPLIERS_MODULE_UPLOAD_FOLDER', module_dir_path(SUPPLIERS_MODULE_NAME, 'uploads'));

// Hook registrations with error handling
try {
    hooks()->add_action('admin_init', 'suppliers_init_menu_items');
    hooks()->add_action('app_admin_head', 'suppliers_add_head_components');  
    hooks()->add_action('app_admin_footer', 'suppliers_add_footer_components');
    
    /**
     * Register activation module hook
     */
    register_activation_hook(SUPPLIERS_MODULE_NAME, 'suppliers_module_activation_hook');
    
    /**
     * Register language files, must be registered if the module is using languages
     */
    register_language_files(SUPPLIERS_MODULE_NAME, [SUPPLIERS_MODULE_NAME]);
    
} catch (Exception $e) {
    log_message('error', 'Suppliers module hook registration failed: ' . $e->getMessage());
}

function suppliers_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function suppliers_init_menu_items()
{
    try {
        $CI = &get_instance();
        
        // Check if user has permission before adding menu
        if (is_admin() || (function_exists('has_permission') && has_permission('suppliers', '', 'view'))) {
            $CI->app_menu->add_sidebar_menu_item('suppliers', [
                'name'     => _l('suppliers'),
                'href'     => admin_url('suppliers'),
                'icon'     => 'fa fa-truck',
                'position' => 6,
            ]);
        }
    } catch (Exception $e) {
        log_message('error', 'Suppliers menu initialization failed: ' . $e->getMessage());
    }
}

/**
 * Add additional head components
 */
function suppliers_add_head_components()
{
    try {
        $CI = &get_instance();
        $viewuri = $_SERVER['REQUEST_URI'];
        
        if (!(strpos($viewuri, '/admin/suppliers') === false)) {
            echo '<link href="' . module_dir_url(SUPPLIERS_MODULE_NAME, 'assets/css/suppliers.css') . '" rel="stylesheet" type="text/css" />';
        }
    } catch (Exception $e) {
        log_message('error', 'Suppliers head components failed: ' . $e->getMessage());
    }
}

/**
 * Add additional footer components  
 */
function suppliers_add_footer_components()
{
    try {
        $CI = &get_instance();
        $viewuri = $_SERVER['REQUEST_URI'];
        
        if (!(strpos($viewuri, '/admin/suppliers') === false)) {
            echo '<script src="' . module_dir_url(SUPPLIERS_MODULE_NAME, 'assets/js/suppliers.js') . '"></script>';
        }
    } catch (Exception $e) {
        log_message('error', 'Suppliers footer components failed: ' . $e->getMessage());
    }
}
?>