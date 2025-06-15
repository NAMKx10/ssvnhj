<?php
// =================================================================
// 0. CONFIGURATION & BOOTSTRAP
// =================================================================
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// 1. CORE INCLUDES & SETUP
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/core/functions.php';

$page = isset($_GET['page']) ? $_GET['page'] : (isset($_SESSION['user_id']) ? 'dashboard' : 'login');
$page_scripts = "";

// =================================================================
// 2. ACTION HANDLERS (Forms, Delete, Reports, etc.)
// =================================================================
$is_ajax_handle_request = strpos($page, '_ajax') !== false;

// --- A: Handle AJAX form submissions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_ajax_handle_request) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => 'Invalid action specified.'];
    try {
        if (!isset($_SESSION['user_id'])) {
             $response['message'] = 'انتهت جلسة العمل. يرجى تسجيل الدخول مرة أخرى.';
             throw new Exception('User not logged in.');
        }

        $pdo->beginTransaction();
        
        // --- Properties AJAX Handler ---
    if ($page === 'properties/handle_add_ajax' || $page === 'properties/handle_edit_ajax') {
        $is_add = ($page === 'properties/handle_add_ajax');
        
        // --- بداية الإصلاح ---
        // 1. التحقق من الصلاحيات أولاً
        if (($is_add && !has_permission('add_property')) || (!$is_add && !has_permission('edit_property'))) {
            $response['message'] = 'ليس لديك صلاحية لتنفيذ هذا الإجراء.';
            throw new Exception('Permission denied.');
        }
        
        // 2. تحديد الاستعلام والمتغيرات بناءً على نوع العملية
        if ($is_add) {
            // منطق الإضافة
            $sql = "INSERT INTO properties (branch_id, property_name, property_code, property_type, ownership_type, status, owner_name, deed_number, property_value, city, district, area, floors_count, address, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $_POST['branch_id'], $_POST['property_name'], $_POST['property_code'], $_POST['property_type'],
                $_POST['ownership_type'], $_POST['status'], $_POST['owner_name'],
                $_POST['deed_number'], $_POST['property_value'] ?: null, $_POST['city'],
                $_POST['district'], $_POST['area'] ?: null, $_POST['floors_count'] ?: null,
                $_POST['address'], $_POST['notes']
            ];
        } else {
            // منطق التعديل
            $sql = "UPDATE properties SET branch_id = ?, property_name = ?, property_code = ?, property_type = ?, ownership_type = ?, status = ?, owner_name = ?, deed_number = ?, property_value = ?, city = ?, district = ?, area = ?, floors_count = ?, address = ?, notes = ? WHERE id = ?";
            $params = [
                $_POST['branch_id'], $_POST['property_name'], $_POST['property_code'], $_POST['property_type'],
                $_POST['ownership_type'], $_POST['status'], $_POST['owner_name'],
                $_POST['deed_number'], $_POST['property_value'] ?: null, $_POST['city'],
                $_POST['district'], $_POST['area'] ?: null, $_POST['floors_count'] ?: null,
                $_POST['address'], $_POST['notes'],
                $_POST['id']
            ];
        }
        
        // 3. تنفيذ الاستعلام
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            $response = ['success' => true];
        }
        // --- نهاية الإصلاح ---
    }
        // --- Units AJAX Handler ---
        elseif ($page === 'units/handle_add_ajax' || $page === 'units/handle_edit_ajax') {
            $is_add = ($page === 'units/handle_add_ajax');
            if (($is_add && !has_permission('add_unit')) || (!$is_add && !has_permission('edit_unit'))) { $response['message'] = 'ليس لديك الصلاحية الكافية.'; throw new Exception('Permission denied.'); }

            $sql = $is_add ? "INSERT INTO units (property_id, unit_name, unit_code, unit_type, area, floor, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)" : "UPDATE units SET property_id = ?, unit_name = ?, unit_code = ?, unit_type = ?, area = ?, floor = ?, status = ?, notes = ? WHERE id = ?";
            $params = [$_POST['property_id'], $_POST['unit_name'], $_POST['unit_code'], $_POST['unit_type'], $_POST['area'] ?: null, $_POST['floor'] ?: null, $_POST['status'], $_POST['notes']];
            if (!$is_add) $params[] = $_POST['id'];
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) $response = ['success' => true];
        }

        // --- Clients AJAX Handler (النسخة المطورة) ---
        elseif ($page === 'clients/handle_add_ajax' || $page === 'clients/handle_edit_ajax') {
            $is_add = ($page === 'clients/handle_add_ajax');
            
            // 1. التحقق من الصلاحيات
            if (($is_add && !has_permission('add_client')) || (!$is_add && !has_permission('edit_client'))) {
                $response['message'] = 'ليس لديك الصلاحية الكافية.'; throw new Exception('Permission denied.'); 
            }

            // 2. حفظ/تحديث بيانات العميل الأساسية
            if ($is_add) {
                $sql = "INSERT INTO clients (client_name, client_type, id_number, tax_number, mobile, email, representative_name, notes, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $params = [$_POST['client_name'], $_POST['client_type'], $_POST['id_number'], $_POST['tax_number'], $_POST['mobile'], $_POST['email'], $_POST['representative_name'], $_POST['notes'], $_POST['address']];
            } else {
                $sql = "UPDATE clients SET client_name = ?, client_type = ?, id_number = ?, tax_number = ?, mobile = ?, email = ?, representative_name = ?, notes = ?, address = ?, status = ? WHERE id = ?";
                $params = [$_POST['client_name'], $_POST['client_type'], $_POST['id_number'], $_POST['tax_number'], $_POST['mobile'], $_POST['email'], $_POST['representative_name'], $_POST['notes'], $_POST['address'], $_POST['status'], $_POST['id']];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            // 3. تحديد ID العميل
            $client_id = $is_add ? $pdo->lastInsertId() : $_POST['id'];

            // 4. تحديث العلاقات مع الفروع
            $selected_branches = $_POST['branches'] ?? [];

            // أولاً، نحذف كل العلاقات القديمة لنبدأ من جديد (الطريقة الأسهل)
            $delete_stmt = $pdo->prepare("DELETE FROM client_branches WHERE client_id = ?");
            $delete_stmt->execute([$client_id]);

            // ثانيًا، نضيف العلاقات الجديدة إذا تم اختيار أي فروع
            if (!empty($selected_branches)) {
                $insert_sql = "INSERT INTO client_branches (client_id, branch_id) VALUES (?, ?)";
                $insert_stmt = $pdo->prepare($insert_sql);
                foreach ($selected_branches as $branch_id) {
                    $insert_stmt->execute([$client_id, $branch_id]);
                }
            }
            
            // 5. إرسال رد النجاح
            $response = ['success' => true];
        }

                // --- Suppliers AJAX Handler (النسخة المطورة) ---
        elseif ($page === 'suppliers/handle_add_ajax' || $page === 'suppliers/handle_edit_ajax') {
            $is_add = ($page === 'suppliers/handle_add_ajax');
            
            // 1. التحقق من الصلاحيات
            if (($is_add && !has_permission('add_supplier')) || (!$is_add && !has_permission('edit_supplier'))) {
                $response['message'] = 'ليس لديك الصلاحية الكافية.'; throw new Exception('Permission denied.');
            }

            // 2. حفظ/تحديث بيانات المورد الأساسية
            if ($is_add) {
                $sql = "INSERT INTO suppliers (supplier_name, supplier_type, service_type, registration_number, tax_number, contact_person, mobile, email, address, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $params = [$_POST['supplier_name'], $_POST['supplier_type'], $_POST['service_type'], $_POST['registration_number'], $_POST['tax_number'], $_POST['contact_person'], $_POST['mobile'], $_POST['email'], $_POST['address'], $_POST['notes']];
            } else {
                $sql = "UPDATE suppliers SET supplier_name = ?, supplier_type = ?, service_type = ?, registration_number = ?, tax_number = ?, contact_person = ?, mobile = ?, email = ?, address = ?, notes = ?, status = ? WHERE id = ?";
                $params = [$_POST['supplier_name'], $_POST['supplier_type'], $_POST['service_type'], $_POST['registration_number'], $_POST['tax_number'], $_POST['contact_person'], $_POST['mobile'], $_POST['email'], $_POST['address'], $_POST['notes'], $_POST['status'], $_POST['id']];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            // 3. تحديد ID المورد
            $supplier_id = $is_add ? $pdo->lastInsertId() : $_POST['id'];

            // 4. تحديث العلاقات مع الفروع
            $selected_branches = $_POST['branches'] ?? [];

            // أولاً، نحذف كل العلاقات القديمة
            $delete_stmt = $pdo->prepare("DELETE FROM supplier_branches WHERE supplier_id = ?");
            $delete_stmt->execute([$supplier_id]);

            // ثانيًا، نضيف العلاقات الجديدة
            if (!empty($selected_branches)) {
                $insert_sql = "INSERT INTO supplier_branches (supplier_id, branch_id) VALUES (?, ?)";
                $insert_stmt = $pdo->prepare($insert_sql);
                foreach ($selected_branches as $branch_id) {
                    $insert_stmt->execute([$supplier_id, $branch_id]);
                }
            }
            
            // 5. إرسال رد النجاح
            $response = ['success' => true];
        }


        // --- Branches AJAX Handler ---
    elseif ($page === 'branches/handle_add_ajax' || $page === 'branches/handle_edit_ajax') {
        $is_add = ($page === 'branches/handle_add_ajax');
        
        if ($is_add) {
            // --- منطق الإضافة ---
            $sql = "INSERT INTO branches (branch_name, branch_code, branch_type, registration_number, tax_number, phone, email, address, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $_POST['branch_name'], $_POST['branch_code'], $_POST['branch_type'], $_POST['registration_number'],
                $_POST['tax_number'], $_POST['phone'], $_POST['email'],
                $_POST['address'], $_POST['notes']
            ];
        } else {
            // --- منطق التعديل ---
            $sql = "UPDATE branches SET branch_name = ?, branch_code = ?, branch_type = ?, registration_number = ?, tax_number = ?, phone = ?, email = ?, address = ?, notes = ?, status = ? WHERE id = ?";
            $params = [
                $_POST['branch_name'], $_POST['branch_code'], $_POST['branch_type'], $_POST['registration_number'],
                $_POST['tax_number'], $_POST['phone'], $_POST['email'],
                $_POST['address'], $_POST['notes'], $_POST['status'],
                $_POST['id'] // for WHERE clause
            ];
        }
        
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            $response = ['success' => true];
        }
    }


        // --- Rental Contracts AJAX Handler ---
        elseif ($page === 'contracts/handle_add_ajax' || $page === 'contracts/handle_edit_ajax') {
            $is_add = ($page === 'contracts/handle_add_ajax');
            if (($is_add && !has_permission('add_contract')) || (!$is_add && !has_permission('edit_contract'))) { $response['message'] = 'ليس لديك الصلاحية الكافية.'; throw new Exception('Permission denied.'); }
            
            if ($is_add) {
                $sql = "INSERT INTO contracts_rental (client_id, contract_number, start_date, end_date, total_amount, payment_cycle, notes) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['client_id'], $_POST['contract_number'], $_POST['start_date'], $_POST['end_date'], $_POST['total_amount'], $_POST['payment_cycle'], $_POST['notes']]);
                $contract_id = $pdo->lastInsertId();
            } else {
                $contract_id = $_POST['id'];
                $sql = "UPDATE contracts_rental SET client_id = ?, contract_number = ?, start_date = ?, end_date = ?, total_amount = ?, payment_cycle = ?, notes = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['client_id'], $_POST['contract_number'], $_POST['start_date'], $_POST['end_date'], $_POST['total_amount'], $_POST['payment_cycle'], $_POST['notes'], $contract_id]);
                $pdo->exec("UPDATE units SET status = 'متاحة' WHERE id IN (SELECT unit_id FROM contract_units WHERE contract_id = $contract_id)");
                $pdo->exec("DELETE FROM contract_units WHERE contract_id = $contract_id");
                $pdo->exec("DELETE FROM payment_schedules WHERE contract_id = $contract_id AND contract_type = 'rental'");
            }
            if (!empty($_POST['units'])) {
                $sql_units = "INSERT INTO contract_units (contract_id, unit_id) VALUES (?, ?)";
                $stmt_units = $pdo->prepare($sql_units);
                $sql_update_units = "UPDATE units SET status = 'مؤجرة' WHERE id = ?";
                $stmt_update_units = $pdo->prepare($sql_update_units);
                foreach ($_POST['units'] as $unit_id) {
                    $stmt_units->execute([$contract_id, $unit_id]);
                    $stmt_update_units->execute([$unit_id]);
                }
            }
            generate_payment_schedule($pdo, $contract_id, 'rental', $_POST['start_date'], $_POST['end_date'], $_POST['total_amount'], $_POST['payment_cycle']);
            $response = ['success' => true];
        }
        // --- Supply Contracts AJAX Handler ---
        elseif ($page === 'supply_contracts/handle_add_ajax' || $page === 'supply_contracts/handle_edit_ajax') {
            $is_add = ($page === 'supply_contracts/handle_add_ajax');
            if (($is_add && !has_permission('add_supply_contract')) || (!$is_add && !has_permission('edit_supply_contract'))) { $response['message'] = 'ليس لديك الصلاحية الكافية.'; throw new Exception('Permission denied.'); }

             if ($is_add) {
                $sql = "INSERT INTO contracts_supply (contract_number, supplier_id, property_id, service_description, start_date, end_date, total_amount, payment_cycle, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['contract_number'], $_POST['supplier_id'], $_POST['property_id'], $_POST['service_description'], $_POST['start_date'], $_POST['end_date'], $_POST['total_amount'], $_POST['payment_cycle'], $_POST['notes']]);
                $contract_id = $pdo->lastInsertId();
                generate_payment_schedule($pdo, $contract_id, 'supply', $_POST['start_date'], $_POST['end_date'], $_POST['total_amount'], $_POST['payment_cycle']);
             } else {
                $sql = "UPDATE contracts_supply SET contract_number = ?, supplier_id = ?, property_id = ?, service_description = ?, start_date = ?, end_date = ?, total_amount = ?, status = ?, payment_cycle = ?, notes = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['contract_number'], $_POST['supplier_id'], $_POST['property_id'], $_POST['service_description'], $_POST['start_date'], $_POST['end_date'], $_POST['total_amount'], $_POST['status'], $_POST['payment_cycle'], $_POST['notes'], $_POST['id']]);
             }
             $response = ['success' => true];
        }
        // --- Financial Transactions AJAX Handler ---
        elseif ($page === 'financial/handle_add_receipt_ajax' || $page === 'financial/handle_add_payment_ajax') {
            $is_receipt = ($page === 'financial/handle_add_receipt_ajax');
            if (($is_receipt && !has_permission('add_receipt')) || (!$is_receipt && !has_permission('add_payment'))) { $response['message'] = 'ليس لديك الصلاحية الكافية.'; throw new Exception('Permission denied.'); }
            
            $stmt_payment = $pdo->prepare("SELECT * FROM payment_schedules WHERE id = ?");
            $stmt_payment->execute([$_POST['payment_id']]);
            $payment = $stmt_payment->fetch();
            if ($payment) {
                $sql_trans = "INSERT INTO transactions (transaction_date, transaction_type, payment_schedule_id, amount, payment_method, reference_number, description) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_trans = $pdo->prepare($sql_trans);
                $stmt_trans->execute([$_POST['transaction_date'], $is_receipt ? 'قبض' : 'صرف', $_POST['payment_id'], $_POST['amount'], $_POST['payment_method'], $_POST['reference_number'], $_POST['description']]);
                $new_paid_amount = $payment['amount_paid'] + $_POST['amount'];
                $new_status = ($new_paid_amount >= $payment['amount_due']) ? 'مدفوع بالكامل' : 'مدفوع جزئي';
                $sql_update_payment = "UPDATE payment_schedules SET amount_paid = ?, status = ?, payment_date = ? WHERE id = ?";
                $stmt_update_payment = $pdo->prepare($sql_update_payment);
                $stmt_update_payment->execute([$new_paid_amount, $new_status, $_POST['transaction_date'], $_POST['payment_id']]);
                $response = ['success' => true];
            }
        }
        // --- Settings, Permissions, Roles AJAX Handlers ---
        elseif (strpos($page, 'settings/') !== false) {
             if (!has_permission('manage_settings')) { $response['message'] = 'ليس لديك صلاحية لإدارة الإعدادات.'; throw new Exception('Permission denied.'); }
            if ($page === 'settings/handle_add_lookup_group_ajax') {
                if (!empty($_POST['group_key']) && !empty($_POST['option_value'])) {
                    $sql = "INSERT INTO lookup_options (group_key, option_key, option_value) VALUES (?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([$_POST['group_key'], $_POST['group_key'], $_POST['option_value']])) $response = ['success' => true];
                }
            } elseif ($page === 'settings/handle_add_lookup_option_ajax') {
                if (!empty($_POST['group_key']) && !empty($_POST['option_value'])) {
                    $option_key = $_POST['option_key'] ?: preg_replace('/[^a-z0-9_]/i', '', strtolower(str_replace(' ', '_', trim($_POST['option_value']))));
                    $sql = "INSERT INTO lookup_options (group_key, option_key, option_value) VALUES (?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([$_POST['group_key'], $option_key, $_POST['option_value']])) $response = ['success' => true];
                }
            } elseif ($page === 'settings/handle_edit_lookup_option_ajax') {
                if (!empty($_POST['id'])) {
                    $sql = "UPDATE lookup_options SET option_value = ?, option_key = ?, bg_color = ?, color = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([$_POST['option_value'], $_POST['option_key'], $_POST['bg_color'], $_POST['color'], $_POST['id']])) $response = ['success' => true];
                }
            } elseif ($page === 'settings/handle_edit_lookup_group_ajax') {
                 if (!empty($_POST['original_group_key']) && !empty($_POST['new_group_key'])) {
                    $sql = "UPDATE lookup_options SET group_key = ?, option_key = CASE WHEN option_key = ? THEN ? ELSE option_key END, option_value = CASE WHEN option_key = ? THEN ? ELSE option_value END WHERE group_key = ?";
                    $stmt = $pdo->prepare($sql);
                    if($stmt->execute([$_POST['new_group_key'], $_POST['original_group_key'], $_POST['new_group_key'], $_POST['original_group_key'], $_POST['new_option_value'], $_POST['original_group_key']])) $response = ['success' => true];
                }
            }
        }
        elseif (strpos($page, 'permissions/') !== false) {
            if (!has_permission('manage_permissions')) { $response['message'] = 'ليس لديك صلاحية لإدارة الصلاحيات.'; throw new Exception('Permission denied.'); }
            try {
                if ($page === 'permissions/handle_add_ajax') {
                    $sql = "INSERT INTO permissions (group_id, permission_key, description) VALUES (?, ?, ?)";
                    $params = [$_POST['group_id'], $_POST['permission_key'], $_POST['description']];
                } elseif ($page === 'permissions/handle_edit_ajax') {
                    $sql = "UPDATE permissions SET permission_key = ?, description = ? WHERE id = ?";
                    $params = [$_POST['permission_key'], $_POST['description'], $_POST['id']];
                } elseif ($page === 'permissions/handle_add_group_ajax') {
                    $sql = "INSERT INTO permission_groups (group_name, group_key, description) VALUES (?, ?, ?)";
                    $params = [$_POST['group_name'], $_POST['group_key'], $_POST['description']];
                } elseif ($page === 'permissions/handle_edit_group_ajax') {
                    $sql = "UPDATE permission_groups SET group_name = ?, group_key = ?, description = ? WHERE id = ?";
                    $params = [$_POST['group_name'], $_POST['group_key'], $_POST['description'], $_POST['id']];
                }
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute($params)) $response = ['success' => true];
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { $response['message'] = 'هذا المفتاح البرمجي مستخدم بالفعل.'; } else { $response['message'] = 'Error: ' . $e->getMessage(); }
            }
        }
        elseif (strpos($page, 'roles/') !== false) {
            if (!has_permission('manage_roles')) { $response['message'] = 'ليس لديك صلاحية لإدارة الأدوار.'; throw new Exception('Permission denied.'); }
            try {
                if ($page === 'roles/handle_add_ajax') {
                    $sql = "INSERT INTO roles (role_name, description) VALUES (?, ?)";
                    $params = [$_POST['role_name'], $_POST['description']];
                } elseif ($page === 'roles/handle_edit_role_ajax') {
                    if ($_POST['id'] <= 3) { $response['message'] = 'لا يمكن تعديل هذا الدور الأساسي.'; throw new Exception('Attempted to edit a core role.'); }
                    $sql = "UPDATE roles SET role_name = ?, description = ? WHERE id = ?";
                    $params = [$_POST['role_name'], $_POST['description'], $_POST['id']];
                }
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute($params)) $response = ['success' => true];
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { $response['message'] = 'اسم هذا الدور مستخدم بالفعل.'; } else { $response['message'] = 'Error: ' . $e->getMessage(); }
            }
        }
        elseif ($page === 'users/handle_add_ajax' || $page === 'users/handle_edit_ajax') {
            $is_add = ($page === 'users/handle_add_ajax');
            if (($is_add && !has_permission('add_user')) || (!$is_add && !has_permission('edit_user'))) { $response['message'] = 'ليس لديك الصلاحية الكافية.'; throw new Exception('Permission denied.'); }
            if ($is_add) {
                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (full_name, username, email, mobile, password, role_id) VALUES (?, ?, ?, ?, ?, ?)";
                $params = [$_POST['full_name'], $_POST['username'], $_POST['email'], $_POST['mobile'], $password_hash, $_POST['role_id']];
            } else {
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                if (!empty($_POST['password'])) {
                    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, mobile = ?, password = ?, role_id = ?, is_active = ? WHERE id = ?";
                    $params = [$_POST['full_name'], $_POST['username'], $_POST['email'], $_POST['mobile'], $password_hash, $_POST['role_id'], $is_active, $_POST['id']];
                } else {
                    $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, mobile = ?, role_id = ?, is_active = ? WHERE id = ?";
                    $params = [$_POST['full_name'], $_POST['username'], $_POST['email'], $_POST['mobile'], $_POST['role_id'], $is_active, $_POST['id']];
                }
            }
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) $response = ['success' => true];
        }

        $pdo->commit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        if ($e->getMessage() !== 'Permission denied.' && $e->getMessage() !== 'Attempted to edit a core role.') {
             $response['message'] = 'Error: ' . $e->getMessage();
        }
    }
    echo json_encode($response);
    exit();
}

// --- B: Handle Traditional Actions ---
else {
    // --- AUTHENTICATION ---
    if ($page === 'handle_login') {
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1 AND deleted_at IS NULL");
            $stmt->execute([$_POST['username']]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];

                if ($user['role_id'] == 1) { // Super Admin
                    $_SESSION['user_permissions'] = ['SUPER_ADMIN'];
                } else {
                    $perm_stmt = $pdo->prepare("SELECT p.permission_key FROM permissions p JOIN role_permissions rp ON p.id = rp.permission_id WHERE rp.role_id = ?");
                    $perm_stmt->execute([$user['role_id']]);
                    $_SESSION['user_permissions'] = $perm_stmt->fetchAll(PDO::FETCH_COLUMN);
                }
                
                header('Location: index.php?page=dashboard');
                exit();
            }
        }
        $_SESSION['login_error'] = 'اسم المستخدم أو كلمة المرور غير صحيحة، أو الحساب غير نشط.';
        header('Location: index.php?page=login');
        exit();
    }
    elseif ($page === 'logout') {
        session_destroy();
        header('Location: index.php');
        exit();
    }
    // --- SOFT DELETE ACTION ---
    elseif (strpos($page, '/delete') !== false) {
        if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit(); }
        $parts = explode('/', $page);
        $module = $parts[0];
        $action = $parts[1] ?? 'delete';
        $id = $_GET['id'] ?? 0;

        if ($id) {
            $delete_perm = "delete_" . rtrim($module, 's');
            if(!has_permission($delete_perm) && !has_permission('manage_permissions') && !has_permission('manage_roles')) { die('Access Denied.'); }

            if ($module === 'permissions' && $action === 'delete_group') {
                $pdo->prepare("UPDATE permission_groups SET deleted_at = NOW() WHERE id = ?")->execute([$id]);
                $pdo->prepare("UPDATE permissions SET deleted_at = NOW() WHERE group_id = ?")->execute([$id]);
            } else {
                $table_name = $module;
                if ($module === 'contracts') $table_name = 'contracts_rental';
                if ($module === 'supply_contracts') $table_name = 'contracts_supply';
                if ($module === 'settings') $table_name = 'lookup_options';

                if (in_array($table_name, ['properties', 'units', 'clients', 'suppliers', 'contracts_rental', 'contracts_supply', 'lookup_options', 'users', 'permissions', 'roles'])) {
                    if (($module === 'roles' && $id <= 3) || ($module === 'users' && $id == 1)) { /* Protect essential roles/users */ } 
                    else {
                        $sql = "UPDATE {$table_name} SET deleted_at = NOW() WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$id]);
                    }
                }
            }
        }
        $redirect_page = ($module === 'settings') ? 'settings/lookups' : $module;
        header("Location: index.php?page={$redirect_page}");
        exit();
    }
    // --- BATCH ARCHIVE ACTIONS ---
    elseif ($page === 'archive/batch_action') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids']) && !empty($_POST['action'])) {
            if(!has_permission('manage_archive')) { die('Access Denied'); }
            $table = $_POST['table'] ?? '';
            $action = $_POST['action'];
            $ids = $_POST['ids'];
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            
            if ($table === 'permission_groups') {
                if ($action === 'restore') {
                    $pdo->prepare("UPDATE permission_groups SET deleted_at = NULL WHERE id IN ({$placeholders})")->execute($ids);
                    $pdo->prepare("UPDATE permissions SET deleted_at = NULL WHERE group_id IN ({$placeholders})")->execute($ids);
                } elseif ($action === 'force_delete') {
                    $pdo->prepare("DELETE FROM permissions WHERE group_id IN ({$placeholders})")->execute($ids);
                    $pdo->prepare("DELETE FROM permission_groups WHERE id IN ({$placeholders})")->execute($ids);
                }
            } 
            elseif (in_array($table, ['users','properties', 'units', 'clients', 'suppliers', 'contracts_rental', 'contracts_supply', 'lookup_options','permissions', 'roles'])) {
                if ($action === 'restore') {
                    $sql = "UPDATE {$table} SET deleted_at = NULL WHERE id IN ({$placeholders})";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($ids);
                } elseif ($action === 'force_delete') {
                    if ($table === 'contracts_rental') {
                        $in_clause = implode(',', array_fill(0, count($ids), '?'));
                        $pdo->prepare("UPDATE units SET status = 'متاحة' WHERE id IN (SELECT unit_id FROM contract_units WHERE contract_id IN ({$in_clause}))")->execute($ids);
                    }
                    $sql = "DELETE FROM {$table} WHERE id IN ({$placeholders})";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($ids);
                }
            }
        }
        header("Location: index.php?page=archive");
        exit();
    }
// --- REPORT GENERATION ---
    elseif (strpos($page, 'reports/') !== false) {
        if ($page === 'reports/client_statement') {
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['client_id'])) {
    // --- 1. قراءة المتغيرات من النموذج ---
    $client_id = $_POST['client_id']; 
    $start_date = $_POST['start_date']; 
    $end_date = $_POST['end_date'];
    $contract_id_filter = $_POST['contract_id'] ?? null; // <-- المتغير الجديد للفلترة
    $show_opening_balance = isset($_POST['show_opening_balance']);
    
    // --- 2. جلب بيانات العميل ---
    $client_stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?"); 
    $client_stmt->execute([$client_id]); 
    $client_info = $client_stmt->fetch();
    
    // --- بناء شروط SQL الديناميكية ---
    $date_condition = "";
    $contract_condition = "";
    $params = [];

    // --- 3. بناء شرط العقد (جديد) ---
    if (!empty($contract_id_filter) && $contract_id_filter !== 'all') {
        $contract_condition = " AND cr.id = ? ";
        $params[] = $contract_id_filter;
    }

    // --- 4. حساب الرصيد الافتتاحي (يجب أن يتأثر بفلتر العقد) ---
    $opening_balance = 0;
    if ($show_opening_balance && !empty($start_date)) {
        $ob_params = array_merge([$client_id], $params, [$start_date, $client_id], $params, [$start_date]);
        $sql_ob = "
            SELECT SUM(debit) - SUM(credit) as balance FROM (
                (SELECT ps.amount_due as debit, 0 as credit FROM payment_schedules ps JOIN contracts_rental cr ON ps.contract_id = cr.id WHERE cr.client_id = ? {$contract_condition} AND ps.contract_type = 'rental' AND ps.due_date < ?)
                UNION ALL
                (SELECT 0 as debit, t.amount as credit FROM transactions t JOIN payment_schedules ps ON t.payment_schedule_id = ps.id JOIN contracts_rental cr ON ps.contract_id = cr.id WHERE t.transaction_type = 'قبض' AND cr.client_id = ? {$contract_condition} AND t.transaction_date < ?)
            ) as opening_balance_table";
        $stmt_ob = $pdo->prepare($sql_ob); 
        $stmt_ob->execute($ob_params);
        $opening_balance = $stmt_ob->fetchColumn() ?? 0;
    }

    // --- 5. جلب بيانات كشف الحساب (يجب أن يتأثر بفلتر العقد والتاريخ) ---
    $main_params = array_merge([$client_id], $params, [$client_id], $params);
    if (!empty($start_date)) { $date_condition .= " AND transaction_date >= ?"; $main_params[] = $start_date; }
    if (!empty($end_date)) { $date_condition .= " AND transaction_date <= ?"; $main_params[] = $end_date; }
    
    $base_sql = "
        SELECT * FROM (
            (SELECT ps.due_date AS transaction_date, CONCAT('استحقاق دفعة عن عقد رقم ', cr.contract_number) as description, ps.amount_due as amount, 'due' as type 
             FROM payment_schedules ps JOIN contracts_rental cr ON ps.contract_id = cr.id 
             WHERE cr.client_id = ? {$contract_condition} AND ps.contract_type = 'rental') 
            UNION ALL 
            (SELECT t.transaction_date, t.description, t.amount, 'paid' as type 
             FROM transactions t JOIN payment_schedules ps ON t.payment_schedule_id = ps.id JOIN contracts_rental cr ON ps.contract_id = cr.id 
             WHERE t.transaction_type = 'قبض' AND cr.client_id = ? {$contract_condition})
        ) AS full_statement";
    
    $sql = $base_sql . " WHERE 1=1 " . $date_condition . " ORDER BY transaction_date ASC, type DESC";
    $stmt = $pdo->prepare($sql); 
    $stmt->execute($main_params); 
    $statement_data = $stmt->fetchAll();
    
    // --- 6. عرض القالب النهائي ---
    require __DIR__ . '/../src/modules/reports/client_statement_view.php';
    exit();
}
        }
        elseif ($page === 'reports/supplier_statement') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['supplier_id'])) {
                $supplier_id = $_POST['supplier_id']; $start_date = $_POST['start_date']; $end_date = $_POST['end_date'];
                $show_opening_balance = isset($_POST['show_opening_balance']); $opening_balance = 0;
                $supplier_stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id = ?"); $supplier_stmt->execute([$supplier_id]); $supplier_info = $supplier_stmt->fetch();
                if ($show_opening_balance && !empty($start_date)) {
                    $sql_ob = "SELECT SUM(CASE WHEN type = 'due' THEN amount ELSE 0 END) as total_credit, SUM(CASE WHEN type = 'paid' THEN amount ELSE 0 END) as total_debit FROM ((SELECT ps.amount_due as amount, 'due' as type FROM payment_schedules ps JOIN contracts_supply cs ON ps.contract_id = cs.id WHERE cs.supplier_id = ? AND ps.contract_type = 'supply' AND ps.due_date < ?) UNION ALL (SELECT t.amount, 'paid' as type FROM transactions t JOIN payment_schedules ps ON t.payment_schedule_id = ps.id JOIN contracts_supply cs ON ps.contract_id = cs.id WHERE t.transaction_type = 'صرف' AND cs.supplier_id = ? AND t.transaction_date < ?)) as opening_balance_table";
                    $stmt_ob = $pdo->prepare($sql_ob); $stmt_ob->execute([$supplier_id, $start_date, $supplier_id, $start_date]);
                    $ob_result = $stmt_ob->fetch(); $opening_balance = ($ob_result['total_credit'] ?? 0) - ($ob_result['total_debit'] ?? 0);
                }
                $params = [$supplier_id, $supplier_id]; $date_condition = ""; $base_sql = "SELECT * FROM ((SELECT ps.due_date AS transaction_date, CONCAT('استحقاق دفعة عن عقد توريد رقم ', cs.contract_number) as description, ps.amount_due as amount, 'due' as type FROM payment_schedules ps JOIN contracts_supply cs ON ps.contract_id = cs.id WHERE cs.supplier_id = ? AND ps.contract_type = 'supply') UNION ALL (SELECT t.transaction_date, t.description, t.amount, 'paid' as type FROM transactions t JOIN payment_schedules ps ON t.payment_schedule_id = ps.id JOIN contracts_supply cs ON ps.contract_id = cs.id WHERE t.transaction_type = 'صرف' AND cs.supplier_id = ?)) AS full_statement";
                if (!empty($start_date)) { $date_condition .= " AND transaction_date >= ?"; $params[] = $start_date; }
                if (!empty($end_date)) { $date_condition .= " AND transaction_date <= ?"; $params[] = $end_date; }
                $sql = $base_sql . " WHERE 1=1 " . $date_condition . " ORDER BY transaction_date ASC, type DESC";
                $stmt = $pdo->prepare($sql); $stmt->execute($params); $statement_data = $stmt->fetchAll();
                require __DIR__ . '/../src/modules/reports/supplier_statement_view.php';
                exit();
            }
        }
        elseif ($page === 'reports/late_rentals') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $as_of_date = $_POST['as_of_date'] ?: date('Y-m-d'); $property_id = $_POST['property_id'] ?: null;
                $params = [$as_of_date, $as_of_date]; $property_condition = "";
                if ($property_id) { $property_condition = " AND p.id = ?"; $params[] = $property_id; }
                $sql = "SELECT ps.due_date, ps.amount_due, ps.amount_paid, (ps.amount_due - ps.amount_paid) AS remaining_amount, DATEDIFF(?, ps.due_date) AS days_late, cr.contract_number, c.client_name as party_name, p.property_name, GROUP_CONCAT(u.unit_name SEPARATOR ', ') as unit_names FROM payment_schedules ps JOIN contracts_rental cr ON ps.contract_id = cr.id AND ps.contract_type = 'rental' JOIN clients c ON cr.client_id = c.id LEFT JOIN contract_units cu ON cr.id = cu.contract_id LEFT JOIN units u ON cu.unit_id = u.id LEFT JOIN properties p ON u.property_id = p.id WHERE ps.status != 'مدفوع بالكامل' AND ps.due_date < ? {$property_condition} GROUP BY ps.id ORDER BY days_late DESC";
                $stmt = $pdo->prepare($sql); $stmt->execute($params); $late_payments = $stmt->fetchAll();
                require __DIR__ . '/../src/modules/reports/late_rentals_view.php';
                exit();
            }
        }
        elseif ($page === 'reports/late_supplies') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $as_of_date = $_POST['as_of_date'] ?: date('Y-m-d'); $property_id = $_POST['property_id'] ?: null;
                $params = [$as_of_date, $as_of_date]; $property_condition = "";
                if ($property_id) { $property_condition = " AND p.id = ?"; $params[] = $property_id; }
                $sql = "SELECT ps.due_date, ps.amount_due, ps.amount_paid, (ps.amount_due - ps.amount_paid) AS remaining_amount, DATEDIFF(?, ps.due_date) AS days_late, cs.contract_number, s.supplier_name as party_name, p.property_name FROM payment_schedules ps JOIN contracts_supply cs ON ps.contract_id = cs.id AND ps.contract_type = 'supply' JOIN suppliers s ON cs.supplier_id = s.id JOIN properties p ON cs.property_id = p.id WHERE ps.status != 'مدفوع بالكامل' AND ps.due_date < ? {$property_condition} ORDER BY days_late DESC";
                $stmt = $pdo->prepare($sql); $stmt->execute($params); $late_payments = $stmt->fetchAll();
                require __DIR__ . '/../src/modules/reports/late_supplies_view.php';
                exit();
            }
           
        }

         // --- ابدأ الإضافة من هنا ---
        elseif ($page === 'reports/property_profile') {
            require __DIR__ . '/../src/modules/reports/property_profile_view.php';
            exit();
        }
        // --- انتهت الإضافة ---
        // --- هذا هو الكود الجديد ---
elseif ($page === 'reports/unit_profile') {
    require __DIR__ . '/../src/modules/reports/unit_profile_view.php';
    exit();
}
    }

    // --- ROLES & PERMISSIONS ACTIONS (NON-AJAX) ---
    elseif ($page === 'roles/handle_edit') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role_id'])) {
            if(!has_permission('manage_roles')) { die('Access Denied'); }
            $role_id = $_POST['role_id'];
            $new_permissions = $_POST['permissions'] ?? [];

            if ($role_id != 1) { // لا تسمح بتعديل صلاحيات Super Admin
                try {
                    $pdo->beginTransaction();
                    $delete_stmt = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?");
                    $delete_stmt->execute([$role_id]);
                    if (!empty($new_permissions)) {
                        $insert_stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
                        foreach ($new_permissions as $permission_id) {
                            $insert_stmt->execute([$role_id, $permission_id]);
                        }
                    }
                    $pdo->commit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    die("حدث خطأ أثناء حفظ الصلاحيات: " . $e->getMessage());
                }
            }
        }
        header("Location: index.php?page=roles");
        exit();
    }
}


// =================================================================
// 3. VIEW ROUTER (Page Display Logic) - النسخة النهائية الصحيحة
// =================================================================

// --- تحديد مسار العرض المطلوب ---
$parts = explode('/', $page);
$module = $parts[0];
$action = (isset($parts[1]) && !empty($parts[1])) ? $parts[1] : $module;
$view_path = __DIR__ . "/../src/modules/{$module}/{$action}_view.php";

// --- المسار الأول: إذا كان الطلب لمحتوى نافذة منبثقة فقط ---
if (isset($_GET['view_only'])) {
    if (file_exists($view_path)) {
        require_once $view_path; // عرض ملف الواجهة فقط بدون القالب
    } else {
        echo "<div class='alert alert-danger'>محتوى العرض غير موجود.</div>";
    }
    // لا يتم عرض الهيدر أو الفوتر أو أي شيء آخر هنا لأن هذا رد AJAX

// --- المسار الثاني: إذا كان الطلب لصفحة كاملة ---
} else {
    // عرض الهيدر أولاً
    require_once __DIR__ . '/../templates/partials/header.php';

    if (isset($_SESSION['user_id'])) {
        // --- عرض الصفحات الداخلية للمستخدم المسجل ---
        echo '<div class="d-flex">';
        require_once __DIR__ . '/../templates/partials/sidebar.php'; // عرض الشريط الجانبي
        echo '<div class="main-content">';

        if (file_exists($view_path)) {
            require_once $view_path; // عرض محتوى الصفحة
        } else {
            echo "<div class='container mt-5 text-center'><h1>404</h1><p>الصفحة المطلوبة غير موجودة.</p></div>";
        }

        echo '</div></div>'; // إغلاق أغلفة المحتوى
    } else {
        // --- عرض صفحة تسجيل الدخول للزوار ---
        require_once __DIR__ . "/../src/modules/login/login_view.php";
    }

    // عرض الفوتر في النهاية لجميع الصفحات الكاملة
    require_once __DIR__ . '/../templates/partials/footer.php';
}

?>