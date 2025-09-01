<?php
// app/Common/Views/users/add_view.php
global $pdo;

// جلب قائمة الأدوار
$roles_stmt = $pdo->query("SELECT id, role_name FROM roles WHERE deleted_at IS NULL ORDER BY role_name");
$roles = $roles_stmt->fetchAll();

// جلب قائمة الفروع النشطة
$branches_stmt = $pdo->query("SELECT id, branch_name FROM branches WHERE status = 'active' AND deleted_at IS NULL ORDER BY branch_name");
$branches = $branches_stmt->fetchAll();
?>

<form action="index.php?page=handle_user_add" method="post" class="ajax-form">
    <div class="modal-header">
        <h5 class="modal-title">إضافة مستخدم جديد</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">رقم الجوال</label>
            <input type="text" name="mobile" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">الدور <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-select">
                <option value="active" selected>نشط</option>
                <option value="inactive">غير نشط</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">الفروع المسموح بها</label>
            <p class="form-hint">إذا لم تختر أي فرع، سيتمكن المستخدم من رؤية بيانات كل الفروع.</p>
            <select name="branches[]" class="form-select" multiple>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['branch_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ المستخدم</button>
    </div>
</form>