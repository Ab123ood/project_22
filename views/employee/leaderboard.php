<?php
// app/views/employee/leaderboard.php
$pageTitle = 'لوحة المتصدرين';
$currentPage = 'leaderboard';
?>

<div class="animate-fade-in">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="card mb-8 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center shadow-md">
                    <i class="ri-trophy-line text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">لوحة المتصدرين</h1>
                    <p class="text-gray-600 mt-1">تنافس مع زملائك واحصل على أعلى النقاط</p>
                </div>
            </div>
        </div>

        <!-- Top 3 Podium -->
        <div class="mb-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <!-- 2nd Place -->
                <div class="order-2 md:order-1 animate-fade-in delay-1">
                    <div class="card bg-gradient-to-br from-gray-100 to-gray-200 text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-gray-300 rounded-bl-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-gray-600">2</span>
                        </div>
                        
                        <div class="mt-4">
                            <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-4 flex items-center justify-center shadow">
                                <i class="ri-user-line text-3xl text-gray-600"></i>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                <?= htmlspecialchars($topUsers[1]['name'] ?? 'لا يوجد') ?>
                            </h3>
                            
                            <div class="flex items-center justify-center gap-2 mb-3">
                                <i class="ri-star-line text-yellow-500"></i>
                                <span class="text-2xl font-bold text-gray-900">
                                    <?= number_format($topUsers[1]['total_points'] ?? 0) ?>
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center gap-1">
                                    <i class="ri-medal-line"></i>
                                    <span><?= $topUsers[1]['badges_earned'] ?? 0 ?></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i class="ri-fire-line"></i>
                                    <span><?= $topUsers[1]['current_streak'] ?? 0 ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 1st Place -->
                <div class="order-1 md:order-2 animate-fade-in delay-2">
                    <div class="card bg-gradient-to-br from-yellow-100 to-yellow-200 text-center relative overflow-hidden transform scale-105">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-yellow-400 rounded-bl-full flex items-center justify-center">
                            <i class="ri-crown-line text-3xl text-yellow-800"></i>
                        </div>
                        
                        <div class="mt-4">
                            <div class="w-24 h-24 bg-yellow-300 rounded-full mx-auto mb-4 flex items-center justify-center border-4 border-yellow-400 shadow">
                                <i class="ri-user-line text-4xl text-yellow-800"></i>
                            </div>
                            
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                <?= htmlspecialchars($topUsers[0]['name'] ?? 'لا يوجد') ?>
                            </h3>
                            
                            <div class="flex items-center justify-center gap-2 mb-3">
                                <i class="ri-star-fill text-yellow-500"></i>
                                <span class="text-3xl font-bold text-gray-900">
                                    <?= number_format($topUsers[0]['total_points'] ?? 0) ?>
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center gap-1">
                                    <i class="ri-medal-line"></i>
                                    <span><?= $topUsers[0]['badges_earned'] ?? 0 ?></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i class="ri-fire-line"></i>
                                    <span><?= $topUsers[0]['current_streak'] ?? 0 ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3rd Place -->
                <div class="order-3 animate-fade-in delay-3">
                    <div class="card bg-gradient-to-br from-orange-100 to-orange-200 text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-orange-300 rounded-bl-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-orange-800">3</span>
                        </div>
                        
                        <div class="mt-4">
                            <div class="w-20 h-20 bg-orange-300 rounded-full mx-auto mb-4 flex items-center justify-center shadow">
                                <i class="ri-user-line text-3xl text-orange-800"></i>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                <?= htmlspecialchars($topUsers[2]['name'] ?? 'لا يوجد') ?>
                            </h3>
                            
                            <div class="flex items-center justify-center gap-2 mb-3">
                                <i class="ri-star-line text-yellow-500"></i>
                                <span class="text-2xl font-bold text-gray-900">
                                    <?= number_format($topUsers[2]['total_points'] ?? 0) ?>
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center gap-1">
                                    <i class="ri-medal-line"></i>
                                    <span><?= $topUsers[2]['badges_earned'] ?? 0 ?></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i class="ri-fire-line"></i>
                                    <span><?= $topUsers[2]['current_streak'] ?? 0 ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current User Stats -->
        <?php if (isset($currentUserStats)): ?>
        <div class="card bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200 mb-8 sticky top-20 z-10">
            <div class="flex items-center justify-between p-4 md:p-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow">
                        <i class="ri-user-line text-2xl text-white"></i>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">موقعك الحالي</h3>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="ri-flag-2-line"></i> المرتبة <?= $currentUserStats['rank_position'] ?? 'غير محدد' ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div>
                        <div class="text-2xl font-extrabold text-gray-900">
                            <?= number_format($currentUserStats['total_points'] ?? 0) ?>
                        </div>
                        <div class="text-sm text-gray-600">النقاط</div>
                    </div>
                    
                    <div>
                        <div class="text-2xl font-extrabold text-gray-900">
                            <?= $currentUserStats['badges_earned'] ?? 0 ?>
                        </div>
                        <div class="text-sm text-gray-600">الشارات</div>
                    </div>
                    
                    <div>
                        <div class="text-2xl font-extrabold text-gray-900">
                            <?= $currentUserStats['exams_completed'] ?? 0 ?>
                        </div>
                        <div class="text-sm text-gray-600">الاختبارات</div>
                    </div>
                    
                    <div>
                        <div class="text-2xl font-extrabold text-gray-900">
                            <?= $currentUserStats['current_streak'] ?? 0 ?>
                        </div>
                        <div class="text-sm text-gray-600">التسلسل</div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Full Leaderboard -->
        <div class="card">
            <div class="flex items-center gap-3 mb-2 p-4 md:p-6 border-b border-gray-100">
                <i class="ri-list-ordered text-2xl text-blue-600"></i>
                <h2 class="text-2xl font-extrabold text-gray-900">الترتيب العام</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">المرتبة</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">المستخدم</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">النقاط</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">الاختبارات</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">المحتوى</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">الشارات</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">التسلسل</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">آخر نشاط</th>
                        </tr>
                    </thead>
                    <tbody id="leaderboardTable" class="bg-white divide-y divide-gray-100">
                        <?php if (!empty($leaderboard)): ?>
                            <?php foreach ($leaderboard as $index => $user): ?>
                            <tr class="hover:bg-gray-50 transition-colors <?= isset($_SESSION['user_id']) && $user['user_id'] == $_SESSION['user_id'] ? 'bg-blue-50' : '' ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <?php if ($user['rank_position'] <= 3): ?>
                                            <?php
                                            $rankColors = [1 => 'text-yellow-500', 2 => 'text-gray-500', 3 => 'text-orange-500'];
                                            $rankIcons = [1 => 'ri-trophy-fill', 2 => 'ri-medal-fill', 3 => 'ri-award-fill'];
                                            ?>
                                            <i class="<?= $rankIcons[$user['rank_position']] ?> <?= $rankColors[$user['rank_position']] ?> text-xl"></i>
                                        <?php endif; ?>
                                        <span class="text-lg font-bold text-gray-900">
                                            <?= $user['rank_position'] ?>
                                        </span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow">
                                            <i class="ri-user-line text-white"></i>
                                        </div>
                                        <div>
                                            <div class="text-base font-semibold text-gray-900">
                                                <?= htmlspecialchars($user['name']) ?>
                                                <?php if (isset($_SESSION['user_id']) && $user['user_id'] == $_SESSION['user_id']): ?>
                                                    <span class="badge badge-primary mr-2">أنت</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($user['department']): ?>
                                            <div class="text-sm text-gray-500">
                                                <?= htmlspecialchars($user['department']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="ri-star-fill text-yellow-500"></i>
                                        <span class="text-lg font-bold text-gray-900">
                                            <?= number_format($user['total_points']) ?>
                                        </span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                    <?= $user['exams_completed'] ?>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-base text-gray-900">
                                    <?= $user['content_completed'] ?>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="ri-medal-line text-blue-500"></i>
                                        <span class="text-base text-gray-900">
                                            <?= $user['badges_earned'] ?>
                                        </span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="ri-fire-line text-red-500"></i>
                                        <span class="text-base text-gray-900">
                                            <?= $user['current_streak'] ?>
                                        </span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $user['last_activity'] ? date('Y/m/d H:i', strtotime($user['last_activity'])) : 'لا يوجد' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="ri-trophy-line text-4xl text-gray-300"></i>
                                        <p class="text-lg">لا توجد بيانات متاحة حالياً</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
