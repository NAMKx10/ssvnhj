<?php
// app/Common/Controllers/GeneralSettingsController.php

if (!has_permission('manage_general_settings')) {
    die('Access Denied.');
}

global $pdo;

// جلب كل الإعدادات دفعة واحدة
$settings_stmt = $pdo->query("SELECT * FROM general_settings");
$settings_array = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// قائمة المناطق الزمنية لسهولة الاختيار
$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

require_once ROOT_PATH . '/app/Common/Views/settings/general_settings_view.php';