<?php

class RealEstate {
    public function create_property($property_data) {
        $post_data = [
            'post_title'    => $property_data['name'],
            'post_content'  => isset($property_data['description']) ? $property_data['description'] : '',
            'post_status'   => 'publish',
            'post_type'     => 'property',
        ];

        $property_id = wp_insert_post($post_data);

        if (!is_wp_error($property_id)) {
            update_post_meta($property_id, '_model_id', $property_data['model_id']);
            update_post_meta($property_id, '_tower_id', $property_data['tower_id']);
            update_post_meta($property_id, '_property_code', $property_data['property_code']);
            update_post_meta($property_id, '_floor', $property_data['floor']);
            update_post_meta($property_id, '_status', $property_data['status']);
        }

        return $property_id;
    }
}

?>
