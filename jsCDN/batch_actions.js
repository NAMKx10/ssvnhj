/**
 * منطق الإجراءات الجماعية (Batch Actions)
 */

function submitBatchAction(action) {
    if (document.querySelectorAll('input[name="row_id[]"]:checked').length === 0) {
        Swal.fire('خطأ!', 'يرجى تحديد سجل واحد على الأقل.', 'error');
        return;
    }

    const actionInput = document.getElementById('batch-action-input');
    const form = document.getElementById('batch-form');
    if (actionInput && form) {
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
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
}

/**
 * توجيه المستخدم لصفحة التعديل الجماعي بعد جمع الـ IDs المحددة.
 */
function redirectToBatchEdit() {
    const checkedIds = Array.from(document.querySelectorAll('input[name="row_id[]"]:checked')).map(cb => cb.value);
    if (checkedIds.length === 0) {
        Swal.fire('خطأ!', 'يرجى تحديد سجل واحد على الأقل للتعديل.', 'error');
        return;
    }
    window.location.href = `index.php?page=users/batch_edit&ids=${checkedIds.join(',')}`;
}

// public/assets/js/batch_actions.js (نهاية الملف)

document.body.addEventListener('click', function(e) {
    // ابحث عن الرابط الذي تم النقر عليه أو أحد آبائه الذي يطابق الشرط
    const link = e.target.closest('.confirm-delete-permanent');

    if (link) {
        e.preventDefault(); // منع الرابط من العمل فورًا
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
            if (result.isConfirmed) {
                window.location.href = url; // اذهب إلى الرابط إذا أكد المستخدم
            }
        });
    }
});