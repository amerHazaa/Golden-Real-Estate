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