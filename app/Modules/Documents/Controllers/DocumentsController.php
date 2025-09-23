<?php
// app/Modules/Documents/Controllers/DocumentsController.php (النسخة الكاملة والنهائية)

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
$where_conditions = ["d.deleted_at IS NULL"];
$params = [];
if ($filter_q !== '') {
    $where_conditions[] = "(d.document_title LIKE ? OR d.document_code LIKE ? OR d.reference_number_1 LIKE ? OR d.reference_number_2 LIKE ?)";
    $params = array_merge($params, ["%$filter_q%", "%$filter_q%", "%$filter_q%", "%$filter_q%"]);
}
if ($filter_type !== '') { $where_conditions[] = "d.document_type_key = ?"; $params[] = $filter_type; }
if ($filter_status !== '') { $where_conditions[] = "d.status = ?"; $params[] = $filter_status; }
$sql_where = " WHERE " . implode(" AND ", $where_conditions);

// 4. جلب الإحصائيات
$stats_sql = "
    SELECT
        (SELECT COUNT(*) FROM documents WHERE deleted_at IS NULL) as total,
        (SELECT COUNT(*) FROM documents WHERE deleted_at IS NULL AND expiry_date IS NOT NULL AND expiry_date < CURDATE()) as expired,
        (SELECT COUNT(*) FROM documents WHERE deleted_at IS NULL AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)) as expiring_soon
";
$stats = $pdo->query($stats_sql)->fetch(PDO::FETCH_ASSOC);

// 5. حساب إجمالي السجلات للترقيم
$count_query = "SELECT COUNT(d.id) FROM documents d" . $sql_where;
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_records = (int)$count_stmt->fetchColumn();
$total_pages = max(1, ceil($total_records / $limit));

// 6. جلب بيانات الوثائق للصفحة الحالية
$data_query = "
    SELECT 
        d.*,
        s.option_value as document_type_value,
        (SELECT COUNT(*) FROM links l WHERE l.source_model = 'document' AND l.source_id = d.id) as links_count
    FROM documents d
    LEFT JOIN settings s ON d.document_type_key = s.option_key
    JOIN setting_groups sg ON s.group_id = sg.id AND sg.group_key = 'document_types'
    {$sql_where}
    ORDER BY d.id DESC
    LIMIT ? OFFSET ?
";
$data_stmt = $pdo->prepare($data_query);
$data_stmt->execute(array_merge($params, [$limit, $offset]));
$documents = $data_stmt->fetchAll();

// 7. جلب بيانات قوائم الفلاتر
$types_list_stmt = $pdo->query("SELECT s.option_key, s.option_value FROM settings s JOIN setting_groups sg ON s.group_id = sg.id WHERE sg.group_key = 'document_types'");
$types_list = $types_list_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$statuses_list = ['active' => 'نشط', 'inactive' => 'غير نشط', 'expired' => 'منتهي الصلاحية'];

// 8. تمرير البيانات إلى الواجهة
require_once ROOT_PATH . '/app/Modules/Documents/Views/index.php';