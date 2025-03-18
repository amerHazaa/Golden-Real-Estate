<?php
// ملف الدوال العامة للإضافة

// مثال على دالة لتنسيق الأسعار
function gre_format_price($price) {
    return number_format($price, 2) . ' $';
}

// يمكنك إضافة المزيد من الدوال هنا حسب الحاجة
?>
