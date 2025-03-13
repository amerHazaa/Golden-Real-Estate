<?php
/**
 * Plugin Name: Golden Real Estate
 * Description: A plugin to manage real estate properties and towers.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: golden-real-estate
 */

if (!defined('ABSPATH')) {
    exit;
}

// استدعاء ملفات الإضافة
require_once plugin_dir_path(__FILE__) . 'includes/class-real-estate.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-property.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tower.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/hooks.php';

// استدعاء ملفات لوحة التحكم
if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin/class-admin-menu.php';
    require_once plugin_dir_path(__FILE__) . 'admin/class-properties-admin.php';
    require_once plugin_dir_path(__FILE__) . 'admin/class-towers-admin.php';
}

// استدعاء ملفات العرض العام
require_once plugin_dir_path(__FILE__) . 'public/class-public-display.php';

// تأكد من أن تفاصيل الشقة موروثة من النموذج الذي تتبعه
add_action('init', function() {
    global $wpdb;

    // إنشاء جدول الأبراج السكنية إذا لم يكن موجودًا
    $table_name = $wpdb->prefix . 'gre_towers';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        ID mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        location tinytext NOT NULL,
        PRIMARY KEY (ID)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // إنشاء جدول النماذج إذا لم يكن موجودًا
    $table_name = $wpdb->prefix . 'gre_models';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        ID mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        tower_id mediumint(9) NOT NULL,
        city tinytext NOT NULL,
        district tinytext NOT NULL,
        price float NOT NULL,
        features text NOT NULL,
        description text NOT NULL,
        images text NOT NULL,
        videos text NOT NULL,
        location tinytext NOT NULL,
        PRIMARY KEY (ID),
        FOREIGN KEY (tower_id) REFERENCES {$wpdb->prefix}gre_towers(ID) ON DELETE CASCADE
    ) $charset_collate;";
    dbDelta($sql);

    // تعديل جدول الشقق لإضافة العمود model_id إذا لم يكن موجودًا
    $table_name = $wpdb->prefix . 'gre_properties';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        ID mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        model_id mediumint(9) NOT NULL,
        tower_id mediumint(9) NOT NULL,
        property_code tinytext NOT NULL,
        floor mediumint(9) NOT NULL,
        status tinytext NOT NULL,
        PRIMARY KEY (ID),
        FOREIGN KEY (model_id) REFERENCES {$wpdb->prefix}gre_models(ID) ON DELETE CASCADE,
        FOREIGN KEY (tower_id) REFERENCES {$wpdb->prefix}gre_towers(ID) ON DELETE CASCADE
    ) $charset_collate;";
    dbDelta($sql);
});

// إضافة البيانات التجريبية عند تفعيل الإضافة
register_activation_hook(__FILE__, 'gre_insert_demo_data');

function gre_insert_demo_data() {
    global $wpdb;

    // تأكد من وجود بيانات تجريبية في جدول الأبراج
    $table_name = $wpdb->prefix . 'gre_towers';
    if ($wpdb->get_var("SELECT COUNT(*) FROM $table_name") == 0) {
        $wpdb->insert($table_name, ['name' => 'برج تجريبي', 'location' => 'الموقع التجريبي']);
    }

    // تأكد من وجود بيانات تجريبية في جدول النماذج
    $table_name = $wpdb->prefix . 'gre_models';
    if ($wpdb->get_var("SELECT COUNT(*) FROM $table_name") == 0) {
        $wpdb->insert($table_name, ['name' => 'نموذج تجريبي', 'tower_id' => 1, 'city' => 'المدينة التجريبية', 'district' => 'الحي التجريبي', 'price' => 100000, 'features' => 'ميزات تجريبية', 'description' => 'وصف تجريبي', 'images' => 'صورة1,صورة2', 'videos' => 'فيديو1,فيديو2', 'location' => 'الموقع التجريبي']);
    }

    // إضافة البيانات التجريبية للعقارات
    $real_estate = new RealEstate();
    $demo_properties = [
        [
            'name' => 'شقة تجريبية 1',
            'model_id' => 1,
            'tower_id' => 1,
            'property_code' => 'P001',
            'floor' => 1,
            'status' => 'متاحة'
        ],
        [
            'name' => 'شقة تجريبية 2',
            'model_id' => 1,
            'tower_id' => 1,
            'property_code' => 'P002',
            'floor' => 2,
            'status' => 'متاحة'
        ]
    ];

    foreach ($demo_properties as $property) {
        $real_estate->create_property($property);
    }
}

// كود الإزالة عند إلغاء تثبيت الإضافة
register_uninstall_hook(__FILE__, 'gre_uninstall_plugin');

function gre_uninstall_plugin() {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}gre_properties");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}gre_towers");
}
