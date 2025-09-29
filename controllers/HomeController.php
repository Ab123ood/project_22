<?php
// app/controllers/HomeController.php
class HomeController extends Controller {
    public function index(): void {
        $this->startSession();
        $basePath = $this->basePath();
        $user = null;
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId > 0) {
            try {
                $user = Database::query('SELECT id, email, user_name FROM users WHERE id = :id', [':id'=>$userId])->fetch();
            } catch (Throwable $e) { $user = null; }
        }

        $contents = [];
        try {
            $contents = Database::query(
                'SELECT id, title, type, publish_status, created_at FROM content 
                 WHERE publish_status = "published" 
                 ORDER BY created_at DESC 
                 LIMIT 6'
            )->fetchAll();
        } catch (Throwable $e) { $contents = []; }

        $this->render('/index.php', [
            'pageTitle' => 'منصة درع - نحو وعي سيبراني أفضل',
            'user'      => $user,
            'contents'  => $contents,
        ]);
    }
}
