<?php
global $pdo;
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT id, file_name FROM files WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) { die("Item not found."); }
?>
<div class="modal-header">
    <h5 class="modal-title">إعادة تسمية</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="index.php?page=handle_file_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="rename">
    <input type="hidden" name="id" value="<?= $item['id'] ?>">
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label required">الاسم الجديد</label>
            <input type="text" class="form-control" name="new_name" value="<?= html($item['file_name']) ?>" required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ</button>
    </div>
</form>