<?php
// app/Common/Controllers/PermissionsController.php

/**
 * متحكم عرض وإدارة الصلاحيات والمجموعات.
 */

global $pdo;

// --- 1. جلب كل المجموعات مع عدد الصلاحيات في كل منها ---
$groups_stmt = $pdo->query("
    SELECT pg.*, COUNT(p.id) as permissions_count
    FROM permission_groups pg
    LEFT JOIN permissions p ON pg.id = p.group_id AND p.deleted_at IS NULL
    WHERE pg.deleted_at IS NULL
    GROUP BY pg.id
    ORDER BY pg.id ASC
");
$groups = $groups_stmt->fetchAll(PDO::FETCH_ASSOC);

// --- 2. تحديد المجموعة النشطة ---
// إذا لم يتم تحديد مجموعة في الرابط، اختر أول مجموعة في القائمة
$active_group_id = (int)($_GET['group_id'] ?? ($groups[0]['id'] ?? 0));

// --- 3. جلب صلاحيات المجموعة النشطة ---
$permissions = [];
if ($active_group_id) {
    $permissions_stmt = $pdo->prepare("SELECT * FROM permissions WHERE group_id = ? AND deleted_at IS NULL ORDER BY id ASC");
    $permissions_stmt->execute([$active_group_id]);
    $permissions = $permissions_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- 4. جلب بيانات المجموعة النشطة للعرض في رأس البطاقة ---
$active_group = null;
foreach ($groups as $group) {
    if ($group['id'] == $active_group_id) {
        $active_group = $group;
        break;
    }
}

// --- 5. تمرير البيانات إلى الواجهة ---
require_once ROOT_PATH . '/app/Common/Views/permissions/index.php';