<div id="form-error-message" class="alert alert-danger" style="display:none;"></div>

<form method="POST" action="index.php?page=branches/handle_add_ajax" class="ajax-form">
    <div class="row g-3">
        <div class="col-sm-6"><label for="branch_name" class="form-label">اسم الفرع/الشركة</label><input type="text" class="form-control" id="branch_name" name="branch_name" required></div>
        <div class="col-sm-6"><label for="branch_code" class="form-label">كود الفرع (فريد)</label><input type="text" class="form-control" id="branch_code" name="branch_code"></div>
        <div class="col-sm-6"><label for="branch_type" class="form-label">نوع الكيان</label><select class="form-select" id="branch_type" name="branch_type"><option value="منشأة" selected>منشأة</option><option value="فرد">فرد</option></select></div>
        <div class="col-sm-6"><label for="registration_number" class="form-label">رقم السجل</label><input type="text" class="form-control" id="registration_number" name="registration_number"></div>
        <div class="col-sm-6"><label for="tax_number" class="form-label">الرقم الضريبي</label><input type="text" class="form-control" id="tax_number" name="tax_number"></div>
        <div class="col-sm-6"><label for="phone" class="form-label">الجوال/الهاتف</label><input type="text" class="form-control" id="phone" name="phone"></div>
        <div class="col-sm-6"><label for="email" class="form-label">البريد الإلكتروني</label><input type="email" class="form-control" id="email" name="email"></div>
        <div class="col-12"><label for="address" class="form-label">العنوان</label><textarea class="form-control" id="address" name="address" rows="2"></textarea></div>
        <div class="col-12"><label for="notes" class="form-label">ملاحظات</label><textarea class="form-control" id="notes" name="notes" rows="2"></textarea></div>
    </div>
    <hr class="my-4">
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-primary">حفظ الفرع</button>
    </div>
</form>