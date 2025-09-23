<?php
// app/Common/Controllers/SettingsController.php (النسخة النهائية)
global $pdo;

$groups_stmt = $pdo->query("
    SELECT sg.*, COUNT(s.id) as options_count 
    FROM setting_groups sg 
    LEFT JOIN settings s ON sg.id = s.group_id AND s.deleted_at IS NULL
    WHERE sg.deleted_at IS NULL -- <-- هذا هو السطر الجديد والمهم
    GROUP BY sg.id 
    ORDER BY sg.group_name ASC
");
$groups = $groups_stmt->fetchAll(PDO::FETCH_ASSOC);

$active_group_id = (int)($_GET['group_id'] ?? ($groups[0]['id'] ?? 0));
$active_group = null;
foreach($groups as $group) { if ($group['id'] == $active_group_id) { $active_group = $group; break; } }

$options = [];
if ($active_group_id) {
    $options_stmt = $pdo->prepare("SELECT * FROM settings WHERE group_id = ? AND deleted_at IS NULL ORDER BY id ASC");
    $options_stmt->execute([$active_group_id]);
    $options = $options_stmt->fetchAll(PDO::FETCH_ASSOC);
}
require_once ROOT_PATH . '/app/Common/Views/settings/index.php';