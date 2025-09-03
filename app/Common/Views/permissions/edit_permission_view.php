<?php
// app/Common/Views/permissions/edit_permission_view.php
global $pdo;

$permission_id = (int)($_GET['id'] ?? 0);
if (!$permission_id) { die("Permission ID is required."); }

$stmt = $pdo->prepare("SELECT * FROM permissions WHERE id = ?");
$stmt->execute([$permission_id]);
$permission = $stmt->fetch();
if (!$permission) { die("Permission not found."); }
?>

<div class="modal-header">
    <h5 class="modal-title">تعديل الصلاحية</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_permission_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="edit_permission">
    <input type="hidden" name="id" value="<?= $permission['id'] ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label required">الوصف</label>
            <input type="text" class="form-control" name="description" value="<?= html($permission['description']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label required">المفتاح البرمجي</label>
            <input type="text" class="form-control" name="permission_key" value="<?= html($permission['permission_key']) ?>" required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </div>
</form>