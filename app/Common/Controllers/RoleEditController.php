<?php
// app/Common/Controllers/RoleEditController.php

/**
 * متحكم تجهيز بيانات صفحة تعديل صلاحيات الدور.
 */

global $pdo;

// --- 1. التحقق من صحة معرّف الدور ---
$role_id = (int)($_GET['id'] ?? 0);
if (!$role_id) {
    header('Location: index.php?page=roles');
    exit();
}

$role_stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ? AND deleted_at IS NULL");
$role_stmt->execute([$role_id]);
$role = $role_stmt->fetch();
if (!$role) {
    // إذا لم يتم العثور على الدور، ارجع لصفحة الأدوار
    header('Location: index.php?page=roles');
    exit();
}

// --- 2. جلب كل الصلاحيات المتاحة، مجمعة حسب المجموعة ---
$all_permissions_stmt = $pdo->query("
    SELECT p.id, p.description, pg.group_name
    FROM permissions p
    JOIN permission_groups pg ON p.group_id = pg.id
    WHERE p.deleted_at IS NULL AND pg.deleted_at IS NULL
    ORDER BY pg.id, p.id
");

// إعادة ترتيب المصفوفة لتصبح 'اسم المجموعة' => [صلاحيات]
$all_permissions = [];
foreach($all_permissions_stmt->fetchAll(PDO::FETCH_ASSOC) as $perm) {
    $all_permissions[$perm['group_name']][] = $perm;
}

// --- 3. جلب الصلاحيات الممنوحة حاليًا لهذا الدور ---
$current_permissions_stmt = $pdo->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
$current_permissions_stmt->execute([$role_id]);
// جلب النتائج كـمصفوفة بسيطة من الأرقام [1, 5, 8] لسهولة التحقق
$current_permissions = $current_permissions_stmt->fetchAll(PDO::FETCH_COLUMN);

// --- 4. تمرير كل البيانات إلى الواجهة ---
require_once ROOT_PATH . '/app/Common/Views/roles/edit_view.php';