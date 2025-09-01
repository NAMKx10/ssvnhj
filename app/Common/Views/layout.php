<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <title>نظام إدارة الأعمال</title>
   
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.rtl.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.34.1/tabler-icons.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.22.5/sweetalert2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.4.3/css/tom-select.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/styles/handsontable.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/styles/ht-theme-main.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>


<style>
    .scroll-buttons {
    position: fixed;
    bottom: 20px;
    left: 20px;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
    /* ارفع طبقة النوافذ المنبثقة لتكون فوق كل شيء */
    .modal {z-index: 1060 !important;}
    /* ارفع طبقة رسائل التأكيد لتكون أعلى طبقة على الإطلاق */
    .swal2-container {z-index: 1070 !important;}
</style>

</head>
<body class="layout-fluid">
    <div class="page">
        
        <!-- تضمين الشريط العلوي -->
        <?php require_once ROOT_PATH . '/app/Common/Views/partials/_header.php'; ?>

        <!-- تضمين القائمة الرئيسية -->
        <?php require_once ROOT_PATH . '/app/Common/Views/partials/_main_nav.php'; ?>

        <div class="page-wrapper">
            <div class="page-body">
                <div class="container-xl">
                    <?php echo $page_content ?? ''; ?>
                </div>
            </div>
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <p class="text-center">جميع الحقوق محفوظة © <?= date('Y') ?></p>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js" defer></script>
    <?php require_once ROOT_PATH . '/app/Common/Views/partials/_footer_scripts.php'; ?>
</body>
</html>