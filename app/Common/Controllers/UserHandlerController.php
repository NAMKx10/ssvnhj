<?php
/**
 * app/Common/Controllers/UserHandlerController.php
 * 
 * هذا الملف هو المعالج المركزي لكل العمليات الخلفية المتعلقة بموديل المستخدمين.
 * وهو مقسم إلى قسمين رئيسيين:
 * 1. معالجات الطلبات العادية: تتعامل مع الحذف والإجراءات الجماعية وتنهي عملها بإعادة توجيه المستخدم.
 * 2. معالجات AJAX: تتعامل مع نماذج الإضافة والتعديل والإضافة الجماعية وترجع استجابة بصيغة JSON.
 */

global $pdo, $page;

// =================================================================
// القسم الأول: معالجة الطلبات العادية (التي تتطلب إعادة توجيه)
// =================================================================

if ($page === 'handle_user_delete' || $page === 'handle_users_batch_action') {

    // --- معالج الحذف الفردي ---
    if ($page === 'handle_user_delete') {
        $user_id = $_GET['id'] ?? 0;
        // لا تسمح بحذف المستخدم رقم 1 (Super Admin)
        if ($user_id != 1) { 
            soft_delete($pdo, 'users', $user_id);
        }
    }

    // --- معالج الإجراءات الجماعية ---
    if ($page === 'handle_users_batch_action') {
        $ids = $_POST['row_id'] ?? [];
        $action = $_POST['action'] ?? '';

        if (!empty($ids)) {
            // قم بتصفية ID رقم 1 (Super Admin) لمنع تعديله أو حذفه
            $filtered_ids = array_filter($ids, function($id) { return $id != 1; });

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
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($filtered_ids);
                }
            }
        }
    }

    // بعد تنفيذ أي إجراء عادي، أعد المستخدم إلى الصفحة السابقة
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=users'));
    exit();
}


// =================================================================
// القسم الثاني: معالجات AJAX (التي ترجع استجابة JSON)
// =================================================================

// جهز الهيدر ليكون دائمًا من نوع JSON
header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'حدث خطأ غير معروف.'];

try {
    // ابدأ المعاملة لضمان سلامة البيانات
    $pdo->beginTransaction();

    // --- معالج إضافة مستخدم جديد (من النافذة المنبثقة) ---
    if ($page === 'handle_user_add') {
        $sql = "INSERT INTO users (full_name, username, email, mobile, password, role_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['full_name'], 
            $_POST['username'], 
            $_POST['email'], 
            $_POST['mobile'],
            $_POST['password'], // كلمة مرور بنص عادي مؤقتًا
            $_POST['role_id'],
            $_POST['status']
        ]);
        $user_id = $pdo->lastInsertId();

        if (!empty($_POST['branches'])) {
            $branch_sql = "INSERT INTO user_branches (user_id, branch_id) VALUES (?, ?)";
            $branch_stmt = $pdo->prepare($branch_sql);
            foreach ($_POST['branches'] as $branch_id) { $branch_stmt->execute([$user_id, $branch_id]); }
        }
        $response = ['success' => true, 'message' => 'تمت إضافة المستخدم بنجاح.'];
    
    // --- معالج تعديل مستخدم حالي (من النافذة المنبثقة) ---
    } elseif ($page === 'handle_user_edit') {
        $user_id = $_POST['id'];
        $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, mobile = ?, role_id = ?, status = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['full_name'], $_POST['username'], $_POST['email'], $_POST['mobile'], $_POST['role_id'], $_POST['status'], $user_id]);
        
        if (!empty($_POST['password'])) {
            $pw_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $pw_stmt->execute([$_POST['password'], $user_id]);
        }
        
        $delete_stmt = $pdo->prepare("DELETE FROM user_branches WHERE user_id = ?");
        $delete_stmt->execute([$user_id]);
        if (!empty($_POST['branches'])) {
            $branch_sql = "INSERT INTO user_branches (user_id, branch_id) VALUES (?, ?)";
            $branch_stmt = $pdo->prepare($branch_sql);
            foreach ($_POST['branches'] as $branch_id) { $branch_stmt->execute([$user_id, $branch_id]); }
        }
        $response = ['success' => true, 'message' => 'تم تحديث بيانات المستخدم بنجاح.'];

    // --- معالج الإضافة الجماعية (من جدول Handsontable) ---
    } elseif ($page === 'handle_users_batch_add') {
        $data = json_decode(file_get_contents('php://input'), true);
        $users_data = $data['users_data'] ?? [];
        $saved_count = 0;
        
        if (!empty($users_data)) {
            $sql = "INSERT INTO users (full_name, username, password, role_id, status, email, mobile) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            // جلب الأدوار لتحويل الاسم إلى ID
            $roles_map_stmt = $pdo->query("SELECT role_name, id FROM roles");
            $roles_map = $roles_map_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            foreach ($users_data as $row) {
                // تجاهل الصفوف الفارغة
                if (empty(array_filter($row))) { continue; }
                
                $role_name = $row[3] ?? 'Data Entry';
                $role_id = $roles_map[$role_name] ?? 3; // قيمة افتراضية إذا لم يتم العثور على الدور

                $stmt->execute([
                    $row[0] ?? 'مستخدم جديد',       // full_name
                    $row[1] ?? 'user_' . uniqid(), // username
                    $row[2] ?? 'password',         // password
                    $role_id,                      // role_id
                    $row[4] ?? 'active',           // status
                    $row[5] ?? null,               // email
                    $row[6] ?? null                // mobile
                ]);
                $saved_count++;
            }
        }
        $response = ['success' => true, 'message' => "تم حفظ عدد {$saved_count} من المستخدمين بنجاح."];
    

    } elseif ($page === 'handle_users_batch_edit') { // ✨ تم وضعه في مكانه الصحيح هنا ✨
        $data = json_decode(file_get_contents('php://input'), true);
        $users_data = $data['users_data'] ?? [];
        $updated_count = 0;

        if (!empty($users_data)) {
            $sql = "UPDATE users SET full_name = ?, username = ?, role_id = ?, status = ?, email = ?, mobile = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            
            $roles_map_stmt = $pdo->query("SELECT role_name, id FROM roles");
            $roles_map = $roles_map_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            foreach ($users_data as $row) {
                if (empty($row[0])) continue; 
                
                $role_name = $row[3] ?? 'Data Entry';
                $role_id = $roles_map[$role_name] ?? 3;

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
}

echo json_encode($response);
exit();