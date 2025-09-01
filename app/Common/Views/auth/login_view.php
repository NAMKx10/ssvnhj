<?php
// app/Common/Views/auth/login_view.php
?>
<div class="row g-0 flex-fill">
    <div class="col-12 col-lg-6 col-xl-4 border-top-wide border-primary d-flex flex-column justify-content-center">
        <div class="container container-tight my-5 px-lg-5">
            <div class="text-center mb-4">
                <!-- يمكنك وضع الشعار هنا لاحقًا -->
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="./assets/static/logo.svg" height="36" alt="">
                </a>
            </div>
            <h2 class="h3 text-center mb-3">تسجيل الدخول إلى حسابك</h2>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['login_error']; ?>
                    <?php unset($_SESSION['login_error']); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=handle_login" method="post" autocomplete="off" novalidate>
                <div class="mb-3">
                    <label class="form-label">اسم المستخدم</label>
                    <input type="text" name="username" class="form-control" placeholder="أدخل اسم المستخدم" autocomplete="off" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">كلمة المرور</label>
                    <div class="input-group input-group-flat">
                        <input type="password" name="password" class="form-control" placeholder="كلمة المرور" autocomplete="off" required>
                    </div>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block">
        <!-- صورة الغلاف -->
        <div class="bg-cover h-100 min-vh-100" style="background-image: url(./assets/static/photos/home-office-laptop-organizer-and-cup-of-coffee.jpg)"></div>
    </div>
</div>