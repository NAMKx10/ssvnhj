<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <title>نظام إدارة الأعمال</title>
   
    <!-- Tabler & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.rtl.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.34.1/tabler-icons.min.css" rel="stylesheet"/>

    <!-- SweetAlert2 & TomSelect -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.22.5/sweetalert2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.4.3/css/tom-select.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">

    <!-- Handsontable -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/styles/handsontable.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/styles/ht-theme-main.min.css" />

    <!-- Alpine.js (جديد) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.9/cdn.min.js" defer></script>

    <!-- Handsontable JS -->
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
        .modal { z-index: 1060 !important; }
        .swal2-container { z-index: 1070 !important; }
    </style>
</head>
<body class="layout-fluid" x-data="{ sidebarOpen: false, theme: localStorage.getItem('theme') || 'light' }" :class="theme" x-init="
    // تفعيل الوضع الليلي/النهاري من LocalStorage
    document.documentElement.setAttribute('data-theme', theme);
    $watch('theme', value => {
        localStorage.setItem('theme', value);
        document.documentElement.setAttribute('data-theme', value);
    });
">
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
    <!-- Tabler JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js" defer></script>
    <?php require_once ROOT_PATH . '/app/Common/Views/partials/_footer_scripts.php'; ?>

    <!-- مثال تفعيل Alpine.js للتبويبات أو أي تفاعل بسيط -->
    <script>
        // مثال: يمكنك الآن استخدام Alpine مباشرة في العناصر مثل:
        // <div x-data="{ open: false }"><button @click="open = !open">فتح</button><div x-show="open">محتوى مخفي</div></div>
    </script>
</body>
</html>