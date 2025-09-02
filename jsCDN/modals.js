/**
 * منطق النوافذ المنبثقة (Modal logic) - النسخة المحصّنة
 */

document.addEventListener("DOMContentLoaded", function() {
    const mainModal = document.getElementById('main-modal');
    if (mainModal) {
        mainModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const url = button.getAttribute('data-bs-url');
            if (!url) return;
            
            const modalContent = mainModal.querySelector('.modal-content');
            modalContent.innerHTML = '<div class="modal-body text-center p-5"><div class="spinner-border"></div></div>';

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.text();
                })
                .then(html => {
                    modalContent.innerHTML = html;
                    if (typeof initializeTomSelect === 'function') {
                        initializeTomSelect(modalContent);
                    }
                })
                .catch(error => {
                    console.error('Modal content fetch error:', error);
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

            let responseClone; // لطباعة النص الخام عند حدوث خطأ
            fetch(form.action, {
                method: form.method,
                body: new FormData(form),
            })
            .then(response => {
                responseClone = response.clone(); // استنساخ الاستجابة
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const modalInstance = tabler.bootstrap.Modal.getInstance(mainModal);
                    if (modalInstance) modalInstance.hide();
                    Swal.fire({ title: 'نجاح!', text: data.message, icon: 'success', timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire('خطأ!', data.message || 'حدث خطأ غير متوقع.', 'error');
                }
            })
            .catch(error => {
                // ▼▼▼ هذا هو الجزء الجديد والمهم ▼▼▼
                console.error('Fetch Error:', error);
                responseClone.text().then(text => {
                    console.error('Raw Response Text:', text); // اطبع النص الخام المسبب للمشكلة
                    Swal.fire('خطأ في الاستجابة!', 'تم استلام استجابة غير صالحة من الخادم. انظر إلى الكونسول لمزيد من التفاصيل.', 'error');
                });
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonHtml;
            });
        }
    });
});