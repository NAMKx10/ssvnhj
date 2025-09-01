<?php
// app/Common/Controllers/UsersBatchController.php (النسخة المحدثة)
global $pdo, $page;

if ($page === 'users/batch_add') {
    $roles_stmt = $pdo->query("SELECT role_name FROM roles WHERE deleted_at IS NULL ORDER BY role_name");
    $roles_for_js = $roles_stmt->fetchAll(PDO::FETCH_COLUMN);
    require_once ROOT_PATH . '/app/Common/Views/users/batch_add_view.php';

} elseif ($page === 'users/batch_edit') {
    $ids_string = $_GET['ids'] ?? '';
    $ids = !empty($ids_string) ? explode(',', $ids_string) : [];
    
    $users_data = [];
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id, full_name, username, email, mobile, role_id, status FROM users WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $users_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    $roles_list = $pdo->query("SELECT id, role_name FROM roles WHERE deleted_at IS NULL")->fetchAll(PDO::FETCH_KEY_PAIR);
    // تحويل البيانات لتناسب Handsontable
    $users_for_js = array_map(function($user) use ($roles_list) {
        return [
            $user['id'],
            $user['full_name'],
            $user['username'],
            $roles_list[$user['role_id']] ?? '',
            $user['status'],
            $user['email'],
            $user['mobile']
        ];
    }, $users_data);

    $roles_for_js = array_values($roles_list);
    
    require_once ROOT_PATH . '/app/Common/Views/users/batch_edit_view.php';
}