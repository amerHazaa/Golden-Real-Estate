<?php
// ملف تعديل الشقق

class PropertyEdit {
    public function edit_property_form($property, $model) {
        echo '<form action="' . admin_url('admin-post.php') . '" method="POST">';
        echo '<input type="hidden" name="action" value="update_property">';
        echo '<input type="hidden" name="property_id" value="' . esc_attr($property->ID) . '">';
        echo '<label for="property_name">اسم الشقة:</label>';
        echo '<input type="text" id="property_name" name="property_name" value="' . esc_attr($property->post_title) . '" required><br>';
        echo '<label for="property_model">النموذج:</label>';
        echo '<select id="property_model" name="property_model" required>';
        // يجب استدعاء بيانات النماذج من قاعدة البيانات وعرضها هنا
        $models = get_posts(['post_type' => 'model', 'posts_per_page' => -1]);
        foreach ($models as $model) {
            $selected = ($model->ID == get_post_meta($property->ID, '_model_id', true)) ? 'selected' : '';
            echo '<option value="' . esc_attr($model->ID) . '" ' . $selected . '>' . esc_html($model->post_title) . '</option>';
        }
        echo '</select><br>';
        echo '<label for="property_city">المدينة:</label>';
        echo '<input type="text" id="property_city" name="property_city" value="' . esc_attr(get_post_meta($property->ID, '_city', true)) . '" required><br>';
        echo '<label for="property_district">الحي:</label>';
        echo '<input type="text" id="property_district" name="property_district" value="' . esc_attr(get_post_meta($property->ID, '_district', true)) . '" required><br>';
        echo '<label for="property_price">السعر:</label>';
        echo '<input type="number" id="property_price" name="property_price" value="' . esc_attr(get_post_meta($property->ID, '_price', true)) . '" required><br>';
        echo '<label for="property_features">المميزات:</label>';
        echo '<textarea id="property_features" name="property_features" required>' . esc_textarea(get_post_meta($property->ID, '_features', true)) . '</textarea><br>';
        echo '<label for="property_description">الوصف:</label>';
        echo '<textarea id="property_description" name="property_description" required>' . esc_textarea($property->post_content) . '</textarea><br>';
        echo '<label for="property_images">الصور (روابط مفصولة بفاصلة):</label>';
        echo '<textarea id="property_images" name="property_images" required>' . esc_textarea(get_post_meta($property->ID, '_images', true)) . '</textarea><br>';
        echo '<label for="property_videos">الفيديوهات (روابط مفصولة بفاصلة):</label>';
        echo '<textarea id="property_videos" name="property_videos" required>' . esc_textarea(get_post_meta($property->ID, '_videos', true)) . '</textarea><br>';
        echo '<label for="property_location">الموقع (إحداثيات):</label>';
        echo '<input type="text" id="property_location" name="property_location" value="' . esc_attr(get_post_meta($property->ID, '_location', true)) . '" required><br>';
        echo '<label for="tower_id">البرج:</label>';
        echo '<select id="tower_id" name="tower_id" required>';
        // يجب استدعاء بيانات الأبراج من قاعدة البيانات وعرضها هنا
        $towers = get_posts(['post_type' => 'tower', 'posts_per_page' => -1]);
        foreach ($towers as $tower) {
            $selected = ($tower->ID == get_post_meta($property->ID, '_tower_id', true)) ? 'selected' : '';
            echo '<option value="' . esc_attr($tower->ID) . '" ' . $selected . '>' . esc_html($tower->post_title) . '</option>';
        }
        echo '</select><br>';
        echo '<label for="property_code">رمز الشقة:</label>';
        echo '<input type="text" id="property_code" name="property_code" value="' . esc_attr(get_post_meta($property->ID, '_property_code', true)) . '" required><br>';
        echo '<label for="property_floor">الدور:</label>';
        echo '<input type="number" id="property_floor" name="property_floor" value="' . esc_attr(get_post_meta($property->ID, '_floor', true)) . '" required><br>';
        echo '<input type="submit" value="تحديث الشقة" class="button button-primary">';
        echo '</form>';
    }
}
