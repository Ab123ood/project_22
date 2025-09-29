<?php
// app/views/employee/dashboard.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>
<section class="bg-white">
  <div class="container mx-auto px-4 py-8">
    <div class="mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Welcome <?= htmlspecialchars($user['name'] ?? 'Guest') ?></h1>
      <p class="text-gray-600">Here’s a quick view of your progress and recent updates</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-sm text-gray-500 mb-1">Total points</div>
        <div class="text-2xl font-bold text-gray-900"><?= (int)($stats['points'] ?? 0) ?></div>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-sm text-gray-500 mb-1">Completed materials</div>
        <div class="text-2xl font-bold text-gray-900"><?= (int)($stats['completed'] ?? 0) ?></div>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-sm text-gray-500 mb-1">Passed assessments</div>
        <div class="text-2xl font-bold text-gray-900"><?= (int)($stats['exams_passed'] ?? 0) ?></div>
      </div>

    </div>

    <div class="grid md:grid-cols-3 gap-6">
      <!-- Notices -->
      <div class="md:col-span-1">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Notifications</h2>
            <a href="<?= $basePath ?>/notifications" class="text-sm text-primary hover:underline">Show all</a>
          </div>
          <div class="space-y-4">
            <?php if (!empty($notifications)): foreach ($notifications as $n): ?>
              <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                  <i class="ri-notification-3-line"></i>
                </div>
                <div>
                  <div class="text-sm text-gray-900 mb-1"><?= htmlspecialchars($n['title']) ?></div>
                  <div class="text-xs text-gray-500"><?= htmlspecialchars($n['time']) ?></div>
                </div>
              </div>
            <?php endforeach; else: ?>
              <div class="text-sm text-gray-500">There are no notifications currently.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Suggested content -->
      <div class="md:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Suggested for you</h2>
            <a href="<?= $basePath ?>/content" class="text-sm text-primary hover:underline">Browse all</a>
          </div>
          <?php if (!empty($suggested)): ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
              <?php foreach ($suggested as $it): ?>
                <div class="border border-gray-200 rounded-xl p-4">
                  <div class="text-xs text-gray-500 mb-1">Type: <?= htmlspecialchars($it['type']) ?></div>
                  <div class="font-semibold text-gray-900 mb-2 truncate"><?= htmlspecialchars($it['title']) ?></div>
                  <a href="#" class="text-primary text-sm hover:underline">Read more</a>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-sm text-gray-500">No suggested content right now.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
