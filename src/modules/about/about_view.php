<?php
$system_name = $settings['site_name'] ?? 'نظام إدارة الأملاك';
?>

<!-- CSS مدمج لتطبيق تصميم بصري غني -->
<style>
    /* هيدر الفيديو */
    .about-header-video {
        position: relative;
        padding: 6rem 2rem;
        border-radius: 0.75rem;
        overflow: hidden;
        text-align: center;
        color: white;
    }
    .about-header-video video {
        position: absolute;
        top: 50%;
        left: 50%;
        min-width: 100%;
        min-height: 100%;
        width: auto;
        height: auto;
        z-index: 0;
        transform: translateX(-50%) translateY(-50%);
    }
    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background-color: rgba(6, 47, 79, 0.7); /* تغميق الفيديو لإبراز النص */
        z-index: 1;
    }
    .header-content {
        position: relative;
        z-index: 2;
    }
    .header-content h1 {
        font-weight: 700;
        font-size: 3rem;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
    }

    /* البطاقات */
    .feature-card-visual {
        border: none;
        box-shadow: 0 4px 25px rgba(0,0,0,0.07);
        border-radius: 0.75rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .feature-card-visual:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }
    .feature-card-visual img.illustration {
        max-height: 180px;
        margin-bottom: 1.5rem;
    }

    /* قائمة الخطط المستقبلية */
    .future-plans-list .list-group-item {
        border: none;
        padding: 1rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .future-plans-list .list-group-item:last-child {
        border-bottom: none;
    }
    .future-plans-list .list-icon {
        font-size: 1.5rem;
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: rgba(40, 167, 69, 0.1); /* خلفية خضراء شفافة */
        color: #198754; /* لون أخضر */
    }

    /* قسم المطور */
    .creator-section {
        margin-top: 3rem;
        padding: 2.5rem;
        background-color: #062f4f; /* لون أزرق غامق */
        color: white;
        border-radius: 0.5rem;
        text-align: center;
    }
    .creator-section .creator-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid #0d6efd;
        margin-bottom: 1rem;
    }
</style>

<!-- بداية عرض الواجهة -->

<!-- 1. هيدر الفيديو -->
<div class="about-header-video mb-5">
    <div class="video-overlay"></div>
    <!-- استخدمت فيديو مجاني بدون حقوق ملكية -->
    <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
        <source src="https://cdn.coverr.co/videos/coverr-a-city-street-at-night-4202/1080p.mp4" type="video/mp4">
    </video>
    <div class="header-content">
        <h1><?= htmlspecialchars($system_name) ?></h1>
        <p class="lead mt-3">منصة متكاملة لإدارة الأصول والعقارات، مصممة لتحويل البيانات إلى قرارات استراتيجية ناجحة.</p>
    </div>
</div>

<!-- 2. الأهداف والرسالة مع صور SVG -->
<div class="row g-4 mb-5">
    <div class="col-lg-6">
        <div class="card feature-card-visual h-100 p-4">
            <div class="card-body text-center">
                <img src="https://www.svgrepo.com/show/493635/data-analysis-and-research.svg" class="illustration" alt="Our Goal">
                <h4 class="fw-bold">فكرتنا وهدفنا</h4>
                <p class="text-muted">الانطلاق من مجرد نظام تقليدي إلى مركز عمليات ذكي يوحد إدارة الأملاك، المالية، والمشاريع في مكان واحد، بهدف تحقيق أعلى مستويات الكفاءة والوضوح.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card feature-card-visual h-100 p-4">
            <div class="card-body text-center">
                <img src="https://www.svgrepo.com/show/494958/data-report-research-analysis.svg" class="illustration" alt="Our Mission">
                <h4 class="fw-bold">رسالتنا</h4>
                <p class="text-muted">تمكين أصحاب القرار بأدوات سريعة، مرنة، وآمنة تمنحهم رؤية شاملة وتحليلات دقيقة لكل جوانب عملياتهم التشغيلية والمالية.</p>
            </div>
        </div>
    </div>
</div>

<!-- 3. الخطط المستقبلية -->
<div class="card feature-card-visual">
    <div class="card-body p-4">
        <h4 class="text-center fw-bold mb-4">خارطة الطريق المستقبلية</h4>
        <div class="future-plans-list">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex align-items-center"><i class="ri-database-2-line list-icon me-3"></i><div><strong>بناء مركز مالي متكامل</strong><p class="text-muted mb-0 small">يشمل الحسابات البنكية، الشيكات، والفواتير.</p></div></li>
                <li class="list-group-item d-flex align-items-center"><i class="ri-building-4-line list-icon me-3"></i><div><strong>إدارة متقدمة للمرافق</strong><p class="text-muted mb-0 small">لتتبع العدادات، المصاعد، وأوامر عمل الصيانة.</p></div></li>
                <li class="list-group-item d-flex align-items-center"><i class="ri-robot-2-line list-icon me-3"></i><div><strong>تطوير أنظمة ذكية</strong><p class="text-muted mb-0 small">دمج مساعد AI للتحليل، والتكامل مع خدمات خارجية.</p></div></li>
            </ul>
        </div>
    </div>
</div>

<!-- 4. المطور -->
<div class="creator-section">
    <img src="https://www.svgrepo.com/show/382100/developer-development-programming-software.svg" alt="Developer" class="creator-avatar">
    <p class="text-white-50 mb-1">تم التطوير بشغف بواسطة</p>
    <h4 class="mb-0 text-white">ناجي قاسم</h4>
</div>