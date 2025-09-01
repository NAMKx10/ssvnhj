<!-- =============================================== -->
<!-- START: Core Modals & JavaScript Libraries       -->
<!-- =============================================== -->

<!-- 1. الهيكل الأساسي للنافذة المنبثقة -->
<div class="modal modal-blur fade" id="main-modal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content"></div>
  </div>
</div>

<!-- 2. أزرار الصعود والنزول -->
<div class="scroll-buttons">
    <a href="#" id="scroll-to-bottom-btn" class="btn btn-icon btn-primary" title="النزول للأسفل"><i class="ti ti-arrow-down"></i></a>
    <a href="#" id="scroll-to-top-btn" class="btn btn-icon btn-primary" title="الصعود للأعلى" style="display: none;"><i class="ti ti-arrow-up"></i></a>
</div>

<!-- 3. المكتبات الأساسية (آخر إصدارات مستقرة) -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.base.min.js"></script>

<!-- =============================================== -->
<!-- END: Core Modals & JavaScript Libraries         -->
<!-- =============================================== -->


<!-- =============================================== -->
<!-- START: Central Application JavaScript Logic   -->
<!-- =============================================== -->
<script>
    // --- 1. الدوال المساعدة العامة (Helpers) ---

    /**
     * دالة لتحديد وإلغاء تحديد كل مربعات الاختيار في الجدول.
     * @param {HTMLInputElement} source - مربع الاختيار الرئيسي.
     */
    function toggleAllCheckboxes(source) {
        document.querySelectorAll('input[name="row_id[]"]').forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }

    /**
     * دالة لإرسال نماذج الإجراءات الجماعية مع رسالة تأكيد.
     * @param {string} action - اسم الإجراء المراد تنفيذه.
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

    // --- 2. المنطق الرئيسي الذي يعمل بعد تحميل الصفحة ---
    document.addEventListener("DOMContentLoaded", function() {
        
        /**
         * دالة مركزية لتشغيل مكتبة TomSelect على أي عنصر.
         * @param {HTMLElement} context - النطاق الذي سيتم البحث فيه عن عناصر Select.
         */
        function initializeTomSelect(context) {
            const selects = context.querySelectorAll('.select-init');
            selects.forEach(el => {
                if (!el.tomselect) {
                    new TomSelect(el, {
                        copyClassesToDropdown: false,
                        dropdownParent: 'body', // يضمن ظهور القائمة فوق كل شيء
                        controlInput: '<input>',
                    });
                }
            });
        }

        // تشغيل TomSelect على العناصر الموجودة في الصفحة عند التحميل
        initializeTomSelect(document.body);

        // --- معالجات الأحداث (Event Handlers) ---

        // أ. تحميل محتوى النافذة المنبثقة (Modal) عند فتحها
        const mainModal = document.getElementById('main-modal');
        if (mainModal) {
            mainModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const url = button.getAttribute('data-bs-url');
                const modalContent = mainModal.querySelector('.modal-content');

                modalContent.innerHTML = '<div class="modal-body text-center p-5"><div class="spinner-border"></div></div>';

                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        modalContent.innerHTML = html;
                        initializeTomSelect(modalContent); // إعادة تفعيل TomSelect
                    })
                    .catch(error => {
                        modalContent.innerHTML = '<div class="modal-body"><div class="alert alert-danger">فشل تحميل المحتوى.</div></div>';
                    });
            });
        }
        
        // ب. إرسال النماذج (Forms) داخل النافذة المنبثقة عبر AJAX
        document.body.addEventListener('submit', function(e) {
            if (e.target.matches('.ajax-form')) {
                e.preventDefault();
                const form = e.target;
                const submitButton = form.querySelector('button[type="submit"]');
                const originalButtonHtml = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري الحفظ...';

                fetch(form.action, {
                    method: form.method,
                    body: new FormData(form),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modalInstance = bootstrap.Modal.getInstance(mainModal);
                        if (modalInstance) modalInstance.hide();
                        
                        Swal.fire({ title: 'نجاح!', text: data.message, icon: 'success', timer: 1500, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire('خطأ!', data.message || 'حدث خطأ غير متوقع.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('خطأ!', 'فشل الاتصال بالخادم.', 'error');
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonHtml;
                });
            }
        });

        // ج. رسالة تأكيد الحذف (Soft Delete)
        document.body.addEventListener('click', function(e) {
            if (e.target.closest('.confirm-delete')) {
                e.preventDefault();
                const url = e.target.closest('.confirm-delete').href;
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "سيتم نقل هذا السجل إلى الأرشيف!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، قم بالأرشفة!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            }
        });

        // د. حل مشكلة القائمة المنسدلة داخل الجداول
        const tables = document.querySelectorAll('.table-responsive');
        tables.forEach(table => {
            table.addEventListener('show.bs.dropdown', function() {
                table.style.overflow = 'inherit';
            });
            table.addEventListener('hide.bs.dropdown', function() {
                table.style.overflow = 'auto';
            });
        });

        // هـ. منطق أزرار الصعود والنزول
        const scrollTopBtn = document.getElementById('scroll-to-top-btn');
        const scrollBottomBtn = document.getElementById('scroll-to-bottom-btn');
        if(scrollTopBtn && scrollBottomBtn) {
            window.addEventListener('scroll', function() {
                window.scrollY > 300 ? scrollTopBtn.style.display = 'block' : scrollTopBtn.style.display = 'none';
            });
            scrollTopBtn.addEventListener('click', (e) => { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
            scrollBottomBtn.addEventListener('click', (e) => { e.preventDefault(); window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); });
        }

        // --- و. تشغيل جدول الإضافة الجماعية (Handsontable) ---
        const container = document.getElementById('handsontable-container');
        if (container)
        {
            // --- و. تشغيل جداول Handsontable ---
const addContainer = document.getElementById('handsontable-container');
if (addContainer) { // هذا الكود سيعمل في صفحتي الإضافة والتعديل
    
    const isBatchEdit = typeof initialData !== 'undefined';
    
    const hot = new Handsontable(addContainer, {
        data: isBatchEdit ? initialData : Array.from({ length: 20 }, () => Array(7).fill(null)),
        rowHeaders: true,
        colHeaders: ['ID', 'الاسم الكامل*', 'اسم المستخدم*', 'الدور', 'الحالة', 'البريد الإلكتروني', 'الجوال'],
        columns: [
            { data: 0, readOnly: true }, // ID
            { data: 1, type: 'text' },
            { data: 2, type: 'text' },
            { data: 3, type: 'dropdown', source: isBatchEdit ? rolesSource : (<?php echo isset($roles_for_js) ? json_encode($roles_for_js) : '[]'; ?>) },
            { data: 4, type: 'dropdown', source: ['active', 'inactive'] },
            { data: 5, type: 'text' },
            { data: 6, type: 'text' }
        ],
        hiddenColumns: { columns: [0] }, // إخفاء عمود ID
        height: 'auto',
        width: '100%',
        stretchH: 'all',
        licenseKey: 'non-commercial-and-evaluation'
    });

    const saveDataBtn = document.getElementById('save-data-btn');
    if (saveDataBtn) {
        saveDataBtn.addEventListener('click', function() {
            const url = isBatchEdit ? 'index.php?page=handle_users_batch_edit' : 'index.php?page=handle_users_batch_add';
            const body = isBatchEdit ? { users_data: hot.getSourceDataArray() } : { users_data: hot.getData().filter(row => row.some(cell => cell !== null && cell !== '')) };

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
                } else { Swal.fire('خطأ!', response.message, 'error'); }
            });
        });
    }
}
        }
    });

    /**
 * دالة لجمع IDs المحددة وتوجيه المستخدم لصفحة التعديل الجماعي.
 */
function redirectToBatchEdit() {
    const checkedIds = Array.from(document.querySelectorAll('input[name="row_id[]"]:checked'))
                            .map(cb => cb.value);

    if (checkedIds.length === 0) {
        Swal.fire('خطأ!', 'يرجى تحديد سجل واحد على الأقل للتعديل.', 'error');
        return;
    }

    // بناء الرابط وتوجيه المستخدم
    window.location.href = `index.php?page=users/batch_edit&ids=${checkedIds.join(',')}`;
}

</script>
<!-- =============================================== -->
<!-- END: Central Application JavaScript Logic     -->
<!-- =============================================== -->