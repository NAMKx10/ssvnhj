<?php
// app/Common/Controllers/RoleHandlerController.php

global $pdo;

$action = $_REQUEST['action'] ?? $_GET['page'] ?? '';

// --- معالجة طلبات AJAX (إضافة وتعديل) ---
if (!empty($_POST)) {
    try {
        switch ($action) {
            case 'handle_role_add':
                $pdo->beginTransaction();
                $role_name = trim($_POST['role_name'] ?? '');
                if (empty($role_name)) { json_response(['success' => false, 'message' => 'اسم الدور مطلوب.']); }
                
                $stmt = $pdo->prepare("INSERT INTO roles (role_name, description) VALUES (?, ?)");
                $stmt->execute([$role_name, $_POST['description'] ?? null]);
                $pdo->commit();
                json_response(['success' => true, 'message' => 'تمت إضافة الدور بنجاح.']);
                break;

            case 'handle_role_edit':
                $pdo->beginTransaction();
                $role_id = (int)($_POST['role_id'] ?? 0);
                $permissions = (array)($_POST['permissions'] ?? []);

                if (!$role_id) { json_response(['success' => false, 'message' => 'معرف الدور مفقود.']); }
                
                // لا يمكن تعديل صلاحيات المدير الخارق
                if ($role_id === 1) {
                    json_response(['success' => false, 'message' => 'لا يمكن تعديل صلاحيات المدير الخارق.']);
                }

                // حذف الصلاحيات القديمة أولاً
                $delete_stmt = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?");
                $delete_stmt->execute([$role_id]);

                // إضافة الصلاحيات الجديدة
                if (!empty($permissions)) {
                    $insert_stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
                    foreach ($permissions as $permission_id) {
                        $insert_stmt->execute([$role_id, (int)$permission_id]);
                    }
                }
                $pdo->commit();
                // نستخدم استجابة خاصة هنا لإعادة التوجيه بدلاً من تحديث الصفحة
                json_response(['success' => true, 'message' => 'تم حفظ الصلاحيات بنجاح.', 'redirect' => 'index.php?page=roles']);
                break;

                case 'handle_role_edit_details':
                $pdo->beginTransaction();
                $id = (int)($_POST['id'] ?? 0);
                $role_name = trim($_POST['role_name'] ?? '');
                $description = trim($_POST['description'] ?? '');

                if (!$id || empty($role_name)) {
                    json_response(['success' => false, 'message' => 'بيانات غير مكتملة.']);
                }

                // لا تقم بتحديث اسم الأدوار الأساسية
                if ($id <= 3) {
                    $stmt = $pdo->prepare("UPDATE roles SET description = ? WHERE id = ?");
                    $stmt->execute([$description, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE roles SET role_name = ?, description = ? WHERE id = ?");
                    $stmt->execute([$role_name, $description, $id]);
                }
                
                $pdo->commit();
                json_response(['success' => true, 'message' => 'تم تحديث بيانات الدور بنجاح.']);
                break;
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        log_error("Role handler error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'حدث خطأ في قاعدة البيانات.']);
    }
}


// --- معالجة طلبات الحذف (روابط عادية) ---
if ($action === 'handle_role_delete') {
    $id = (int)($_GET['id'] ?? 0);
    // حماية الأدوار الأساسية من الحذف
    if ($id > 3) {
        soft_delete($pdo, 'roles', $id);
    }
    header("Location: index.php?page=roles");
    exit();
}