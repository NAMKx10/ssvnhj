/**
 * public/assets/js/helpers.js (النسخة المطورة)
 */

/**
 * تحديد أو إلغاء تحديد كل مربعات الاختيار في الجدول.
 * @param {HTMLInputElement} source 
 */
function toggleAllCheckboxes(source) {
    // ابحث عن أقرب جدول يحتوي على هذا الزر
    const table = source.closest('table');
    if (table) {
        // حدد كل مربعات الاختيار داخل هذا الجدول فقط
        table.querySelectorAll('input[name="ids[]"]').forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }
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

// دالة لتحديث ظهور شريط الإجراءات الجماعية
function updateBatchActionsToolbar() {
    const toolbar = document.getElementById('batch-actions-toolbar');
    const countEl = document.getElementById('selected-count');
    const checked = document.querySelectorAll('#batch-form input[name="ids[]"]:checked');

    if (toolbar && countEl) {
        if (checked.length > 0) {
            toolbar.style.display = 'block';
            countEl.textContent = checked.length;
        } else {
            toolbar.style.display = 'none';
        }
    }
}


