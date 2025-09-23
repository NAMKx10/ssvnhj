<?php
// app/Common/Controllers/PrintTemplateHandlerController.php

if (!has_permission('manage_print_templates')) {
    die('Access Denied.');
}

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $templates = $_POST['templates'] ?? [];
    
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("
            UPDATE print_templates SET 
            header_right=?, header_center=?, header_left=?, 
            footer_right=?, footer_center=?, footer_left=? 
            WHERE id = ?
        ");

        foreach ($templates as $id => $data) {
            $stmt->execute([
                $data['header_right'], $data['header_center'], $data['header_left'],
                $data['footer_right'], $data['footer_center'], $data['footer_left'],
                (int)$id
            ]);
        }
        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        log_error("Print template update error: " . $e->getMessage());
    }
}

// (يمكن إضافة رسالة نجاح هنا في الجلسة)
header("Location: index.php?page=settings/print_templates");
exit();