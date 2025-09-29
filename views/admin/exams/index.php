<?php
// app/views/admin/exams/index.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
$isAdmin = (session_status() === PHP_SESSION_ACTIVE || @session_start() === null) && (int)($_SESSION['role_id'] ?? 0) === 3;
?>

<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Assessment management</h2>
    <a href="<?= $basePath ?>/admin/exams/create" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm">
        <i class="ri-add-line"></i>
        Create an assessment
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">List of assessments</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">the address</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Duration (d)</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($exams)): ?>
                    <?php foreach ($exams as $e): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($e['title'] ?? '') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($e['category'] ?? '') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php
                                    $difficultyLabels = [
                                        'beginner' => 'junior',
                                        'intermediate' => 'Medium',
                                        'advanced' => 'advanced'
                                    ];
                                    echo htmlspecialchars($difficultyLabels[$e['difficulty_level'] ?? 'beginner'] ?? 'junior');
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= (int)($e['duration_minutes'] ?? 0) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                    $isActive = (int)($e['is_active'] ?? 0);
                                    $statusLabel = $isActive ? 'active' : 'Inactive';
                                    $statusClass = $isActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>"><?= $statusLabel ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a class="text-blue-600 hover:text-blue-900 p-1" href="<?= $basePath ?>/admin/exams/questions?exam_id=<?= $e['id'] ?>" title="Questions Management ">
                                        <i class="ri-question-answer-line"></i>
                                    </a>
                                    <?php if ($isAdmin): ?>
                                    <a class="text-gray-600 hover:text-gray-900 p-1" href="<?= $basePath ?>/admin/exams/edit?id=<?= $e['id'] ?>" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form method="POST" action="<?= $basePath ?>/admin/exams/delete" class="inline" onsubmit="return confirm('Are you sure to delete this assessment?')">
                                        <input type="hidden" name="exam_id" value="<?= $e['id'] ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900 p-1" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="px-6 py-6 text-center text-sm text-gray-500" colspan="6">There are no assessments yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
