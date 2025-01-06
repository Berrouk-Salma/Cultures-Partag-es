<?php
class Role {
    const ADMIN = 'admin';
    const AUTHOR = 'author';
    const USER = 'user';
    
    public static function getAllRoles() {
        return [self::ADMIN, self::AUTHOR, self::USER];
    }
    
    public static function getDashboardPath($role) {
        switch($role) {
            case self::ADMIN:
                return 'admin/dashboard.php';
            case self::AUTHOR:
                return 'author/dashboard.php';
            default:
                return 'user/profile.php';
        }
    }
}