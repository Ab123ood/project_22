<?php
/**
 * Base Controller Class
 * Provides common functionality for all controllers
 */
class Controller {
    
    /**
     * Get the base path of the application
     * @return string
     */
    protected function basePath(): string {
        $bp = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        return ($bp === '/' || $bp === '\\') ? '' : $bp;
    }

    /**
     * Start session if not already started
     * @return void
     */
    protected function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Require user to be logged in
     * @return void
     */
    protected function requireLogin(): void {
        $this->startSession();
        $basePath = $this->basePath();
        
        if (empty($_SESSION['user_id'])) {
            $this->redirect($basePath . '/auth');
        }
    }

    /**
     * Require specific role
     * @param array $allowedRoles Array of allowed role IDs
     * @return void
     */
    protected function requireRole(array $allowedRoles): void {
        $this->requireLogin();
        
        $userRole = (int)($_SESSION['role_id'] ?? 0);
        if (!in_array($userRole, $allowedRoles, true)) {
            $this->redirect($this->basePath() . '/auth');
        }
    }

    /**
     * Render a view with data
     * @param string $view View file path
     * @param array $data Data to pass to the view
     * @return void
     */
    protected function render(string $view, array $data = []): void {
        $viewFile = VIEW_PATH . '/' . trim($view, '/');
        if (!str_ends_with($viewFile, '.php')) {
            $viewFile .= '.php';
        }

        if (!file_exists($viewFile)) {
            $this->errorResponse(500, 'View not found: ' . $view);
            return;
        }

        $localization = $this->bootLocalization($data['locale'] ?? null);

        $viewData = $data;
        $viewData['localization'] = $localization;
        $viewData['locale'] = $localization->getLocale();
        $viewData['lang'] = [
            'code' => $localization->getLocale(),
            'dir' => $localization->getDirection(),
            'name' => $localization->getLanguageName(),
        ];
        $viewData['isRtl'] = $localization->isRtl();
        $viewData['availableLocales'] = Localization::getSupportedLocales();

        // Extract data for the view
        extract($viewData, EXTR_SKIP);
        
        // Capture view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render with layout
        require VIEW_PATH . '/layout/layout.php';
    }

    /**
     * Determine the preferred locale for the current request
     * @param string|null $preferred Preferred locale passed by the caller
     * @return string
     */
    protected function determineLocale(?string $preferred = null): string {
        $this->startSession();

        $supportedLocales = Localization::getSupportedLocales();
        $configuredLocale = $GLOBALS['config']['app']['locale'] ?? 'en';

        $candidates = [
            $preferred,
            $_SESSION['locale'] ?? null,
            $_COOKIE['locale'] ?? null,
            $configuredLocale,
        ];

        foreach ($candidates as $candidate) {
            if (!is_string($candidate) || $candidate === '') {
                continue;
            }

            $normalized = strtolower(trim($candidate));
            if (array_key_exists($normalized, $supportedLocales)) {
                return $normalized;
            }
        }

        return array_key_first($supportedLocales) ?? 'en';
    }

    /**
     * Boot the localization service for the current request lifecycle
     * @param string|null $preferred Preferred locale passed by the caller
     * @return Localization
     */
    protected function bootLocalization(?string $preferred = null): Localization {
        $this->startSession();

        $locale = $this->determineLocale($preferred);
        $localization = new Localization($locale);
        Localization::setInstance($localization);

        $_SESSION['locale'] = $localization->getLocale();

        if (!headers_sent()) {
            $cookiePath = $this->basePath() ?: '/';
            setcookie('locale', $localization->getLocale(), time() + (60 * 60 * 24 * 30), $cookiePath, '', !IS_DEVELOPMENT, true);
        }

        return $localization;
    }

    /**
     * Redirect to a URL
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code
     * @return void
     */
    protected function redirect(string $url, int $statusCode = 302): void {
        header("Location: $url", true, $statusCode);
        exit;
    }

    /**
     * Send JSON response
     * @param mixed $data Data to encode as JSON
     * @param int $statusCode HTTP status code
     * @return void
     */
    protected function jsonResponse($data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send error response
     * @param int $statusCode HTTP status code
     * @param string $message Error message
     * @return void
     */
    protected function errorResponse(int $statusCode, string $message = ''): void {
        http_response_code($statusCode);
        
        if ($this->isAjaxRequest()) {
            $this->jsonResponse(['error' => $message], $statusCode);
        } else {
            echo $message ?: 'An error occurred';
        }
        exit;
    }

    /**
     * Check if request is AJAX
     * @return bool
     */
    protected function isAjaxRequest(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get POST data safely
     * @param string $key Key to retrieve
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    protected function getPost(string $key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data safely
     * @param string $key Key to retrieve
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    protected function getGet(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    /**
     * Validate CSRF token
     * @param string $token Token to validate
     * @return bool
     */
    protected function validateCsrfToken(string $token): bool {
        $this->startSession();
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate CSRF token
     * @return string
     */
    protected function generateCsrfToken(): string {
        $this->startSession();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
