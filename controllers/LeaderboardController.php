<?php

class LeaderboardController extends Controller {
    private $db;
    
    public function __construct() {
        $this->db = Database::connection();
    }
    
    public function index() {
        $topUsersQuery = "
            SELECT l.*, u.user_name AS name, u.email, NULL AS department
            FROM leaderboard l
            LEFT JOIN users u ON l.user_id = u.id
            WHERE COALESCE(u.status, 'active') = 'active'
            ORDER BY l.rank_position ASC
            LIMIT 3
        ";
        $topUsers = $this->db->query($topUsersQuery)->fetchAll(PDO::FETCH_ASSOC);
        
        while (count($topUsers) < 3) {
            $topUsers[] = [
                'name' => null,
                'total_points' => 0,
                'badges_earned' => 0,
                'current_streak' => 0
            ];
        }
        
        $currentUserStats = null;
        if (isset($_SESSION['user_id'])) {
            $currentUserQuery = "
                SELECT l.*, u.user_name AS name, u.email, NULL AS department
                FROM leaderboard l
                LEFT JOIN users u ON l.user_id = u.id
                WHERE l.user_id = ?
            ";
            $stmt = $this->db->prepare($currentUserQuery);
            $stmt->execute([$_SESSION['user_id']]);
            $currentUserStats = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        $leaderboardQuery = "
            SELECT l.*, u.user_name AS name, u.email, NULL AS department
            FROM leaderboard l
            LEFT JOIN users u ON l.user_id = u.id
            WHERE COALESCE(u.status, 'active') = 'active'
            ORDER BY l.rank_position ASC
            LIMIT 50
        ";
        $leaderboard = $this->db->query($leaderboardQuery)->fetchAll(PDO::FETCH_ASSOC);
        
        $this->render('employee/leaderboard', [
            'topUsers' => $topUsers,
            'currentUserStats' => $currentUserStats,
            'leaderboard' => $leaderboard
        ]);
    }
    
    public function filter() {
        $period = $_GET['period'] ?? 'all';
        
        $dateCondition = '';
        $params = [];
        
        switch ($period) {
            case 'today':
                $dateCondition = 'AND DATE(l.last_activity) = CURDATE()';
                break;
            case 'week':
                $dateCondition = 'AND l.last_activity >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
                break;
            case 'month':
                $dateCondition = 'AND l.last_activity >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                break;
            default:
                break;
        }
        
        $leaderboardQuery = "
            SELECT l.*, u.user_name AS name, u.email, NULL AS department,
                   (@rank := @rank + 1) as filtered_rank
            FROM leaderboard l
            LEFT JOIN users u ON l.user_id = u.id
            CROSS JOIN (SELECT @rank := 0) r
            WHERE COALESCE(u.status, 'active') = 'active' $dateCondition
            ORDER BY l.total_points DESC, l.last_activity DESC
            LIMIT 50
        ";
        
        $leaderboard = $this->db->query($leaderboardQuery)->fetchAll(PDO::FETCH_ASSOC);
        
        $topUsers = array_slice($leaderboard, 0, 3);
        while (count($topUsers) < 3) {
            $topUsers[] = [
                'name' => null,
                'total_points' => 0,
                'badges_earned' => 0,
                'current_streak' => 0
            ];
        }
        
        if (isset($_SESSION['user_id'])) {
            foreach ($leaderboard as &$user) {
                $user['isCurrentUser'] = ($user['user_id'] == $_SESSION['user_id']);
            }
        }
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'leaderboard' => $leaderboard,
            'topUsers' => $topUsers
        ]);
        exit;
    }
    
    public function refresh() {
        try {
            $this->recalculateLeaderboard();
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'فشل في تحديث البيانات']);
        }
        exit;
    }
    
    private function recalculateLeaderboard() {
        $usersQuery = "
            SELECT u.id as user_id,
                   COALESCE(us.total_points, 0) as total_points,
                   COALESCE(us.exams_completed, 0) as exams_completed,
                   COALESCE(us.content_completed, 0) as content_completed,
                   COALESCE(us.current_streak, 0) as current_streak,
                   COALESCE(us.last_activity, NOW()) as last_activity,
                   (SELECT COUNT(*) FROM user_badges ub WHERE ub.user_id = u.id) as badges_earned
            FROM users u
            LEFT JOIN user_stats us ON u.id = us.user_id
            WHERE COALESCE(u.status, 'active') = 'active'
        ";
        
        $users = $this->db->query($usersQuery)->fetchAll(PDO::FETCH_ASSOC);
        
        $this->db->exec("DELETE FROM leaderboard");
        
        $insertQuery = "
            INSERT INTO leaderboard (user_id, total_points, exams_completed, content_completed, 
                                   current_streak, badges_earned, last_activity, rank_position) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $this->db->prepare($insertQuery);
        
        usort($users, function($a, $b) {
            if ($a['total_points'] == $b['total_points']) {
                return strtotime($b['last_activity']) - strtotime($a['last_activity']);
            }
            return $b['total_points'] - $a['total_points'];
        });
        
        foreach ($users as $index => $user) {
            $rank = $index + 1;
            $stmt->execute([
                $user['user_id'],
                $user['total_points'],
                $user['exams_completed'],
                $user['content_completed'],
                $user['current_streak'],
                $user['badges_earned'],
                $user['last_activity'],
                $rank
            ]);
        }
    }
    
    public function analytics() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] < 2) {
            header('Location: /dashboard');
            exit;
        }
        
        $activityTrendsQuery = "
            SELECT DATE(last_activity) as activity_date,
                   COUNT(*) as active_users,
                   AVG(total_points) as avg_points
            FROM leaderboard
            WHERE last_activity >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(last_activity)
            ORDER BY activity_date DESC
        ";
        $activityTrends = $this->db->query($activityTrendsQuery)->fetchAll(PDO::FETCH_ASSOC);
        
        $topByExamsQuery = "
            SELECT l.*, u.user_name AS name, u.email, NULL AS department
            FROM leaderboard l
            LEFT JOIN users u ON l.user_id = u.id
            WHERE COALESCE(u.status, 'active') = 'active'
            ORDER BY l.exams_completed DESC
            LIMIT 10
        ";
        $topByExams = $this->db->query($topByExamsQuery)->fetchAll(PDO::FETCH_ASSOC);
        
        $topByContentQuery = "
            SELECT l.*, u.user_name AS name, u.email, NULL AS department
            FROM leaderboard l
            LEFT JOIN users u ON l.user_id = u.id
            WHERE COALESCE(u.status, 'active') = 'active'
            ORDER BY l.content_completed DESC
            LIMIT 10
        ";
        $topByContent = $this->db->query($topByContentQuery)->fetchAll(PDO::FETCH_ASSOC);
        
        $topByStreakQuery = "
            SELECT l.*, u.user_name AS name, u.email, NULL AS department
            FROM leaderboard l
            LEFT JOIN users u ON l.user_id = u.id
            WHERE COALESCE(u.status, 'active') = 'active'
            ORDER BY l.current_streak DESC
            LIMIT 10
        ";
        $topByStreak = $this->db->query($topByStreakQuery)->fetchAll(PDO::FETCH_ASSOC);
        
        $this->render('admin/leaderboard/analytics', [
            'activityTrends' => $activityTrends,
            'topByExams' => $topByExams,
            'topByContent' => $topByContent,
            'topByStreak' => $topByStreak
        ]);
    }
    
    public function awardBadges() {
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] < 2) {
            echo json_encode(['success' => false, 'message' => 'غير مصرح']);
            exit;
        }
        
        try {
            $this->processAutomaticBadges();
            echo json_encode(['success' => true, 'message' => 'تم منح الشارات بنجاح']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'فشل في منح الشارات']);
        }
        exit;
    }
    
    private function processAutomaticBadges() {
        $badgesQuery = "SELECT * FROM badges WHERE is_active = 1";
        $badges = $this->db->query($badgesQuery)->fetchAll(PDO::FETCH_ASSOC);
        
        $usersQuery = "
            SELECT u.id, us.*
            FROM users u
            LEFT JOIN user_stats us ON u.id = us.user_id
            WHERE COALESCE(u.status, 'active') = 'active'
        ";
        $users = $this->db->query($usersQuery)->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($users as $user) {
            foreach ($badges as $badge) {
                $hasBadge = $this->db->prepare("SELECT id FROM user_badges WHERE user_id = ? AND badge_id = ?");
                $hasBadge->execute([$user['id'], $badge['id']]);
                
                if ($hasBadge->fetch()) {
                    continue;
                }
                
                $earned = false;
                $criteria = json_decode($badge['criteria'], true);
                
                if ($criteria) {
                    $earned = $this->checkBadgeCriteria($user, $criteria);
                }
                
                if ($earned) {
                    $awardQuery = "INSERT INTO user_badges (user_id, badge_id, earned_at) VALUES (?, ?, NOW())";
                    $stmt = $this->db->prepare($awardQuery);
                    $stmt->execute([$user['id'], $badge['id']]);
                    
                    if ($badge['points_reward'] > 0) {
                        $this->awardPoints($user['id'], 'badge_earned', $badge['id'], $badge['points_reward']);
                    }
                }
            }
        }
    }
    
    private function checkBadgeCriteria($user, $criteria) {
        foreach ($criteria as $criterion => $value) {
            switch ($criterion) {
                case 'total_points':
                    if (($user['total_points'] ?? 0) < $value) return false;
                    break;
                case 'exams_completed':
                    if (($user['exams_completed'] ?? 0) < $value) return false;
                    break;
                case 'content_completed':
                    if (($user['content_completed'] ?? 0) < $value) return false;
                    break;
                case 'current_streak':
                    if (($user['current_streak'] ?? 0) < $value) return false;
                    break;
                case 'perfect_scores':
                    if (($user['perfect_scores'] ?? 0) < $value) return false;
                    break;
            }
        }
        return true;
    }
    
    private function awardPoints($userId, $actionType, $referenceId, $points) {
        try {
            $logQuery = "INSERT INTO points_log (user_id, points, action_type, reference_id, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($logQuery);
            $stmt->execute([$userId, $points, $actionType, $referenceId, 'مكافأة شارة']);
            
            $updateStatsQuery = "
                INSERT INTO user_stats (user_id, total_points, last_activity) 
                VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                total_points = total_points + ?, 
                last_activity = NOW()
            ";
            $stmt = $this->db->prepare($updateStatsQuery);
            $stmt->execute([$userId, $points, $points]);
            
        } catch (Exception $e) {
        }
    }
}
