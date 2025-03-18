<?php
function register_model_post_type() {
    $labels = array(
        'name'               => 'النماذج',
        'singular_name'      => 'نموذج',
        'menu_name'          => 'النماذج',
        'name_admin_bar'     => 'نموذج',
        'add_new'            => 'إضافة نموذج جديد',
        'add_new_item'       => 'إضافة نموذج جديد',
        'new_item'           => 'نموذج جديد',
        'edit_item'          => 'تعديل النموذج',
        'view_item'          => 'عرض النموذج',
        'all_items'          => 'جميع النماذج',
        'search_items'       => 'بحث في النماذج',
        'parent_item_colon'  => 'نموذج رئيسي:',
        'not_found'          => 'لا يوجد نماذج',
        'not_found_in_trash' => 'لا يوجد نماذج في سلة المهملات'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'models'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail')
    );

    register_post_type('model', $args);
}

add_action('init', 'register_model_post_type');