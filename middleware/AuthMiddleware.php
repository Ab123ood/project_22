<?php
/**
 * Authentication Middleware
 * Handles user authentication and authorization
 */
class AuthMiddleware {
    
    /**
     * Ensure session is started
     * @return void
     */
    public static function ensureSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Require user to be logged in
     * @param string $basePath Application base path
     * @return void
     */
    public static function requireLogin(string $basePath): void {
        self::ensureSession();
        
        if (empty($_SESSION['user_id'])) {
            self::redirect($basePath . '/auth');
        }
    }

    /**
     * Require specific role(s)
     * @param array<int> $allowedRoleIds Array of allowed role IDs
     * @param string $basePath Application base path
     * @return void
     */
    public static function requireRole(array $allowedRoleIds, string $basePath): void {
        self::ensureSession();
        
        $roleId = (int)($_SESSION['role_id'] ?? 0);
        
        if ($roleId === 0 || (!empty($allowedRoleIds) && !in_array($roleId, $allowedRoleIds, true))) {
            self::forbiddenResponse();
        }
    }

    /**
     * Check if user is logged in
     * @return bool
     */
    public static function isLoggedIn(): bool {
        self::ensureSession();
        return !empty($_SESSION['user_id']);
    }

    /**
     * Get current user ID
     * @return int|null
     */
    public static function getCurrentUserId(): ?int {
        self::ensureSession();
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    /**
     * Get current user role
     * @return int|null
     */
    public static function getCurrentUserRole(): ?int {
        self::ensureSession();
        return isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : null;
    }

    /**
     * Check if user has specific role
     * @param int $roleId Role ID to check
     * @return bool
     */
    public static function hasRole(int $roleId): bool {
        return self::getCurrentUserRole() === $roleId;
    }

    /**
     * Check if user is admin
     * @return bool
     */
    public static function isAdmin(): bool {
        return self::hasRole(3);
    }

    /**
     * Check if user is manager or admin
     * @return bool
     */
    public static function isManagerOrAdmin(): bool {
        $role = self::getCurrentUserRole();
        return in_array($role, [2, 3], true);
    }

    /**
     * Redirect to URL
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code
     * @return void
     */
    private static function redirect(string $url, int $statusCode = 302): void {
        header("Location: $url", true, $statusCode);
        exit;
    }

    /**
     * Send forbidden response
     * @return void
     */
    private static function forbiddenResponse(): void {
        http_response_code(403);
        
        // Check if it's an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Access forbidden'], JSON_UNESCAPED_UNICODE);
        } else {
            echo '403 - Access Forbidden';
        }
        exit;
    }
}


