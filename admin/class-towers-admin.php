<?php
if (!defined('ABSPATH')) {
    exit;
}

class TowersAdmin {
    public function __construct() {
        add_action('admin_post_create_tower', array($this, 'create_tower'));
        add_action('admin_post_update_tower', array($this, 'update_tower'));
        add_action('admin_post_delete_tower', array($this, 'delete_tower'));
    }

    public function tower_list_page() {
        // التحقق من خيار الفرز
        $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'title';
        $allowed_sort_by = ['title', 'city', 'floors'];
        if (!in_array($sort_by, $allowed_sort_by)) {
            $sort_by = 'title';
        }

        // التحقق من خيار الفلترة
        $filter_column = isset($_GET['filter_column']) ? $_GET['filter_column'] : '';
        $filter_value = isset($_GET['filter_value']) ? $_GET['filter_value'] : '';
        $allowed_filter_columns = ['city', 'floors'];
        if (!in_array($filter_column, $allowed_filter_columns)) {
            $filter_column = '';
        }

        // استعلام جلب البيانات
        $query_args = [
            'post_type' => 'tower',
            'orderby' => $sort_by,
            'order' => 'ASC',
            'posts_per_page' => -1
        ];

        if ($filter_column && $filter_value) {
            $query_args['meta_query'] = [
                [
                    'key' => '_' . $filter_column,
                    'value' => $filter_value,
                    'compare' => 'LIKE'
                ]
            ];
        }

        $towers = get_posts($query_args);

        echo '<div class="wrap">';
        echo '<h1>إدارة الأبراج</h1>';
        
        // خيارات الفرز والفلترة
        echo '<form method="GET">';
        echo '<input type="hidden" name="page" value="gre_towers">';
        echo '<label>فرز حسب: </label>';
        echo '<select name="sort_by">';
        echo '<option value="title"' . ('title' === $sort_by ? ' selected' : '') . '>الاسم</option>';
        echo '<option value="city"' . ('city' === $sort_by ? ' selected' : '') . '>المدينة</option>';
        echo '<option value="floors"' . ('floors' === $sort_by ? ' selected' : '') . '>عدد الطوابق</option>';
        echo '</select><br>';
        echo '<label>فلترة حسب: </label>';
        echo '<select name="filter_column">';
        echo '<option value="city"' . ('city' === $filter_column ? ' selected' : '') . '>المدينة</option>';
        echo '<option value="floors"' . ('floors' === $filter_column ? ' selected' : '') . '>عدد الطوابق</option>';
        echo '</select>';
        echo '<input type="text" name="filter_value" value="' . esc_attr($filter_value) . '"><br>';
        echo '<input type="submit" value="تطبيق" class="button button-primary">';
        echo '</form>';

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>الاسم</th><th>المدينة</th><th>عدد الطوابق</th><th>عدد العقارات</th><th>النماذج المتوفرة</th><th>الإجراءات</th></tr></thead>';
        echo '<tbody>';
        foreach ($towers as $tower) {
            $models = get_posts(['post_type' => 'model', 'meta_key' => '_tower_id', 'meta_value' => $tower->ID, 'posts_per_page' => -1]);
            $model_names = implode(', ', wp_list_pluck($models, 'post_title'));
            echo '<tr>';
            echo '<td>' . esc_html($tower->post_title) . '</td>';
            echo '<td>' . esc_html(get_post_meta($tower->ID, '_city', true)) . '</td>';
            echo '<td>' . esc_html(get_post_meta($tower->ID, '_floors', true)) . '</td>';
            echo '<td>' . esc_html(get_post_meta($tower->ID, '_properties_count', true)) . '</td>';
            echo '<td>' . esc_html($model_names) . '</td>';
            echo '<td><a href="' . admin_url('admin.php?page=edit_tower&id=' . $tower->ID) . '">تعديل</a> | <a href="' . admin_url('admin-post.php?action=delete_tower&id=' . $tower->ID) . '">حذف</a></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

    public function tower_add_page() {
        echo '<div class="wrap"><h1>إضافة برج جديد</h1>';
        echo '<form action="' . admin_url('admin-post.php') . '" method="POST">';
        echo '<input type="hidden" name="action" value="create_tower">';
        echo '<label for="tower_name">اسم البرج:</label>';
        echo '<input type="text" id="tower_name" name="tower_name" required><br>';
        echo '<label for="tower_city">المدينة:</label>';
        echo '<input type="text" id="tower_city" name="tower_city" required><br>';
        echo '<label for="tower_floors">عدد الطوابق:</label>';
        echo '<input type="number" id="tower_floors" name="tower_floors" required><br>';
        echo '<label for="tower_properties_count">عدد العقارات:</label>';
        echo '<input type="number" id="tower_properties_count" name="tower_properties_count" required><br>';
        echo '<input type="submit" value="إنشاء برج" class="button button-primary">';
        echo '</form>';
        echo '</div>';
    }

    public function create_tower() {
        $post_data = [
            'post_title'    => sanitize_text_field($_POST['tower_name']),
            'post_type'     => 'tower',
            'post_status'   => 'publish'
        ];

        $tower_id = wp_insert_post($post_data);

        if (!is_wp_error($tower_id)) {
            update_post_meta($tower_id, '_city', sanitize_text_field($_POST['tower_city']));
            update_post_meta($tower_id, '_floors', intval($_POST['tower_floors']));
            update_post_meta($tower_id, '_properties_count', intval($_POST['tower_properties_count']));
        }

        wp_redirect(admin_url('admin.php?page=gre_towers'));
        exit;
    }

    public function update_tower() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        $tower_id = intval($_GET['id']);
        $tower = get_post($tower_id);

        if (!$tower || $tower->post_type !== 'tower') {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        echo '<div class="wrap">';
        echo '<h1>تعديل برج: ' . esc_html($tower->post_title) . '</h1>';
        echo '<form action="' . admin_url('admin-post.php') . '" method="POST">';
        echo '<input type="hidden" name="action" value="save_tower">';
        echo '<input type="hidden" name="tower_id" value="' . esc_attr($tower_id) . '">';
        echo '<label for="tower_name">اسم البرج:</label>';
        echo '<input type="text" id="tower_name" name="tower_name" value="' . esc_attr($tower->post_title) . '" required><br>';
        echo '<label for="tower_city">المدينة:</label>';
        echo '<input type="text" id="tower_city" name="tower_city" value="' . esc_attr(get_post_meta($tower_id, '_city', true)) . '" required><br>';
        echo '<label for="tower_floors">عدد الطوابق:</label>';
        echo '<input type="number" id="tower_floors" name="tower_floors" value="' . esc_attr(get_post_meta($tower_id, '_floors', true)) . '" required><br>';
        echo '<label for="tower_properties_count">عدد العقارات:</label>';
        echo '<input type="number" id="tower_properties_count" name="tower_properties_count" value="' . esc_attr(get_post_meta($tower_id, '_properties_count', true)) . '" required><br>';
        echo '<input type="submit" value="تحديث البرج" class="button button-primary">';
        echo '</form>';
        echo '</div>';
    }

    public function save_tower() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        $tower_id = intval($_POST['tower_id']);
        $post_data = [
            'ID'           => $tower_id,
            'post_title'   => sanitize_text_field($_POST['tower_name']),
        ];

        wp_update_post($post_data);

        update_post_meta($tower_id, '_city', sanitize_text_field($_POST['tower_city']));
        update_post_meta($tower_id, '_floors', intval($_POST['tower_floors']));
        update_post_meta($tower_id, '_properties_count', intval($_POST['tower_properties_count']));

        wp_redirect(admin_url('admin.php?page=gre_towers'));
        exit;
    }

    public function delete_tower() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        $tower_id = intval($_GET['id']);
        wp_delete_post($tower_id, true);

        wp_redirect(admin_url('admin.php?page=gre_towers'));
        exit;
    }
}

new TowersAdmin();
