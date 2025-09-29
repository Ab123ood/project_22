<?php
// app/views/admin/users/view.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="<?= $basePath ?>/admin/users" class="text-gray-600 hover:text-gray-900">
                <i class="ri-arrow-right-line text-xl"></i>
            </a>
            <h2 class="text-xl md:text-2xl font-bold text-gray-900">عرض المستخدم</h2>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= $basePath ?>/admin/users/edit?id=<?= $user['id'] ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                <i class="ri-edit-line"></i>
                تعديل
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Info Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-start gap-4 mb-6">
                    <?php $initial = function_exists('mb_substr') ? mb_substr($user['name'] ?? '؟', 0, 1, 'UTF-8') : substr($user['name'] ?? '?', 0, 1); ?>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xl font-medium">
                        <?= htmlspecialchars($initial) ?>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-1"><?= htmlspecialchars($user['name'] ?? '') ?></h3>
                        <p class="text-gray-600 mb-2"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                        <?php
                            $status = $user['status'] ?? 'inactive';
                            $statusMap = [
                                'active' => ['bg-green-100 text-green-800', 'نشط'],
                                'inactive' => ['bg-yellow-100 text-yellow-800', 'غير نشط'],
                                'banned' => ['bg-red-100 text-red-800', 'محظور'],
                                'pending' => ['bg-gray-100 text-gray-800', 'في الانتظار'],
                            ];
                            $statusInfo = $statusMap[$status] ?? ['bg-gray-100 text-gray-800', $status];
                        ?>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $statusInfo[0] ?>">
                            <?= htmlspecialchars($statusInfo[1]) ?>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">معلومات الحساب</h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-xs text-gray-500">الدور</dt>
                                <dd class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['role_name'] ?? 'غير محدد') ?></dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">تاريخ الانضمام</dt>
                                <dd class="text-sm font-medium text-gray-900"><?= htmlspecialchars(substr((string)($user['created_at'] ?? ''), 0, 10)) ?></dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">آخر تحديث</dt>
                                <dd class="text-sm font-medium text-gray-900"><?= htmlspecialchars(substr((string)($user['updated_at'] ?? ''), 0, 10)) ?></dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">معلومات إضافية</h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-xs text-gray-500">آخر دخول</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    <?= $user['last_login'] ? htmlspecialchars(substr((string)$user['last_login'], 0, 16)) : 'لم يسجل دخول بعد' ?>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">فرض تغيير كلمة المرور</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    <?= (int)($user['force_password_reset'] ?? 0) === 1 ? 'نعم' : 'لا' ?>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">الإحصائيات</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="ri-file-list-3-line text-blue-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700">الاختبارات المكتملة</span>
                        </div>
                        <span class="text-lg font-bold text-blue-600"><?= (int)$stats['exams_taken'] ?></span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="ri-survey-line text-green-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700">الاستبيانات المكتملة</span>
                        </div>
                        <span class="text-lg font-bold text-green-600"><?= (int)$stats['surveys_completed'] ?></span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="ri-article-line text-purple-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700">المحتوى المشاهد</span>
                        </div>
                        <span class="text-lg font-bold text-purple-600"><?= (int)$stats['content_viewed'] ?></span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="ri-trophy-line text-yellow-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700">إجمالي النقاط</span>
                        </div>
                        <span class="text-lg font-bold text-yellow-600"><?= (int)$stats['total_points'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
