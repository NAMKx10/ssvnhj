<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col"><h2 class="page-title">مدير الملفات - هذا الموديل معطل وتم تاجيله لوجود مشكلة في الحذف</h2></div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <?php if (has_permission('create_folders')): ?>
                            <a href="#" class="btn" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=file-manager/add_folder&folder_id=<?= $current_folder_id ?>&view_only=true">
                                <i class="ti ti-folder-plus me-2"></i>إنشاء مجلد
                            </a>
                        <?php endif; ?>
                        <?php if (has_permission('upload_files')): ?>
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=file-manager/upload_file&folder_id=<?= $current_folder_id ?>&view_only=true">
                                <i class="ti ti-upload me-2"></i>رفع ملف
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
        </div>
    </div>
</div>

<!-- محتوى الصفحة -->
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <!-- شريط التنقل (Breadcrumbs) -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-arrows mb-0">
                        <li class="breadcrumb-item"><a href="index.php?page=file-manager">الرئيسية</a></li>
                        <?php foreach ($breadcrumbs as $breadcrumb): ?>
                            <li class="breadcrumb-item"><a href="index.php?page=file-manager&folder_id=<?= $breadcrumb['id'] ?>"><?= html($breadcrumb['file_name']) ?></a></li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            </div>
            <!-- شريط الإجراءات الجماعية -->
<div class="card-body border-bottom py-3" id="batch-actions-toolbar" style="display: none;">
                <div class="d-flex">
                    <div class="text-muted">تم تحديد <span id="selected-count">0</span> عنصر.</div>
                    <div class="ms-auto">
                        <div class="btn-list">
                            <?php if (has_permission('delete_files')): ?>
                                <a href="#" class="btn btn-danger" onclick="submitBatchAction('delete', 'batch-form')">
                                    <i class="ti ti-trash me-2"></i> حذف المحدد
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

<form action="index.php?page=handle_file_actions" method="POST" id="batch-form">
    <input type="hidden" name="form_action" id="batch-action-input">
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap table-hover">
                    <thead>
                        <tr>
                            <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" onchange="toggleAllCheckboxes(this); updateBatchActionsToolbar();"></th>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>الحجم</th>
                            <th>تاريخ الإنشاء</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr><td colspan="6" class="text-center p-4">هذا المجلد فارغ.</td></tr>
                        <?php else: foreach($items as $item): ?>
                        <tr>
                            <td><input class="form-check-input m-0 align-middle" type="checkbox" name="ids[]" value="<?= $item['id'] ?>" onchange="updateBatchActionsToolbar()"></td>
                            <td>
                                <?php if ($item['file_type'] === 'folder'): ?>
                                    <a href="index.php?page=file-manager&folder_id=<?= $item['id'] ?>" class="text-reset d-flex align-items-center">
                                        <i class="ti ti-folder me-2"></i>
                                        <?= html($item['file_name']) ?>
                                    </a>
                                <?php else: ?>
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-file me-2"></i>
                                        <?= html($item['file_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= $item['file_type'] === 'folder' ? 'مجلد' : html($item['file_extension']) ?></td>
                            <td><?= $item['file_size'] ? round($item['file_size'] / 1024, 2) . ' KB' : '--' ?></td>
                            <td><?= format_date($item['created_at'], 'Y-m-d H:i') ?></td>
                            <td class="text-end">
    <div class="btn-list flex-nowrap">
        <?php if ($item['file_type'] === 'file' && has_permission('download_files')): ?>
            <a href="index.php?page=handle_file_actions&action=download&id=<?= $item['id'] ?>" class="btn btn-ghost-info btn-icon" title="تنزيل">
                <i class="ti ti-download"></i>
            </a>
        <?php endif; ?>
        <?php if (has_permission('rename_files')): ?>
            <a href="#" class="btn btn-ghost-secondary btn-icon" title="إعادة تسمية" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=file-manager/rename&id=<?= $item['id'] ?>&view_only=true">
                <i class="ti ti-pencil"></i>
            </a>
        <?php endif; ?>
        <?php if (has_permission('delete_files')): ?>
            <a href="index.php?page=handle_file_actions&action=delete&id=<?= $item['id'] ?>" class="btn btn-ghost-danger btn-icon confirm-delete" title="حذف">
                <i class="ti ti-trash"></i>
            </a>
        <?php endif; ?>
    </div>
</td>
                        </tr>
                        <?php endforeach; endif; ?>

                    </tbody>
                </table>
            </div>
            </form>
        <div class="card-footer d-flex align-items-center justify-content-between">
                <p class="m-0 text-muted">عرض <span><?= count($items) ?></span> من <span><?= $total_records ?></span> سجل</p>
                <?php if ($total_pages > 1): ?>
                    <div class="m-auto"><?php render_pagination($current_page, $total_pages, $_GET); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>