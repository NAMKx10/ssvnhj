<?php
// app/Modules/Documents/Controllers/AjaxController.php

global $pdo;
header('Content-Type: application/json');

$model = $_GET['model'] ?? '';
$response = [];

try {
    switch ($model) {
        case 'contact':
            $stmt = $pdo->query("SELECT id, full_name as name FROM contacts WHERE deleted_at IS NULL ORDER BY name");
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'branch':
            $stmt = $pdo->query("SELECT id, branch_name as name FROM branches WHERE deleted_at IS NULL ORDER BY name");
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'property':
            // (سيتم تفعيل هذا لاحقًا عندما نبني موديل العقارات)
            // $stmt = $pdo->query("SELECT id, property_name as name FROM properties WHERE deleted_at IS NULL ORDER BY name");
            // $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    $response = ['error' => 'Database query failed'];
}

echo json_encode($response);
exit();