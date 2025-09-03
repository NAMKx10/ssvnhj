<?php
// app/Common/Views/permissions/edit_group_view.php
global $pdo;

$group_id = (int)($_GET['id'] ?? 0);
if (!$group_id) { die("Group ID is required."); }

$stmt = $pdo->prepare("SELECT * FROM permission_groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch();
if (!$group) { die("Group not found."); }
?>

<div class="modal-header">
    <h5 class="modal-title">تعديل مجموعة الصلاحيات</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_permission_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="edit_group">
    <input type="hidden" name="id" value="<?= $group['id'] ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label required">اسم المجموعة</label>
            <input type="text" class="form-control" name="group_name" value="<?= html($group['group_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label required">المفتاح البرمجي</label>
            <input type="text" class="form-control" name="group_key" value="<?= html($group['group_key']) ?>" required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </div>
</form>