<?php
// app/Modules/Branches/Controllers/BranchHandlerController.php (النسخة النهائية المنقحة)

global $pdo;

if (!function_exists('json_response')) { die("Error: Core helper functions are not loaded."); }

// تحديد الإجراء
$action = $_REQUEST['action'] ?? $_POST['form_action'] ?? '';
$data = [];
if (empty($action) && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['form_action'] ?? '';
}

// معالجة الحذف والإجراءات الجماعية العادية
if (in_array($action, ['delete', 'activate', 'deactivate'])) {
    $required_permission = ($action === 'delete') ? 'delete_branch' : 'edit_branch';
    if (!has_permission($required_permission)) { die('Access Denied.'); }
    
    $ids = (array)($_GET['id'] ?? $_POST['ids'] ?? []);
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = '';
        switch ($action) {
            case 'delete': $sql = "UPDATE branches SET deleted_at = NOW() WHERE id IN ($placeholders)"; break;
            case 'activate': $sql = "UPDATE branches SET status = 'active' WHERE id IN ($placeholders)"; break;
            case 'deactivate': $sql = "UPDATE branches SET status = 'inactive' WHERE id IN ($placeholders)"; break;
        }
        if ($sql) { try { $pdo->prepare($sql)->execute($ids); } catch (PDOException $e) { log_error("Branch action error: " . $e->getMessage()); } }
    }
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=branches'));
    exit();
}


// معالجة طلبات AJAX
try {
    $pdo->beginTransaction();
    $message = 'تم تنفيذ العملية بنجاح.';

    switch ($action) {

        case 'add_branch':
            if (!has_permission('add_branch')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $branch_name = trim($_POST['branch_name'] ?? '');
            $branch_code = trim($_POST['branch_code'] ?? '');
            if (empty($branch_name) || empty($branch_code)) { json_response(['success' => false, 'message' => 'اسم الفرع والكود حقول إلزامية.']); }

            $stmt = $pdo->prepare("INSERT INTO branches (branch_name, branch_code, branch_type, status, phone, email, cr_number, tax_number, address, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([ $branch_name, $branch_code, $_POST['branch_type'] ?? 'operational', $_POST['status'] ?? 'active', $_POST['phone'] ?: null, $_POST['email'] ?: null, $_POST['cr_number'] ?: null, $_POST['tax_number'] ?: null, $_POST['address'] ?: null, $_POST['notes'] ?: null ]);
            $branch_id = $pdo->lastInsertId();

            $owners = (array)($_POST['owners'] ?? []);
            if (!empty($owners)) {
                $owner_stmt = $pdo->prepare("INSERT INTO branch_owners (branch_id, contact_id) VALUES (?, ?)");
                foreach ($owners as $owner_id) { $owner_stmt->execute([$branch_id, (int)$owner_id]); }
            }
            $message = "تمت إضافة الفرع بنجاح.";
            break;

        case 'edit_branch':
            if (!has_permission('edit_branch')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $branch_id = (int)($_POST['id'] ?? 0);
            $branch_name = trim($_POST['branch_name'] ?? '');
            $branch_code = trim($_POST['branch_code'] ?? '');
            if (!$branch_id || empty($branch_name) || empty($branch_code)) { json_response(['success' => false, 'message' => 'بيانات غير مكتملة.']); }

            // 1. تحديث الجدول الأساسي
            $stmt = $pdo->prepare("UPDATE branches SET branch_name=?, branch_code=?, branch_type=?, status=?, phone=?, email=?, cr_number=?, tax_number=?, address=?, notes=? WHERE id=?");
            $stmt->execute([ $branch_name, $branch_code, $_POST['branch_type'] ?? 'operational', $_POST['status'] ?? 'active', $_POST['phone'] ?: null, $_POST['email'] ?: null, $_POST['cr_number'] ?: null, $_POST['tax_number'] ?: null, $_POST['address'] ?: null, $_POST['notes'] ?: null, $branch_id ]);

            // 2. تحديث الملاك (حذف القديم وإضافة الجديد)
            $pdo->prepare("DELETE FROM branch_owners WHERE branch_id = ?")->execute([$branch_id]);
            $owners = (array)($_POST['owners'] ?? []);
            if (!empty($owners)) {
                $owner_stmt = $pdo->prepare("INSERT INTO branch_owners (branch_id, contact_id) VALUES (?, ?)");
                foreach ($owners as $owner_id) { $owner_stmt->execute([$branch_id, (int)$owner_id]); }
            }
            $message = "تم تحديث الفرع بنجاح.";
            break;


                    case 'batch_add_branch':
            if (!has_permission('add_branch')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $branches_data = $data['data'] ?? [];
            $saved_count = 0;
            if (!empty($branches_data)) {
                $sql = "INSERT INTO branches (branch_name, branch_code, branch_type, status, cr_number, tax_number, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                foreach ($branches_data as $row) {
                    if (empty(trim($row[1] ?? '')) || empty(trim($row[2] ?? ''))) continue;
                    $stmt->execute([ $row[1], $row[2], $row[3] ?? 'operational', $row[4] ?? 'active', $row[5], $row[6], $row[7], $row[8] ]);
                    $saved_count++;
                }
            }
            $message = "تم حفظ عدد {$saved_count} من الفروع بنجاح.";
            break;

                case 'batch_add_branch':
            if (!has_permission('add_branch')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $branches_data = $data['data'] ?? [];
            $saved_count = 0;
            if (!empty($branches_data)) {
                $sql = "INSERT INTO branches (branch_name, branch_code, branch_type, status, cr_number, tax_number, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                foreach ($branches_data as $row) {
                    if (empty(trim($row[1] ?? '')) || empty(trim($row[2] ?? ''))) continue;
                    $stmt->execute([ $row[1], $row[2], $row[3] ?? 'operational', $row[4] ?? 'active', $row[5], $row[6], $row[7], $row[8] ]);
                    $saved_count++;
                }
            }
            $message = "تم حفظ عدد {$saved_count} من الفروع بنجاح.";
            break;

        case 'batch_edit_branch':
            if (!has_permission('edit_branch')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $branches_data = $data['data'] ?? [];
            $updated_count = 0;
            if (!empty($branches_data)) {
                $sql = "UPDATE branches SET branch_name=?, branch_code=?, branch_type=?, status=?, cr_number=?, tax_number=?, phone=?, email=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                foreach ($branches_data as $row) {
                    if (empty($row[0])) continue;
                    $stmt->execute([ $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[0] ]);
                    $updated_count++;
                }
            }
            $message = "تم تحديث عدد {$updated_count} من الفروع بنجاح.";
            break;

        default:
            json_response(['success' => false, 'message' => 'الإجراء المطلوب غير معروف.']);
            break;
    }
    
    $pdo->commit();
    json_response(['success' => true, 'message' => $message]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    $error_message = (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) ? 'كود الفرع موجود بالفعل.' : 'خطأ في قاعدة البيانات.';
    log_error("Branch handler error: " . $e->getMessage());
    json_response(['success' => false, 'message' => $error_message]);
}
