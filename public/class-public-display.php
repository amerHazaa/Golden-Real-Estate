<?php
// ملف إدارة عرض الشقق

class PropertyDisplay {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;

        add_shortcode('display_properties', array($this, 'display_properties'));
        add_shortcode('property_details', array($this, 'property_details'));
    }

    public function display_properties($atts) {
        $properties = $this->get_all_properties();

        ob_start();
        echo '<div class="properties-list">';
        foreach ($properties as $property) {
            echo '<div class="property">';
            echo '<h2>' . esc_html($property->name) . '</h2>';
            echo '<p>' . esc_html($property->city) . ', ' . esc_html($property->district) . '</p>';
            echo '<p>' . esc_html($property->price) . ' $</p>';
            echo '<a href="' . get_permalink() . '?property_id=' . $property->ID . '">View Details</a>';
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
        $property = $this->get_property($property_id);

        if (!$property) {
            return '<p>Property not found.</p>';
        }

        ob_start();
        echo '<div class="property-details">';
        echo '<h1>' . esc_html($property->name) . '</h1>';
        echo '<p>' . esc_html($property->city) . ', ' . esc_html($property->district) . '</p>';
        echo '<p>' . esc_html($property->price) . ' $</p>';
        echo '<p>' . esc_html($property->description) . '</p>';
        echo '<div class="property-images">';
        $images = explode(',', $property->images);
        foreach ($images as $image) {
            echo '<img src="' . esc_url($image) . '" alt="' . esc_attr($property->name) . '">';
        }
        echo '</div>';
        echo '</div>';
        return ob_get_clean();
    }

    private function get_all_properties() {
        $table_name = $this->wpdb->prefix . 'gre_properties';
        return $this->wpdb->get_results("SELECT * FROM $table_name");
    }

    private function get_property($id) {
        $table_name = $this->wpdb->prefix . 'gre_properties';
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $table_name WHERE ID = %d", $id));
    }

    public function property_details_admin($property_id) {
        $property = $this->get_property($property_id);

        if (!$property) {
            return '<p>Property not found.</p>';
        }

        ob_start();
        echo '<div class="property-details">';
        echo '<h1>' . esc_html($property->name) . '</h1>';
        echo '<p>' . esc_html($property->city) . ', ' . esc_html($property->district) . '</p>';
        echo '<p>' . esc_html($property->price) . ' $</p>';
        echo '<p>' . esc_html($property->description) . '</p>';
        echo '<div class="property-images">';
        $images = explode(',', $property->images);
        foreach ($images as $image) {
            echo '<img src="' . esc_url($image) . '" alt="' . esc_attr($property->name) . '">';
        }
        echo '</div>';
        echo '</div>';
        return ob_get_clean();
    }
}

new PropertyDisplay();