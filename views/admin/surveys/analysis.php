<?php
// app/views/admin/surveys/analysis.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>

<div class="max-w-6xl mx-auto">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">تحليل نتائج الاستبيان</h1>
        <p class="text-sm text-gray-600 mt-1">عنوان الاستبيان: <span class="font-medium text-gray-900"><?= htmlspecialchars($survey['title'] ?? '') ?></span></p>
      </div>
      <a href="<?= $basePath ?>/admin/surveys" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">عودة إلى القائمة</a>
    </div>
  </div>

  <?php if (!empty($analysis)): ?>
    <div class="space-y-6">
      <?php foreach ($analysis as $idx => $q): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h2 class="text-base md:text-lg font-semibold text-gray-900">س<?= $idx+1 ?>. <?= htmlspecialchars($q['text']) ?></h2>
              <p class="text-xs text-gray-500 mt-1">نوع السؤال: <span class="font-medium text-gray-700"><?= htmlspecialchars($q['type']) ?></span> • إجمالي الردود: <span class="font-medium text-gray-700"><?= (int)$q['total'] ?></span></p>
            </div>
          </div>

          <?php if (in_array($q['type'], ['multiple_choice','single_choice','yes_no'], true)): ?>
            <?php
              $dist = $q['distribution'] ?? [];
              $total = max(1, (int)$q['total']);
            ?>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-2 text-right text-gray-600 font-medium">الخيار</th>
                    <th class="px-4 py-2 text-right text-gray-600 font-medium">العدد</th>
                    <th class="px-4 py-2 text-right text-gray-600 font-medium">النسبة</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <?php foreach ($dist as $row): ?>
                    <?php $pct = round(((int)$row['cnt'] / $total) * 100); ?>
                    <tr>
                      <td class="px-4 py-2 text-gray-900"><?= htmlspecialchars($row['option_text'] ?? '-') ?></td>
                      <td class="px-4 py-2 text-gray-700"><?= (int)$row['cnt'] ?></td>
                      <td class="px-4 py-2">
                        <div class="w-full bg-gray-100 rounded-full h-2">
                          <div class="h-2 bg-blue-500 rounded-full" style="width: <?= $pct ?>%"></div>
                        </div>
                        <div class="text-xs text-gray-600 mt-1"><?= $pct ?>%</div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

          <?php elseif ($q['type'] === 'rating'): ?>
            <?php $avg = $q['rating']['avg'] ?? null; $counts = $q['rating']['counts'] ?? []; ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                <p class="text-sm text-gray-700">متوسط التقييم</p>
                <p class="text-3xl font-bold text-blue-700 mt-1"><?= $avg !== null ? number_format($avg, 2) : '—' ?></p>
              </div>
              <div class="md:col-span-2 bg-white">
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-2 text-right text-gray-600 font-medium">القيمة</th>
                        <th class="px-4 py-2 text-right text-gray-600 font-medium">العدد</th>
                        <th class="px-4 py-2 text-right text-gray-600 font-medium">النسبة</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                      <?php
                        $sum = 0; foreach ($counts as $c) { $sum += (int)$c['cnt']; }
                        $sum = max(1, $sum);
                      ?>
                      <?php foreach ($counts as $c): ?>
                        <?php $pct = round(((int)$c['cnt'] / $sum) * 100); ?>
                        <tr>
                          <td class="px-4 py-2 text-gray-900"><?= htmlspecialchars((string)($c['rating_value'])) ?></td>
                          <td class="px-4 py-2 text-gray-700"><?= (int)$c['cnt'] ?></td>
                          <td class="px-4 py-2">
                            <div class="w-full bg-gray-100 rounded-full h-2">
                              <div class="h-2 bg-purple-500 rounded-full" style="width: <?= $pct ?>%"></div>
                            </div>
                            <div class="text-xs text-gray-600 mt-1"><?= $pct ?>%</div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          <?php else: ?>
            <?php $samples = $q['samples'] ?? []; ?>
            <?php if (!empty($samples)): ?>
              <div class="space-y-3">
                <?php foreach ($samples as $s): ?>
                  <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="text-sm text-gray-900 mb-1">"<?= htmlspecialchars($s['answer_text']) ?>"</div>
                    <div class="text-xs text-gray-500">بتاريخ: <?= date('Y/m/d H:i', strtotime($s['created_at'] ?? 'now')) ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="text-sm text-gray-500">لا توجد إجابات نصية لعرضها.</div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center text-gray-500">لا توجد بيانات تحليلية لهذا الاستبيان.</div>
  <?php endif; ?>
</div>
