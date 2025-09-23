<?php
// app/Common/Views/settings/edit_option_view.php (النسخة النهائية الصحيحة)
global $pdo;
$id = (int)($_GET['id'] ?? 0);
if (!$id) { die("ID is required."); }

$stmt = $pdo->prepare("SELECT * FROM settings WHERE id = ?");
$stmt->execute([$id]);
$option = $stmt->fetch();
if (!$option) { die("Option not found."); }

// جلب اسم المجموعة بناءً على group_id
$group_stmt = $pdo->prepare("SELECT group_name FROM setting_groups WHERE id = ?");
$group_stmt->execute([$option['group_id']]);
$group_name = $group_stmt->fetchColumn();
?>

<div class="modal-header">
    <h5 class="modal-title">تعديل خيار</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_settings_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="edit_option">
    <input type="hidden" name="id" value="<?= $option['id'] ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">المجموعة</label>
            <input type="text" class="form-control" value="<?= html($group_name ?: 'غير معروف') ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label required">القيمة المعروضة</label>
            <input type="text" class="form-control" name="option_value" value="<?= html($option['option_value']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label required">المفتاح البرمجي للخيار</label>
            <input type="text" class="form-control" name="option_key" value="<?= html($option['option_key']) ?>" required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </div>
</form>