<?php
// app/Modules/Branches/Views/add_view.php
global $pdo;

// جلب أنواع الفروع من جدول settings
$types_stmt = $pdo->query("SELECT option_key, option_value FROM settings WHERE group_id = 'branch_types'");
$branch_types = $types_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// جلب الملاك (جهات الاتصال التي لها دور 'owner')
$owners_stmt = $pdo->query("
    SELECT c.id, c.full_name 
    FROM contacts c
    JOIN contact_roles cr ON c.id = cr.contact_id
    WHERE cr.role_key = 'owner' AND c.status = 'active' AND c.deleted_at IS NULL
    ORDER BY c.full_name
");
$owners = $owners_stmt->fetchAll();

?>

<div class="modal-header">
    <h5 class="modal-title">إضافة فرع جديد</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_branch_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="add_branch">
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label required">اسم الفرع / المحفظة</label>
                <input type="text" class="form-control" name="branch_name" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label required">كود الفرع</label>
                <input type="text" class="form-control" name="branch_code" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">نوع الفرع</label>
                <select name="branch_type" class="form-select">
                    <?php foreach ($branch_types as $key => $value): ?>
                        <option value="<?= html($key) ?>"><?= html($value) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label required">الحالة</label>
                <select name="status" class="form-select" required>
                    <option value="active" selected>نشط</option>
                    <option value="inactive">غير نشط</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">الملاك</label>
            <select name="owners[]" class="form-select" multiple>
                <?php foreach ($owners as $owner): ?>
                    <option value="<?= $owner['id'] ?>"><?= html($owner['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">الجوال</label><input type="text" class="form-control" name="phone"></div>
            <div class="col-md-6 mb-3"><label class="form-label">البريد الإلكتروني</label><input type="email" class="form-control" name="email"></div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">رقم السجل التجاري</label><input type="text" class="form-control" name="cr_number"></div>
            <div class="col-md-6 mb-3"><label class="form-label">الرقم الضريبي</label><input type="text" class="form-control" name="tax_number"></div>
        </div>
        <div class="mb-3"><label class="form-label">العنوان</label><textarea name="address" class="form-control" rows="2"></textarea></div>
        <div class="mb-3"><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ الفرع</button>
    </div>
</form>