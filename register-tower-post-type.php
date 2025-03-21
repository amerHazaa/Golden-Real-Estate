<?php
function register_tower_post_type() {
    $labels = array(
        'name'               => 'الأبراج السكنية',
        'singular_name'      => 'برج سكني',
        'menu_name'          => 'الأبراج السكنية',
        'name_admin_bar'     => 'برج سكني',
        'add_new'            => 'إضافة برج جديد',
        'add_new_item'       => 'إضافة برج جديد',
        'new_item'           => 'برج جديد',
        'edit_item'          => 'تعديل البرج',
        'view_item'          => 'عرض البرج',
        'all_items'          => 'جميع الأبراج',
        'search_items'       => 'بحث في الأبراج',
        'parent_item_colon'  => 'برج رئيسي:',
        'not_found'          => 'لا يوجد أبراج',
        'not_found_in_trash' => 'لا يوجد أبراج في سلة المهملات'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'towers'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields')
    );

    register_post_type('tower', $args);
}

add_action('init', 'register_tower_post_type');
