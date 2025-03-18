<?php
// ملف إدارة الأكواد البرمجية (Filters & Actions)

// فلتر لتعديل عنوان العقار
function gre_modify_property_title($title, $id) {
    $post = get_post($id);
    if ($post->post_type === 'property') {
        return 'عقار: ' . $title;
    }
    return $title;
}
add_filter('the_title', 'gre_modify_property_title', 10, 2);

// فلتر لتعديل محتوى العقار
function gre_modify_property_content($content) {
    if (is_singular('property')) {
        return 'تفاصيل العقار: ' . $content;
    }
    return $content;
}
add_filter('the_content', 'gre_modify_property_content');

// إجراء عند حفظ العقار
function gre_after_save_property($post_id) {
    $post = get_post($post_id);
    if ($post->post_type === 'property') {
        // تنفيذ بعض الإجراءات مثل إرسال إشعار أو تسجيل الحدث في سجل النظام
        error_log('تم حفظ العقار بالمعرف: ' . $post_id);
    }
}
add_action('save_post', 'gre_after_save_property');

// إجراء عند حذف العقار
function gre_after_delete_property($post_id) {
    $post = get_post($post_id);
    if ($post->post_type === 'property') {
        // تنفيذ بعض الإجراءات مثل إرسال إشعار أو تسجيل الحدث في سجل النظام
        error_log('تم حذف العقار بالمعرف: ' . $post_id);
    }
}
add_action('before_delete_post', 'gre_after_delete_property');
