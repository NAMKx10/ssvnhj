<?php
// app/Common/Controllers/RolesController.php

/**
 * متحكم عرض قائمة الأدوار والصلاحيات.
 */

global $pdo;

// --- 1. الإعدادات والمدخلات ---
$limit = 10;
$current_page = max(1, (int)($_GET['p'] ?? 1));
$offset = ($current_page - 1) * $limit;

// --- 2. جلب البيانات ---
$sql_where = " WHERE deleted_at IS NULL";

$count_query = "SELECT COUNT(*) FROM roles" . $sql_where;
$total_records = (int)$pdo->query($count_query)->fetchColumn();
$total_pages = max(1, ceil($total_records / $limit));

$data_query = "SELECT * FROM roles {$sql_where} ORDER BY id ASC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($data_query);
$stmt->execute([$limit, $offset]);
$roles = $stmt->fetchAll();

// --- 3. تمرير البيانات إلى الواجهة ---
require_once ROOT_PATH . '/app/Common/Views/roles/index.php';