<?php
// app/Modules/Branches/Views/edit_view.php
global $pdo;

$branch_id = (int)($_GET['id'] ?? 0);
if (!$branch_id) { die("Branch ID is required."); }

// 1. جلب البيانات الأساسية للفرع
$stmt = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
$stmt->execute([$branch_id]);
$branch = $stmt->fetch();
if (!$branch) { die("Branch not found."); }

// 2. جلب كل أنواع الفروع المتاحة
$types_stmt = $pdo->query("SELECT option_key, option_value FROM settings WHERE group_id = 'branch_types'");
$branch_types = $types_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// 3. جلب كل الملاك المتاحين
$owners_stmt = $pdo->query("
    SELECT c.id, c.full_name 
    FROM contacts c 
    JOIN contact_roles cr ON c.id = cr.contact_id 
    WHERE cr.role_key = 'owner' AND c.status = 'active' AND c.deleted_at IS NULL 
    ORDER BY c.full_name
");
$owners_list = $owners_stmt->fetchAll();

// 4. جلب الملاك المحددين حاليًا لهذا الفرع
$current_owners_stmt = $pdo->prepare("SELECT contact_id FROM branch_owners WHERE branch_id = ?");
$current_owners_stmt->execute([$branch_id]);
$current_owners = $current_owners_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="modal-header">
    <h5 class="modal-title">تعديل بيانات الفرع: <?= html($branch['branch_name']) ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_branch_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="edit_branch">
    <input type="hidden" name="id" value="<?= $branch['id'] ?>">
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label required">اسم الفرع</label><input type="text" class="form-control" name="branch_name" value="<?= html($branch['branch_name']) ?>" required></div>
            <div class="col-md-6 mb-3"><label class="form-label required">كود الفرع</label><input type="text" class="form-control" name="branch_code" value="<?= html($branch['branch_code']) ?>" required></div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">نوع الفرع</label>
                <select name="branch_type" class="form-select">
                    <?php foreach ($branch_types as $key => $value): ?>
                        <option value="<?= html($key) ?>" <?= ($branch['branch_type'] == $key) ? 'selected' : '' ?>><?= html($value) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label required">الحالة</label>
                <select name="status" class="form-select" required>
                    <option value="active" <?= ($branch['status'] == 'active') ? 'selected' : '' ?>>نشط</option>
                    <option value="inactive" <?= ($branch['status'] == 'inactive') ? 'selected' : '' ?>>غير نشط</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">الملاك</label>
            <select name="owners[]" class="form-select" multiple>
                <?php foreach ($owners_list as $owner): ?>
                    <option value="<?= $owner['id'] ?>" <?= in_array($owner['id'], $current_owners) ? 'selected' : '' ?>><?= html($owner['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">الجوال</label><input type="text" class="form-control" name="phone" value="<?= html($branch['phone']) ?>"></div>
            <div class="col-md-6 mb-3"><label class="form-label">البريد الإلكتروني</label><input type="email" class="form-control" name="email" value="<?= html($branch['email']) ?>"></div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">رقم السجل التجاري</label><input type="text" class="form-control" name="cr_number" value="<?= html($branch['cr_number']) ?>"></div>
            <div class="col-md-6 mb-3"><label class="form-label">الرقم الضريبي</label><input type="text" class="form-control" name="tax_number" value="<?= html($branch['tax_number']) ?>"></div>
        </div>
        <div class="mb-3"><label class="form-label">العنوان</label><textarea name="address" class="form-control" rows="2"><?= html($branch['address']) ?></textarea></div>
        <div class="mb-3"><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="2"><?= html($branch['notes']) ?></textarea></div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </div>
</form>