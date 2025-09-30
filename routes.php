<?php
// Routes definitions extracted from index.php
/** @var Router $router */

// الصفحة الرئيسية العامة
$router->add('GET', '/', [HomeController::class, 'index']);

// الموظف
$router->add('GET', '/dashboard', [EmployeeController::class, 'dashboard']);
$router->add('GET', '/profile', [EmployeeController::class, 'profile']);

// المحتوى
$router->add('GET', '/content', [ContentController::class, 'index']);
$router->add('GET', '/content/view/(\d+)', [ContentController::class, 'view']);
$router->add('POST', '/content/like/(\d+)', [ContentController::class, 'like']);
$router->add('GET', '/content/search', [ContentController::class, 'search']);
$router->add('POST', '/content/favorite/(\d+)', [ContentController::class, 'favorite']);
$router->add('POST', '/content/unfavorite/(\d+)', [ContentController::class, 'unfavorite']);

// المتصدرين
$router->add('GET', '/leaderboard', [LeaderboardController::class, 'index']);
$router->add('GET', '/leaderboard/filter', [LeaderboardController::class, 'filter']);
$router->add('POST', '/leaderboard/refresh', [LeaderboardController::class, 'refresh']);

// اختبارات/استبيانات للموظف
$router->add('GET', '/exams', [EmployeeController::class, 'exams']);
$router->add('GET', '/progress', [EmployeeController::class, 'progress']);
$router->add('GET', '/surveys', [EmployeeController::class, 'surveys']);
$router->add('GET', '/exams/(\d+)/take', [EmployeeController::class, 'takeExam']);
$router->add('GET', '/exams/start', [EmployeeController::class, 'startExam']);
$router->add('POST', '/exams/save-answer', [EmployeeController::class, 'saveAnswer']);
$router->add('POST', '/exams/submit', [EmployeeController::class, 'submitExam']);
$router->add('POST', '/exams/abandon', [EmployeeController::class, 'abandonExam']);

// استبيانات ديناميكية
$router->add('GET', '/surveys/(\d+)/take', [EmployeeController::class, 'takeSurvey']);
$router->add('POST', '/surveys/(\d+)/submit', [EmployeeController::class, 'submitSurvey']);
$router->add('POST', '/surveys/(\d+)/save-progress', [EmployeeController::class, 'saveSurveyProgress']);

// الإدارة - تم توحيد dashboard مع EmployeeController

// المستخدمون
$router->add('GET', '/admin/users', [UsersController::class, 'index']);
$router->add('GET', '/admin/users/add', [UsersController::class, 'create']);
$router->add('POST', '/admin/users', [UsersController::class, 'store']);
$router->add('GET', '/admin/users/view', [UsersController::class, 'view']);
$router->add('GET', '/admin/users/edit', [UsersController::class, 'edit']);
$router->add('POST', '/admin/users/update', [UsersController::class, 'update']);
$router->add('POST', '/admin/users/delete', [UsersController::class, 'delete']);

// الاختبارات (إدارة)
$router->add('GET', '/admin/exams', [ExamsController::class, 'index']);
$router->add('GET', '/admin/exams/create', [ExamsController::class, 'create']);
$router->add('POST', '/admin/exams', [ExamsController::class, 'store']);
$router->add('GET', '/admin/exams/questions', [ExamsController::class, 'questions']);
$router->add('POST', '/admin/exams/questions', [ExamsController::class, 'addQuestion']);
$router->add('POST', '/admin/exams/options', [ExamsController::class, 'addOption']);
$router->add('POST', '/admin/exams/questions/delete', [ExamsController::class, 'deleteQuestion']);
$router->add('POST', '/admin/exams/options/delete', [ExamsController::class, 'deleteOption']);
$router->add('POST', '/admin/exams/options/set-correct', [ExamsController::class, 'setCorrectOption']);
$router->add('POST', '/admin/exams/questions/update', [ExamsController::class, 'updateQuestionText']);
$router->add('POST', '/admin/exams/options/update', [ExamsController::class, 'updateOptionText']);
$router->add('GET', '/admin/exams/edit', [ExamsController::class, 'edit']);
$router->add('POST', '/admin/exams/update', [ExamsController::class, 'update']);
$router->add('POST', '/admin/exams/delete', [ExamsController::class, 'delete']);

// الاستبيانات (إدارة)
$router->add('GET', '/admin/surveys', [SurveysController::class, 'index']);
$router->add('GET', '/admin/surveys/create', [SurveysController::class, 'create']);
$router->add('POST', '/admin/surveys', [SurveysController::class, 'store']);
$router->add('GET', '/admin/surveys/questions', [SurveysController::class, 'questions']);
$router->add('POST', '/admin/surveys/questions', [SurveysController::class, 'addQuestion']);
$router->add('POST', '/admin/surveys/options', [SurveysController::class, 'addOption']);
$router->add('POST', '/admin/surveys/questions/delete', [SurveysController::class, 'deleteQuestion']);
$router->add('POST', '/admin/surveys/options/delete', [SurveysController::class, 'deleteOption']);
$router->add('POST', '/admin/surveys/questions/update', [SurveysController::class, 'updateQuestionText']);
$router->add('POST', '/admin/surveys/options/update', [SurveysController::class, 'updateOptionText']);
$router->add('GET', '/admin/surveys/edit', [SurveysController::class, 'edit']);
$router->add('POST', '/admin/surveys/update', [SurveysController::class, 'update']);
$router->add('POST', '/admin/surveys/delete', [SurveysController::class, 'delete']);
$router->add('GET', '/admin/surveys/analysis', [SurveysController::class, 'analysis']);

// المحتوى (إدارة)
$router->add('GET', '/admin/content', [ContentController::class, 'adminIndex']);
$router->add('GET', '/admin/content/create', [ContentController::class, 'create']);
$router->add('POST', '/admin/content', [ContentController::class, 'store']);
$router->add('GET', '/admin/content/edit', [ContentController::class, 'edit']);
$router->add('POST', '/admin/content/update', [ContentController::class, 'update']);
$router->add('POST', '/admin/content/delete', [ContentController::class, 'delete']);

// التقارير
$router->add('GET', '/admin/reports', [ReportsController::class, 'index']);

// المصادقة
$router->add('GET', '/auth', [AuthController::class, 'show']);
$router->add('POST', '/auth/login', [AuthController::class, 'login']);
$router->add('POST', '/auth/register', [AuthController::class, 'register']);
$router->add('GET', '/logout', [AuthController::class, 'logout']);

// تبديل اللغة
$router->add('GET', '/locale/switch', [LocaleController::class, 'switch']);


