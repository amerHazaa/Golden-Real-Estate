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
        $properties = $this->get_all_properties();

        // التحقق من خيار التجميع
        $group_by_model = isset($_GET['group_by_model']) ? true : false;

        // التحقق من خيار الفرز
        $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'title';
        $allowed_sort_by = ['title', 'city', 'price'];
        if (!in_array($sort_by, $allowed_sort_by)) {
            $sort_by = 'title';
        }

        // التحقق من خيار الفلترة
        $filter_column = isset($_GET['filter_column']) ? $_GET['filter_column'] : '';
        $filter_value = isset($_GET['filter_value']) ? $_GET['filter_value'] : '';
        $allowed_filter_columns = ['city', 'district', 'price'];
        if (!in_array($filter_column, $allowed_filter_columns)) {
            $filter_column = '';
        }

        // استعلام جلب البيانات
        $query_args = [
            'post_type' => 'property',
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

        $properties = get_posts($query_args);

        echo '<div class="wrap">';
        echo '<h1>إدارة العقارات</h1>';

        // خيارات التجميع، الفرز، والفلترة
        echo '<form method="GET">';
        echo '<input type="hidden" name="page" value="gre_properties">';
        echo '<label><input type="checkbox" name="group_by_model" ' . ($group_by_model ? 'checked' : '') . '> تجميع الشقق من نفس النموذج</label><br>';
        echo '<label>فرز حسب: </label>';
        echo '<select name="sort_by">';
        echo '<option value="title"' . ('title' === $sort_by ? ' selected' : '') . '>الاسم</option>';
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
        echo '<thead><tr><th>الاسم</th><th>المدينة</th><th>الحي</th><th>السعر</th><th>البرج</th><th>رمز الشقة</th>' . ($group_by_model ? '<th>عدد الشقق</th>' : '<th>الدور</th>') . '<th>إجراءات</th></tr></thead>';
        echo '<tbody>';
        foreach ($properties as $property) {
            $property_id = $property->ID;
            $tower_id = get_post_meta($property_id, '_tower_id', true);
            $model_id = get_post_meta($property_id, '_model_id', true);

            $tower = get_post($tower_id);
            $model = get_post($model_id);

            echo '<tr>';
            echo '<td><a href="' . admin_url('admin.php?page=property_details&id=' . $property_id) . '">' . esc_html($property->post_title) . '</a></td>';
            echo '<td>' . esc_html(get_post_meta($property_id, '_city', true)) . '</td>';
            echo '<td>' . esc_html(get_post_meta($property_id, '_district', true)) . '</td>';
            echo '<td>' . esc_html(get_post_meta($property_id, '_price', true)) . ' $</td>';
            echo '<td>' . esc_html($tower ? $tower->post_title : 'غير متوفر') . '</td>';
            echo '<td>' . esc_html(get_post_meta($property_id, '_property_code', true)) . '</td>';
            if ($group_by_model) {
                echo '<td>' . esc_html($this->get_property_count_by_model($model_id)) . '</td>';
            } else {
                echo '<td>' . esc_html(get_post_meta($property_id, '_floor', true)) . '</td>';
            }
            echo '<td><a href="' . admin_url('admin.php?page=edit_property&id=' . $property_id) . '">تعديل</a> | <a href="' . wp_nonce_url(admin_url('admin-post.php?action=delete_property&id=' . $property_id), 'delete_property_' . $property_id) . '" onclick="return confirm(\'هل أنت متأكد من حذف هذه الشقة؟\');">حذف</a></td>';
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
        $models = get_posts(['post_type' => 'model', 'posts_per_page' => -1]);
        foreach ($models as $model) {
            echo '<option value="' . esc_attr($model->ID) . '">' . esc_html($model->post_title) . '</option>';
        }
        echo '</select><br>';
        echo '<label for="property_tower">البرج:</label>';
        echo '<select id="property_tower" name="property_tower" required>';
        $towers = get_posts(['post_type' => 'tower', 'posts_per_page' => -1]);
        foreach ($towers as $tower) {
            echo '<option value="' . esc_attr($tower->ID) . '">' . esc_html($tower->post_title) . '</option>';
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

        $property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $property = get_post($property_id);

        if (!$property || $property->post_type !== 'property') {
            echo '<div class="wrap"><h1>الشقة غير موجودة</h1></div>';
            return;
        }

        // جلب تفاصيل النموذج المرتبطة بالشقة
        $model_id = get_post_meta($property_id, '_model_id', true);
        $model = get_post($model_id);

        echo '<div class="wrap">';
        echo '<h1>تفاصيل الشقة</h1>';
        echo '<p>الاسم: ' . esc_html($property->post_title) . '</p>';
        echo '<p>النموذج: ' . esc_html($model ? $model->post_title : 'غير متوفر') . '</p>';
        echo '<p>المدينة: ' . esc_html(get_post_meta($model->ID, '_city', true)) . '</p>';
        echo '<p>الحي: ' . esc_html(get_post_meta($model->ID, '_district', true)) . '</p>';
        echo '<p>السعر: ' . esc_html(get_post_meta($model->ID, '_price', true)) . ' $</p>';
        echo '<p>المميزات: ' . esc_html(get_post_meta($model->ID, '_features', true)) . '</p>';
        echo '<p>الوصف: ' . esc_html($model->post_content) . '</p>';
        echo '<p>الصور: ' . esc_html(get_post_meta($property_id, '_images', true)) . '</p>';
        echo '<p>الفيديوهات: ' . esc_html(get_post_meta($property_id, '_videos', true)) . '</p>';
        echo '<p>الموقع: ' . esc_html(get_post_meta($model->ID, '_location', true)) . '</p>';
        echo '<p>البرج: ' . esc_html(get_post_meta($property_id, '_tower_id', true)) . '</p>';
        echo '<p>رمز الشقة: ' . esc_html(get_post_meta($property_id, '_property_code', true)) . '</p>';
        echo '<p>الدور: ' . esc_html(get_post_meta($property_id, '_floor', true)) . '</p>';
        echo '<p>الحالة: ' . esc_html(get_post_meta($property_id, '_status', true)) . '</p>';
        echo '</div>';
    }

    public function delete_property_confirmation() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        $property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        echo '<div class="wrap">';
        echo '<h1>تأكيد الحذف</h1>';
        echo '<p>هل تريد حذف هذه الشقة فقط أم حذف النموذج بالكامل؟</p>';
        echo '<a href="' . wp_nonce_url(admin_url('admin-post.php?action=delete_property&id=' . $property_id), 'delete_property_' . $property_id) . '" class="button button-primary">حذف الشقة فقط</a>';
        echo '<a href="' . wp_nonce_url(admin_url('admin-post.php?action=delete_property_model&id=' . $property_id), 'delete_property_model_' . $property_id) . '" class="button button-primary">حذف النموذج بالكامل</a>';
        echo '</div>';
    }

    public function delete_property() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        $property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        wp_delete_post($property_id, true);

        wp_redirect(admin_url('admin.php?page=gre_properties'));
        exit;
    }

    public function delete_property_model() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        $property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $model_id = get_post_meta($property_id, '_model_id', true);
        if ($model_id) {
            $properties = get_posts(['post_type' => 'property', 'meta_key' => '_model_id', 'meta_value' => $model_id, 'posts_per_page' => -1]);
            foreach ($properties as $property) {
                wp_delete_post($property->ID, true);
            }
            wp_delete_post($model_id, true);
        }

        wp_redirect(admin_url('admin.php?page=gre_properties'));
        exit;
    }

    public function edit_property() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        $property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $property = get_post($property_id);

        if (!$property || $property->post_type !== 'property') {
            echo '<div class="wrap"><h1>الشقة غير موجودة</h1></div>';
            return;
        }

        // جلب تفاصيل النموذج المرتبطة بالشقة
        $model_id = get_post_meta($property_id, '_model_id', true);
        $model = get_post($model_id);

        include_once plugin_dir_path(__FILE__) . '../includes/class-property-edit.php';

        echo '<div class="wrap">';
        echo '<h1>تعديل الشقة</h1>';
        $property_edit = new PropertyEdit();
        $property_edit->edit_property_form($property, $model);
        echo '</div>';
    }

    private function get_all_properties() {
        $args = array(
            'post_type' => 'property',
            'posts_per_page' => -1
        );
        return get_posts($args);
    }

    private function get_property_count_by_model($model_id) {
        $args = array(
            'post_type' => 'property',
            'meta_key' => '_model_id',
            'meta_value' => $model_id,
            'posts_per_page' => -1
        );
        $properties = get_posts($args);
        return count($properties);
    }
}

new PropertiesAdmin();
