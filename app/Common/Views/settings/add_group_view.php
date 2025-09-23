<?php // app/Common/Views/settings/add_group_view.php (النسخة المصححة) ?>

<div class="modal-header">
    <h5 class="modal-title">إضافة مجموعة خيارات جديدة</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_settings_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="add_group">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label required">اسم المجموعة (للعرض)</label>
            <input type="text" class="form-control" name="group_name" placeholder="مثال: أنواع العقارات" required>
        </div>
        <div class="mb-3">
            <label class="form-label required">المفتاح البرمجي للمجموعة</label>
            <input type="text" class="form-control" name="group_key" placeholder="مثال: property_types" required>
            <small class="form-hint">انجليزي، بدون مسافات، يستخدم للربط البرمجي.</small>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ المجموعة</button>
    </div>
</form>