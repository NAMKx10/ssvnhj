<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col"><h2 class="page-title">إدارة الوثائق (<?= html($total_records) ?>)</h2></div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn" onclick="window.print();"><i class="ti ti-printer me-2"></i> طباعة</a>
                    <?php if (has_permission('add_document')): ?>
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=documents/add&view_only=true"><i class="ti ti-plus"></i> إضافة وثيقة</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- محتوى الصفحة -->
<div class="page-body">
    <div class="container-xl">
        <!-- شريط الإحصائيات -->
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-primary text-white avatar"><i class="ti ti-file-text"></i></span></div><div class="col"><div class="font-weight-medium">إجمالي الوثائق</div><div class="text-muted"><?= $stats['total'] ?? 0 ?></div></div></div></div></div></div>
            <div class="col-md-4"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-warning text-white avatar"><i class="ti ti-alert-triangle"></i></span></div><div class="col"><div class="font-weight-medium">ستنتهي قريبًا</div><div class="text-muted"><?= $stats['expiring_soon'] ?? 0 ?></div></div></div></div></div></div>
            <div class="col-md-4"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-danger text-white avatar"><i class="ti ti-file-x"></i></span></div><div class="col"><div class="font-weight-medium">منتهية الصلاحية</div><div class="text-muted"><?= $stats['expired'] ?? 0 ?></div></div></div></div></div></div>
        </div>

        <div class="card">
            <!-- قسم الفلاتر -->
            <div class="card-body border-bottom py-3">
                <form action="index.php" method="GET">
                    <input type="hidden" name="page" value="documents">
                    <div class="row g-3">
                        <div class="col-md-4"><input type="search" name="q" class="form-control" placeholder="بحث بالاسم، الكود، الأرقام المرجعية..." value="<?= html($filter_q) ?>"></div>
                        <div class="col-md-3"><select name="type" class="form-select"><option value="">كل الأنواع</option><?php foreach($types_list as $key => $value){ echo "<option value='".html($key)."' ".($filter_type == $key ? 'selected' : '').">".html($value)."</option>"; } ?></select></div>
                        <div class="col-md-3"><select name="status" class="form-select"><option value="">كل الحالات</option><?php foreach($statuses_list as $key => $value){ echo "<option value='".html($key)."' ".($filter_status == $key ? 'selected' : '').">".html($value)."</option>"; } ?></select></div>
                        <div class="col-md-2 d-flex"><button type="submit" class="btn btn-primary w-100 me-2">فلترة</button><a href="index.php?page=documents" class="btn btn-icon" title="إعادة تعيين"><i class="ti ti-refresh"></i></a></div>
                    </div>
                </form>
            </div>

            <form action="index.php?page=handle_document_actions" method="post" id="batch-form">
                <input type="hidden" name="form_action" id="batch-action-input">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap table-hover">
                        <thead>
                            <tr>
                                <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" onchange="toggleAllCheckboxes(this)"></th>
                                <th>#</th>
                                <th>الوثيقة</th>
                                <th>النوع</th>
                                <th>الأرقام المرجعية</th>
                                <th>التواريخ</th>
                                <th>الروابط</th>
                                <th>الحالة</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documents)): ?>
                                <tr><td colspan="9" class="text-center p-4">لا توجد نتائج.</td></tr>
                            <?php else: $i = $offset + 1; foreach($documents as $doc): ?>
                            <tr>
                                <td><input class="form-check-input m-0 align-middle" type="checkbox" name="ids[]" value="<?= $doc['id'] ?>"></td>
                                <td><span class="text-muted"><?= $i++ ?></span></td>
                                <td><div><strong><?= html($doc['document_title']) ?></strong></div><div class="text-muted"><?= html($doc['document_code']) ?></div></td>
                                <td><span class="badge bg-blue-lt"><?= html($doc['document_type_value']) ?></span></td>
                                <td><div><small>أساسي:</small> <?= html($doc['reference_number_1']) ?></div><div class="text-muted"><small>إضافي:</small> <?= html($doc['reference_number_2']) ?></div></td>
                                <td><div><small>إصدار:</small> <?= format_date($doc['issue_date']) ?></div><div class="text-muted"><small>انتهاء:</small> <?= format_date($doc['expiry_date']) ?></div></td>
                                <td><span class="badge bg-secondary-lt"><?= $doc['links_count'] ?></span></td>
                                <td><span class="badge bg-<?= ($doc['status'] === 'active') ? 'success' : 'danger' ?> me-1"></span><?= html($statuses_list[$doc['status']] ?? $doc['status']) ?></td>
                                <td class="text-end">
                                    <div class="btn-list flex-nowrap">
                                        <a href="<?= html($doc['file_link']) ?>" target="_blank" class="btn btn-ghost-info btn-icon" title="عرض الملف"><i class="ti ti-external-link"></i></a>
                                        <?php if (has_permission('edit_document')): ?>
                                            <a href="#" class="btn btn-ghost-primary btn-icon" title="تعديل" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=documents/edit&id=<?= $doc['id'] ?>&view_only=true"><i class="ti ti-pencil"></i></a>
                                        <?php endif; ?>
                                        <div class="dropdown">
                                            <button class="btn dropdown-toggle align-text-top" data-bs-toggle="dropdown">الإجراءات</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#"><i class="ti ti-file-text me-2"></i> طباعة ملف الوثيقة</a>
                                                <?php if (has_permission('delete_document')): ?>
                                                    <a class="dropdown-item text-danger confirm-delete" href="index.php?page=handle_document_actions&action=delete&id=<?= $doc['id'] ?>"><i class="ti ti-trash me-2"></i> حذف (أرشفة)</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>

            <div class="card-footer d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <?php if (has_permission('edit_document') || has_permission('delete_document')): ?>
                    <div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        إجراءات جماعية
    </button>
    <div class="dropdown-menu">
        <?php if (has_permission('edit_document')): ?>
            <!-- (ملاحظة: التعديل الجماعي للوثائق معقد بسبب حقوله المتعددة، يمكن إضافته كميزة متقدمة لاحقًا) -->
            <a class="dropdown-item" href="#" onclick="submitBatchAction('activate', 'batch-form')">
                <i class="ti ti-check me-2"></i> تفعيل المحدد
            </a>
            <a class="dropdown-item" href="#" onclick="submitBatchAction('deactivate', 'batch-form')">
                <i class="ti ti-ban me-2"></i>  تعطيل المحدد
            </a>
        <?php endif; ?>
        <?php if (has_permission('delete_document')): ?>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="#" onclick="submitBatchAction('delete', 'batch-form')">
                <i class="ti ti-trash me-2"></i> أرشفة المحدد
            </a>
        <?php endif; ?>
    </div>
</div>
                    <?php endif; ?>
                    <select class="form-select form-select-sm ms-2" style="width: 80px;" onchange="window.location.href = '?page=documents&limit=' + this.value;">
                        <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= ($limit == 20) ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= ($limit == 50) ? 'selected' : '' ?>>50</option>
                    </select>
                </div>
                <?php if ($total_pages > 1): ?>
                    <div class="m-auto"><?php render_pagination($current_page, $total_pages, $_GET); ?></div>
                <?php endif; ?>
                <p class="m-0 text-muted">عرض <span><?= count($documents) ?></span> من <span><?= $total_records ?></span> سجل</p>
            </div>
        </div>
    </div>
</div>