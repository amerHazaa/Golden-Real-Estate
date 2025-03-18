<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'class-properties-admin.php';
require_once plugin_dir_path(__FILE__) . 'class-towers-admin.php';
require_once plugin_dir_path(__FILE__) . '../includes/class-property-create.php'; // إضافة استدعاء ملف إنشاء النموذج

function gre_register_admin_menu() {
    $propertiesAdmin = new PropertiesAdmin();
    $towersAdmin = new TowersAdmin();
    $propertyCreate = new PropertyCreate(); // إنشاء كائن من فئة PropertyCreate

    add_menu_page(
        'إدارة العقارات', 
        'العقارات', 
        'manage_options', 
        'gre_properties', 
        array($propertiesAdmin, 'properties_page'), 
        'dashicons-admin-home', 
        2
    );

    add_submenu_page(
        'gre_properties', 
        'إضافة عقار', 
        'إضافة عقار', 
        'manage_options', 
        'gre_add_property', 
        array($propertiesAdmin, 'property_add_page')
    );

    add_submenu_page(
        'gre_properties', 
        'إدارة الأبراج', 
        'إدارة الأبراج', 
        'manage_options', 
        'gre_towers', 
        array($towersAdmin, 'tower_list_page')
    );

    add_submenu_page(
        'gre_properties', 
        'إضافة برج', 
        'إضافة برج', 
        'manage_options', 
        'gre_add_tower', 
        array($towersAdmin, 'tower_add_page')
    );

    add_submenu_page( // إضافة عنصر فرعي لإنشاء النماذج
        'gre_properties',
        'إضافة نموذج',
        'إضافة نموذج',
        'manage_options',
        'gre_add_model',
        array($propertyCreate, 'create_property_page')
    );
}
add_action('admin_menu', 'gre_register_admin_menu');

?>
