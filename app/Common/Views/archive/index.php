<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl"><h2 class="page-title">الأرشيف (سلة المحذوفات)</h2></div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="alert alert-warning" role="alert">
            <div class="d-flex">
                <div><i class="icon ti ti-alert-triangle me-2"></i></div>
                <div><h4 class="alert-title">تحذير!</h4><div class="text-muted">الحذف النهائي سيزيل العنصر من قاعدة البيانات بشكل دائم ولا يمكن التراجع عنه.</div></div>
            </div>
        </div>

        <?php if (empty($archived_items)): ?>
            <div class="card"><div class="card-body text-center text-muted">الأرشيف فارغ حالياً.</div></div>
        <?php else: ?>
            <div class="accordion" id="archiveAccordion">
                <?php foreach ($archived_items as $table => $items): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?= $table ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $table ?>">
                                <span class="me-auto">
                                    <?= html($tables_map[$table]['display']) ?>
                                    <span class="badge bg-secondary-lt ms-2"><?= count($items) ?></span>
                                </span>
                            </button>
                        </h2>
                        <div id="collapse-<?= $table ?>" class="accordion-collapse collapse" data-bs-parent="#archiveAccordion">
                            <div class="accordion-body">
                                <form method="POST" action="index.php?page=archive">
                                    <input type="hidden" name="table" value="<?= $table ?>">
                                    <div class="d-flex gap-2 mb-3">
                                        <select name="action" class="form-select" style="width: auto;" required>
                                            <option value="">-- إجراء جماعي --</option>
                                            <option value="restore">استعادة المحدد</option>
                                            <option value="force_delete">حذف نهائي للمحدد</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary">تنفيذ</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table card-table table-vcenter text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox" onchange="this.closest('table').querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked)"></th>
                                                    <th>الاسم/المعرف</th>
                                                    <th>تاريخ الحذف</th>
                                                    <th class="w-1"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($items as $item): ?>
                                                <tr>
                                                    <td><input class="form-check-input m-0 align-middle item-checkbox" type="checkbox" name="ids[]" value="<?= $item['id'] ?>"></td>
                                                    <td><?= html($item['name'] ?: "ID: " . $item['id']) ?></td>
                                                    <td><span class="text-muted"><?= format_date($item['deleted_at'], 'Y-m-d H:i') ?></span></td>
                                                    <td class="text-end">
                                                        <a href="index.php?page=archive&action=restore&table=<?= $table ?>&id=<?= $item['id'] ?>" class="btn btn-sm btn-ghost-success">استعادة</a>
                                                        <a href="index.php?page=archive&action=force_delete&table=<?= $table ?>&id=<?= $item['id'] ?>" class="btn btn-sm btn-ghost-danger confirm-delete-permanent">حذف</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>