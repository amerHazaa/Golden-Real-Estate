<?php

if (!defined('ABSPATH')) {
    exit;
}

// استدعاء ملف إنشاء الشقق
require_once plugin_dir_path(__FILE__) . '../includes/class-property-create.php';

// ملف إدارة العقارات في لوحة التحكم

class PropertiesAdmin {
    public function __construct() {
        add_action('admin_post_delete_property', array($this, 'delete_property'));
        add_action('admin_post_delete_property_model', array($this, 'delete_property_model'));
        add_action('admin_post_edit_property', array($this, 'edit_property'));
    }

    public function properties_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gre_properties';

        // التحقق من خيار التجميع
        $group_by_model = isset($_GET['group_by_model']) ? true : false;

        // التحقق من خيار الفرز
        $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
        $allowed_sort_by = ['name', 'city', 'price'];
        if (!in_array($sort_by, $allowed_sort_by)) {
            $sort_by = 'name';
        }

        // التحقق من خيار الفلترة
        $filter_column = isset($_GET['filter_column']) ? $_GET['filter_column'] : '';
        $filter_value = isset($_GET['filter_value']) ? $_GET['filter_value'] : '';
        $allowed_filter_columns = ['city', 'district', 'price'];
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

        if ($group_by_model) {
            $properties = $wpdb->get_results("SELECT model_id, COUNT(*) as count FROM ($query) as grouped_table GROUP BY model_id");
        } else {
            $properties = $wpdb->get_results($query);
        }

        echo '<div class="wrap">';
        echo '<h1>إدارة العقارات</h1>';

        // خيارات التجميع، الفرز، والفلترة
        echo '<form method="GET">';
        echo '<input type="hidden" name="page" value="gre_properties">';
        echo '<label><input type="checkbox" name="group_by_model" ' . ($group_by_model ? 'checked' : '') . '> تجميع الشقق من نفس النموذج</label><br>';
        echo '<label>فرز حسب: </label>';
        echo '<select name="sort_by">';
        echo '<option value="name"' . ('name' === $sort_by ? ' selected' : '') . '>الاسم</option>';
        echo '<option value="city"' . ('city' === $sort_by ? ' selected' : '') . '>المدينة</option>';
        echo '<option value="price"' . ('price' === $sort_by ? ' selected' : '') . '>السعر</option>';
        echo '</select><br>';
        echo '<label>فلترة حسب: </label>';
        echo '<select name="filter_column">';
        echo '<option value="city"' . ('city' === $filter_column ? ' selected' : '') . '>المدينة</option>';
        echo '<option value="district"' . ('district' === $filter_column ? ' selected' : '') . '>الحي</option>';
        echo '<option value="price"' . ('price' === $filter_column ? ' selected' : '') . '>السعر</option>';
        echo '</select>';
        echo '<input type="text" name="filter_value" value="' . esc_attr($filter_value) . '"><br>';
        echo '<input type="submit" value="تطبيق" class="button button-primary">';
        echo '</form>';

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>الاسم</th><th>المدينة</th><th>الحي</th><th>السعر</th><th>البرج</th><th>رمز الشقة</th>' . ($group_by_model ? '<th>عدد الشقق</th>' : '<th>الدور</th>') . '<th>الإجراءات</th></tr></thead>';
        echo '<tbody>';
        foreach ($properties as $property) {
            $tower = $wpdb->get_row($wpdb->prepare("SELECT name FROM {$wpdb->prefix}gre_towers WHERE ID = %d", isset($property->tower_id) ? $property->tower_id : 0));
            $model = $wpdb->get_row($wpdb->prepare("SELECT name FROM {$wpdb->prefix}gre_models WHERE ID = %d", isset($property->model_id) ? $property->model_id : 0));
            echo '<tr>';
            echo '<td><a href="' . admin_url('admin.php?page=property_details&id=' . (isset($property->ID) ? $property->ID : 0)) . '">' . esc_html(isset($model->name) ? $model->name : 'غير متوفر') . '</a></td>';
            echo '<td>' . esc_html(isset($property->city) ? $property->city : 'غير متوفر') . '</td>';
            echo '<td>' . esc_html(isset($property->district) ? $property->district : 'غير متوفر') . '</td>';
            echo '<td>' . esc_html(isset($property->price) ? $property->price : 'غير متوفر') . '</td>';
            echo '<td>' . esc_html(isset($tower->name) ? $tower->name : 'غير متوفر') . '</td>';
            echo '<td>' . esc_html(isset($property->property_code) ? $property->property_code : 'غير متوفر') . '</td>';
            if ($group_by_model) {
                echo '<td>' . esc_html(isset($property->count) ? $property->count : 'غير متوفر') . '</td>';
            } else {
                echo '<td>' . esc_html(isset($property->floor) ? $property->floor : 'غير متوفر') . '</td>';
            }
            echo '<td><a href="' . admin_url('admin.php?page=edit_property&id=' . (isset($property->ID) ? $property->ID : 0)) . '">تعديل</a> | <a href="' . wp_nonce_url(admin_url('admin-post.php?action=delete_property&id=' . (isset($property->ID) ? $property->ID : 0)), 'delete_property_' . (isset($property->ID) ? $property->ID : 0)) . '">حذف</a></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

    public function property_add_page() {
        echo '<div class="wrap"><h1>إضافة عقار جديد</h1>';
        echo '<form action="' . admin_url('admin-post.php') . '" method="POST">';
        echo '<input type="hidden" name="action" value="create_property">';
        echo '<label for="property_name">اسم العقار:</label>';
        echo '<input type="text" id="property_name" name="property_name" required><br>';
        echo '<label for="property_model">النموذج:</label>';
        echo '<select id="property_model" name="property_model" required>';
        global $wpdb;
        $models = $wpdb->get_results("SELECT ID, name FROM {$wpdb->prefix}gre_models");
        foreach ($models as $model) {
            echo '<option value="' . esc_attr($model->ID) . '">' . esc_html($model->name) . '</option>';
        }
        echo '</select><br>';
        echo '<label for="property_tower">البرج:</label>';
        echo '<select id="property_tower" name="property_tower" required>';
        $towers = $wpdb->get_results("SELECT ID, name FROM {$wpdb->prefix}gre_towers");
        foreach ($towers as $tower) {
            echo '<option value="' . esc_attr($tower->ID) . '">' . esc_html($tower->name) . '</option>';
        }
        echo '</select><br>';
        echo '<label for="property_code">رمز الشقة:</label>';
        echo '<input type="text" id="property_code" name="property_code" required><br>';
        echo '<label for="property_floor">الدور:</label>';
        echo '<input type="number" id="property_floor" name="property_floor" required><br>';
        echo '<label for="property_status">الحالة:</label>';
        echo '<select id="property_status" name="property_status" required>';
        echo '<option value="غير جاهزة">غير جاهزة</option>';
        echo '<option value="قيد التجهيز">قيد التجهيز</option>';
        echo '<option value="للتشطيب">للتشطيب</option>';
        echo '<option value="جاهزة">جاهزة</option>';
        echo '<option value="مباعة">مباعة</option>';
        echo '</select><br>';
        echo '<input type="submit" value="إضافة عقار" class="button button-primary">';
        echo '</form>';
        echo '</div>';
    }

    public function property_details_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        global $wpdb;
        $property_id = intval($_GET['id']);
        $table_name = $wpdb->prefix . 'gre_properties';
        $property = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE ID = %d", $property_id));

        if (!$property) {
            echo '<div class="wrap"><h1>الشقة غير موجودة</h1></div>';
            return;
        }

        // جلب تفاصيل النموذج المرتبطة بالشقة
        $model = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}gre_models WHERE ID = %d", $property->model_id));

        echo '<div class="wrap">';
        echo '<h1>تفاصيل الشقة</h1>';
        echo '<p>الاسم: ' . esc_html($property->name) . '</p>';
        echo '<p>النموذج: ' . esc_html(isset($model->name) ? $model->name : 'غير متوفر') . '</p>';
        echo '<p>المدينة: ' . esc_html(isset($model->city) ? $model->city : 'غير متوفر') . '</p>';
        echo '<p>الحي: ' . esc_html(isset($model->district) ? $model->district : 'غير متوفر') . '</p>';
        echo '<p>السعر: ' . esc_html(isset($model->price) ? $model->price : 'غير متوفر') . '</p>';
        echo '<p>المميزات: ' . esc_html($model->features) . '</p>';
        echo '<p>الوصف: ' . esc_html($model->description) . '</p>';
        echo '<p>الصور: ' . esc_html($model->images) . '</p>';
        echo '<p>الفيديوهات: ' . esc_html($model->videos) . '</p>';
        echo '<p>الموقع: ' . esc_html($model->location) . '</p>';
        echo '<p>البرج: ' . esc_html(isset($property->tower_id) ? $property->tower_id : 'غير متوفر') . '</p>';
        echo '<p>رمز الشقة: ' . esc_html(isset($property->property_code) ? $property->property_code : 'غير متوفر') . '</p>';
        echo '<p>الدور: ' . esc_html($property->floor) . '</p>';
        echo '<p>الحالة: ' . esc_html($property->status) . '</p>';
        echo '</div>';
    }

    public function delete_property_confirmation() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        $property_id = intval($_GET['id']);
        echo '<div class="wrap">';
        echo '<h1>تأكيد الحذف</h1>';
        echo '<p>هل تريد حذف هذه الشقة فقط أم حذف النموذج بالكامل؟</p>';
        echo '<a href="' . wp_nonce_url(admin_url('admin-post.php?action=delete_property&id=' . $property_id), 'delete_property_' . $property_id) . '" class="button button-primary">حذف الشقة</a>';
        echo '<a href="' . wp_nonce_url(admin_url('admin-post.php?action=delete_property_model&id=' . $property_id), 'delete_property_model_' . $property_id) . '" class="button button-primary">حذف النموذج</a>';
        echo '</div>';
    }

    public function delete_property() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        global $wpdb;

        $property_id = intval($_GET['id']);
        $table_name = $wpdb->prefix . 'gre_properties';
        $wpdb->delete($table_name, array('ID' => $property_id));

        wp_redirect(admin_url('admin.php?page=gre_properties'));
        exit;
    }

    public function delete_property_model() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        global $wpdb;

        $property_id = intval($_GET['id']);
        $property = $wpdb->get_row($wpdb->prepare("SELECT model_id FROM {$wpdb->prefix}gre_properties WHERE ID = %d", $property_id));
        if ($property) {
            $wpdb->delete($wpdb->prefix . 'gre_properties', array('model_id' => $property->model_id));
            $wpdb->delete($wpdb->prefix . 'gre_models', array('ID' => $property->model_id));
        }

        wp_redirect(admin_url('admin.php?page=gre_properties'));
        exit;
    }

    public function edit_property() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        global $wpdb;
        $property_id = intval($_GET['id']);
        $property = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}gre_properties WHERE ID = %d", $property_id));

        if (!$property) {
            echo '<div class="wrap"><h1>الشقة غير موجودة</h1></div>';
            return;
        }

        // جلب تفاصيل النموذج المرتبطة بالشقة
        $model = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}gre_models WHERE ID = %d", $property->model_id));

        include_once plugin_dir_path(__FILE__) . '../includes/class-property-edit.php';

        echo '<div class="wrap">';
        echo '<h1>تعديل الشقة</h1>';
        $property_edit = new PropertyEdit();
        $property_edit->edit_property_form($property, $model);
        echo '</div>';
    }
}

new PropertiesAdmin();
