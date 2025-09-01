<?php
// app/Config/firewall.php (النسخة الآمنة والنهائية)

global $page;


if (!isset($_SESSION['user_id'])) {
    if ($page !== 'login' && $page !== 'handle_login') {
        header('Location: index.php?page=login');
        exit();
    }
} else {
    if ($page === 'login' || $page === 'handle_login') {
        header('Location: index.php?page=dashboard');
        exit();
    }
}