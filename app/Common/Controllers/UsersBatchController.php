<?php
// app/Common/Controllers/UsersBatchController.php (النسخة المحدثة)

global $pdo, $page;

// دالة مساعدة لجلب قائمة الأدوار كأسماء فقط
function get_roles_names_for_js($pdo) {
    return $pdo->query("SELECT role_name FROM roles WHERE deleted_at IS NULL ORDER BY role_name")->fetchAll(PDO::FETCH_COLUMN);
}

// --- معالجة طلب التعديل الجماعي ---
if ($page === 'users/batch_edit') {
    if (!has_permission('edit_user')) { die('Access Denied.'); }
    
    $ids_string = $_GET['ids'] ?? '';
    $ids = !empty($ids_string) ? array_filter(explode(',', $ids_string), 'is_numeric') : [];
    
    $users_data = [];
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        // جلب اسم الدور مباشرة من الاستعلام
        $stmt = $pdo->prepare("
            SELECT u.id, u.full_name, u.username, r.role_name, u.status, u.email, u.mobile
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id IN ($placeholders)
        ");
        $stmt->execute($ids);
        // جلب البيانات كمصفوفة من المصفوفات الرقمية مباشرة
        $users_data = $stmt->fetchAll(PDO::FETCH_NUM);
    }
    
    $roles_for_js = get_roles_names_for_js($pdo);
    require_once ROOT_PATH . '/app/Common/Views/users/batch_edit_view.php';

// --- معالجة طلب الإضافة الجماعية ---
} elseif ($page === 'users/batch_add') {
    if (!has_permission('add_user')) { die('Access Denied.'); }
    
    $roles_for_js = get_roles_names_for_js($pdo);
    require_once ROOT_PATH . '/app/Common/Views/users/batch_add_view.php';
}