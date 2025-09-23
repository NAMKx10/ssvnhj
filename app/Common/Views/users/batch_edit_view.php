<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col"><h2 class="page-title">تعديل جماعي للمستخدمين</h2></div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="index.php?page=users" class="btn"><i class="ti ti-arrow-left me-2"></i> العودة</a>
                    <button id="save-data-btn" class="btn btn-primary"><i class="ti ti-device-floppy me-2"></i> حفظ التعديلات</button>
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
                <div id="handsontable-container" style="width: 100%; height: 500px; overflow: hidden;"></div>
            </div>
        </div>
    </div>
</div>

<!-- ▼▼▼ التعديل هنا ▼▼▼ -->
<!-- تمرير الإعدادات الخاصة بجدول تعديل المستخدمين إلى JavaScript -->
<script>
    // تمرير البيانات الأولية التي سيتم عرضها في الجدول
    window.initialData = <?= json_encode($users_data ?? [], JSON_UNESCAPED_UNICODE) ?>;
    
    // تمرير الإعدادات اللازمة للجافاسكريبت العام
    window.saveUrl = 'index.php?page=handle_users_batch_edit';
    window.formAction = 'batch_edit_user';
    
    // تمرير قائمة الأدوار كمصدر للقائمة المنسدلة
    window.rolesSource = <?= json_encode($roles_for_js ?? [], JSON_UNESCAPED_UNICODE) ?>;

    // تعريف عناوين الأعمدة وترتيبها
    window.colHeaders = ['ID', 'الاسم الكامل*', 'اسم المستخدم*', 'الدور', 'الحالة', 'الإيميل', 'الجوال'];
    
    // تعريف نوع كل عمود
    window.columnsConfig = [
        { data: 0, readOnly: true }, // ID
        { data: 1 },                 // Full Name
        { data: 2 },                 // Username
        { data: 3, type: 'dropdown', source: window.rolesSource }, // Role
        { data: 4, type: 'dropdown', source: ['active', 'inactive'] }, // Status
        { data: 5 },                 // Email
        { data: 6 }                  // Mobile
    ];
</script>
<!-- ▲▲▲ نهاية التعديل ▲▲▲ -->