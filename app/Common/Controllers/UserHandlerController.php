<?php
/**
 * app/Common/Controllers/UserHandlerController.php
 * 
 * المعالج المركزي لكل العمليات الخلفية المتعلقة بموديل المستخدمين.
 * مقسم إلى:
 * 1. معالجات الطلبات العادية (الحذف، الإجراءات الجماعية)
 * 2. معالجات AJAX (الإضافة، التعديل، الإضافة والتعديل الجماعي)
 */

global $pdo, $page;

/**
 * دالة مساعدة لتحويل اسم الدور إلى ID (للاستخدام في الإضافة الجماعية)
 * @param array $roles_map مصفوفة الأدوار (role_name => id)
 * @param string $role_name اسم الدور
 * @return int
 */
function get_role_id($roles_map, $role_name) {
    return $roles_map[$role_name] ?? 3; // 3: Data Entry كقيمة افتراضية
}

// =================================================================
// القسم الأول: معالجة الطلبات العادية (إعادة توجيه بعد التنفيذ)
// =================================================================

if ($page === 'handle_user_delete' || $page === 'handle_users_batch_action') {

    // --- معالج الحذف الفردي ---
    if ($page === 'handle_user_delete') {
        $user_id = $_GET['id'] ?? 0;
        if ($user_id != 1) { // حماية Super Admin من الحذف
            soft_delete($pdo, 'users', $user_id);
        }
    }

    // --- معالج الإجراءات الجماعية ---
    if ($page === 'handle_users_batch_action') {
        $ids = $_POST['row_id'] ?? [];
        $action = $_POST['action'] ?? '';

        if (!empty($ids)) {
            $filtered_ids = array_filter($ids, fn($id) => $id != 1); // استبعاد Super Admin
            if (!empty($filtered_ids)) {
                $placeholders = implode(',', array_fill(0, count($filtered_ids), '?'));
                $sql = null;
                switch ($action) {
                    case 'soft_delete':
                        $sql = "UPDATE users SET deleted_at = NOW() WHERE id IN ({$placeholders})";
                        break;
                    case 'activate':
                        $sql = "UPDATE users SET status = 'active' WHERE id IN ({$placeholders})";
                        break;
                    case 'deactivate':
                        $sql = "UPDATE users SET status = 'inactive' WHERE id IN ({$placeholders})";
                        break;
                }
                if ($sql) {
                    try {
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($filtered_ids);
                    } catch (PDOException $e) {
                        // سجل الخطأ في لوج النظام
                        if (function_exists('log_error')) {
                            log_error('Batch action error: ' . $e->getMessage());
                        }
                    }
                }
            }
        }
    }

    // إعادة التوجيه بعد العملية
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=users'));
    exit();
}

// =================================================================
// القسم الثاني: معالجات AJAX (ترجع استجابة JSON)
// =================================================================

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'حدث خطأ غير معروف.'];

try {
    $pdo->beginTransaction();

    // --- إضافة مستخدم جديد ---
    if ($page === 'handle_user_add') {
        // تحقق أولي من المدخلات الأساسية
        $full_name = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $password = $_POST['password'] ?? '';
        $role_id = $_POST['role_id'] ?? 3;
        $status = $_POST['status'] ?? 'active';

        // تحقق أساسي (يمكنك التوسعة لاحقًا)
        if (!$full_name || !$username || !$email || !$password) {
            $response['message'] = 'يرجى تعبئة جميع الحقول المطلوبة.';
            echo json_encode($response); exit();
        }

        $sql = "INSERT INTO users (full_name, username, email, mobile, password, role_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $full_name, $username, $email, $mobile, $password, $role_id, $status
        ]);
        $user_id = $pdo->lastInsertId();

        // إضافة الفروع المسموح بها
        if (!empty($_POST['branches'])) {
            $branch_stmt = $pdo->prepare("INSERT INTO user_branches (user_id, branch_id) VALUES (?, ?)");
            foreach ($_POST['branches'] as $branch_id) {
                $branch_stmt->execute([$user_id, $branch_id]);
            }
        }
        $response = ['success' => true, 'message' => 'تمت إضافة المستخدم بنجاح.'];

    // --- تعديل مستخدم حالي ---
    } elseif ($page === 'handle_user_edit') {
        $user_id = $_POST['id'];
        $full_name = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $role_id = $_POST['role_id'] ?? 3;
        $status = $_POST['status'] ?? 'active';
        $password = $_POST['password'] ?? '';

        // تحقق أساسي
        if (!$user_id || !$full_name || !$username || !$email) {
            $response['message'] = 'بيانات غير مكتملة.';
            echo json_encode($response); exit();
        }

        $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, mobile = ?, role_id = ?, status = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$full_name, $username, $email, $mobile, $role_id, $status, $user_id]);

        // تحديث كلمة المرور إذا تم إدخالها
        if (!empty($password)) {
            $pw_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $pw_stmt->execute([$password, $user_id]);
        }

        // تحديث الفروع المسموح بها
        $delete_stmt = $pdo->prepare("DELETE FROM user_branches WHERE user_id = ?");
        $delete_stmt->execute([$user_id]);
        if (!empty($_POST['branches'])) {
            $branch_stmt = $pdo->prepare("INSERT INTO user_branches (user_id, branch_id) VALUES (?, ?)");
            foreach ($_POST['branches'] as $branch_id) {
                $branch_stmt->execute([$user_id, $branch_id]);
            }
        }
        $response = ['success' => true, 'message' => 'تم تحديث بيانات المستخدم بنجاح.'];

    // --- الإضافة الجماعية من Handsontable ---
    } elseif ($page === 'handle_users_batch_add') {
        $data = json_decode(file_get_contents('php://input'), true);
        $users_data = $data['users_data'] ?? [];
        $saved_count = 0;

        if (!empty($users_data)) {
            $sql = "INSERT INTO users (full_name, username, password, role_id, status, email, mobile) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            // جلب الأدوار مرة واحدة
            $roles_map = $pdo->query("SELECT role_name, id FROM roles")->fetchAll(PDO::FETCH_KEY_PAIR);

            foreach ($users_data as $row) {
                if (empty(array_filter($row))) continue; // تجاهل الصفوف الفارغة

                $role_id = get_role_id($roles_map, $row[3] ?? 'Data Entry');
                $stmt->execute([
                    $row[0] ?? 'مستخدم جديد',       // full_name
                    $row[1] ?? 'user_' . uniqid(), // username
                    $row[2] ?? 'password',         // password
                    $role_id,
                    $row[4] ?? 'active',
                    $row[5] ?? null,
                    $row[6] ?? null
                ]);
                $saved_count++;
            }
        }
        $response = ['success' => true, 'message' => "تم حفظ عدد {$saved_count} من المستخدمين بنجاح."];

    // --- التعديل الجماعي من Handsontable ---
    } elseif ($page === 'handle_users_batch_edit') {
        $data = json_decode(file_get_contents('php://input'), true);
        $users_data = $data['users_data'] ?? [];
        $updated_count = 0;

        if (!empty($users_data)) {
            $sql = "UPDATE users SET full_name = ?, username = ?, role_id = ?, status = ?, email = ?, mobile = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);

            $roles_map = $pdo->query("SELECT role_name, id FROM roles")->fetchAll(PDO::FETCH_KEY_PAIR);

            foreach ($users_data as $row) {
                if (empty($row[0])) continue; // يجب وجود ID

                $role_id = get_role_id($roles_map, $row[3] ?? 'Data Entry');
                $stmt->execute([
                    $row[1], // full_name
                    $row[2], // username
                    $role_id,
                    $row[4], // status
                    $row[5], // email
                    $row[6], // mobile
                    $row[0]  // id
                ]);
                $updated_count++;
            }
        }
        $response = ['success' => true, 'message' => "تم تحديث عدد {$updated_count} من المستخدمين بنجاح."];
    }

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
        $response['message'] = 'خطأ: أحد أسماء المستخدمين أو رسائل البريد الإلكتروني التي أدخلتها موجود بالفعل.';
    } else {
        $response['message'] = 'خطأ في قاعدة البيانات: ' . $e->getMessage();
    }
    // سجل الخطأ في اللوج إن وجدت الدالة
    if (function_exists('log_error')) {
        log_error('DB Error: ' . $e->getMessage());
    }
}

echo json_encode($response);
exit();