<?php
// app/Common/Controllers/GeneralSettingsHandlerController.php

if (!has_permission('manage_general_settings')) {
    die('Access Denied.');
}

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = $_POST['settings'] ?? [];

    // التعامل مع الإعدادات التي هي عبارة عن checkbox
    // إذا لم يتم تحديد المربع، لن يتم إرساله، لذا يجب أن نضع قيمته يدويًا
    $settings['maintenance_mode'] = isset($settings['maintenance_mode']) ? '1' : '0';

    try {
        $pdo->beginTransaction();

        // 1. تحديث الإعدادات النصية في قاعدة البيانات
        $stmt = $pdo->prepare("UPDATE general_settings SET option_value = ? WHERE option_name = ?");
        foreach ($settings as $name => $value) {
            $stmt->execute([$value, $name]);
        }

        // 2. التعامل مع رفع الملفات (الشعار والأيقونة)
        $upload_dir = ROOT_PATH . '/public/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0775, true);
        }

        // معالجة الشعار
        if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_OK) {
            $file_name = 'logo_' . time() . '_' . basename($_FILES['logo_image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['logo_image']['tmp_name'], $target_file)) {
                // احفظ مسار الملف الجديد في قاعدة البيانات
                $pdo->prepare("UPDATE general_settings SET option_value = ? WHERE option_name = 'logo_image_path'")
                    ->execute(['/uploads/' . $file_name]);
            }
        }

        // معالجة أيقونة المفضلة (Favicon)
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $file_name = 'favicon_' . time() . '_' . basename($_FILES['favicon']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['favicon']['tmp_name'], $target_file)) {
                $pdo->prepare("UPDATE general_settings SET option_value = ? WHERE option_name = 'favicon_path'")
                    ->execute(['/uploads/' . $file_name]);
            }
        }

        $pdo->commit();

        // (يمكن إضافة رسالة نجاح هنا في الجلسة)

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        log_error("General settings update error: " . $e->getMessage());
    }
}

header("Location: index.php?page=settings/general");
exit();