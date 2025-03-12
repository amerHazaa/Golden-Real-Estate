<?php
// ملف إدارة الأكواد البرمجية (Filters & Actions)

// مثال على إضافة فلتر لتعديل عنوان العقار
function gre_modify_property_title($title) {
    return 'عقار: ' . $title;
}
add_filter('the_title', 'gre_modify_property_title');

// يمكنك إضافة المزيد من الفلاتر والإجراءات هنا حسب الحاجة