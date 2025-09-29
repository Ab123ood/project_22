<?php
/**
 * Exam Service
 * Handles exam-related business logic and operations
 */
class ExamService {
    /**
     * Create a new exam
     * @param array $data Exam data
     * @return int Exam ID
     * @throws Exception
     */
    public static function createExam(array $data): int {
        $sql = "INSERT INTO exams (title, category, difficulty_level, duration_minutes, description, 
                passing_score, max_attempts, randomize_questions, show_results, is_active, created_at, updated_at)
                VALUES (:title, :category, :difficulty, :duration, :description, :passing_score, 
                :max_attempts, :randomize, :show_results, :is_active, NOW(), NOW())";
        
        try {
            Database::query($sql, [
                ':title' => $data['title'],
                ':category' => $data['category'] ?? 'general',
                ':difficulty' => $data['difficulty_level'] ?? 'beginner',
                ':duration' => $data['duration_minutes'] ?? 30,
                ':description' => $data['description'] ?? '',
                ':passing_score' => $data['passing_score'] ?? 70,
                ':max_attempts' => $data['max_attempts'] ?? 3,
                ':randomize' => $data['randomize_questions'] ?? 0,
                ':show_results' => $data['show_results'] ?? 1,
                ':is_active' => $data['is_active'] ?? 1,
            ]);
            return (int) Database::connection()->lastInsertId();
        } catch (Exception $e) {
            error_log('Failed to create exam: ' . $e->getMessage());
            throw new Exception('Failed to create exam');
        }
    }

    /**
     * Add a question to an exam
     * @param int $examId Exam ID
     * @param array $questionData Question data
     * @return int Question ID
     * @throws Exception
     */
    public static function addQuestion(int $examId, array $questionData): int {
        $questionType = self::normalizeQuestionType($questionData['question_type']);
        
        try {
            Database::query(
                "INSERT INTO exam_questions (exam_id, question_text, question_type, correct_answer, points, order_index) 
                 VALUES (:exam_id, :text, :type, :correct_answer, :points, :order_index)",
                [
                    ':exam_id' => $examId,
                    ':text' => $questionData['question_text'],
                    ':type' => $questionType,
                    ':correct_answer' => $questionData['correct_answer'],
                    ':points' => $questionData['points'] ?? 10,
                    ':order_index' => $questionData['order_index'] ?? 0
                ]
            );
            $questionId = (int) Database::connection()->lastInsertId();
            self::addQuestionOptions($questionId, $questionType, $questionData);
            return $questionId;
        } catch (Exception $e) {
            error_log('Failed to add question: ' . $e->getMessage());
            throw new Exception('Failed to add question');
        }
    }

    /**
     * Normalize question type to standard format
     * @param string $type Question type
     * @return string Normalized question type
     */
    private static function normalizeQuestionType(string $type): string {
        $type = strtolower(trim($type));
        if (in_array($type, ['mcq', 'multiple_choice', 'single', 'single_choice'], true)) {
            return 'multiple_choice';
        } elseif (in_array($type, ['truefalse', 'true_false', 'boolean', 'yesno', 'yes_no'], true)) {
            return 'true_false';
        }
        return 'text';
    }

    /**
     * Add options for a question based on its type
     * @param int $questionId Question ID
     * @param string $questionType Question type
     * @param array $questionData Question data
     * @return void
     */
    private static function addQuestionOptions(int $questionId, string $questionType, array $questionData): void {
        if ($questionType === 'true_false') {
            self::addTrueFalseOptions($questionId, $questionData['correct_answer']);
        } elseif ($questionType === 'multiple_choice' && isset($questionData['options'])) {
            self::addMultipleChoiceOptions($questionId, $questionData['options'], $questionData['correct_index'] ?? 1);
        }
    }

    /**
     * Add true/false options for a question
     * @param int $questionId Question ID
     * @param string $correctAnswer Correct answer
     * @return void
     */
    private static function addTrueFalseOptions(int $questionId, string $correctAnswer): void {
        $correctAnswerNormalized = self::normalizeTrueFalseAnswer($correctAnswer);
        
        Database::query(
            "INSERT INTO question_options (question_id, option_text, is_correct, order_index) VALUES (:qid, 'صحيح', :correct, 1)",
            [':qid' => $questionId, ':correct' => $correctAnswerNormalized === 'true' ? 1 : 0]
        );
        Database::query(
            "INSERT INTO question_options (question_id, option_text, is_correct, order_index) VALUES (:qid, 'خطأ', :correct, 2)",
            [':qid' => $questionId, ':correct' => $correctAnswerNormalized === 'false' ? 1 : 0]
        );
    }

    /**
     * Normalize true/false answer to boolean string
     * @param string $answer Answer text
     * @return string 'true' or 'false'
     */
    public static function normalizeTrueFalseAnswer(string $answer): string {
        $answer = mb_strtolower(trim($answer));
        if (in_array($answer, ['صحيح', 'true', '1', 'yes', 'نعم'], true)) {
            return 'true';
        }
        return 'false';
    }

    /**
     * Add multiple choice options for a question
     * @param int $questionId Question ID
     * @param array $options Array of option texts
     * @param int $correctIndex Index of correct option (1-based)
     * @return void
     */
    private static function addMultipleChoiceOptions(int $questionId, array $options, int $correctIndex): void {
        foreach ($options as $index => $optionText) {
            $optionText = trim($optionText);
            if ($optionText !== '') {
                $isCorrect = ($index + 1) === $correctIndex ? 1 : 0;
                Database::query(
                    "INSERT INTO question_options (question_id, option_text, is_correct, order_index) VALUES (:qid, :text, :correct, :order)",
                    [
                        ':qid' => $questionId,
                        ':text' => $optionText,
                        ':correct' => $isCorrect,
                        ':order' => $index + 1
                    ]
                );
            }
        }
    }

    /**
     * Evaluate an answer for a question
     * @param int $questionId Question ID
     * @param int|null $optionId Selected option ID (for multiple choice/true-false)
     * @param string|null $textAnswer Text answer (for text questions)
     * @return array Evaluation result
     */
    public static function evaluateAnswer(int $questionId, ?int $optionId = null, ?string $textAnswer = null): array {
        $question = Database::query(
            'SELECT question_type, correct_answer, points FROM exam_questions WHERE id = :qid',
            [':qid' => $questionId]
        )->fetch();
        
        if (!$question) {
            return [
                'is_correct' => false, 
                'user_answer' => '', 
                'correct_answer' => '', 
                'points_earned' => 0, 
                'max_points' => 0
            ];
        }
        
        $isCorrect = false;
        $userAnswer = '';
        $correctAnswer = '';
        
        if ($question['question_type'] === 'multiple_choice' || $question['question_type'] === 'true_false') {
            if ($optionId) {
                $selectedOption = Database::query(
                    'SELECT option_text, is_correct FROM question_options WHERE id = :oid AND question_id = :qid',
                    [':oid' => $optionId, ':qid' => $questionId]
                )->fetch();
                
                if ($selectedOption) {
                    $userAnswer = $selectedOption['option_text'];
                    $isCorrect = (int)$selectedOption['is_correct'] === 1;
                }
                
                $correctOption = Database::query(
                    'SELECT option_text FROM question_options WHERE question_id = :qid AND is_correct = 1 LIMIT 1',
                    [':qid' => $questionId]
                )->fetch();
                
                if ($correctOption) {
                    $correctAnswer = $correctOption['option_text'];
                }
            } else {
                $correctOption = Database::query(
                    'SELECT option_text FROM question_options WHERE question_id = :qid AND is_correct = 1 LIMIT 1',
                    [':qid' => $questionId]
                )->fetch();
                
                if ($correctOption) {
                    $correctAnswer = $correctOption['option_text'];
                }
                $userAnswer = 'لم يتم الاختيار';
            }
        } elseif ($question['question_type'] === 'text') {
            $userAnswer = trim($textAnswer ?? '');
            $correctAnswer = trim($question['correct_answer']);
            $isCorrect = strcasecmp($userAnswer, $correctAnswer) === 0;
        }
        
        $pointsEarned = $isCorrect ? (int)$question['points'] : 0;
        
        return [
            'is_correct' => $isCorrect,
            'user_answer' => $userAnswer,
            'correct_answer' => $correctAnswer,
            'points_earned' => $pointsEarned,
            'max_points' => (int)$question['points']
        ];
    }

    /**
     * Save an answer for an exam attempt
     * @param int $attemptId Exam attempt ID
     * @param int $questionId Question ID
     * @param int|null $optionId Selected option ID
     * @param string|null $textAnswer Text answer
     * @return array Evaluation result
     */
    public static function saveAnswer(int $attemptId, int $questionId, ?int $optionId = null, ?string $textAnswer = null): array {
        $evaluation = self::evaluateAnswer($questionId, $optionId, $textAnswer);
        
        $existing = Database::query(
            'SELECT id FROM exam_answers WHERE attempt_id = :aid AND question_id = :qid LIMIT 1',
            [':aid' => $attemptId, ':qid' => $questionId]
        )->fetch();
        
        if ($existing) {
            Database::query(
                'UPDATE exam_answers SET selected_option_id = :oid, text_answer = :txt, is_correct = :correct, points_earned = :points, answered_at = NOW() WHERE id = :id',
                [
                    ':oid' => $optionId, 
                    ':txt' => $textAnswer, 
                    ':correct' => $evaluation['is_correct'] ? 1 : 0, 
                    ':points' => $evaluation['points_earned'], 
                    ':id' => (int)$existing['id']
                ]
            );
        } else {
            Database::query(
                'INSERT INTO exam_answers (attempt_id, question_id, selected_option_id, text_answer, is_correct, points_earned, answered_at) VALUES (:aid, :qid, :oid, :txt, :correct, :points, NOW())',
                [
                    ':aid' => $attemptId, 
                    ':qid' => $questionId, 
                    ':oid' => $optionId, 
                    ':txt' => $textAnswer, 
                    ':correct' => $evaluation['is_correct'] ? 1 : 0, 
                    ':points' => $evaluation['points_earned']
                ]
            );
        }
        
        return $evaluation;
    }

    /**
     * Calculate exam score for an attempt
     * @param int $attemptId Exam attempt ID
     * @return array Score data
     */
    public static function calculateExamScore(int $attemptId): array {
        $answers = Database::query(
            'SELECT ea.is_correct, ea.points_earned, eq.points as max_points 
             FROM exam_answers ea 
             JOIN exam_questions eq ON ea.question_id = eq.id 
             WHERE ea.attempt_id = :aid',
            [':aid' => $attemptId]
        )->fetchAll();
        
        $totalQuestions = count($answers);
        $correctAnswers = 0;
        $totalPointsEarned = 0;
        $totalMaxPoints = 0;
        
        foreach ($answers as $answer) {
            if ((int)$answer['is_correct'] === 1) {
                $correctAnswers++;
            }
            $totalPointsEarned += (int)$answer['points_earned'];
            $totalMaxPoints += (int)$answer['max_points'];
        }
        
        $scorePercentage = $totalMaxPoints > 0 ? round(($totalPointsEarned / $totalMaxPoints) * 100, 2) : 0;
        
        return [
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'total_points_earned' => $totalPointsEarned,
            'total_max_points' => $totalMaxPoints,
            'score_percentage' => $scorePercentage
        ];
    }

    /**
     * Complete an exam attempt
     * @param int $attemptId Exam attempt ID
     * @return void
     */
    public static function completeExamAttempt(int $attemptId): void {
        $scoreData = self::calculateExamScore($attemptId);
        
        Database::query(
            'UPDATE exam_attempts SET score = :score, total_questions = :total_q, correct_answers = :correct, status = "completed", completed_at = NOW() WHERE id = :id',
            [
                ':score' => $scoreData['score_percentage'],
                ':total_q' => $scoreData['total_questions'],
                ':correct' => $scoreData['correct_answers'],
                ':id' => $attemptId
            ]
        );
    }
}


