<?php
// app/controllers/ReportsController.php
class ReportsController extends Controller {
    private function dt(string $param, string $default = ''): string {
        $v = trim($_GET[$param] ?? $default);
        return $v;
    }

    /**
     * Helper: build BETWEEN filters for created_at-like columns
     * Returns [whereClauseFragment, params]
     */
    private function buildDateFilter(string $column, string $from, string $to): array {
        $where = [];
        $params = [];
        if ($from !== '') { $where[] = "$column >= :{$column}_from"; $params[":{$column}_from"] = $from . ' 00:00:00'; }
        if ($to   !== '') { $where[] = "$column <= :{$column}_to";   $params[":{$column}_to"]   = $to   . ' 23:59:59'; }
        $sql = $where ? (' WHERE ' . implode(' AND ', $where)) : '';
        return [$sql, $params];
    }

    public function index(): void {
        $pageTitle = 'التقارير والإحصائيات';

        // فلاتر
        $from = $this->dt('from'); // YYYY-MM-DD
        $to   = $this->dt('to');   // YYYY-MM-DD
        // أنواع موسعة: overview|exams|exam_attempts|content|content_views|users|surveys|points
        $type = $this->dt('type', 'overview');

        // KPIs محسّنة
        $kpis = [
            'active_users' => 0,
            'users_count' => 0,
            'exams_count' => 0,
            'exam_attempts' => 0,
            'avg_exam_score' => null,
            'completed_attempts' => 0,
            'surveys_count' => 0,
            'survey_responses' => 0,
            'content_count' => 0,
            'content_views' => 0,
            'distinct_viewers' => 0,
            'points_total' => 0,
        ];

        // المستخدمون
        try { $kpis['users_count'] = (int) Database::query('SELECT COUNT(*) c FROM users')->fetch()['c']; } catch (Throwable $e) {}
        try { $kpis['active_users'] = (int) Database::query("SELECT COUNT(*) c FROM users WHERE status='active'")->fetch()['c']; } catch (Throwable $e) {}

        // الاختبارات
        try { $kpis['exams_count'] = (int) Database::query('SELECT COUNT(*) c FROM exams')->fetch()['c']; } catch (Throwable $e) {}
        try { $kpis['exam_attempts'] = (int) Database::query('SELECT COUNT(*) c FROM exam_attempts')->fetch()['c']; } catch (Throwable $e) {}
        try { $kpis['completed_attempts'] = (int) Database::query("SELECT COUNT(*) c FROM exam_attempts WHERE status='completed'")->fetch()['c']; } catch (Throwable $e) {}
        try {
            $row = Database::query("SELECT AVG(score) avg_score FROM exam_attempts WHERE status='completed'")->fetch();
            if ($row && $row['avg_score'] !== null) { $kpis['avg_exam_score'] = round((float)$row['avg_score'], 2); }
        } catch (Throwable $e) {}

        // الاستبيانات
        try { $kpis['surveys_count'] = (int) Database::query('SELECT COUNT(*) c FROM surveys')->fetch()['c']; } catch (Throwable $e) {}
        try { $kpis['survey_responses'] = (int) Database::query('SELECT COUNT(*) c FROM survey_responses')->fetch()['c']; } catch (Throwable $e) {}

        // المحتوى
        try { $kpis['content_count'] = (int) Database::query('SELECT COUNT(*) c FROM content')->fetch()['c']; } catch (Throwable $e) {}
        try { $kpis['content_views'] = (int) Database::query('SELECT COUNT(*) c FROM content_views')->fetch()['c']; } catch (Throwable $e) {}
        try { $kpis['distinct_viewers'] = (int) Database::query('SELECT COUNT(DISTINCT user_id) c FROM content_views')->fetch()['c']; } catch (Throwable $e) {}

        // النقاط
        try { $row = Database::query('SELECT COALESCE(SUM(points),0) s FROM points_log')->fetch(); if ($row) { $kpis['points_total'] = (int)$row['s']; } } catch (Throwable $e) {}

        // تجميع الصفوف حسب النوع + تطبيق الفلاتر (مبسّط عبر خريطة أقسام)
        $rows = [];
        $export = isset($_GET['export']) && strtolower($_GET['export']) === 'csv';

        $sections = [
            'exams' => [
                'date_col' => 'created_at',
                'sql' => 'SELECT id, title AS item, "exam" AS kind, NULL AS user, created_at AS dt FROM exams {WHERE} ORDER BY created_at DESC LIMIT 1000',
            ],
            'exam_attempts' => [
                'date_col' => 'created_at',
                'sql' => 'SELECT ea.id, CONCAT("Attempt #", ea.id, " - ", e.title, " (", ea.score, "%)") AS item, "exam_attempt" AS kind, u.email AS user, ea.created_at AS dt FROM exam_attempts ea LEFT JOIN users u ON u.id = ea.user_id LEFT JOIN exams e ON e.id = ea.exam_id {WHERE} ORDER BY ea.created_at DESC LIMIT 1000',
            ],
            'content' => [
                'date_col' => 'created_at',
                'sql' => 'SELECT id, title AS item, "content" AS kind, NULL AS user, created_at AS dt FROM content {WHERE} ORDER BY created_at DESC LIMIT 1000',
            ],
            'content_views' => [
                'date_col' => 'viewed_at',
                'sql' => 'SELECT cv.id, CONCAT("View #", cv.id, " - Content #", cv.content_id) AS item, "content_view" AS kind, u.email AS user, cv.viewed_at AS dt FROM content_views cv LEFT JOIN users u ON u.id = cv.user_id {WHERE} ORDER BY cv.viewed_at DESC LIMIT 1000',
            ],
            'users' => [
                'date_col' => 'created_at',
                'sql' => 'SELECT id, email AS item, "user" AS kind, email AS user, created_at AS dt FROM users {WHERE} ORDER BY created_at DESC LIMIT 1000',
                'fallback' => 'SELECT id, CONCAT("user#", id) AS item, "user" AS kind, NULL AS user, NOW() AS dt FROM users ORDER BY id DESC LIMIT 1000',
            ],
            'surveys' => [
                'date_col' => 'created_at',
                'sql' => 'SELECT id, title AS item, "survey" AS kind, NULL AS user, created_at AS dt FROM surveys {WHERE} ORDER BY created_at DESC LIMIT 1000',
            ],
            'survey_responses' => [
                'date_col' => 'submitted_at',
                'sql' => 'SELECT sr.id, CONCAT("Response #", sr.id, " - Survey #", sr.survey_id) AS item, "survey_response" AS kind, u.email AS user, sr.submitted_at AS dt FROM survey_responses sr LEFT JOIN users u ON u.id = sr.user_id {WHERE} ORDER BY sr.submitted_at DESC LIMIT 1000',
            ],
            'points' => [
                'date_col' => 'created_at',
                'sql' => 'SELECT id, CONCAT("Points: ", points, " (", COALESCE(action, ""), ")") AS item, "points" AS kind, CAST(user_id AS CHAR) AS user, created_at AS dt FROM points_log {WHERE} ORDER BY created_at DESC LIMIT 1000',
            ],
        ];

        $wanted = $type === 'overview' ? array_keys($sections) : [$type];
        foreach ($wanted as $sec) {
            if (!isset($sections[$sec])) { continue; }
            $conf = $sections[$sec];
            try {
                [$wh, $p] = $this->buildDateFilter($conf['date_col'], $from, $to);
                $sql = str_replace('{WHERE}', $wh, $conf['sql']);
                $rows = array_merge($rows, Database::query($sql, $p)->fetchAll());
            } catch (Throwable $e) {
                if ($sec === 'users' && isset($conf['fallback'])) {
                    try { $rows = array_merge($rows, Database::query($conf['fallback'])->fetchAll()); } catch (Throwable $ee) {}
                }
            }
        }

        // ترتيب موحّد بحسب التاريخ إن توفّر
        usort($rows, function($a,$b){
            $ad = $a['dt'] ?? null; $bd = $b['dt'] ?? null;
            if ($ad === $bd) return 0;
            if ($ad === null) return 1;
            if ($bd === null) return -1;
            return strcmp($bd, $ad); // نزولياً
        });

        // CSV export if requested
        if ($export) {
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="reports_export.csv"');
            $out = fopen('php://output', 'w');
            // header row
            fputcsv($out, ['id','item','kind','user','dt']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['id'] ?? '',
                    $r['item'] ?? '',
                    $r['kind'] ?? '',
                    $r['user'] ?? '',
                    $r['dt'] ?? '',
                ]);
            }
            fclose($out);
            return;
        }

        // تمرير البيانات إلى الواجهة
        $this->render('admin/reports/index', compact('pageTitle','kpis','rows','from','to','type'));
    }
}
