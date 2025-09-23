<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl"><h2 class="page-title">تهيئة مدخلات النظام</h2></div>
    <div class="col-auto ms-auto d-print-none"><a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=settings/add_group&view_only=true"><i class="ti ti-plus me-2"></i>إضافة مجموعة</a></div>
</div>
<!-- محتوى الصفحة -->
<div class="page-body">
    <div class="container-xl">
        <div class="row gx-lg-4">
            <div class="col-lg-4">
                <div class="list-group mb-3">
                    <?php foreach ($groups as $group): ?>
                        <a href="index.php?page=settings&group_id=<?= $group['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= ($group['id'] == $active_group_id) ? 'active' : '' ?>">
                            <?= html($group['group_name']) ?>
                            <span class="badge bg-primary-lt"><?= $group['options_count'] ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-8">
                <?php if ($active_group): ?>
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title mb-0"><?= html($active_group['group_name']) ?></h3>
                                <code class="text-muted d-block mt-1"><?= html($active_group['group_key']) ?></code>
                            </div>
                            <div class="card-actions">
                                <a href="#" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=settings/edit_group&id=<?= $active_group['id'] ?>&view_only=true">تعديل</a>
                                <?php if (!$active_group['is_core']): ?>
                                    <a href="index.php?page=handle_settings_actions&action=delete_group&id=<?= $active_group['id'] ?>" class="btn btn-outline-danger confirm-delete">حذف</a>
                                <?php endif; ?>
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=settings/add_option&group_id=<?= $active_group['id'] ?>&view_only=true">
    <i class="ti ti-plus me-1"></i> إضافة خيار جديد
</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter">
                                <thead><tr><th>القيمة المعروضة</th><th>المفتاح البرمجي</th><th class="w-1"></th></tr></thead>
                                <tbody>
                                    <?php foreach ($options as $option): ?>
                                        <tr>
                                            <td><?= html($option['option_value']) ?></td>
                                            <td><code><?= html($option['option_key']) ?></code></td>
                                            <td class="text-end">
                                                <div class="btn-list flex-nowrap">
                                                    <a href="#" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=settings/edit_option&id=<?= $option['id'] ?>&view_only=true">تعديل</a>
                                                    <a href="index.php?page=handle_settings_actions&action=delete_option&id=<?= $option['id'] ?>" class="btn btn-sm btn-outline-danger confirm-delete">حذف</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>