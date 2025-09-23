<?php
// app/Modules/Reports/Controllers/ReportsController.php

global $pdo;

// (مستقبلاً، سنجلب قائمة التقارير من قاعدة البيانات)
$report_groups = [
    'جهات الاتصال' => [
        'contacts_list' => 'تقرير قائمة جهات الاتصال'
    ],
    'الفروع' => [
        'branches_list' => 'تقرير قائمة الفروع'
    ],
    'الوثائق' => [
        'documents_list' => 'تقرير قائمة الوثائق'
    ]
];

// تحديد التقرير النشط
$active_report_key = $_GET['report'] ?? null;

require_once ROOT_PATH . '/app/Modules/Reports/Views/index.php';