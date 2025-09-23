<?php
// app/Modules/Contacts/Controllers/ContactsController.php (النسخة المطورة)

global $pdo;

// --- 1. الإعدادات والتحقق من المدخلات ---
$limit = 10;
$current_page = max(1, (int)($_GET['p'] ?? 1));
$offset = ($current_page - 1) * $limit;

// جلب الفلاتر من الرابط وتأمينها
$filter_q = trim($_GET['q'] ?? '');
$filter_role = trim($_GET['role'] ?? '');
$filter_type = trim($_GET['type'] ?? '');
$filter_status = trim($_GET['status'] ?? '');

// --- 2. بناء شروط الفلاتر بشكل ديناميكي وآمن ---
$where_conditions = ["c.deleted_at IS NULL"];
$params = [];

if ($filter_q !== '') {
    $where_conditions[] = "(c.full_name LIKE ? OR c.short_code LIKE ? OR c.primary_phone LIKE ? OR c.primary_email LIKE ? OR c.id_number LIKE ? OR c.tax_number LIKE ?)";
    $params = array_merge($params, ["%$filter_q%", "%$filter_q%", "%$filter_q%", "%$filter_q%", "%$filter_q%", "%$filter_q%"]);
}
if ($filter_role !== '') {
    // هذا الفلتر يتطلب JOIN إضافي
    $where_conditions[] = "EXISTS (SELECT 1 FROM contact_roles cr WHERE cr.contact_id = c.id AND cr.role_key = ?)";
    $params[] = $filter_role;
}
if ($filter_type !== '') {
    $where_conditions[] = "c.contact_type = ?";
    $params[] = $filter_type;
}
if ($filter_status !== '') {
    $where_conditions[] = "c.status = ?";
    $params[] = $filter_status;
}

$sql_where = " WHERE " . implode(" AND ", $where_conditions);

// --- 3. جلب الإحصائيات ---
$stats_sql = "
    SELECT
        (SELECT COUNT(*) FROM contacts WHERE deleted_at IS NULL) as total,
        (SELECT COUNT(DISTINCT contact_id) FROM contact_roles WHERE role_key = 'client') as clients,
        (SELECT COUNT(DISTINCT contact_id) FROM contact_roles WHERE role_key = 'supplier') as suppliers,
        (SELECT COUNT(DISTINCT contact_id) FROM contact_roles WHERE role_key = 'employee') as employees
";
$stats = $pdo->query($stats_sql)->fetch(PDO::FETCH_ASSOC);

// --- 4. حساب إجمالي السجلات للترقيم (بعد تطبيق الفلاتر) ---
$count_query = "SELECT COUNT(c.id) FROM contacts c" . $sql_where;
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_records = (int)$count_stmt->fetchColumn();
$total_pages = max(1, ceil($total_records / $limit));

// --- 5. جلب بيانات جهات الاتصال للصفحة الحالية ---
$data_query = "
    SELECT 
        c.*,
        (SELECT GROUP_CONCAT(cr.role_key) FROM contact_roles cr WHERE cr.contact_id = c.id) as roles,
        (SELECT GROUP_CONCAT(b.branch_name SEPARATOR ', ') FROM contact_branches cb JOIN branches b ON cb.branch_id = b.id WHERE cb.contact_id = c.id) as branches
    FROM contacts c
    {$sql_where}
    ORDER BY c.id DESC
    LIMIT ? OFFSET ?
";
$data_stmt = $pdo->prepare($data_query);
$data_stmt->execute(array_merge($params, [$limit, $offset]));
$contacts = $data_stmt->fetchAll();

// --- 6. جلب بيانات قوائم الفلاتر ---
$roles_list_stmt = $pdo->query("
    SELECT s.option_key, s.option_value 
    FROM settings s
    JOIN setting_groups sg ON s.group_id = sg.id
    WHERE sg.group_key = 'contact_roles'
");
$roles_list = $roles_list_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$types_list = ['فرد' => 'فرد', 'منشأة' => 'منشأة'];
$statuses_list = ['active' => 'نشط', 'inactive' => 'غير نشط'];

// --- 7. تمرير البيانات إلى الواجهة ---
require_once ROOT_PATH . '/app/Modules/Contacts/Views/index.php';