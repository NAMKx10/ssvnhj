<?php
// app/Common/Views/roles/edit_role_view.php
global $pdo;

$role_id = (int)($_GET['id'] ?? 0);
if (!$role_id) { die("Role ID is required."); }

$stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
$stmt->execute([$role_id]);
$role = $stmt->fetch();
if (!$role) { die("Role not found."); }
?>

<div class="modal-header">
    <h5 class="modal-title">تعديل بيانات الدور</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_role_edit_details" class="ajax-form">
    <input type="hidden" name="id" value="<?= $role['id'] ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label required">اسم الدور</label>
            <input type="text" class="form-control" name="role_name" value="<?= html($role['role_name']) ?>" required <?= ($role['id'] <= 3) ? 'disabled' : '' ?>>
            <?php if ($role['id'] <= 3): ?>
                <small class="form-hint">لا يمكن تعديل اسم الأدوار الأساسية.</small>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label class="form-label">الوصف</label>
            <textarea class="form-control" name="description" rows="3"><?= html($role['description']) ?></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </div>
</form>