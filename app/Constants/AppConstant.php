<?php

namespace App\Constants;

class AppConstant
{
    // Roles
    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ADMIN      = 'admin';
    public const ROLE_OWNER      = 'owner';
    public const ROLE_EMPLOYEE   = 'employee';

    // Permissions
    public const PERM_VIEW_DASHBOARD      = 'view_dashboard';
    public const PERM_VIEW_INFRASTRUCTURE = 'view_infrastructure';
    public const PERM_VIEW_DIAGNOSTICS    = 'view_diagnostics';
    public const PERM_MANAGE_PERMISSIONS  = 'manage_permissions';
    public const PERM_VIEW_USERS          = 'view_users';
    public const PERM_DELETE_USERS        = 'delete_users';
    public const PERM_CREATE_INVITATIONS  = 'create_invitations';
    public const PERM_VIEW_SETTINGS       = 'view_settings';
    public const PERM_MANAGE_REQUESTS    = 'manage_requests';

    // Pagination
    public const DEFAULT_PAGINATION = 10;

    // Performance
    public const SLOW_THRESHOLD_MS   = 500;
    public const PERFORMANCE_LOG_KEY = 'performance:slow_requests';
}
