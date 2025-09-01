<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="index.php?page=dashboard">
                <img src="./assets/static/logo.svg" width="110" height="32" alt="System Logo" class="navbar-brand-image">
            </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex">
                <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="تفعيل الوضع الليلي" data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <i class="ti ti-moon"></i>
                </a>
                <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="تفعيل الوضع النهاري" data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <i class="ti ti-sun"></i>
                </a>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url(./assets/static/avatars/073m.jpg)"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div><?= htmlspecialchars($_SESSION['username'] ?? 'المستخدم') ?></div>
                        <div class="mt-1 small text-muted">مدير النظام</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="#" class="dropdown-item">الحالة</a>
                    <a href="#" class="dropdown-item">الملف الشخصي</a>
                    <a href="#" class="dropdown-item">الملاحظات</a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">الإعدادات</a>
                    <a href="index.php?page=logout" class="dropdown-item">تسجيل الخروج</a>
                </div>
            </div>
        </div>
    </div>
</header>