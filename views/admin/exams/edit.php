<?php
// app/views/admin/exams/edit.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl md:text-2xl font-bold text-gray-900">تعديل الاختبار</h2>
    <a href="<?= $basePath ?>/admin/exams" class="text-sm text-gray-600 hover:text-gray-900">عودة إلى قائمة الاختبارات</a>
</div>

<?php if (!empty($errors ?? [])): ?>
    <div class="mb-6 p-4 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm">
        <ul class="list-disc mr-6">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= $basePath ?>/admin/exams/update" id="examForm" class="max-w-5xl mx-auto">
    <input type="hidden" name="exam_id" value="<?= $exam['id'] ?? '' ?>">
    
    <!-- معلومات الاختبار الأساسية -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                <i class="ri-information-line text-primary-600 text-xl"></i>
            </div>
            <h2 class="text-lg font-medium text-gray-900">معلومات الاختبار الأساسية</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الاختبار</label>
                <input type="text" name="title" value="<?= htmlspecialchars($exam['title'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="مثال: اختبار التصيد الإلكتروني" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">فئة الاختبار</label>
                <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <?php $cat = $exam['category'] ?? 'general'; ?>
                    <option value="general" <?= $cat==='general'?'selected':''; ?>>عام</option>
                    <option value="phishing" <?= $cat==='phishing'?'selected':''; ?>>التصيد الإلكتروني</option>
                    <option value="passwords" <?= $cat==='passwords'?'selected':''; ?>>كلمات المرور</option>
                    <option value="malware" <?= $cat==='malware'?'selected':''; ?>>البرمجيات الخبيثة</option>
                    <option value="social" <?= $cat==='social'?'selected':''; ?>>الهندسة الاجتماعية</option>
                    <option value="network" <?= $cat==='network'?'selected':''; ?>>أمان الشبكات</option>
                    <option value="data" <?= $cat==='data'?'selected':''; ?>>حماية البيانات</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">مستوى الصعوبة</label>
                <?php $dif = $exam['difficulty_level'] ?? 'beginner'; ?>
                <select name="difficulty_level" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="beginner" <?= $dif==='beginner'?'selected':''; ?>>مبتدئ</option>
                    <option value="intermediate" <?= $dif==='intermediate'?'selected':''; ?>>متوسط</option>
                    <option value="advanced" <?= $dif==='advanced'?'selected':''; ?>>متقدم</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">مدة الاختبار (بالدقائق)</label>
                <input type="number" name="duration_minutes" value="<?= htmlspecialchars($exam['duration_minutes'] ?? '30') ?>" min="5" max="120" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="30" required>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">وصف الاختبار</label>
            <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="وصف مختصر عن محتوى الاختبار وأهدافه..."><?= htmlspecialchars($exam['description'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- إعدادات الاختبار -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                <i class="ri-settings-line text-green-600 text-xl"></i>
            </div>
            <h2 class="text-lg font-medium text-gray-900">إعدادات الاختبار</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">درجة النجاح (%)</label>
                <input type="number" name="passing_score" value="<?= htmlspecialchars($exam['passing_score'] ?? '70') ?>" min="50" max="100" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="70">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">عدد المحاولات المسموحة</label>
                <input type="number" name="max_attempts" value="<?= htmlspecialchars($exam['max_attempts'] ?? '3') ?>" min="1" max="10" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="3">
            </div>
        </div>

        <div class="mt-6 space-y-4">
            <label class="flex items-center">
                <input type="checkbox" name="randomize_questions" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= !empty($exam['randomize_questions']) ? 'checked' : '' ?>>
                <span class="mr-3 text-sm text-gray-700">ترتيب الأسئلة عشوائياً</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="show_results" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= !empty($exam['show_results']) ? 'checked' : '' ?>>
                <span class="mr-3 text-sm text-gray-700">إظهار النتائج فور الانتهاء</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= !empty($exam['is_active']) ? 'checked' : '' ?>>
                <span class="mr-3 text-sm text-gray-700">تفعيل الاختبار</span>
            </label>
        </div>
    </div>

    <!-- أزرار الحفظ -->
    <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <a href="<?= $basePath ?>/admin/exams" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">إلغاء</a>
        <div class="flex space-x-3 space-x-reverse">
            <a href="<?= $basePath ?>/admin/exams/questions?exam_id=<?= $exam['id'] ?? '' ?>" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg">إدارة الأسئلة</a>
            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">حفظ التغييرات</button>
        </div>
    </div>
</form>
