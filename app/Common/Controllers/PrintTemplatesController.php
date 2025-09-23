<?php
// app/Common/Controllers/PrintTemplatesController.php

if (!has_permission('manage_print_templates')) {
    die('Access Denied.');
}

global $pdo;

$templates = $pdo->query("SELECT * FROM print_templates ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

require_once ROOT_PATH . '/app/Common/Views/settings/print_templates_view.php';