<?php
// app/Common/Controllers/UsersController.php (النسخة النهائية الكاملة)

global $pdo;

// --- 1. الإعدادات والفلترة ---
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$current_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($current_page - 1) * $limit;

$filter_q = $_GET['q'] ?? null;
$filter_role_id = $_GET['role_id'] ?? null;
$filter_status = $_GET['status'] ?? null;

// --- 2. بناء الاستعلام الديناميكي ---
$where_conditions = ["users.deleted_at IS NULL"];
$params = [];

if ($filter_q) {
    $where_conditions[] = "(users.full_name LIKE ? OR users.username LIKE ? OR users.email LIKE ? OR users.mobile LIKE ?)";
    $params = array_merge($params, ["%$filter_q%", "%$filter_q%", "%$filter_q%", "%$filter_q%"]);
}
if ($filter_role_id) {
    $where_conditions[] = "users.role_id = ?";
    $params[] = $filter_role_id;
}
if ($filter_status) {
    $where_conditions[] = "users.status = ?";
    $params[] = $filter_status;
}

$sql_where = " WHERE " . implode(" AND ", $where_conditions);

// --- 3. جلب الإحصائيات ---
$stats_sql = "SELECT 
                (SELECT COUNT(*) FROM users WHERE deleted_at IS NULL) as total, 
                (SELECT COUNT(*) FROM users WHERE deleted_at IS NULL AND status = 'active') as active";
$stats = $pdo->query($stats_sql)->fetch(PDO::FETCH_ASSOC);
$stats['inactive'] = $stats['total'] - ($stats['active'] ?? 0);

// --- 4. حساب إجمالي السجلات للترقيم ---
$count_query = "SELECT COUNT(*) FROM users" . $sql_where;
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// --- 5. جلب بيانات المستخدمين للصفحة الحالية ---
$data_query = "SELECT users.*, roles.role_name 
               FROM users 
               LEFT JOIN roles ON users.role_id = roles.id 
               $sql_where 
               ORDER BY users.id DESC 
               LIMIT $limit OFFSET $offset";
$data_stmt = $pdo->prepare($data_query);
$data_stmt->execute($params);
$users = $data_stmt->fetchAll();

// --- 6. جلب بيانات الفلاتر (الأدوار والحالات) ---
$roles_list = $pdo->query("SELECT id, role_name FROM roles WHERE deleted_at IS NULL ORDER BY role_name")->fetchAll();
$statuses_list = $pdo->query("SELECT option_key, option_value FROM settings WHERE group_key = 'status' AND deleted_at IS NULL")->fetchAll(PDO::FETCH_KEY_PAIR);

// --- 7. استدعاء الواجهة ---
require_once ROOT_PATH . '/app/Common/Views/users/index.php';