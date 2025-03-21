<?php
global $wpdb;
$properties = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'property'");

echo '<div class="wrap">';
echo '<h1>إدارة العقارات</h1>';
echo '<table class="wp-list-table widefat fixed striped">';
echo '<thead><tr><th>العقار</th><th>المدينة</th><th>الحي</th><th>السعر</th><th>الإجراءات</th></tr></thead>';
echo '<tbody>';
foreach ($properties as $property) {
    echo '<tr>';
    echo '<td>' . esc_html($property->post_title) . '</td>';
    echo '<td>' . get_post_meta($property->ID, 'city', true) . '</td>';
    echo '<td>' . get_post_meta($property->ID, 'district', true) . '</td>';
    echo '<td>' . get_post_meta($property->ID, 'price', true) . '</td>';
    echo '<td><a href="' . get_edit_post_link($property->ID) . '">تحرير</a> | <a href="' . get_delete_post_link($property->ID) . '">حذف</a></td>';
    echo '</tr>';
}
echo '</tbody></table>';
echo '</div>';