<?php
// app/controllers/EmployeeController.php

class EmployeeController extends Controller {
    

    private function getExamsPassedCount(int $userId): int {
        try {
            $row = Database::query(
                'SELECT COUNT(DISTINCT ea.exam_id) AS passed
                 FROM exam_attempts ea
                 JOIN exams e ON e.id = ea.exam_id
                 WHERE ea.user_id = :uid AND ea.status = "completed" AND ea.score >= e.passing_score',
                [':uid' => $userId]
            )->fetch();
            return (int)($row['passed'] ?? 0);
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function dashboard(): void {
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        $roleId = $_SESSION['role_id'] ?? 1;
        
        // إذا كان موظف عادي، إعادة توجيه لصفحة حسابي
        if ($roleId === 1) {
            header('Location: ' . $this->basePath() . '/profile');
            exit;
        }
        
        // للمسؤولين ومسؤولي التوعية - جلب البيانات المناسبة
        $stats = [];
        $suggested = [];
        $notifications = [];
        $recentActivities = []; // للأدمن

        try {
            if ($roleId === 2) { // مسؤول توعية
                $stats['published_content'] = (int)Database::query('SELECT COUNT(*) FROM content WHERE publish_status = "published"')->fetchColumn();
                $stats['total_views'] = (int)Database::query('SELECT COALESCE(SUM(view_count),0) FROM content')->fetchColumn();
                $stats['active_exams'] = (int)Database::query('SELECT COUNT(*) FROM exams WHERE is_active = 1')->fetchColumn();
                $stats['active_users'] = (int)Database::query('SELECT COUNT(*) FROM users WHERE status = "active"')->fetchColumn();
            } elseif ($roleId === 3) { // أدمن
                $stats['total_users'] = (int)Database::query('SELECT COUNT(*) FROM users')->fetchColumn();
                $stats['completed_courses'] = (int)Database::query('SELECT COALESCE(SUM(content_completed),0) FROM user_stats')->fetchColumn();
                $stats['certificates_issued'] = 892; // قيمة افتراضية
                $stats['security_rate'] = 94; // قيمة افتراضية

                // بيانات النشاطات الأخيرة للأدمن
                $recentActivities = [
                    ['title' => 'مستخدم جديد انضم', 'time' => 'منذ 5 دقائق'],
                    ['title' => 'دورة جديدة أضيفت', 'time' => 'منذ ساعة'],
                    ['title' => 'تحديث أمني جديد', 'time' => 'منذ ساعتين']
                ];
            }
            
            // محتوى مقترح للمسؤولين
            $suggested = Database::query(
                'SELECT id, title, description, type, created_at FROM content 
                 WHERE publish_status = "published" 
                 ORDER BY created_at DESC 
                 LIMIT 6'
            )->fetchAll();
            
            // إشعارات للمسؤولين
            $notifications = Database::query(
                'SELECT title, message, created_at FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 10',
                [':uid' => $userId]
            )->fetchAll();
            
        } catch (Throwable $e) {
            // استخدام القيم الافتراضية في الواجهة
        }

        $this->render('dashboard', [
            'pageTitle' => $roleId === 2 ? 'لوحة مسؤول التوعية' : 'لوحة الإدارة',
            'stats' => $stats,
            'suggested' => $suggested,
            'notifications' => $notifications,
            'recentActivities' => $recentActivities,
        ]);
    }

    public function profile(): void {
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        $roleId = $_SESSION['role_id'] ?? 1;
        
        // جلب إحصائيات المستخدم من قاعدة البيانات
        $userStats = $this->getUserStats($userId);
        // طَبِّق خريطة مفاتيح لتتوافق مع الواجهة
        $stats = [
            'points' => (int)($userStats['total_points'] ?? 0),
            'completed' => (int)($userStats['content_completed'] ?? 0),
            // احسب الاختبارات الناجحة فعلياً من exam_attempts + exams
            'exams_passed' => $this->getExamsPassedCount($userId),
            'streak' => (int)($userStats['current_streak'] ?? 0),
        ];

        // جلب المحتوى المقترح
        $suggested = $this->getSuggestedContent($userId);
        
        // جلب الإشعارات الحديثة
        $notifications = $this->getRecentNotifications($userId);

        $this->render('dashboard', [
            'pageTitle' => 'حسابي',
            'stats' => $stats,
            'suggested' => $suggested,
            'notifications' => $notifications,
            'recentActivities' => $recentActivities,
        ]);
    }
    
    private function getUserStats($userId): array {
        try {
            // جلب إحصائيات المستخدم
            $stats = Database::query(
                'SELECT * FROM user_stats WHERE user_id = :user_id',
                [':user_id' => $userId]
            )->fetch();
            
            if (!$stats) {
                // إنشاء سجل جديد إذا لم يكن موجوداً
                Database::query(
                    'INSERT INTO user_stats (user_id) VALUES (:user_id)',
                    [':user_id' => $userId]
                );
                return [
                    'total_points' => 0,
                    'exams_completed' => 0,
                    'exams_passed' => 0,
                    'content_completed' => 0,
                    'surveys_completed' => 0,
                    'current_streak' => 0,
                    'level' => 1
                ];
            }
            
            return $stats;
        } catch (Exception $e) {
            // في حالة الخطأ، إرجاع بيانات افتراضية
            return [
                'total_points' => 0,
                'exams_completed' => 0,
                'exams_passed' => 0,
                'content_completed' => 0,
                'surveys_completed' => 0,
                'current_streak' => 0,
                'level' => 1
            ];
        }
    }
    
    private function getRecentNotifications($userId): array {
        try {
            // جلب الإشعارات الخاصة بالمستخدم
            $notifications = Database::query(
                'SELECT * FROM notifications 
                 WHERE user_id = :user_id 
                 ORDER BY created_at DESC 
                 LIMIT 5',
                [':user_id' => $userId]
            )->fetchAll();
            
            return $notifications ?: [];
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function getAvailableExams($userId): array {
        try {
            // استعلام بسيط ومباشر للاختبارات المفعلة
            $exams = Database::query(
                'SELECT id, title, description, category, difficulty_level, duration_minutes, is_active, created_at
                 FROM exams 
                 WHERE is_active = 1 
                 ORDER BY created_at DESC 
                 LIMIT 6'
            )->fetchAll();
            
            // إضافة حالة التقدم لكل اختبار منفصلاً
            foreach ($exams as &$exam) {
                // البحث عن تقدم المستخدم في هذا الاختبار
                $progress = Database::query(
                    'SELECT status, progress_percentage FROM user_progress 
                     WHERE user_id = ? AND content_id = ? AND content_type = "exam"',
                    [$userId, $exam['id']]
                )->fetch();
                
                $exam['progress_status'] = $progress ? $progress['status'] : 'not_started';
                $exam['progress_percentage'] = $progress ? (int)$progress['progress_percentage'] : 0;
                $exam['status'] = $exam['progress_status']; // للتوافق مع العرض
            }
            
            return $exams;
        } catch (Exception $e) {
            error_log("EmployeeController getAvailableExams error: " . $e->getMessage());
            return [];
        }
    }
    
    private function getSuggestedContent($userId): array {
        try {
            // جلب المحتوى المقترح بناءً على الاختبارات غير المكتملة
            $content = Database::query(
                'SELECT e.id, e.title, e.description, e.category, e.difficulty_level
                 FROM exams e
                 LEFT JOIN user_progress up ON e.id = up.content_id 
                     AND up.user_id = :user_id 
                     AND up.content_type = "exam"
                 WHERE e.is_active = 1 
                     AND (up.status IS NULL OR up.status != "completed")
                 ORDER BY 
                     CASE e.difficulty_level 
                         WHEN "beginner" THEN 1 
                         WHEN "intermediate" THEN 2 
                         WHEN "advanced" THEN 3 
                     END
                 LIMIT 4',
                [':user_id' => $userId]
            )->fetchAll();
            
            return $content ?: [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function exams(): void {
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        
        // استخدام استعلام بسيط مضمون
        try {
            // تنظيف الاختبارات المنتهية الصلاحية أولاً
            Database::query(
                'UPDATE exam_attempts ea 
                 JOIN exams e ON ea.exam_id = e.id 
                 SET ea.status = "abandoned", ea.completed_at = NOW(), ea.time_taken = TIMESTAMPDIFF(SECOND, ea.started_at, NOW())
                 WHERE ea.status = "in_progress" 
                 AND TIMESTAMPDIFF(MINUTE, ea.started_at, NOW()) > e.duration_minutes + 10'
            );
            
            // تحديث تقدم المستخدم للاختبارات المنتهية
            Database::query(
                'UPDATE user_progress up
                 JOIN exam_attempts ea ON up.content_id = ea.exam_id AND up.user_id = ea.user_id
                 SET up.status = "not_started", up.progress_percentage = 0, up.updated_at = NOW()
                 WHERE up.content_type = "exam" AND ea.status = "abandoned" AND up.status = "in_progress"'
            );
            
            // جلب الاختبارات بشكل بسيط أولاً
            $exams = Database::query(
                'SELECT id, title, description, category, difficulty_level, duration_minutes, 
                        passing_score, max_attempts, is_active, created_at
                 FROM exams 
                 WHERE is_active = 1 
                 ORDER BY created_at DESC'
            )->fetchAll();
            
            // إضافة معلومات التقدم لكل اختبار منفصلاً
            foreach ($exams as &$exam) {
                // جلب حالة التقدم
                $progress = Database::query(
                    'SELECT status, progress_percentage FROM user_progress 
                     WHERE user_id = ? AND content_id = ? AND content_type = "exam"',
                    [$userId, $exam['id']]
                )->fetch();
                
                $exam['progress_status'] = $progress ? $progress['status'] : 'not_started';
                $exam['progress_percentage'] = $progress ? (int)$progress['progress_percentage'] : 0;
                
                // جلب أفضل نتيجة
                $bestScore = Database::query(
                    'SELECT MAX(score) as best_score FROM exam_attempts 
                     WHERE user_id = ? AND exam_id = ? AND status = "completed"',
                    [$userId, $exam['id']]
                )->fetch();
                
                $exam['best_score'] = $bestScore ? (float)$bestScore['best_score'] : 0;
                
                // عدد الأسئلة
                $questionsCount = Database::query(
                    'SELECT COUNT(*) as count FROM exam_questions WHERE exam_id = ?',
                    [$exam['id']]
                )->fetch();
                
                $exam['questions_count'] = $questionsCount ? (int)$questionsCount['count'] : 0;
                
                // للتوافق مع العرض
                $exam['status'] = $exam['progress_status'];
            }
            
        } catch (Exception $e) {
            error_log("EmployeeController exams(): Error: " . $e->getMessage());
            $exams = [];
        }
        
        // حساب إحصائيات الاختبارات للمستخدم لعرضها في الواجهة
        $stats = [
            'completed_exams' => 0,
            'average_score' => 0,
            'earned_points' => 0,
            'available_exams' => count($exams)
        ];
        
        try {
            // حساب الاختبارات المكتملة والنقاط
            $userStats = Database::query(
                'SELECT 
                    COUNT(DISTINCT ea.exam_id) as completed_count,
                    AVG(ea.score) as avg_score,
                    SUM(CASE WHEN ea.score >= e.passing_score THEN e.duration_minutes * 2 ELSE 0 END) as total_points
                 FROM exam_attempts ea
                 JOIN exams e ON ea.exam_id = e.id
                 WHERE ea.user_id = :user_id AND ea.status = "completed"',
                [':user_id' => $userId]
            )->fetch();
            
            if ($userStats) {
                $stats['completed_exams'] = (int)$userStats['completed_count'];
                $stats['average_score'] = (int)$userStats['avg_score'];
                $stats['earned_points'] = (int)$userStats['total_points'];
            }
        } catch (Exception $e) {
            // استخدام القيم الافتراضية
        }
        
        $this->render('employee/exams', ['exams' => $exams, 'stats' => $stats]);
    }

    public function progress(): void {
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        
        // جلب إحصائيات المستخدم التفصيلية
        $userStats = $this->getUserStats($userId);
        
        // جلب الشارات المكتسبة
        try {
            $earnedBadges = Database::query(
                'SELECT b.*, ub.earned_at 
                 FROM user_badges ub
                 JOIN badges b ON ub.badge_id = b.id
                 WHERE ub.user_id = :user_id
                 ORDER BY ub.earned_at DESC',
                [':user_id' => $userId]
            )->fetchAll();
        } catch (Exception $e) {
            $earnedBadges = [];
        }
        
        // جلب الأنشطة الأخيرة
        try {
            $recentActivities = Database::query(
                'SELECT pl.*, e.title as exam_title
                 FROM points_log pl
                 LEFT JOIN exams e ON pl.reference_id = e.id AND pl.action_type = "exam_completed"
                 WHERE pl.user_id = :user_id
                 ORDER BY pl.created_at DESC
                 LIMIT 10',
                [':user_id' => $userId]
            )->fetchAll();
        } catch (Exception $e) {
            $recentActivities = [];
        }
        
        $this->render('employee/progress', [
            'userStats' => $userStats,
            'earnedBadges' => $earnedBadges,
            'recentActivities' => $recentActivities
        ]);
    }

    public function surveys(): void {
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        
        // جلب جميع الاستبيانات المتاحة
        try {
            $surveys = Database::query(
                'SELECT s.id,
                        s.title,
                        s.description,
                        s.category,
                        s.status,
                        s.is_anonymous,
                        s.created_at,
                        COUNT(q.id) AS questions_count,
                        COALESCE(up.status, "available") AS user_status
                 FROM surveys s
                 LEFT JOIN survey_questions q ON q.survey_id = s.id
                 LEFT JOIN user_progress up ON up.content_type = "survey" 
                      AND up.content_id = s.id 
                      AND up.user_id = :user_id
                 WHERE s.status = "published" AND s.is_active = 1
                 GROUP BY s.id
                 ORDER BY s.created_at DESC',
                [':user_id' => $userId]
            )->fetchAll();
        } catch (Exception $e) {
            $surveys = [];
        }

        // حساب إحصائيات الاستبيانات للمستخدم لعرضها في الواجهة
        $stats = [
            'completed_surveys' => 0,
            'average_rating' => 0,
            'earned_points' => 0,
        ];
        try {
            $row = Database::query(
                'SELECT COUNT(*) AS completed
                 FROM user_progress
                 WHERE user_id = :user_id AND content_type = "survey" AND status = "completed"',
                [':user_id' => $userId]
            )->fetch();
            $stats['completed_surveys'] = (int)($row['completed'] ?? 0);
        } catch (Exception $e) {}

        // متوسط التقييم (إن وجد نظام تقييم للاستبيانات)
        try {
            $row = Database::query(
                'SELECT AVG(rating) AS avg_rating
                 FROM survey_responses
                 WHERE user_id = :user_id',
                [':user_id' => $userId]
            )->fetch();
            if ($row && $row['avg_rating'] !== null) {
                $stats['average_rating'] = round((float)$row['avg_rating'], 1);
            }
        } catch (Exception $e) {}

        // مجموع النقاط المكتسبة من الاستبيانات
        try {
            $row = Database::query(
                'SELECT COALESCE(SUM(points), 0) AS pts
                 FROM points_log
                 WHERE user_id = :user_id AND action_type = "survey_completed"',
                [':user_id' => $userId]
            )->fetch();
            $stats['earned_points'] = (int)($row['pts'] ?? 0);
        } catch (Exception $e) {}
        
        $this->render('employee/surveys', [
            'surveys' => $surveys,
            'stats' => $stats,
        ]);
    }

    public function takeExam(): void {
        $this->requireLogin();
        
        // استخراج معرف الاختبار من الرابط
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (preg_match('/\/exams\/(\d+)\/take/', $uri, $matches)) {
            $examId = (int)$matches[1];
        } else {
            header('Location: ' . $this->basePath() . '/exams');
            return;
        }

        $userId = $_SESSION['user_id'];
        
        // جلب بيانات الاختبار
        try {
            $exam = Database::query(
                'SELECT id, title, description, duration_minutes, difficulty_level 
                 FROM exams 
                 WHERE id = :id AND is_active = 1',
                [':id' => $examId]
            )->fetch();
        } catch (Exception $e) {
            $exam = null;
        }

        if (!$exam) {
            header('Location: ' . $this->basePath() . '/exams');
            return;
        }

        // جلب الأسئلة والخيارات للاختبار
        $questions = [];
        try {
            $rows = Database::query(
                'SELECT q.id, q.question_text, q.question_type, q.points, q.order_index
                 FROM exam_questions q
                 WHERE q.exam_id = :exam_id
                 ORDER BY q.order_index ASC, q.id ASC',
                [':exam_id' => $examId]
            )->fetchAll();
            
            if ($rows) {
                // جمع معرفات الأسئلة لجلب الخيارات دفعة واحدة
                $questionIds = array_column($rows, 'id');
                $placeholders = implode(',', array_fill(0, count($questionIds), '?'));
                $optionsByQuestion = [];
                
                if (!empty($questionIds)) {
                    $opts = Database::query(
                        'SELECT id, question_id, option_text, is_correct
                         FROM question_options
                         WHERE question_id IN ('.$placeholders.')',
                        $questionIds
                    )->fetchAll();
                    
                    foreach ($opts as $opt) {
                        $qid = (int)$opt['question_id'];
                        if (!isset($optionsByQuestion[$qid])) { $optionsByQuestion[$qid] = []; }
                        $optionsByQuestion[$qid][] = $opt;
                    }
                }
                
                // معالجة الأسئلة
                foreach ($rows as $r) {
                    $qid = (int)$r['id'];
                    // تطبيع/استنتاج نوع السؤال ليتوافق مع الواجهة
                    $rawType = strtolower(trim((string)($r['question_type'] ?? '')));
                    $normalizedType = '';
                    if ($rawType === 'mcq' || $rawType === 'multiple_choice') {
                        $normalizedType = 'multiple_choice';
                    } elseif ($rawType === 'truefalse' || $rawType === 'true_false') {
                        $normalizedType = 'true_false';
                    } elseif ($rawType === 'text') {
                        $normalizedType = 'text';
                    } else {
                        // استنتاج النوع عند عدم توفره
                        $hasOptions = !empty($optionsByQuestion[$qid]);
                        $corr = strtolower(trim((string)($r['correct_answer'] ?? '')));
                        $tfMap = ['true','false','صحيح','خطأ'];
                        if ($hasOptions) {
                            $normalizedType = 'multiple_choice';
                        } elseif (in_array($corr, $tfMap, true)) {
                            $normalizedType = 'true_false';
                        } else {
                            $normalizedType = 'text';
                        }
                    }
                    
                    $q = [
                        'id' => $qid,
                        'question_text' => $r['question_text'],
                        'question_type' => $normalizedType,
                        'points' => (int)($r['points'] ?? 0),
                        'options' => [],
                    ];
                    if ($normalizedType === 'multiple_choice' || $normalizedType === 'true_false') {
                        $q['options'] = $optionsByQuestion[$qid] ?? [];
                    }
                    $questions[] = $q;
                }
            }
        } catch (Exception $e) {
            $questions = [];
        }
        
        // محاولات قيد التقدم + الوقت المتبقي + الإجابات المحفوظة
        $remainingSeconds = null;
        $savedAnswers = [];
        try {
            $attempt = Database::query(
                'SELECT id, started_at FROM exam_attempts WHERE user_id = :uid AND exam_id = :eid AND status = "in_progress" ORDER BY id DESC LIMIT 1',
                [':uid' => $_SESSION['user_id'], ':eid' => $examId]
            )->fetch();
            if ($attempt) {
                $startedAt = strtotime((string)$attempt['started_at']);
                $durationSec = (int)($exam['duration_minutes'] ?? 0) * 60;
                $elapsed = max(0, time() - $startedAt);
                $remainingSeconds = max(0, $durationSec - $elapsed);

                // حمل الإجابات المحفوظة لتعبئتها في الواجهة
                $ans = Database::query(
                    'SELECT question_id, selected_option_id, text_answer FROM exam_answers WHERE attempt_id = :aid',
                    [':aid' => (int)$attempt['id']]
                )->fetchAll();
                foreach ($ans as $a) {
                    $qid = (int)$a['question_id'];
                    $savedAnswers[$qid] = [
                        'option_id' => isset($a['selected_option_id']) ? (int)$a['selected_option_id'] : null,
                        'answer_text' => $a['text_answer'] ?? null,
                    ];
                }
            }
        } catch (Throwable $e) {}

        // إنشاء optionsByQ للـ view
        $optionsByQ = [];
        foreach ($questions as $question) {
            if (isset($question['options']) && !empty($question['options'])) {
                $optionsByQ[$question['id']] = $question['options'];
            }
        }

        $this->render('employee/take-exam', [
            'pageTitle' => 'اختبار: ' . ($exam['title'] ?? ''),
            'exam' => $exam,
            'questions' => $questions,
            'optionsByQ' => $optionsByQ,
            'remainingSeconds' => $remainingSeconds,
            'savedAnswers' => $savedAnswers,
        ]);
    }

    public function startExam(): void {
        $this->requireLogin();
        
        // استخراج معرف الاختبار من الرابط
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (preg_match('/\/exams\/(\d+)\/take/', $uri, $matches)) {
            $examId = (int)$matches[1];
        } else {
            header('Location: ' . $this->basePath() . '/exams');
            return;
        }

        $userId = (int)$_SESSION['user_id'];

        try {
            // Fetch exam settings
            $exam = Database::query(
                'SELECT id, duration_minutes, max_attempts, is_active FROM exams WHERE id = :id',
                [':id' => $examId]
            )->fetch();
            if (!$exam || (int)$exam['is_active'] !== 1) { header('Location: ' . $this->basePath() . '/exams'); return; }

            // Count previous attempts
            $row = Database::query(
                'SELECT COUNT(*) c FROM exam_attempts WHERE user_id = :uid AND exam_id = :eid',
                [':uid' => $userId, ':eid' => $examId]
            )->fetch();
            $attemptsCount = (int)($row['c'] ?? 0);
            $maxAttempts = (int)($exam['max_attempts'] ?? 0);
            if ($maxAttempts > 0 && $attemptsCount >= $maxAttempts) {
                header('Location: ' . $this->basePath() . '/exams?error=max_attempts');
                return;
            }

            // Try to reuse an in-progress attempt if exists
            $activeAttempt = Database::query(
                'SELECT id, started_at FROM exam_attempts WHERE user_id = :uid AND exam_id = :eid AND status = "in_progress" ORDER BY id DESC LIMIT 1',
                [':uid' => $userId, ':eid' => $examId]
            )->fetch();
            if ($activeAttempt) {
                header('Location: ' . $this->basePath() . '/exams/' . $examId . '/take');
                return;
            }

            // Create new attempt
            Database::query(
                'INSERT INTO exam_attempts (user_id, exam_id, status, score, started_at) VALUES (:uid, :eid, "in_progress", 0, NOW())',
                [':uid' => $userId, ':eid' => $examId]
            );
        } catch (Throwable $e) {
            error_log('startExam error: ' . $e->getMessage());
        }

        header('Location: ' . $this->basePath() . '/exams/' . $examId . '/take');
    }

    public function saveAnswer(): void {
        $this->requireLogin();
        
        header('Content-Type: application/json; charset=utf-8');
        
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $examId = (int)($_POST['exam_id'] ?? 0);
        $questionId = (int)($_POST['question_id'] ?? 0);
        $optionId = !empty($_POST['option_id']) ? (int)$_POST['option_id'] : null;
        $answerText = !empty($_POST['answer_text']) ? trim($_POST['answer_text']) : null;

        if ($examId <= 0 || $questionId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'بيانات غير صالحة']);
            return;
        }

        try {
            error_log("saveAnswer: Starting - userId: $userId, examId: $examId, questionId: $questionId");
            
            $attempt = Database::query(
                'SELECT id FROM exam_attempts WHERE user_id = :uid AND exam_id = :eid AND status = "in_progress" ORDER BY id DESC LIMIT 1',
                [':uid' => $userId, ':eid' => $examId]
            )->fetch();

            if (!$attempt) {
                error_log("saveAnswer: No attempt found, creating new one");
                // إنشاء محاولة جديدة إذا لم توجد
                Database::query(
                    'INSERT INTO exam_attempts (user_id, exam_id, status, score, started_at) VALUES (:uid, :eid, "in_progress", 0, NOW())',
                    [':uid' => $userId, ':eid' => $examId]
                );
                $attemptId = (int)Database::connection()->lastInsertId();
                error_log("saveAnswer: Created new attempt with ID: $attemptId");
            } else {
                $attemptId = (int)$attempt['id'];
                error_log("saveAnswer: Using existing attempt ID: $attemptId");
            }

            error_log("saveAnswer: Calling ExamService::saveAnswer");
            $result = ExamService::saveAnswer($attemptId, $questionId, $optionId, $answerText);
            error_log("saveAnswer: ExamService returned: " . json_encode($result));

            echo json_encode([
                'success' => true,
                'is_correct' => $result['is_correct'],
                'user_answer' => $result['user_answer'],
                'correct_answer' => $result['correct_answer'],
                'points_earned' => $result['points_earned'],
                'max_points' => $result['max_points']
            ]);

        } catch (Throwable $e) {
            error_log('EmployeeController::saveAnswer error: ' . $e->getMessage());
            error_log('EmployeeController::saveAnswer file: ' . $e->getFile());
            error_log('EmployeeController::saveAnswer line: ' . $e->getLine());
            error_log('EmployeeController::saveAnswer trace: ' . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'خطأ في الخادم']);
        }
    }

    public function submitExam(): void {
        $this->requireLogin();
        
        // Set JSON content type header
        header('Content-Type: application/json; charset=utf-8');
        
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $examId = (int)($_POST['exam_id'] ?? 0);

        if ($examId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'معرف الاختبار غير صالح']);
            return;
        }

        try {
            // البحث عن محاولة نشطة
            $attempt = Database::query(
                'SELECT id FROM exam_attempts WHERE user_id = :uid AND exam_id = :eid AND status = "in_progress" ORDER BY id DESC LIMIT 1',
                [':uid' => $userId, ':eid' => $examId]
            )->fetch();

            if (!$attempt) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'لا توجد محاولة نشطة']);
                return;
            }

            $attemptId = (int)$attempt['id'];

            // استخدام ExamService لإنهاء المحاولة وحساب النتيجة
            ExamService::completeExamAttempt($attemptId);

            // جلب النتيجة النهائية
            $scoreData = ExamService::calculateExamScore($attemptId);

            // جلب معلومات الاختبار للتحقق من النجاح
            $exam = Database::query(
                'SELECT passing_score FROM exams WHERE id = :id',
                [':id' => $examId]
            )->fetch();

            $passed = $exam && $scoreData['score_percentage'] >= (int)$exam['passing_score'];

            // منح النقاط إذا نجح
            if ($passed) {
                $pointsToAward = $scoreData['total_points_earned'];
                Database::query(
                    'INSERT INTO points_log (user_id, points, action_type, reference_id, description, created_at) VALUES (:uid, :points, "exam_completion", :exam_id, :desc, NOW())',
                    [
                        ':uid' => $userId,
                        ':points' => $pointsToAward,
                        ':exam_id' => $examId,
                        ':desc' => 'إكمال اختبار بنجاح'
                    ]
                );
            }

            echo json_encode([
                'success' => true,
                'score' => $scoreData['score_percentage'],
                'correct_answers' => $scoreData['correct_answers'],
                'total_questions' => $scoreData['total_questions'],
                'points_earned' => $scoreData['total_points_earned'],
                'passed' => $passed
            ]);

        } catch (Throwable $e) {
            error_log('EmployeeController::submitExam error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'خطأ في الخادم']);
        }
    }

    public function takeSurvey($surveyIdParam = null): void {
        $this->requireLogin();

        // استخراج معرف الاستبيان من الرابط
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $surveyId = 0;
        if (preg_match('/\/surveys\/(\d+)\/take/', $uri, $matches)) {
            $surveyId = (int)$matches[1];
        }
        if ($surveyId === 0 && $surveyIdParam !== null) { $surveyId = (int)$surveyIdParam; }
        if ($surveyId <= 0) { header('Location: '.$this->basePath().'/surveys'); return; }

        $userId = (int)$_SESSION['user_id'];

        // جلب بيانات الاستبيان
        try {
            $survey = Database::query('SELECT id, title, description, is_anonymous FROM surveys WHERE id = :id AND is_active = 1', [':id'=>$surveyId])->fetch();
        } catch (Throwable $e) { $survey = null; }
        if (!$survey) { header('Location: '.$this->basePath().'/surveys'); return; }

        // جلب الأسئلة والخيارات مع تطبيع الحقول
        $questions = [];
        try {
            // محاولات متعددة لأسماء الأعمدة
            try {
                $qs = Database::query('SELECT id, type, text, order_index FROM survey_questions WHERE survey_id = :sid ORDER BY order_index, id', [':sid'=>$surveyId])->fetchAll();
            } catch (Throwable $e1) {
                $qs = Database::query('SELECT id, question_type AS type, question_text AS text, order_index FROM survey_questions WHERE survey_id = :sid ORDER BY order_index, id', [':sid'=>$surveyId])->fetchAll();
            }
            $qids = array_map(fn($q)=>(int)$q['id'], $qs);
            $optionsByQ = [];
            if (!empty($qids)) {
                $in = implode(',', array_fill(0,count($qids),'?'));
                try {
                    $stmt = Database::connection()->prepare('SELECT id, question_id, text FROM survey_options WHERE question_id IN ('.$in.') ORDER BY id');
                    $stmt->execute($qids);
                } catch (Throwable $e2) {
                    $stmt = Database::connection()->prepare('SELECT id, question_id, option_text AS text FROM survey_question_options WHERE question_id IN ('.$in.') ORDER BY id');
                    $stmt->execute($qids);
                }
                foreach ($stmt->fetchAll() as $row) {
                    $qid = (int)$row['question_id'];
                    if (!isset($optionsByQ[$qid])) $optionsByQ[$qid] = [];
                    $optionsByQ[$qid][] = $row;
                }
            }
            foreach ($qs as $q) {
                $type = $q['type'];
                // تحويل أنواع الأسئلة من قاعدة البيانات إلى أنواع العرض
                switch ($type) {
                    case 'multiple_choice':
                        $type = 'single';
                        break;
                    case 'single_choice':
                        $type = 'single';
                        break;
                    case 'text':
                        $type = 'text';
                        break;
                    case 'rating':
                        $type = 'rating';
                        break;
                    case 'yes_no':
                        $type = 'yesno';
                        break;
                    case 'mcq':
                        $type = 'single';
                        break;
                    case 'checkbox':
                        $type = 'multiple';
                        break;
                    default:
                        $type = 'single';
                        break;
                }
                
                // إذا كان النوع single أو multiple ولا توجد خيارات، تحويل إلى text
                $options = $optionsByQ[(int)$q['id']] ?? [];
                if (($type === 'single' || $type === 'multiple') && empty($options)) {
                    $type = 'text';
                }
                
                $questions[] = [
                    'id' => (int)$q['id'],
                    'type' => $type,
                    'text' => $q['text'],
                    'options' => $options,
                ];
            }
        } catch (Throwable $e) {
            $questions = [];
        }

        // استرجاع إجابات محفوظة وفق المخطط (survey_responses + survey_answers)
        $saved = [];
        try {
            $resp = Database::query(
                'SELECT id FROM survey_responses WHERE user_id = :u AND survey_id = :s AND is_completed = 0 ORDER BY id DESC LIMIT 1',
                [':u'=>$userId, ':s'=>$surveyId]
            )->fetch();
            if ($resp) {
                $respId = (int)$resp['id'];
                $savedRows = Database::query(
                    'SELECT question_id, selected_option_id, answer_text FROM survey_answers WHERE response_id = :rid',
                    [':rid'=>$respId]
                )->fetchAll();
                foreach ($savedRows as $row) {
                    $qid = (int)$row['question_id'];
                    // للاختيارات المتعددة قد تظهر عدة صفوف؛ نأخذ آخر واحد فقط كتعبئة مبدئية
                    $saved[$qid] = [
                        'option_id' => isset($row['selected_option_id']) ? (int)$row['selected_option_id'] : null,
                        'answer_text' => $row['answer_text'] ?? null,
                    ];
                }
            }
        } catch (Throwable $e) {}

        $this->render('employee/take-survey', [
            'pageTitle' => 'استبيان: ' . ($survey['title'] ?? ''),
            'survey' => $survey,
            'questions' => $questions,
            'saved' => $saved,
        ]);
    }

    public function saveSurveyProgress($surveyIdParam = null): void {
        $this->requireLogin();
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }

        $userId = (int)$_SESSION['user_id'];
        $surveyId = (int)($_POST['survey_id'] ?? 0);
        if ($surveyId === 0 && $surveyIdParam !== null) { $surveyId = (int)$surveyIdParam; }
        $questionId = (int)($_POST['question_id'] ?? 0);
        $optionId = isset($_POST['option_id']) ? (int)$_POST['option_id'] : null;
        $answerText = isset($_POST['answer_text']) ? trim($_POST['answer_text']) : null;
        $ratingValue = isset($_POST['rating_value']) ? (int)$_POST['rating_value'] : null;
        if ($surveyId<=0 || $questionId<=0) { http_response_code(400); echo 'Bad Request'; return; }

        try {
            // احصل/أنشئ response للرجل الحالي
            $resp = Database::query('SELECT id FROM survey_responses WHERE user_id = :u AND survey_id = :s AND is_completed = 0 ORDER BY id DESC LIMIT 1', [':u'=>$userId, ':s'=>$surveyId])->fetch();
            if (!$resp) {
                Database::query('INSERT INTO survey_responses (survey_id, user_id, is_completed, created_at, updated_at) VALUES (:s, :u, 0, NOW(), NOW())', [':s'=>$surveyId, ':u'=>$userId]);
                $responseId = (int)Database::connection()->lastInsertId();
            } else {
                $responseId = (int)$resp['id'];
            }

            // upsert في survey_answers (سجل واحد للسؤال في single/text)
            $ans = Database::query('SELECT id FROM survey_answers WHERE response_id = :r AND question_id = :q ORDER BY id DESC LIMIT 1', [':r'=>$responseId, ':q'=>$questionId])->fetch();
            if ($ans) {
                Database::query('UPDATE survey_answers SET selected_option_id = :o, answer_text = :t, rating_value = :r WHERE id = :id', [':o'=>$optionId, ':t'=>$answerText, ':r'=>$ratingValue, ':id'=>$ans['id']]);
            } else {
                Database::query('INSERT INTO survey_answers (response_id, question_id, answer_text, selected_option_id, rating_value, created_at) VALUES (:r, :q, :t, :o, :rv, NOW())', [':r'=>$responseId, ':q'=>$questionId, ':t'=>$answerText, ':o'=>$optionId, ':rv'=>$ratingValue]);
            }

            // تقدّم المستخدم العام
            Database::query('INSERT INTO user_progress (user_id, content_type, content_id, status, progress_percentage, updated_at) VALUES (:u, "survey", :sid, "in_progress", 0, NOW()) ON DUPLICATE KEY UPDATE status = "in_progress", updated_at = NOW()', [':u'=>$userId, ':sid'=>$surveyId]);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success'=>true]);
        } catch (Throwable $e) {
            error_log('saveSurveyProgress error: ' . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success'=>false,'message'=>'فشل الحفظ']);
        }
    }

    public function submitSurvey($surveyIdParam = null): void {
        $this->requireLogin();
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { 
            http_response_code(405); 
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success'=>false,'message'=>'Method Not Allowed']); 
            return; 
        }

        $userId = (int)$_SESSION['user_id'];
        $surveyId = (int)($_POST['survey_id'] ?? 0);
        if ($surveyId === 0 && $surveyIdParam !== null) { $surveyId = (int)$surveyIdParam; }
        if ($surveyId<=0) { 
            http_response_code(400); 
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success'=>false,'message'=>'Bad Request: Invalid survey ID']); 
            return; 
        }

        try {
            // التحقق من وجود الاستبيان
            $survey = Database::query('SELECT id, title FROM surveys WHERE id = :id AND is_active = 1', [':id'=>$surveyId])->fetch();
            if (!$survey) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success'=>false,'message'=>'الاستبيان غير موجود أو غير متاح']);
                return;
            }

            // وضع علامة مكتمل على response الأحدث للمستخدم في هذا الاستبيان
            $resp = Database::query(
                'SELECT id FROM survey_responses WHERE user_id = :u AND survey_id = :s AND is_completed = 0 ORDER BY id DESC LIMIT 1',
                [':u'=>$userId, ':s'=>$surveyId]
            )->fetch();
            if ($resp) {
                Database::query('UPDATE survey_responses SET is_completed = 1, submitted_at = NOW(), updated_at = NOW() WHERE id = :id', [':id'=>(int)$resp['id']]);
            } else {
                // إن لم يكن هناك response، أنشئ واكمله
                Database::query('INSERT INTO survey_responses (survey_id, user_id, is_completed, submitted_at, created_at, updated_at) VALUES (:s, :u, 1, NOW(), NOW(), NOW())', [':s'=>$surveyId, ':u'=>$userId]);
            }

            // تقدّم المستخدم العام
            Database::query('INSERT INTO user_progress (user_id, content_type, content_id, status, progress_percentage, updated_at) VALUES (:u, "survey", :sid, "completed", 100, NOW()) ON DUPLICATE KEY UPDATE status = "completed", progress_percentage = 100, updated_at = NOW()', [':u'=>$userId, ':sid'=>$surveyId]);
            // منح نقاط بسيطة على الإكمال
            Database::query('INSERT INTO points_log (user_id, points, action_type, reference_id, description, created_at) VALUES (:u, 5, "survey_completed", :sid, :d, NOW())', [':u'=>$userId, ':sid'=>$surveyId, ':d'=>'إكمال استبيان']);
            Database::query('INSERT INTO user_stats (user_id, total_points, surveys_completed, last_activity) VALUES (:u, 5, 1, NOW()) ON DUPLICATE KEY UPDATE total_points = total_points + 5, surveys_completed = surveys_completed + 1, last_activity = NOW()', [':u'=>$userId]);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success'=>true, 'message'=>'تم إرسال الاستبيان بنجاح']);
        } catch (Throwable $e) {
            error_log('submitSurvey error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success'=>false,'message'=>'فشل الإرسال: '.$e->getMessage()]);
        }
    }

    public function abandonExam(): void {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');
        
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            return;
        }
        
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $examId = (int)($_POST['exam_id'] ?? 0);
        
        if ($userId <= 0 || $examId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'معاملات مفقودة']);
            return;
        }
        
        try {
            // البحث عن محاولة نشطة
            $attempt = Database::query(
                'SELECT id FROM exam_attempts WHERE user_id = :u AND exam_id = :e AND status = "in_progress" ORDER BY id DESC LIMIT 1',
                [':u' => $userId, ':e' => $examId]
            )->fetch();
            
            if (!$attempt) {
                echo json_encode(['success' => false, 'message' => 'لا توجد محاولة نشطة']);
                return;
            }
            
            // تحديث حالة المحاولة إلى مهجورة
            Database::query(
                'UPDATE exam_attempts SET status = "abandoned", completed_at = NOW(), time_taken = TIMESTAMPDIFF(SECOND, started_at, NOW()) WHERE id = :id',
                [':id' => (int)$attempt['id']]
            );
            
            // تحديث تقدم المستخدم
            Database::query(
                'UPDATE user_progress SET status = "not_started", progress_percentage = 0, updated_at = NOW() WHERE user_id = :u AND content_id = :e AND content_type = "exam"',
                [':u' => $userId, ':e' => $examId]
            );
            
            echo json_encode(['success' => true, 'message' => 'تم إلغاء الاختبار بنجاح']);
            
        } catch (Throwable $e) {
            error_log('abandonExam error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'خطأ في إلغاء الاختبار']);
        }
    }
}
