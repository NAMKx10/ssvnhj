<?php
// =================================================================
// ملف عرض الفروع (branches_view.php) - النسخة المطورة
// =================================================================

// 1. إعدادات الترقيم والفلترة
// -----------------------------------------------------------------
$records_per_page_options = [10, 25, 50, 100];
$default_records_per_page = 10;

$filter_q = $_GET['q'] ?? null;
$filter_type = $_GET['type'] ?? null;
$filter_status = $_GET['status'] ?? null;
$limit = isset($_GET['limit']) && in_array($_GET['limit'], $records_per_page_options) ? (int)$_GET['limit'] : $default_records_per_page;
$current_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($current_page - 1) * $limit;

// 2. بناء الاستعلام الديناميكي
// -----------------------------------------------------------------
$sql_from_joins = " FROM branches b ";
$sql_where = " WHERE b.deleted_at IS NULL ";
$params = [];

if (!empty($filter_q)) {
    $search_term = '%' . $filter_q . '%';
    $sql_where .= " AND (b.branch_name LIKE ? OR b.registration_number LIKE ? OR b.tax_number LIKE ? OR b.phone LIKE ?) ";
    array_push($params, $search_term, $search_term, $search_term, $search_term);
}
if (!empty($filter_type)) {
    $sql_where .= " AND b.branch_type = ? ";
    $params[] = $filter_type;
}
if (!empty($filter_status)) {
    $sql_where .= " AND b.status = ? ";
    $params[] = $filter_status;
}

// 3. حساب الإحصائيات والإجمالي
// -----------------------------------------------------------------
$stats_sql = "
    SELECT 
        COUNT(b.id) AS total_count,
        SUM(CASE WHEN b.branch_type = 'فرد' THEN 1 ELSE 0 END) AS individual_count,
        SUM(CASE WHEN b.branch_type = 'منشأة' THEN 1 ELSE 0 END) AS company_count,
        SUM(CASE WHEN b.status = 'نشط' THEN 1 ELSE 0 END) AS active_count
    " . $sql_from_joins . $sql_where;
    
$stats_stmt = $pdo->prepare($stats_sql);
$stats_stmt->execute($params);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

$total_records = $stats['total_count'] ?? 0;
$total_pages = ceil($total_records / $limit);

// 4. جلب سجلات الصفحة الحالية
// -----------------------------------------------------------------
$data_sql = "SELECT b.* " . $sql_from_joins . $sql_where . " ORDER BY b.id DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
$data_stmt = $pdo->prepare($data_sql);
$data_stmt->execute($params);
$branches = $data_stmt->fetchAll();

$status_colors = ['نشط' => 'success', 'ملغي' => 'danger'];
?>

<!-- ============================================================= -->
<!-- بداية عرض الواجهة (HTML)                                    -->
<!-- ============================================================= -->

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-sitemap ms-2"></i>إدارة الفروع</h1>
    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#mainModal" data-bs-url="index.php?page=branches/add&view_only=true" data-bs-title="إضافة فرع جديد">
            <i class="fas fa-plus-circle ms-1"></i>إضافة فرع جديد
        </button>
    </div>
</div>

<!-- نموذج الفرز والبحث -->
<div class="card bg-light mb-4">
    <div class="card-body">
        <form action="index.php" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="branches">
            <div class="col-md-5"><label for="q" class="form-label">بحث شامل</label><input type="search" class="form-control" name="q" id="q" placeholder="ابحث بالاسم، السجل، الجوال..." value="<?php echo htmlspecialchars($filter_q ?? ''); ?>"></div>
            <div class="col-md-3"><label for="type" class="form-label">النوع</label><select name="type" id="type" class="form-select"><option value="">الكل</option><option value="فرد" <?php echo ($filter_type == 'فرد') ? 'selected' : ''; ?>>فرد</option><option value="منشأة" <?php echo ($filter_type == 'منشأة') ? 'selected' : ''; ?>>منشأة</option></select></div>
            <div class="col-md-2"><label for="status" class="form-label">الحالة</label><select name="status" id="status" class="form-select"><option value="">كل الحالات</option><option value="نشط" <?php echo ($filter_status == 'نشط') ? 'selected' : ''; ?>>نشط</option><option value="ملغي" <?php echo ($filter_status == 'ملغي') ? 'selected' : ''; ?>>ملغي</option></select></div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-search ms-1"></i> تطبيق</button><a href="index.php?page=branches" class="btn btn-secondary w-100 mt-1">إلغاء</a></div>
        </form>
    </div>
</div>

<!-- شريط الإحصائيات -->
<div class="row mb-4 text-center">
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6 class="card-subtitle mb-2 text-muted">إجمالي الفروع</h6><p class="card-text fs-4 fw-bold text-primary"><?php echo $stats['total_count'] ?? 0; ?></p></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6 class="card-subtitle mb-2 text-muted">فروع (أفراد)</h6><p class="card-text fs-4 fw-bold text-info"><?php echo $stats['individual_count'] ?? 0; ?></p></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6 class="card-subtitle mb-2 text-muted">فروع (منشآت)</h6><p class="card-text fs-4 fw-bold text-secondary"><?php echo $stats['company_count'] ?? 0; ?></p></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6 class="card-subtitle mb-2 text-muted">الفروع النشطة</h6><p class="card-text fs-4 fw-bold text-success"><?php echo $stats['active_count'] ?? 0; ?></p></div></div></div>
</div>

<!-- جدول عرض البيانات -->
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr><th>#</th>
            <th>اسم الفرع</th>
            <th>كود الفرع</th>
            <th>النوع</th>
            <th>رقم السجل</th>
            <th>الرقم الضريبي</th>
            <th>الجوال</th>
            <th>العنوان</th>
             <th>ملاحظات</th>
            <th>الحالة</th>
            <th>الإجراءات</th></tr>
        </thead>
        <tbody>
            <?php if (empty($branches)): ?>
                <tr><td colspan="10" class="text-center">لا توجد سجلات تطابق شروط البحث.</td></tr>
            <?php else: ?>
                <?php foreach ($branches as $branch): ?>
                    <tr>
                        <td><?php echo $branch['id']; ?></td>
                        <td><?php echo htmlspecialchars($branch['branch_name']); ?></td>
                        <td><?php echo htmlspecialchars($branch['branch_code'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($branch['branch_type']); ?></td>
                        <td><?php echo htmlspecialchars($branch['registration_number'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($branch['tax_number'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($branch['phone'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($branch['address'] ?? '—'); ?></td>
                        <td><?php if (!empty($branch['notes'])): ?><i class="fas fa-info-circle text-primary" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($branch['notes']); ?>"></i><?php endif; ?></td>
                        <td><span class="badge bg-<?php echo $status_colors[$branch['status']] ?? 'secondary'; ?>"><?php echo htmlspecialchars($branch['status']); ?></span></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#mainModal" data-bs-url="index.php?page=branches/edit&id=<?php echo $branch['id']; ?>&view_only=true" data-bs-toggle="tooltip" title="تعديل"><i class="fas fa-edit"></i></button>
                                <a href="index.php?page=branches/delete&id=<?php echo $branch['id']; ?>" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="حذف" onclick="return confirm('سيتم نقل هذا الفرع إلى الأرشيف. هل أنت متأكد؟');"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- شريط الترقيم -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <?php render_smart_pagination($current_page, $total_pages, $_GET); ?>
</div>