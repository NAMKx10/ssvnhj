<?php
// app/Common/Views/permissions/add_permission_view.php

$group_id = (int)($_GET['group_id'] ?? 0);
if (!$group_id) { die("Group ID is required."); }
?>
<div class="modal-header">
    <h5 class="modal-title">إضافة صلاحية جديدة</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_permission_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="add_permission">
    <input type="hidden" name="group_id" value="<?= $group_id ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label required">الوصف (ماذا تفعل الصلاحية)</label>
            <input type="text" class="form-control" name="description" placeholder="مثال: إنشاء فاتورة جديدة" required>
        </div>
        <div class="mb-3">
            <label class="form-label required">المفتاح البرمجي (انجليزي، بدون مسافات)</label>
            <input type="text" class="form-control" name="permission_key" placeholder="مثال: add_invoice" required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ الصلاحية</button>
    </div>
</form>