<?php
$current_module = explode('/', ($_GET['page'] ?? 'dashboard'))[0];
?>
<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark sidebar">
    <a href="index.php?page=dashboard" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="fas fa-building-user fa-2x ms-2"></i>
        <span class="fs-4"><?php echo htmlspecialchars($settings['site_name'] ?? 'إدارة الأملاك'); ?></span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        
        <!-- القسم الأول: الرئيسية -->
        <?php if (has_permission('view_dashboard')): ?>
            <li class="nav-item">
                <a href="index.php?page=dashboard" class="nav-link text-white <?php echo ($current_module === 'dashboard') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt fa-fw ms-2"></i>الرئيسية
                </a>
            </li>
        <?php endif; ?>
        
        <!-- القسم الثاني: الإدارة الأساسية -->
        <li class="nav-item mt-2"><small class="text-muted ps-3">الإدارة الأساسية</small></li>
        <?php if (has_permission('view_properties')): ?><li><a href="index.php?page=properties" class="nav-link text-white <?php echo ($current_module === 'properties') ? 'active' : ''; ?>"><i class="fas fa-building fa-fw ms-2"></i>العقارات</a></li><?php endif; ?>
        <?php if (has_permission('view_units')): ?><li><a href="index.php?page=units" class="nav-link text-white <?php echo ($current_module === 'units') ? 'active' : ''; ?>"><i class="fas fa-door-closed fa-fw ms-2"></i>الوحدات</a></li><?php endif; ?>
        <?php if (has_permission('view_clients')): ?><li><a href="index.php?page=clients" class="nav-link text-white <?php echo ($current_module === 'clients') ? 'active' : ''; ?>"><i class="fas fa-users fa-fw ms-2"></i>العملاء</a></li><?php endif; ?>
        <?php if (has_permission('view_suppliers')): ?><li><a href="index.php?page=suppliers" class="nav-link text-white <?php echo ($current_module === 'suppliers') ? 'active' : ''; ?>"><i class="fas fa-truck fa-fw ms-2"></i>الموردين</a></li><?php endif; ?>

        <!-- القسم الثالث: العقود والمالية -->
        <li class="nav-item mt-2"><small class="text-muted ps-3">العقود والمالية</small></li>
        <?php if (has_permission('view_contracts')): ?><li><a href="index.php?page=contracts" class="nav-link text-white <?php echo ($current_module === 'contracts') ? 'active' : ''; ?>"><i class="fas fa-file-signature fa-fw ms-2"></i>عقود الإيجار</a></li><?php endif; ?>
        <?php if (has_permission('view_supply_contracts')): ?><li><a href="index.php?page=supply_contracts" class="nav-link text-white <?php echo ($current_module === 'supply_contracts') ? 'active' : ''; ?>"><i class="fas fa-file-invoice fa-fw ms-2"></i>عقود التوريد</a></li><?php endif; ?>
        
        <!-- القسم الرابع: التقارير والبيانات (تمت إعادته) -->
        <li class="nav-item mt-2"><small class="text-muted ps-3">البيانات والتقارير</small></li>
        <?php if (has_permission('view_reports')): ?><li><a href="index.php?page=reports" class="nav-link text-white <?php echo ($current_module === 'reports') ? 'active' : ''; ?>"><i class="fas fa-chart-pie fa-fw ms-2"></i>التقارير</a></li><?php endif; ?>
        <!-- (يمكن إضافة الوثائق والمشاريع هنا بنفس الطريقة مستقبلاً) -->

        <!-- القسم الخامس: إدارة النظام -->
        <li class="nav-item mt-2"><small class="text-muted ps-3">إدارة النظام</small></li>
        <?php if (has_permission('view_branches')): ?><li><a href="index.php?page=branches" class="nav-link text-white <?php echo ($current_module === 'branches') ? 'active' : ''; ?>"><i class="fas fa-sitemap fa-fw ms-2"></i>الفروع</a></li><?php endif; ?>
        <?php if (has_permission('manage_permissions')): ?><li><a href="index.php?page=permissions" class="nav-link text-white <?php echo ($current_module === 'permissions') ? 'active' : ''; ?>"><i class="fas fa-key fa-fw ms-2"></i>الصلاحيات</a></li><?php endif; ?>
        <?php if (has_permission('view_users')): ?><li><a href="index.php?page=users" class="nav-link text-white <?php echo ($current_module === 'users') ? 'active' : ''; ?>"><i class="fas fa-users-cog fa-fw ms-2"></i>المستخدمين</a></li><?php endif; ?>
        <?php if (has_permission('view_roles')): ?><li><a href="index.php?page=roles" class="nav-link text-white <?php echo ($current_module === 'roles') ? 'active' : ''; ?>"><i class="fas fa-user-shield fa-fw ms-2"></i>الأدوار</a></li><?php endif; ?>
        <?php if (has_permission('manage_settings')): ?><li><a href="index.php?page=settings/lookups" class="nav-link text-white <?php echo ($current_module === 'settings') ? 'active' : ''; ?>"><i class="fas fa-cogs fa-fw ms-2"></i>الإعدادات</a></li><?php endif; ?>
        <?php if (has_permission('view_archive')): ?><li><a href="index.php?page=archive" class="nav-link text-white <?php echo ($current_module === 'archive') ? 'active' : ''; ?>"><i class="fas fa-archive fa-fw ms-2"></i>الأرشيف</a></li><?php endif; ?>

    </ul>
    <hr>
<ul class="nav nav-pills flex-column">
    <li class="nav-item">
        <a href="index.php?page=about" class="nav-link text-white <?php echo ($current_module === 'about') ? 'active' : ''; ?>">
            <i class="fas fa-info-circle fa-fw ms-2"></i>حول النظام
        </a>
    </li>
</ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle fa-fw ms-2"></i>
            <strong><?php if (isset($_SESSION['username'])) { echo htmlspecialchars($_SESSION['username']); } ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="index.php?page=logout">تسجيل الخروج</a></li>
        </ul>
    </div>
</div>