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
        global $wpdb;
        $table_name = $wpdb->prefix . 'gre_towers';

        // التحقق من خيار الفرز
        $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
        $allowed_sort_by = ['name', 'city', 'floors'];
        if (!in_array($sort_by, $allowed_sort_by)) {
            $sort_by = 'name';
        }

        // التحقق من خيار الفلترة
        $filter_column = isset($_GET['filter_column']) ? $_GET['filter_column'] : '';
        $filter_value = isset($_GET['filter_value']) ? $_GET['filter_value'] : '';
        $allowed_filter_columns = ['city', 'floors'];
        if (!in_array($filter_column, $allowed_filter_columns)) {
            $filter_column = '';
        }

        // استعلام جلب البيانات
        $query = "SELECT * FROM $table_name";
        $query_conditions = [];

        if ($filter_column && $filter_value) {
            $query_conditions[] = $wpdb->prepare("$filter_column LIKE %s", '%' . $filter_value . '%');
        }

        if ($query_conditions) {
            $query .= ' WHERE ' . implode(' AND ', $query_conditions);
        }

        $query .= " ORDER BY $sort_by";

        $towers = $wpdb->get_results($query);

        echo '<div class="wrap">';
        echo '<h1>إدارة الأبراج</h1>';
        
        // خيارات الفرز والفلترة
        echo '<form method="GET">';
        echo '<input type="hidden" name="page" value="gre_towers">';
        echo '<label>فرز حسب: </label>';
        echo '<select name="sort_by">';
        echo '<option value="name"' . ('name' === $sort_by ? ' selected' : '') . '>الاسم</option>';
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
            echo '<tr>';
            echo '<td>' . esc_html($tower->name) . '</td>';
            echo '<td>' . esc_html($tower->city) . '</td>';
            echo '<td>' . esc_html($tower->floors) . '</td>';
            echo '<td>' . esc_html($tower->properties_count) . '</td>';
            echo '<td>' . esc_html($tower->models) . '</td>';
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
        echo '<label for="tower_image">صورة البرج (رابط):</label>';
        echo '<input type="text" id="tower_image" name="tower_image" required><br>';
        echo '<label for="tower_short_name">الاسم المختصر:</label>';
        echo '<input type="text" id="tower_short_name" name="tower_short_name" required><br>';
        echo '<label for="tower_models">النماذج المتوفرة (مفصولة بفاصلة):</label>';
        echo '<input type="text" id="tower_models" name="tower_models" required><br>';
        echo '<input type="submit" value="إنشاء برج" class="button button-primary">';
        echo '</form>';
        echo '</div>';
    }

    public function create_tower() {
        global $wpdb;

        $tower_name = sanitize_text_field($_POST['tower_name']);
        $tower_city = sanitize_text_field($_POST['tower_city']);
        $tower_floors = intval($_POST['tower_floors']);
        $tower_properties_count = intval($_POST['tower_properties_count']);
        $tower_image = esc_url_raw($_POST['tower_image']);
        $tower_short_name = sanitize_text_field($_POST['tower_short_name']);
        $tower_models = sanitize_text_field($_POST['tower_models']);

        $table_name = $wpdb->prefix . 'gre_towers';
        $wpdb->insert($table_name, array(
            'name' => $tower_name,
            'city' => $tower_city,
            'floors' => $tower_floors,
            'properties_count' => $tower_properties_count,
            'image' => $tower_image,
            'short_name' => $tower_short_name,
            'models' => $tower_models
        ));

        wp_redirect(admin_url('admin.php?page=gre_towers'));
        exit;
    }

    public function update_tower() {
        // كود التحديث
    }

    public function delete_tower() {
        global $wpdb;

        $tower_id = intval($_GET['id']);
        $table_name = $wpdb->prefix . 'gre_towers';
        $wpdb->delete($table_name, array('ID' => $tower_id));

        wp_redirect(admin_url('admin.php?page=gre_towers'));
        exit;
    }
}

new TowersAdmin();