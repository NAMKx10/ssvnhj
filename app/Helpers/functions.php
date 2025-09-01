<?php
/**
 * app/Helpers/functions.php
 * دوال مساعدة مركزية للنظام (مقترح مُحسّن واحترافي)
 */


/* ============================= */
/* دوال الترقيم والعرض           */
/* ============================= */

/**
 * رسم مكون ترقيم الصفحات.
 *
 * @param int   $current_page  الصفحة الحالية.
 * @param int   $total_pages   إجمالي عدد الصفحات.
 * @param array $params        مصفوفة GET الحالية للحفاظ على الفلاتر.
 * @return void
 */
function render_pagination($current_page, $total_pages, $params = [])
{
    if ($total_pages <= 1) {
        return; // لا تعرض أي شيء إذا كانت هناك صفحة واحدة فقط
    }

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


/* ============================= */
/* دوال قاعدة البيانات           */
/* ============================= */

/**
 * حذف ناعم لسجل في جدول محدد.
 *
 * @param PDO    $pdo     - متغير اتصال قاعدة البيانات.
 * @param string $table   - اسم الجدول.
 * @param int    $id      - معرّف السجل المراد حذفه.
 * @return bool           - true عند النجاح, false عند الفشل.
 */
function soft_delete($pdo, $table, $id) {
    // التحقق من أن اسم الجدول آمن
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
        log_error("محاولة حذف غير آمنة من جدول: {$table}");
        return false;
    }

    try {
        $sql = "UPDATE `{$table}` SET deleted_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int)$id]);
        return true;
    } catch (PDOException $e) {
        log_error("خطأ في الحذف الناعم: " . $e->getMessage());
        return false;
    }
}


/* ============================= */
/* دوال التحقق والصلاحيات        */
/* ============================= */

/**
 * التحقق من صلاحية المستخدم الحالي.
 *
 * @param string $permission_key المفتاح البرمجي للصلاحية.
 * @return bool
 */
function has_permission($permission_key) {
    // تحقق أولاً من وجود مصفوفة الصلاحيات في الجلسة
    if (!isset($_SESSION['user_permissions']) || !is_array($_SESSION['user_permissions'])) {
        return false;
    }

    // إذا كان المستخدم هو Super Admin (بناءً على الدور)، امنحه كل الصلاحيات
    // if (isset($_SESSION['user_role_name']) && $_SESSION['user_role_name'] === 'Super Admin') {
    //     return true;
    // }

    // تحقق مما إذا كانت الصلاحية مطلوبة موجودة في مصفوفة صلاحيات المستخدم
    if (in_array($permission_key, $_SESSION['user_permissions'])) {
        return true;
    }

    return false;
}


/* ============================= */
/* دوال تنسيق البيانات           */
/* ============================= */

/**
 * تنسيق التاريخ للعرض حسب اللغة المطلوبة.
 *
 * @param string $date تاريخ بصيغة SQL أو Timestamp.
 * @param string $format صيغة العرض (افتراضي: 'Y-m-d').
 * @return string
 */
function format_date($date, $format = 'Y-m-d') {
    if (!$date) return '';
    return date($format, strtotime($date));
}

/**
 * طباعة نص آمن للعرض في الواجهة (HTML).
 *
 * @param string $text
 * @return string
 */
function html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}


/* ============================= */
/* دوال التحقق من المدخلات        */
/* ============================= */

/**
 * التحقق من صحة رقم الجوال السعودي.
 *
 * @param string $mobile
 * @return bool
 */
function is_valid_sa_mobile($mobile) {
    return preg_match('/^05\d{8}$/', $mobile);
}


/* ============================= */
/* دوال تسجيل الأخطاء            */
/* ============================= */

/**
 * تسجيل رسالة خطأ في ملف log مخصص.
 *
 * @param string $message
 * @return void
 */
function log_error($message) {
    $log_path = defined('ROOT_PATH') ? ROOT_PATH . '/storage/logs/error.log' : __DIR__ . '/../../storage/logs/error.log';
    if (!is_dir(dirname($log_path))) {
        mkdir(dirname($log_path), 0777, true);
    }
    file_put_contents($log_path, '['.date('Y-m-d H:i:s').'] ' . $message . "\n", FILE_APPEND);
}


/* ============================= */
/* دوال إضافية مقترحة            */
/* ============================= */

/**
 * دالة لتوليد كلمة مرور عشوائية قوية.
 *
 * @param int $length طول كلمة المرور المطلوبة.
 * @return string
 */
function generate_password($length = 10) {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+'), 0, $length);
}

/**
 * دالة للتحقق من صحة الإيميل.
 *
 * @param string $email
 * @return bool
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * دالة مركزية لتشفير كلمة المرور (استخدمها عند الإضافة/التعديل).
 *
 * @param string $password كلمة المرور الأصلية.
 * @return string كلمة المرور المشفرة.
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * دالة للتحقق من كلمة المرور المدخلة مقابل المخزنة.
 *
 * @param string $entered_pw كلمة المرور المدخلة من المستخدم.
 * @param string $hashed_pw  كلمة المرور المشفرة المخزنة.
 * @return bool
 */
function verify_password($entered_pw, $hashed_pw) {
    return password_verify($entered_pw, $hashed_pw);
}