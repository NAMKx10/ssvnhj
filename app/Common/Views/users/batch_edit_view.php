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
<!-- تمرير البيانات من PHP إلى JavaScript -->
<script>
    window.initialData = <?= json_encode($users_for_js ?? [], JSON_UNESCAPED_UNICODE) ?>;
    window.rolesSource = <?= json_encode($roles_for_js ?? [], JSON_UNESCAPED_UNICODE) ?>;
</script>