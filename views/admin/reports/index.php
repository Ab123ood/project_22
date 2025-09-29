<?php
// app/views/admin/reports/index.php
// المتغيرات المتوقعة: $kpis, $rows, $from, $to, $type
$from = $from ?? '';
$to = $to ?? '';
$type = $type ?? 'overview';
$k_active_users = isset($kpis['active_users']) && $kpis['active_users'] !== null ? (int)$kpis['active_users'] : (int)($kpis['users_count'] ?? 0);
$k_pass = $kpis['avg_exam_score'] ?? null; // متوسط نتائج الاختبارات المكتملة
$k_content = $kpis['content_views'] ?? null; // إجمالي مشاهدات المحتوى
$totalRows = is_array($rows ?? null) ? count($rows) : 0;
?>
<div class="px-6 py-6">
  <!-- العنوان ومسارات التصفح -->
  <div class="mb-6 flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center ml-3">
        <i class="ri-bar-chart-line text-primary text-xl"></i>
      </div>
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">التقارير والإحصائيات</h1>
        <p class="text-sm text-gray-600">تحليل استخدام المنصة ونتائج الاختبارات والمحتوى</p>
      </div>
    </div>
  </div>

  <!-- فلاتر البحث -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
    <form id="reportsFilter" method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
        <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
        <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">نوع التقرير</label>
        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
          <?php $types = [
            'overview'=>'نظرة عامة',
            'exams'=>'الاختبارات',
            'exam_attempts'=>'محاولات الاختبارات',
            'content'=>'المحتوى',
            'content_views'=>'مشاهدات المحتوى',
            'users'=>'المستخدمون',
            'surveys'=>'الاستبيانات',
            'points'=>'النقاط'
          ]; ?>
          <?php foreach($types as $val=>$label): ?>
            <option value="<?= $val ?>" <?= $type===$val?'selected':''; ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex items-end gap-2">
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:opacity-90"><i class="ri-search-line ml-2"></i>تطبيق</button>
        <button type="button" id="btnExport" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200"><i class="ri-download-2-line ml-2"></i>تصدير CSV</button>
      </div>
    </form>
  </div>

  <!-- مؤشرات رئيسية -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
          <i class="ri-user-star-line text-2xl"></i>
        </div>
        <div class="mr-4">
          <p class="text-sm text-gray-600">المستخدمون النشطون</p>
          <p class="text-2xl font-bold text-gray-900" id="kpiActiveUsers"><?= number_format($k_active_users) ?></p>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
      <div class="flex items-center">
        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
          <i class="ri-checkbox-circle-line text-2xl"></i>
        </div>
        <div class="mr-4">
          <p class="text-sm text-gray-600">متوسط نتيجة الاختبارات</p>
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
          <p class="text-sm text-gray-600">إجمالي مشاهدات المحتوى</p>
          <p class="text-2xl font-bold text-gray-900" id="kpiContentViews"><?= $k_content===null?'-':htmlspecialchars((string)$k_content) ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- شريط مؤشرات ثانوي -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">عدد المستخدمين</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['users_count'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">عدد الاختبارات</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['exams_count'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">محاولات الاختبارات</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['exam_attempts'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">المحاولات المكتملة</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['completed_attempts'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">عدد الاستبيانات</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['surveys_count'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">إجابات الاستبيانات</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['survey_responses'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">عدد المحتويات</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['content_count'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
      <p class="text-sm text-gray-600">إجمالي النقاط</p>
      <p class="text-xl font-bold text-gray-900"><?= (int)($kpis['points_total'] ?? 0) ?></p>
    </div>
  </div>

  <!-- تبويب المحتوى: جدول/مخطط (حالياً الجدول حسب البيانات) -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <button class="tab-btn px-4 py-2 text-sm rounded-lg bg-primary text-white" data-tab="table">جدول</button>
        <button class="tab-btn px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-700" data-tab="chart" disabled title="سيضاف لاحقاً">مخطط</button>
      </div>
      <div class="text-sm text-gray-500" id="resultsInfo">عرض 1–<?= $totalRows ?> من <?= $totalRows ?> نتيجة</div>
    </div>

    <!-- جدول النتائج -->
    <div id="tab-table" class="p-5">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-right font-medium text-gray-700">#</th>
              <th class="px-4 py-3 text-right font-medium text-gray-700">النوع</th>
              <th class="px-4 py-3 text-right font-medium text-gray-700">العنصر</th>
              <th class="px-4 py-3 text-right font-medium text-gray-700">المستخدم</th>
              <th class="px-4 py-3 text-right font-medium text-gray-700">التاريخ</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php if (!empty($rows)): $i=1; foreach($rows as $r): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 text-gray-700"><?= $i++; ?></td>
              <td class="px-4 py-2 text-gray-700">
                <?php
                  $kind = $r['kind'] ?? '';
                  echo $kind==='exam'?'اختبار'
                    :($kind==='exam_attempt'?'محاولة اختبار'
                    :($kind==='content'?'محتوى'
                    :($kind==='content_view'?'مشاهدة محتوى'
                    :($kind==='user'?'مستخدم'
                    :($kind==='survey'?'استبيان'
                    :($kind==='survey_response'?'إجابة استبيان'
                    :($kind==='points'?'نقاط':'-')))))));
                ?>
              </td>
              <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($r['item'] ?? '-') ?></td>
              <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($r['user'] ?? '-') ?></td>
              <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($r['dt'] ?? '-') ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr>
              <td colspan="5" class="px-4 py-8 text-center text-gray-500">لا توجد بيانات ضمن نطاق الفلاتر المحدد.</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- تبويب المخطط (Placeholder) -->
    <div id="tab-chart" class="p-5 hidden">
      <div class="h-64 bg-gray-50 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-500">
        منطقة مخطط - يمكن دمج Chart.js لاحقاً
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
      // حافظ على الفلاتر الحالية وقم بإضافة export=csv
      const params = new URLSearchParams(new FormData(form));
      params.set('export', 'csv');
      const url = window.location.pathname + '?' + params.toString();
      window.location.href = url;
    });
  });
</script>
