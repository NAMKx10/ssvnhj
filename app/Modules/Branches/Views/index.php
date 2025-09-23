<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col"><h2 class="page-title">إدارة الفروع (<?= html($total_records) ?>)</h2></div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn" onclick="window.print();"><i class="ti ti-printer me-2"></i> طباعة</a>
                    <?php if (has_permission('add_branch')): ?>
                        <a href="index.php?page=branches/batch_add" class="btn btn-primary">
            <i class="ti ti-table-plus me-2"></i> إضافة جماعية
        </a>
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=branches/add&view_only=true">
                            <i class="ti ti-plus"></i> إضافة فرع جديد
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
        <!-- شريط الإحصائيات -->
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-primary text-white avatar"><i class="ti ti-building-community"></i></span></div><div class="col"><div class="font-weight-medium">إجمالي الفروع</div><div class="text-muted"><?= $stats['total'] ?? 0 ?></div></div></div></div></div></div>
            <div class="col-md-4"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-success text-white avatar"><i class="ti ti-checks"></i></span></div><div class="col"><div class="font-weight-medium">النشطة</div><div class="text-muted"><?= $stats['active'] ?? 0 ?></div></div></div></div></div></div>
            <div class="col-md-4"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-danger text-white avatar"><i class="ti ti-ban"></i></span></div><div class="col"><div class="font-weight-medium">غير النشطة</div><div class="text-muted"><?= $stats['inactive'] ?? 0 ?></div></div></div></div></div></div>
        </div>

        <div class="card">
            <!-- قسم الفلاتر -->
            <div class="card-body border-bottom py-3">
                <form action="index.php" method="GET">
                    <input type="hidden" name="page" value="branches">
                    <div class="row g-3">
                        <div class="col-md-4"><input type="search" name="q" class="form-control" placeholder="بحث بالاسم، الكود، الجوال، السجل..." value="<?= html($filter_q) ?>"></div>
                        <div class="col-md-3"><select name="type" class="form-select"><option value="">كل الأنواع</option><?php foreach($types_list as $key => $value){ echo "<option value='".html($key)."' ".($filter_type == $key ? 'selected' : '').">".html($value)."</option>"; } ?></select></div>
                        <div class="col-md-3"><select name="status" class="form-select"><option value="">كل الحالات</option><?php foreach($statuses_list as $key => $value){ echo "<option value='".html($key)."' ".($filter_status == $key ? 'selected' : '').">".html($value)."</option>"; } ?></select></div>
                        <div class="col-md-2 d-flex"><button type="submit" class="btn btn-primary w-100 me-2">فلترة</button><a href="index.php?page=branches" class="btn btn-icon" title="إعادة تعيين"><i class="ti ti-refresh"></i></a></div>
                    </div>
                </form>
            </div>

            <form action="index.php?page=handle_branch_actions" method="post" id="batch-form">
                <input type="hidden" name="action" id="batch-action-input">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap table-hover">
                        <thead>
                            <tr>
                                <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" onchange="toggleAllCheckboxes(this)"></th>
                                <th>#</th>
                                <th>الفرع</th>
                                <th>المعرفات</th>
                                <th>معلومات التواصل</th>
                                <th>الملاك</th>
                                <th>الحالة</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($branches)): ?>
                                <tr><td colspan="8" class="text-center p-4">لا توجد نتائج تطابق بحثك.</td></tr>
                            <?php else: $i = $offset + 1; foreach($branches as $branch): ?>
                            <tr>
                                <td><input class="form-check-input m-0 align-middle" type="checkbox" name="ids[]" value="<?= $branch['id'] ?>"></td>
                                <td><span class="text-muted"><?= $i++ ?></span></td>
                                <td><div><strong><?= html($branch['branch_name']) ?></strong></div><div class="text-muted"><?= html($branch['branch_type_value']) ?></div></td>
                                <td><div><small>الكود:</small> <code><?= html($branch['branch_code']) ?></code></div><div class="text-muted"><small>السجل:</small> <?= html($branch['cr_number']) ?></div><div class="text-muted"><small>الضريبي:</small> <?= html($branch['tax_number']) ?></div></td>
                                <td><div><?= html($branch['phone']) ?></div><div class="text-muted"><?= html($branch['email']) ?></div></td>
                                <td><span class="text-muted"><?= html($branch['owners']) ?></span></td>
                                <td><span class="badge bg-<?= ($branch['status'] === 'active') ? 'success' : 'danger' ?> me-1"></span><?= html($statuses_list[$branch['status']] ?? $branch['status']) ?></td>
                                <td class="text-end">
                                    <div class="btn-list flex-nowrap">
                                        <?php if (has_permission('edit_branch')): ?>
                                            <a href="#" class="btn btn-ghost-primary btn-icon" title="تعديل" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=branches/edit&id=<?= $branch['id'] ?>&view_only=true"><i class="ti ti-pencil"></i></a>
                                        <?php endif; ?>
                                        <div class="dropdown">
                                            <button class="btn dropdown-toggle align-text-top" data-bs-toggle="dropdown">الإجراءات</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#"><i class="ti ti-file-text me-2"></i> طباعة ملف الفرع</a>
                                                <?php if (has_permission('delete_branch')): ?>
                                                    <a class="dropdown-item text-danger confirm-delete" href="index.php?page=handle_branch_actions&action=delete&id=<?= $branch['id'] ?>"><i class="ti ti-trash me-2"></i> حذف (أرشفة)</a>
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
                    <?php if (has_permission('edit_branch') || has_permission('delete_branch')): ?>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">إجراءات جماعية</button>
                        <div class="dropdown-menu">
                            <?php if (has_permission('edit_branch')): ?>
                                <a class="dropdown-item" href="#" onclick="redirectToBatchEdit('branches', 'batch_edit')">
<i class="ti ti-pencil me-2"></i> تعديل جماعي
</a>
    <a class="dropdown-item" href="#" onclick="submitBatchAction('activate', 'batch-form')"><i class="ti ti-user-check me-2"></i>تفعيل المحدد</a>
                                <a class="dropdown-item" href="#" onclick="submitBatchAction('deactivate', 'batch-form')"><i class="ti ti-user-x me-2"></i>تعطيل المحدد</a>
                            <?php endif; ?>
                            <?php if (has_permission('delete_branch')): ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#" onclick="submitBatchAction('delete', 'batch-form')"><i class="ti ti-trash me-2"></i>أرشفة المحدد</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <select class="form-select form-select-sm ms-2" style="width: 80px;" onchange="window.location.href = '?page=branches&limit=' + this.value;">
                        <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= ($limit == 20) ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= ($limit == 50) ? 'selected' : '' ?>>50</option>
                    </select>
                </div>
                <?php if ($total_pages > 1): ?>
                    <div class="m-auto"><?php render_pagination($current_page, $total_pages, $_GET); ?></div>
                <?php endif; ?>
                <p class="m-0 text-muted">عرض <span><?= count($branches) ?></span> من <span><?= $total_records ?></span> سجل</p>
            </div>
        </div>
    </div>
</div>