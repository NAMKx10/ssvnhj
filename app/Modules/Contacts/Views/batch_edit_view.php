<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col"><h2 class="page-title">تعديل جماعي لجهات الاتصال</h2></div>
            <div class="col-auto ms-auto d-print-none">
                <a href="index.php?page=contacts" class="btn"><i class="ti ti-arrow-left me-2"></i> العودة</a>
                <button id="save-data-btn" class="btn btn-primary"><i class="ti ti-device-floppy me-2"></i> حفظ التعديلات</button>
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
<!-- تمرير الإعدادات الكاملة إلى JavaScript -->
<script>
    // تمرير البيانات الأولية التي سيتم عرضها في الجدول
    window.initialData = <?= json_encode($contacts_for_js ?? [], JSON_UNESCAPED_UNICODE) ?>;
    
    // تمرير الإعدادات اللازمة للجافاسكريبت العام
    window.saveUrl = 'index.php?page=handle_contact_actions';
    window.formAction = 'batch_edit_contact';

    // تعريف عناوين الأعمدة وترتيبها
    window.colHeaders = [
        'ID', 'الاسم*', 'الكود', 'النوع', 'الحالة', 
        'رقم السجل', 'الرقم الضريبي', 'الجوال', 'الإيميل'
    ];
    
    // تعريف نوع كل عمود
    window.columnsConfig = [
        { data: 0, readOnly: true },
        { data: 1 }, // الاسم
        { data: 2 }, // الكود
        { data: 3, type: 'dropdown', source: ['فرد', 'منشأة'] }, // النوع
        { data: 4, type: 'dropdown', source: ['active', 'inactive'] }, // الحالة
        { data: 5 }, // رقم السجل
        { data: 6 }, // الرقم الضريبي
        { data: 7 }, // الجوال
        { data: 8 }  // الإيميل
    ];
</script>
<!-- ▲▲▲ نهاية التعديل ▲▲▲ -->