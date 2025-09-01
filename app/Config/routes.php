<?php
// app/Config/routes.php

// هذه المصفوفة تربط كل 'page' في الرابط بالملف المسؤول عن معالجتها
return [
    'dashboard' => 'Common/Controllers/DashboardController.php',
    
    'login' => 'Common/Controllers/AuthController.php',
    'handle_login' => 'Common/Controllers/HandleLoginController.php',
    'logout' => 'Common/Controllers/LogoutController.php',

    'users' => 'Common/Controllers/UsersController.php',
    'users/add' => 'Common/Views/users/add_view.php',
    'users/edit' => 'Common/Views/users/edit_view.php',
    'handle_user_delete' => 'Common/Controllers/UserHandlerController.php',
    'handle_users_batch_action' => 'Common/Controllers/UserHandlerController.php',
    'handle_user_add' => 'Common/Controllers/UserHandlerController.php',
    'handle_user_edit' => 'Common/Controllers/UserHandlerController.php',
    'users/batch_add' => 'Common/Controllers/UsersBatchController.php',
    'handle_users_batch_add' => 'Common/Controllers/UserHandlerController.php',
    'users/batch_edit' => 'Common/Controllers/UsersBatchController.php',
    'handle_users_batch_edit' => 'Common/Controllers/UserHandlerController.php',
    
    // سنضيف بقية المسارات هنا لاحقًا
    // 'users' => 'Modules/Users/Controllers/UserController.php',
    // 'users/create' => 'Modules/Users/Controllers/UserCreateController.php',
];