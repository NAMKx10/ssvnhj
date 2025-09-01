<?php
// app/Common/Controllers/UsersBatchController.php (نسخة محسنة مع توصيات التنظيم والتحقق، وتحضير لاستخدام Handsontable في عدة أماكن)

global $pdo, $page;

/**
 * دالة مساعدة لتصفية الـ IDs المدخلة - تقبل مصفوفة وترجع فقط القيم الرقمية الصحيحة
 * @param array $ids
 * @return array
 */
function filter_valid_ids($ids) {
    return array_values(array_filter($ids, function($id) {
        return is_numeric($id) && $id > 0;
    }));
}

/**
 * دالة مساعدة لجلب قائمة الأدوار بشكل جاهز لـ Handsontable
 * @return array قائمة الأدوار (role_name فقط أو (id => name) حسب الحاجة)
 */
function get_roles_for_js($pdo, $assoc = false) {
    $stmt = $pdo->query("SELECT id, role_name FROM roles WHERE deleted_at IS NULL ORDER BY role_name");
    return $assoc ? $stmt->fetchAll(PDO::FETCH_KEY_PAIR) : $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
}

// --- إضافة جماعية للمستخدمين (batch_add) ---
if ($page === 'users/batch_add') {
    // جلب قائمة الأدوار (بدون ID، فقط الأسماء لسهولة الاستخدام في Handsontable)
    $roles_for_js = get_roles_for_js($pdo, false);

    // إذا أردت تمرير مصادر أخرى (حالات، فروع...) هنا مستقبلاً، يمكنك ذلك بسهولة
    // $statuses_for_js = [...]; $branches_for_js = [...];

    require_once ROOT_PATH . '/app/Common/Views/users/batch_add_view.php';

// --- تعديل جماعي للمستخدمين (batch_edit) ---
} elseif ($page === 'users/batch_edit') {
    $ids_string = $_GET['ids'] ?? '';
    $ids = !empty($ids_string) ? explode(',', $ids_string) : [];
    $ids = filter_valid_ids($ids);

    $users_data = [];
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id, full_name, username, email, mobile, role_id, status FROM users WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $users_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // جلب قائمة الأدوار بشكل (ID => Name) لسهولة التحويل في Handsontable
    $roles_list = get_roles_for_js($pdo, true);

    // تحويل بيانات المستخدمين لكي تتوافق مع Handsontable
    $users_for_js = array_map(function($user) use ($roles_list) {
        return [
            $user['id'],
            $user['full_name'],
            $user['username'],
            $roles_list[$user['role_id']] ?? '', // تحويل role_id إلى اسم الدور
            $user['status'],
            $user['email'],
            $user['mobile']
        ];
    }, $users_data);

    $roles_for_js = array_values($roles_list);

    // يمكنك مستقبلاً تمرير مصادر أخرى (الحالات، الفروع...) إلى Handsontable هنا بنفس النمط
    // $statuses_for_js = [...]; $branches_for_js = [...];

    require_once ROOT_PATH . '/app/Common/Views/users/batch_edit_view.php';
}

/**
 * نقطة توسعة مستقبلية:
 * إذا أردت استخدام Handsontable في موديلات أخرى (مثل المنتجات أو العقارات)، 
 * أنشئ دوال مساعدة مشابهة لجلب المصادر وتحويل البيانات، ومررها للواجهات بنفس النمط.
 * يمكن وضع الدوال المساعدة في app/Helpers/functions.php مستقبلاً لتكون مركزية.
 */