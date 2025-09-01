<?php
// app/Common/Controllers/LogoutController.php

// بدء الجلسة للتأكد من أننا نتعامل معها
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// إلغاء كل متغيرات الجلسة
$_SESSION = [];

// تدمير الجلسة
session_destroy();

// إعادة التوجيه إلى شاشة الدخول
header("Location: index.php?page=login");
exit();