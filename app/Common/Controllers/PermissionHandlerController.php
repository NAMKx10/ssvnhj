<?php
// app/Common/Controllers/PermissionHandlerController.php (النسخة النهائية المؤمنة)
global $pdo;

if (!function_exists('json_response')) { die("Error: Core helper functions are not loaded."); }

$action = $_REQUEST['form_action'] ?? $_REQUEST['action'] ?? '';

// --- معالجة طلبات الحذف (التي تأتي من روابط عادية) ---
if ($action === 'delete_group' || $action === 'delete_permission') {
    // ▼▼▼ التأمين هنا ▼▼▼
    $required_permission = ($action === 'delete_group') ? 'delete_permission_group' : 'delete_permission';
    if (!has_permission($required_permission)) {
        die('Access Denied.');
    }
    // ▲▲▲ نهاية التأمين ▲▲▲

    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        $table = ($action === 'delete_group') ? 'permission_groups' : 'permissions';
        soft_delete($pdo, $table, $id);
    }
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=permissions'));
    exit();
}


// --- معالجة طلبات AJAX (إضافة وتعديل) ---
try {
    switch ($action) {
        case 'add_group':
            if (!has_permission('add_permission_group')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية المطلوبة.']); }
            
            $pdo->beginTransaction();
            $group_name = trim($_POST['group_name'] ?? '');
            $group_key = trim($_POST['group_key'] ?? '');
            if (empty($group_name) || empty($group_key)) { json_response(['success' => false, 'message' => 'يرجى تعبئة كل الحقول.']); }
            if (!preg_match('/^[a-z0-9_]+$/', $group_key)) { json_response(['success' => false, 'message' => 'المفتاح البرمجي غير صالح.']); }
            
            $stmt = $pdo->prepare("INSERT INTO permission_groups (group_name, group_key) VALUES (?, ?)");
            $stmt->execute([$group_name, $group_key]);
            $pdo->commit();
            json_response(['success' => true, 'message' => 'تمت إضافة المجموعة بنجاح.']);
            break;

        case 'edit_group':
            if (!has_permission('edit_permission_group')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية المطلوبة.']); }

            $pdo->beginTransaction();
            $id = (int)($_POST['id'] ?? 0);
            $group_name = trim($_POST['group_name'] ?? '');
            $group_key = trim($_POST['group_key'] ?? '');
            if (!$id || empty($group_name) || empty($group_key)) { json_response(['success' => false, 'message' => 'بيانات غير مكتملة.']); }
            if (!preg_match('/^[a-z0-9_]+$/', $group_key)) { json_response(['success' => false, 'message' => 'المفتاح البرمجي غير صالح.']); }
            
            $stmt = $pdo->prepare("UPDATE permission_groups SET group_name = ?, group_key = ? WHERE id = ?");
            $stmt->execute([$group_name, $group_key, $id]);
            $pdo->commit();
            json_response(['success' => true, 'message' => 'تم تحديث المجموعة بنجاح.']);
            break;

        case 'add_permission':
            if (!has_permission('add_permission')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية المطلوبة.']); }

            $pdo->beginTransaction();
            $group_id = (int)($_POST['group_id'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $permission_key = trim($_POST['permission_key'] ?? '');
            if (!$group_id || empty($description) || empty($permission_key)) { json_response(['success' => false, 'message' => 'يرجى تعبئة كل الحقول.']); }
            if (!preg_match('/^[a-z0-9_]+$/', $permission_key)) { json_response(['success' => false, 'message' => 'المفتاح البرمجي غير صالح.']); }

            $stmt = $pdo->prepare("INSERT INTO permissions (group_id, description, permission_key) VALUES (?, ?, ?)");
            $stmt->execute([$group_id, $description, $permission_key]);
            $pdo->commit();
            json_response(['success' => true, 'message' => 'تمت إضافة الصلاحية بنجاح.']);
            break;
        
        case 'edit_permission':
            if (!has_permission('edit_permission')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية المطلوبة.']); }

            $pdo->beginTransaction();
            $id = (int)($_POST['id'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $permission_key = trim($_POST['permission_key'] ?? '');
            if (!$id || empty($description) || empty($permission_key)) { json_response(['success' => false, 'message' => 'بيانات غير مكتملة.']); }
            if (!preg_match('/^[a-z0-9_]+$/', $permission_key)) { json_response(['success' => false, 'message' => 'المفتاح البرمجي غير صالح.']); }
            
            $stmt = $pdo->prepare("UPDATE permissions SET description = ?, permission_key = ? WHERE id = ?");
            $stmt->execute([$description, $permission_key, $id]);
            $pdo->commit();
            json_response(['success' => true, 'message' => 'تم تحديث الصلاحية بنجاح.']);
            break;

        default:
            if (!empty($_POST)) {
                 json_response(['success' => false, 'message' => 'الإجراء المطلوب غير معروف.']);
            }
            break;
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    $message = (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062)
        ? 'المفتاح البرمجي الذي أدخلته موجود بالفعل.'
        : 'حدث خطأ في قاعدة البيانات.';
    log_error("Permission handler error: " . $e->getMessage());
    json_response(['success' => false, 'message' => $message]);
}