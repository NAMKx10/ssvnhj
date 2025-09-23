<?php
// app/Modules/Documents/Views/edit_view.php (النسخة النهائية - jQuery)
global $pdo;

$id = (int)($_GET['id'] ?? 0);
if (!$id) { die("ID is required."); }

// 1. جلب بيانات الوثيقة
$doc_stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
$doc_stmt->execute([$id]);
$doc = $doc_stmt->fetch();
if (!$doc) { die("Document not found."); }

// 2. جلب أنواع الوثائق
$types_stmt = $pdo->query("SELECT s.option_key, s.option_value FROM settings s JOIN setting_groups sg ON s.group_id = sg.id WHERE sg.group_key = 'document_types'");
$doc_types = $types_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// 3. جلب الروابط الحالية لهذه الوثيقة
$links_stmt = $pdo->prepare("SELECT target_model, target_id FROM links WHERE source_model = 'document' AND source_id = ?");
$links_stmt->execute([$id]);
$current_links = $links_stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!-- قالب HTML لصفوف الروابط (نفس قالب الإضافة) -->
<template id="link-row-template">
    <div class="row g-2 mb-2 align-items-center link-row">
        <div class="col-md-5">
            <select name="links[__COUNTER__][target_model]" class="form-select link-model-select">
                <option value="">-- اختر نوع الكيان --</option>
                <option value="contact">جهة اتصال</option>
                <option value="branch">فرع</option>
            </select>
        </div>
        <div class="col-md-6">
            <select name="links[__COUNTER__][target_id]" class="form-select link-target-select">
                <option value="">-- اختر الكيان --</option>
            </select>
        </div>
        <div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-icon delete-link-btn"><i class="ti ti-trash"></i></button></div>
    </div>
</template>

<div class="modal-header"><h5 class="modal-title">تعديل وثيقة: <?= html($doc['document_title']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form method="POST" action="index.php?page=handle_document_actions" class="ajax-form">
    <input type="hidden" name="form_action" value="edit_document">
    <input type="hidden" name="id" value="<?= $doc['id'] ?>">
    <div class="modal-body" x-data="documentForm()">
        <div class="row"><div class="col-md-8 mb-3"><label class="form-label required">اسم الوثيقة</label><input type="text" class="form-control" name="document_title" value="<?= html($doc['document_title']) ?>" required></div><div class="col-md-4 mb-3"><label class="form-label">كود الوثيقة</label><input type="text" class="form-control" name="document_code" value="<?= html($doc['document_code']) ?>"></div></div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label required">نوع الوثيقة</label><select name="document_type_key" class="form-select" required><?php foreach ($doc_types as $key => $value){ echo "<option value='".html($key)."' ".($doc['document_type_key'] == $key ? 'selected' : '').">".html($value)."</option>"; } ?></select></div><div class="col-md-6 mb-3"><label class="form-label required">الحالة</label><select name="status" class="form-select" required><option value="active" <?= $doc['status'] == 'active' ? 'selected' : '' ?>>نشط</option><option value="inactive" <?= $doc['status'] == 'inactive' ? 'selected' : '' ?>>غير نشط</option><option value="expired" <?= $doc['status'] == 'expired' ? 'selected' : '' ?>>منتهي الصلاحية</option></select></div></div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">تاريخ الإصدار</label><input type="date" class="form-control" name="issue_date" value="<?= html($doc['issue_date']) ?>"></div><div class="col-md-6 mb-3"><label class="form-label">تاريخ الانتهاء (اختياري)</label><input type="date" class="form-control" name="expiry_date" value="<?= html($doc['expiry_date']) ?>"></div></div>
        <div class="mb-3"><label class="form-label">الرابط الخارجي للملف</label><input type="url" class="form-control" name="file_link" placeholder="https://..." value="<?= html($doc['file_link']) ?>"></div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">رقم مرجعي أساسي</label><input type="text" class="form-control" name="reference_number_1" value="<?= html($doc['reference_number_1']) ?>"></div><div class="col-md-6 mb-3"><label class="form-label">رقم مرجعي إضافي</label><input type="text" class="form-control" name="reference_number_2" value="<?= html($doc['reference_number_2']) ?>"></div></div>
        <div class="mb-3"><label class="form-label">نص الوثيقة</label><textarea name="document_content" class="form-control" rows="4"><?= html($doc['document_content']) ?></textarea></div>
        <div class="mb-3"><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="2"><?= html($doc['notes']) ?></textarea></div>
        <hr><h3 class="mb-3">الربط مع كيانات أخرى</h3>
            <div id="links-container"></div>
                <?php foreach ($current_links as $index => $link): ?>
                <div class="row g-2 mb-2 align-items-center link-row">
                    <div class="col-md-5">
                        <select name="links[<?= $index ?>][target_model]" class="form-select link-model-select" data-selected-id="<?= $link['target_id'] ?>">
                            <option value="">-- اختر نوع الكيان --</option>
                            <option value="contact" <?= ($link['target_model'] == 'contact') ? 'selected' : '' ?>>جهة اتصال</option>
                            <option value="branch" <?= ($link['target_model'] == 'branch') ? 'selected' : '' ?>>فرع</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="links[<?= $index ?>][target_id]" class="form-select link-target-select">
                            <option value="">-- جاري التحميل... --</option>
                        </select>
                    </div>
                    <div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-icon delete-link-btn"><i class="ti ti-trash"></i></button></div>
                </div>
                 <?php endforeach; ?>
           
            <button type="button" id="add-link-btn" class="btn btn-outline-primary btn-sm"><i class="ti ti-plus me-1"></i> إضافة رابط جديد</button>
        </div>
    <div class="modal-footer"><button type="button" class="btn" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">حفظ التعديلات</button></div>
</form>
<!-- جافاسكريبت لتشغيل القوائم المنسدلة الحالية -->
