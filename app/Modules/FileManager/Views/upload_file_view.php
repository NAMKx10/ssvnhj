<?php $parent_id = (int)($_GET['folder_id'] ?? 0); ?>
<div class="modal-header">
<h5 class="modal-title">رفع ملف جديد</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_file_actions" class="ajax-form" enctype="multipart/form-data">
<input type="hidden" name="form_action" value="upload_file">
<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
<div class="modal-body">
<div class="mb-3">
<label class="form-label required">اختر الملف</label>
<input type="file" class="form-control" name="file_to_upload" required>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
<button type="submit" class="btn btn-primary">رفع</button>
</div>
</form>