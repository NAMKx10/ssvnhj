<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">تعديل صلاحيات الدور: <span class="text-primary"><?= html($role['role_name']) ?></span></h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="#" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#main-modal" data-bs-url="index.php?page=roles/edit_role_view&id=<?= $role['id'] ?>&view_only=true">
                    تعديل بيانات الدور
                </a>
                <a href="index.php?page=roles" class="btn"><i class="ti ti-arrow-left me-2"></i>العودة لقائمة الأدوار</a>
            </div>
        </div>
    </div>
</div>

<!-- محتوى الصفحة -->
<div class="page-body">
    <div class="container-xl">
        <form method="POST" action="index.php?page=handle_role_edit" id="permissions-form">
            <input type="hidden" name="role_id" value="<?= $role['id'] ?>">
            
            <div class="row row-cards">
                <?php if (empty($all_permissions)): ?>
                    <div class="col-12"><div class="alert alert-warning">لا توجد أي صلاحيات معرفة في النظام. يرجى إضافتها أولاً من صفحة "إدارة الصلاحيات".</div></div>
                <?php else: foreach($all_permissions as $group_name => $permissions): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title"><?= html($group_name) ?></h3></div>
                            <div class="card-body">
                                <?php foreach($permissions as $permission): ?>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $permission['id'] ?>" id="perm-<?= $permission['id'] ?>"
                                            <?= in_array($permission['id'], $current_permissions) ? 'checked' : '' ?>
                                            <?= ($role['id'] == 1) ? 'disabled' : '' ?> >
                                        <label class="form-check-label" for="perm-<?= $permission['id'] ?>"><?= html($permission['description']) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>

            <div class="mt-4">
                <?php if($role['id'] != 1): ?>
                    <button type="submit" class="btn btn-primary">حفظ الصلاحيات</button>
                <?php else: ?>
                    <div class="alert alert-info">لا يمكن تعديل صلاحيات دور "Super Admin". يمتلك هذا الدور كل الصلاحيات بشكل دائم.</div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- (اختياري ولكن موصى به) جافاسكريبت لتحويل النموذج إلى AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('permissions-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonHtml = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري الحفظ...';

            fetch(form.action, {
                method: form.method,
                body: new FormData(form),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ title: 'نجاح!', text: data.message, icon: 'success' })
                        .then(() => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            }
                        });
                } else {
                    Swal.fire('خطأ!', data.message || 'حدث خطأ غير متوقع.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('خطأ!', 'فشل الاتصال بالخادم.', 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonHtml;
            });
        });
    }
});
</script>