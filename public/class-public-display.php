<?php
// ملف العرض العام

class PublicDisplay {
    public function display_properties() {
        $properties = get_posts(['post_type' => 'property', 'posts_per_page' => -1]);

        echo '<div class="properties-list">';
        foreach ($properties as $property) {
            $model_id = get_post_meta($property->ID, '_model_id', true);
            $model = get_post($model_id);

            echo '<div class="property-item">';
            echo '<h2>' . esc_html($property->post_title) . '</h2>';
            echo '<p>' . esc_html($model ? $model->post_title : 'غير متوفر') . '</p>';
            echo '<p>المدينة: ' . esc_html(get_post_meta($property->ID, '_city', true)) . '</p>';
            echo '<p>الحي: ' . esc_html(get_post_meta($property->ID, '_district', true)) . '</p>';
            echo '<p>السعر: ' . esc_html(get_post_meta($property->ID, '_price', true)) . ' $</p>';
            echo '<p>المميزات: ' . esc_html(get_post_meta($property->ID, '_features', true)) . '</p>';
            echo '<p>الوصف: ' . esc_html($property->post_content) . '</p>';
            echo '<p>الصور: ' . esc_html(get_post_meta($property->ID, '_images', true)) . '</p>';
            echo '<p>الفيديوهات: ' . esc_html(get_post_meta($property->ID, '_videos', true)) . '</p>';
            echo '<p>الموقع: ' . esc_html(get_post_meta($property->ID, '_location', true)) . '</p>';
            echo '</div>';
        }
        echo '</div>';
    }
}
