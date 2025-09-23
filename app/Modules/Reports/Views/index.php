<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl"><h2 class="page-title">مركز التقارير</h2></div>
</div>

<!-- محتوى الصفحة -->
<div class="page-body">
    <div class="container-xl">
        <div class="row gx-lg-4">
            <!-- العمود الأول: قائمة التقارير -->
            <div class="col-lg-3">
                <div class="list-group list-group-transparent mb-3">
                    <?php foreach ($report_groups as $group_name => $reports): ?>
                        <div class="list-group-header"><?= html($group_name) ?></div>
                        <?php foreach ($reports as $report_key => $report_name): ?>
                            <a href="index.php?page=reports&report=<?= $report_key ?>" class="list-group-item list-group-item-action <?= ($active_report_key == $report_key) ? 'active' : '' ?>">
                                <?= html($report_name) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- العمود الثاني: منطقة التقرير -->
            <div class="col-lg-9">
                <?php if ($active_report_key): ?>
                    <div class="card card-lg">
                        <div class="card-body">
                            <?php
                            // تضمين ملف الفلاتر والعرض الخاص بالتقرير المحدد
                            $report_file = ROOT_PATH . "/app/Modules/Reports/Views/{$active_report_key}_view.php";
                            if (file_exists($report_file)) {
                                require $report_file;
                            } else {
                                echo "<p class='text-danger'>ملف التقرير غير موجود.</p>";
                            }
                            ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty">
                        <div class="empty-icon"><i class="ti ti-chart-bar"></i></div>
                        <p class="empty-title">مرحباً بك في مركز التقارير</p>
                        <p class="empty-subtitle text-muted">يرجى تحديد تقرير من القائمة على اليمين لعرض خياراته.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>