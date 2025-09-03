<!-- =============================================== -->
<!-- START: Core Modals & JavaScript Libraries       -->
<!-- =============================================== -->

<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js"></script>


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
<script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>
<!-- إذا أردت إضافة مكتبات أخرى مثل Alpine.js أو Toastify، أضفها هنا -->

<!-- =============================================== -->
<!-- END: Core Modals & JavaScript Libraries         -->
<!-- =============================================== -->

<!-- =============================================== -->
<!-- START: Central Application JavaScript Logic     -->
<!-- =============================================== -->

<!-- تنظيم السكريبتات في ملفات مستقلة حسب الوظيفة -->
<script src="assets/js/helpers.js"></script>
<script src="assets/js/modals.js"></script>
<script src="assets/js/batch_actions.js"></script>
<script src="assets/js/handsontable.js"></script>
<script src="assets/js/scroll_buttons.js"></script>

<!-- يمكنك هنا إضافة أي كود صغير خاص بالصفحة مباشرة -->
<script>
    // مثال: تشغيل TomSelect على عناصر جديدة بعد تحميل محتوى ديناميكي
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof initializeTomSelect === 'function') {
            initializeTomSelect(document.body);
        }
    });

    // إذا كان لديك متغيرات ديناميكية من PHP للصفحات الجماعية، مررها هنا:
    // مثال:
    // window.initialData = <?= json_encode($users_for_js ?? [], JSON_UNESCAPED_UNICODE) ?>;
    // window.rolesSource = <?= json_encode($roles_for_js ?? [], JSON_UNESCAPED_UNICODE) ?>;
</script>
<!-- =============================================== -->
<!-- END: Central Application JavaScript Logic       -->
<!-- =============================================== -->