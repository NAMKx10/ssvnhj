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

    // --- ▼▼▼ الكود الجديد يبدأ هنا ▼▼▼ ---

    // جلب اسم الدور وتخزينه في الجلسة
    $role_stmt = $pdo->prepare("SELECT role_name FROM roles WHERE id = ?");
    $role_stmt->execute([$user['role_id']]);
    $role = $role_stmt->fetch();
    $_SESSION['role_name'] = $role['role_name'] ?? 'Undefined';

    // جلب كل الصلاحيات المرتبطة بهذا الدور
    $permissions_stmt = $pdo->prepare("
        SELECT p.permission_key
        FROM role_permissions rp
        JOIN permissions p ON rp.permission_id = p.id
        WHERE rp.role_id = ?
    ");
    $permissions_stmt->execute([$user['role_id']]);
    
    // تخزين الصلاحيات في مصفوفة داخل الجلسة
    $permissions = $permissions_stmt->fetchAll(PDO::FETCH_COLUMN);
    $_SESSION['user_permissions'] = $permissions;

    // --- ▲▲▲ الكود الجديد ينتهي هنا ▲▲▲ ---

    // أعد التوجيه إلى لوحة التحكم
    header("Location: index.php?page=dashboard");
    exit();


}

    else {
        // فشل تسجيل الدخول
        $_SESSION['login_error'] = "اسم المستخدم أو كلمة المرور غير صحيحة.";
        header("Location: index.php?page=login");
        exit();
    }