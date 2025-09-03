<?php // app/Common/Views/permissions/add_group_view.php ?>

<div class="modal-header">
    <h5 class="modal-title">إضافة مجموعة صلاحيات جديدة</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_permission_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="add_group">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label required">اسم المجموعة (للعرض)</label>
            <input type="text" class="form-control" name="group_name" placeholder="مثال: إدارة الفواتير" required>
        </div>
        <div class="mb-3">
            <label class="form-label required">المفتاح البرمجي (انجليزي، بدون مسافات)</label>
            <input type="text" class="form-control" name="group_key" placeholder="مثال: invoicing_management" required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ المجموعة</button>
    </div>
</form>