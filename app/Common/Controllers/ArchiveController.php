<?php
// app/Common/Controllers/ArchiveController.php (النسخة المحدثة)

global $pdo;

// --- 1. تعريف كل الجداول القابلة للأرشفة في النظام ---
$tables_map = [
    // =============================================
    // النواة الإدارية (Admin Core)
    // =============================================
    'users'             => ['display' => 'المستخدمون',          'name_col' => 'full_name'],
    'roles'             => ['display' => 'الأدوار',             'name_col' => 'role_name'],
    'permission_groups' => ['display' => 'مجموعات الصلاحيات',  'name_col' => 'group_name'],
    'permissions'       => ['display' => 'الصلاحيات',            'name_col' => 'description'],
    'branches'          => ['display' => 'الفروع',              'name_col' => 'branch_name'],

    // =============================================
    // الكيانات وجهات الاتصال (Entities & Contacts)
    // =============================================

    'setting_groups'    => ['display' => 'مجموعات الإعدادات',   'name_col' => 'group_name'],
    'settings'          => ['display' => 'خيارات الإعدادات',    'name_col' => 'option_value'],

    // ملاحظة: سنفترض وجود جدول مركزي `contacts` يخدم كل الأطراف
    'contacts'          => ['display' => 'جهات الاتصال (عملاء، موردون، ...)', 'name_col' => 'full_name'],

    // =============================================
    // موديلات العقارات (Real Estate Modules)
    // =============================================
    'properties'        => ['display' => 'العقارات',            'name_col' => 'property_name'],
    'units'             => ['display' => 'الوحدات',             'name_col' => 'unit_name'],
    'rental_contracts'  => ['display' => 'عقود الإيجار',         'name_col' => 'contract_number'],

    // =============================================
    // الموديلات المالية والمحاسبية (Financial & Accounting)
    // =============================================
    'sales_invoices'    => ['display' => 'فواتير المبيعات',      'name_col' => 'invoice_number'],
    'purchase_invoices' => ['display' => 'فواتير المشتريات',     'name_col' => 'invoice_number'],
    'receipt_vouchers'  => ['display' => 'سندات القبض',         'name_col' => 'voucher_number'],
    'payment_vouchers'  => ['display' => 'سندات الصرف',         'name_col' => 'voucher_number'],
    'journal_entries'   => ['display' => 'قيود اليومية',          'name_col' => 'description'],
    'chart_of_accounts' => ['display' => 'الحسابات (شجرة الحسابات)', 'name_col' => 'account_name'],

    // =============================================
    // موديلات المخزون والمشاريع (Inventory & Projects)
    // =============================================
    'products'          => ['display' => 'المنتجات والخدمات',    'name_col' => 'product_name'],
    'warehouses'        => ['display' => 'المخازن',             'name_col' => 'warehouse_name'],
    'projects'          => ['display' => 'المشاريع (المقاولات)',  'name_col' => 'project_name'],
    'supply_contracts'  => ['display' => 'عقود التوريد',         'name_col' => 'contract_number'],
    
    // =============================================
    // موديلات متنوعة (Miscellaneous Modules)
    // =============================================
    'documents'         => ['display' => 'الوثائق والمستندات',    'name_col' => 'document_title'],
    'legal_cases'       => ['display' => 'القضايا القانونية',     'name_col' => 'case_number'],

];

// --- 2. التحقق من وجود طلب إجراء (Action Request) ---
$action = $_REQUEST['action'] ?? null;

if ($action) {
        // تحديد الصلاحية المطلوبة بناءً على الإجراء
    $required_permission = '';
    if ($action === 'restore') {
        $required_permission = 'restore_from_archive';
    } elseif ($action === 'force_delete') {
        $required_permission = 'force_delete_from_archive';
    }

    // التحقق من الصلاحية قبل أي شيء آخر
    if (empty($required_permission) || !has_permission($required_permission)) {
        // يمكنك هنا عرض صفحة "Access Denied" أو مجرد إيقاف التنفيذ
        die('Access Denied. You do not have the required permission.');
    }

    $table = $_REQUEST['table'] ?? null;
    $ids = (array)($_REQUEST['ids'] ?? $_GET['id'] ?? []);
    
    if ($table && isset($tables_map[$table]) && !empty($ids)) {
        // حماية إضافية: لا تسمح بالحذف النهائي للمستخدم أو الدور رقم 1
        if ($table === 'users' || $table === 'roles') {
            $ids = array_filter($ids, fn($id) => (int)$id !== 1);
        }
        
        if (!empty($ids)) {
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
    }
    header("Location: index.php?page=archive");
    exit();
}


// --- 3. إذا لم يكن هناك إجراء، قم بعرض الصفحة ---
$archived_items = [];
foreach ($tables_map as $table => $details) {
    $name_column = $details['name_col'];
    try {
        $stmt = $pdo->query("SELECT id, `{$name_column}` as name, deleted_at FROM `{$table}` WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($items) {
            $archived_items[$table] = $items;
        }
    } catch (PDOException $e) {
        // تجاهل الخطأ إذا كان الجدول أو العمود غير موجود (للمرونة المستقبلية)
        log_error("Archive view error for table '{$table}': " . $e->getMessage());
    }
}

// --- 4. قم بتضمين ملف الواجهة ---
require_once ROOT_PATH . '/app/Common/Views/archive/index.php';