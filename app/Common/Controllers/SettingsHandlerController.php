<?php
// app/Common/Controllers/SettingsHandlerController.php (النسخة المطورة مع الحذف الناعم)

global $pdo;

if (!function_exists('json_response')) { die("Core functions not loaded."); }

$action = $_POST['form_action'] ?? $_GET['action'] ?? '';

// --- معالجة طلبات الحذف (روابط عادية) ---
if ($action === 'delete_group' || $action === 'delete_option') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        if ($action === 'delete_group') {
            // لا نسمح بحذف المجموعات الأساسية
            $stmt = $pdo->prepare("SELECT is_core FROM setting_groups WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetchColumn()) {
                soft_delete($pdo, 'setting_groups', $id);
            }
        } else {
            // هنا لا توجد قيود على حذف الخيارات
            soft_delete($pdo, 'settings', $id);
        }
    }
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=settings'));
    exit();
}


// --- معالجة طلبات AJAX ---
try {
    $pdo->beginTransaction();
    $message = 'تم تنفيذ العملية بنجاح.';
    switch ($action) {
        case 'add_group':
            $group_name = trim($_POST['group_name'] ?? '');
            $group_key = trim($_POST['group_key'] ?? '');
            if (empty($group_name) || empty($group_key)) { json_response(['success' => false, 'message' => 'كل الحقول مطلوبة.']); }
            $stmt = $pdo->prepare("INSERT INTO setting_groups (group_name, group_key) VALUES (?, ?)");
            $stmt->execute([$group_name, $group_key]);
            $message = "تمت إضافة المجموعة بنجاح.";
            break;
        case 'edit_group':
            $id = (int)($_POST['id'] ?? 0);
            $group_name = trim($_POST['group_name'] ?? '');
            $group_key = trim($_POST['group_key'] ?? '');
            if (!$id || empty($group_name) || empty($group_key)) { json_response(['success' => false, 'message' => 'بيانات غير مكتملة.']); }
            $stmt = $pdo->prepare("UPDATE setting_groups SET group_name = ?, group_key = ? WHERE id = ?");
            $stmt->execute([$group_name, $group_key, $id]);
            $message = "تم تحديث المجموعة بنجاح.";
            break;
        case 'add_option':
    $group_id = (int)($_POST['group_id'] ?? 0); // <-- التعديل هنا
    $option_key = trim($_POST['option_key'] ?? '');
    $option_value = trim($_POST['option_value'] ?? '');
    if (empty($group_id) || empty($option_key) || empty($option_value)) { json_response(['success' => false, 'message' => 'كل الحقول مطلوبة.']); }
    $stmt = $pdo->prepare("INSERT INTO settings (group_id, option_key, option_value) VALUES (?, ?, ?)"); // <-- والتعديل هنا
    $stmt->execute([$group_id, $option_key, $option_value]);
    $message = "تمت إضافة الخيار بنجاح.";
    break;
        case 'edit_option':
            $id = (int)($_POST['id'] ?? 0);
            $option_key = trim($_POST['option_key'] ?? '');
            $option_value = trim($_POST['option_value'] ?? '');
            if (!$id || empty($option_key) || empty($option_value)) { json_response(['success' => false, 'message' => 'بيانات غير مكتملة.']); }
            $stmt = $pdo->prepare("UPDATE settings SET option_key = ?, option_value = ? WHERE id = ?");
            $stmt->execute([$option_key, $option_value, $id]);
            $message = "تم تحديث الخيار بنجاح.";
            break;
        default:
            json_response(['success' => false, 'message' => 'الإجراء المطلوب غير معروف.']);
            break;
    }
    $pdo->commit();
    json_response(['success' => true, 'message' => $message]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    log_error("Settings handler error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'حدث خطأ في قاعدة البيانات.']);
}