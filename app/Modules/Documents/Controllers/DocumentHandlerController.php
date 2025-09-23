<?php
// app/Modules/Documents/Controllers/DocumentHandlerController.php (النسخة النهائية الكاملة)

global $pdo;

if (!function_exists('json_response')) { die("Core functions not loaded."); }

// تحديد الإجراء من المصادر المختلفة
$action = $_POST['form_action'] ?? $_GET['action'] ?? '';
$data = [];
if (empty($action) && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['form_action'] ?? '';
}

// --- معالجة الإجراءات العادية (الحذف والإجراءات الجماعية) ---
if (in_array($action, ['delete', 'activate', 'deactivate'])) {
    $ids = (array)($_GET['id'] ?? $_POST['ids'] ?? []);
    if (empty($ids)) { header("Location: index.php?page=documents"); exit(); }

    $required_permission = ($action === 'delete') ? 'delete_document' : 'edit_document';
    if (!has_permission($required_permission)) { die('Access Denied.'); }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = '';

    switch ($action) {
        case 'delete': $sql = "UPDATE documents SET deleted_at = NOW() WHERE id IN ($placeholders)"; break;
        case 'activate': $sql = "UPDATE documents SET status = 'active' WHERE id IN ($placeholders)"; break;
        case 'deactivate': $sql = "UPDATE documents SET status = 'inactive' WHERE id IN ($placeholders)"; break;
    }

    if ($sql) { try { $pdo->prepare($sql)->execute($ids); } catch (PDOException $e) { log_error("Document action error: " . $e->getMessage()); } }
    
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=documents'));
    exit();
}
// --- معالجة طلبات AJAX ---
try {
    $pdo->beginTransaction();
    $message = 'تم تنفيذ العملية بنجاح.';

    switch ($action) {
        case 'add_document':
            if (!has_permission('add_document')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $title = trim($_POST['document_title'] ?? '');
            if (empty($title)) { json_response(['success' => false, 'message' => 'اسم الوثيقة حقل إلزامي.']); }
            $stmt = $pdo->prepare("INSERT INTO documents (document_title, document_code, document_type_key, status, issue_date, expiry_date, file_link, reference_number_1, reference_number_2, document_content, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([ $title, $_POST['document_code'] ?: null, $_POST['document_type_key'], $_POST['status'] ?? 'active', $_POST['issue_date'] ?: null, $_POST['expiry_date'] ?: null, $_POST['file_link'] ?: null, $_POST['reference_number_1'] ?: null, $_POST['reference_number_2'] ?: null, $_POST['document_content'] ?: null, $_POST['notes'] ?: null ]);
            $document_id = $pdo->lastInsertId();
            $links = $_POST['links'] ?? [];
            if (!empty($links)) {
                $link_stmt = $pdo->prepare("INSERT INTO links (source_model, source_id, target_model, target_id) VALUES ('document', ?, ?, ?)");
                foreach ($links as $link) { if (!empty($link['target_model']) && !empty($link['target_id'])) { $link_stmt->execute([$document_id, $link['target_model'], (int)$link['target_id']]); } }
            }
            $message = "تمت إضافة الوثيقة بنجاح.";
            break;

        case 'edit_document':
            if (!has_permission('edit_document')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $doc_id = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['document_title'] ?? '');
            if (!$doc_id || empty($title)) { json_response(['success' => false, 'message' => 'بيانات غير مكتملة.']); }
            $stmt = $pdo->prepare("UPDATE documents SET document_title=?, document_code=?, document_type_key=?, status=?, issue_date=?, expiry_date=?, file_link=?, reference_number_1=?, reference_number_2=?, document_content=?, notes=? WHERE id=?");
            $stmt->execute([ $title, $_POST['document_code'] ?: null, $_POST['document_type_key'], $_POST['status'] ?? 'active', $_POST['issue_date'] ?: null, $_POST['expiry_date'] ?: null, $_POST['file_link'] ?: null, $_POST['reference_number_1'] ?: null, $_POST['reference_number_2'] ?: null, $_POST['document_content'] ?: null, $_POST['notes'] ?: null, $doc_id ]);
            $pdo->prepare("DELETE FROM links WHERE source_model = 'document' AND source_id = ?")->execute([$doc_id]);
            $links = $_POST['links'] ?? [];
            if (!empty($links)) {
                $link_stmt = $pdo->prepare("INSERT INTO links (source_model, source_id, target_model, target_id) VALUES ('document', ?, ?, ?)");
                foreach ($links as $link) { if (!empty($link['target_model']) && !empty($link['target_id'])) { $link_stmt->execute([$doc_id, $link['target_model'], (int)$link['target_id']]); } }
            }
            $message = "تم تحديث الوثيقة بنجاح.";
            break;

        default:
            json_response(['success' => false, 'message' => 'الإجراء المطلوب غير معروف.']);
            break;
    }
    
    $pdo->commit();
    json_response(['success' => true, 'message' => $message]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    log_error("Document handler error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'حدث خطأ في قاعدة البيانات.']);
}