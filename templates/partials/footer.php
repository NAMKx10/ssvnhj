<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="modal fade" id="mainModal" tabindex="-1" aria-labelledby="mainModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="mainModalLabel"></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <div class="modal-body" id="mainModalBody"></div>
    </div>
  </div>
</div>

<script>
// =================================================================
//  GLOBAL SCRIPTING & EVENT HANDLING
// =================================================================
$(document).ready(function() {
    
    // --- 1. تعريف دالة مركزية لتفعيل Select2 ---
    function initializeSelect2(context) {
        $(context).find('.select2-init').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({
                    theme: "bootstrap-5",
                    dir: "rtl",
                    placeholder: $(this).data('placeholder') || "اختر...",
                    dropdownParent: $('#mainModal')
                });
            }
        });
    }

    // === بداية الإضافة: دالة مركزية لتفعيل Popovers ===
function activatePopovers(context) {
    var popoverTriggerList = [].slice.call($(context).find('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        
        var popover = new bootstrap.Popover(popoverTriggerEl, {
            trigger: 'focus' 
        });

        popoverTriggerEl.addEventListener('click', function (e) {
            e.preventDefault();
            popoverTriggerList.forEach(function (otherPopoverEl) {
                if (otherPopoverEl !== popoverTriggerEl) {
                    var otherPopover = bootstrap.Popover.getInstance(otherPopoverEl);
                    if (otherPopover) {
                        otherPopover.hide();
                    }
                }
            });
        });

        return popover;
    });
}

    // --- 2. إعداد النافذة المنبثقة الرئيسية (mainModal) ---
    var mainModalElement = document.getElementById('mainModal');
    if (mainModalElement) {
        var modalInstance = new bootstrap.Modal(mainModalElement);
        $('#mainModal').on('show.bs.modal', function(e) {
            var button = e.relatedTarget;
            var url = $(button).data('bs-url');
            var title = $(button).data('bs-title');
            $('#mainModalLabel').text(title);
            $('#mainModalBody').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            
            $.get(url, function(data) {
                $('#mainModalBody').html(data);
                initializeSelect2('#mainModalBody');
                activatePopovers('#mainModalBody');
            }).fail(function() {
                $('#mainModalBody').html('<div class="alert alert-danger">فشل تحميل المحتوى.</div>');
            });
        });
    }

    // --- 3. معالجة إرسال النماذج التي تعمل بـ AJAX ---
    $(document).on('submit', '.ajax-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري الحفظ...');

        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    modalInstance.hide();
                    location.reload();
                } else {
                    form.find('#form-error-message').text(response.message || 'حدث خطأ.').show();
                }
            },
            error: function() {
                form.find('#form-error-message').text('حدث خطأ غير متوقع في الاتصال.').show();
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });

    // --- 4. تفعيل المكونات التفاعلية في الصفحة الرئيسية ---
    // تفعيل Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // تفعيل Popovers الموجودة في الصفحة الرئيسية (خارج النوافذ المنبثقة)
    activatePopovers(document.body);


    // --- 5. منطق أزرار الصعود والنزول ---
    $(window).scroll(function() {
        if ($(this).scrollTop() > 200) {
            $('.scroll-buttons').fadeIn();
        } else {
            $('.scroll-buttons').fadeOut();
        }
    });

}); // <-- هذا هو القوس الناقص الذي تم إصلاحه
</script>

<?php 
if (isset($page_scripts) && !empty($page_scripts)) {
    echo $page_scripts;
}
?>

<!-- Scroll to Top/Bottom Buttons -->
<div class="scroll-buttons">
    <button onclick="window.scrollTo(0, 0);" class="btn btn-dark" title="صعود لأعلى">
        <i class="fas fa-arrow-up"></i>
    </button>
    <button onclick="window.scrollTo(0, document.body.scrollHeight);" class="btn btn-dark" title="نزول لأسفل">
        <i class="fas fa-arrow-down"></i>
    </button>
</div>
</body>
</html>