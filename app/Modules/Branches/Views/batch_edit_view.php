<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col"><h2 class="page-title">تعديل جماعي للفروع</h2></div>
            <div class="col-auto ms-auto d-print-none">
                <a href="index.php?page=branches" class="btn"><i class="ti ti-arrow-left me-2"></i> العودة</a>
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
                <div id="handsontable-container" style="height: 500px; overflow: hidden;"></div>
            </div>
        </div>
    </div>
</div>
<!-- تمرير الإعدادات إلى JavaScript -->
<script>
    window.branchTypesSource = <?= json_encode($branch_types_for_js ?? [], JSON_UNESCAPED_UNICODE) ?>;
    window.initialData = <?= json_encode($branches_data ?? [], JSON_UNESCAPED_UNICODE) ?>;
    window.saveUrl = 'index.php?page=handle_branch_actions';
    window.formAction = 'batch_edit_branch';
    window.colHeaders = ['ID', 'اسم الفرع*', 'الكود*', 'النوع', 'الحالة', 'رقم السجل', 'الرقم الضريبي', 'الجوال', 'الإيميل'];
    window.columnsConfig = [
        { data: 0, readOnly: true }, { data: 1 }, { data: 2 },
        { data: 3, type: 'dropdown', source: window.branchTypesSource },

        { data: 4, type: 'dropdown', source: ['active', 'inactive'] },
        { data: 5 }, { data: 6 }, { data: 7 }, { data: 8 }
    ];
</script>