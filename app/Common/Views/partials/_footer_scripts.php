<!-- =============================================== -->
<!-- START: Core JavaScript Libraries & Logic        -->
<!-- =============================================== -->

<!-- 1. Tabler/Bootstrap Core (يجب تحميله مرة واحدة فقط هنا) -->
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js"></script>

<!-- 2. الهيكل الأساسي للنافذة المنبثقة -->
<div class="modal modal-blur fade" id="main-modal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content"></div>
  </div>
</div>

<!-- 3. أزرار الصعود والنزول -->
<div class="scroll-buttons">
    <a href="#" id="scroll-to-bottom-btn" class="btn btn-icon btn-primary" title="النزول للأسفل"><i class="ti ti-arrow-down"></i></a>
    <a href="#" id="scroll-to-top-btn" class="btn btn-icon btn-primary" title="الصعود للأعلى" style="display: none;"><i class="ti ti-arrow-up"></i></a>
</div>

<!-- 4. المكتبات الإضافية (محلية) -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js"></script>
<script src="assets/js/tom-select.complete.min.js"></script>
<script src="assets/js/handsontable.full.min.js"></script>
<!-- (تم حذف Alpine.js) -->

<!-- 5. السكريبتات المخصصة بنا -->
<script src="assets/js/helpers.js"></script>
<script src="assets/js/modals.js"></script>
<script src="assets/js/dynamic_links.js"></script>
<script src="assets/js/batch_actions.js"></script>
<script src="assets/js/handsontable.js"></script>
<script src="assets/js/scroll_buttons.js"></script>

<!-- 6. كود التشغيل النهائي -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof initializeTomSelect === 'function') {
            initializeTomSelect(document.body);
        }
    });
</script>

<!-- =============================================== -->
<!-- END: Core JavaScript Libraries & Logic          -->
<!-- =============================================== -->