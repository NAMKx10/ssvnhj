<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl"><h2 class="page-title">إعدادات النظام العامة</h2></div>
</div>

<!-- محتوى الصفحة -->
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="row g-0">
                <!-- العمود الأيسر: قائمة التنقل -->
                <div class="col-12 col-md-3 border-end">
                    <div class="card-body">
                        <div class="list-group list-group-transparent" data-bs-toggle="tabs">
                            <a href="#tab-general" class="list-group-item list-group-item-action d-flex align-items-center active" data-bs-toggle="tab">الإعدادات العامة</a>
                            <a href="#tab-identity" class="list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="tab">التخصيص والهوية</a>
                            <a href="#tab-datetime" class="list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="tab">التاريخ والوقت</a>
                            <a href="#tab-currency" class="list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="tab">العملة والترقيم</a>
                            <a href="#tab-smtp" class="list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="tab">إعدادات البريد (SMTP)</a>
                            <a href="#tab-maintenance" class="list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="tab">صيانة النظام</a>
                        </div>
                    </div>
                </div>

                <!-- العمود الأيمن: محتوى التبويبات -->
                <div class="col-12 col-md-9 d-flex flex-column">
                    <form action="index.php?page=handle_general_settings_update" method="POST" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- تبويب: الإعدادات العامة -->
                                <div class="tab-pane active show" id="tab-general">
                                    <h3 class="card-title mb-4">الإعدادات العامة</h3>
                                    <div class="mb-3"><label class="form-label">اسم النظام</label><input type="text" class="form-control" name="settings[site_name]" value="<?= html($settings_array['site_name']) ?>"></div>
                                    <div class="mb-3"><label class="form-label">وصف النظام</label><textarea class="form-control" name="settings[site_description]" rows="3"><?= html($settings_array['site_description']) ?></textarea></div>
                                    <div class="mb-3"><label class="form-label">البريد الإلكتروني للنظام</label><input type="email" class="form-control" name="settings[site_email]" value="<?= html($settings_array['site_email']) ?>"></div>
                                </div>
                                <!-- تبويب: التخصيص والهوية -->
                                <div class="tab-pane" id="tab-identity">
                                    <h3 class="card-title mb-4">التخصيص والهوية</h3>
                                    <div class="mb-3"><label class="form-label">الشعار</label><input type="file" class="form-control" name="logo_image"><small class="form-hint">ارفع شعارًا جديدًا (الأبعاد المقترحة: 110x32 بكسل). سيتم تجاهل هذا إذا كان النص مفعلاً.</small></div>
                                    <div class="mb-3"><label class="form-label">أيقونة المفضلة (Favicon)</label><input type="file" class="form-control" name="favicon"></div>
                                    <div class="mb-3"><label class="form-label">اللون الأساسي للنظام</label><select class="form-select" name="settings[primary_color]"> ... </select></div>
                                    <div class="mb-3"><label class="form-label">تذييل الموقع - يمين</label><textarea class="form-control" name="settings[footer_right]" rows="2"><?= html($settings_array['footer_right']) ?></textarea></div>
                                    <div class="mb-3"><label class="form-label">تذييل الموقع - وسط</label><textarea class="form-control" name="settings[footer_center]" rows="2"><?= html($settings_array['footer_center']) ?></textarea></div>
                                    <div class="mb-3"><label class="form-label">تذييل الموقع - يسار</label><textarea class="form-control" name="settings[footer_left]" rows="2"><?= html($settings_array['footer_left']) ?></textarea></div>
                                </div>
<!-- تبويب: التاريخ والوقت -->
<div class="tab-pane" id="tab-datetime">
    <h3 class="card-title mb-4">التاريخ والوقت</h3>
    <div class="mb-3">
        <label class="form-label">المنطقة الزمنية</label>
        <select class="form-select" name="settings[timezone]" id="timezone-select">
            <?php foreach($timezones as $tz){ echo "<option value='{$tz}' ".($settings_array['timezone'] == $tz ? 'selected' : '').">{$tz}</option>"; } ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">صيغة عرض التاريخ</label>
        <select class="form-select" name="settings[date_format]" id="date-format-select">
            <option value="Y-m-d" <?= $settings_array['date_format'] == 'Y-m-d' ? 'selected' : '' ?>>2025-09-20 (Y-m-d)</option>
            <option value="d-m-Y" <?= $settings_array['date_format'] == 'd-m-Y' ? 'selected' : '' ?>>20-09-2025 (d-m-Y)</option>
            <option value="m/d/Y" <?= $settings_array['date_format'] == 'm/d/Y' ? 'selected' : '' ?>>09/20/2025 (m/d/Y)</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">صيغة عرض الوقت</label>
        <div class="form-selectgroup" id="time-format-group">
            <label class="form-selectgroup-item"><input type="radio" name="settings[time_format]" value="24" class="form-selectgroup-input" <?= $settings_array['time_format'] == '24' ? 'checked' : '' ?>> <span class="form-selectgroup-label">24 ساعة</span></label>
            <label class="form-selectgroup-item"><input type="radio" name="settings[time_format]" value="12" class="form-selectgroup-input" <?= $settings_array['time_format'] == '12' ? 'checked' : '' ?>> <span class="form-selectgroup-label">12 ساعة (am/pm)</span></label>
        </div>
    </div>
    <div class="mt-4">
        <label class="form-label">معاينة حية</label>
        <div class="alert alert-info">
            الوقت والتاريخ الحالي بناءً على الإعدادات المختارة:
            <strong id="datetime-preview" class="d-block mt-1 fs-4"></strong>
        </div>
    </div>
</div>
                                <!-- تبويب: العملة والترقيم -->
                                <div class="tab-pane" id="tab-currency">
                                    <h3 class="card-title mb-4">العملة وتنسيق الأرقام</h3>
                                    <div id="currency-settings">
                                        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">رمز العملة</label><input type="text" class="form-control" name="settings[currency_symbol]" value="<?= html($settings_array['currency_symbol']) ?>"></div><div class="col-md-6 mb-3"><label class="form-label">مكان الرمز</label><select class="form-select" name="settings[currency_position]"><option value="left" <?= $settings_array['currency_position'] == 'left' ? 'selected' : '' ?>>يسار الرقم</option><option value="right" <?= $settings_array['currency_position'] == 'right' ? 'selected' : '' ?>>يمين الرقم</option></select></div></div>
                                        <div class="row"><div class="col-md-4 mb-3"><label class="form-label">فاصل الآلاف</label><select class="form-select" name="settings[thousands_separator]"><option value="," <?= $settings_array['thousands_separator'] == ',' ? 'selected' : '' ?>>فاصلة (,)</option><option value="." <?= $settings_array['thousands_separator'] == '.' ? 'selected' : '' ?>>نقطة (.)</option></select></div><div class="col-md-4 mb-3"><label class="form-label">الفاصل العشري</label><select class="form-select" name="settings[decimal_separator]"><option value="." <?= $settings_array['decimal_separator'] == '.' ? 'selected' : '' ?>>نقطة (.)</option><option value="," <?= $settings_array['decimal_separator'] == ',' ? 'selected' : '' ?>>فاصلة (,)</option></select></div><div class="col-md-4 mb-3"><label class="form-label">عدد الخانات العشرية</label><input type="number" class="form-control" name="settings[decimal_places]" value="<?= html($settings_array['decimal_places']) ?>"></div></div>
                                        <div class="mt-2"><label class="form-label">معاينة</label><div class="alert alert-info"><strong id="currency-preview" class="fs-4"></strong></div></div>
                                    </div>
                                    <hr>
                                    <h4 class="mb-3">تسلسل الترقيم</h4>
                                    <div class="row mb-2"><div class="col-md-4"><label class="form-label">الفواتير</label></div><div class="col-md-4"><input type="text" class="form-control" name="settings[sequence_invoice_prefix]" value="<?= html($settings_array['sequence_invoice_prefix']) ?>" placeholder="البادئة"></div><div class="col-md-4"><input type="number" class="form-control" name="settings[sequence_invoice_next]" value="<?= html($settings_array['sequence_invoice_next']) ?>" placeholder="الرقم التالي"></div></div>
                                    <div class="row mb-2"><div class="col-md-4"><label class="form-label">سندات القبض</label></div><div class="col-md-4"><input type="text" class="form-control" name="settings[sequence_receipt_prefix]" value="<?= html($settings_array['sequence_receipt_prefix']) ?>" placeholder="البادئة"></div><div class="col-md-4"><input type="number" class="form-control" name="settings[sequence_receipt_next]" value="<?= html($settings_array['sequence_receipt_next']) ?>" placeholder="الرقم التالي"></div></div>
                                    <div class="row mb-2"><div class="col-md-4"><label class="form-label">سندات الصرف</label></div><div class="col-md-4"><input type="text" class="form-control" name="settings[sequence_payment_prefix]" value="<?= html($settings_array['sequence_payment_prefix']) ?>" placeholder="البادئة"></div><div class="col-md-4"><input type="number" class="form-control" name="settings[sequence_payment_next]" value="<?= html($settings_array['sequence_payment_next']) ?>" placeholder="الرقم التالي"></div></div>
                                </div>
                                <!-- تبويب: إعدادات البريد (SMTP) -->
                                <div class="tab-pane" id="tab-smtp">
                                    <div class="alert alert-info"><strong>ميزة مستقبلية:</strong> سيتم هنا وضع حقول إعدادات SMTP (المضيف، المنفذ، اسم المستخدم، كلمة المرور) للسماح للنظام بإرسال رسائل البريد الإلكتروني مباشرة. هذا يضمن وصول الإشعارات والفواتير بشكل موثوق.</div>
                                </div>
                                <!-- تبويب: صيانة النظام -->
                                <div class="tab-pane" id="tab-maintenance">
                                    <h3 class="card-title mb-4">صيانة النظام</h3>
                                    <div class="mb-3"><label class="form-check form-switch form-switch-lg"><input class="form-check-input" type="checkbox" name="settings[maintenance_mode]" value="1" <?= ($settings_array['maintenance_mode'] == '1') ? 'checked' : '' ?>><span class="form-check-label">تفعيل وضع الصيانة</span></label></div>
                                    <div class="mb-3"><label class="form-label">رسالة وضع الصيانة</label><textarea class="form-control" name="settings[maintenance_message]" rows="3"><?= html($settings_array['maintenance_message']) ?></textarea></div>
                                    <hr>
                                    <div class="mb-3"><label class="form-label">أدوات إضافية</label><div class="btn-list"><a href="#" class="btn">مسح الكاش</a><a href="#" class="btn">إنشاء نسخة احتياطية</a></div></div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent mt-auto text-end">
                            <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const timezoneSelect = document.getElementById('timezone-select');
    const dateFormatSelect = document.getElementById('date-format-select');
    const timeFormatGroup = document.getElementById('time-format-group');
    const previewEl = document.getElementById('datetime-preview');

    let serverTime = new Date(); // استخدم وقت المتصفح كنقطة بداية

    function updatePreview() {
        // احصل على القيم الحالية من الحقول
        const timezone = timezoneSelect.value;
        const dateFormat = dateFormatSelect.value;
        const timeFormat = timeFormatGroup.querySelector('input:checked').value;

        // خيارات التنسيق
        const dateOptions = {
            year: 'numeric', month: '2-digit', day: '2-digit',
            timeZone: timezone
        };
        
        const timeOptions = {
            hour: '2-digit', minute: '2-digit', second: '2-digit',
            hour12: (timeFormat === '12'),
            timeZone: timezone
        };

        // قم بإنشاء التاريخ والوقت المنسق
        let formattedDate = new Intl.DateTimeFormat('fr-CA', dateOptions).format(serverTime);
        if (dateFormat === 'd-m-Y') {
            const [y, m, d] = formattedDate.split('-');
            formattedDate = `${d}-${m}-${y}`;
        } else if (dateFormat === 'm/d/Y') {
             const [y, m, d] = formattedDate.split('-');
            formattedDate = `${m}/${d}/${y}`;
        }
        
        const formattedTime = new Intl.DateTimeFormat('en-US', timeOptions).format(serverTime);

        // قم بتحديث عنصر المعاينة
        previewEl.textContent = `${formattedDate} ${formattedTime}`;
    }

    // قم بتشغيل التحديث كل ثانية
    setInterval(() => {
        serverTime = new Date();
        updatePreview();
    }, 1000);

    // أضف مستمعي الأحداث للحقول
    timezoneSelect.addEventListener('change', updatePreview);
    dateFormatSelect.addEventListener('change', updatePreview);
    timeFormatGroup.addEventListener('change', updatePreview);

    // قم بتشغيل المعاينة لأول مرة عند تحميل الصفحة
    updatePreview();

    // --- كود المعاينة الحية للعملة ---

    const currencySettings = document.getElementById('currency-settings');
    const currencyPreview = document.getElementById('currency-preview');
    function updateCurrencyPreview() {
        const symbol = currencySettings.querySelector('[name="settings[currency_symbol]"]').value;
        const position = currencySettings.querySelector('[name="settings[currency_position]"]').value;
        const thousands = currencySettings.querySelector('[name="settings[thousands_separator]"]').value;
        const decimal = currencySettings.querySelector('[name="settings[decimal_separator]"]').value;
        const places = parseInt(currencySettings.querySelector('[name="settings[decimal_places]"]').value) || 0;
        const number = 12345.6789;
        const formattedNumber = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: places,
            maximumFractionDigits: places
        }).format(number).replace(/,/g, thousands).replace(/\./g, decimal);
        currencyPreview.textContent = (position === 'left') ? `${symbol} ${formattedNumber}` : `${formattedNumber} ${symbol}`;
    }
    currencySettings.addEventListener('input', updateCurrencyPreview);
    updateCurrencyPreview();
});

</script>