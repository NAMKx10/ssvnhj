/**
 * public/assets/js/batch_actions.js (النسخة النهائية - JavaScript نقي)
 */

// --- الإجراءات الجماعية ---
function submitBatchAction(action, formId) {
    const form = document.getElementById(formId);
    if (!form) { console.error(`Form with ID "${formId}" not found.`); return; }
    if (form.querySelectorAll('input[name="ids[]"]:checked').length === 0) {
        Swal.fire('خطأ!', 'يرجى تحديد سجل واحد على الأقل.', 'error');
        return;
    }
    const actionInput = form.querySelector('#batch-action-input');
    if (actionInput) {
        actionInput.value = action;
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم تنفيذ هذا الإجراء على كل العناصر المحددة.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، قم بالتنفيذ!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) { form.submit(); }
        });
    }
}

// --- التعديل الجماعي ---
function redirectToBatchEdit(module, page) {
    const checkedIds = Array.from(document.querySelectorAll('#batch-form input[name="ids[]"]:checked')).map(cb => cb.value);
    if (checkedIds.length === 0) {
        Swal.fire('خطأ!', 'يرجى تحديد سجل واحد على الأقل للتعديل.', 'error');
        return;
    }
    window.location.href = `index.php?page=${module}/${page}&ids=${checkedIds.join(',')}`;
}


// --- مستمع الأحداث العام (Event Listener) ---
document.addEventListener('click', function(e) {
    const target = e.target;

    // --- الحذف العادي (الأرشفة) ---
    if (target.matches('.confirm-delete') || target.closest('.confirm-delete')) {
        e.preventDefault();
        const link = target.closest('.confirm-delete');
        const url = link.href;
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم نقل هذا السجل إلى الأرشيف.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، قم بالحذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) { window.location.href = url; }
        });
    }

    // --- الحذف النهائي ---
    if (target.matches('.confirm-delete-permanent') || target.closest('.confirm-delete-permanent')) {
        e.preventDefault();
        const link = target.closest('.confirm-delete-permanent');
        const url = link.href;
        Swal.fire({
            title: 'هل أنت متأكد تمامًا؟',
            text: "سيتم حذف هذا السجل بشكل نهائي. هذا الإجراء لا يمكن التراجع عنه!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، قم بالحذف النهائي!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) { window.location.href = url; }
        });
    }
});