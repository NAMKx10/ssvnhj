<?php
// app/Modules/Branches/Controllers/BranchesBatchController.php

global $pdo, $page;
function get_branch_types_for_js($pdo) {
    $stmt = $pdo->query("SELECT option_value FROM settings WHERE group_id = 'branch_types' ORDER BY id");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

if ($page === 'branches/batch_edit') {
    if (!has_permission('edit_branch')) { die('Access Denied.'); }
    
    $ids_string = $_GET['ids'] ?? '';
    $ids = !empty($ids_string) ? array_filter(explode(',', $ids_string), 'is_numeric') : [];
    
    $branches_data = [];
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("
            SELECT id, branch_name, branch_code, branch_type, status, cr_number, tax_number, phone, email 
            FROM branches 
            WHERE id IN ($placeholders)
        ");
        $stmt->execute($ids);
        $branches_data = $stmt->fetchAll(PDO::FETCH_NUM);
    }
    $branch_types_for_js = get_branch_types_for_js($pdo);
    require_once ROOT_PATH . '/app/Modules/Branches/Views/batch_edit_view.php';

} elseif ($page === 'branches/batch_add') {
    if (!has_permission('add_branch')) { die('Access Denied.'); }
    $branch_types_for_js = get_branch_types_for_js($pdo);
    // لا نحتاج لجلب أي بيانات، فقط نعرض الواجهة
    require_once ROOT_PATH . '/app/Modules/Branches/Views/batch_add_view.php';
}