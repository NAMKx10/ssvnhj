<?php
/**
 * app/Common/Controllers/UserHandlerController.php
 * 
 * المعالج المركزي - النسخة النهائية والنظيفة
 */

global $pdo, $page;

// =================================================================
// القسم الأول: معالجة الطلبات العادية (إعادة توجيه)
// =================================================================
if ($page === 'handle_user_delete' || $page === 'handle_users_batch_action') {
    if ($page === 'handle_user_delete') {
        $user_id = (int)($_GET['id'] ?? 0);
        if ($user_id !== 1) { 
            soft_delete($pdo, 'users', $user_id); 
        }
    }
    
    if ($page === 'handle_users_batch_action') {
        $ids = $_POST['row_id'] ?? [];
        $action = $_POST['action'] ?? '';
        if (!empty($ids)) {
            $filtered_ids = array_filter($ids, fn($id) => (int)$id !== 1);
            if (!empty($filtered_ids)) {
                $placeholders = implode(',', array_fill(0, count($filtered_ids), '?'));
                $sql = '';
                switch ($action) {
                    case 'soft_delete': $sql = "UPDATE users SET deleted_at = NOW() WHERE id IN ($placeholders)"; break;
                    case 'activate': $sql = "UPDATE users SET status = 'active' WHERE id IN ($placeholders)"; break;
                    case 'deactivate': $sql = "UPDATE users SET status = 'inactive' WHERE id IN ($placeholders)"; break;
                }
                if ($sql) {
                    try {
                        $pdo->prepare($sql)->execute($filtered_ids);
                    } catch (PDOException $e) {
                        if (function_exists('log_error')) { log_error('Batch action error: ' . $e->getMessage()); }
                    }
                }
            }
        }
    }
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=users'));
    exit();
}

// =================================================================
// القسم الثاني: معالجات AJAX (استجابة JSON)
// =================================================================

try {
    $pdo->beginTransaction();

    if ($page === 'handle_user_add') {
        $full_name = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$full_name || !$username || !$email || !$password) {
            json_response(['success' => false, 'message' => 'يرجى تعبئة جميع الحقول المطلوبة.']);
        }

        $sql = "INSERT INTO users (full_name, username, email, mobile, password, role_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$full_name, $username, $email, $_POST['mobile'] ?? null, $password, (int)($_POST['role_id'] ?? 3), $_POST['status'] ?? 'active']);
        $user_id = $pdo->lastInsertId();

        if (!empty($_POST['branches'])) {
            $branch_stmt = $pdo->prepare("INSERT INTO user_branches (user_id, branch_id) VALUES (?, ?)");
            foreach ($_POST['branches'] as $branch_id) {
                $branch_stmt->execute([$user_id, (int)$branch_id]);
            }
        }
        
        $pdo->commit();
        json_response(['success' => true, 'message' => 'تمت إضافة المستخدم بنجاح.']);
    }

    if ($page === 'handle_user_edit') {
        $user_id = (int)($_POST['id'] ?? 0);
        $full_name = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!$user_id || !$full_name || !$username || !$email) {
            json_response(['success' => false, 'message' => 'بيانات غير مكتملة أو معرّف المستخدم مفقود.']);
        }

        $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, mobile = ?, role_id = ?, status = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$full_name, $username, $email, $_POST['mobile'] ?? null, (int)($_POST['role_id'] ?? 3), $_POST['status'] ?? 'active', $user_id]);

        if (!empty($_POST['password'])) {
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$_POST['password'], $user_id]);
        }

        $pdo->prepare("DELETE FROM user_branches WHERE user_id = ?")->execute([$user_id]);
        if (!empty($_POST['branches'])) {
            $branch_stmt = $pdo->prepare("INSERT INTO user_branches (user_id, branch_id) VALUES (?, ?)");
            foreach ($_POST['branches'] as $branch_id) {
                $branch_stmt->execute([$user_id, (int)$branch_id]);
            }
        }
        
        $pdo->commit();
        json_response(['success' => true, 'message' => 'تم تحديث بيانات المستخدم بنجاح.']);
    }

    if ($page === 'handle_users_batch_add') {
        $data = json_decode(file_get_contents('php://input'), true);
        $users_data = $data['users_data'] ?? [];
        $saved_count = 0;
        if (!empty($users_data)) {
            $sql = "INSERT INTO users (full_name, username, password, role_id, status, email, mobile) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            foreach ($users_data as $row) {
                if (empty(array_filter($row))) continue;
                $role_id = get_role_id_from_name($pdo, $row[3] ?? 'Data Entry');
                $stmt->execute([$row[0]??'مستخدم جديد', $row[1]??'user_'.uniqid(), $row[2]??'password', $role_id, $row[4]??'active', $row[5]??null, $row[6]??null]);
                $saved_count++;
            }
        }
        $pdo->commit();
        json_response(['success' => true, 'message' => "تم حفظ عدد {$saved_count} من المستخدمين بنجاح."]);
    }
    
    if ($page === 'handle_users_batch_edit') {
        $data = json_decode(file_get_contents('php://input'), true);
        $users_data = $data['users_data'] ?? [];
        $updated_count = 0;
        if (!empty($users_data)) {
            $sql = "UPDATE users SET full_name = ?, username = ?, role_id = ?, status = ?, email = ?, mobile = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            foreach ($users_data as $row) {
                if (empty($row[0])) continue;
                $role_id = get_role_id_from_name($pdo, $row[3] ?? 'Data Entry');
                $stmt->execute([$row[1], $row[2], $role_id, $row[4], $row[5], $row[6], $row[0]]);
                $updated_count++;
            }
        }
        $pdo->commit();
        json_response(['success' => true, 'message' => "تم تحديث عدد {$updated_count} من المستخدمين بنجاح."]);
    }

} catch (PDOException $e) {
    $pdo->rollBack();
    $message = (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) ? 'اسم المستخدم أو الإيميل موجود بالفعل.' : 'خطأ في قاعدة البيانات.';
    if (function_exists('log_error')) { log_error('DB Error: ' . $e->getMessage()); }
    json_response(['success' => false, 'message' => $message]);
}

// Fallback in case no page matched
json_response(['success' => false, 'message' => 'الإجراء المطلوب غير معروف.']);