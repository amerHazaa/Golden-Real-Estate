<?php
// ملف إنشاء الشقق

class PropertyCreate {
    public static function create_property_page() {
        global $wpdb;
        $towers = $wpdb->get_results("SELECT ID, name FROM {$wpdb->prefix}gre_towers");
        $models = $wpdb->get_results("SELECT ID, name FROM {$wpdb->prefix}gre_models");

        echo '<div class="wrap">';
        echo '<h1>إنشاء نموذج شقة جديد</h1>';
        echo '<form action="' . admin_url('admin-post.php') . '" method="POST">';
        echo '<input type="hidden" name="action" value="create_property">';
        echo '<label for="property_name">اسم الشقة:</label>';
        echo '<input type="text" id="property_name" name="property_name" required><br>';
        echo '<label for="property_model">النموذج:</label>';
        echo '<select id="property_model" name="property_model" required>';
        foreach ($models as $model) {
            echo '<option value="' . esc_attr($model->ID) . '">' . esc_html($model->name) . '</option>';
        }
        echo '</select><br>';
        echo '<label for="property_city">المدينة:</label>';
        echo '<input type="text" id="property_city" name="property_city" required><br>';
        echo '<label for="property_district">الحي:</label>';
        echo '<input type="text" id="property_district" name="property_district" required><br>';
        echo '<label for="property_price">السعر:</label>';
        echo '<input type="number" id="property_price" name="property_price" required><br>';
        echo '<label for="property_features">المميزات:</label>';
        echo '<textarea id="property_features" name="property_features" required></textarea><br>';
        echo '<label for="property_description">الوصف:</label>';
        echo '<textarea id="property_description" name="property_description" required></textarea><br>';
        echo '<label for="property_images">الصور (روابط مفصولة بفاصلة):</label>';
        echo '<textarea id="property_images" name="property_images" required></textarea><br>';
        echo '<label for="property_videos">الفيديوهات (روابط مفصولة بفاصلة):</label>';
        echo '<textarea id="property_videos" name="property_videos" required></textarea><br>';
        echo '<label for="property_location">الموقع (إحداثيات):</label>';
        echo '<input type="text" id="property_location" name="property_location" required><br>';
        echo '<label for="tower_id">البرج:</label>';
        echo '<select id="tower_id" name="tower_id" required>';
        foreach ($towers as $tower) {
            echo '<option value="' . esc_attr($tower->ID) . '">' . esc_html($tower->name) . '</option>';
        }
        echo '</select><br>';
        echo '<label for="property_code">رمز الشقة:</label>';
        echo '<input type="text" id="property_code" name="property_code" required><br>';
        echo '<label for="property_floor">الدور:</label>';
        echo '<input type="number" id="property_floor" name="property_floor" required><br>';
        echo '<input type="submit" value="إنشاء شقة" class="button button-primary">';
        echo '</form>';
        echo '</div>';
    }

    public static function create_property() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        global $wpdb;

        $property_name = sanitize_text_field($_POST['property_name']);
        $property_model = intval($_POST['property_model']);
        $property_city = sanitize_text_field($_POST['property_city']);
        $property_district = sanitize_text_field($_POST['property_district']);
        $property_price = floatval($_POST['property_price']);
        $property_features = sanitize_textarea_field($_POST['property_features']);
        $property_description = sanitize_textarea_field($_POST['property_description']);
        $property_images = sanitize_textarea_field($_POST['property_images']);
        $property_videos = sanitize_textarea_field($_POST['property_videos']);
        $property_location = sanitize_text_field($_POST['property_location']);
        $tower_id = intval($_POST['tower_id']);
        $property_code = sanitize_text_field($_POST['property_code']);
        $property_floor = intval($_POST['property_floor']);

        $table_name = $wpdb->prefix . 'gre_properties';
        $wpdb->insert($table_name, array(
            'name' => $property_name,
            'model_id' => $property_model,
            'city' => $property_city,
            'district' => $property_district,
            'price' => $property_price,
            'features' => $property_features,
            'description' => $property_description,
            'images' => $property_images,
            'videos' => $property_videos,
            'location' => $property_location,
            'tower_id' => $tower_id,
            'property_code' => $property_code,
            'floor' => $property_floor
        ));

        wp_redirect(admin_url('admin.php?page=gre_properties'));
        exit;
    }
}

add_action('admin_post_create_property', array('PropertyCreate', 'create_property'));
