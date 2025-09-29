<?php
class UsersController extends Controller {

    public function index(): void {
        $pageTitle = 'إدارة المستخدمين';

        $sql = "
            SELECT 
                u.id, u.user_name as name, u.email, u.status, u.created_at, u.updated_at,
                r.name AS role
            FROM users u
            LEFT JOIN roles r ON r.id = u.role_id
            ORDER BY u.created_at DESC
            LIMIT 50
        ";
        try {
            $users = Database::query($sql)->fetchAll();
        } catch (Throwable $e) {
            $users = [];
        }

        $counts = [
            'total' => 0,
            'active' => 0,
            'banned' => 0,
            'new30' => 0,
        ];
        try {
            $counts['total'] = (int) Database::query("SELECT COUNT(*) c FROM users")->fetch()['c'] ?? 0;
            $counts['active'] = (int) Database::query("SELECT COUNT(*) c FROM users WHERE status='active'")->fetch()['c'] ?? 0;
            $counts['banned'] = (int) Database::query("SELECT COUNT(*) c FROM users WHERE status='banned'")->fetch()['c'] ?? 0;
            $counts['new30'] = (int) Database::query("SELECT COUNT(*) c FROM users WHERE created_at >= (NOW() - INTERVAL 30 DAY)")->fetch()['c'] ?? 0;
        } catch (Throwable $e) {}

        $this->render('admin/users/list', compact('pageTitle', 'users', 'counts'));
    }

    public function create(): void {
        $pageTitle = 'إضافة مستخدم';
        try {
            $roles = Database::query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
        } catch (Throwable $e) {
            $roles = [];
        }
        $this->render('admin/users/add-user', compact('pageTitle', 'roles'));
    }

    public function store(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . $this->basePath() . '/admin/users/add');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : null;
        $status = in_array($_POST['status'] ?? 'active', ['active','inactive','pending','banned']) ? $_POST['status'] : 'active';
        $password = trim($_POST['password'] ?? '');
        $force_reset = isset($_POST['force_reset']) ? 1 : 0;

        $errors = [];
        if ($name === '') $errors[] = 'الاسم مطلوب';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'البريد الإلكتروني غير صالح';
        if (empty($role_id)) $errors[] = ' role_id الدور مطلوب';
        if ($password === '' || strlen($password) < 6) $errors[] = 'كلمة المرور مطلوبة (6 أحرف على الأقل)';

        if (!empty($errors)) {
            try {
                $roles = Database::query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
            } catch (Throwable $e) {
                $roles = [];
            }
            $pageTitle = 'إضافة مستخدم';
            $old = compact('name','email','role_id','status','force_reset');
            $this->render('admin/users/add-user', compact('pageTitle','roles','errors','old'));
            return;
        }

        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (user_name, email, password_hash, role_id, status, force_password_reset, created_at)
                    VALUES (:name, :email, :password_hash, :role_id, :status, :force_reset, NOW())";
            Database::query($sql, [
                ':name' => $name,
                ':email' => $email,
                ':password_hash' => $hash,
                ':role_id' => $role_id,
                ':status' => $status,
                ':force_reset' => $force_reset,
            ]);
            header('Location: ' . $this->basePath() . '/admin/users');
            return;
        } catch (Throwable $e) {
            $errors = ['فشل حفظ المستخدم. تأكد من عدم تكرار البريد.'];
            try {
                $roles = Database::query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
            } catch (Throwable $e2) {
                $roles = [];
            }
            $pageTitle = 'إضافة مستخدم';
            $old = compact('name','email','role_id','status','force_reset');
            $this->render('admin/users/add-user', compact('pageTitle','roles','errors','old'));
        }
    }

    public function view(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . $this->basePath() . '/admin/users');
            return;
        }

        $pageTitle = 'عرض المستخدم';
        
        try {
            $sql = "
                SELECT 
                    u.id, u.user_name as name, u.email, u.status, u.created_at, u.updated_at,
                    u.last_login, u.force_password_reset, u.role_id,
                    r.name AS role_name
                FROM users u
                LEFT JOIN roles r ON r.id = u.role_id
                WHERE u.id = :id
            ";
            $user = Database::query($sql, [':id' => $id])->fetch();
            
            if (!$user) {
                header('Location: ' . $this->basePath() . '/admin/users');
                return;
            }

            $stats = [
                'exams_taken' => 0,
                'surveys_completed' => 0,
                'content_viewed' => 0,
                'total_points' => 0,
            ];

            try {
                $stats['exams_taken'] = (int) Database::query(
                    "SELECT COUNT(*) c FROM exam_attempts WHERE user_id = :uid AND status = 'completed'", 
                    [':uid' => $id]
                )->fetch()['c'] ?? 0;

                $stats['surveys_completed'] = (int) Database::query(
                    "SELECT COUNT(*) c FROM survey_responses WHERE user_id = :uid AND status = 'completed'", 
                    [':uid' => $id]
                )->fetch()['c'] ?? 0;

                $stats['content_viewed'] = (int) Database::query(
                    "SELECT COUNT(*) c FROM content_views WHERE user_id = :uid", 
                    [':uid' => $id]
                )->fetch()['c'] ?? 0;

                $stats['total_points'] = (int) Database::query(
                    "SELECT COALESCE(SUM(points), 0) total FROM points_log WHERE user_id = :uid", 
                    [':uid' => $id]
                )->fetch()['total'] ?? 0;
            } catch (Throwable $e) {}

        } catch (Throwable $e) {
            header('Location: ' . $this->basePath() . '/admin/users');
            return;
        }

        $this->render('admin/users/view', compact('pageTitle', 'user', 'stats'));
    }

    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . $this->basePath() . '/admin/users');
            return;
        }

        $pageTitle = 'تعديل المستخدم';
        
        try {
            $sql = "
                SELECT 
                    u.id, u.user_name as name, u.email, u.status, u.role_id, u.force_password_reset
                FROM users u
                WHERE u.id = :id
            ";
            $user = Database::query($sql, [':id' => $id])->fetch();
            
            if (!$user) {
                header('Location: ' . $this->basePath() . '/admin/users');
                return;
            }

            $roles = Database::query("SELECT id, name FROM roles ORDER BY name")->fetchAll();

        } catch (Throwable $e) {
            header('Location: ' . $this->basePath() . '/admin/users');
            return;
        }

        $this->render('admin/users/edit', compact('pageTitle', 'user', 'roles'));
    }

    public function update(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . $this->basePath() . '/admin/users');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . $this->basePath() . '/admin/users');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : null;
        $status = in_array($_POST['status'] ?? 'active', ['active','inactive','pending','banned']) ? $_POST['status'] : 'active';
        $password = trim($_POST['password'] ?? '');
        $force_reset = isset($_POST['force_reset']) ? 1 : 0;

        $errors = [];
        if ($name === '') $errors[] = 'الاسم مطلوب';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'البريد الإلكتروني غير صالح';
        if (empty($role_id)) $errors[] = 'الدور مطلوب';

        if (!empty($errors)) {
            $roles = Database::query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
            $user = ['id' => $id, 'name' => $name, 'email' => $email, 'role_id' => $role_id, 'status' => $status, 'force_password_reset' => $force_reset];
            $pageTitle = 'تعديل المستخدم';
            $this->render('admin/users/edit', compact('pageTitle','roles','errors','user'));
            return;
        }

        try {
            $updateFields = [
                'user_name = :name',
                'email = :email', 
                'role_id = :role_id',
                'status = :status',
                'force_password_reset = :force_reset',
                'updated_at = NOW()'
            ];
            
            $params = [
                ':id' => $id,
                ':name' => $name,
                ':email' => $email,
                ':role_id' => $role_id,
                ':status' => $status,
                ':force_reset' => $force_reset,
            ];

            if ($password !== '') {
                if (strlen($password) < 6) {
                    $errors[] = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
                } else {
                    $updateFields[] = 'password_hash = :password_hash';
                    $params[':password_hash'] = password_hash($password, PASSWORD_BCRYPT);
                }
            }

            if (!empty($errors)) {
                $roles = Database::query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
                $user = ['id' => $id, 'name' => $name, 'email' => $email, 'role_id' => $role_id, 'status' => $status, 'force_password_reset' => $force_reset];
                $pageTitle = 'تعديل المستخدم';
                $this->render('admin/users/edit', compact('pageTitle','roles','errors','user'));
                return;
            }

            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";
            Database::query($sql, $params);
            
            header('Location: ' . $this->basePath() . '/admin/users');
            return;
            
        } catch (Throwable $e) {
            $errors = ['فشل تحديث المستخدم. تأكد من عدم تكرار البريد.'];
            $roles = Database::query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
            $user = ['id' => $id, 'name' => $name, 'email' => $email, 'role_id' => $role_id, 'status' => $status, 'force_password_reset' => $force_reset];
            $pageTitle = 'تعديل المستخدم';
            $this->render('admin/users/edit', compact('pageTitle','roles','errors','user'));
        }
    }

    public function delete(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'طريقة طلب غير صالحة']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'معرف المستخدم غير صالح']);
            return;
        }

        try {
            $user = Database::query("SELECT id, user_name FROM users WHERE id = :id", [':id' => $id])->fetch();
            if (!$user) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'المستخدم غير موجود']);
                return;
            }

            if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'لا يمكن حذف المستخدم الحالي']);
                return;
            }

            Database::query("DELETE FROM users WHERE id = :id", [':id' => $id]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'تم حذف المستخدم بنجاح']);
            
        } catch (Throwable $e) {
            error_log('UsersController::delete error: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'فشل في حذف المستخدم']);
        }
    }
}
