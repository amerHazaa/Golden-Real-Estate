<?php
// ملف إدارة عرض الشقق والابراج السكنية والنماذج

if (!class_exists('PublicDisplay')) {
    class PublicDisplay {
        public function __construct() {
            add_shortcode('display_properties', array($this, 'display_properties'));
            add_shortcode('property_details', array($this, 'property_details'));
            add_shortcode('display_towers', array($this, 'display_towers'));
            add_shortcode('display_models', array($this, 'display_models'));
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

            ob_start();
            echo '<div class="property-details">';
            echo '<h1>' . esc_html($property->post_title) . '</h1>';
            echo '<p>' . esc_html(get_post_meta($property->ID, '_city', true)) . ', ' . esc_html(get_post_meta($property->ID, '_district', true)) . '</p>';
            echo '<p>' . esc_html(get_post_meta($property->ID, '_price', true)) . ' $</p>';
            echo '<p>' . esc_html($property->post_content) . '</p>';
            echo '<div class="property-images">';
            $images = explode(',', get_post_meta($property->ID, '_images', true));
            foreach ($images as $image) {
                echo '<img src="' . esc_url($image) . '" alt="' . esc_attr($property->post_title) . '">';
            }
            echo '</div>';
            echo '</div>';
            return ob_get_clean();
        }

        public function display_towers($atts) {
            $towers = get_posts(['post_type' => 'tower', 'posts_per_page' => -1]);

            ob_start();
            echo '<div class="towers-list">';
            foreach ($towers as $tower) {
                echo '<div class="tower">';
                echo '<h2>' . esc_html($tower->post_title) . '</h2>';
                echo '<p>' . esc_html(get_post_meta($tower->ID, '_city', true)) . '</p>';
                echo '<p>' . esc_html(get_post_meta($tower->ID, '_floors', true)) . ' floors</p>';
                echo '<a href="' . get_permalink($tower->ID) . '">View Details</a>';
                echo '</div>';
            }
            echo '</div>';
            return ob_get_clean();
        }

        public function display_models($atts) {
            $models = get_posts(['post_type' => 'model', 'posts_per_page' => -1]);

            ob_start();
            echo '<div class="models-list">';
            foreach ($models as $model) {
                echo '<div class="model">';
                echo '<h2>' . esc_html($model->post_title) . '</h2>';
                echo '<p>' . esc_html(get_post_meta($model->ID, '_room_count', true)) . ' rooms, ' . esc_html(get_post_meta($model->ID, '_bathroom_count', true)) . ' bathrooms</p>';
                echo '<p>' . esc_html(get_post_meta($model->ID, '_area', true)) . ' sq ft</p>';
                echo '<a href="' . get_permalink($model->ID) . '">View Details</a>';
                echo '</div>';
            }
            echo '</div>';
            return ob_get_clean();
        }

        private function get_all_properties() {
            $args = [
                'post_type' => 'property',
                'posts_per_page' => -1
            ];
            return get_posts($args);
        }
    }

    new PublicDisplay();
}
