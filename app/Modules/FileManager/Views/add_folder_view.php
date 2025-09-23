<?php $parent_id = (int)($_GET['folder_id'] ?? 0); ?>
<div class="modal-header">
    <h5 class="modal-title">إنشاء مجلد جديد</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_file_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="create_folder">
    <input type="hidden" name="parent_id" value="<?= $parent_id ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label required">اسم المجلد</label>
            <input type="text" class="form-control" name="folder_name" required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">إنشاء</button>
    </div>
</form>