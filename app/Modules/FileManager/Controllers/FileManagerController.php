<?php
// app/Modules/FileManager/Controllers/FileManagerController.php (النسخة المطورة)

if (!has_permission('access_file_manager')) { die('Access Denied.'); }

global $pdo;

$current_folder_id = isset($_GET['folder_id']) ? (int)$_GET['folder_id'] : null;

// --- بناء شرط WHERE ---
$where_clause = $current_folder_id ? "WHERE parent_id = ?" : "WHERE parent_id IS NULL";
$params = $current_folder_id ? [$current_folder_id] : [];

// --- حساب الترقيم ---
$limit = 20; // يمكنك تغيير هذا الرقم
$count_query = "SELECT COUNT(*) FROM files " . $where_clause;
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_records = (int)$count_stmt->fetchColumn();
$total_pages = max(1, ceil($total_records / $limit));
$current_page = max(1, (int)($_GET['p'] ?? 1));
$offset = ($current_page - 1) * $limit;

// --- جلب المحتويات ---
$data_query = "SELECT * FROM files {$where_clause} ORDER BY file_type DESC, file_name ASC LIMIT {$limit} OFFSET {$offset}";
$stmt = $pdo->prepare($data_query);
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- 3. بناء شريط التنقل (Breadcrumbs) ---
$breadcrumbs = [];
$folder_id_tracker = $current_folder_id;

while ($folder_id_tracker) {
    $stmt = $pdo->prepare("SELECT id, parent_id, file_name FROM files WHERE id = ? AND file_type = 'folder'");
    $stmt->execute([$folder_id_tracker]);
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($parent) {
        array_unshift($breadcrumbs, $parent);
        $folder_id_tracker = $parent['parent_id'];
    } else {
        break; // توقف إذا لم يتم العثور على المجلد
    }
}

// --- 4. تمرير البيانات إلى الواجهة ---
require_once ROOT_PATH . '/app/Modules/FileManager/Views/index.php';