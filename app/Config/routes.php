<?php
// app/Config/routes.php

// هذه المصفوفة تربط كل 'page' في الرابط بالملف المسؤول عن معالجتها
return [
    'dashboard' => 'Common/Controllers/DashboardController.php',
    
    'login' => 'Common/Controllers/AuthController.php',
    'handle_login' => 'Common/Controllers/HandleLoginController.php',
    'logout' => 'Common/Controllers/LogoutController.php',

    'archive' => 'Common/Controllers/ArchiveController.php',

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

    'roles' => 'Common/Controllers/RolesController.php',
    'roles/add' => 'Common/Views/roles/add_view.php',
    'roles/edit' => 'Common/Controllers/RoleEditController.php',
    'handle_role_add' => 'Common/Controllers/RoleHandlerController.php',
    'handle_role_edit' => 'Common/Controllers/RoleHandlerController.php',
    'roles/edit_role_view' => 'Common/Views/roles/edit_role_view.php',
    'handle_role_edit_details' => 'Common/Controllers/RoleHandlerController.php',
    'handle_role_delete' => 'Common/Controllers/RoleHandlerController.php',

    // --- موديل الفروع ---
    'branches' => 'Modules/Branches/Controllers/BranchesController.php',
    'branches/add' => 'Modules/Branches/Views/add_view.php',
    'branches/edit' => 'Modules/Branches/Views/edit_view.php',
    'branches/batch_add' => 'Modules/Branches/Controllers/BranchesBatchController.php',
    'branches/batch_edit' => 'Modules/Branches/Controllers/BranchesBatchController.php',
    'handle_branch_actions' => 'Modules/Branches/Controllers/BranchHandlerController.php',

        // --- موديل التقارير ---
    'reports' => 'Modules/Reports/Controllers/ReportsController.php',
    'settings/print_templates' => 'Common/Controllers/PrintTemplatesController.php',
    'handle_print_template_update' => 'Common/Controllers/PrintTemplateHandlerController.php',

        // --- موديل الوثائق والمستندات ---
    'documents' => 'Modules/Documents/Controllers/DocumentsController.php',
    'documents/add' => 'Modules/Documents/Views/add_view.php',
    'documents/edit' => 'Modules/Documents/Views/edit_view.php',
    'handle_document_actions' => 'Modules/Documents/Controllers/DocumentHandlerController.php',
    'ajax_get_targets' => 'Modules/Documents/Controllers/AjaxController.php',

    'permissions' => 'Common/Controllers/PermissionsController.php',
    'permissions/add_group' => 'Common/Views/permissions/add_group_view.php',
    'permissions/edit_group' => 'Common/Views/permissions/edit_group_view.php',
    'permissions/add' => 'Common/Views/permissions/add_permission_view.php',
    'permissions/edit' => 'Common/Views/permissions/edit_permission_view.php',
    'handle_permission_actions' => 'Common/Controllers/PermissionHandlerController.php',

        // --- موديل جهات الاتصال ---
    'contacts' => 'Modules/Contacts/Controllers/ContactsController.php',
    'contacts/add' => 'Modules/Contacts/Views/add_view.php',
    'contacts/edit' => 'Modules/Contacts/Views/edit_view.php',
    'contacts/batch_add' => 'Modules/Contacts/Controllers/ContactsBatchController.php',
    'contacts/batch_edit' => 'Modules/Contacts/Controllers/ContactsBatchController.php',
    'handle_contact_actions' => 'Modules/Contacts/Controllers/ContactHandlerController.php',

        // --- موديل تهيئة المدخلات ---
    'settings' => 'Common/Controllers/SettingsController.php',
    'settings/add_group' => 'Common/Views/settings/add_group_view.php',
    'settings/edit_group' => 'Common/Views/settings/edit_group_view.php',
    'settings/add_option' => 'Common/Views/settings/add_option_view.php',
    'settings/edit_option' => 'Common/Views/settings/edit_option_view.php',
    'handle_settings_actions' => 'Common/Controllers/SettingsHandlerController.php',

        // --- مدير الملفات ---
    'file-manager' => 'Modules/FileManager/Controllers/FileManagerController.php',
    'file-manager/add_folder' => 'Modules/FileManager/Views/add_folder_view.php',
    'file-manager/upload_file' => 'Modules/FileManager/Views/upload_file_view.php',
    'file-manager/rename' => 'Modules/FileManager/Views/rename_view.php',
    'file-manager/move' => 'Modules/FileManager/Views/move_view.php',
    'handle_file_actions' => 'Modules/FileManager/Controllers/FileActionsHandler.php',

        // --- إعدادات النظام العامة ---
    'settings/general' => 'Common/Controllers/GeneralSettingsController.php',
    'handle_general_settings_update' => 'Common/Controllers/GeneralSettingsHandlerController.php',

];