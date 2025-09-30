<?php
/**
 * Authentication Controller
 * Handles user authentication, registration, and logout
 */
class AuthController extends Controller {
    
    /**
     * Show authentication page
     * @return void
     */
    public function show(): void {
        $this->startSession();

        // Check if user is already logged in
        if (!empty($_SESSION['user_id'])) {
            $this->redirect($this->basePath() . '/');
            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        $this->render('auth', [
            'flash' => $flash,
        ]);
    }

    /**
     * Handle user login
     * @return void
     */
    public function login(): void {
        $this->startSession();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->basePath() . '/auth');
            return;
        }
        
        $email = trim($this->getPost('email', ''));
        $password = $this->getPost('password', '');
        
        // Validate input
        if (empty($email) || empty($password)) {
            $_SESSION['flash'] = ['error' => ['key' => 'auth.flash.missing_credentials']];
            $this->redirect($this->basePath() . '/auth');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['error' => ['key' => 'auth.flash.invalid_email']];
            $this->redirect($this->basePath() . '/auth');
            return;
        }
        
        try {
            // Get user data
            $user = Database::query(
                'SELECT id, email, password_hash, role_id, user_name FROM users WHERE email = :email AND status = "active" LIMIT 1',
                [':email' => $email]
            )->fetch();
            
            if (!$user) {
                $_SESSION['flash'] = ['error' => ['key' => 'auth.flash.invalid_credentials']];
                $this->redirect($this->basePath() . '/auth');
                return;
            }

            // Verify password
            if (!isset($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
                $_SESSION['flash'] = ['error' => ['key' => 'auth.flash.invalid_credentials']];
                $this->redirect($this->basePath() . '/auth');
                return;
            }
            
            // Login successful
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['role_id'] = (int)$user['role_id'];
            
            // Update last login
            Database::query(
                'UPDATE users SET last_login = NOW(), updated_at = NOW() WHERE id = :id',
                [':id' => $user['id']]
            );
            
            $this->redirect($this->basePath() . '/');
            
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            $_SESSION['flash'] = ['error' => ['key' => 'auth.flash.generic_error']];
            $this->redirect($this->basePath() . '/auth');
        }
    }

    /**
     * Handle user registration
     * @return void
     */
    public function register(): void {
        $this->startSession();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->basePath() . '/auth');
            return;
        }
        
        $name = trim($this->getPost('name', ''));
        $email = trim($this->getPost('email', ''));
        $password = $this->getPost('password', '');
        $confirmPassword = $this->getPost('confirm_password', '');
        
        // Validate input
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            $_SESSION['flash'] = ['error' => ['key' => 'auth.flash.missing_fields']];
            $this->redirect($this->basePath() . '/auth');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['error' => ['key' => 'auth.flash.invalid_email']];
            $this->redirect($this->basePath() . '/auth');
            return;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['flash'] = ['error' => ['key' => 'auth.flash.password_mismatch']];
            $this->redirect($this->basePath() . '/auth');
            return;
        }

        $minPasswordLength = $GLOBALS['config']['security']['password_min_length'] ?? 8;
        if (strlen($password) < $minPasswordLength) {
            $_SESSION['flash'] = ['error' => ['key' => 'auth.flash.password_length', 'replace' => ['min' => $minPasswordLength]]];
            $this->redirect($this->basePath() . '/auth');
            return;
        }
        
        try {
            // Check if email already exists
            $existingUser = Database::query(
                'SELECT id FROM users WHERE email = :email LIMIT 1',
                [':email' => $email]
            )->fetch();
            
            if ($existingUser) {
                $_SESSION['flash'] = ['error' => ['key' => 'auth.flash.email_exists']];
                $this->redirect($this->basePath() . '/auth');
                return;
            }
            
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // Create new user
            Database::query(
                'INSERT INTO users (email, user_name, role_id, status, password_hash, created_at, updated_at) 
                 VALUES (:email, :name, :role, :status, :password_hash, NOW(), NOW())',
                [
                    ':email' => $email,
                    ':name' => $name,
                    ':role' => 1, // Default role: Employee
                    ':status' => 'active',
                    ':password_hash' => $passwordHash
                ]
            );
            
            $_SESSION['flash'] = ['success' => ['key' => 'auth.flash.register_success']];

        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            $_SESSION['flash'] = ['error' => ['key' => 'auth.flash.generic_error']];
        }

        $this->redirect($this->basePath() . '/auth');
    }

    /**
     * Handle user logout
     * @return void
     */
    public function logout(): void {
        $this->startSession();
        
        // Clear session data
        $_SESSION = [];
        
        // Clear session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000, 
                $params['path'], 
                $params['domain'], 
                $params['secure'], 
                $params['httponly']
            );
        }
        
        // Destroy session
        session_destroy();
        
        $this->redirect($this->basePath() . '/auth');
    }
}
