<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">إدارة الأدوار والصلاحيات (<?= html($total_records) ?>)</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=roles/add&view_only=true">
                    <i class="ti ti-plus"></i> إضافة دور جديد
                </a>
            </div>
        </div>
    </div>
</div>

<!-- محتوى الصفحة -->
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الدور</th>
                            <th>الوصف</th>
                            <th>عدد المستخدمين</th>
                            <th>عدد الصلاحيات</th> <!-- العمود الجديد -->
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($roles)): ?>
                            <tr><td colspan="6" class="text-center p-4">لا توجد أدوار معرفة.</td></tr>
                        <?php else: foreach($roles as $role): ?>
                        <tr>
                            <td><span class="text-muted"><?= $role['id'] ?></span></td>
                            <td><?= html($role['role_name']) ?></td>
                            <td><?= html($role['description']) ?></td>
                            <td><span class="badge bg-blue-lt"><?= $role['users_count'] ?></span></td>
                            <td><span class="badge bg-green-lt"><?= $role['permissions_count'] ?></span></td> <!-- بيانات العمود الجديد -->
                            <td class="text-end">
                                <div class="btn-list flex-nowrap">
                                    <a href="index.php?page=roles/edit&id=<?= $role['id'] ?>" class="btn">
                                        تعديل الصلاحيات
                                    </a>
                                    <?php if ($role['id'] > 3): ?>
                                        <a href="index.php?page=handle_role_delete&id=<?= $role['id'] ?>" class="btn btn-outline-danger btn-icon confirm-delete" title="حذف الدور">
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
            
            <?php if ($total_pages > 1): ?>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <div class="m-auto"><?php render_pagination($current_page, $total_pages, $_GET); ?></div>
                <p class="m-0 text-muted">عرض <span><?= count($roles) ?></span> من <span><?= $total_records ?></span> سجل</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>