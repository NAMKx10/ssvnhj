<?php
// public/index.php (النسخة المطهرة)
session_start();
define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/app/Config/database.php';
require_once ROOT_PATH . '/app/Helpers/functions.php';

$page = $_GET['page'] ?? 'dashboard';

$is_handler = str_starts_with($page, 'handle_');
$is_view_only = isset($_GET['view_only']);

if (!$is_view_only && !$is_handler) {
    require_once ROOT_PATH . '/app/Config/firewall.php';
}

$routes = require_once ROOT_PATH . '/app/Config/routes.php';

if (isset($routes[$page])) {
    $controller_path = ROOT_PATH . '/app/' . $routes[$page];
    if (file_exists($controller_path)) {
        if ($is_handler || $is_view_only) {
            require_once $controller_path;
            exit();
        }
        $public_pages = ['login'];
        $is_public_page = in_array($page, $public_pages);
        ob_start();
        require_once $controller_path;
        $page_content = ob_get_clean();
        $layout = $is_public_page ? '/app/Common/Views/layouts/public_layout.php' : '/app/Common/Views/layout.php';
        require_once ROOT_PATH . $layout;
    } else {
        http_response_code(404); exit("Error 404: Controller file not found.");
    }
} else {
    http_response_code(404); exit("Error 404: Page not defined in routes.");
}