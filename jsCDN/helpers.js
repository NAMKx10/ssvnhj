/**
 * دوال مساعدة عامة لكل صفحات النظام.
 */

/**
 * تحديد أو إلغاء تحديد كل مربعات الاختيار في الجدول.
 * @param {HTMLInputElement} source 
 */
function toggleAllCheckboxes(source) {
    document.querySelectorAll('input[name="row_id[]"]').forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}

/**
 * تشغيل Tom Select على العناصر المطلوبة داخل سياق معين.
 * @param {HTMLElement} context 
 */
function initializeTomSelect(context) {
    const selects = context.querySelectorAll('.select-init');
    selects.forEach(el => {
        if (!el.tomselect) {
            new TomSelect(el, {
                copyClassesToDropdown: false,
                dropdownParent: 'body',
                controlInput: '<input>',
            });
        }
    });
}