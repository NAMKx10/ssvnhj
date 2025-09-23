<?php
// app/Modules/Contacts/Controllers/ContactsBatchController.php (النسخة المطورة)

global $pdo, $page;

if ($page === 'contacts/batch_edit') {
    if (!has_permission('edit_contact')) { die('Access Denied.'); }
    
    $ids_string = $_GET['ids'] ?? '';
    $ids = !empty($ids_string) ? array_filter(explode(',', $ids_string), 'is_numeric') : [];

    $contacts_data = [];
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id, full_name, short_code, contact_type, status, id_number, tax_number, primary_phone, primary_email FROM contacts WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $contacts_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    $contacts_for_js = array_map('array_values', $contacts_data);
    require_once ROOT_PATH . '/app/Modules/Contacts/Views/batch_edit_view.php';

} elseif ($page === 'contacts/batch_add') {
    if (!has_permission('add_contact')) { die('Access Denied.'); }
    
    // لا نحتاج لجلب أي بيانات، فقط نعرض الواجهة
    require_once ROOT_PATH . '/app/Modules/Contacts/Views/batch_add_view.php';
}