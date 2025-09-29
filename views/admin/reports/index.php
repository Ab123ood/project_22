<?php
// app/views/admin/reports/index.php
// Expected variables: $kpis, $rows, $from, $to, $type
$from = $from ?? '';
$to = $to ?? '';
$type = $type ?? 'overview';
$k_active_users = isset($kpis['active_users']) && $kpis['active_users'] !== null ? (int)$kpis['active_users'] : (int)($kpis['users_count'] ?? 0);
$k_pass = $kpis['avg_exam_score'] ?? null; // Average results of complete assessments
$k_content = $kpis['content_views'] ?? null; // Total content views
$totalRows = is_array($rows ?? null) ? count($rows) : 0;
?>
<div class="px-6 py-6">
  <!-- Address and browsing paths -->
  <div class="mb-6 flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center mr-3">
        <i class="ri-bar-chart-line text-primary text-xl"></i>
      </div>
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">Reports and statistics</h1>
        <p class="text-sm text-gray-600">Plastic use analysis, assessment results and content</p>
      </div>
    </div>
  </div>

  <!-- Search filters -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
    <form id="reportsFilter" method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">I am history</label>
        <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">To a date</label>
        <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Report</label>
        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
          <?php $types = [
            'overview'=>'Overview',
            'exams'=>'Assessments',
            'exam_attempts'=>'Assessment attempts',
            'content'=>'Content',
            'content_views'=>'Content views',
            'users'=>'Users',
            'surveys'=>'Surveys',
            'points'=>'Points'
          ]; ?>
          <?php foreach($types as $val=>$label): ?>
            <option value="<?= $val ?>" <?= $type===$val?'selected':''; ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex items-end gap-2">
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:opacity-90"><i class="ri-search-line mr-2"></i>Apply filters</button>
        <button type="button" id="btnExport" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200"><i class="ri-download-2-line mr-2"></i>Export CSV</button>
      </div>
    </form>
  </div>

  <!-- Main indicators -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
          <i class="ri-user-star-line text-2xl"></i>
        </div>
        <div class="mr-4">
          <p class="text-sm text-gray-600">Active users</p>
          <p class="text-2xl font-bold text-gray-900" id="kpiActiveUsers"><?= Count_format($k_active_users) ?></p>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
          <i class="ri-checkbox-circle-line text-2xl"></i>
        </div>
        <div class="mr-4">
          <p class="text-sm text-gray-600">Average assessment result</p>
          <p class="text-2xl font-bold text-gray-900" id="kpiAvgExamScore"><?= $k_pass===null?'-':htmlspecialchars((string)$k_pass.'%') ?></p>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
          <i class="ri-play-circle-line text-2xl"></i>
        </div>
        <div class="mr-4">
          <p class="text-sm text-gray-600">Total content views</p>
          <p class="text-2xl font-bold text-gray-900" id="kpiContentViews"><?= $k_content===null?'-':htmlspecialchars((string)$k_content) ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Secondary indicators -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">The Count of users</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['users_count'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">Number of assessments</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['exams_count'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">Assessment attempts</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['exam_attempts'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">Complete attempts</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['completed_attempts'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">Number of surveys</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['surveys_count'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">Answers survey</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['survey_responses'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">Number of contents</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['content_count'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">Total points</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['points_total'] ?? 0) ?></p>
    </div>
  </div>

  <!-- Content tab: schedule/scheme (currently schedule according to data) - - - - - - - -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <button class="tab-btn px-4 py-2 text-sm rounded-lg bg-primary text-white" data-tab="table">Table</button>
        <button class="tab-btn px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-700" data-tab="chart" disabled title="It will be added later ">a plan</button>
      </div>
      <div class="text-sm text-gray-500" id="resultsInfo">View 1–<?= $totalRows ?> from <?= $totalRows ?> a result</div>
    </div>

    <!-- Result schedule -->
    <div id="tab-table" class="p-5">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-right font-medium text-gray-700">#</th>
              <th class="px-4 py-3 text-right font-medium text-gray-700">Format</th>
              <th class="px-4 py-3 text-right font-medium text-gray-700">Element</th>
              <th class="px-4 py-3 text-right font-medium text-gray-700">user</th>
              <th class="px-4 py-3 text-right font-medium text-gray-700">the date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php if (!empty($rows)): $i=1; foreach($rows as $r): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 text-gray-700"><?= $i++; ?></td>
              <td class="px-4 py-2 text-gray-700">
                <?php
                  $kind = $r['kind'] ?? '';
                  echo $kind==='exam'?'a assessment'
                    :($kind==='exam_attempt'?'Assessment attempt'
                    :($kind==='content'?'content'
                    :($kind==='content_view'?'Watch content'
                    :($kind==='user'?'user'
                    :($kind==='survey'?'poll'
                    :($kind==='survey_response'?'Answer to a survey'
                    :($kind==='points'?'Points':'-')))))));
                ?>
              </td>
              <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($r['item'] ?? '-') ?></td>
              <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($r['user'] ?? '-') ?></td>
              <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($r['dt'] ?? '-') ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr>
              <td colspan="5" class="px-4 py-8 text-center text-gray-500">There is no data within the specified filters.</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Planning tab (Placeholder) -->
    <div id="tab-chart" class="p-5 hidden">
      <div class="h-64 bg-gray-50 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-500">
        Planned area - can be combined Chart.js Later
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('reportsFilter');
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabTable = document.getElementById('tab-table');
    const tabChart = document.getElementById('tab-chart');

    tabBtns.forEach(btn => btn.addEventListener('click', () => {
      if (btn.hasAttribute('disabled')) return;
      tabBtns.forEach(b => b.classList.remove('bg-primary','text-white'));
      tabBtns.forEach(b => b.classList.add('bg-gray-100','text-gray-700'));
      btn.classList.add('bg-primary','text-white');
      btn.classList.remove('bg-gray-100','text-gray-700');
      const t = btn.dataset.tab;
      if (t === 'table') { tabTable.classList.remove('hidden'); tabChart.classList.add('hidden'); }
      else { tabChart.classList.remove('hidden'); tabTable.classList.add('hidden'); }
    }));

    document.getElementById('btnExport')?.addEventListener('click', function(){
      // Keep the current filters and add export=csv
      const params = new URLSearchParams(new FormData(form));
      params.set('export', 'csv');
      const url = window.location.pathname + '?' + params.toString();
      window.location.href = url;
    });
  });
</script>
