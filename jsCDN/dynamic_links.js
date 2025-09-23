// public/assets/js/dynamic_links.js (النسخة النهائية الذكية)

// دالة مركزية لجلب وتعبئة الخيارات
function fetchAndPopulateTargets($modelSelect) {
    const model = $modelSelect.val();
    const $targetSelect = $modelSelect.closest('.link-row').find('.link-target-select');
    const selectedId = $modelSelect.data('selected-id'); 

    $targetSelect.html('<option value="">... جاري التحميل ...</option>');

    if (!model) {
        $targetSelect.html('<option value="">-- اختر الكيان --</option>');
        return;
    }

    $.ajax({
        url: `index.php?page=ajax_get_targets&model=${model}`,
        type: 'GET',
        dataType: 'json',
        success: function(targets) {
            $targetSelect.html('<option value="">-- اختر الكيان --</option>');
            $.each(targets, function(index, target) {
                const $option = $('<option>', { value: target.id, text: target.name });
                // إذا كان هذا هو الـ ID المطلوب، قم بتحديده
                if (target.id == selectedId) {
                    $option.prop('selected', true);
                }
                $targetSelect.append($option);
            });
        },
        error: function() {
            $targetSelect.html('<option value="">فشل التحميل</option>');
        }
    });
}


$(document).ready(function() {
    let linkCounter = 100;

    // استخدام event delegation لزر الإضافة
    $('body').on('click', '#add-link-btn', function() {
        const templateHtml = $('#link-row-template').html().replace(/__COUNTER__/g, linkCounter);
        $('#links-container').append(templateHtml);
        linkCounter++;
    });

    // استخدام event delegation لزر الحذف
    $('body').on('click', '.delete-link-btn', function() {
        $(this).closest('.link-row').remove();
    });

    // استخدام event delegation لربط حدث التغيير بالقوائم الجديدة
    $('body').on('change', '.link-model-select', function() {
        // عند التغيير، قم بمسح selected-id القديم لتجنب إعادة تحديده
        $(this).data('selected-id', null);
        fetchAndPopulateTargets($(this));
    });

    // --- الجزء الجديد والمهم ---
    // عند فتح أي مودال، ابحث عن الروابط الموجودة مسبقًا وقم بتشغيلها
    $('#main-modal').on('shown.bs.modal', function () {
        $(this).find('.link-model-select').each(function() {
            fetchAndPopulateTargets($(this));
        });
    });
});