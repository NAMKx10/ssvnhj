/**
 * منطق جداول Handsontable (الإضافة والتعديل الجماعي)
 */

document.addEventListener("DOMContentLoaded", function() {
    const addContainer = document.getElementById('handsontable-container');
    if (addContainer) {
        // جلب بيانات أولية من window إذا كانت موجودة (للتعديل الجماعي)
        const isBatchEdit = typeof window.initialData !== 'undefined';
        const rolesSource = typeof window.rolesSource !== 'undefined' ? window.rolesSource : [];
        const hot = new Handsontable(addContainer, {
            data: isBatchEdit ? window.initialData : Array.from({ length: 20 }, () => Array(7).fill(null)),
            rowHeaders: true,
            colHeaders: ['ID', 'الاسم الكامل*', 'اسم المستخدم*', 'الدور', 'الحالة', 'البريد الإلكتروني', 'الجوال'],
            columns: [
                { data: 0, readOnly: true }, // ID
                { data: 1, type: 'text' },
                { data: 2, type: 'text' },
                { data: 3, type: 'dropdown', source: rolesSource },
                { data: 4, type: 'dropdown', source: ['active', 'inactive'] },
                { data: 5, type: 'text' },
                { data: 6, type: 'text' }
            ],
            hiddenColumns: { columns: [0] },
            height: 'auto',
            width: '100%',
            stretchH: 'all',
            licenseKey: 'non-commercial-and-evaluation'
        });

        const saveDataBtn = document.getElementById('save-data-btn');
        if (saveDataBtn) {
            saveDataBtn.addEventListener('click', function() {
                const url = isBatchEdit ? 'index.php?page=handle_users_batch_edit' : 'index.php?page=handle_users_batch_add';
                const body = isBatchEdit
                    ? { users_data: hot.getSourceDataArray() }
                    : { users_data: hot.getData().filter(row => row.some(cell => cell !== null && cell !== '')) };

                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        Swal.fire('نجاح!', response.message, 'success').then(() => {
                            window.location.href = 'index.php?page=users';
                        });
                    } else {
                        Swal.fire('خطأ!', response.message, 'error');
                    }
                });
            });
        }
    }
});