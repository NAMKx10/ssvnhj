<?php
// app/Modules/Contacts/Views/add_view.php
global $pdo;

// جلب بيانات الأدوار من جدول settings
$roles_stmt = $pdo->query("
    SELECT s.option_key, s.option_value 
    FROM settings s
    JOIN setting_groups sg ON s.group_id = sg.id
    WHERE sg.group_key = 'contact_roles' AND s.deleted_at IS NULL
    ORDER BY s.option_value
");
$roles = $roles_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// جلب بيانات الفروع النشطة
$branches_stmt = $pdo->query("SELECT id, branch_name FROM branches WHERE status = 'active' AND deleted_at IS NULL ORDER BY branch_name");
$branches = $branches_stmt->fetchAll();
?>

<div class="modal-header">
    <h5 class="modal-title">إضافة جهة اتصال جديدة</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_contact_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="add_contact">
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label required">الاسم الكامل / الشركة</label>
                <input type="text" class="form-control" name="full_name" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">الكود / الاسم المختصر</label>
                <input type="text" class="form-control" name="short_code">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label required">نوع جهة الاتصال</label>
                <select name="contact_type" class="form-select" required>
                    <option value="فرد">فرد</option>
                    <option value="منشأة">منشأة</option>
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
            <label class="form-label required">الدور/الأدوار</label>
            <select name="roles[]" class="form-select" multiple required>
                <?php foreach ($roles as $key => $value): ?>
                    <option value="<?= html($key) ?>"><?= html($value) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">الفروع</label>
            <select name="branches[]" class="form-select" multiple>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= $branch['id'] ?>"><?= html($branch['branch_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">رقم الهوية / السجل</label>
                <input type="text" class="form-control" name="id_number">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">الرقم الضريبي</label>
                <input type="text" class="form-control" name="tax_number">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">الجوال</label>
                <input type="text" class="form-control" name="primary_phone">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" class="form-control" name="primary_email">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">العنوان</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">ملاحظات</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ جهة الاتصال</button>
    </div>
</form>