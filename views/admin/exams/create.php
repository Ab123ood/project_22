<?php
// app/views/admin/exams/create.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Create a new assessment</h2>
    <a href="<?= $basePath ?>/admin/exams" class="text-sm text-gray-600 hover:text-gray-900">Back to list of assessments</a>
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

<form method="post" action="<?= $basePath ?>/admin/exams" id="examForm" class="max-w-5xl mx-auto">
    <!-- Basic assessment information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                <i class="ri-information-line text-primary-600 text-xl"></i>
            </div>
            <h2 class="text-lg font-medium text-gray-900">Basic assessment information</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assessment address</label>
                <input type="text" name="title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Example: E -hunting assessment " required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assessment category</label>
                <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <?php $cat = $old['category'] ?? ''; ?>
                    <option value="general" <?= $cat==='general'?'selected':''; ?>>general</option>
                    <option value="phishing" <?= $cat==='phishing'?'selected':''; ?>>E -hunter</option>
                    <option value="passwords" <?= $cat==='passwords'?'selected':''; ?>>Passwords</option>
                    <option value="malware" <?= $cat==='malware'?'selected':''; ?>>Malignant software</option>
                    <option value="social" <?= $cat==='social'?'selected':''; ?>>Social engineering</option>
                    <option value="network" <?= $cat==='network'?'selected':''; ?>>Network safety</option>
                    <option value="data" <?= $cat==='data'?'selected':''; ?>>Data protection</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">The level of difficulty</label>
                <?php $dif = $old['difficulty_level'] ?? 'beginner'; ?>
                <select name="difficulty_level" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="beginner" <?= $dif==='beginner'?'selected':''; ?>>junior</option>
                    <option value="intermediate" <?= $dif==='intermediate'?'selected':''; ?>>Medium</option>
                    <option value="advanced" <?= $dif==='advanced'?'selected':''; ?>>advanced</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assessment period (minutes)</label>
                <input type="Count" name="duration_minutes" value="<?= htmlspecialchars($old['duration_minutes'] ?? '30') ?>" min="5" max="120" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="30" required>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Assessment description</label>
            <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="A brief description of the assessment content and its goals ... "><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Assessment settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center mb-6">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                <i class="ri-settings-line text-green-600 text-xl"></i>
            </div>
            <h2 class="text-lg font-medium text-gray-900">Assessment settings</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Success degree (%)</label>
                <input type="Count" name="passing_score" value="<?= htmlspecialchars($old['passing_score'] ?? '70') ?>" min="50" max="100" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="70">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">The Count of permissible attempts</label>
                <input type="Count" name="max_attempts" value="<?= htmlspecialchars($old['max_attempts'] ?? '3') ?>" min="1" max="10" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="3">
            </div>
        </div>

        <div class="mt-6 space-y-4">
            <label class="flex items-center">
                <input type="checkbox" name="randomize_questions" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= !empty($old['randomize_questions']) ? 'checked' : '' ?>>
                <span class="mr-3 text-sm text-gray-700">Arrange questions randomly</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="show_results" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= !empty($old['show_results']) ? 'checked' : '' ?>>
                <span class="mr-3 text-sm text-gray-700">Show the results upon completion</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= !empty($old['is_active']) ? 'checked' : '' ?>>
                <span class="mr-3 text-sm text-gray-700">Activate the assessment</span>
            </label>
        </div>
    </div>

    <!-- Note about questions -->
    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 mb-6 text-sm">
        After creating the assessment, you will be converted automatically to the "Assessment Questions" page to add questions and options.
    </div>

    <!-- Save buttons -->
    <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <a href="<?= $basePath ?>/admin/exams" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">cancellation</a>
        <div class="flex space-x-3 space-x-reverse">
            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Create the assessment</button>
        </div>
    </div>
</form>
