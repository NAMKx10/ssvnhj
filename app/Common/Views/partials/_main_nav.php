<?php
// تحديد الصفحة الحالية لتفعيل القائمة النشطة
$current_page = $_GET['page'] ?? 'dashboard';

// تعريف المجموعات لتسهيل تفعيل القوائم المنسدلة
$entities_pages = ['branches', 'contacts', 'properties', 'units'];
$contracts_pages = ['contracts', 'supply_contracts'];
$financial_pages = ['invoices', 'payments', 'financial_reports'];
$admin_pages = ['users', 'roles', 'settings', 'archive'];
?>
<div class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">
                    
                    <li class="nav-item <?= ($current_page === 'dashboard') ? 'active' : '' ?>">
                        <a class="nav-link" href="index.php?page=dashboard">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-home"></i></span>
                            <span class="nav-link-title">الرئيسية</span>
                        </a>
                    </li>

                    <li class="nav-item dropdown <?= in_array($current_page, $entities_pages) ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-entities" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-building-community"></i></span>
                            <span class="nav-link-title">الكيانات والأصول</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">الفروع</a>
                            <a class="dropdown-item" href="#">جهات الاتصال</a>
                            <a class="dropdown-item" href="#">العقارات</a>
                            <a class="dropdown-item" href="#">الوحدات</a>
                        </div>
                    </li>

                    <li class="nav-item dropdown <?= in_array($current_page, $contracts_pages) ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-contracts" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-file-text"></i></span>
                            <span class="nav-link-title">العقود والوثائق</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">عقود الإيجار</a>
                            <a class="dropdown-item" href="#">عقود التوريد</a>
                            <a class="dropdown-item" href="#">الوثائق والمستندات</a>
                        </div>
                    </li>

                    <li class="nav-item dropdown <?= in_array($current_page, $financial_pages) ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-financial" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-cash"></i></span>
                            <span class="nav-link-title">المركز المالي</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">سندات القبض</a>
                            <a class="dropdown-item" href="#">سندات الصرف</a>
                            <a class="dropdown-item" href="#">الفواتير</a>
                            <a class="dropdown-item" href="#">الحسابات البنكية</a>
                            <a class="dropdown-item" href="#">شجرة الحسابات</a>
                            <a class="dropdown-item" href="#">قيود اليومية</a>
                        </div>
                    </li>

                    <li class="nav-item dropdown <?= in_array($current_page, $admin_pages) ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-admin" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-settings"></i></span>
                            <span class="nav-link-title">إدارة النظام</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="index.php?page=users">المستخدمين</a>
                            <a class="dropdown-item" href="index.php?page=roles">الأدوار والصلاحيات</a>
                            <a class="dropdown-item" href="#">تهيئة المدخلات</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">سجل الحركات</a>
                            <a class="dropdown-item" href="index.php?page=archive">الأرشيف</a>
                            <a class="dropdown-item" href="#">حول النظام</a>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</div>