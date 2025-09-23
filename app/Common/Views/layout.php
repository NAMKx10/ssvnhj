<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <title>نظام إدارة الأعمال</title>
   
    <!-- Tabler & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.rtl.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.34.1/tabler-icons.min.css" rel="stylesheet"/>

    <!-- SweetAlert2 & TomSelect -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.23.0/sweetalert2.min.css" rel="stylesheet" />
    <link href="assets/css/tom-select.css" rel="stylesheet">

    <!-- Handsontable -->
    <link rel="stylesheet" href="assets/css/handsontable.min.css" />
    <link rel="stylesheet" href="assets/css/ht-theme-main.min.css" />

    <!-- Handsontable JS -->
    <script src="assets/js/handsontable.full.min.js"></script>

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
        .modal { z-index: 1060 !important; }
        .swal2-container { z-index: 1070 !important; }
    </style>
</head>
<body class="layout-fluid">
    <div class="page">

        <!-- الشريط العلوي -->
        <?php require_once ROOT_PATH . '/app/Common/Views/partials/_header.php'; ?>

        <!-- القائمة الرئيسية -->
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

<!-- الآن قم بتضمين السكريبتات المخصصة بنا -->
<?php require_once ROOT_PATH . '/app/Common/Views/partials/_footer_scripts.php'; ?>
    
</body>
</html>