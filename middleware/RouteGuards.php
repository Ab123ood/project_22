<?php
/**
 * Route Guards Middleware
 * Handles route-based access control and authorization
 * 
 * Role definitions:
 * 1 = Employee (موظف)
 * 2 = Awareness Manager (مسؤول توعية) 
 * 3 = Admin (أدمن)
 */
class RouteGuards {
    
    /**
     * Admin-only routes that require role 3
     */
    private const ADMIN_ONLY_ROUTES = [
        '/admin/users',
        '/admin/users/add',
        '/admin/users/view', 
        '/admin/users/edit',
        '/admin/reports',
        '/admin/settings',
        '/admin/surveys/edit',
        '/admin/surveys/update',
        '/admin/surveys/delete',
        '/admin/content/edit',
        '/admin/content/update',
        '/admin/content/delete',
        '/admin/content/create',
        '/admin/content/store',
        '/admin/exams/edit',
        '/admin/exams/update',
        '/admin/exams/delete',
        '/admin/exams/create',
        '/admin/exams/store',
    ];

    /**
     * Employee routes that require login
     */
    private const EMPLOYEE_ROUTES = [
        '/dashboard',
        '/exams',
        '/progress', 
        '/surveys',
        '/profile',
        '/leaderboard',
        '/content'
    ];

    /**
     * Dynamic routes that require login
     */
    private const DYNAMIC_ROUTES = [
        '/exams/',
        '/content/',
        '/surveys/',
        '/notifications/'
    ];

    /**
     * Enforce route-based access control
     * @param string $uri Current URI
     * @param string $basePath Application base path
     * @return void
     */
    public static function enforce(string $uri, string $basePath): void {
        // Handle admin routes
        if (str_starts_with($uri, '/admin')) {
            self::handleAdminRoutes($uri, $basePath);
        }
        
        // Handle employee routes
        if (self::isEmployeeRoute($uri)) {
            AuthMiddleware::requireLogin($basePath);
        }
        
        // Handle dashboard redirect for regular employees
        if ($uri === '/dashboard') {
            self::handleDashboardRedirect($basePath);
        }
    }

    /**
     * Handle admin route access control
     * @param string $uri Current URI
     * @param string $basePath Application base path
     * @return void
     */
    private static function handleAdminRoutes(string $uri, string $basePath): void {
        AuthMiddleware::requireLogin($basePath);
        
        if (self::isAdminOnlyRoute($uri)) {
            AuthMiddleware::requireRole([3], $basePath); // Admin only
        } else {
            AuthMiddleware::requireRole([2, 3], $basePath); // Manager or Admin
        }
    }

    /**
     * Check if route is admin-only
     * @param string $uri Current URI
     * @return bool
     */
    private static function isAdminOnlyRoute(string $uri): bool {
        foreach (self::ADMIN_ONLY_ROUTES as $route) {
            if (str_starts_with($uri, $route)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if route requires employee login
     * @param string $uri Current URI
     * @return bool
     */
    private static function isEmployeeRoute(string $uri): bool {
        // Check static employee routes
        foreach (self::EMPLOYEE_ROUTES as $route) {
            if ($uri === $route || str_starts_with($uri, $route . '/')) {
                return true;
            }
        }
        
        // Check dynamic routes
        foreach (self::DYNAMIC_ROUTES as $route) {
            if (str_starts_with($uri, $route)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Handle dashboard redirect for regular employees
     * @param string $basePath Application base path
     * @return void
     */
    private static function handleDashboardRedirect(string $basePath): void {
        AuthMiddleware::ensureSession();
        
        $roleId = AuthMiddleware::getCurrentUserRole();
        $userId = AuthMiddleware::getCurrentUserId();

        if (empty($userId)) {
            self::redirect($basePath . '/auth');
        }

        // Redirect regular employees to profile page
        if ($roleId === 1) {
            self::redirect($basePath . '/profile');
        }

        // Only allow managers and admins to access dashboard
        if (!in_array($roleId, [2, 3], true)) {
            self::redirect($basePath . '/auth');
        }
    }

    /**
     * Redirect to URL
     * @param string $url URL to redirect to
     * @return void
     */
    private static function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
}


