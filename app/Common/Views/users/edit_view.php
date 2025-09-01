<?php
// app/Common/Views/users/edit_view.php
global $pdo;

// جلب ID المستخدم من الرابط
$user_id = $_GET['id'] ?? 0;
if (!$user_id) { die("User ID is missing."); }

// جلب بيانات المستخدم المحدد
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();
if (!$user) { die("User not found."); }

// جلب قائمة الأدوار
$roles_stmt = $pdo->query("SELECT id, role_name FROM roles WHERE deleted_at IS NULL ORDER BY role_name");
$roles = $roles_stmt->fetchAll();

// جلب قائمة الفروع النشطة
$branches_stmt = $pdo->query("SELECT id, branch_name FROM branches WHERE status = 'active' AND deleted_at IS NULL ORDER BY branch_name");
$branches = $branches_stmt->fetchAll();

// جلب الفروع المرتبطة حاليًا بهذا المستخدم
$current_branches_stmt = $pdo->prepare("SELECT branch_id FROM user_branches WHERE user_id = ?");
$current_branches_stmt->execute([$user_id]);
$current_branch_ids = $current_branches_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<form action="index.php?page=handle_user_edit" method="post" class="ajax-form">
    <input type="hidden" name="id" value="<?= $user['id'] ?>">
    <div class="modal-header">
        <h5 class="modal-title">تعديل بيانات المستخدم</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
        </div>
        <div class="mb-3">
    <label class="form-label">رقم الجوال</label>
    <input type="text" name="mobile" class="form-control" value="<?= htmlspecialchars($user['mobile']) ?>">
</div>
        <div class="mb-3">
            <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-control" placeholder="اتركه فارغًا لعدم التغيير">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">الدور <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= ($user['role_id'] == $role['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['role_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
         <div class="mb-3">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-select">
                <option value="active" <?= ($user['status'] == 'active') ? 'selected' : '' ?>>نشط</option>
                <option value="inactive" <?= ($user['status'] == 'inactive') ? 'selected' : '' ?>>غير نشط</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">الفروع المسموح بها</label>
            <p class="form-hint">إذا لم تختر أي فرع، سيتمكن المستخدم من رؤية بيانات كل الفروع.</p>
            <select name="branches[]" class="form-select" multiple>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= $branch['id'] ?>" <?= in_array($branch['id'], $current_branch_ids) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($branch['branch_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </div>
</form>