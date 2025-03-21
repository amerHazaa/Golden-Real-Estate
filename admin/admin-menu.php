<?php
class GRE_AdminMenu {
    public function __construct() {
        add_action('admin_menu', array($this, 'register_menu'));
    }

    public function register_menu() {
        add_menu_page(
            'إدارة العقارات', 
            'العقارات', 
            'manage_options', 
            'gre_properties', 
            array($this, 'properties_page'), 
            'dashicons-admin-home', 
            6
        );

        add_submenu_page(
            'gre_properties',
            'إضافة شقة جديدة',
            'إضافة شقة',
            'manage_options',
            'add_property',
            array($this, 'add_property_page')
        );

        add_submenu_page(
            'gre_properties',
            'إدارة الأبراج السكنية',
            'الأبراج السكنية',
            'manage_options',
            'manage_towers',
            array($this, 'manage_towers_page')
        );
    }

    public function properties_page() {
        include_once GOLDEN_REAL_ESTATE_DIR . 'admin/properties-list.php';
    }

    public function add_property_page() {
        include_once GOLDEN_REAL_ESTATE_DIR . 'admin/add-property.php';
    }

    public function manage_towers_page() {
        include_once GOLDEN_REAL_ESTATE_DIR . 'admin/manage-towers.php';
    }
}

new GRE_AdminMenu();