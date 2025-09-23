<?php
// app/Modules/Branches/Controllers/BranchesController.php (النسخة الكاملة والنهائية)

global $pdo;

// 1. الإعدادات والمدخلات
$limit = (int)($_GET['limit'] ?? 10);
$current_page = max(1, (int)($_GET['p'] ?? 1));
$offset = ($current_page - 1) * $limit;

// 2. جلب الفلاتر وتأمينها
$filter_q = trim($_GET['q'] ?? '');
$filter_type = trim($_GET['type'] ?? '');
$filter_status = trim($_GET['status'] ?? '');

// 3. بناء شروط الفلاتر الديناميكية
$where_conditions = ["b.deleted_at IS NULL"];
$params = [];
if ($filter_q !== '') {
    $where_conditions[] = "(b.branch_name LIKE ? OR b.branch_code LIKE ? OR b.phone LIKE ? OR b.email LIKE ? OR b.cr_number LIKE ?)";
    $params = array_merge($params, ["%$filter_q%", "%$filter_q%", "%$filter_q%", "%$filter_q%", "%$filter_q%"]);
}
if ($filter_type !== '') { $where_conditions[] = "b.branch_type = ?"; $params[] = $filter_type; }
if ($filter_status !== '') { $where_conditions[] = "b.status = ?"; $params[] = $filter_status; }
$sql_where = " WHERE " . implode(" AND ", $where_conditions);

// 4. جلب الإحصائيات
$stats_sql = "
    SELECT
        (SELECT COUNT(*) FROM branches WHERE deleted_at IS NULL) as total,
        (SELECT COUNT(*) FROM branches WHERE deleted_at IS NULL AND status = 'active') as active,
        (SELECT COUNT(*) FROM branches WHERE deleted_at IS NULL AND status = 'inactive') as inactive
";
$stats = $pdo->query($stats_sql)->fetch(PDO::FETCH_ASSOC);

// 5. حساب إجمالي السجلات للترقيم
$count_query = "SELECT COUNT(*) FROM branches b" . $sql_where;
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_records = (int)$count_stmt->fetchColumn();
$total_pages = max(1, ceil($total_records / $limit));

// 6. جلب بيانات الفروع للصفحة الحالية
$data_query = "
    SELECT 
        b.*,
        s.option_value as branch_type_value,
        GROUP_CONCAT(c.full_name SEPARATOR ', ') as owners
    FROM branches b
    LEFT JOIN settings s ON b.branch_type = s.option_key AND s.group_id = 'branch_types'
    LEFT JOIN branch_owners bo ON b.id = bo.branch_id
    LEFT JOIN contacts c ON bo.contact_id = c.id
    {$sql_where}
    GROUP BY b.id
    ORDER BY b.id DESC
    LIMIT ? OFFSET ?
";
$data_stmt = $pdo->prepare($data_query);
$data_stmt->execute(array_merge($params, [$limit, $offset]));
$branches = $data_stmt->fetchAll();

// 7. جلب بيانات قوائم الفلاتر
$types_list_stmt = $pdo->query("SELECT option_key, option_value FROM settings WHERE group_id = 'branch_types'");
$types_list = $types_list_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$statuses_list = ['active' => 'نشط', 'inactive' => 'غير نشط'];

// 8. تمرير البيانات إلى الواجهة
require_once ROOT_PATH . '/app/Modules/Branches/Views/index.php';