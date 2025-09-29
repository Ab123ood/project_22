<?php

class ExamsController extends Controller {
    

    public function index(): void {
        $pageTitle = 'إدارة الاختبارات';
        try {
            $exams = Database::query("SELECT id, title, category, difficulty_level, duration_minutes, is_active, created_at FROM exams ORDER BY created_at DESC LIMIT 100")->fetchAll();
        } catch (Throwable $e) {
            $exams = [];
        }
        $this->render('admin/exams/index', compact('pageTitle', 'exams'));
    }

    public function create(): void {
        $pageTitle = 'إنشاء اختبار';
        $this->render('admin/exams/create', compact('pageTitle'));
    }

    public function store(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . $this->basePath() . '/admin/exams/create');
            return;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'category' => trim($_POST['category'] ?? 'general'),
            'difficulty_level' => in_array($_POST['difficulty_level'] ?? 'beginner', ['beginner','intermediate','advanced']) ? $_POST['difficulty_level'] : 'beginner',
            'duration_minutes' => (int)($_POST['duration_minutes'] ?? 30),
            'description' => trim($_POST['description'] ?? ''),
            'passing_score' => (int)($_POST['passing_score'] ?? 70),
            'max_attempts' => (int)($_POST['max_attempts'] ?? 3),
            'randomize_questions' => isset($_POST['randomize_questions']) ? 1 : 0,
            'show_results' => isset($_POST['show_results']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 1,
        ];

        $errors = [];
        if ($data['title'] === '') $errors[] = 'عنوان الاختبار مطلوب';
        if ($data['duration_minutes'] < 1) $errors[] = 'المدة يجب أن تكون دقيقة واحدة على الأقل';

        if (!empty($errors)) {
            $pageTitle = 'إنشاء اختبار';
            $old = $data;
            $this->render('admin/exams/create', compact('pageTitle','errors','old'));
            return;
        }

        try {
            $examId = ExamService::createExam($data);
            header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id=' . $examId);
            return;
        } catch (Throwable $e) {
            $errors = ['تعذّر حفظ الاختبار'];
            $pageTitle = 'إنشاء اختبار';
            $old = $data;
            $this->render('admin/exams/create', compact('pageTitle','errors','old'));
        }
    }

    public function addQuestion(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { 
            http_response_code(405); 
            echo 'Method Not Allowed'; 
            return; 
        }
        
        $examId = (int)($_POST['exam_id'] ?? 0);
        
        $questionData = [
            'question_type' => $_POST['question_type'] ?? 'text',
            'question_text' => trim($_POST['question_text'] ?? ''),
            'correct_answer' => trim($_POST['correct_answer'] ?? ''),
            'points' => (int)($_POST['points'] ?? 10),
            'order_index' => (int)($_POST['order_index'] ?? 0),
        ];
        
        if (isset($_POST['option_text']) && is_array($_POST['option_text'])) {
            $questionData['options'] = $_POST['option_text'];
            $questionData['correct_index'] = (int)($_POST['correct_index'] ?? 1);
        }
        
        $errors = [];
        if ($examId <= 0) $errors[] = 'exam_id مطلوب';
        if ($questionData['question_text'] === '') $errors[] = 'نص السؤال مطلوب';
        if ($questionData['question_type'] !== 'text' && $questionData['correct_answer'] === '') {
            $errors[] = 'الإجابة الصحيحة مطلوبة';
        }
        
        if (!empty($errors)) { 
            header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
            return; 
        }
        
        try {
            ExamService::addQuestion($examId, $questionData);
        } catch (Throwable $e) {
            $errors = ['تعذّر إضافة السؤال'];
            header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
            return;
        }
        
        header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
    }

    public function questions(): void {
        $examId = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;
        if ($examId <= 0) { http_response_code(400); echo 'Invalid exam_id'; return; }
        try {
            $exam = Database::query("SELECT id, title FROM exams WHERE id = :id", [':id' => $examId])->fetch();
            if (!$exam) { http_response_code(404); echo 'Exam not found'; return; }
            $questions = Database::query(
                "SELECT q.id, q.question_type, q.question_text, q.correct_answer, q.points, q.order_index
                 FROM exam_questions q
                 WHERE q.exam_id = :id
                 ORDER BY q.order_index, q.id",
                [':id' => $examId]
            )->fetchAll();
            $optionsByQ = [];
            if ($questions) {
                $qids = array_map(fn($q) => (int)$q['id'], $questions);
                if (!empty($qids)) {
                    $in = implode(',', array_fill(0, count($qids), '?'));
                    $stmt = Database::connection()->prepare("SELECT id, question_id, option_text, is_correct, order_index FROM question_options WHERE question_id IN ($in) ORDER BY question_id, order_index");
                    $stmt->execute($qids);
                    foreach ($stmt->fetchAll() as $opt) {
                        $qid = (int)$opt['question_id'];
                        if (!isset($optionsByQ[$qid])) $optionsByQ[$qid] = [];
                        $optionsByQ[$qid][] = $opt;
                    }
                }
            }
        } catch (Throwable $e) {
            $exam = null; $questions = []; $optionsByQ = [];
        }
        $pageTitle = 'أسئلة الاختبار';
        $this->render('admin/exams/questions', compact('pageTitle','exam','questions','optionsByQ'));
    }

    public function addOption(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $questionId = (int)($_POST['question_id'] ?? 0);
        $examId = (int)($_POST['exam_id'] ?? 0);
        $text = trim($_POST['option_text'] ?? '');
        $isCorrect = isset($_POST['is_correct']) ? 1 : 0;
        $orderIndex = (int)($_POST['order_index'] ?? 0);
        
        if ($questionId > 0 && $text !== '') {
            try { 
                Database::query("INSERT INTO question_options (question_id, option_text, is_correct, order_index) VALUES (:qid, :t, :c, :order)", 
                    [':qid'=>$questionId, ':t'=>$text, ':c'=>$isCorrect, ':order'=>$orderIndex]); 
            } catch (Throwable $e) {
                $errors = ['تعذّر إضافة الخيار'];
                header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
                return;
            }
        }
        header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
    }

    public function deleteQuestion(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $examId = (int)($_POST['exam_id'] ?? 0);
        $questionId = (int)($_POST['question_id'] ?? 0);
        if ($questionId > 0) {
            try { 
                // حذف الخيارات أولاً
                Database::query("DELETE FROM question_options WHERE question_id = :id", [':id'=>$questionId]);
                // ثم حذف السؤال
                Database::query("DELETE FROM exam_questions WHERE id = :id", [':id'=>$questionId]); 
            } catch (Throwable $e) {
                $errors = ['تعذّر حذف السؤال'];
                header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
                return;
            }
        }
        header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
    }

    public function deleteOption(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $examId = (int)($_POST['exam_id'] ?? 0);
        $optionId = (int)($_POST['option_id'] ?? 0);
        if ($optionId > 0) {
            try { Database::query("DELETE FROM question_options WHERE id = :id", [':id'=>$optionId]); } catch (Throwable $e) {
                $errors = ['تعذّر حذف الخيار'];
                header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
                return;
            }
        }
        header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
    }

    public function setCorrectOption(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $examId = (int)($_POST['exam_id'] ?? 0);
        $questionId = (int)($_POST['question_id'] ?? 0);
        $optionId = (int)($_POST['option_id'] ?? 0);
        if ($questionId > 0 && $optionId > 0) {
            try {
                Database::begin();
                Database::query("UPDATE question_options SET is_correct = 0 WHERE question_id = :qid", [':qid'=>$questionId]);
                Database::query("UPDATE question_options SET is_correct = 1 WHERE id = :oid", [':oid'=>$optionId]);
                Database::commit();
            } catch (Throwable $e) { 
                Database::rollback(); 
                $errors = ['تعذّر تحديث الخيار'];
                header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
                return;
            }
        }
        header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
    }

    public function updateQuestionText(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $examId = (int)($_POST['exam_id'] ?? 0);
        $questionId = (int)($_POST['question_id'] ?? 0);
        $text = trim($_POST['question_text'] ?? '');
        if ($examId > 0 && $questionId > 0 && $text !== '') {
            try { Database::query("UPDATE exam_questions SET question_text = :t WHERE id = :id", [':t'=>$text, ':id'=>$questionId]); } catch (Throwable $e) {
                $errors = ['تعذّر تحديث السؤال'];
                header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
                return;
            }
        }
        header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
    }

    public function updateOptionText(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $examId = (int)($_POST['exam_id'] ?? 0);
        $optionId = (int)($_POST['option_id'] ?? 0);
        $text = trim($_POST['option_text'] ?? '');
        if ($examId > 0 && $optionId > 0 && $text !== '') {
            try { Database::query("UPDATE question_options SET option_text = :t WHERE id = :id", [':t'=>$text, ':id'=>$optionId]); } catch (Throwable $e) {
                $errors = ['تعذّر تحديث الخيار'];
                header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
                return;
            }
        }
        header('Location: ' . $this->basePath() . '/admin/exams/questions?exam_id='.$examId);
    }

    public function edit(): void {
        $examId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($examId <= 0) { http_response_code(400); echo 'Invalid exam ID'; return; }
        
        try {
            $exam = Database::query("SELECT * FROM exams WHERE id = :id", [':id' => $examId])->fetch();
            if (!$exam) { http_response_code(404); echo 'Exam not found'; return; }
        } catch (Throwable $e) {
            $exam = null;
        }
        
        $pageTitle = 'تعديل الاختبار';
        $this->render('admin/exams/edit', compact('pageTitle', 'exam'));
    }

    public function update(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . $this->basePath() . '/admin/exams');
            return;
        }

        $examId = (int)($_POST['exam_id'] ?? 0);
        if ($examId <= 0) { http_response_code(400); echo 'Invalid exam ID'; return; }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'category' => trim($_POST['category'] ?? 'general'),
            'difficulty_level' => in_array($_POST['difficulty_level'] ?? 'beginner', ['beginner','intermediate','advanced']) ? $_POST['difficulty_level'] : 'beginner',
            'duration_minutes' => (int)($_POST['duration_minutes'] ?? 30),
            'description' => trim($_POST['description'] ?? ''),
            'passing_score' => (int)($_POST['passing_score'] ?? 70),
            'max_attempts' => (int)($_POST['max_attempts'] ?? 3),
            'randomize_questions' => isset($_POST['randomize_questions']) ? 1 : 0,
            'show_results' => isset($_POST['show_results']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 1,
        ];

        $errors = [];
        if ($data['title'] === '') $errors[] = 'عنوان الاختبار مطلوب';
        if ($data['duration_minutes'] < 1) $errors[] = 'المدة يجب أن تكون دقيقة واحدة على الأقل';

        if (!empty($errors)) {
            $pageTitle = 'تعديل الاختبار';
            $exam = (object)$data;
            $this->render('admin/exams/edit', compact('pageTitle','errors','exam'));
            return;
        }

        try {
            ExamService::updateExam($examId, $data);
            header('Location: ' . $this->basePath() . '/admin/exams');
            return;
        } catch (Throwable $e) {
            $errors = ['تعذّر تحديث الاختبار'];
            $pageTitle = 'تعديل الاختبار';
            $exam = (object)$data;
            $this->render('admin/exams/edit', compact('pageTitle','errors','exam'));
        }
    }

    public function delete(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        $examId = (int)($_POST['exam_id'] ?? 0);
        if ($examId > 0) {
            try {
                ExamService::deleteExam($examId);
            } catch (Throwable $e) {
                $errors = ['تعذّر حذف الاختبار'];
                header('Location: ' . $this->basePath() . '/admin/exams');
                return;
            }
        }
        header('Location: ' . $this->basePath() . '/admin/exams');
    }
}
