<?php
// app/views/admin/surveys/create.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>

<!-- رأس الصفحة -->
<div class="bg-white shadow-sm border border-gray-200 rounded-xl mb-6">
  <div class="px-6 py-4 flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
        <i class="ri-survey-line text-blue-600 text-xl"></i>
      </div>
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">إنشاء استبيان جديد</h1>
        <p class="text-sm text-gray-600">صمم استبيانك وحدد جمهوره</p>
      </div>
    </div>
    <a href="<?= $basePath ?>/admin/surveys" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">عودة</a>
  </div>
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

<form id="surveyForm" method="post" action="<?= $basePath ?>/admin/surveys" class="max-w-6xl mx-auto">
  <!-- معلومات الاستبيان الأساسية -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center mb-6">
      <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
        <i class="ri-information-line text-blue-600 text-xl"></i>
      </div>
      <h2 class="text-lg font-medium text-gray-900">معلومات الاستبيان الأساسية</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الاستبيان</label>
        <input type="text" id="surveyTitle" name="title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="مثال: استبيان الوعي بكلمات المرور" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">فئة الاستبيان</label>
        <?php $cat = $old['category'] ?? ''; ?>
        <select id="surveyCategory" name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
          <option value="" <?= $cat===''?'selected':''; ?>>اختر الفئة</option>
          <option value="phishing" <?= $cat==='phishing'?'selected':''; ?>>التصيد الإلكتروني</option>
          <option value="passwords" <?= $cat==='passwords'?'selected':''; ?>>كلمات المرور</option>
          <option value="malware" <?= $cat==='malware'?'selected':''; ?>>البرمجيات الخبيثة</option>
          <option value="social" <?= $cat==='social'?'selected':''; ?>>الهندسة الاجتماعية</option>
          <option value="network" <?= $cat==='network'?'selected':''; ?>>أمان الشبكات</option>
          <option value="data" <?= $cat==='data'?'selected':''; ?>>حماية البيانات</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">نوع الاستبيان (اختياري)</label>
        <select id="surveyType" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
          <option value="assessment">تقييم وعي</option>
          <option value="feedback">ملاحظات على المحتوى</option>
          <option value="campaign">قياس حملة توعوية</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
        <?php $st = $old['status'] ?? 'draft'; ?>
        <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
          <option value="draft" <?= $st==='draft'?'selected':''; ?>>مسودة</option>
          <option value="published" <?= $st==='published'?'selected':''; ?>>منشور</option>
          <option value="archived" <?= $st==='archived'?'selected':''; ?>>مؤرشف</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
      <?php
        // إعادة تعبئة تاريخ/وقت بصيغة datetime-local (T) عند وجود old
        $af = $old['availabilityFrom'] ?? '';
        $at = $old['availabilityTo'] ?? '';
      ?>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">بداية التوفر (اختياري)</label>
        <input type="datetime-local" id="availableFrom" name="availability_from" value="<?= htmlspecialchars($af) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">نهاية التوفر (اختياري)</label>
        <input type="datetime-local" id="availableTo" name="availability_to" value="<?= htmlspecialchars($at) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
      </div>
    </div>

    <div class="mt-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">وصف الاستبيان</label>
      <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="وصف مختصر عن هدف الاستبيان..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
    </div>

    <div class="mt-6 space-y-4">
      <?php $an = (int)($old['anonymous'] ?? 0); $am = (int)($old['allowMultiple'] ?? 0); ?>
      <div class="flex items-center">
        <input type="checkbox" id="anonymous" name="anonymous" value="1" <?= $an ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
        <label for="anonymous" class="mr-3 text-sm text-gray-700">السماح بالاستجابة مجهولة الهوية</label>
      </div>
      <div class="flex items-center">
        <input type="checkbox" id="allowMultiple" name="allow_multiple" value="1" <?= $am ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
        <label for="allowMultiple" class="mr-3 text-sm text-gray-700">السماح بأكثر من استجابة لكل مستخدم</label>
      </div>
    </div>
  </div>

  <!-- تنبيه: إدارة الأسئلة ستكون في الصفحة التالية -->
  <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 mb-6 text-sm">
    بعد إنشاء الاستبيان سيتم تحويلك تلقائياً إلى صفحة "أسئلة الاستبيان" لإضافة الأسئلة والخيارات.
  </div>

  <!-- أزرار الحفظ -->
  <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <a href="<?= $basePath ?>/admin/surveys" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">إلغاء</a>
    <div class="flex space-x-3 space-x-reverse">
      <button type="button" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">حفظ كمسودة</button>
      <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">إنشاء الاستبيان</button>
    </div>
  </div>
</form>

<!-- لا سكربت خاص ببناء الأسئلة هنا بعد الآن -->
