<?php
// app/Common/Views/settings/edit_group_view.php (النسخة النهائية الصحيحة)
global $pdo;

$id = (int)($_GET['id'] ?? 0);
if (!$id) { die("Group ID is required."); }

$stmt = $pdo->prepare("SELECT * FROM setting_groups WHERE id = ?");
$stmt->execute([$id]);
$group = $stmt->fetch();
if (!$group) { die("Group not found."); }
?>

<div class="modal-header">
    <h5 class="modal-title">تعديل المجموعة</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_settings_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="edit_group">
    <input type="hidden" name="id" value="<?= $group['id'] ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label required">اسم المجموعة (للعرض)</label>
            <input type="text" class="form-control" name="group_name" value="<?= html($group['group_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label required">المفتاح البرمجي</label>
            <input type="text" class="form-control" name="group_key" value="<?= html($group['group_key']) ?>" <?= $group['is_core'] ? 'disabled' : '' ?> required>
            <?php if ($group['is_core']): ?>
                <small class="form-hint">لا يمكن تعديل المفتاح البرمجي للمجموعات الأساسية.</small>
            <?php endif; ?>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </div>
</form>