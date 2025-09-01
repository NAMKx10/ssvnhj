<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">إدارة المستخدمين (<?= $total_records ?>)</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn"><i class="ti ti-upload me-2"></i> استيراد</a>
                    <a href="#" class="btn"><i class="ti ti-download me-2"></i> تصدير</a>
                    <a href="#" class="btn"><i class="ti ti-printer me-2"></i> طباعة</a>
                    <a href="index.php?page=users/batch_add" class="btn btn-primary">
    <i class="ti ti-table-plus me-2"></i> إضافة جماعية
</a>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=users/add&view_only=true">
                        <i class="ti ti-plus"></i> إضافة مستخدم
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- محتوى الصفحة -->
<div class="page-body">
    <div class="container-xl">
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-primary text-white avatar"><i class="ti ti-users"></i></span></div><div class="col"><div class="font-weight-medium">إجمالي المستخدمين</div><div class="text-muted"><?= $stats['total'] ?? 0 ?></div></div></div></div></div></div>
            <div class="col-md-4"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-success text-white avatar"><i class="ti ti-user-check"></i></span></div><div class="col"><div class="font-weight-medium">النشطون</div><div class="text-muted"><?= $stats['active'] ?? 0 ?></div></div></div></div></div></div>
            <div class="col-md-4"><div class="card card-sm"><div class="card-body"><div class="row align-items-center"><div class="col-auto"><span class="bg-danger text-white avatar"><i class="ti ti-user-x"></i></span></div><div class="col"><div class="font-weight-medium">غير النشطين</div><div class="text-muted"><?= $stats['inactive'] ?? 0 ?></div></div></div></div></div></div>
        </div>
        

        <!-- بطاقة الفلترة والجدول -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">قائمة المستخدمين</h3>
            </div>
            
            <!-- قسم الفلاتر -->
            <div class="card-body border-bottom py-3">
                <form action="index.php" method="GET">
                    <input type="hidden" name="page" value="users">
                    <div class="row g-3">
                        <div class="col-md-4"><input type="search" name="q" class="form-control" placeholder="ابحث بالاسم، المستخدم، الإيميل..." value="<?= htmlspecialchars($filter_q ?? '') ?>"></div>
                        <div class="col-md-3">
                            <select name="role_id" class="form-select">
                                <option value="">كل الأدوار</option>
                                <?php foreach($roles_list as $role):?>
                                    <option value="<?=$role['id']?>" <?= (($filter_role_id ?? '') == $role['id']) ? 'selected' : '' ?>><?=htmlspecialchars($role['role_name'])?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select ">
                                <option value="">كل الحالات</option>
                                <?php foreach($statuses_list as $key => $value):?>
                                    <option value="<?=$key?>" <?= (($filter_status ?? '') == $key) ? 'selected' : '' ?>><?=htmlspecialchars($value)?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex">
                            <button type="submit" class="btn btn-primary w-100 me-2">فلترة</button>
                            <a href="index.php?page=users" class="btn btn-icon" title="إعادة تعيين"><i class="ti ti-refresh"></i></a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- الجدول -->
             <form action="index.php?page=handle_users_batch_action" method="post" id="batch-form">
                <input type="hidden" name="action" id="batch-action-input">
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap table-hover">
                    <thead>
                        <tr>
                            <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" onchange="toggleAllCheckboxes(this)"></th>
                            <th>#</th>
                            <th>الاسم الكامل</th>
                            <th>معلومات التواصل</th>
                            <th>الدور</th>
                            <th>الحالة</th>
                            <th class="w-1">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr><td colspan="7" class="text-center p-4">لا توجد نتائج.</td></tr>
                        <?php else: $i = $offset + 1; foreach($users as $user): ?>
                        <tr>
                            <td><input class="form-check-input m-0 align-middle" type="checkbox" name="row_id[]" value="<?= $user['id'] ?>"></td>
                            <td><span class="text-muted"><?= $i++ ?></span></td>
                            <td>
                                <div><?= htmlspecialchars($user['full_name']) ?></div>
                                <div class="text-muted"><?= htmlspecialchars($user['username']) ?></div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($user['email']) ?></div>
                                <div class="text-muted"><?= htmlspecialchars($user['mobile']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($user['role_name']) ?></td>
                            <td>
                                <span class="badge bg-<?= ($user['status'] === 'active') ? 'success' : 'danger' ?> me-1"></span>
                                <?= ($user['status'] === 'active') ? 'نشط' : 'غير نشط' ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-list flex-nowrap">
                                    <a href="#" class="btn btn-ghost-primary btn-icon" title="تعديل" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=users/edit&id=<?= $user['id'] ?>&view_only=true">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <div class="dropdown">
    <button class="btn dropdown-toggle align-text-top" data-bs-toggle="dropdown" data-bs-placement="bottom-end">
        الإجراءات
    </button>
    <div class="dropdown-menu dropdown-menu-end">
        <a class="dropdown-item" href="#">
            <i class="ti ti-history me-2"></i> عرض حركات المستخدم
        </a>
        <a class="dropdown-item text-danger confirm-delete" href="index.php?page=handle_user_delete&id=<?= $user['id'] ?>">
            <i class="ti ti-trash me-2"></i> حذف (أرشفة)
        </a>
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
        <div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        إجراءات جماعية
    </button>
    <div class="dropdown-menu">
            <a class="dropdown-item" href="#" onclick="redirectToBatchEdit()">
        <i class="ti ti-pencil me-2"></i> تعديل جماعي
    </a>

        <a class="dropdown-item" href="#" onclick="submitBatchAction('activate')">
            <i class="ti ti-user-check me-2"></i> تفعيل المحدد
        </a>
        <a class="dropdown-item" href="#" onclick="submitBatchAction('deactivate')">
            <i class="ti ti-user-x me-2"></i> تعطيل المحدد
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="#" onclick="submitBatchAction('soft_delete')">
            <i class="ti ti-trash me-2"></i> أرشفة المحدد
        </a>
    </div>
</div>
        <select class="form-select form-select-sm ms-2" style="width: 80px;">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div>
    <div class="m-auto"><?php render_pagination($current_page, $total_pages, $_GET); ?></div>
    <p class="m-0 text-muted">عرض <span><?= count($users) ?></span> من <span><?= $total_records ?></span> سجل</p>
</div>
        </div>
    </div>
</div>