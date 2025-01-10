<?php
class Permissions {
    private static $roles = [
        'admin' => [
            'manage_users',
            'manage_products',
            'manage_orders',
            'view_reports',
            'manage_settings'
        ],
        'user' => [
            'view_products',
            'place_orders',
            'manage_profile',
            'write_reviews'
        ],
        'guest' => [
            'view_products'
        ]
    ];

    public static function hasPermission($permission) {
        if (!isset($_SESSION['user_role'])) {
            $_SESSION['user_role'] = 'guest';
        }
        
        return in_array($permission, self::$roles[$_SESSION['user_role']]);
    }

    public static function requirePermission($permission) {
        if (!self::hasPermission($permission)) {
            header('HTTP/1.0 403 Forbidden');
            die('Access Denied');
        }
    }
} 