<?php
// app/Helpers/functions.php

// (الكود الموجود حاليًا يبقى كما هو)

/**
 * دالة مركزية لرسم مكون ترقيم الصفحات.
 *
 * @param int   $current_page  الصفحة الحالية.
 * @param int   $total_pages   إجمالي عدد الصفحات.
 * @param array $params        مصفوفة GET الحالية للحفاظ على الفلاتر.
 */
function render_pagination($current_page, $total_pages, $params = [])
{
    if ($total_pages <= 1) {
        return; // لا تعرض أي شيء إذا كانت هناك صفحة واحدة فقط
    }

    // إزالة باراميتر الصفحة الحالي لتجنب تكراره
    unset($params['p']);

    echo '<ul class="pagination m-0 ms-auto">';

    // زر "السابق"
    $prev_class = ($current_page <= 1) ? 'disabled' : '';
    echo '<li class="page-item ' . $prev_class . '">';
    echo '<a class="page-link" href="?' . http_build_query(array_merge($params, ['p' => $current_page - 1])) . '">السابق</a>';
    echo '</li>';

    // عرض أرقام الصفحات
    for ($i = 1; $i <= $total_pages; $i++) {
        $active_class = ($i == $current_page) ? 'active' : '';
        echo '<li class="page-item ' . $active_class . '"><a class="page-link" href="?' . http_build_query(array_merge($params, ['p' => $i])) . '">' . $i . '</a></li>';
    }

    // زر "التالي"
    $next_class = ($current_page >= $total_pages) ? 'disabled' : '';
    echo '<li class="page-item ' . $next_class . '">';
    echo '<a class="page-link" href="?' . http_build_query(array_merge($params, ['p' => $current_page + 1])) . '">التالي</a>';
    echo '</li>';

    echo '</ul>';
}

/**
 * دالة مركزية للقيام بالحذف الناعم (Soft Delete).
 * @param PDO    $pdo     - متغير اتصال قاعدة البيانات.
 * @param string $table   - اسم الجدول.
 * @param int    $id      - معرّف السجل المراد حذفه.
 * @return bool           - true عند النجاح, false عند الفشل.
 */
function soft_delete($pdo, $table, $id) {
    // التحقق من أن اسم الجدول آمن
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
        return false;
    }
    
    try {
        $sql = "UPDATE `{$table}` SET deleted_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int)$id]);
        return true;
    } catch (PDOException $e) {
        // يمكن تسجيل الخطأ هنا مستقبلاً
        return false;
    }
}

/**
 * دالة مركزية للتحقق من صلاحية المستخدم الحالي.
 * @param string $permission_key المفتاح البرمجي للصلاحية.
 * @return bool
 */
function has_permission($permission_key) {
    // تحقق أولاً من وجود مصفوفة الصلاحيات في الجلسة
    if (!isset($_SESSION['user_permissions']) || !is_array($_SESSION['user_permissions'])) {
        return false;
    }
    
    // إذا كان المستخدم هو Super Admin (بناءً على الدور)، امنحه كل الصلاحيات
    // سنضيف منطق الدور لاحقًا عندما نبنيه بالكامل
    // if (isset($_SESSION['user_role_name']) && $_SESSION['user_role_name'] === 'Super Admin') {
    //     return true;
    // }
    
    // تحقق مما إذا كانت الصلاحية المطلوبة موجودة في مصفوفة صلاحيات المستخدم
    if (in_array($permission_key, $_SESSION['user_permissions'])) {
        return true;
    }

    // إذا لم يتحقق أي شرط، فالمستخدم لا يملك الصلاحية
    return false;
}