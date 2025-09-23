<?php
// app/Modules/Contacts/Controllers/ContactHandlerController.php (النسخة النهائية مع قراءة JSON الصحيحة)

global $pdo;

if (!function_exists('json_response')) { die("Error: Core helper functions are not loaded."); }

// --- تحديد الإجراء ---
// أولاً، تحقق من الطلبات العادية (GET/POST)
$action = $_REQUEST['action'] ?? $_POST['form_action'] ?? '';
$data = [];

// إذا لم يتم العثور على إجراء، تحقق مما إذا كان هناك جسم طلب JSON
if (empty($action) && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['form_action'] ?? '';
}


// --- القسم الأول: معالجة الإجراءات العادية (التي لا تستخدم AJAX) ---
if (in_array($action, ['delete', 'activate', 'deactivate'])) {
    $ids = (array)($_GET['id'] ?? $_POST['ids'] ?? []);
    if (empty($ids)) { header("Location: index.php?page=contacts"); exit(); }
    $required_permission = ($action === 'delete') ? 'delete_contact' : 'edit_contact';
    if (!has_permission($required_permission)) { die('Access Denied.'); }
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = '';
    switch ($action) {
        case 'delete': $sql = "UPDATE contacts SET deleted_at = NOW() WHERE id IN ($placeholders)"; break;
        case 'activate': $sql = "UPDATE contacts SET status = 'active' WHERE id IN ($placeholders)"; break;
        case 'deactivate': $sql = "UPDATE contacts SET status = 'inactive' WHERE id IN ($placeholders)"; break;
    }
    if ($sql) { try { $pdo->prepare($sql)->execute($ids); } catch (PDOException $e) { log_error("Contact action error: " . $e->getMessage()); } }
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=contacts'));
    exit();
}

// --- معالجة طلبات AJAX ---
try {
    $pdo->beginTransaction();

    switch ($action) {
        case 'add_contact':
            if (!has_permission('add_contact')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $full_name = trim($_POST['full_name'] ?? '');
            if (empty($full_name)) { json_response(['success' => false, 'message' => 'اسم جهة الاتصال مطلوب.']); }
            $stmt = $pdo->prepare("INSERT INTO contacts (contact_type, full_name, short_code, id_number, tax_number, primary_phone, primary_email, address, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([ $_POST['contact_type'], $full_name, $_POST['short_code'] ?: null, $_POST['id_number'] ?: null, $_POST['tax_number'] ?: null, $_POST['primary_phone'] ?: null, $_POST['primary_email'] ?: null, $_POST['address'] ?: null, $_POST['notes'] ?: null, $_POST['status'] ?? 'active' ]);
            $contact_id = $pdo->lastInsertId();
            $roles = (array)($_POST['roles'] ?? []);
            $role_stmt = $pdo->prepare("INSERT INTO contact_roles (contact_id, role_key) VALUES (?, ?)");
            foreach ($roles as $role_key) { $role_stmt->execute([$contact_id, $role_key]); }
            $branches = (array)($_POST['branches'] ?? []);
            if (!empty($branches)) {
                $branch_stmt = $pdo->prepare("INSERT INTO contact_branches (contact_id, branch_id) VALUES (?, ?)");
                foreach ($branches as $branch_id) { $branch_stmt->execute([$contact_id, (int)$branch_id]); }
            }
            break;

        case 'edit_contact':
            if (!has_permission('edit_contact')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $contact_id = (int)($_POST['id'] ?? 0);
            $full_name = trim($_POST['full_name'] ?? '');
            if (!$contact_id || empty($full_name)) { json_response(['success' => false, 'message' => 'بيانات غير مكتملة.']); }
            $stmt = $pdo->prepare("UPDATE contacts SET contact_type=?, full_name=?, short_code=?, id_number=?, tax_number=?, primary_phone=?, primary_email=?, address=?, notes=?, status=? WHERE id=?");
            $stmt->execute([ $_POST['contact_type'], $full_name, $_POST['short_code'] ?: null, $_POST['id_number'] ?: null, $_POST['tax_number'] ?: null, $_POST['primary_phone'] ?: null, $_POST['primary_email'] ?: null, $_POST['address'] ?: null, $_POST['notes'] ?: null, $_POST['status'] ?? 'active', $contact_id ]);
            $pdo->prepare("DELETE FROM contact_roles WHERE contact_id = ?")->execute([$contact_id]);
            $roles = (array)($_POST['roles'] ?? []);
            if (!empty($roles)) {
                $role_stmt = $pdo->prepare("INSERT INTO contact_roles (contact_id, role_key) VALUES (?, ?)");
                foreach ($roles as $role_key) { $role_stmt->execute([$contact_id, $role_key]); }
            }
            $pdo->prepare("DELETE FROM contact_branches WHERE contact_id = ?")->execute([$contact_id]);
            $branches = (array)($_POST['branches'] ?? []);
            if (!empty($branches)) {
                $branch_stmt = $pdo->prepare("INSERT INTO contact_branches (contact_id, branch_id) VALUES (?, ?)");
                foreach ($branches as $branch_id) { $branch_stmt->execute([$contact_id, (int)$branch_id]); }
            }
            break;

                case 'batch_add_contact':
            if (!has_permission('add_contact')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $contacts_data = $data['data'] ?? [];
            $saved_count = 0;
            if (!empty($contacts_data)) {
                $sql = "INSERT INTO contacts (full_name, short_code, contact_type, status, id_number, tax_number, primary_phone, primary_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                foreach ($contacts_data as $row) {
                    if (empty(trim($row[1] ?? ''))) continue;
                    $stmt->execute([ $row[1], $row[2], $row[3] ?? 'فرد', $row[4] ?? 'active', $row[5], $row[6], $row[7], $row[8] ]);
                    $saved_count++;
                }
            }
            $message = "تم حفظ عدد {$saved_count} من جهات الاتصال بنجاح.";
            break;

        case 'batch_edit_contact':
            if (!has_permission('edit_contact')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $contacts_data = $data['data'] ?? [];
            $updated_count = 0;
            if (!empty($contacts_data)) {
                $sql = "UPDATE contacts SET full_name=?, short_code=?, contact_type=?, status=?, id_number=?, tax_number=?, primary_phone=?, primary_email=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                foreach ($contacts_data as $row) {
                    if (empty($row[0])) continue;
                    $stmt->execute([ $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[0] ]);
                    $updated_count++;
                }
            }
            $message = "تم تحديث عدد {$updated_count} من جهات الاتصال بنجاح.";
            break;
        
        default:
            json_response(['success' => false, 'message' => 'الإجراء المطلوب غير معروف.']);
            break;
    }


    $pdo->commit();
    json_response(['success' => true, 'message' => $message]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    $error_message = (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) ? 'الكود المختصر موجود بالفعل.' : 'خطأ في قاعدة البيانات.';
    log_error("Contact handler error: " . $e->getMessage());
    json_response(['success' => false, 'message' => $error_message]);
}
