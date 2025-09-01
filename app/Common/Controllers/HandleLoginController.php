<?php
// app/Common/Controllers/HandleLoginController.php (النسخة النهائية - نص عادي)

global $pdo;

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active' AND deleted_at IS NULL");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && $password === $user['password']) {
    // نجح تسجيل الدخول
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['full_name'];
    $_SESSION['role_id'] = $user['role_id'];

    // أعد التوجيه إلى لوحة التحكم
    header("Location: index.php?page=dashboard");
    exit();

} else {
    // فشل تسجيل الدخول
    $_SESSION['login_error'] = "اسم المستخدم أو كلمة المرور غير صحيحة.";
    header("Location: index.php?page=login");
    exit();
}