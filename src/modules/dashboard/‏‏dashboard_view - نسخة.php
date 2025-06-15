<?php
// =================================================================
// 1. جلب الإحصائيات الرئيسية
// =================================================================
$stats = $pdo->query("
    SELECT
        (SELECT COUNT(*) FROM branches WHERE deleted_at IS NULL AND status = 'نشط') as active_branches,
        (SELECT COUNT(*) FROM properties WHERE deleted_at IS NULL AND status = 'نشط') as active_properties,
        (SELECT COUNT(*) FROM units WHERE deleted_at IS NULL AND status = 'متاحة') as available_units,
        (SELECT COUNT(*) FROM clients WHERE deleted_at IS NULL AND status = 'نشط') as active_clients
")->fetch(PDO::FETCH_ASSOC);

// =================================================================
// 2. جلب التنبيهات العاجلة
// =================================================================
// عقود الإيجار التي تنتهي خلال 30 يومًا
$expiring_contracts = $pdo->query("
    SELECT cr.id, cr.contract_number, cr.end_date, c.client_name, DATEDIFF(cr.end_date, CURDATE()) as days_left
    FROM contracts_rental cr
    JOIN clients c ON cr.client_id = c.id
    WHERE cr.deleted_at IS NULL AND cr.status = 'نشط' AND cr.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ORDER BY cr.end_date ASC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// الدفعات المتأخرة (إيجار وتوريد)
$late_payments = $pdo->query("
    SELECT 
        ps.id, ps.due_date, ps.amount_due, ps.amount_paid, 
        (ps.amount_due - ps.amount_paid) as remaining,
        CASE 
            WHEN ps.contract_type = 'rental' THEN (SELECT c.client_name FROM clients c JOIN contracts_rental cr ON c.id = cr.client_id WHERE cr.id = ps.contract_id)
            WHEN ps.contract_type = 'supply' THEN (SELECT s.supplier_name FROM suppliers s JOIN contracts_supply cs ON s.id = cs.supplier_id WHERE cs.id = ps.contract_id)
        END as party_name,
        ps.contract_type
    FROM payment_schedules ps
    WHERE ps.status != 'مدفوع بالكامل' AND ps.due_date < CURDATE()
    ORDER BY ps.due_date ASC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);


// =================================================================
// 3. بيانات الرسوم البيانية
// =================================================================
// توزيع حالات الوحدات
$units_status_data = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM units 
    WHERE deleted_at IS NULL 
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

$chart_labels = json_encode(array_keys($units_status_data));
$chart_values = json_encode(array_values($units_status_data));

?>

<!-- تحميل مكتبة الرسوم البيانية -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- بداية عرض الواجهة -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-chart-pie ms-2"></i>لوحة التحكم الرئيسية</h1>
</div>

<!-- صف الإحصائيات الرئيسي -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body"><h5 class="card-title">الفروع النشطة</h5><p class="card-text fs-2 fw-bold"><?= $stats['active_branches'] ?? 0 ?></p></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body"><h5 class="card-title">العقارات النشطة</h5><p class="card-text fs-2 fw-bold"><?= $stats['active_properties'] ?? 0 ?></p></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body"><h5 class="card-title">الوحدات المتاحة</h5><p class="card-text fs-2 fw-bold"><?= $stats['available_units'] ?? 0 ?></p></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-white bg-secondary shadow-sm h-100">
            <div class="card-body"><h5 class="card-title">العملاء النشطين</h5><p class="card-text fs-2 fw-bold"><?= $stats['active_clients'] ?? 0 ?></p></div>
        </div>
    </div>
</div>

<!-- التنبيهات والرسوم البيانية -->
<div class="row">
    <!-- عمود التنبيهات -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-bell text-warning ms-2"></i>تنبيهات وإجراءات عاجلة</h5></div>
            <div class="card-body">
                <h6><i class="fas fa-file-signature text-danger"></i> عقود تنتهي قريباً</h6>
                <ul class="list-group list-group-flush mb-3">
                    <?php if(empty($expiring_contracts)): ?>
                        <li class="list-group-item text-muted">لا توجد عقود تنتهي خلال 30 يوم.</li>
                    <?php else: foreach($expiring_contracts as $contract): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="index.php?page=contracts/view&id=<?= $contract['id'] ?>"><?= htmlspecialchars($contract['client_name']) ?> (عقد <?= htmlspecialchars($contract['contract_number']) ?>)</a>
                            <span class="badge bg-danger rounded-pill">باقي <?= $contract['days_left'] ?> يوم</span>
                        </li>
                    <?php endforeach; endif; ?>
                </ul>

                <h6><i class="fas fa-money-bill-wave text-danger"></i> دفعات متأخرة</h6>
                <ul class="list-group list-group-flush">
                     <?php if(empty($late_payments)): ?>
                        <li class="list-group-item text-muted">لا توجد دفعات متأخرة.</li>
                    <?php else: foreach($late_payments as $payment): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($payment['party_name']) ?> (متبقي: <strong class="text-danger"><?= number_format($payment['remaining'], 2) ?></strong>)</span>
                            <small class="text-muted">مستحقة منذ <?= $payment['due_date'] ?></small>
                        </li>
                    <?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- عمود الرسوم البيانية -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-pie ms-2"></i>توزيع حالات الوحدات</h5></div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="unitsStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('unitsStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut', // أو 'pie'
        data: {
            labels: <?= $chart_labels ?>,
            datasets: [{
                label: 'عدد الوحدات',
                data: <?= $chart_values ?>,
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)', // success - متاحة
                    'rgba(255, 193, 7, 0.7)',  // warning - مؤجرة
                    'rgba(220, 53, 69, 0.7)',  // danger - ملغاة
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false,
                    text: 'حالة الوحدات'
                }
            }
        }
    });
});
</script>