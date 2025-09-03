<?php
// app/Common/Controllers/ArchiveController.php (النسخة الذكية والنهائية)

global $pdo;

// --- 1. تعريف الجداول القابلة للأرشفة ---
// (ملاحظة: تأكد من أن أسماء الجداول والأعمدة تطابق قاعدة بياناتك الجديدة)
$tables_map = [
    'users'   => ['display' => 'المستخدمون', 'name_col' => 'full_name'],
    'roles'   => ['display' => 'الأدوار',    'name_col' => 'role_name'],
    // أضف جداول أخرى هنا في المستقبل
];

// --- 2. التحقق من وجود طلب إجراء (Action Request) ---
$action = $_GET['action'] ?? $_POST['action'] ?? null;

if ($action) {
    $table = $_REQUEST['table'] ?? null;
    $ids = (array)($_REQUEST['ids'] ?? $_GET['id'] ?? []);
    
    if ($table && isset($tables_map[$table]) && !empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        try {
            switch ($action) {
                case 'restore':
                    $stmt = $pdo->prepare("UPDATE `{$table}` SET deleted_at = NULL WHERE id IN ({$placeholders})");
                    $stmt->execute($ids);
                    break;
                case 'force_delete':
                    $stmt = $pdo->prepare("DELETE FROM `{$table}` WHERE id IN ({$placeholders})");
                    $stmt->execute($ids);
                    break;
            }
        } catch (PDOException $e) {
            log_error("Archive action error: " . $e->getMessage());
        }
    }
    // بعد تنفيذ الإجراء، قم بإعادة التوجيه لمنع إعادة الإرسال
    header("Location: index.php?page=archive");
    exit();
}


// --- 3. إذا لم يكن هناك إجراء، قم بعرض الصفحة ---
$archived_items = [];
foreach ($tables_map as $table => $details) {
    $name_column = $details['name_col'];
    $stmt = $pdo->query("SELECT id, `{$name_column}` as name, deleted_at FROM `{$table}` WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($items) {
        $archived_items[$table] = $items;
    }
}

// --- 4. قم بتضمين ملف الواجهة ---
require_once ROOT_PATH . '/app/Common/Views/archive/index.php';