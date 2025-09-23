<?php
// app/Common/Views/settings/add_option_view.php (النسخة المصححة)
global $pdo;
$group_id = (int)($_GET['group_id'] ?? 0);
if (!$group_id) { die("Group ID is required."); }

// جلب اسم المجموعة للعرض
$group_stmt = $pdo->prepare("SELECT group_name FROM setting_groups WHERE id = ?");
$group_stmt->execute([$group_id]);
$group_name = $group_stmt->fetchColumn();
?>
<div class="modal-header">
    <h5 class="modal-title">إضافة خيار جديد لمجموعة: <strong><?= html($group_name) ?></strong></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_settings_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="add_option">
    <input type="hidden" name="group_id" value="<?= $group_id ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label required">القيمة المعروضة</label>
            <input type="text" class="form-control" name="option_value" placeholder="مثال: مبنى سكني" required>
        </div>
        <div class="mb-3">
            <label class="form-label required">المفتاح البرمجي للخيار</label>
            <input type="text" class="form-control" name="option_key" placeholder="مثال: residential_building" required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ الخيار</button>
    </div>
</form>