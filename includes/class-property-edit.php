<?php
// ملف تعديل الشقق

class PropertyEdit {
    public function edit_property_form($property) {
        echo '<form action="' . admin_url('admin-post.php') . '" method="POST">';
        echo '<input type="hidden" name="action" value="update_property">';
        echo '<input type="hidden" name="property_id" value="' . esc_attr($property->ID) . '">';
        echo '<label for="property_name">اسم الشقة:</label>';
        echo '<input type="text" id="property_name" name="property_name" value="' . esc_attr($property->name) . '" required><br>';
        echo '<label for="property_model">النموذج:</label>';
        echo '<input type="text" id="property_model" name="property_model" value="' . esc_attr($property->model) . '" required><br>';
        echo '<label for="property_city">المدينة:</label>';
        echo '<input type="text" id="property_city" name="property_city" value="' . esc_attr($property->city) . '" required><br>';
        echo '<label for="property_district">الحي:</label>';
        echo '<input type="text" id="property_district" name="property_district" value="' . esc_attr($property->district) . '" required><br>';
        echo '<label for="property_price">السعر:</label>';
        echo '<input type="number" id="property_price" name="property_price" value="' . esc_attr($property->price) . '" required><br>';
        echo '<label for="property_features">المميزات:</label>';
        echo '<textarea id="property_features" name="property_features" required>' . esc_textarea($property->features) . '</textarea><br>';
        echo '<label for="property_description">الوصف:</label>';
        echo '<textarea id="property_description" name="property_description" required>' . esc_textarea($property->description) . '</textarea><br>';
        echo '<label for="property_images">الصور (روابط مفصولة بفاصلة):</label>';
        echo '<textarea id="property_images" name="property_images" required>' . esc_textarea($property->images) . '</textarea><br>';
        echo '<label for="property_videos">الفيديوهات (روابط مفصولة بفاصلة):</label>';
        echo '<textarea id="property_videos" name="property_videos" required>' . esc_textarea($property->videos) . '</textarea><br>';
        echo '<label for="property_location">الموقع (إحداثيات):</label>';
        echo '<input type="text" id="property_location" name="property_location" value="' . esc_attr($property->location) . '" required><br>';
        echo '<label for="tower_id">البرج:</label>';
        echo '<select id="tower_id" name="tower_id" required>';
        // يجب استدعاء بيانات الأبراج من قاعدة البيانات وعرضها هنا
        global $wpdb;
        $towers = $wpdb->get_results("SELECT ID, name FROM {$wpdb->prefix}gre_towers");
        foreach ($towers as $tower) {
            $selected = ($tower->ID == $property->tower_id) ? 'selected' : '';
            echo '<option value="' . esc_attr($tower->ID) . '" ' . $selected . '>' . esc_html($tower->name) . '</option>';
        }
        echo '</select><br>';
        echo '<label for="property_code">رمز الشقة:</label>';
        echo '<input type="text" id="property_code" name="property_code" value="' . esc_attr($property->property_code) . '" required><br>';
        echo '<label for="property_floor">الدور:</label>';
        echo '<input type="number" id="property_floor" name="property_floor" value="' . esc_attr($property->floor) . '" required><br>';
        echo '<input type="submit" value="تحديث الشقة" class="button button-primary">';
        echo '</form>';
    }
}