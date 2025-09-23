<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">إدارة جهات الاتصال (<?= html($total_records) ?>)</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn" onclick="window.print();"><i class="ti ti-printer me-2"></i> طباعة</a>
                    <?php if (has_permission('add_contact')): ?>
                        <a href="index.php?page=contacts/batch_add" class="btn btn-primary">
                            <i class="ti ti-table-plus me-2"></i> إضافة جماعية
                        </a>
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=contacts/add&view_only=true">
                            <i class="ti ti-plus"></i> إضافة جهة اتصال
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
            <div class="col-md-3"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-primary text-white avatar"><i class="ti ti-users"></i></span></div><div class="col"><div class="font-weight-medium">إجمالي الجهات</div><div class="text-muted"><?= $stats['total'] ?? 0 ?></div></div></div></div></div></div>
            <div class="col-md-3"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-success text-white avatar"><i class="ti ti-user-check"></i></span></div><div class="col"><div class="font-weight-medium">العملاء</div><div class="text-muted"><?= $stats['clients'] ?? 0 ?></div></div></div></div></div></div>
            <div class="col-md-3"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-warning text-white avatar"><i class="ti ti-truck"></i></span></div><div class="col"><div class="font-weight-medium">الموردون</div><div class="text-muted"><?= $stats['suppliers'] ?? 0 ?></div></div></div></div></div></div>
            <div class="col-md-3"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-info text-white avatar"><i class="ti ti-user-circle"></i></span></div><div class="col"><div class="font-weight-medium">الموظفون</div><div class="text-muted"><?= $stats['employees'] ?? 0 ?></div></div></div></div></div></div>
        </div>

        <div class="card">
            <!-- قسم الفلاتر المتقدمة -->
            <div class="card-body border-bottom py-3">
                <form action="index.php" method="GET">
                    <input type="hidden" name="page" value="contacts">
                    <div class="row g-3">
                        <div class="col-md-3"><input type="search" name="q" class="form-control" placeholder="بحث عام..." value="<?= html($filter_q) ?>"></div>
                        <div class="col-md-2">
                            <select name="role" class="form-select">
                                <option value="">كل الأدوار</option>
                                <?php foreach($roles_list as $key => $value): ?>
                                    <option value="<?= html($key) ?>" <?= ($filter_role == $key) ? 'selected' : '' ?>><?= html($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <?php foreach($types_list as $key => $value): ?>
                                    <option value="<?= html($key) ?>" <?= ($filter_type == $key) ? 'selected' : '' ?>><?= html($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <?php foreach($statuses_list as $key => $value): ?>
                                    <option value="<?= html($key) ?>" <?= ($filter_status == $key) ? 'selected' : '' ?>><?= html($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex">
                            <button type="submit" class="btn btn-primary w-100 me-2">فلترة</button>
                            <a href="index.php?page=contacts" class="btn btn-icon" title="إعادة تعيين"><i class="ti ti-refresh"></i></a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- النموذج الخاص بالإجراءات الجماعية -->
            <form action="index.php?page=handle_contact_actions" method="post" id="batch-form">
                <input type="hidden" name="form_action" id="batch-action-input">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap table-hover">
                        <thead>
                            <tr>
                                <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" onchange="toggleAllCheckboxes(this)"></th>
                                <th>#</th>
                                <th>جهة الاتصال</th>
                                <th>المعرفات</th>
                                <th>معلومات التواصل</th>
                                <th>الأدوار</th>
                                <th>الحالة</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contacts)): ?>
                                <tr><td colspan="8" class="text-center p-4">لا توجد نتائج تطابق بحثك.</td></tr>
                            <?php else: $i = $offset + 1; foreach($contacts as $contact): ?>
                            <tr>
                                <td><input class="form-check-input m-0 align-middle" type="checkbox" name="ids[]" value="<?= $contact['id'] ?>"></td>
                                <td><span class="text-muted"><?= $i++ ?></span></td>
                                <td>
                                    <div><strong><?= html($contact['full_name']) ?></strong></div>
                                    <div class="text-muted"><?= html($contact['contact_type']) ?></div>
                                </td>
                                <td>
                                    <div><small>الكود:</small> <code><?= html($contact['short_code']) ?></code></div>
                                    <div class="text-muted"><small>السجل:</small> <?= html($contact['id_number']) ?></div>
                                    <div class="text-muted"><small>الضريبي:</small> <?= html($contact['tax_number']) ?></div>
                                </td>
                                <td>
                                    <div><?= html($contact['primary_phone']) ?></div>
                                    <div class="text-muted"><?= html($contact['primary_email']) ?></div>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($contact['roles'])) {
                                        $contact_roles = explode(',', $contact['roles']);
                                        foreach ($contact_roles as $role_key) {
                                            echo '<span class="badge bg-purple-lt m-1">' . html($roles_list[$role_key] ?? $role_key) . '</span>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= ($contact['status'] === 'active') ? 'success' : 'danger' ?> me-1"></span>
                                    <?= html($statuses_list[$contact['status']] ?? $contact['status']) ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-list flex-nowrap">
                                        <?php if (has_permission('edit_contact')): ?>
                                            <a href="#" class="btn btn-ghost-primary btn-icon" title="تعديل" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=contacts/edit&id=<?= $contact['id'] ?>&view_only=true">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <div class="dropdown">
                                            <button class="btn dropdown-toggle align-text-top" data-bs-toggle="dropdown">الإجراءات</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#">
                                                    <i class="ti ti-file-text me-2"></i> طباعة ملف الجهة
                                                </a>
                                                <?php if (has_permission('delete_contact')): ?>
                                                    <a class="dropdown-item text-danger confirm-delete" href="index.php?page=handle_contact_actions&action=delete&id=<?= $contact['id'] ?>">
                                                        <i class="ti ti-trash me-2"></i> حذف (أرشفة)
                                                    </a>
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
            
            <!-- تذييل الجدول -->
            <div class="card-footer d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <?php if (has_permission('edit_contact') || has_permission('delete_contact')): ?>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            إجراءات جماعية
                        </button>
                        <div class="dropdown-menu">
                            <?php if (has_permission('edit_contact')): ?>
                                <a class="dropdown-item" href="#" onclick="redirectToBatchEdit('contacts', 'batch_edit')">
                                    <i class="ti ti-pencil me-2"></i> تعديل جماعي
                                </a>
                                <a class="dropdown-item" href="#" onclick="submitBatchAction('activate', 'batch-form')">
                                    <i class="ti ti-user-check me-2"></i> تفعيل المحدد
                                </a>
                                <a class="dropdown-item" href="#" onclick="submitBatchAction('deactivate', 'batch-form')">
                                    <i class="ti ti-user-x me-2"></i> تعطيل المحدد
                                </a>
                            <?php endif; ?>
                            <?php if (has_permission('delete_contact')): ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#" onclick="submitBatchAction('delete', 'batch-form')">
                                    <i class="ti ti-trash me-2"></i> أرشفة المحدد
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <select class="form-select form-select-sm ms-2" style="width: 80px;" onchange="window.location.href = '?page=contacts&limit=' + this.value;">
                        <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= ($limit == 20) ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= ($limit == 50) ? 'selected' : '' ?>>50</option>
                    </select>
                </div>
                <?php if ($total_pages > 1): ?>
                    <div class="m-auto"><?php render_pagination($current_page, $total_pages, $_GET); ?></div>
                <?php endif; ?>
                <p class="m-0 text-muted">عرض <span><?= count($contacts) ?></span> من <span><?= $total_records ?></span> سجل</p>
            </div>
        </div>
    </div>
</div>
