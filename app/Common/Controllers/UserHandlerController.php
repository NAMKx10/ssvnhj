<?php
// app/Common/Controllers/UserHandlerController.php (النسخة النهائية الموحدة)

global $pdo, $page;

if (!function_exists('json_response')) { die("Error: Core helper functions are not loaded."); }

// --- تحديد الإجراء ---
$action = $_REQUEST['action'] ?? $_POST['form_action'] ?? '';
$data = [];
// إذا لم يتم العثور على إجراء، تحقق مما إذا كان هناك جسم طلب JSON
if (empty($action) && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['form_action'] ?? '';
}


// --- القسم الأول: معالجة الإجراءات العادية (التي لا تستخدم AJAX) ---
if ($page === 'handle_user_delete' || $page === 'handle_users_batch_action') {
    
    if ($page === 'handle_user_delete') {
        if (!has_permission('delete_user')) { die('Access Denied.'); }
        $user_id = (int)($_GET['id'] ?? 0);
        if ($user_id !== 1) { soft_delete($pdo, 'users', $user_id); }
    }
    
    if ($page === 'handle_users_batch_action') {
        $batch_action = $_POST['action'] ?? '';
        $ids = $_POST['ids'] ?? [];
        $required_permission = ($batch_action === 'soft_delete') ? 'delete_user' : 'edit_user';
        if (!has_permission($required_permission)) { die('Access Denied.'); }

        if (!empty($ids)) {
            $filtered_ids = array_filter($ids, fn($id) => (int)$id !== 1);
            if (!empty($filtered_ids)) {
                $placeholders = implode(',', array_fill(0, count($filtered_ids), '?'));
                $sql = '';
                switch ($batch_action) {
                    case 'soft_delete': $sql = "UPDATE users SET deleted_at = NOW() WHERE id IN ($placeholders)"; break;
                    case 'activate': $sql = "UPDATE users SET status = 'active' WHERE id IN ($placeholders)"; break;
                    case 'deactivate': $sql = "UPDATE users SET status = 'inactive' WHERE id IN ($placeholders)"; break;
                }
                if ($sql) { try { $pdo->prepare($sql)->execute($filtered_ids); } catch (PDOException $e) { if (function_exists('log_error')) { log_error('Batch action error: ' . $e->getMessage()); } } }
            }
        }
    }
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=users'));
    exit();
}


// --- القسم الثاني: معالجة طلبات AJAX ---
try {
    $pdo->beginTransaction();
    $message = 'تم تنفيذ العملية بنجاح.';

    switch ($action) {
        case 'add_user':
            if (!has_permission('add_user')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $full_name = trim($_POST['full_name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            if (!$full_name || !$username || !$email || !$password) { json_response(['success' => false, 'message' => 'يرجى تعبئة الحقول المطلوبة.']); }
            $sql = "INSERT INTO users (full_name, username, email, mobile, password, role_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$full_name, $username, $email, $_POST['mobile'] ?? null, $password, (int)($_POST['role_id'] ?? 3), $_POST['status'] ?? 'active']);
            $user_id = $pdo->lastInsertId();
            if (!empty($_POST['branches'])) {
                $branch_stmt = $pdo->prepare("INSERT INTO user_branches (user_id, branch_id) VALUES (?, ?)");
                foreach ($_POST['branches'] as $branch_id) { $branch_stmt->execute([$user_id, (int)$branch_id]); }
            }
            $message = "تمت إضافة المستخدم بنجاح.";
            break;

        case 'edit_user':
            if (!has_permission('edit_user')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $user_id = (int)($_POST['id'] ?? 0);
            $full_name = trim($_POST['full_name'] ?? '');
            if (!$user_id || empty($full_name)) { json_response(['success' => false, 'message' => 'بيانات غير مكتملة.']); }
            $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, mobile = ?, role_id = ?, status = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$full_name, $_POST['username'], $_POST['email'], $_POST['mobile'] ?? null, (int)($_POST['role_id'] ?? 3), $_POST['status'] ?? 'active', $user_id]);
            if (!empty($_POST['password'])) { $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$_POST['password'], $user_id]); }
            $pdo->prepare("DELETE FROM user_branches WHERE user_id = ?")->execute([$user_id]);
            if (!empty($_POST['branches'])) {
                $branch_stmt = $pdo->prepare("INSERT INTO user_branches (user_id, branch_id) VALUES (?, ?)");
                foreach ($_POST['branches'] as $branch_id) { $branch_stmt->execute([$user_id, (int)$branch_id]); }
            }
            $message = "تم تحديث بيانات المستخدم بنجاح.";
            break;
            
        case 'batch_add_user':
            if (!has_permission('add_user')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $users_data = $data['data'] ?? [];
            $saved_count = 0;
            if (!empty($users_data)) {
                $sql = "INSERT INTO users (full_name, username, password, role_id, status, email, mobile) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                foreach ($users_data as $row) {
                    if (empty(trim($row[1] ?? ''))) continue;
                    $role_id = get_role_id_from_name($pdo, $row[4] ?? 'Data Entry');
                    $stmt->execute([ $row[1], $row[2], $row[3] ?? '123456', $role_id, $row[5] ?? 'active', $row[6], $row[7] ]);
                    $saved_count++;
                }
            }
            $message = "تم حفظ عدد {$saved_count} من المستخدمين بنجاح.";
            break;

        case 'batch_edit_user':
            if (!has_permission('edit_user')) { json_response(['success' => false, 'message' => 'ليس لديك الصلاحية.']); }
            $users_data = $data['data'] ?? [];
            $updated_count = 0;
            if (!empty($users_data)) {
                $sql = "UPDATE users SET full_name=?, username=?, role_id=?, status=?, email=?, mobile=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                foreach ($users_data as $row) {
                    if (empty($row[0])) continue;
                    $role_id = get_role_id_from_name($pdo, $row[3] ?? 'Data Entry');
                    $stmt->execute([ $row[1], $row[2], $role_id, $row[4], $row[5], $row[6], $row[0] ]);
                    $updated_count++;
                }
            }
            $message = "تم تحديث عدد {$updated_count} من المستخدمين بنجاح.";
            break;
            
        default:
            json_response(['success' => false, 'message' => 'الإجراء المطلوب غير معروف.']);
            break;
    }
    
    $pdo->commit();
    json_response(['success' => true, 'message' => $message]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    $error_message = (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) ? 'اسم المستخدم أو الإيميل موجود بالفعل.' : 'خطأ في قاعدة البيانات.';
    log_error("User handler error: " . $e->getMessage());
    json_response(['success' => false, 'message' => $error_message]);
}