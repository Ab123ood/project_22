<?php
class SurveysController extends Controller {

    public function index(): void {
        $pageTitle = 'إدارة الاستبيانات';
        try {
            $sql = "
                SELECT 
                  s.id,
                  s.title,
                  s.category,
                  CASE WHEN s.is_active=1 THEN 'published' ELSE 'draft' END AS status,
                  s.created_at,
                  (SELECT COUNT(*) FROM survey_responses sr WHERE sr.survey_id = s.id) AS total_responses,
                  (SELECT COUNT(*) FROM survey_responses sr WHERE sr.survey_id = s.id AND sr.is_completed = 1) AS completed_responses,
                  (SELECT COUNT(*) FROM survey_responses sr WHERE sr.survey_id = s.id AND sr.created_at >= (NOW() - INTERVAL 7 DAY)) AS responses_last7,
                  (SELECT ROUND(AVG(sa.rating_value), 2) FROM survey_answers sa JOIN survey_responses sr2 ON sr2.id = sa.response_id WHERE sr2.survey_id = s.id AND sa.rating_value IS NOT NULL) AS avg_rating
                FROM surveys s
                ORDER BY s.created_at DESC
                LIMIT 100";
            $surveys = Database::query($sql)->fetchAll();

            foreach ($surveys as &$sv) {
                $tot = (int)($sv['total_responses'] ?? 0);
                $cmp = (int)($sv['completed_responses'] ?? 0);
                $sv['completion_rate'] = $tot > 0 ? round(($cmp / $tot) * 100) : 0;
            }
            unset($sv);
        } catch (Throwable $e) { $surveys = []; }

        $this->render('admin/surveys/index', compact('pageTitle','surveys'));
    }

    public function create(): void {
        $pageTitle = 'إنشاء استبيان';
        $this->render('admin/surveys/create', compact('pageTitle'));
    }

    public function store(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . $this->basePath() . '/admin/surveys/create');
            return;
        }

        $title       = trim($_POST['title'] ?? '');
        $category    = trim($_POST['category'] ?? 'عام');
        $description = trim($_POST['description'] ?? '');
        $status      = in_array($_POST['status'] ?? 'draft', ['draft','published','archived']) ? $_POST['status'] : 'draft';

        $isAnonymous = isset($_POST['anonymous']) ? 1 : 0; 
        $startDate   = trim($_POST['availability_from'] ?? '');
        $endDate     = trim($_POST['availability_to'] ?? '');
        $startDate   = ($startDate === '') ? null : $startDate;
        $endDate     = ($endDate === '') ? null : $endDate;

        $errors = [];
        if ($title === '') {
            $errors[] = 'عنوان الاستبيان مطلوب';
        }

        if (session_status() === PHP_SESSION_NONE) { @session_start(); }
        $createdBy = (int)($_SESSION['user_id'] ?? 0);

        if ($createdBy <= 0) {
            try {
                $row = Database::query('SELECT id FROM users ORDER BY id ASC LIMIT 1')->fetch();
                $createdBy = (int)($row['id'] ?? 0);
            } catch (Throwable $e) {}
        }

        if ($createdBy <= 0) {
            $errors[] = 'لا يوجد مستخدم لإنشاء الاستبيان. فضلاً أضف مستخدماً أولاً.';
        }

        if (!empty($errors)) {
            $pageTitle = 'إنشاء استبيان';
            $old = compact('title','category','description','status','isAnonymous','startDate','endDate');
            $this->render('admin/surveys/create', compact('pageTitle','errors','old'));
            return;
        }

        try {
            $isActive = ($status === 'published') ? 1 : 0;

            $sql = "
                INSERT INTO surveys (
                    id,
                    title,
                    description,
                    status,
                    category,
                    is_anonymous,
                    is_active,
                    start_date,
                    end_date,
                    created_by,
                    created_at,
                    updated_at
                ) VALUES (
                    NULL,
                    :t,
                    :d,
                    :s,
                    :c,
                    :ian,
                    :ia,
                    :sd,
                    :ed,
                    :u,
                    NOW(),
                    NOW()
                )
            ";

            $params = [
                ':t'   => $title,
                ':d'   => $description,
                ':s'   => $status,
                ':c'   => $category,
                ':ian' => $isAnonymous,
                ':ia'  => $isActive,
                ':sd'  => $startDate,
                ':ed'  => $endDate,
                ':u'   => $createdBy
            ];

            $stmt = Database::connection()->prepare($sql);
            $stmt->execute($params);

            $surveyId = (int) Database::connection()->lastInsertId();
            header('Location: ' . $this->basePath() . '/admin/surveys/questions?survey_id=' . $surveyId);
            return;

        } catch (Throwable $e) {
            $errors = [
                'تعذّر حفظ الاستبيان.',
                'تفاصيل: ' . $e->getMessage()
            ];
            $pageTitle = 'إنشاء استبيان';
            $old = compact('title','category','description','status','isAnonymous','startDate','endDate');
            $this->render('admin/surveys/create', compact('pageTitle','errors','old'));
        }
    }

    public function questions(): void {
        $surveyId = (int)($_GET['survey_id'] ?? 0);
        if ($surveyId <= 0) { header('Location: ' . $this->basePath() . '/admin/surveys'); return; }
        try {
            $survey = Database::query('SELECT id, title FROM surveys WHERE id = :id', [':id'=>$surveyId])->fetch();
            if (!$survey) { header('Location: ' . $this->basePath() . '/admin/surveys'); return; }
            $questions = Database::query(
                'SELECT id, type, text, order_index, section_title FROM survey_questions WHERE survey_id = :id ORDER BY order_index, id',
                [':id'=>$surveyId]
            )->fetchAll();
            $optionsByQ = [];
            if ($questions) {
                $qids = array_map(fn($q)=>(int)$q['id'],$questions);
                if (!empty($qids)) {
                    $in = implode(',', array_fill(0,count($qids),'?'));
                    $stmt = Database::connection()->prepare("SELECT id, question_id, text FROM survey_options WHERE question_id IN ($in) ORDER BY id");
                    $stmt->execute($qids);
                    $opts = $stmt->fetchAll();
                    foreach ($opts as $opt) {
                        $qid = (int)$opt['question_id'];
                        if (!isset($optionsByQ[$qid])) $optionsByQ[$qid] = [];
                        $optionsByQ[$qid][] = $opt;
                    }
                }
            }
        } catch (Throwable $e) { $survey=null; $questions=[]; $optionsByQ=[]; }

        if (session_status() === PHP_SESSION_NONE) { @session_start(); }
        $flashError = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_error']);

        $pageTitle = 'أسئلة الاستبيان';
        $this->render('admin/surveys/questions', compact('pageTitle','survey','questions','optionsByQ','flashError'));
    }

    public function addQuestion(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $surveyId = (int)($_POST['survey_id'] ?? 0);
        $type = $_POST['type'] ?? 'mcq'; 
        $text = trim($_POST['text'] ?? '');
        $order = (int)($_POST['order_index'] ?? 0);
        $sectionTitle = trim($_POST['section_title'] ?? '');
        $sectionTitle = ($sectionTitle === '') ? null : $sectionTitle;

        $isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

        if ($surveyId<=0 || $text==='') {
            if ($isAjax) { http_response_code(400); header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'survey_id أو نص السؤال غير صالح']); return; }
            header('Location: ' . $this->basePath() . '/admin/surveys/questions?survey_id='.$surveyId); return;
        }
        try {
            $dbType = $type;
            switch ($type) {
                case 'mcq':
                    $dbType = 'multiple_choice';
                    break;
                case 'checkbox':
                    $dbType = 'multiple_choice';
                    break;
                case 'text':
                    $dbType = 'text';
                    break;
                case 'likert':
                    $dbType = 'rating';
                    break;
                case 'ranking':
                    $dbType = 'multiple_choice';
                    break;
                case 'rating':
                    $dbType = 'rating';
                    break;
                case 'yesno':
                    $dbType = 'yes_no';
                    break;
                default:
                    $dbType = 'single_choice';
                    break;
            }

            Database::query(
                'INSERT INTO survey_questions (survey_id, section_title, question_type, question_text, order_index) VALUES (:sid,:sec,:t,:txt,:ord)',
                [':sid'=>$surveyId, ':sec'=>$sectionTitle, ':t'=>$dbType, ':txt'=>$text, ':ord'=>$order]
            );

            $qid = (int) Database::connection()->lastInsertId();
            if ($type === 'mcq' || $type === 'checkbox') {
                $options = $_POST['option_text'] ?? [];
                if (is_array($options)) {
                    foreach ($options as $optText) {
                        $t = trim((string)$optText); if ($t==='') continue;
                        Database::query('INSERT INTO survey_options (question_id, text) VALUES (:qid,:t)', [':qid'=>$qid, ':t'=>$t]);
                    }
                }
            }
            if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok'=>true,'question_id'=>$qid]); return; }
        } catch (Throwable $e) {
            if ($isAjax) { http_response_code(500); header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); return; }
            if (session_status() === PHP_SESSION_NONE) { @session_start(); }
            $_SESSION['flash_error'] = 'فشل حفظ السؤال: ' . $e->getMessage();
        }
        header('Location: ' . $this->basePath() . '/admin/surveys/questions?survey_id='.$surveyId);
    }

    public function addOption(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $surveyId = (int)($_POST['survey_id'] ?? 0);
        $questionId = (int)($_POST['question_id'] ?? 0);
        $text = trim($_POST['option_text'] ?? '');
        if ($questionId>0 && $text!=='') {
            Database::query('INSERT INTO survey_options (question_id, text) VALUES (:qid,:t)', [':qid'=>$questionId, ':t'=>$text]);
        }
        header('Location: ' . $this->basePath() . '/admin/surveys/questions?survey_id='.$surveyId);
    }

    public function deleteQuestion(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $surveyId = (int)($_POST['survey_id'] ?? 0);
        $questionId = (int)($_POST['question_id'] ?? 0);
        if ($questionId>0) { Database::query('DELETE FROM survey_questions WHERE id = :id', [':id'=>$questionId]); }
        header('Location: ' . $this->basePath() . '/admin/surveys/questions?survey_id='.$surveyId);
    }

    public function deleteOption(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $surveyId = (int)($_POST['survey_id'] ?? 0);
        $optionId = (int)($_POST['option_id'] ?? 0);
        if ($optionId>0) {
            Database::query('DELETE FROM survey_options WHERE id = :id', [':id'=>$optionId]);
        }
        header('Location: ' . $this->basePath() . '/admin/surveys/questions?survey_id='.$surveyId);
    }

    public function updateQuestionText(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $surveyId = (int)($_POST['survey_id'] ?? 0);
        $questionId = (int)($_POST['question_id'] ?? 0);
        $text = trim($_POST['text'] ?? '');
        if ($surveyId>0 && $questionId>0 && $text!=='') { Database::query('UPDATE survey_questions SET question_text = :t WHERE id = :id', [':t'=>$text, ':id'=>$questionId]); }
        header('Location: ' . $this->basePath() . '/admin/surveys/questions?survey_id='.$surveyId);
    }

    public function updateOptionText(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $surveyId = (int)($_POST['survey_id'] ?? 0);
        $optionId = (int)($_POST['option_id'] ?? 0);
        $text = trim($_POST['text'] ?? '');
        if ($surveyId>0 && $optionId>0 && $text!=='') { Database::query('UPDATE survey_options SET text = :t WHERE id = :id', [':t'=>$text, ':id'=>$optionId]); }
        header('Location: ' . $this->basePath() . '/admin/surveys/questions?survey_id='.$surveyId);
    }

    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { header('Location: ' . $this->basePath() . '/admin/surveys'); return; }
        $pageTitle = 'تعديل الاستبيان';
        try {
            $survey = Database::query('SELECT id, title, description, status, category, is_anonymous, is_active, start_date, end_date FROM surveys WHERE id = :id', [':id'=>$id])->fetch();
            if (!$survey) { header('Location: ' . $this->basePath() . '/admin/surveys'); return; }
        } catch (Throwable $e) { header('Location: ' . $this->basePath() . '/admin/surveys'); return; }
        $this->render('admin/surveys/edit', compact('pageTitle','survey'));
    }

    public function update(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { header('Location: ' . $this->basePath() . '/admin/surveys'); return; }
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) { header('Location: ' . $this->basePath() . '/admin/surveys'); return; }

        $title       = trim($_POST['title'] ?? '');
        $category    = trim($_POST['category'] ?? 'عام');
        $description = trim($_POST['description'] ?? '');
        $status      = in_array($_POST['status'] ?? 'draft', ['draft','published','archived']) ? $_POST['status'] : 'draft';
        $isAnonymous = isset($_POST['anonymous']) ? 1 : 0;
        $startDate   = trim($_POST['availability_from'] ?? '');
        $endDate     = trim($_POST['availability_to'] ?? '');
        $startDate   = ($startDate === '') ? null : $startDate;
        $endDate     = ($endDate === '') ? null : $endDate;

        $errors = [];
        if ($title === '') { $errors[] = 'عنوان الاستبيان مطلوب'; }

        if (!empty($errors)) {
            $pageTitle = 'تعديل الاستبيان';
            $survey = compact('id','title','category','description','status','isAnonymous','startDate','endDate');
            $this->render('admin/surveys/edit', compact('pageTitle','survey','errors'));
            return;
        }

        try {
            $isActive = ($status === 'published') ? 1 : 0;
            $sql = 'UPDATE surveys SET title=:t, description=:d, status=:s, category=:c, is_anonymous=:ian, is_active=:ia, start_date=:sd, end_date=:ed, updated_at=NOW() WHERE id=:id';
            Database::query($sql, [
                ':t'=>$title,
                ':d'=>$description,
                ':s'=>$status,
                ':c'=>$category,
                ':ian'=>$isAnonymous,
                ':ia'=>$isActive,
                ':sd'=>$startDate,
                ':ed'=>$endDate,
                ':id'=>$id,
            ]);
            header('Location: ' . $this->basePath() . '/admin/surveys');
            return;
        } catch (Throwable $e) {
            $pageTitle = 'تعديل الاستبيان';
            $errors = ['فشل تحديث الاستبيان: ' . $e->getMessage()];
            $survey = compact('id','title','category','description','status','isAnonymous','startDate','endDate');
            $this->render('admin/surveys/edit', compact('pageTitle','survey','errors'));
        }
    }

    public function delete(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'طريقة الطلب غير صالحة']); return; }
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'معرّف غير صالح']); return; }
        try {
            Database::query('DELETE FROM surveys WHERE id = :id', [':id'=>$id]);
            header('Content-Type: application/json'); echo json_encode(['success'=>true,'message'=>'تم حذف الاستبيان بنجاح']);
        } catch (Throwable $e) {
            header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'فشل حذف الاستبيان']);
        }
    }

    public function analysis(): void {
        $surveyId = (int)($_GET['survey_id'] ?? 0);
        if ($surveyId <= 0) {
            http_response_code(400);
            echo 'survey_id مطلوب';
            return;
        }

        try {
            $survey = Database::query('SELECT id, title, category, is_active, created_at FROM surveys WHERE id = ?', [$surveyId])->fetch();
            if (!$survey) { http_response_code(404); echo 'الاستبيان غير موجود'; return; }

            $questions = Database::query('SELECT id, question_text, question_type FROM survey_questions WHERE survey_id = ? ORDER BY order_index, id', [$surveyId])->fetchAll();

            $optionsByQ = Database::query('SELECT id, question_id, option_text FROM survey_question_options WHERE question_id IN (SELECT id FROM survey_questions WHERE survey_id = ?)', [$surveyId])->fetchAll();
            $optMap = [];
            foreach ($optionsByQ as $o) { $optMap[(int)$o['question_id']][] = $o; }

            $analysis = [];
            foreach ($questions as $q) {
                $qid = (int)$q['id'];
                $type = $q['question_type'];
                $item = [
                    'id' => $qid,
                    'text' => $q['question_text'],
                    'type' => $type,
                    'total' => 0,
                    'distribution' => [],
                    'rating' => [ 'avg' => null, 'counts' => [] ],
                    'samples' => [],
                ];

                $item['total'] = (int) Database::query(
                    'SELECT COUNT(sa.id)
                     FROM survey_answers sa
                     JOIN survey_responses sr ON sr.id = sa.response_id
                     WHERE sr.survey_id = ? AND sa.question_id = ?',
                    [$surveyId, $qid]
                )->fetchColumn();

                if (in_array($type, ['multiple_choice','single_choice','yes_no'], true)) {
                    $dist = Database::query(
                        'SELECT o.id AS option_id, o.option_text, COUNT(sa.id) AS cnt
                         FROM survey_question_options o
                         LEFT JOIN survey_answers sa ON sa.selected_option_id = o.id
                         LEFT JOIN survey_responses sr ON sr.id = sa.response_id
                         WHERE o.question_id = ? AND (sr.survey_id = ? OR sr.survey_id IS NULL)
                         GROUP BY o.id, o.option_text
                         ORDER BY o.id',
                        [$qid, $surveyId]
                    )->fetchAll();
                    $item['distribution'] = $dist ?: [];
                } elseif ($type === 'rating') {
                    $row = Database::query(
                        'SELECT ROUND(AVG(sa.rating_value),2) AS avg_rating
                         FROM survey_answers sa
                         JOIN survey_responses sr ON sr.id = sa.response_id
                         WHERE sr.survey_id = ? AND sa.question_id = ? AND sa.rating_value IS NOT NULL',
                        [$surveyId, $qid]
                    )->fetch();
                    $item['rating']['avg'] = $row && $row['avg_rating'] !== null ? (float)$row['avg_rating'] : null;
                    $counts = Database::query(
                        'SELECT sa.rating_value, COUNT(*) AS cnt
                         FROM survey_answers sa
                         JOIN survey_responses sr ON sr.id = sa.response_id
                         WHERE sr.survey_id = ? AND sa.question_id = ? AND sa.rating_value IS NOT NULL
                         GROUP BY sa.rating_value
                         ORDER BY sa.rating_value',
                        [$surveyId, $qid]
                    )->fetchAll();
                    $item['rating']['counts'] = $counts ?: [];
                } else { // text
                    $samples = Database::query(
                        "SELECT sa.answer_text, sa.created_at
                         FROM survey_answers sa
                         JOIN survey_responses sr ON sr.id = sa.response_id
                         WHERE sr.survey_id = ? AND sa.question_id = ? AND sa.answer_text IS NOT NULL AND sa.answer_text <> ''
                         ORDER BY sa.created_at DESC
                         LIMIT 10",
                        [$surveyId, $qid]
                    )->fetchAll();
                    $item['samples'] = $samples ?: [];
                }

                $analysis[] = $item;
            }

            $this->render('admin/surveys/analysis', [
                'survey' => $survey,
                'analysis' => $analysis,
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo 'حدث خطأ أثناء التحليل';
        }
    }
}
