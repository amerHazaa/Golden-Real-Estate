<?php
// ملف إدارة عرض الشقق

class PropertyDisplay {
    public function __construct() {
        add_shortcode('display_properties', array($this, 'display_properties'));
        add_shortcode('property_details', array($this, 'property_details'));
    }

    public function display_properties($atts) {
        $properties = $this->get_all_properties();

        ob_start();
        echo '<div class="properties-list">';
        foreach ($properties as $property) {
            echo '<div class="property">';
            echo '<h2>' . esc_html($property->post_title) . '</h2>';
            echo '<p>' . esc_html(get_post_meta($property->ID, '_city', true)) . ', ' . esc_html(get_post_meta($property->ID, '_district', true)) . '</p>';
            echo '<p>' . esc_html(get_post_meta($property->ID, '_price', true)) . ' $</p>';
            echo '<a href="' . get_permalink($property->ID) . '">View Details</a>';
            echo '</div>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    public function property_details($atts) {
        if (!isset($_GET['property_id'])) {
            return '<p>Property not found.</p>';
        }

        $property_id = intval($_GET['property_id']);
        $property = get_post($property_id);

        if (!$property || $property->post_type !== 'property') {
            return '<p>Property not found.</p>';
        }

        // جلب تفاصيل النموذج المرتبطة بالشقة
        $model_id = get_post_meta($property_id, '_model_id', true);
        $model = get_post($model_id);

        ob_start();
        echo '<div class="property-details">';
        echo '<h1>' . esc_html($property->post_title) . '</h1>';
        echo '<p>' . esc_html(get_post_meta($property_id, '_city', true)) . ', ' . esc_html(get_post_meta($property_id, '_district', true)) . '</p>';
        echo '<p>' . esc_html(get_post_meta($property_id, '_price', true)) . ' $</p>';
        echo '<p>' . esc_html($property->post_content) . '</p>';
        echo '<h2>تفاصيل النموذج</h2>';
        echo '<p>الاسم: ' . esc_html($model->post_title) . '</p>';
        echo '<p>المدينة: ' . esc_html(get_post_meta($model->ID, '_city', true)) . '</p>';
        echo '<p>الحي: ' . esc_html(get_post_meta($model->ID, '_district', true)) . '</p>';
        echo '<p>السعر: ' . esc_html(get_post_meta($model->ID, '_price', true)) . ' $</p>';
        echo '<p>المميزات: ' . esc_html(get_post_meta($model->ID, '_features', true)) . '</p>';
        echo '<p>الوصف: ' . esc_html($model->post_content) . '</p>';
        echo '<div class="property-images">';
        $images = explode(',', get_post_meta($property_id, '_images', true));
        foreach ($images as $image) {
            echo '<img src="' . esc_url($image) . '" alt="' . esc_attr($property->post_title) . '">';
        }
        echo '</div>';
        echo '</div>';
        return ob_get_clean();
    }

    private function get_all_properties() {
        $args = array(
            'post_type' => 'property',
            'posts_per_page' => -1
        );
        return get_posts($args);
    }
}

new PropertyDisplay();