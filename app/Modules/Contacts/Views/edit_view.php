<?php
// app/Modules/Contacts/Views/edit_view.php
global $pdo;

$contact_id = (int)($_GET['id'] ?? 0);
if (!$contact_id) { die("Contact ID is required."); }

// 1. جلب البيانات الأساسية
$stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->execute([$contact_id]);
$contact = $stmt->fetch();
if (!$contact) { die("Contact not found."); }

// 2. جلب كل الأدوار المتاحة
$roles_stmt = $pdo->query("
    SELECT s.option_key, s.option_value 
    FROM settings s
    JOIN setting_groups sg ON s.group_id = sg.id
    WHERE sg.group_key = 'contact_roles' AND s.deleted_at IS NULL
    ORDER BY s.option_value
");
$roles_list = $roles_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// 3. جلب الأدوار المحددة حاليًا لجهة الاتصال
$current_roles_stmt = $pdo->prepare("SELECT role_key FROM contact_roles WHERE contact_id = ?");
$current_roles_stmt->execute([$contact_id]);
$current_roles = $current_roles_stmt->fetchAll(PDO::FETCH_COLUMN);

// 4. جلب كل الفروع المتاحة
$branches_stmt = $pdo->query("SELECT id, branch_name FROM branches WHERE status = 'active' AND deleted_at IS NULL ORDER BY branch_name");
$branches_list = $branches_stmt->fetchAll();

// 5. جلب الفروع المحددة حاليًا لجهة الاتصال
$current_branches_stmt = $pdo->prepare("SELECT branch_id FROM contact_branches WHERE contact_id = ?");
$current_branches_stmt->execute([$contact_id]);
$current_branches = $current_branches_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="modal-header">
    <h5 class="modal-title">تعديل بيانات: <?= html($contact['full_name']) ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_contact_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="edit_contact">
    <input type="hidden" name="id" value="<?= $contact['id'] ?>">
    <div class="modal-body">
        <!-- (نفس حقول نموذج الإضافة، مع ملء القيم الحالية) -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label required">الاسم الكامل / الشركة</label>
                <input type="text" class="form-control" name="full_name" value="<?= html($contact['full_name']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">الكود / الاسم المختصر</label>
                <input type="text" class="form-control" name="short_code" value="<?= html($contact['short_code']) ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label required">نوع جهة الاتصال</label>
                <select name="contact_type" class="form-select" required>
                    <option value="فرد" <?= $contact['contact_type'] == 'فرد' ? 'selected' : '' ?>>فرد</option>
                    <option value="منشأة" <?= $contact['contact_type'] == 'منشأة' ? 'selected' : '' ?>>منشأة</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label required">الحالة</label>
                <select name="status" class="form-select" required>
                    <option value="active" <?= $contact['status'] == 'active' ? 'selected' : '' ?>>نشط</option>
                    <option value="inactive" <?= $contact['status'] == 'inactive' ? 'selected' : '' ?>>غير نشط</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label required">الدور/الأدوار</label>
            <select name="roles[]" class="form-select" multiple required>
                <?php foreach ($roles_list as $key => $value): ?>
                    <option value="<?= html($key) ?>" <?= in_array($key, $current_roles) ? 'selected' : '' ?>><?= html($value) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">الفروع</label>
            <select name="branches[]" class="form-select" multiple>
                <?php foreach ($branches_list as $branch): ?>
                    <option value="<?= $branch['id'] ?>" <?= in_array($branch['id'], $current_branches) ? 'selected' : '' ?>><?= html($branch['branch_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">رقم الهوية / السجل</label>
                <input type="text" class="form-control" name="id_number" value="<?= html($contact['id_number']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">الرقم الضريبي</label>
                <input type="text" class="form-control" name="tax_number" value="<?= html($contact['tax_number']) ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">الجوال</label>
                <input type="text" class="form-control" name="primary_phone" value="<?= html($contact['primary_phone']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" class="form-control" name="primary_email" value="<?= html($contact['primary_email']) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">العنوان</label>
            <textarea name="address" class="form-control" rows="2"><?= html($contact['address']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">ملاحظات</label>
            <textarea name="notes" class="form-control" rows="2"><?= html($contact['notes']) ?></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </div>
</form>