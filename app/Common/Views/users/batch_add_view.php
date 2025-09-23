<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">إضافة جماعية للمستخدمين</h2>
                <div class="text-muted mt-1">يمكنك إدخال البيانات مباشرة أو نسخها ولصقها من ملف Excel.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="index.php?page=users" class="btn"><i class="ti ti-arrow-left me-2"></i> العودة</a>
                    <button id="save-data-btn" class="btn btn-primary"><i class="ti ti-device-floppy me-2"></i> حفظ السجلات</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- محتوى الصفحة -->
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <div id="handsontable-container" style="height: 500px; overflow: hidden;"></div>
            </div>
        </div>
    </div>
</div>

<!-- ▼▼▼ التعديل هنا ▼▼▼ -->
<!-- تمرير الإعدادات الخاصة بجدول إضافة المستخدمين إلى JavaScript -->
<script>
    window.saveUrl = 'index.php?page=handle_users_batch_add';
    window.formAction = 'batch_add_user';
    
    // تمرير قائمة الأدوار كمصدر للقائمة المنسدلة
    window.rolesSource = <?= json_encode($roles_for_js ?? [], JSON_UNESCAPED_UNICODE) ?>;
    
    // تعريف عناوين الأعمدة وترتيبها
    window.colHeaders = ['ID', 'الاسم الكامل*', 'اسم المستخدم*', 'كلمة المرور*', 'الدور', 'الحالة', 'الإيميل', 'الجوال'];
    
    // تعريف نوع كل عمود
    window.columnsConfig = [
        { data: 0, readOnly: true }, // ID
        { data: 1 },                 // Full Name
        { data: 2 },                 // Username
        { data: 3, type: 'password' }, // Password
        { data: 4, type: 'dropdown', source: window.rolesSource }, // Role
        { data: 5, type: 'dropdown', source: ['active', 'inactive'] }, // Status
        { data: 6 },                 // Email
        { data: 7 }                  // Mobile
    ];
</script>
<!-- ▲▲▲ نهاية التعديل ▲▲▲ -->