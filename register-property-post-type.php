<?php
function register_property_post_type() {
    $labels = array(
        'name'               => 'الشقق',
        'singular_name'      => 'شقة',
        'menu_name'          => 'الشقق',
        'name_admin_bar'     => 'شقة',
        'add_new'            => 'إضافة شقة جديدة',
        'add_new_item'       => 'إضافة شقة جديدة',
        'new_item'           => 'شقة جديدة',
        'edit_item'          => 'تعديل الشقة',
        'view_item'          => 'عرض الشقة',
        'all_items'          => 'جميع الشقق',
        'search_items'       => 'بحث في الشقق',
        'parent_item_colon'  => 'شقة رئيسية:',
        'not_found'          => 'لا يوجد شقق',
        'not_found_in_trash' => 'لا يوجد شقق في سلة المهملات'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'properties'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields')
    );

    register_post_type('property', $args);
}

add_action('init', 'register_property_post_type');
