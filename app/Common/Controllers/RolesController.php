<?php
// app/Common/Controllers/RolesController.php (النسخة المطورة)

global $pdo;

// --- 1. الإعدادات ---
$limit = 10;
$current_page = max(1, (int)($_GET['p'] ?? 1));
$offset = ($current_page - 1) * $limit;

// --- 2. جلب البيانات مع الإحصائيات الجديدة ---
$sql_where = " WHERE r.deleted_at IS NULL";

// استعلام العدّ
$count_query = "SELECT COUNT(*) FROM roles r" . $sql_where;
$total_records = (int)$pdo->query($count_query)->fetchColumn();
$total_pages = max(1, ceil($total_records / $limit));

// استعلام جلب البيانات المطور
// لقد أضفنا COUNT لـ up.user_id و rp.permission_id
$data_query = "
    SELECT 
        r.*, 
        COUNT(DISTINCT u.id) as users_count,
        COUNT(DISTINCT rp.permission_id) as permissions_count
    FROM roles r
    LEFT JOIN users u ON r.id = u.role_id AND u.deleted_at IS NULL
    LEFT JOIN role_permissions rp ON r.id = rp.role_id
    {$sql_where} 
    GROUP BY r.id
    ORDER BY r.id ASC 
    LIMIT ? OFFSET ?
";
$stmt = $pdo->prepare($data_query);
$stmt->execute([$limit, $offset]);
$roles = $stmt->fetchAll();

// --- 3. تمرير البيانات إلى الواجهة ---
require_once ROOT_PATH . '/app/Common/Views/roles/index.php';