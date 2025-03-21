<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gre_add_property'])) {
    $post_id = wp_insert_post(array(
        'post_title' => sanitize_text_field($_POST['property_name']),
        'post_type' => 'property',
        'post_status' => 'publish'
    ));
    
    if ($post_id) {
        update_post_meta($post_id, 'city', sanitize_text_field($_POST['city']));
        update_post_meta($post_id, 'district', sanitize_text_field($_POST['district']));
        update_post_meta($post_id, 'price', sanitize_text_field($_POST['price']));
    }
}

echo '<div class="wrap">';
echo '<h1>إضافة عقار جديد</h1>';
echo '<form method="POST">';
echo '<label>اسم العقار:</label> <input type="text" name="property_name"><br>';
echo '<label>المدينة:</label> <input type="text" name="city"><br>';
echo '<label>الحي:</label> <input type="text" name="district"><br>';
echo '<label>السعر:</label> <input type="text" name="price"><br>';
echo '<input type="submit" name="gre_add_property" value="إضافة" class="button button-primary">';
echo '</form>';
echo '</div>';