<?php

class Tower {
    public function create_tower($tower_data) {
        $post_data = [
            'post_title'    => $tower_data['name'],
            'post_content'  => isset($tower_data['description']) ? $tower_data['description'] : '',
            'post_status'   => 'publish',
            'post_type'     => 'tower',
        ];

        $tower_id = wp_insert_post($post_data);

        if (!is_wp_error($tower_id)) {
            update_post_meta($tower_id, '_location', $tower_data['location']);
            update_post_meta($tower_id, '_longitude', $tower_data['longitude']);
            update_post_meta($tower_id, '_latitude', $tower_data['latitude']);
            update_post_meta($tower_id, '_floors', $tower_data['floors']);
            update_post_meta($tower_id, '_apartments_per_floor', $tower_data['apartments_per_floor']);
            update_post_meta($tower_id, '_total_area', $tower_data['total_area']);
            update_post_meta($tower_id, '_year_built', $tower_data['year_built']);
            update_post_meta($tower_id, '_status', $tower_data['status']);
            update_post_meta($tower_id, '_features', $tower_data['features']);
        }

        return $tower_id;
    }
}

?>
