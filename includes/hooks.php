<?php
// ملف الهوكات

add_action('save_post', 'gre_save_custom_meta_data');
function gre_save_custom_meta_data($post_id) {
    // تحقق مما إذا كان يتم الحفظ تلقائيًا
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // تحقق من صلاحيات المستخدم
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // تحقق من نوع المقالة
    if (isset($_POST['post_type']) && 'property' == $_POST['post_type']) {  // إضافة تحقق من وجود post_type
        // حفظ البيانات المخصصة
        update_post_meta($post_id, '_city', sanitize_text_field($_POST['city']));
        update_post_meta($post_id, '_district', sanitize_text_field($_POST['district']));
        update_post_meta($post_id, '_price', floatval($_POST['price']));
        update_post_meta($post_id, '_features', sanitize_textarea_field($_POST['features']));
        update_post_meta($post_id, '_videos', sanitize_textarea_field($_POST['videos']));
        update_post_meta($post_id, '_images', sanitize_textarea_field($_POST['images']));
        update_post_meta($post_id, '_location', sanitize_text_field($_POST['location']));
    }
}

?>