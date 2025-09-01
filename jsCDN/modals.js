/**
 * منطق النوافذ المنبثقة (Modal logic)
 */

document.addEventListener("DOMContentLoaded", function() {
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
                    if (typeof initializeTomSelect === 'function') {
                        initializeTomSelect(modalContent);
                    }
                })
                .catch(() => {
                    modalContent.innerHTML = '<div class="modal-body"><div class="alert alert-danger">فشل تحميل المحتوى.</div></div>';
                });
        });
    }

    // إرسال النماذج داخل المودال عبر AJAX
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
            .catch(() => {
                Swal.fire('خطأ!', 'فشل الاتصال بالخادم.', 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonHtml;
            });
        }
    });
});