document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById('handsontable-container');
    if (!container) return;

    // اقرأ البيانات الأولية من النافذة، وإذا لم تكن موجودة (صفحة إضافة)، أنشئ صفوفًا فارغة
    // استخدم عدد الأعمدة من الإعدادات لتحديد عرض المصفوفة الفارغة
    const colCount = (window.columnsConfig || []).length;
    const initialGridData = window.initialData || Array.from({ length: 20 }, () => Array(colCount).fill(null));

    // استخدم الإعدادات التي تم تمريرها من الواجهة
    const hot = new Handsontable(container, {
        data: initialGridData,
        rowHeaders: true,
        colHeaders: window.colHeaders || [], // اقرأ عناوين الأعمدة
        columns: window.columnsConfig || [], // اقرأ إعدادات الأعمدة
        hiddenColumns: { columns: [0], indicators: false },
        height: 'auto',
        stretchH: 'all',
        licenseKey: 'non-commercial-and-evaluation'
    });

    const saveDataBtn = document.getElementById('save-data-btn');
    if (saveDataBtn) {
        saveDataBtn.addEventListener('click', function() {
            const url = window.saveUrl || '';
            const action = window.formAction || '';
            if (!url || !action) {
                console.error("Save URL or Form Action is not defined.");
                return;
            }

            // تحديد البيانات التي سيتم إرسالها
            const data_to_send = (action.includes('add'))
                ? hot.getSourceDataArray().filter(row => row.some(cell => cell !== null && cell !== ''))
                : hot.getSourceDataArray();
            
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                // استخدم اسمًا عامًا للبيانات: `data`
                body: JSON.stringify({ form_action: action, data: data_to_send })
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    Swal.fire('نجاح!', response.message, 'success').then(() => {
                        // أعد التوجيه إلى الصفحة السابقة (التي جئنا منها)
                        window.location.href = document.referrer || 'index.php';
                    });
                } else {
                    Swal.fire('خطأ!', response.message, 'error');
                }
            });
        });
    }
});