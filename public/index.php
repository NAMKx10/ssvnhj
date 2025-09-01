<?php
// public/index.php (النسخة النهائية الذكية)

session_start();
define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/app/Config/database.php';
require_once ROOT_PATH . '/app/Helpers/functions.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// جدار الحماية يعمل فقط على طلبات الصفحات الكاملة
if (!isset($_GET['view_only'])) {
    require_once ROOT_PATH . '/app/Config/firewall.php';
}

$routes = require_once ROOT_PATH . '/app/Config/routes.php';

if (isset($routes[$page])) {
    $controller_path = ROOT_PATH . '/app/' . $routes[$page];
    
    if (file_exists($controller_path)) {
        
        // --- المنطق الجديد يبدأ هنا ---
        
        // إذا كان الطلب لمحتوى نافذة منبثقة فقط...
        if (isset($_GET['view_only'])) {
            // ...فقط قم بتضمين ملف الواجهة/المتحكم واخرج
            require_once $controller_path;
            exit();
        }

        // --- إذا كان الطلب لصفحة كاملة (المنطق القديم) ---
        $public_pages = ['login', 'handle_login'];
        $is_public_page = in_array($page, $public_pages);

        ob_start();
        require_once $controller_path;
        $page_content = ob_get_clean();

        if ($is_public_page) {
            require_once ROOT_PATH . '/app/Common/Views/layouts/public_layout.php';
        } else {
            require_once ROOT_PATH . '/app/Common/Views/layout.php';
        }
        // --- المنطق الجديد ينتهي هنا ---

    } else {
        http_response_code(404);
        echo "Error 404: Controller file not found.";
    }
} else {
    http_response_code(404);
    echo "Error 404: Page not defined in routes.";
}