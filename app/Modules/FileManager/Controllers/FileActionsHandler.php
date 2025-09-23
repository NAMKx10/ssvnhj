<?php
// app/Modules/FileManager/Controllers/FileActionsHandler.php (النسخة النهائية الكاملة)
global $pdo;

// التأكد من أن الدوال المساعدة محملة
if (!function_exists('json_response') || !function_exists('soft_delete_recursive')) { 
    die("Error: Core helper functions are not loaded."); 
}

// تحديد الإجراء من أي مصدر ممكن
$action = $_POST['action'] ?? $_GET['action'] ?? $_POST['form_action'] ?? '';
$data = [];
if (empty($action) && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['form_action'] ?? '';
}

// --- معالجة الإجراءات التي لا تستخدم AJAX (الحذف والتنزيل) ---
if ($action === 'delete' || $action === 'download') {
    // احصل على IDs سواء من رابط (GET) أو من نموذج (POST)
    $ids = (array)($_GET['id'] ?? $_POST['ids'] ?? []); // <-- كان الخطأ هنا
    if (empty($ids)) { die("ID(s) are required."); }

    if ($action === 'delete') {
        if (!has_permission('delete_files')) { die('Access Denied.'); }
        soft_delete_recursive($pdo, $ids);
    }

    if ($action === 'download') {
        if (!has_permission('download_files')) { die('Access Denied.'); }
        $id = $ids[0]; // التنزيل يعمل على ملف واحد فقط
        $stmt = $pdo->prepare("SELECT file_name, system_path FROM files WHERE id = ? AND file_type = 'file'");
        $stmt->execute([$id]);
        $file = $stmt->fetch();
        if ($file && file_exists($file['system_path'])) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file['file_name']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file['system_path']));
            readfile($file['system_path']);
            exit;
        }
    }
    
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=file-manager'));
    exit();
}

try {
    $pdo->beginTransaction();
    $message = 'تم تنفيذ العملية بنجاح.';

    switch ($action) {
        case 'create_folder':
            if (!has_permission('create_folders')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $folder_name = trim($_POST['folder_name'] ?? '');
            if (empty($folder_name)) { json_response(['success' => false, 'message' => 'اسم المجلد مطلوب.']); }

            $stmt = $pdo->prepare("INSERT INTO files (parent_id, user_id, file_name, file_type) VALUES (?, ?, ?, 'folder')");
            $stmt->execute([$parent_id, $_SESSION['user_id'], $folder_name]);
            $message = "تم إنشاء المجلد بنجاح.";
            break;

        case 'upload_file':
            if (!has_permission('upload_files')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            
            if (!isset($_FILES['file_to_upload']) || $_FILES['file_to_upload']['error'] !== UPLOAD_ERR_OK) {
                json_response(['success' => false, 'message' => 'حدث خطأ أثناء رفع الملف.']);
            }

            $file = $_FILES['file_to_upload'];
            $file_name = basename($file['name']);
            $file_size = $file['size'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // إنشاء مسار آمن للملف
            $upload_dir = ROOT_PATH . '/public/uploads/files/';
            if (!is_dir($upload_dir)) { mkdir($upload_dir, 0775, true); }
            $system_path = $upload_dir . time() . '_' . uniqid() . '.' . $file_extension;

            if (move_uploaded_file($file['tmp_name'], $system_path)) {
                // حفظ البيانات الوصفية في قاعدة البيانات
                $stmt = $pdo->prepare("INSERT INTO files (parent_id, user_id, file_name, system_path, file_type, file_extension, file_size) VALUES (?, ?, ?, ?, 'file', ?, ?)");
                $stmt->execute([$parent_id, $_SESSION['user_id'], $file_name, $system_path, $file_extension, $file_size]);
                $message = "تم رفع الملف بنجاح.";
            } else {
                json_response(['success' => false, 'message' => 'فشل نقل الملف إلى وجهته النهائية.']);
            }
            break;

        case 'rename':
            if (!has_permission('rename_files')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $id = (int)($_POST['id'] ?? 0);
            $new_name = trim($_POST['new_name'] ?? '');
            if (!$id || empty($new_name)) { json_response(['success' => false, 'message' => 'بيانات غير مكتملة.']); }
            $stmt = $pdo->prepare("UPDATE files SET file_name = ? WHERE id = ?");
            $stmt->execute([$new_name, $id]);
            $message = "تمت إعادة التسمية بنجاح.";
            break;

        
                default:
            json_response(['success' => false, 'message' => 'الإجراء المطلوب غير معروف.']);
            break;
    }
    
    $pdo->commit();
    json_response(['success' => true, 'message' => $message]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    log_error("File manager handler error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'حدث خطأ في قاعدة البيانات.']);
}
