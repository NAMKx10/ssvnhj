<?php
/*
 * الملف: public/print.php
 * الوظيفة: بوابة آمنة لعرض وطباعة التقارير والقوالب المختلفة.
*/

// --- 1. التأسيس الكامل للبيئة ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/core/functions.php';

// --- 2. التحقق من تسجيل الدخول ---
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// --- 3. تحديد القالب المطلوب وتضمينه ---
$baseDir = __DIR__; 
$template = $_GET['template'] ?? '';

// القائمة النهائية والموحدة للقوالب القابلة للطباعة
$allowed_templates = [
    'client_profile_print'   => $baseDir . '/../src/modules/reports/client_profile_print_view.php',
    'property_profile_print' => $baseDir . '/../src/modules/reports/property_profile_print_view.php', 
    'unit_profile_print'     => $baseDir . '/../src/modules/reports/unit_profile_print_view.php',
    'supplier_profile_print' => $baseDir . '/../src/modules/reports/supplier_profile_print_view.php',
];

if (isset($allowed_templates[$template])) {
    $template_path = $allowed_templates[$template];
    if (file_exists($template_path)) {
        require_once $template_path;
    } else {
        http_response_code(404);
        die("خطأ: ملف القالب غير موجود.");
    }
} else {
    http_response_code(403);
    die("خطأ: قالب الطباعة غير مسموح به أو غير محدد.");
}
?>