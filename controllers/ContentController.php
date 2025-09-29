<?php
// app/controllers/ContentController.php
class ContentController extends Controller {
    private $db;
    
    public function __construct() {
        $this->db = Database::connection();
    }
    
    
    
    public function index() {
        $userId = $_SESSION['user_id'] ?? 0;
        
        // المحتوى المميز
        $featuredQuery = "
            SELECT c.id, c.title, c.description, c.type, c.thumbnail_url AS thumbnail,
                   c.est_duration AS duration, c.view_count AS views, cc.name AS category_name,
                   COALESCE(cv.liked, 0) AS is_favorited
            FROM content c
            LEFT JOIN content_categories cc ON c.category_id = cc.id
            LEFT JOIN content_views cv ON c.id = cv.content_id AND cv.user_id = ?
            WHERE c.publish_status = 'published' AND c.is_featured = 1
            ORDER BY c.created_at DESC LIMIT 6
        ";
        $stmt = $this->db->prepare($featuredQuery);
        $stmt->execute([$userId]);
        $featuredContent = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // كل المحتوى
        $contentQuery = str_replace('c.is_featured = 1', '1=1', $featuredQuery);
        $stmt = $this->db->prepare($contentQuery);
        $stmt->execute([$userId]);
        $content = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // الإحصائيات
        $stats = ['total_content' => 0, 'viewed_content' => 0, 'favorited_content' => 0, 'earned_points' => 0];
        
        if ($userId) {
            $stats['total_content'] = $this->db->query("SELECT COUNT(*) FROM content WHERE publish_status = 'published'")->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM content_views WHERE user_id = ?");
            $stmt->execute([$userId]);
            $stats['viewed_content'] = $stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM content_views WHERE user_id = ? AND liked = 1");
            $stmt->execute([$userId]);
            $stats['favorited_content'] = $stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(points), 0) FROM points_log WHERE user_id = ? AND action_type = 'content_viewed'");
            $stmt->execute([$userId]);
            $stats['earned_points'] = $stmt->fetchColumn();
        } else {
            $stats['total_content'] = $this->db->query("SELECT COUNT(*) FROM content WHERE publish_status = 'published'")->fetchColumn();
        }
        
        $this->render('employee/content', [
            'featuredContent' => $featuredContent,
            'content' => $content,
            'stats' => $stats
        ]);
    }
    
    public function view($id) {
        if (!$id) {
            header('Location: ' . $this->basePath() . '/content');
            exit;
        }
        
        $contentQuery = "
            SELECT c.*, cc.name as category_name, cc.color as category_color, cc.icon as category_icon,
                   u.user_name as author_name,
                   CASE c.type 
                       WHEN 'video' THEN 'فيديو'
                       WHEN 'article' THEN 'مقال'
                       WHEN 'infographic' THEN 'إنفوجرافيك'
                       WHEN 'guide' THEN 'دليل'
                       ELSE c.type END as type_display,
                   CASE c.difficulty_level 
                       WHEN 'beginner' THEN 'مبتدئ'
                       WHEN 'intermediate' THEN 'متوسط'
                       WHEN 'advanced' THEN 'متقدم'
                       ELSE c.difficulty_level END as difficulty_display
            FROM content c
            LEFT JOIN content_categories cc ON c.category_id = cc.id
            LEFT JOIN users u ON c.created_by = u.id
            WHERE c.id = ? AND c.publish_status = 'published'
        ";
        $stmt = $this->db->prepare($contentQuery);
        $stmt->execute([$id]);
        $content = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$content) {
            header('Location: ' . $this->basePath() . '/content');
            exit;
        }
        
        if (isset($_SESSION['user_id'])) {
            $this->trackContentView($id, $_SESSION['user_id']);
        }
        
        // المحتوى ذو الصلة
        $relatedQuery = "
            SELECT c.*, cc.name as category_name,
                   CASE c.type 
                       WHEN 'video' THEN 'فيديو'
                       WHEN 'article' THEN 'مقال'
                       WHEN 'infographic' THEN 'إنفوجرافيك'
                       WHEN 'guide' THEN 'دليل'
                       ELSE c.type END as type_display
            FROM content c
            LEFT JOIN content_categories cc ON c.category_id = cc.id
            WHERE c.category_id = ? AND c.id != ? AND c.publish_status = 'published'
            ORDER BY c.created_at DESC LIMIT 4
        ";
        $stmt = $this->db->prepare($relatedQuery);
        $stmt->execute([$content['category_id'], $id]);
        $relatedContent = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->render('employee/content-view', [
            'content' => $content,
            'relatedContent' => $relatedContent
        ]);
    }
    
    public function like($id) {
        if (!isset($_SESSION['user_id']) || !$id) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'غير مصرح']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            // Check if user already liked this content
            $checkQuery = "SELECT liked FROM content_views WHERE user_id = ? AND content_id = ?";
            $stmt = $this->db->prepare($checkQuery);
            $stmt->execute([$userId, $id]);
            $view = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($view) {
                $newLikeStatus = (int)!$view['liked'];
                
                // Update like status
                $updateQuery = "UPDATE content_views SET liked = ? WHERE user_id = ? AND content_id = ?";
                $stmt = $this->db->prepare($updateQuery);
                $stmt->execute([$newLikeStatus, $userId, $id]);
                
                // Update content like count
                if ($newLikeStatus) {
                    $updateContentQuery = "UPDATE content SET like_count = like_count + 1 WHERE id = ?";
                } else {
                    $updateContentQuery = "UPDATE content SET like_count = like_count - 1 WHERE id = ?";
                }
                $stmt = $this->db->prepare($updateContentQuery);
                $stmt->execute([$id]);
                
                // Get updated like count
                $countQuery = "SELECT like_count FROM content WHERE id = ?";
                $stmt = $this->db->prepare($countQuery);
                $stmt->execute([$id]);
                $likeCount = $stmt->fetchColumn();
                
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => true,
                    'liked' => $newLikeStatus,
                    'likeCount' => $likeCount
                ]);
            } else {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'يجب مشاهدة المحتوى أولاً']);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'حدث خطأ']);
        }
        exit;
    }
    
    public function search() {
        $query = $_GET['q'] ?? '';
        $category = $_GET['category'] ?? '';
        $type = $_GET['type'] ?? '';
        
        $sql = "
            SELECT c.*, cc.name as category_name,
                   CASE 
                       WHEN c.type = 'video' THEN 'فيديو'
                       WHEN c.type = 'article' THEN 'مقال'
                       WHEN c.type = 'infographic' THEN 'إنفوجرافيك'
                       WHEN c.type = 'guide' THEN 'دليل'
                       ELSE c.type
                   END as type_display
            FROM content c
            LEFT JOIN content_categories cc ON c.category_id = cc.id
            WHERE c.publish_status = 'published'
        ";
        
        $params = [];
        
        if ($query) {
            $sql .= " AND (c.title LIKE ? OR c.body LIKE ?)";
            $params[] = "%$query%";
            $params[] = "%$query%";
        }
        
        if ($category) {
            $sql .= " AND c.category_id = ?";
            $params[] = $category;
        }
        
        if ($type) {
            $sql .= " AND c.type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($results);
        exit;
    }
    
    private function trackContentView($contentId, $userId) {
        try {
            // Check if user already viewed this content
            $checkQuery = "SELECT id FROM content_views WHERE user_id = ? AND content_id = ?";
            $stmt = $this->db->prepare($checkQuery);
            $stmt->execute([$userId, $contentId]);
            
            if (!$stmt->fetch()) {
                // Insert new view record
                $insertQuery = "INSERT INTO content_views (user_id, content_id, viewed_at) VALUES (?, ?, NOW())";
                $stmt = $this->db->prepare($insertQuery);
                $stmt->execute([$userId, $contentId]);
                
                // Update content view count
                $updateQuery = "UPDATE content SET view_count = view_count + 1 WHERE id = ?";
                $stmt = $this->db->prepare($updateQuery);
                $stmt->execute([$contentId]);
                
                // Award points to user
                $this->awardPoints($userId, 'content_viewed', $contentId);
            }
        } catch (Exception $e) {
            // Silent: do not log errors per user request
        }
    }
    
    private function awardPoints($userId, $actionType, $referenceId) {
        try {
            // Get content reward points
            $pointsQuery = "SELECT reward_points FROM content WHERE id = ?";
            $stmt = $this->db->prepare($pointsQuery);
            $stmt->execute([$referenceId]);
            $points = $stmt->fetchColumn() ?: 5;
            
            // Insert points log
            $logQuery = "INSERT INTO points_log (user_id, points, action_type, reference_id, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($logQuery);
            $stmt->execute([$userId, $points, $actionType, $referenceId, 'مشاهدة محتوى توعوي']);
            
            // Update user stats
            $updateStatsQuery = "
                INSERT INTO user_stats (user_id, total_points, content_completed, last_activity) 
                VALUES (?, ?, 1, NOW())
                ON DUPLICATE KEY UPDATE 
                total_points = total_points + ?, 
                content_completed = content_completed + 1,
                last_activity = NOW()
            ";
            $stmt = $this->db->prepare($updateStatsQuery);
            $stmt->execute([$userId, $points, $points]);
            
            // Update leaderboard
            $this->updateLeaderboard($userId);
            
        } catch (Exception $e) {
            // Silent: do not log errors per user request
        }
    }
    
    private function updateLeaderboard($userId) {
        try {
            // Get user stats
            $statsQuery = "SELECT total_points, exams_completed, content_completed, current_streak FROM user_stats WHERE user_id = ?";
            $stmt = $this->db->prepare($statsQuery);
            $stmt->execute([$userId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($stats) {
                // Get badges count
                $badgesQuery = "SELECT COUNT(*) FROM user_badges WHERE user_id = ?";
                $stmt = $this->db->prepare($badgesQuery);
                $stmt->execute([$userId]);
                $badgesCount = $stmt->fetchColumn();
                
                // Update leaderboard
                $updateQuery = "
                    INSERT INTO leaderboard (user_id, total_points, exams_completed, content_completed, current_streak, badges_earned, last_activity) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE 
                    total_points = ?, 
                    exams_completed = ?, 
                    content_completed = ?, 
                    current_streak = ?, 
                    badges_earned = ?,
                    last_activity = NOW()
                ";
                $stmt = $this->db->prepare($updateQuery);
                $stmt->execute([
                    $userId, $stats['total_points'], $stats['exams_completed'], $stats['content_completed'], 
                    $stats['current_streak'], $badgesCount,
                    $stats['total_points'], $stats['exams_completed'], $stats['content_completed'], 
                    $stats['current_streak'], $badgesCount
                ]);
                
                // Update rankings
                $this->updateRankings();
            }
        } catch (Exception $e) {
            // Silent: do not log errors per user request
        }
    }
    
    private function updateRankings() {
        try {
            // تهيئة المتغير @rank
            $this->db->exec('SET @rank := 0');
            // تحديث المراكز بترتيب النقاط والنشاط
            $this->db->exec('UPDATE leaderboard SET rank_position = (@rank := @rank + 1) ORDER BY total_points DESC, last_activity DESC');
        } catch (Exception $e) {
            // Silent: do not log errors per user request
        }
    }
    
    // Admin methods
    public function adminIndex() {
        // Check admin permissions
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] < 2) {
            header('Location: ' . $this->basePath() . '/dashboard');
            exit;
        }
        
        // Get filter parameters
        $q = $_GET['q'] ?? '';
        $type = $_GET['type'] ?? '';
        $status = $_GET['publish_status'] ?? '';
        
        // Build query
        $sql = "
            SELECT c.*, cc.name as category_name, u.user_name as author_name
            FROM content c
            LEFT JOIN content_categories cc ON c.category_id = cc.id
            LEFT JOIN users u ON c.created_by = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($q) {
            $sql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        
        if ($type) {
            $sql .= " AND c.type = ?";
            $params[] = $type;
        }
        
        if ($status) {
            $sql .= " AND c.publish_status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->render('admin/content/index', [
            'items' => $items,
            'q' => $q,
            'type' => $type,
            'status' => $status
        ]);
    }

    public function create() {
        // Check admin permissions
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] < 2) {
            header('Location: ' . $this->basePath() . '/dashboard');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }
        
        // Get categories for dropdown
        $categoriesQuery = "SELECT * FROM content_categories WHERE is_active = 1 ORDER BY order_index ASC";
        $categories = $this->db->query($categoriesQuery)->fetchAll(PDO::FETCH_ASSOC);

        // Map error codes from query string to friendly messages
        $errors = [];
        $errorCode = $_GET['error'] ?? '';
        switch ($errorCode) {
            case 'title_required':
                $errors[] = 'يرجى إدخال عنوان المحتوى.';
                break;
            case 'media_url_required':
                $errors[] = 'يرجى إدخال رابط الوسائط المناسب لنوع المحتوى.';
                break;
            case 'invalid_media_url':
                $errors[] = 'رابط الوسائط غير صالح. يرجى إدخال رابط صحيح (YouTube/Vimeo/رابط مباشر).';
                break;
            case 'category_required':
                $errors[] = 'يرجى اختيار فئة للمحتوى.';
                break;
            case 'category_not_found':
                $errors[] = 'الفئة المحددة غير موجودة.';
                break;
            case 'user_not_found':
                $errors[] = 'المستخدم الحالي غير موجود، يرجى تسجيل الدخول مجدداً.';
                break;
            case 'create_failed':
                $errors[] = 'تعذر إنشاء المحتوى. يرجى التحقق من الحقول وقيود قاعدة البيانات والمحاولة مرة أخرى.';
                break;
        }
        
        // Pull any detailed error (from previous failed create) from session for console only
        $devError = null;
        if (!empty($_SESSION['last_error_console'])) {
            $devError = $_SESSION['last_error_console'];
            unset($_SESSION['last_error_console']);
        }
        
        $this->render('admin/content/create', [
            'categories' => $categories,
            'errors' => $errors,
            'devError' => $devError,
        ]);
    }
    
    public function store() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] < 2) {
            header('Location: ' . $this->basePath() . '/dashboard');
            exit;
        }
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = strtolower(trim($_POST['type'] ?? 'article'));
        if (!in_array($type, ['article','video','infographic','guide'], true)) { $type = 'article'; }
        $categoryId = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
        $body = trim($_POST['body'] ?? '');
        $mediaUrl = trim($_POST['media_url'] ?? '');
        $thumbnailUrl = trim($_POST['thumbnail_url'] ?? '');
        if (strlen($thumbnailUrl) > 500) { $thumbnailUrl = substr($thumbnailUrl, 0, 500); }
        $difficultyLevel = trim($_POST['difficulty_level'] ?? '');
        $difficultyLevel = in_array($difficultyLevel, ['beginner','intermediate','advanced'], true) ? $difficultyLevel : 'beginner';
        $rewardPoints = (int)($_POST['reward_points'] ?? 5);
        $estDuration = (int)($_POST['est_duration'] ?? 0);
        $publishStatus = trim($_POST['publish_status'] ?? 'draft');
        $publishStatus = in_array($publishStatus, ['draft','published','archived'], true) ? $publishStatus : 'draft';
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

        // Normalize by type
        if ($type !== 'article') {
            // If user pasted the URL into body, move it to media_url
            if ($mediaUrl === '' && $body !== '') {
                $mediaUrl = $body;
                $body = '';
            }
        } else {
            // For article, media_url is optional; keep body as main content
        }

        // Minimal validation
        if ($title === '') {
            header('Location: ' . $this->basePath() . '/admin/content/create?error=title_required');
            exit;
        }
        // If your DB requires category_id NOT NULL, uncomment the next block
        // if ($categoryId === null) {
        //     header('Location: ' . $this->basePath() . '/admin/content/create?error=category_required');
        //     exit;
        // }
        if ($type !== 'article' && $mediaUrl === '') {
            header('Location: ' . $this->basePath() . '/admin/content/create?error=media_url_required');
            exit;
        }
        if ($type !== 'article' && $mediaUrl !== '') {
            // Basic URL validation (accept youtu.be and youtube.com, vimeo, and generic URLs)
            $isValid = filter_var($mediaUrl, FILTER_VALIDATE_URL) !== false;
            if (!$isValid) {
                header('Location: ' . $this->basePath() . '/admin/content/create?error=invalid_media_url');
                exit;
            }
        }

        // Foreign key pre-checks (no logging, just friendly redirects)
        if ($categoryId !== null) {
            try {
                $stmt = $this->db->prepare('SELECT 1 FROM content_categories WHERE id = ? AND is_active = 1');
                $stmt->execute([$categoryId]);
                if (!$stmt->fetchColumn()) {
                    header('Location: ' . $this->basePath() . '/admin/content/create?error=category_not_found');
                    exit;
                }
            } catch (Exception $e) { /* silent */ }
        }
        // Ensure current user exists (for created_by FK)
        $currentUserId = (int)($_SESSION['user_id'] ?? 0);
        if ($currentUserId <= 0) {
            header('Location: ' . $this->basePath() . '/admin/content/create?error=user_not_found');
            exit;
        } else {
            try {
                $stmt = $this->db->prepare('SELECT 1 FROM users WHERE id = ?');
                $stmt->execute([$currentUserId]);
                if (!$stmt->fetchColumn()) {
                    header('Location: ' . $this->basePath() . '/admin/content/create?error=user_not_found');
                    exit;
                }
            } catch (Exception $e) { /* silent */ }
        }

        try {
            $insertQuery = "
                INSERT INTO content (
                    title, description, type, category_id, body, media_url, thumbnail_url,
                    difficulty_level, reward_points, est_duration, publish_status, is_featured,
                    created_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ";
            $stmt = $this->db->prepare($insertQuery);
            $stmt->execute([
                $title, $description, $type, $categoryId, $body, $mediaUrl, $thumbnailUrl,
                $difficultyLevel, $rewardPoints, $estDuration, $publishStatus, $isFeatured,
                $currentUserId
            ]);
            
            header('Location: ' . $this->basePath() . '/admin/content?success=created');
        } catch (Exception $e) {
            // Silent (no server log). Store sanitized details in session for console display only
            $detail = [
                'type' => get_class($e),
                'message' => mb_substr((string)$e->getMessage(), 0, 300),
                'time' => date('c'),
            ];
            if (property_exists($e, 'errorInfo') && is_array($e->errorInfo)) {
                $detail['sqlstate'] = $e->errorInfo[0] ?? null;
                $detail['driver'] = $e->errorInfo[2] ?? null;
            }
            $_SESSION['last_error_console'] = $detail;
            header('Location: ' . $this->basePath() . '/admin/content/create?error=create_failed');
        }
        exit;
    }

    public function edit() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] < 2) {
            header('Location: ' . $this->basePath() . '/dashboard');
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . $this->basePath() . '/admin/content');
            exit;
        }

        $item = $this->db->prepare("SELECT * FROM content WHERE id = ?")->execute([$id])->fetch(PDO::FETCH_ASSOC);
        if (!$item) {
            header('Location: ' . $this->basePath() . '/admin/content');
            exit;
        }

        $categories = $this->db->query("SELECT * FROM content_categories WHERE is_active = 1 ORDER BY order_index ASC")->fetchAll(PDO::FETCH_ASSOC);

        $errors = [];
        if ($_GET['error'] ?? '' === 'update_failed') {
            $errors[] = 'تعذر تحديث المحتوى. يرجى المحاولة مرة أخرى.';
        }

        $this->render('admin/content/edit', [
            'item' => $item,
            'categories' => $categories,
            'errors' => $errors
        ]);
    }

    public function update() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] < 2 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->basePath() . '/admin/content');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . $this->basePath() . '/admin/content');
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = strtolower(trim($_POST['type'] ?? 'article'));
        $type = in_array($type, ['article','video','infographic','guide']) ? $type : 'article';
        $categoryId = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
        $body = trim($_POST['body'] ?? '');
        $mediaUrl = trim($_POST['media_url'] ?? '');
        $thumbnailUrl = substr(trim($_POST['thumbnail_url'] ?? ''), 0, 500);
        $difficultyLevel = trim($_POST['difficulty_level'] ?? '');
        $difficultyLevel = in_array($difficultyLevel, ['beginner','intermediate','advanced']) ? $difficultyLevel : 'beginner';
        $rewardPoints = (int)($_POST['reward_points'] ?? 5);
        $estDuration = (int)($_POST['est_duration'] ?? 0);
        $publishStatus = trim($_POST['publish_status'] ?? 'draft');
        $publishStatus = in_array($publishStatus, ['draft','published','archived']) ? $publishStatus : 'draft';
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

        if ($type !== 'article' && $mediaUrl === '' && $body !== '') {
            $mediaUrl = $body;
            $body = '';
        }

        if ($title === '' || ($type !== 'article' && $mediaUrl === '')) {
            header('Location: ' . $this->basePath() . '/admin/content/edit?id='.$id.'&error=update_failed');
            exit;
        }

        try {
            $updateQuery = "UPDATE content SET
                title = ?, description = ?, type = ?, category_id = ?, body = ?, media_url = ?, 
                thumbnail_url = ?, difficulty_level = ?, reward_points = ?, est_duration = ?, 
                publish_status = ?, is_featured = ?, updated_at = NOW()
                WHERE id = ?";
            
            $stmt = $this->db->prepare($updateQuery);
            $stmt->execute([
                $title, $description, $type, $categoryId, $body, $mediaUrl, $thumbnailUrl,
                $difficultyLevel, $rewardPoints, $estDuration, $publishStatus, $isFeatured, $id
            ]);

            header('Location: ' . $this->basePath() . '/admin/content?success=updated');
        } catch (Exception $e) {
            header('Location: ' . $this->basePath() . '/admin/content/edit?id='.$id.'&error=update_failed');
        }
        exit;
    }

    public function delete() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] < 2 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . $this->basePath() . '/admin/content');
            exit;
        }

        try {
            $this->db->beginTransaction();
            $this->db->prepare('DELETE FROM content_views WHERE content_id = ?')->execute([$id]);
            $this->db->prepare('DELETE FROM content WHERE id = ?')->execute([$id]);
            $this->db->commit();
            header('Location: ' . $this->basePath() . '/admin/content?success=deleted');
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            header('Location: ' . $this->basePath() . '/admin/content?error=delete_failed');
        }
        exit;
    }

    public function favorite($id) {
        $this->toggleFavorite($id, true);
    }

    public function unfavorite($id) {
        $this->toggleFavorite($id, false);
    }

    private function toggleFavorite($id, $liked) {
        if (!isset($_SESSION['user_id']) || !$id) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'غير مصرح']);
            exit;
        }

        $userId = (int)$_SESSION['user_id'];

        try {
            $checkQuery = 'SELECT id FROM content_views WHERE user_id = ? AND content_id = ?';
            $stmt = $this->db->prepare($checkQuery);
            $stmt->execute([$userId, $id]);

            if ($stmt->fetch()) {
                $stmt = $this->db->prepare('UPDATE content_views SET liked = ? WHERE user_id = ? AND content_id = ?');
                $stmt->execute([$liked ? 1 : 0, $userId, $id]);
            } else {
                $stmt = $this->db->prepare('INSERT INTO content_views (user_id, content_id, viewed_at, liked) VALUES (?, ?, NOW(), ?)');
                $stmt->execute([$userId, $id, $liked ? 1 : 0]);
            }

            $stmt = $this->db->prepare('UPDATE content SET like_count = (SELECT COUNT(*) FROM content_views WHERE content_id = ? AND liked = 1) WHERE id = ?');
            $stmt->execute([$id, $id]);

            $stmt = $this->db->prepare('SELECT like_count FROM content WHERE id = ?');
            $stmt->execute([$id]);
            $likeCount = (int)$stmt->fetchColumn();

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'liked' => $liked, 'likeCount' => $likeCount]);
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'حدث خطأ']);
        }
        exit;
    }
}
