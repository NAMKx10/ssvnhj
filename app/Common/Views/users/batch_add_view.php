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
                    <a href="index.php?page=users" class="btn">
                        <i class="ti ti-arrow-left me-2"></i> العودة لقائمة المستخدمين
                    </a>
                    <button id="save-data-btn" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-2"></i> حفظ كل السجلات
                    </button>
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